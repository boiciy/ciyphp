<?php
/* =================================================================================
 * 版权声明：保留开源作者及版权声明前提下，开源代码可进行修改及用于任何商业用途。
 * 开源作者：众产国际产业公会  http://ciy.cn/code
 * 版本：0.5.2
====================================================================================*/
/*
 * common.php 常用公共函数库
 * 
 * diegoto                              页面302跳转
 * get/post/request/isset/cookie        获取用户输入数据
 * ciy_runJSON/succjson/errjson         本页面Ajax请求处理函数及快捷返回函数
 * g_substr/g_strlen                    汉字字符串处理函数
 * urlparam                             Url参数拼接函数
 * getnow/getip                         数据库转换时间和IP的快捷函数
 * getstrparam/setstrparam              比json还简化的多数据保存方式，一般用于单列多项设置的数据库保存。
 * file_down/img2thumb                  文件下载到本地和生成图片缩略图
 * makedir/savefile/savelogfile/delfile 创建多层新文件夹/保存文本文件/本地保存LOG/文件静默删除（影响程序稳定较多的地方，做了一个单独封装）
 * getapplication/setapplication        全局永久保存数据函数。类似ASP的Application对象。
 * pr/var_dump                          PHP调试变量界面打印。
 * ciy_runCSV                           导出到Excel，CSV格式。
 * 
 * 版本更新：
 * 2018-5-5 开源版本
 * 2014-6-1 初始版本

 */
//jsonstr_decode 在smile\datasync.pro.php用过
//htmlstr_decode 在ajaxboi.php用过
//convertUrlQuery 在weixinfunc.php用过
//getUrlQuery 在weixinfunc.php用过


/**
 * json_函数内部使用，返回失败数据。
 * return errjson('错误信息');
 */
function errjson($error, $errcode = 0, $ext = null) {
    if(is_array($ext))
        return array('result' => false, 'errcode' => $errcode, 'msg' => $error)+$ext;
    return array('result' => false, 'errcode' => $errcode, 'msg' => $error);
}
/**
 * json_函数内部使用，返回成功数据。
 * return succjson();
 * return succjson(array('data'=>$data));
 */
function succjson($ext = null) {
    if(is_array($ext))
        return array('result' => true)+$ext;
    return array('result' => true);
}
/**
 * 302跳转
 */
function diegoto($url) {
    header("Location: " . $url);
    echo '<html><head><meta http-equiv="refresh" content="1; url='.$url.'" /><script>location.href="'.$url.'";</script></head><body></body></html>';
    die();
}
/**
 * 类似substr，主要用于显示字符限制。支持中文，2个半角算一个字符占位。超过部分用$dot代替。
 * 建议：能用css实现超出汉字隐藏，尽量不要用此函数。
 * 例：g_substr('CHN中华人民共和国',10)='CHN中华...'  g_substr('CHN中华人民共和国',9)='CHN中华...'
 */
function g_substr($str, $len, $dot = '...') {
    $i = 0;
    $l = 0;
    $c = 0;
    $a = array();
    while ($l < $len) {
        $t = substr($str, $i, 1);
        if (ord($t) >= 224) {
            $c = 3;
            $t = substr($str, $i, $c);
            $l += 2;
        } elseif (ord($t) >= 192) {
            $c = 2;
            $t = substr($str, $i, $c);
            $l += 2;
        } else {
            $c = 1;
            $l++;
        }
        $i += $c;
        if ($l > $len)
            break;
        $a[] = $t;
    }
    $re = implode('', $a);
    if (substr($str, $i, 1) !== false) {
        array_pop($a);
        ($c == 1) and array_pop($a);
        $re = implode('', $a);
        $re .= $dot;
    }
    return $re;
}
/**
 * 类似strlen，英文中文都算1个字符。
 * 例：g_strlen('CHN中华人民共和国')=10
 */
