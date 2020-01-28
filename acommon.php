<?php
/* =================================================================================
 * 版权声明：保留开源作者及版权声明前提下，开源代码可进行修改及用于任何商业用途。
 * 开源作者：众产国际产业公会  http://ciy.cn/code
 * 版本：0.6.1
====================================================================================*/
/*
 * acommon.php 扩展函数库。
 * 
 * diehtml      报错输出页面/跳转
 * encrypt      字符串加解密
 * enid/deid    ID数字加解密
 * verify       判断用户是否合法
 * isweixin     判断客户端是否在微信中
 * get_mstime   获取当前微秒数
 * savelog      在数据库保存log信息。
 */
function cmoney($money)
{
    $money/=100;
    return number_format($money, 2);
}
function create_li($rows, $default,$exturl = '',$first = array('title'=>'全部','codeid'=>''))
{
    if(is_array($first))
        array_unshift($rows,$first);
    $showcode = 'codeid';
    $showtitle = 'title';
    $ret = '';
    foreach ($rows as $row) {
        $ret .= '<li';
        if($default == $row[$showcode])
            $ret .= ' class="active"';
        $ret .= '><a href="?liid='.$row[$showcode].$exturl.'">'.$row[$showtitle].'</a></li>';
    }
    return $ret;
}
function create_select($rows, $default, $formname, $itemfunc = null)
{
    $ret = '<select name="'.$formname.'">';
    foreach ($rows as $row) {
        if ($itemfunc instanceof Closure)
            $ret .= $itemfunc($row);
        else
        {
            $kw['title'] = $row['title'];
            $kw['value'] = $row['codeid'];
            $ret .= '<option value="'.$kw['value'].'"';
            if($default == $kw['value'])
                $ret .= ' selected="true"';
            $ret .= '>'.$kw['title'].'</option>';
        }
    }
    $ret .= '</select>';
    return $ret;
}
function create_checkbox($rows, $default, $formname,$opt = null)
{
    $dot = '';
    $attr = '';
    if(is_array($opt))
    {
        $dot = isset($opt['dot']) ? $opt['dot'] : '';
        $attr = isset($opt['attr']) ? $opt['attr'] : '';
    }
    $showcode = 'codeid';
    $showtitle = 'title';
    $ret = '';
    foreach ($rows as $row) {
        $ret.='<label class="formi"><input type="checkbox" name="'.$formname.'" '.$attr.' value="'.$row[$showcode].'"';
        if(strpos($default,$dot.$row[$showcode].$dot) !== false)
            $ret.=' checked="checked"';
        $ret.='/><i></i>'.$row[$showtitle].'</label>';
    }
    return $ret;
}
function create_radio($rows, $default, $formname, $itemfunc = null)
{
    $ret = '';
    foreach ($rows as $row) {
        if ($itemfunc instanceof Closure)
            $kw = $itemfunc($row);
        else
        {
            $kw['title'] = $row['title'];
            $kw['value'] = $row['codeid'];
        }
        $ret.='<label class="formi"><input type="radio" name="'.$formname.'" value="'.$kw['value'].'"';
        if($default == $kw['value'])
            $ret.=' checked="checked"';
        $ret.='/><i></i>'.$kw['title'].'</label>';
    }
    return $ret;
}
function getconfig($code,$defp1,$defp2 = null,$defp3 = null){
    global $mydata;
    $csql = new ciy_sql('p_config');
    $csql->where('types',$code);
    $row = $mydata->getone($csql);
    if(is_array($row)){
        if($defp2 == null)
            return $row['params'];
        if($defp3 == null)
            return array($row['params'],$row['param2']);
        return array($row['params'],$row['param2'],$row['param3']);
    }else{
        if($defp2 == null)
            return $defp1;
        if($defp3 == null)
            return array($defp1,$defp2);
        return array($defp1,$defp2,$defp3);
    }
}
function getcodes($code,$upid = -1)
{
    global $mydata;
    $csql = new ciy_sql('p_cata');
    $csql->where('types',$code)->order('nums,id');
    if($upid>-1)
        $csql->where('upid',$upid);
    $catarows = $mydata->get($csql);
    return $catarows;
}
function ccode($rows,$code,$showcode = 'codeid',$showtitle = 'title')
{
    foreach($rows as $row){
        if($code == $row[$showcode])
            return $row[$showtitle];
    }
    return $code;
}
function treerows_sort(&$rows,$upfield = 'upid',$upid=0,$deep=0)//树形排序
{
    $ret = array();
    $cnt = count($rows);
    for($i=0;$i<$cnt;$i++)
    {
        if($rows[$i][$upfield] == $upid)
        {
            $subrows = treerows_sort($rows,$upfield,$rows[$i]['id'],$deep+1);
            $rows[$i]['_count'] = count($subrows);
            $rows[$i]['_deep'] = $deep;
            $ret[] = $rows[$i];
            $ret = array_merge($ret,$subrows);
        }
    }
    if($deep == 0)
    {
        for($i=0;$i<$cnt;$i++)
        {
            if(!isset($rows[$i]['_deep']))
            {
                $rows[$i]['_count'] = 0;
                $rows[$i]['_deep'] = 0;
                $ret[] = $rows[$i];
            }
        }
    }
    return $ret;
}
function diehtml($msg,$title='提示信息') {
    echo '<!DOCTYPE html><html><head><meta http-equiv="Content-type" content="text/html; charset=utf-8">';
    echo '<title>'.$title.'</title><meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0"/><meta name="format-detection" content="telephone=no,email=no"/>';
    echo '<meta name="apple-mobile-web-app-capable" content="yes" /></head><body>';
    echo '<fieldset style="margin:2em;border-radius: 0.5em;border: 1px solid #eeeeee;line-height:2em;"><legend style="font-size: 1.3em;padding: 0.2em 0.5em;">'.$title.'</legend><div style="padding:0 15px 15px 15px;"><b>来源： '.NAME_SELF.'</b><br/>'.$msg.'</div></fieldset>';
    echo '</body></html>';
    die();
}

