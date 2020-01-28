<?php
$mydata = new ciy_data();
$table = 'p_user';
ciy_runJSON();

function json_login() {
    global $mydata;
    global $table;
    //仅为演示，用户单设备登录
    $post = new ciy_post();
    $user = $post->get('user');
    if(empty($user))
        return errjson('请填写用户名');
    $csql = new ciy_sql($table);
    $csql->where('user',$user);
    $userrow = $mydata->getone($csql);//仅为代码，请自行建表
    if ($userrow === false)
        return errjson($mydata->error);
    if (!is_array($userrow))
        return errjson('用户名不存在');
    if ($userrow['pass'] != md5($post->get('pass').ciy_config::$conmmonkey))
        return errjson('用户名或密码错误.');
    $id = $userrow['id'];
    $sid = uniqid();
    $updata = array();
    $updata['sid'] = $sid;
    $updata['logintime'] = time();
    $sql = new ciy_sql($table);
    $sql->where('id',$id);
    $execute = $mydata->data($updata)->set($sql, 'update');
    if($execute === false)
        return errjson('logintime数据库更新失败:'.$mydata->error);
    $cookieexp = time() + 360000000;
    setcookie('uid', $id, $cookieexp);
    setcookie('sid', $sid, $cookieexp);
    return succjson();
}