function g_strlen($str) {
    $i = 0;
    $c = 0;
    $ln = 0;
    $len = strlen($str);
    while ($i < $len) {
        $t = substr($str, $i, 1);
        if (ord($t) >= 224) {
            $c = 3;
        } elseif (ord($t) >= 192) {
            $c = 2;
        } elseif (ord($t) == 0) {
            break;
        } else {
            $c = 1;
        }
        $i += $c;
        $ln++;
        if ($i > $len)
            break;
    }
    return $ln;
}
/**
 * 使用array拼接url，用于带原有url参数的访问。
 * $baseurl 不能有?
 * $keyarray key/value型 array数组
 * 例：当前页面为test.php?search=true&title=123&type=SSE
 * 调用 urlparam('',array('nid'=>2,'type'=>'SNR')) = '?search=true&title=123&nid=2&type=SNR'
 */
function urlparam($baseurl, $keyarray) {
    $arr = $keyarray + $_GET;
    $url = $baseurl.'?';
    foreach ($arr as $key => $value)
        $url .= $key . '=' . $value . '&';
    $url = substr($url, 0, -1);
    return $url;
}

/**
 * 返回mysql数据库所用的标准datetime格式字符串。
 * 用非mysql数据库，请注意修改该函数。
 *   例：getnow() = '2018-01-20 14:54:34'
 */
function getnow($time = null) {
    if($time === null)
        $time = time();
    return date('Y-m-d H:i:s', $time);
}
/**
 * 返回32位int真实IP地址。显示时请用long2ip()函数。
 * $real = true 时，返回字符串ip地址。
 * 数据库ip字段建议设置：int有符号类型（建议）；varchar(15)；兼容IPv6 varchar(40)
 *   例：getip() = 1343193298          getip(true) = '80.15.128.210'
 *   例：getip() = -765170305          getip(true) = '210.100.109.127'
 * 提示：真实IP地址访问方法有很多种，有些用户会用header伪造真实IP。请根据您的实际服务器集群部署调整本函数。特别是CDN和反向代理部分。
 */
function getip($real = false) {
    $ips = array();
    if (!empty($_SERVER['REMOTE_ADDR'])) {
        array_push($ips, $_SERVER['REMOTE_ADDR']);
    }
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        array_push($ips, $_SERVER['HTTP_CLIENT_IP']);
    }
    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        array_push($ips,explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']));
    }
    $count = count($ips);
    for ($i = 0; $i < $count; $i++) {
        $ipn = trim($ips[$i]);
        if(strpos($ipn,'127') === 0)
            continue;
        if (strpos($ipn, '10.') === 0)
            continue;
        if (strpos($ipn, '192.168') === 0)
            continue;
        if (strpos($ipn, '172.') === 0) {
            $ipa = explode('.', $ipn);
            $ipb = (int)@$ipa[1];
            if ($ipb >= 16 && $ipb <= 32)
                continue;
        }
        if($real)
            return $ipn;
        return ip2long($ipn);
    }
    if($real)
        return '0.0.0.0';
    else
        return 0;
}

/**
 * 比json还简化的多数据保存方式。该方式有一定的限制，请熟悉后使用。
 *   例：getstrparam('Skey=10392|Pid=33|Memo=其他备注文字') = Array([Skey] => 10392[Pid] => 33[Memo] => 其他备注文字);
 * $sparam = getstrparam('Skey=10392|Pid=33|Memo=其他备注文字');
 * $val = @$sparam['Memo'];
 * $sparam['Memo'] = 'xxx';
 * $retval = setstrparam($sparam);     值： 'Skey=10392|Pid=33|Memo=xxx'
 */
function getstrparam($pstr, $split = '|') {
    $strs = explode($split, $pstr);
    $data = array();
    foreach ($strs as $str) {
        $ind = strpos($str, '=');
        if ($ind !== false)
            $data[substr($str, 0, $ind)] = substr($str, $ind + 1);
    }
    return $data;
}
function setstrparam($parr, $split = '|') {
    $pstr = '';
    foreach($parr as $p=>$a)
        $pstr .= $p.'='.$a.$split;
    return trim($pstr,$split);
}

