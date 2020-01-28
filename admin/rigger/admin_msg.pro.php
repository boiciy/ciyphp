<?php
$mydata = new ciy_data();
$rsuser = verifyadmin();
if(nopower('admin')) diehtml('您无权限');
$table = 'p_admin_msg';
ciy_runJSON();
$msql = new ciy_sql($table);
$liid = getint('liid');
if($liid != 10)
    $liid = 1;
$msql->where('status',$liid);
$msql->where('userid',$rsuser['id']);
$msql->where('types like',get('types'));
$msql->where('content like',get('content'));
$msql->order(get('order','id desc'));
$pageno = getint('pageno', 1);$pagecount = 20;
$msql->limit($pageno,$pagecount);
$rows = $mydata->get($msql,$mainrowcount);
function json_setread() {
    global $mydata;
    global $rsuser;
    global $table;
    $post = new ciy_post();
    $csql = new ciy_sql($table);
    $csql->where('id',$post->getint('id'));
    $csql->where('userid',$rsuser['id']);
    $updata = array();
    $updata['status'] = 10;
    $updata['readtimes'] = time();
    $execute = $mydata->data($updata)->set($csql);
    if ($execute === false)
        return errjson('操作失败:'.$mydata->error);
    return succjson();
}
function json_del() {
    global $mydata;
    global $rsuser;
    global $table;
    $post = new ciy_post();
    $ids = $post->get('ids');
    $csql = new ciy_sql($table);
    $csql->where('id in',$ids);
    $csql->where('userid',$rsuser['id']);
    $execute = $mydata->delete($csql);
    if ($execute === false)
        return errjson('操作失败:'.$mydata->error);
    return succjson();
}
function json_testsend() {
    global $rsuser;
    addmessage('测试消息', $rsuser['id'], '测试内容 '.date('Y-m-d H:i:s').'<br/><a href="https://ciy.cn/code" target="_blank">前往查看</a>','来自系统');
    return succjson();
}