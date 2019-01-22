<?php
$mydata = new ciy_data();
$table = 'd_test';
ciy_runJSON();
ciy_runCSV();
$csql = new ciy_sql($table);
$csql->where('truename',get('truename'),'like');
$csql->order(get('order','id desc'));
$pageno = getint('pageno', 1);
$rows = $mydata->get($csql,$pageno,$pagecount,$mainrowcount);
function csv_cc()//Excel CSV数据导出函数，ciy_runCSV()调用。
{
    global $mydata;
    global $table;
    $ret = array();
    $ret[] = 'Test_' . date('Y-m-d_h-i-s') . '.csv';
    $fs = array('编号','头像','姓名','分数','日期','IP');
    $ret = array_merge($ret,array($fs));
    $rows = $mydata->get(0,0, $table, '','id desc','id,icon,truename,scores,addtimes,ip');
    $count = count($rows);
    for($i=0;$i<$count;$i++)
    {
        $rows[$i]['id'] = enid($rows[$i]['id']);
        $rows[$i]['ip'] = long2ip($rows[$i]['ip']);
    }
    $ret = array_merge($ret,$rows);
    return $ret;
}

function json_del() {//Ajax交互函数，ciy_runJSON()调用。
    global $mydata;
    global $table;
    $post = new ciy_post();
    $id = $post->getint('id');
    $csql = new ciy_sql($table);
    $csql->where('id',$id);
    $oldrow = $mydata->getone($csql);
    $ret = $mydata->tran(function()use($mydata,$csql){
        return $mydata->delete($csql,true);
    });
    if($ret === false)
        return errjson('删除失败:'.$mydata->error);
    return succjson();
}
function json_setact() {
    global $mydata;
    global $table;
    $post = new ciy_post();
    $act = $post->get('act');
    $ids = $post->get('ids');
    if($act === 'read')
    {
        $execute = $mydata->execute('update '.$table.' set scores=scores+1 where id in ('.$ids.')');
        if ($execute === false)
            return errjson('操作失败:'.$mydata->error);
    }
    return succjson();
}