/**
 * JSON函数调用组件，用于界面Ajax请求在本页处理。
 * isform   用于页面form提交刷新的直接请求。非ajax请求。（为兼容传统更新方式，不建议使用）
 * ?json=true&func=函数名
 * 将调用json_函数名(){}
 * 函数返回array数组。
 */
function ciy_runJSON($isform = false) {
    if (!isget('json'))
        return;
    $funcname = 'json_' . get('func');
    if (!function_exists($funcname))
        $retarr = array('result' => false, 'errcode' => 0, 'msg' => 'JSON 调用函数缺失');
    else
        $retarr = call_user_func($funcname);
    if($isform)
        return $retarr;
    $cb = get('callback');
    if (empty($cb))
        echo json_encode($retarr);
    else
        echo $cb . '(' . json_encode($retarr) . ')';
    exit;
}

/**
 * CSV函数调用组件，用于数据导出到Excel操作
 * ?json=true&func=函数名
 * 将调用csv_函数名(){}
 * 函数返回array数组。第一行包含.csv，则为CSV文件名。
 */
function ciy_runCSV() {
    if (!isget('csv'))
        return;
    $filename = date('Y-m-d_H-i-s', time()) . '.csv';
    $funcname = 'csv_' . get('func');
    if (!function_exists($funcname))
    {
        global $mydata;
        $table = get('table');
        $where = get('where');
        $order = get('order');
        $column = get('column');
        if(empty($table))
        {
            pr($funcname.'调试信息：函数不存在，无table参数');
            return;
        }
        $retarr = array();
        $rows = $mydata->get(0,0, $table, $where,$order,$column);
        if(is_array($rows) && count($rows) > 0)
        {
            $fs = array();
            foreach($rows[0] as $f=>$v)
                $fs[] = $f;
            $retarr = array_merge($retarr,array($fs));
        }
        $retarr = array_merge($retarr,$rows);
    }
    else
        $retarr = call_user_func($funcname);
    if(!is_array($retarr))
    {
        pr('调试信息：'.$funcname.'函数返回值不是数组');
        return;
    }
    if(count($retarr) > 0 && !is_array($retarr[0]) && substr($retarr[0],-4) == '.csv')
    {
        $filename = $retarr[0];
        unset($retarr[0]);
    }
    header("Cache-Control: public");
    header("Pragma: public");
    header("Content-type: text/csv");
    header("Content-Disposition: attachment; filename=" . $filename);
    echo(chr(255) . chr(254));
    foreach ($retarr as $line)
    {
        if (is_array($line))
        {
            $bline = false;
            foreach ($line as $l)
            {
                if($bline)
                    echo mb_convert_encoding("\t", "UTF-16LE", "UTF-8");
                echo mb_convert_encoding('"'.$l.'"', "UTF-16LE", "UTF-8");
                $bline = true;
            }
        }
        else
            echo mb_convert_encoding($line, "UTF-16LE", "UTF-8");
        echo mb_convert_encoding("\r\n", "UTF-16LE", "UTF-8");
    }
    exit;
}

function get($name, $defvalue = '') {
    return isset($_GET[$name]) ? $_GET[$name] : $defvalue;
}

function cookie($name, $defvalue = '') {
    return isset($_COOKIE[$name]) ? $_COOKIE[$name] : $defvalue;
}

function post($name, $defvalue = '') {
    return isset($_POST[$name]) ? $_POST[$name] : $defvalue;
}

function request($name, $defvalue = '') {
    return isset($_REQUEST[$name]) ? $_REQUEST[$name] : $defvalue;
}

function isget($name) {
    return isset($_GET[$name]);
}

/**
 * 下载音视频文件到本地，图片可以直接保存为缩略图
 * url      下载链接地址
 * savepath 保存相对路径。如upload/
 * filename 保存文件名。默认日期文件名
 * thumb    生成缩略图参数的 如array('width'=>75,'height'=>75,'cut'=>true,'jpgquality'=>70)
 * timeout  下载超时时间 秒
 */
