<?php
/* =================================================================================
 * 版权声明：保留开源作者及版权声明前提下，开源代码可进行修改及用于任何商业用途。
 * 开源作者：众产国际产业公会  http://ciy.cn/code
 * 版本：0.5.2
 * 远程数据集中处理文件
====================================================================================*/
/**
 * 远程数据操作类库 透传执行文件
 * 如不需要该功能，请删除该文件。
 * 建议放到项目目录下执行。
*/
require 'init.php';
$mydata = new ciy_data();
ciy_runJSON();
/**
 * 自定义授权函数。
 * 调用方将传递  _user / _pass 两个参数。用于授权校验。
 */
function verifysoft()
{
    return false;
    if(get('_user') == 'root')
        return true;
    return false;
}
function json_sqlset() {
    global $mydata;
    if (!verifysoft())
        return errjson('授权失败');
    $table = get('table');
    $where = get('where');
    $type = get('type');
    $updata = json_decode(get('updata'),true);
    if(get('insertdata') != '')
        $insertdata = json_decode(get('insertdata'),true);
    else
        $insertdata = null;
    $data = $mydata->set($updata,$table,$where,$type,$insertdata);
    if($data === false)
        return errjson($mydata->error,1);
    return succjson(array('data'=>$data));
}
function json_sqldelete() {
    global $mydata;
    if (!verifysoft())
        return errjson('授权失败');
    $table = get('table');
    $where = get('where');
    $type = get('type');
    $data = $mydata->delete($table,$where,$type);
    if($data === false)
        return errjson($mydata->error,1);
    return succjson(array('data'=>$data));
}

function json_sqlexecute() {
    global $mydata;
    if (!verifysoft())
        return errjson('授权失败');
    $sqls = get('sql');
    $data = $mydata->execute($sql);
    if($data === false)
        return errjson($mydata->error,1);
    return succjson(array('data'=>$data));
}
function json_sqlgetone() {
    global $mydata;
    if (!verifysoft())
        return errjson('授权失败');
    $table = get('table');
    $order = get('order');
    $column = get('column');
    $where= get('where');
    $data = $mydata->getone($table,$where,$order,$column);
    if($data === false)
        return errjson($mydata->error,1);
    return succjson(array('data'=>$data));
}
function json_sqlscalar() {
    global $mydata;
    if (!verifysoft())
        return errjson('授权失败');
    $table = get('table');
    $order = get('order');
    $column = get('column');
    $where = get('where');
    $data = $mydata->getonescalar($table,$where,$column,$order);
    if($data === false)
        return errjson($mydata->error,1);
    return succjson(array('data'=>$data));
}

function json_sqlget() {
    global $mydata;
    if (!verifysoft())
        return errjson('授权失败');
    $table = get('table');
    $order = get('order');
    $column = get('column');
    $where = get('where');
    $pageno = (int)get('pageno');
    $pagecount = (int)get('pagecount');
    $data = $mydata->get($pageno,$pagecount,$table,$where,$order,$column);
    if($data === false)
        return errjson ($mydata->error,1);
    return succjson(array('data'=>$data));
}
