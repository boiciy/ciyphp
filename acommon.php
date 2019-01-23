<?php
/* =================================================================================
 * 版权声明：保留开源作者及版权声明前提下，开源代码可进行修改及用于任何商业用途。
 * 开源作者：众产国际产业公会  http://ciy.cn/code
 * 版本：0.6.0
====================================================================================*/
/*
 * acommon.php 扩展函数库。
 * 
 * diehtml/diegoto     报错输出页面/跳转
 * encrypt  字符串加解密
 * enid/deid/id_calnumber   ID数字加解密
 * verify   判断用户是否合法
 * isweixin     判断客户端是否在微信中
 * get_millistime   获取当前微秒数
 * savelog  在数据库保存log信息。与savelogfile类似。
 */
function create_li($rows, $default,$first = array('title'=>'全部','codeid'=>''))
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
        $ret .= '><a href="?liid='.$row[$showcode].'">'.$row[$showtitle].'</a></li>';
    }
    return $ret;
}
function create_select($rows, $default, $formname, $opt)
{
    $showcode = 'codeid';
    $showtitle = 'title';
    $ret = '<select name="'.$formname.'" '.@$opt['ext'].'>';
    foreach ($rows as $row) {
        $ret .= '<option value="'.$row[$showcode].'"';
        if($default == $row[$showcode])
            $ret .= ' selected="true"';
        $ret .= '>'.$row[$showtitle].'</option>';
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
    foreach ($rows as $row) {
        echo '<label class="formi"><input type="checkbox" name="'.$formname.'" '.$attr.' value="'.$row[$showcode].'"';
        if(strpos($default,$dot.$row[$showcode].$dot) !== false)
            echo ' checked="checked"';
        echo '/><i></i>'.$row[$showtitle].'</label>';
    }
}
function create_queryone($rows, $default, $formname,$opt = null)
{
    if(!is_array($opt)) $opt = array();//(!is_array($opt)) && $opt = array();
    if(!isset($opt['showtitle'])) $opt['showtitle'] = 'title';
    if(!isset($opt['showcode'])) $opt['showcode'] = 'title';
    $showtitle = $opt['showtitle'];
    $showcode = $opt['showcode'];
    echo '<a href="'.urlparam('', array($formname=>'','pageno'=>1)).'" class="nm-query';
        if(get($formname) == '')
            echo ' active';
    echo '">不限</a>';
    foreach ($rows as $row) {
        echo '<a href="'.urlparam('', array($formname=>$row[$showcode],'pageno'=>1)).'" class="nm-query';
        if($row[$showcode] == get($formname))
            echo ' active';
        echo '">'.$row[$showtitle].'</a>';
    }
}
function getcodes($code)
{
    global $mydata;
    $csql = new ciy_sql('p_cata');
    $csql->where('types',$code)->order('nums desc,id');
    $catarows = $mydata->get($csql);
    return $catarows;
}
function ccode($rows,$code,$showcode = 'codeid',$showtitle = 'title')
{
    foreach($rows as $row)
    {
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
        $pagestr.= '<input class="n" type="text" name="topage" value="'.$pageno.'" style="width:3em;height:30px;text-align:center;margin:0 4px;"/><button onclick="location.href=\''.urlparam('', array('pageno' => '[topage]')).'\'.replace(\'[topage]\',$(\'input[name=topage]\').val());" class="btn btn-default">GO</button>';
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
    return '<i class="asc'.$asc.'" title="从小到大，升序排序" onclick="location.href=\''.urlparam('', array('order' => $field)).'\';"></i><i class="desc'.$desc.'" title="从大到小，降序排序" onclick="location.href=\''.urlparam('', array('order' => $field.' desc')).'\';"></i>';
}
/*
db.power: .xxx.
    if(nopower('admin,xxx'))
        return errjson('您无权操作');
 */
function nopower($rig)
{
    global $rsuser;
    if(!$rsuser)
        return true;
    $power = @$rsuser['power'];
    if(empty($power))
        return true;
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

function ismobile($mob)
{
    return preg_match( '/^1\d{10}$/',$mob);
}
function isweixin()
{
    $useragent = strtolower($_SERVER["HTTP_USER_AGENT"]);
    $is_weixin = strripos($useragent,'micromessenger');
    if($is_weixin > 0)
        return true;
    return false;
}
function get_millistime()
{
    $microtime = microtime();
    $comps = explode(' ', $microtime);
    return sprintf('%d%03d', $comps[1], $comps[0] * 1000);
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
    }
    $updata = array();
    $updata['types'] = $types;
    $updata['userid'] = (int)@$rsuser['id'];
    $updata['logs'] = $msg;
    $updata['addtimes'] = time();
    $updata['ip'] = getip();
    $mydata->data($updata)->set(new ciy_sql('p_log'));
}

function verifyadmin($errfunc = null) {
    global $mydata;
    $oid = (int)cookie('aoid');
    $uid = deid(cookie('auid'));
    $sql = new ciy_sql('p_adminonline');
    $sql->where('id',$oid);
    $onlinerow = $mydata->getone($sql);
    $err = '您尚未登录或已超时';
    if($onlinerow === false)
        $err = $mydata->error;
    else if (is_array($onlinerow))
    {
        if (cookie('asid') == $onlinerow['sid'])
        {
            if ($uid == $onlinerow['userid'])
            {
                $sql = new ciy_sql('p_admin');
                $sql->where('id',$uid);
                $userrow = $mydata->getone($sql);
                if($userrow === false)
                    $err = $mydata->error;
                else if(is_array($userrow))
                    return $userrow;
            }
        }
    }
    if ($errfunc instanceof Closure)
    {
        $errfunc($err);
        die();
        return;
    }
    if (isset($_GET['json']))
        die(json_encode(errjson($err)));
    if(NAME_SELF == 'manage')
        diegoto('login.php');
    diehtml($err.'<br/><a href="/admin/login.php" target="_top">重新登录</a>');
}