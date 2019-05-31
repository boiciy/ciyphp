<?php
$mydata = new ciy_data();
$rsuser = verifyadmin();
if(nopower('admin')) diehtml('您无权限');
$table = 'p_system';
ciy_runJSON();
$msql = new ciy_sql($table);
$msql->where('id',get('id'));
$msql->where('name like',get('name'));
$rows = $mydata->get($msql);
$rows[] = array('id'=>0,'status'=>0,'expsec'=>1800,'nextsec'=>3600,'nexttime'=>time());
function json_update() {
    global $mydata;
    global $table;
    if(nopower('admin'))
        return errjson('您无权操作');
    $post = new ciy_post();
    $id = $post->getint('id');
    $name = $post->get('name');
    $runfunc = $post->get('runfunc');
    $runrequire = $post->get('runrequire');
    $nextsec = $post->getint('nextsec');
    if($name == '')
        return errjson('请填写名称');
    if($runrequire == '')
        return errjson('请填写引用文件');
    if($runfunc == '')
        return errjson('请填写入口函数');
    if($nextsec <= 0)
        return errjson('执行周期填写错误');
    $updata = array();
    $updata['name'] = $name;
    $updata['runfunc'] = $runfunc;
    $updata['runrequire'] = $runrequire;
    $updata['runparam'] = $post->get('runparam');
    $updata['nexttime'] = strtotime($post->get('nexttime'));
    $updata['nextsec'] = $nextsec;
    $updata['status'] = $post->getint('status');
    $csql = new ciy_sql($table);
    $csql->where('id',$id);
    $oldrow = $mydata->getone($csql);
    $execute = $mydata->data($updata)->set($csql);
    if ($execute === false)
        return errjson('操作数据库失败.' . $mydata->error);
    savelogdb($table, $oldrow, $updata,"ID={$id}");
    return succjson();
}
function json_del() {
    if(nopower('admin'))
        return errjson('您无权操作');
    global $mydata;
    global $table;
    $post = new ciy_post();
    $csql = new ciy_sql($table);
    $csql->where('id',$post->getint('id'));
    $oldrow = $mydata->getone($csql);
    $execute = $mydata->delete($csql);
    if ($execute === false)
        return errjson('删除数据库失败.' . $mydata->error);
    savelogdb($table, $oldrow, null);
    return succjson();
}
