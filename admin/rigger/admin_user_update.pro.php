<?php
$mydata = new ciy_data();
$rsuser = verifyadmin();
if(nopower('admin')) diehtml('您无权限');
$table = 'p_admin';
ciy_runJSON();
$id = getint('id');
$msql = new ciy_sql($table);
$msql->where('id',$id);
$updaterow = $mydata->getone($msql);
$btnname = '更新';
if(!is_array($updaterow))
    $btnname = '新增';

$csql = new ciy_sql('p_admin_role');
$roles = $mydata->get($csql);
$csql = new ciy_sql('p_admin_urole');
$csql->where('userid',$id)->where('status',10);
$urole = $mydata->get($csql);

function json_update() {
    global $mydata;
    global $rsuser;
    global $table;
    if(nopower('admin'))
        return errjson('您无权限');
    $post = new ciy_post();
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
    $pass = $post->get('password');
    if(!empty($pass)){
        if(strlen($pass) < 6)
            return errjson('密码应至少6位');
        if(!preg_match('/[@#$%^&*()_+!]/',$pass))
            return errjson('密码应包含特殊符号@#$%^&*()_+!等');
        if(!preg_match('/[a-z]/',$pass))
            return errjson('密码应包含小写字母');
        if(!preg_match('/[A-Z]/',$pass))
            return errjson('密码应包含大写字母');
        if(!preg_match('/[0-9]/',$pass))
            return errjson('密码应包含数字');
        $pass = md5($pass.ciy_config::$conmmonkey);
    }
    try{
        $mydata->begin();
        $updata = array();
        $updata['icon'] = $post->get('icon');
        $updata['truename'] = $truename;
        $updata['departid'] = $departid;
        $updata['depart'] = getdepart($departid);
        $updata['mobile'] = $mobile;
        if(!empty($pass))
            $updata['password'] = $pass;
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
            throw new Exception('操作数据库失败:'.$mydata->error);
        $newid = (int)$execute;
        if(@$oldrow['power'] != '.*.'){
            $roles = explode(',',$post->get('role'));
            $csql = new ciy_sql('p_admin_urole');
            $csql->where('userid',$newid);
            $urolerows = $mydata->get($csql);
            foreach($urolerows as $row){
                $ind = array_search($row['roleid'],$roles);
                if($ind === false){
                    if($row['status'] == 10){
                        $execute = $mydata->execute('update p_admin_urole set status=9 where id='.$row['id']);
                        if ($execute === false)
                            throw new Exception('操作urole数据库9失败:'.$mydata->error);
                    }
                }
                else
                {
                    array_splice($roles,$ind,1);
                    if($row['status'] < 10){
                        $execute = $mydata->execute('update p_admin_urole set status=10,adminid='.$rsuser['id'].',addtimes='.time().' where id='.$row['id']);
                        if ($execute === false)
                            throw new Exception('操作urole数据库10失败:'.$mydata->error);
                    }
                }
            }
            foreach($roles as $role){
                $csql = new ciy_sql('p_admin_role');
                $csql->where('id',$role);
                $rolerow = $mydata->getone($csql);
                if(!is_array($rolerow))
                    throw new Exception('角色不存在:'.$role);
                $updatarole = array();
                $updatarole['userid'] = $newid;
                $updatarole['username'] = $truename;
                $updatarole['roleid'] = $role;
                $updatarole['rolename'] = $rolerow['title'];
                $updatarole['status'] = 10;
                $updatarole['adminid'] = $rsuser['id'];
                $updatarole['addtimes'] = time();
                $csql = new ciy_sql('p_admin_urole');
                $execute = $mydata->data($updatarole)->set($csql,'insert');
                if ($execute === false)
                    throw new Exception('新增urole数据库失败:'.$mydata->error);
            }
            refreshpower($newid);
        }
        $execute = $mydata->execute('update p_admin_urole set username=? where userid=?',array($truename,$newid));
        if ($execute === false)
            throw new Exception('更新urole数据库失败:'.$mydata->error);
        savelogdb($table, $oldrow, $updata,"管理员ID={$id}，");
        $mydata->commit();
    }catch(Exception $ex){
        $mydata->rollback();
        return errjson('事务失败:'.$ex->getMessage());
    }
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
        $csql = new ciy_sql($table.'_depart');
        $csql->where('id',$id);
        $row = $mydata->getone($csql);
        if(!is_array($row))
            break;
        $id = $row['upid'];
        $ns[] = $row['title'];
    }
    return implode('·', $ns);
}
