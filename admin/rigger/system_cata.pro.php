<?php
$mydata = new ciy_data();
$rsuser = verifyadmin();
$table = 'p_cata';
ciy_runJSON();
$msql = new ciy_sql($table);
$msql->where('codeid',get('codeid'));
$msql->where('types',get('types'));
$msql->where('title like',get('title'));
$msql->order('types,nums desc,id');
$pageno = getint('pageno', 1);$pagecount = 20;
$msql->limit($pageno,$pagecount);
$rows = $mydata->get($msql,$mainrowcount);
$rows[] = array('id'=>0,'types'=>get('types'),'nums'=>0,'upid'=>0);

function json_update() {
    if(nopower('admin'))
        return errjson('您无权操作');
    global $mydata;
    global $table;
    $post = new ciy_post();
    $act = $post->get('act');
    $id = $post->getint('id');
    $types = $post->get('types');
    $title = $post->get('title');
    $codeid = $post->get('codeid');
    if($types == '')
        return errjson('请填写类型');
    if($title == '')
        return errjson('请填写名称');
    $csql = new ciy_sql($table);
    $csql->where('types',$types)->where('codeid',$codeid)->column('id');
    $chkrow = $mydata->getone($csql);
    if(is_array($chkrow) && $chkrow['id'] != $id)
        return errjson('该类型上的代码值重复');
    $updata = array();
    $updata['nums'] = $post->getint('nums');
    $updata['types'] = $types;
    $updata['codeid'] = $codeid;
    $updata['title'] = $title;
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
