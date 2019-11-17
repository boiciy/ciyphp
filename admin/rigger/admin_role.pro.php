<?php
$mydata = new ciy_data();
$rsuser = verifyadmin();
if(nopower('admin')) diehtml('您无权限');
$table = 'p_admin_role';
ciy_runJSON();
$code_rolegroup = getcodes('user_rolegroup');
$msql = new ciy_sql($table);
$liid = getint('liid');
if($liid > 0)
    $msql->where('groups',$liid);
$msql->where('title like',get('title'));
$msql->order(get('order','id desc'));
$pageno = getint('pageno', 1);$pagecount = 20;
$msql->limit($pageno,$pagecount);
$rows = $mydata->get($msql,$mainrowcount);

function json_del() {
    global $mydata;
    global $table;
    if(nopower('admin'))
        return errjson('您无权限');
    $post = new ciy_post();
    $id = $post->getint('id');
    $csql = new ciy_sql('p_admin_urole');
    $csql->where('roleid',$id)->where('status',10);
    $urrow = $mydata->getone($csql);
    if(is_array($urrow))
        return errjson('该角色正在被使用，不能删除');
    
    $csql = new ciy_sql($table);
    $csql->where('id',$id);
    $oldrow = $mydata->getone($csql);
    $execute = $mydata->delete($csql);
    if($execute === false)
        return errjson('删除失败:'.$mydata->error);
    $csql = new ciy_sql('p_admin_urole');
    $csql->where('roleid',$id);
    $execute = $mydata->delete($csql);
    if($execute === false)
        return errjson('删除授权失败:'.$mydata->error);
    savelogdb($table, $oldrow, null, '删除了角色，');
    return succjson();
}