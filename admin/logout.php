<?php
require 'init.php';
$mydata = new ciy_data();
$rsuser = verifyadmin();
$oid = (int)cookie('aoid');
$csql = new ciy_sql('p_admin_online');
$csql->where('id',$oid);
$execute = $mydata->delete($csql);
savelog('LOGIN', '退出登录');
cookieadmin(0,0,0,0,true);
diegoto('login.php');
