<?php
$mydata = new ciy_data();
$rsuser = verify();
//if(!$rsuser)
//    dieshowhtmlalert('您还未登陆');
ciy_runJSON();
$id = (int)get('id');
$btnname = '　更新　';
$updaterow = $mydata->getone('d_test', 'id=' . $id);
if($updaterow == null || $updaterow === false)
    $btnname = '　新增　';

function json_update() {
    global $mydata;
    global $rsuser;
    $updata = array();
    $updatainsert = array();
    $id = (int) post('id');
    $updata['truename'] = post('truename');
    $updata['icon'] = post('icon');
    $updata['scores'] = post('scores');
    $updatainsert['addtimes'] = getnow();
    $updatainsert['ip'] = getip();
    $execute = $mydata->set($updata, 'd_test', 'id=' . $id,'auto',$updatainsert);
    if ($execute === null)
        return errjson('操作数据库失败.' . $mydata->error);
    return succjson();
}
