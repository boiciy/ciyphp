<?php
$mydata = new ciy_data();
$table = 'p_admin';
ciy_runJSON();

function json_login() {
    global $mydata;
    global $table;
    global $rsuser;
    $post = new ciy_post();
    $user = $post->get('user');
    if(empty($user))
        return errjson('请填写用户名');
    $csql = new ciy_sql($table);
    $csql->where('mobile',$user);
    $rsuser = $mydata->getone($csql);
    if ($rsuser === false)
        return errjson($mydata->error);
    if (!is_array($rsuser))
    {
        savelog('LOGIN', "用户[{$user}]不存在，在尝试登录");
        return errjson('用户名不存在');
    }
    if($rsuser['trytime'] > 10)
    {
        if(time()-$rsuser['activetime'] < 600)
        {
            savelog('LOGIN', "用户[{$user}]登录连续失败");
            return errjson('连续输入密码错误，10分钟后再来登录.');
        }
    }
    if ($rsuser['status'] != 10)
    {
        savelog('LOGIN', "用户[{$user}]被禁用，在尝试登录");
        return errjson('您的账户已经被禁用.');
    }
    $md5pass = md5($post->get('pass').ciy_config::$conmmonkey);
    if ($rsuser['password'] != $md5pass)
    {
        $execute = $mydata->execute("update {$table} set trytime=trytime+1,activetime=".(time()+86400)." where id={$rsuser['id']}");
        savelog('LOGIN', "用户[{$user}]登录密码错误{$md5pass}");
        return errjson('用户名或密码错误.');
    }
    $id = $rsuser['id'];
    $sid = uniqid();
    $exp = time()+259200;//默认三天过期，每天换秘钥
    $updata = array();
    $updata['userid'] = $id;
    $updata['target'] = 10;
    $updata['sid'] = $sid;
    $updata['exptime'] = $exp;
    $updata['ip'] = getip();
    $execute = $mydata->data($updata)->set(new ciy_sql($table.'_online'));
    if($execute === false)
        return errjson('online数据库更新失败:'.$mydata->error);
    $oid = (int)$execute;
    $updata = array();
    $updata['activetime'] = time();
    $updata['trytime'] = 0;
    $updata['ip'] = getip();
    $csql = new ciy_sql($table);
    $csql->where('id',$id);
    $execute = $mydata->data($updata)->set($csql, 'update');
    if($execute === false)
        return errjson('user数据库更新失败:'.$mydata->error);
    cookieadmin($oid,$id,$sid,$exp);
    savelog('LOGIN', '登录成功');
    return succjson();
}
