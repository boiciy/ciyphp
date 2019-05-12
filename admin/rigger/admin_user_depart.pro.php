<?php
$mydata = new ciy_data();
$rsuser = verifyadmin();
$table = 'p_admin';
ciy_runJSON();
$msql = new ciy_sql($table.'depart');
$msql->where('title like',get('title'))->order('id');
$rows = $mydata->get($msql);
function json_del() {
    global $mydata;
    global $table;
    $post = new ciy_post();
    $id = $post->getint('id');
    $csql = new ciy_sql($table.'depart');
    $csql->where('upid',$id);
    $departcnt = (int)$mydata->get1($csql);
    if($departcnt > 0)
        return errjson("该部门有{$departcnt}个下属部门，请先删除下属部门");
    $csql = new ciy_sql($table);
    $csql->where('departid',$id);
    $admincnt = (int)$mydata->get1($csql);
    if($admincnt > 0)
        return errjson("该部门有{$admincnt}位下属成员，请先删除成员");
    $csql = new ciy_sql($table.'depart');
    $csql->where('id',$id);
    $oldrow = $mydata->getone($csql);
    $execute = $mydata->delete($csql);
    if ($execute === false)
        return errjson('操作失败:'.$mydata->error);
    savelogdb($table.'depart', $oldrow, null, '部门，');
    return succjson();
}