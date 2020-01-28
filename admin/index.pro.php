<?php
$mydata = new ciy_data();
$rsuser = verifyadmin(function(){
    diegoto("login.php");
});
$csql = new ciy_sql('p_admin_msg');
$csql->where('userid',$rsuser['id'])->where('status',1);
$messagecnt = $mydata->get1($csql);