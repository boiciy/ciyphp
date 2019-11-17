<?php
$mydata = new ciy_data();
$rsuser = verifyadmin();
$table = 'p_user';
ciy_runJSON();
$code_user_level = getcodes('user_level');
$code_user_wxstatus = getcodes('user_wxstatus');
$msql = new ciy_sql($table);
$liid = getint('liid');
if($liid > 0)
    $msql->where('level',$liid);
$eid = deid(get('eid'));
if($eid == 0)
    $eid = getint('eid');
if($eid > 0)
    $msql->where('id',$eid);
$msql->where('nickname like',get('nickname'));
$msql->where('mobile',get('mobile'));
$msql->order(get('order','id desc'));
$pageno = getint('pageno', 1);$pagecount = 20;
$msql->limit($pageno,$pagecount);
$rows = $mydata->get($msql,$mainrowcount);
function json_del() {
    global $mydata;
    global $table;
    $post = new ciy_post();
    $id = $post->getint('id');
    $csql = new ciy_sql($table);
    $csql->where('id',$id);
    $ret = $mydata->delete($csql);
    if($ret === false)
        return errjson('删除失败:'.$mydata->error);
    return succjson();
}