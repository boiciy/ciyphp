<?php
/* =================================================================================
 * 版权声明：保留开源作者及版权声明前提下，开源代码可进行修改及用于任何商业用途。
 * 开源作者：众产国际产业公会  http://ciy.cn/code
 * 版本：0.7.0
====================================================================================*/
/*
 * common.php 常用公共函数库
 * 
 * diegoto                              页面302跳转
 * get/post/request/cookie              获取用户输入数据
 * ciy_runJSON/succjson/errjson         本页面Ajax请求处理函数及快捷返回函数
 * g_substr/g_strlen                    汉字字符串处理函数
 * urlparam                             Url参数拼接函数
 * getnow/getip/todate                  数据库转换时间和IP的快捷函数，建议用bigint替代datetime存储
 * getstrparam/setstrparam              比json还简化的多数据保存方式，一般用于单列多项设置的数据库保存。
 * file_down/img2thumb                  文件下载到本地和生成图片缩略图
 * makedir/delfile                      创建多层新文件夹/文件静默删除
 * pr/var_dump                          PHP调试变量界面打印
 * ciy_post                             payload json参数处理类
 * 
 * 版本更新：
 * 0.7.0  2019-07-18 Excel函数
 * 0.6.0  2019-01-18 POST接口更新
 * 0.5.2  2018-09-09 配置接口更新
 * 0.5.0  2018-05-05 源码开源
 * 0.1.0  2014-06-01 初始版本

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
    {
        if(!empty($value))
            $url .= $key . '=' . $value . '&';
    }
    $url = substr($url, 0, -1);
    return $url;
}

function todate($time,$format = 'i')
{
    if($time == 0)
        return '--';
    if($format == 'H')
        return date('Y-m-d H',$time);
    if($format == 'd')
        return date('Y-m-d',$time);
    if($format == 'hi')
        return date('H:i',$time);
    if($format == 'm')
        return date('Y-m',$time);
    if($format == 's')
        return date('Y-m-d H:i:s',$time);
    return date('Y-m-d H:i',$time);
}
/**
 * 返回mysql数据库所用的标准datetime格式字符串。
 * 用非mysql数据库，请注意修改该函数。
 *   例：getnow() = '2018-01-20 14:54:34'
 * 重要：mysql存储时间建议使用bigint取代datetime
 */
function getnow($time = null) {
    if($time === null)
        return date('Y-m-d H:i:s');
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
    if (!isset($_GET['json']))
        return;
    $funcname = 'json_' . get('func');
    if (!function_exists($funcname))
        $retarr = array('result' => false, 'errcode' => 0, 'msg' => 'JSON 调用函数缺失');
    else
        $retarr = call_user_func($funcname);
    if($isform)
        return $retarr;
    $cb = get('callback');
    $jsonstr = json_encode($retarr);//JSON_PARTIAL_OUTPUT_ON_ERROR PHP7以上可以带此参数。
    if($jsonstr === false)
    {
        $retarr['msg'] = utf8_encode($retarr['msg']);
        $jsonstr = json_encode($retarr);
        if($jsonstr === false)
        {
            $retarr['msg'] = json_last_error_msg();
            $jsonstr = json_encode($retarr);
        }
    }
    if (empty($cb))
        echo $jsonstr;
    else
        echo $cb . '(' . $jsonstr . ')';
    exit;
}

function get($name, $defvalue = '') {
    if(!isset($_GET[$name]))
        return $defvalue;
    $val = $_GET[$name];
    return _checkstr($val,$defvalue,$name);
}
function getint($name, $defvalue = 0) {
    if(!isset($_GET[$name]))
        return $defvalue;
    return (int)$_GET[$name];
}

function cookie($name, $defvalue = '') {
    return isset($_COOKIE[$name]) ? _checkstr($_COOKIE[$name],$defvalue,'Cookie--'.$name) : $defvalue;
}

