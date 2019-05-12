<?php
$mydata = new ciy_data();
$rsuser = verifyadmin();
$table = 'p_admin';
ciy_runJSON();
$id = getint('id');
$msql = new ciy_sql($table);
$msql->where('id',$id);
$updaterow = $mydata->getone($msql);
$btnname = '更新';
if(!is_array($updaterow))
    $btnname = '新增';

function json_update() {
    global $mydata;
    global $table;
    $post = new ciy_post();
    $updata = array();
    $id = $post->getint('id');
    $departid = $post->getint('departid');
    $truename = $post->get('truename');
    $mobile = $post->get('mobile');
    if($mobile == '')
        return errjson('请填写登录手机号');
    if($truename == '')
        return errjson('请填写姓名');
    if($departid == 0)
        return errjson('请选择部门');
    $csql = new ciy_sql($table);
    $csql->where('mobile',$mobile)->column('id');
    $chkrow = $mydata->getone($csql);
    if(is_array($chkrow) && $chkrow['id'] != $id)
        return errjson('手机号重复');
    $updata['icon'] = $post->get('icon');
    $updata['truename'] = $truename;
    $updata['departid'] = $departid;
    $updata['depart'] = getdepart($departid);
    $updata['power'] = '.'.str_replace(',', '.', $post->get('power')).'.';
    $updata['mobile'] = $mobile;
    $pass = $post->get('password');
    if(!empty($pass))
        $updata['password'] = md5($pass.ciy_config::$conmmonkey);
    $updata['status'] = $post->getbool('status')?1:10;
    $updata['sex'] = $post->getbool('sex')?1:2;
    $updata['leader'] = $post->getbool('leader')?1:2;
    $updata['activetime'] = time();
    $updata['ip'] = getip();
    $csql = new ciy_sql($table);
    $csql->where('id',$id);
    $oldrow = $mydata->getone($csql);
    $execute = $mydata->data($updata)->datainsert(array('addtimes'=>time()))->set($csql);
    if ($execute === false)
        return errjson('操作数据库失败.' . $mydata->error);
    savelogdb($table, $oldrow, $updata,"管理员ID={$id}，");
    return succjson();
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
        $csql = new ciy_sql($table.'depart');
        $csql->where('id',$id);
        $row = $mydata->getone($csql);
        if(!is_array($row))
            break;
        $id = $row['upid'];
        $ns[] = $row['title'];
    }
    return implode('·', $ns);
}