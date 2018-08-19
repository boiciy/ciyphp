<?php
$mydata = new ciy_data();
ciy_runJSON();

function json_login() {
    global $mydata;
    $userrow = $mydata->getone('d_user', 'user=\'' . post('user') . '\'');//仅为代码，请自行建表
    if (!is_array($userrow))
        return errjson('用户名不存在');
    if ($userrow['pass'] != md5(post('password').'3ASDtkw'))
        return errjson('用户名或密码错误.');
    $id = $userrow['id'];
    $sid = uniqid();
    $updata = array();
    $updata['sid'] = $sid;
    $updata['logintime'] = Getnow();
    $execute = $mydata->set($updata, 'd_user', 'id='.$id, 'update');
    if($execute === false)
        return errjson('logintime数据库更新失败:'.$mydata->error);
    $cookieexp = time() + 360000000;
    setcookie('auid', $id, $cookieexp);
    setcookie('asid', $sid, $cookieexp);
    return succjson();
}