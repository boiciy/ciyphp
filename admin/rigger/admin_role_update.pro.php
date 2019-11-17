<?php
$mydata = new ciy_data();
$rsuser = verifyadmin();
if(nopower('admin')) diehtml('您无权限');
$table = 'p_admin_role';
ciy_runJSON();
$id = (int)get('id');
$btnname = '更新';
$msql = new ciy_sql($table);
$msql->where('id',$id);
$updaterow = $mydata->getone($msql);
if(!is_array($updaterow))
    $btnname = '新增';
$code_rolegroup = getcodes('user_rolegroup');
$code_power = getcodes('user_power');

function json_update() {
    global $mydata;
    global $table;
    if(nopower('admin'))
        return errjson('您无权限');
    $post = new ciy_post();
    $id = $post->getint('id');
    $title = $post->get('title');
    if($title == '')
        return errjson('请填写角色名称');
    try{
        $mydata->begin();
        $updata = array();
        $updata['title'] = $title;
        $updata['groups'] = $post->getint('groups');
        $updata['power'] = '.'.str_replace(',', '.', $post->get('power')).'.';
        $updata['memo'] = $post->get('memo');
        $csql = new ciy_sql($table);
        $csql->where('id',$id);
        $oldrow = $mydata->getone($csql);
        $execute = $mydata->data($updata)->set($csql);
        if ($execute === false)
            throw new Exception('操作数据库失败:'.$mydata->error);
        if(is_array($oldrow))
        {
            if(@$oldrow['title'] != $title)
            {
                $execute = $mydata->execute('update p_admin_urole set rolename=? where roleid=?',array($title,$id));
                if ($execute === false)
                    throw new Exception('更新数据库失败:'.$mydata->error);
            }
            $csql = new ciy_sql('p_admin_urole');
            $csql->where('roleid',$id)->where('status',10);
            $urrows = $mydata->get($csql);
            foreach($urrows as $urrow)
                refreshpower($urrow['userid']);
        }
        savelogdb($table, $oldrow, $updata,"角色ID={$id}，");
        $mydata->commit();
    }catch(Exception $ex){
        $mydata->rollback();
        return errjson('事务失败:'.$ex->getMessage());
    }
    return succjson();
}