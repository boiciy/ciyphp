<?php
$mydata = new ciy_data();
$table = 'd_user';
ciy_runJSON();

function json_login() {
    global $mydata;
    global $table;
    //仅为演示，用户单设备登录
    $post = new ciy_post();
    $userrow = $mydata->getone($table, 'user=\'' . $post->get('user') . '\'');//仅为代码，请自行建表
    if (!is_array($userrow))
        return errjson('用户名不存在');
    if ($userrow['pass'] != md5($post->get('pass').ciy_config::$conmmonkey))
        return errjson('用户名或密码错误.');
    $id = $userrow['id'];
    $sid = uniqid();
    $updata = array();
    $updata['sid'] = $sid;
    $updata['logintime'] = time();
    $execute = $mydata->set($updata, $table, 'id='.$id, 'update');
    if($execute === false)
        return errjson('logintime数据库更新失败:'.$mydata->error);
    $cookieexp = time() + 360000000;
    setcookie('uid', $id, $cookieexp);
    setcookie('sid', $sid, $cookieexp);
    return succjson();
}