function file_down($url,$savepath,$filename = '', $thumb = null,$timeout = 60)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_TIMEOUT,$timeout);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    @curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
    $data = curl_exec($ch);
    if ($data === false || empty($data)) {
        return 'ERR:下载失败';
    }
    $info = curl_getinfo($ch);
    curl_close($ch);
    if(empty($filename))
        $filename = date('Ymd_His_').rand(1000,9999);
    $fileext = '';
    if($info['content_type'] == 'image/jpeg')
        $fileext = '.jpg';
    else if($info['content_type'] == 'image/jpg')
        $fileext = '.jpg';
    else if($info['content_type'] == 'image/png')
        $fileext = '.png';
    else if($info['content_type'] == 'image/gif')
        $fileext = '.gif';
    else if($info['content_type'] == 'audio/amr')
        $fileext = '.amr';
    else if($info['content_type'] == 'audio/wav')
        $fileext = '.wav';
    else if($info['content_type'] == 'audio/mpeg')
        $fileext = '.mp3';
    else if($info['content_type'] == 'audio/ogg')
        $fileext = '.ogg';
    if(empty($fileext))
        return 'ERR:文件类型未知';
    $fp= fopen(PATH_ROOT.$savepath.$filename.$fileext,'w');
    fwrite($fp,$data);
    @fclose($fp);
    if($thumb !== null)
    {
        if(!img2thumb(PATH_ROOT.$savepath.$filename.$fileext, PATH_ROOT.$savepath.$filename.'_thumb.jpg', (int)@$thumb['width'], (int)@$thumb['height'], (bool)@$thumb['cut'], (int)@$thumb['jpgquality']))
            return 'ERR:缩略图生成失败';
        else
        {
            delfile(PATH_ROOT.$savepath.$filename.$fileext);
            return $savepath.$filename.'_thumb.jpg';
        }
    }
    return $savepath.$filename.$fileext;
}
/**
 * 生成缩略图
 * src_img      原图文件绝对完整地址。
 * dst_img      缩略图文件绝对完整地址。
 * width/height 缩略图宽高。不能同时为0。一个为0时，则等比例缩放。
 * cut          是否裁切图片
 * jpgquality   保存jpg的清晰度。
 */
function img2thumb($src_img, $dst_img, $width = 75, $height = 75, $cut = false,$jpgquality=50)
{
    if(!is_file($src_img))
        return false;
    if($jpgquality < 10)
        $jpgquality = 50;
    $srcinfo = getimagesize($src_img);
    $src_w = $srcinfo[0];
    $src_h = $srcinfo[1];
    $type  = strtolower(substr(image_type_to_extension($srcinfo[2]), 1));
    $createfun = 'imagecreatefrom' . ($type == 'jpg' ? 'jpeg' : $type);
 
    $dst_h = $height;
    $dst_w = $width;
    $x = $y = 0;

    if($width> $src_w)
        $dst_w = $width = $src_w;
    if($height> $src_h)
        $dst_h = $height = $src_h;
 
    if(!$width && !$height)
        return false;
    if(!$cut)
    {
        if($dst_w && $dst_h)
        {
            if($dst_w/$src_w> $dst_h/$src_h)
            {
                $dst_w = $src_w * ($dst_h / $src_h);
                $x = 0 - ($dst_w - $width) / 2;
            }
            else
            {
                $dst_h = $src_h * ($dst_w / $src_w);
                $y = 0 - ($dst_h - $height) / 2;
            }
        }
        else if($dst_w xor $dst_h)
        {
            if($dst_w && !$dst_h)  //有宽无高
            {
                $propor = $dst_w / $src_w;
                $height = $dst_h  = $src_h * $propor;
            }
            else if(!$dst_w && $dst_h)  //有高无宽
            {
                $propor = $dst_h / $src_h;
                $width  = $dst_w = $src_w * $propor;
            }
        }
    }
    else
    {
        if(!$dst_h)  //裁剪时无高
            $height = $dst_h = $dst_w;
        if(!$dst_w)  //裁剪时无宽
            $width = $dst_w = $dst_h;
        $propor = min(max($dst_w / $src_w, $dst_h / $src_h), 1);
        $dst_w = (int)round($src_w * $propor);
        $dst_h = (int)round($src_h * $propor);
        $x = ($width - $dst_w) / 2;
        $y = ($height - $dst_h) / 2;
    }
 
    $src = $createfun($src_img);
    $dst = imagecreatetruecolor($width ? $width : $dst_w, $height ? $height : $dst_h);
    $white = imagecolorallocate($dst, 255, 255, 255);
    imagefill($dst, 0, 0, $white);
 
    if(function_exists('imagecopyresampled'))
        imagecopyresampled($dst, $src, $x, $y, 0, 0, $dst_w, $dst_h, $src_w, $src_h);
    else
        imagecopyresized($dst, $src, $x, $y, 0, 0, $dst_w, $dst_h, $src_w, $src_h);
    imagejpeg($dst, $dst_img, $jpgquality);
    imagedestroy($dst);
    imagedestroy($src);
    return true;
}
/**
 * 实现类似ASP中的Application对象。采用文件存储方式。默认保存到cache文件夹。
 * 与Application不同，保存数据永久有效。
 */