function showpage($pageno,$pagecount, $rowcount, $showpages = 5) {
    $pagestr= '<div class="ciy-page"><div class="ciy-page-txt">'.$rowcount.'条 '.$pagecount.'条/页</div>';
    $pagemax = ceil($rowcount / $pagecount);
    if ($pageno > $pagemax)
        $pageno = $pagemax;
    if ($pageno < 1)
        $pageno = 1;
    if ($pageno > 1)
    {
        $pagestr.= '<a href="' . urlparam('', array('pageno' => 1)) . '">&lt;&lt;</a>';
        $pagestr.= '<a href="' . urlparam('', array('pageno' => ($pageno - 1))) . '">&lt;</a>';
    }
    $spage = 1;
    if ($pageno > $showpages)
        $spage = $pageno - $showpages;
    $epage = $pagemax;
    if ($pageno < $pagemax - $showpages)
        $epage = $pageno + $showpages;
    for ($i = $spage; $i <= $epage; $i++) {
        if ($i == $pageno)
            $pagestr.= '<a class="current">' . $i . '</a>';
        else
            $pagestr.= '<a href="' . urlparam('', array('pageno' => $i)) . '">' . $i . '</a>';
    }
    if ($pageno < $pagemax)
    {
        $pagestr.= '<a href="' . urlparam('', array('pageno' => ($pageno + 1))) . '">&gt;</a>';
        $pagestr.= '<a href="' . urlparam('', array('pageno' => $pagemax)) . '">&gt;&gt;</a>';
    }
    if($pagemax > $showpages)
        $pagestr.= '<input class="n" type="text" name="topage" value="'.$pageno.'" style="width:3em;min-height:2.2em;height:2.2em;text-align:center;margin:0 4px;"/><button onclick="location.href=\''.urlparam('', array('pageno' => '[topage]')).'\'.replace(\'[topage]\',$(\'input[name=topage]\').val());" class="btn btn-default">GO</button>';
    $pagestr.= '</div><div class="clearfix"></div>';
    return $pagestr;
}
function showorder($field)
{
    $order = get('order');
    $asc = '';
    $desc = '';
    if(strpos($order,$field) === 0)
    {
        if(strpos($order,' desc')>0)
            $desc = ' active';
        else
            $asc = ' active';
    }
    return '<i class="asc'.$asc.'" title="升序排序" onclick="location.href=\''.urlparam('', array('order' => $field)).'\';"></i><i class="desc'.$desc.'" title="降序排序" onclick="location.href=\''.urlparam('', array('order' => $field.' desc')).'\';"></i>';
}
function refreshpower($uid){
    global $mydata;
    $csql = new ciy_sql('p_admin_role');
    $csql->where('id in (select roleid from p_admin_urole where userid=? and status=10)',array($uid));
    $rolerows = $mydata->get($csql);
    $pss = array();
    foreach($rolerows as $rolerow)
    {
        $ps = explode('.',$rolerow['power']);
        $pss = array_merge($pss,$ps);
    }
    $newpower = implode('.',array_unique($pss)).'.';
    
    $execute = $mydata->execute('update p_admin set power=? where id=?',array($newpower,$uid));
    if ($execute === false)
        throw new Exception('操作admin数据库power失败:'.$mydata->error);
}
function nopower($rig)
{
    global $rsuser;
    if(!$rsuser)
        return true;
    $power = @$rsuser['power'];
    if(empty($power))
        return true;
    if($power == '.*.')//超级管理员
        return false;
    if(strpos($rig,',') === false)
        return (strpos($power,'.'.$rig.'.') === false);
    $rigs = explode(',', $rig);
    foreach($rigs as $r)
    {
        if(strpos($power,'.'.$r.'.') !== false)
            return false;
    }
    return true;
}
function power_trans($rows,$power)
{
    foreach($rows as $row)
        $power = str_replace('.'.$row['codeid'].'.','.'.$row['title'].'.',$power);
    return $power;
}
/* * *******************************************************************
 * 函数名称:encrypt
 * 函数作用:加密解密字符串
 * 安全函数，建议有能力自行修改一些
 * 使用方法:
 * 加密     :encrypt('str','E','nowamagic');
 * 解密     :encrypt('被加密过的字符串','D','nowamagic');
 * 参数说明:
 * $string   :需要加密解密的字符串
 * $operation:判断是加密还是解密:E:加密   D:解密
 * $key      :加密的钥匙(密匙);

 * 担心加密强度？哲学上没有无法破解的算法，只是有待科技提高。您稍加修改，提高破解成本即可。
* ******************************************************************* */
function encrypt($string, $operation, $key = '') {
    $key = md5($key);
    $key_length = strlen($key);
    $string = $operation == 'D' ? base64_decode(str_replace('_', '/', str_replace('-', '+', $string))) : substr(md5($string . $key), 0, 8) . $string;
    $string_length = strlen($string);
    $rndkey = $box = array();
    $result = '';
    for ($i = 0; $i <= 255; $i++) {
        $rndkey[$i] = ord($key[$i % $key_length]);
        $box[$i] = $i;
    }
    for ($j = $i = 0; $i < 256; $i++) {
        $j = ($j + $box[$i] + $rndkey[$i]) % 256;
        $tmp = $box[$i];
        $box[$i] = $box[$j];
        $box[$j] = $tmp;
    }
    for ($a = $j = $i = 0; $i < $string_length; $i++) {
        $a = ($a + 1) % 256;
        $j = ($j + $box[$a]) % 256;
        $tmp = $box[$a];
        $box[$a] = $box[$j];
        $box[$j] = $tmp;
        $result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
    }
    if ($operation == 'D') {
        if (substr($result, 0, 8) == substr(md5(substr($result, 8) . $key), 0, 8)) {
            return substr($result, 8);
        } else {
            return '';
        }
    } else {
        return str_replace('/', '_', str_replace('+', '-', str_replace('=', '', base64_encode($result))));
    }
}

