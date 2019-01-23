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
define('USER', 'ciyphp');
define('TOKEN', 'ciy');
require 'init.php';
$mydata = new ciy_data();
ciy_runJSON();
/**
 * 自定义授权函数。
 * 调用方将传递  _user / _pass 两个参数。用于授权校验。
 */
function verifysoft()
{
    $t = get('_t');
    if(abs(time() - (int)$t) > 30)
        return false;
    if(get('_user') != USER)
        return false;
    if(get('_token') != md5(TOKEN.$t))
        return false;
    return true;
}
function json_get() {
    global $mydata;
    if (!verifysoft())
        return errjson('授权失败');
    $type = get('type');
    $query = get('query');
    $data = json_decode(file_get_contents('php://input'), true);
    $retdata = $mydata->getraw($query,$data);
    if($retdata === false)
        return errjson($mydata->error);
    return succjson(array('data'=>$retdata));
}

function json_set() {
    global $mydata;
    if (!verifysoft())
        return errjson('授权失败');
    $type = get('type');
    $data = json_decode(file_get_contents('php://input'), true);
    $csql = new ciy_sql();
    $csql->table($data['table']);
    if(count($data['tsmt']) == 1)
        $csql->where($data['where'],$data['tsmt'][0],'m');
    else
        $csql->where($data['where'],$data['tsmt'],'m');
    $csql->column($data['column']);
    $csql->order($data['order']);
    $csql->group($data['group']);
    $mydata->data($data['updata']);
    $mydata->datainsert($data['insertdata']);
    $data = $mydata->set($csql,$type);
    if($data === false)
        return errjson($mydata->error);
    return succjson(array('data'=>$data));
}
function json_execute() {
    global $mydata;
    if (!verifysoft())
        return errjson('授权失败');
    $query = get('query');
    $data = json_decode(file_get_contents('php://input'), true);
    $retdata = $mydata->execute($query,$data);
    if($retdata === false)
        return errjson($mydata->error);
    return succjson(array('data'=>$retdata));
}