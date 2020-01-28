<?php
$mydata = new ciy_data();
$rsuser = verifyadmin();
$table = 'p_vcode';
ciy_runJSON();
$msql = new ciy_sql($table);
$msql->where('mobile',get('mobile'));
$msql->order(get('order','id desc'));
$pageno = getint('pageno', 1);$pagecount = 20;
$msql->limit($pageno,$pagecount);
$rows = $mydata->get($msql,$mainrowcount);
function json_newtest() {
    global $mydata;
    global $table;
    global $rsuser;
    $post = new ciy_post();
    $mobile = $post->get('mobile');
    $code = $post->get('code');
    $updata = array();
    $updata['userid'] = $rsuser['id'];
    $updata['mobile'] = $mobile;
    $updata['code'] = $code;
    $updata['addtimes'] = getnow();
    $updata['ip'] = getip();
    $csql = new ciy_sql($table);
    $execute = $mydata->data($updata)->set($csql, 'insert');
    if ($execute === false)
        return errjson('操作失败:'.$mydata->error);
    return succjson();
}
function json_del() {
    global $mydata;
    global $table;
    $post = new ciy_post();
    $ids = $post->get('ids');
    $csql = new ciy_sql($table);
    $csql->where('id in',$ids);
    $execute = $mydata->delete($csql);
    if ($execute === false)
        return errjson('操作失败:'.$mydata->error);
    return succjson();
}