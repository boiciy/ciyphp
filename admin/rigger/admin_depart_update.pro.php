<?php
$mydata = new ciy_data();
$rsuser = verifyadmin();
if(nopower('admin')) diehtml('您无权限');
$table = 'p_admin_depart';
ciy_runJSON();
$id = (int)get('id');
$upid = (int)get('upid');
$btnname = '更新';
$msql = new ciy_sql($table);
$msql->where('id',$id);
$updaterow = $mydata->getone($msql);
if(!is_array($updaterow))
{
    $btnname = '新增';
    $updaterow['upid'] = $upid;
}
if($upid == 0)
    $updepart = '《根部门》';
else
{
    $csql = new ciy_sql($table);
    $csql->where('id',$upid)->column('title');
    $updepart = $mydata->get1($csql);
}
$code_power = getcodes('user_power');
$csql = new ciy_sql('p_admin_role');
$roles = $mydata->get($csql);

function json_update() {
    global $mydata;
    global $table;
    if(nopower('admin'))
        return errjson('您无权限');
    $post = new ciy_post();
    $updata = array();
    $updatainsert = array();
    $id = $post->getint('id');
    $title = $post->get('title');
    if($title == '')
        return errjson('请填写部门名称');
    $updata['title'] = $title;
    $updata['upid'] = $post->getint('upid');
    $updata['defroles'] = $post->get('defroles');
    $csql = new ciy_sql($table);
    $csql->where('id',$id);
    $oldrow = $mydata->getone($csql);
    $execute = $mydata->data($updata)->set($csql);
    if ($execute === false)
        return errjson('操作数据库失败.' . $mydata->error);
    savelogdb($table, $oldrow, $updata,"部门ID={$id}，");
    return succjson();
}
