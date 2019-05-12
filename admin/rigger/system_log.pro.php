<?php
$mydata = new ciy_data();
$rsuser = verifyadmin();
$table = 'p_log';
ciy_runJSON();
$msql = new ciy_sql($table);
$liid = getint('liid');
$msql->where('status',$liid);
$msql->where('userid',get('userid'));
$msql->where('types',get('types'));
$msql->where('logs like',get('logs'));
$msql->order(get('order','id desc'));
$pageno = getint('pageno', 1);$pagecount = 20;
$msql->limit($pageno,$pagecount);
$rows = $mydata->get($msql,$mainrowcount);
function json_setact() {
    global $mydata;
    global $rsuser;
    global $table;
    $post = new ciy_post();
    $act = $post->get('act');
    $ids = $post->get('ids');
    if($act === 'read')
    {
        $csql = new ciy_sql($table);
        $csql->where('status',0);
        $csql->where('id in',$ids);
        $updata = array();
        $updata['status'] = 1;
        $updata['readid'] = $rsuser['id'];
        $execute = $mydata->data($updata)->set($csql);
        if ($execute === false)
            return errjson('操作失败:'.$mydata->error);
    }
    if($act === 'lock')
    {
        $csql = new ciy_sql($table);
        $csql->where('id in',$ids);
        $updata = array();
        $updata['status'] = 2;
        $updata['readid'] = $rsuser['id'];
        $execute = $mydata->data($updata)->set($csql);
        if ($execute === false)
            return errjson('操作失败:'.$mydata->error);
    }
    if($act === 'unlock')
    {
        if(nopower('admin'))
            return errjson('您无权操作');
        $csql = new ciy_sql($table);
        $csql->where('id in',$ids);
        $updata = array();
        $updata['status'] = 1;
        $updata['readid'] = $rsuser['id'];
        $execute = $mydata->data($updata)->set($csql);
        if ($execute === false)
            return errjson('操作失败:'.$mydata->error);
        savelog('LOG', '解锁ID：'.$ids);
    }
    return succjson();
}
function json_clear() {
    if(nopower('admin'))
        return errjson('您无权操作');
    global $mydata;
    global $table;
    $csql = new ciy_sql($table);
    $csql->where('status',1);
    $csql->where('addtimes<',time()-86400*100);
    $execute = $mydata->delete($csql);
    if ($execute === false)
        return errjson('操作失败:'.$mydata->error);
    savelog('LOG', '清理了'.$execute.'条记录');
    return succjson();
}