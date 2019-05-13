<?php
$mydata = new ciy_data();
$rsuser = verifyadmin();
$table = 'p_admin';
ciy_runJSON();
$code_user = getcodes('user');
$msql = new ciy_sql($table);
$liid = getint('liid');
if($liid > 0)
    $msql->where('status',$liid);
$nav = '';
$departid = getint('departid');
if($departid > 0)
{
    $msql->where('departid',$departid);
    $csql = new ciy_sql($table.'depart');
    $csql->where('id',$departid)->column('title');
    $nav = ' → '.$mydata->get1($csql);
}
$msql->where('truename like',get('truename'));
$msql->where('depart like',get('depart'));
$msql->where('mobile',get('mobile'));
$msql->where('id',get('id'));
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
    $oldrow = $mydata->getone($csql);
    $ret = $mydata->tran(function()use($mydata,$csql){
        return $mydata->delete($csql);
    });
    if($ret === false)
        return errjson('删除失败:'.$mydata->error);
    savelogdb($table, $oldrow, null, '删除了管理员，');
    return succjson();
}