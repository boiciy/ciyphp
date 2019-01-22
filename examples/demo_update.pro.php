<?php
$mydata = new ciy_data();
$rsuser = verify();
$table = 'd_test';
ciy_runJSON();
$id = getint('id');
$csql = new ciy_sql($table);
$csql->where('id',$id);
$updaterow = $mydata->getone($csql);
$btnname = '更新';
if($updaterow == null || $updaterow === false)
    $btnname = '新增';

function json_update() {
    global $mydata;
    global $table;
    $post = new ciy_post();
    $updata = array();
    $updatainsert = array();
    $id = $post->getint('id');
    $updata['truename'] = $post->get('truename');
    $updata['icon'] = $post->get('icon');
    $updata['scores'] = $post->getint('scores');
    $updata['activetime'] = time();
    $updatainsert['addtimes'] = time();
    $updatainsert['ip'] = getip();
    $csql = new ciy_sql($table);
    $csql->where('id',$id);
    $oldrow = $mydata->getone($csql);
    $execute = $mydata->data($updata)->datainsert($updatainsert)->set($csql);
    if ($execute === false)
        return errjson('操作数据库失败.' . $mydata->error);
    return succjson();
}
