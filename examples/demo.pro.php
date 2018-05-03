<?php
$mydata = new ciy_data();
ciy_runJSON();
ciy_runCSV();
$pageno = (int)get('page', 1);
$pagecount = 20;
$table = 'd_test';
$where = '';
$val = get('truename');
if(!empty($val))
    $where .= ' and truename like \'%'.$val.'%\'';
(strpos($where,' and ') === 0) && $where = substr($where,5);
$rows = $mydata->get($pageno,$pagecount, $table, $where,'id desc');
$mainrowcount = (int)$mydata->getonescalar($table, $where);
function csv_cc()//Excel CSV数据导出函数，ciy_runCSV()调用。
{
    global $mydata;
    $ret = array();
    $ret[] = 'Test_' . date('Y-m-d_h-i-s') . '.csv';
    $fs = array('编号','头像','姓名','分数','日期','IP');
    $ret = array_merge($ret,array($fs));
    $rows = $mydata->get(0,0, 'd_test', '','id desc','id,icon,truename,scores,addtimes,ip');
    $count = count($rows);
    for($i=0;$i<$count;$i++)
    {
        $rows[$i]['id'] = enid($rows[$i]['id']);
        $rows[$i]['ip'] = long2ip($rows[$i]['ip']);
    }
    $ret = array_merge($ret,$rows);
    return $ret;
}

function json_setact() {//Ajax交互函数，ciy_runJSON()调用。
    global $mydata;
    $act = post('act');
    if($act === 'del')
    {
        $execute = $mydata->delete('d_test', 'id in (' . post('ids') . ')', 'backup');
        if ($execute === null)
            return errjson('操作失败:'.$mydata->error);
    }
    $ret = array();
    $ret['result'] = true;
    return $ret;
}