/* * *******************************************************************
 * 函数名称:enid/deid
 * 函数作用:id加密函数/id解密函数。可以减轻黑客撞库的危害，商业竞争对手不易猜测数据规模。
 * 安全函数，建议有能力自行修改一些
 * 参数说明:
 * $id    两位数的数字id（id>=10）
 * 返回值：加密后的数字
 *   例：enid(8245) = 872435
 *   例：deid(872435) = 8245
* ******************************************************************* */
function enid($id)
{
    if($id<10)
        $strid = '0'.$id;
    else
        $strid = $id.'';
    $id = (int)$id;
    if($id == 0)
        return 0;
    $fx = id_calnumber($id);
    $ulen = strlen($strid);
    return substr($strid,0,1).$fx[0].substr($strid,1,$ulen-2).$fx[1].substr($strid,-1,1);
}
function deid($id)
{
    $strid = $id.'';
    $ulen = strlen($strid);
    $fx = substr($strid,1,1).substr($strid,-2,1);
    $id = (int)(substr($strid,0,1).substr($strid,2,$ulen-4).substr($strid,-1,1));
    if($fx == id_calnumber($id))
        return $id;
    return 0;
}
/* * *******************************************************************
 * 函数名称:calnumber
 * 函数作用:换算id的加密校验数，被enid、deid函数调用。
 * 参数说明:
 * $key    默认加密因子，可以自行修改。
 * $len    返回校验数位数。
 * 返回值：len位数字字符串
 *   例：calnumber(8245,224,2) = 73
* ******************************************************************* */
function id_calnumber($num, $key = 224,$len = 2) {
    if($num > 250600)
        $num %= 250600;
    $n = $num%8566;
    if($n < 100)$n+=100;
    $xx = abs($num*$n+$key);
    if($xx % 13 > 8)
        $xx+=$key;
    if($xx % 13 > 4)
        $xx+=$key+$key+$key;
    if($len < 1)$len=2;
    $ret = abs($xx%pow(10,$len));
    return sprintf('%0'.$len.'d',$ret);
}