function getapplication($name, $defvalue='') {
    if (!is_file(PATH_ROOT.'cache/' . $name . '.alt'))
        return $defvalue;
    return file_get_contents(PATH_ROOT.'cache/' . $name . '.alt');
}
function setapplication($name, $value) {
    if (!is_dir(PATH_ROOT.'cache/'))
        mkdir(PATH_ROOT.'cache/', 0777);
    $fp = fopen(PATH_ROOT.'cache/' . $name . '.alt', 'w');
    fwrite($fp, $value);
    fclose($fp);
}

/**
 * 保存log到本地。
 */
function savelogfile($types,$msg,$isrequest=false,$path='log/')
{
    $filename = $GLOBALS['cachedir'].$path.$types.'.log';
    if (makedir(dirname($filename))) {
        if ($fp = fopen($filename, 'a')) {
            if($isrequest)
            {
                $msg.=' GET:';
                foreach ($_GET as $key => $value)
                    $msg.=$key.'='.$value.'&';
                $msg.=' POST:';
                foreach ($_POST as $key => $value)
                    $msg.=$key.'='.$value.'&';
            }
            $msg .= "\r\n";
            if (@fwrite($fp, date('Y-m-d H:i:s')."\t".$msg)) {
                fclose($fp);
                return true;
            } else {
                fclose($fp);
                return false;
            } 
        } 
    }
}

/**
 * 循环建立新文件夹。
 */
function makedir($dir) {
    if (!$dir)
        return false;
    if(!is_dir($dir))
        return mkdir($dir,0777,true);
    return true;
}
/**
 * 保存文本数据到本地文件。
 */
function savefile($filename, $text) {
    if (!$filename || !$text)
        return false;
    if (makedir(dirname($filename))) {
        $filename = iconv("UTF-8", "GBK", $filename);
        if ($fp = fopen($filename, "w")) {
            if (@fwrite($fp, $text)) {
                fclose($fp);
                return true;
            } else {
                fclose($fp);
                return false;
            } 
        } 
    } 
    return false;
}
/**
 * 静默删除文件。
 */
function delfile($file)
{
    try{unlink($file);}catch(Exception $ex){}
}
/**
 * 调试打印函数。也可以用var_dump。
 * 对于需要立即输出的情况，用本函数更方便。
 */
function pr($var) {
    
    echo "\n".'<pre>';
    if (is_null($var))
        echo 'null';
    else if (is_long($var))
        echo 'long:' . $var;
    else if (is_integer($var))
        echo 'int:' . $var;
    else if (is_string($var))
    {
        if(empty($var))
            echo 'str:---空----';
        else
            echo 'str:' . $var;
    }
    else if (is_bool($var))
        echo 'bool:' . ($var ? 'true' : 'false');
    else {
        echo 'Type:' . gettype($var) . "\n";
        print_r($var);
    }
    echo '</pre>'."\n";
    @ob_flush();
    flush();
}