<?php
$mydata = new ciy_data();
$rsuser = verify();
//if(!$rsuser)
//    dieshowhtmlalert('您还未登陆');
$table = 'd_test';
ciy_runJSON();
$id = (int)get('id');
$btnname = '更新';
$updaterow = $mydata->getone($table, 'id=' . $id);
if($updaterow == null || $updaterow === false)
    $btnname = '新增';

function json_update() {
    global $mydata;
    global $rsuser;
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
    $execute = $mydata->set($updata, $table, 'id=' . $id,'auto',$updatainsert);
    if ($execute === false)
        return errjson('操作数据库失败.' . $mydata->error);
    return succjson();
}