function isweixin()
{
    $useragent = strtolower($_SERVER["HTTP_USER_AGENT"]);
    $is_weixin = strripos($useragent,'micromessenger');
    if($is_weixin > 0)
        return true;
    return false;
}
function get_mstime()
{
    $microtime = microtime();
    $comps = explode(' ', $microtime);
    return sprintf('%d%03d', $comps[1], $comps[0] * 1000);
}
function xssfilter($val)
{
    if(stripos($val,'\x3c') !== false || strpos($val,'\74') !== false || stripos($val,'eval') !== false)
    {
        return true;
    }
    return false;
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
    if (!makedir(PATH_ROOT.$savepath))
        return 'ERR:保存目录建立失败';
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
function img2thumb($src_img, $dst_img, $width = 75, $height = 75, $cut = false,$jpgquality=60)
{
    if(!is_file($src_img))
        return false;
    ini_set ('memory_limit', '1128M');
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
//addmessage('用户注册', 10, '有新用户注册<br/><a>前往查看</a>','来自系统');
function addmessage($types, $adminid, $content, $frommsg = '', $fromuid = 0){
    global $mydata;
    $updata = array();
    $updata['types'] = $types;
    $updata['userid'] = $adminid;
    $updata['content'] = $content;
    $updata['status'] = 1;
    $updata['fromuid'] = $fromuid;
    $updata['frommsg'] = $frommsg;
    $updata['addtimes'] = time();
    return $mydata->data($updata)->set(new ciy_sql('p_admin_msg'));
}
function savelogdb($types,$oldrow, $newrow, $msg = ''){
    if(is_array($oldrow) && is_array($newrow))
    {
        $msg .= '更新数据';
        $modify = false;
        foreach($newrow as $f=>$v)
        {
            if($oldrow[$f] != $v)
            {
                if($f == 'activetime')
                    continue;
                $msg .= '，'.$f.'='.$oldrow[$f].'→'.$v;
                $modify = true;
            }
        }
        if(!$modify)
            $msg .= '，无';
    }
    else if(is_array($newrow))
    {
        $msg .= '新增数据';
        foreach($newrow as $f=>$v)
            $msg .= '，'.$f.'='.$v;
    }
    else if(is_array($oldrow))
    {
        $msg .= '删除数据';
        foreach($oldrow as $f=>$v)
            $msg .= '，'.$f.'='.$v;
    }
    else
    {
        $msg .= '未删除数据';
    }
    savelog($types,$msg);
}
function savelog($types,$msg,$isrequest=false){
    global $mydata;
    global $rsuser;
    if($isrequest)
    {
        $msg.=' GET:';
        foreach ($_GET as $key => $value)
            $msg.=$key.'='.$value.'&';
        $msg.=' POST:';
        foreach ($_POST as $key => $value)
            $msg.=$key.'='.$value.'&';
        $msg.=' JSON:'.file_get_contents('php://input');
    }
    $updata = array();
    $updata['types'] = $types;
    $updata['userid'] = (int)@$rsuser['id'];
    $updata['logs'] = $msg;
    $updata['status'] = 0;
    $updata['readid'] = 0;
    $updata['addtimes'] = time();
    $updata['ip'] = getip();
    $mydata->data($updata)->set(new ciy_sql('p_log'));
}

function cookieadmin($oid,$uid,$sid,$exp,$logout = false) {
    if($logout)
        $cookieexp = time()-1;
    else
        $cookieexp = time() + 360000000;
    setcookie('aoid', $oid, $cookieexp,'/',null,false,true);
    setcookie('auid', enid($uid), $cookieexp,'/',null,false,true);
    setcookie('asid', $sid, $cookieexp,'/',null,false,true);
    setcookie('aexp', $exp, $cookieexp,'/',null,false,true);
}
function verifyadmin($errfunc = null) {
    global $mydata;
    $oid = (int)cookie('aoid');
    $exp = (int)cookie('aexp');
    $sid = cookie('asid');
    $uid = deid(cookie('auid'));
    $csql = new ciy_sql('p_admin_online');
    $csql->where('id',$oid);
    $onlinerow = $mydata->getone($csql);
    $err = '您尚未登录或已超时';
    if($onlinerow === false)
        $err = $mydata->error;
    else if (is_array($onlinerow))
    {
        if ($sid == $onlinerow['sid'])
        {
            if ($uid == $onlinerow['userid'])
            {
                if($onlinerow['exptime']>time())
                {
                    if($onlinerow['exptime']<time()+259200-86400)
                    {
                        $sid = uniqid();
                        $exp = time()+259200;
                        $execute = $mydata->execute('update p_admin_online set sid=?,exptime=? where id='.$oid,array($sid,$exp));
                        if($execute !== false)
                            cookieadmin($oid,$uid,$sid,$exp);
                    }
                    $csql = new ciy_sql('p_admin');
                    $csql->where('id',$uid);
                    $userrow = $mydata->getone($csql);
                    if($userrow === false)
                        $err = $mydata->error;
                    else if(is_array($userrow)){
                        if($userrow['status'] != 10)
                            $err = '您的账号被禁用，请联系管理员';
                        else
                        return $userrow;
                    }
                }
            }
            else
                savelog('LOGIN', "UID不一致！在尝试登录[OID={$oid}][UID={$uid}][SID={$sid}] DBUID=".$onlinerow['userid']);
        }
        else
            savelog('LOGIN', "SID不一致，在尝试登录[OID={$oid}][UID={$uid}][SID={$sid}] DBSID=".$onlinerow['sid']);
    }
    if ($errfunc instanceof Closure)
    {
        $errfunc($err);
        die();
        return;
    }
    if (isset($_GET['json']))
        die(json_encode(errjson($err)));
    if(NAME_SELF == 'index')
        diegoto('login.php');
    diehtml($err.'<br/><a href="/admin/login.php" target="_top">重新登录</a>');
}
