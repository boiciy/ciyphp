<?php
$mydata = new ciy_data();
$rsuser = verifyadmin();
if(nopower('admin')) diehtml('您无权限');
$table = 'p_admin_urole';
ciy_runJSON();
$code_urole = getcodes('user_urole');
$msql = new ciy_sql($table);
$liid = getint('liid');
if($liid > 0)
    $msql->where('status',$liid);
$msql->where('rolename like',get('rolename'));
$msql->where('username like',get('username'));
$msql->order(get('order','id desc'));
$pageno = getint('pageno', 1);$pagecount = 20;
$msql->limit($pageno,$pagecount);
$rows = $mydata->get($msql,$mainrowcount);

function json_status() {
    global $mydata;
    global $rsuser;
    global $table;
    if(nopower('admin'))
        return errjson('您无权限');
    $post = new ciy_post();
    $id = $post->getint('id');
    $status = $post->getint('status');
    $updata = array();
    $updata['status'] = $status;
    if($status == 10){
        $updatarole['adminid'] = $rsuser['id'];
        $updatarole['addtimes'] = time();
    }
    $csql = new ciy_sql($table);
    $csql->where('id',$id);
    $oldrow = $mydata->getone($csql);
    $execute = $mydata->data($updata)->set($csql,'update');
    if ($execute === false)
        throw new Exception('操作数据库失败:'.$mydata->error);
    //单独用户，更新权限。
    savelogdb($table, $oldrow, $updata,"更新了授权，ID={$id}，");
    return succjson();
}