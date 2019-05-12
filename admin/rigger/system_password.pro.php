<?php
$mydata = new ciy_data();
$rsuser = verifyadmin();
$table = 'p_admin';
ciy_runJSON();

function json_update() {
    global $mydata;
    global $rsuser;
    global $table;
    $post = new ciy_post();
    if ($rsuser['password'] != md5($post->get('oldpass').ciy_config::$conmmonkey))
        return errjson('原密码错误.');
    $updata = array();
    $updata['password'] = md5($post->get('pass').ciy_config::$conmmonkey);
    $csql = new ciy_sql($table);
    $csql->where('id',$rsuser['id']);
    $execute = $mydata->data($updata)->set($csql, 'update');
    if($execute === false)
        return errjson('数据库更新失败:'.$mydata->error);
    savelog('ADMIN', '修改密码'.$updata['password']);
    return succjson();
}