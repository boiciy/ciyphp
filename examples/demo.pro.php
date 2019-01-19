<?php
$mydata = new ciy_data();
$table = 'd_test';
ciy_runJSON();
ciy_runCSV();
$pageno = (int)get('page', 1);
$pagecount = 20;
$where = '';//不用担心" and "前缀，组合sql时会自动过滤
$val = get('truename');
if(!empty($val))
    $where .= ' and truename like \'%'.$val.'%\'';
$order = get('order','id desc');
$rows = $mydata->get($pageno,$pagecount, $table, $where,$order);
$mainrowcount = (int)$mydata->getonescalar($table, $where);
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
    $execute = $mydata->delete($table, 'id=' . $id, 'backup');
    if ($execute === false)
        return errjson('操作失败:'.$mydata->error);
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