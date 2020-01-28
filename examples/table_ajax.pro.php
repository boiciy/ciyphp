<?php
$mydata = new ciy_data();
$table = 'd_test';
ciy_runJSON();

function json_addscores() {
    global $mydata;
    global $table;
    $post = new ciy_post();
    $ids = $post->get('ids');
    $execute = $mydata->execute('update '.$table.' set scores=scores+1 where id in ('.$ids.')');
    if ($execute === false)
        return errjson('操作失败:'.$mydata->error);
    return succjson();
}
function json_init() {
    global $mydata;
    global $table;
    $post = new ciy_post();
    $msql = new ciy_sql($table);
    $liid = $post->getint('liid');
    if($liid>0)
        $msql->where('kg',$liid);
    $msql->where('truename like',$post->get('truename'));
    $msql->wherenumber('scores',$post->get('scores'));
    $order = $post->get('order','id desc');
    $msql->order($order);
    $pageno = $post->getint('pageno', 1);
    $pagecount = $post->getint('pagecount', 20);
    $msql->limit($pageno,$pagecount);
    if($post->getbool('nototal'))
        $mainrowcount = false;
    $rows = $mydata->get($msql,$mainrowcount);
    $ret = array('order'=>$order,'pageno'=>$pageno,'pagecount'=>$pagecount,'count'=>$mainrowcount,'data'=>$rows);
    if($post->getint('field') == 0){
        $fieldrows = $mydata->getraw('show full fields from '.$table);
        foreach($fieldrows as $fr)
            $field[$fr['Field']] = array('c'=>$fr['Comment']);
        unset($field['dh']);
        unset($field['lbk']);
        unset($field['dxk']);
        unset($field['fxk']);
        $field['id']['c'] = '';
        $field['truename']['order'] = true;
        $field['scores']['order'] = true;
        $field['scores']['width'] = '8em';
        $field['truename']['prop'] = ' style="width:18em;"';
        $ret['field'] = $field;
    }
    return succjson($ret);
}