function post($name, $defvalue = '') {
    return isset($_POST[$name]) ? _checkstr($_POST[$name],$defvalue,$name) : $defvalue;
}
function request($name, $defvalue = '') {
    return isset($_REQUEST[$name]) ? _checkstr($_REQUEST[$name],$defvalue,$name) : $defvalue;
}
function _checkstr($val,$defvalue,$name = '')
{
    if(!is_string($val))
        return $val;
    $cnt = strlen($val);
    for($i=0;$i<$cnt;$i++)
    {
        if(ord($val[$i]) == 0)
        {
            savelogfile('checkstr',"ORD={$cnt},name={$name},value={$val},url=".@$_SERVER['HTTP_HOST'].@$_SERVER['REQUEST_URI'].",cookie=".@$_SERVER['HTTP_COOKIE']);
            return $defvalue;
        }
    }
    return $val;
}

/**
 * 保存log到本地。
 */
function savelogfile($types,$msg,$isrequest=false,$path='log/')
{
    if(strpos($types,'/') !== false || strpos($types,'\\') !== false)
        $types = '_def';
    $filename = PATH_ROOT.$path.$types.'.log';
    if (makedir(dirname($filename))) {
        if ($fp = fopen($filename, 'a')) {
            if($isrequest)
            {
                $msg.=' GET:';
                foreach ($_GET as $key => $value)
                    $msg.=$key.'='.$value.'&';
                $msg.=' POST:'.file_get_contents('php://input');
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
function pr() {
    echo "\n".'<pre>';
    $first = true;
    foreach(func_get_args() as $var) {
        if (is_null($var))
            echo 'null';
        else if (is_long($var))
            echo 'long:' . $var;
        else if (is_integer($var))
            echo 'int:' . $var;
        else if (is_string($var))
        {
            if(strlen($var) == 0)
                echo 'str:---空----';
            else
            {
                if($first&&func_num_args()>1)
                    echo '<kbd>' . $var.'</kbd>';
                else
                    echo 'str:' . $var;
            }
        }
        else if (is_bool($var))
            echo 'bool:' . ($var ? 'true' : 'false');
        else {
            echo 'Type:' . gettype($var) . "\n";
            print_r($var);
        }
        $first = false;
        echo "\n";
    }
    echo '</pre>'."\n";
    @ob_flush();
    flush();
}
/**
 * payload json参数处理类
 * $post = new ciy_post();
 * $post->get('act');
 * $post->getarray('lists');
 * $post->getint('lists>id');//语法糖，不大建议使用
 */
class ciy_post {
    public $post;
    function __construct() {
        $this->post = json_decode(file_get_contents('php://input'), true);
        if($this->post === null)
            $this->post = $_POST;
    }
    function getraw($key,$defvalue = null) {
        if(strpos($key,'>') === false)
        {
            if(!isset($this->post[$key]))
                return $defvalue;
            return _checkstr($this->post[$key],$defvalue,$key);
        }
        $ks = explode('>', $key);
        if(!isset($this->post[$ks[0]]))
            return $defvalue;
        $data = $this->post[$ks[0]];
        $i = 0;
        $cnt = count($ks);
        while(true)
        {
            $i++;
            if($i >= $cnt)
                return _checkstr($data,$defvalue,$key);
            if(!isset($data[$ks[$i]]))
                return $defvalue;
            $data = $data[$ks[$i]];
        }
    }
    function getarray($key) {
        $data = $this->getraw($key);
        if(!is_array($data))
            return array();
        return $data;
    }
    function get($key,$defvalue = '') {
        return $this->getraw($key,$defvalue);
    }
    function getint($key,$defvalue = 0) {
        return (int)$this->getraw($key,$defvalue);
    }
    function getfloat($key,$defvalue = 0) {
        return (float)$this->getraw($key,$defvalue);
    }
    function getbool($key,$defvalue = false) {
        $data = $this->getraw($key,$defvalue);
        if($data === 'false')
            return false;
        return (bool)$data;
    }
}