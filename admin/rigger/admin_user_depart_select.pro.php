<?php
$mydata = new ciy_data();
$rsuser = verifyadmin();
$table = 'p_admindepart';
ciy_runJSON();
$selid = getint('id');
$csql = new ciy_sql($table);
$csql->where('title like',get('title'))->order('id');
$rows = $mydata->get($csql);

function json_getdepart() {
    global $mydata;
    global $table;
    $post = new ciy_post();
    $id = $post->getint('id');
    $csql = new ciy_sql($table);
    $csql->where('id',$id);
    $row = $mydata->getone($csql);
    if(!is_array($row))
        return errjson('部门不存在.');
    $depart = getdepart($id);
    return succjson(array('depart'=>$depart,'power'=>$row['power'],'powerleader'=>$row['power'].$row['powerleader']));
}
function getdepart($id)
{
    global $mydata;
    global $table;
    $ns = array();
    while(true)
    {
        if($id == 0)
            break;
        $csql = new ciy_sql($table);
        $csql->where('id',$id);
        $row = $mydata->getone($csql);
        if(!is_array($row))
            break;
        $id = $row['upid'];
        $ns[] = $row['title'];
    }
    return implode('·', $ns);
}