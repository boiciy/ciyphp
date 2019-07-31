<?php
$mydata = new ciy_data();
$rsuser = verifyadmin();
if(nopower('admin')) diehtml('您无权限');
$table = 'p_cata';
ciy_runJSON();
$msql = new ciy_sql($table);
$msql->where('codeid',get('codeid'));
$msql->where('types like',get('types'));
$msql->where('title like',get('title'));
$msql->order('types,nums,id');
$pageno = getint('pageno', 1);$pagecount = 20;
$msql->limit($pageno,$pagecount);
$rows = $mydata->get($msql,$mainrowcount);
$rows[] = array('id'=>0,'types'=>get('types'),'nums'=>0,'upid'=>0);

function json_update() {
    global $mydata;
    global $table;
    if(nopower('admin'))
        return errjson('您无权操作');
    $post = new ciy_post();
    $act = $post->get('act');
    $id = $post->getint('id');
    $types = $post->get('types');
    $title = $post->get('title');
    $codeid = $post->get('codeid');
    if($types == '')
        return errjson('请填写分类');
    if($title == '')
        return errjson('请填写名称');
    $csql = new ciy_sql($table);
    $csql->where('types',$types)->where('codeid',$codeid)->column('id');
    $chkrow = $mydata->getone($csql);
    if(is_array($chkrow) && $chkrow['id'] != $id)
        return errjson('该类型上的代码值重复');
    $updata = array();
    $updata['nums'] = $post->getint('nums');
    $updata['types'] = $types;
    $updata['codeid'] = $codeid;
    $updata['title'] = $title;
    $updata['extdata'] = $post->get('extdata');
    $csql = new ciy_sql($table);
    $csql->where('id',$id);
    $oldrow = $mydata->getone($csql);
    $execute = $mydata->data($updata)->set($csql);
    if ($execute === false)
        return errjson('操作数据库失败.' . $mydata->error);
    savelogdb($table, $oldrow, $updata,"ID={$id}");
    return succjson();
}
function json_multiadd() {
    global $mydata;
    global $table;
    if(nopower('admin'))
        return errjson('您无权操作');
    $post = new ciy_post();
    $types = $post->get('multi_types');
    $code = $post->get('multi_code');
    if($types == '')
        return errjson('请填写分类');
    if($code == '')
        return errjson('请填写代码');
    $codes = explode("\n",$code);
    if(count($codes) == 1 && strpos($code,',') !== false && strpos($code,'.') !== false)
    {
        $codes = explode(',',$code);
        foreach($codes as $c)
        {
            //订单状态,1.创建订单,10.支付成功,20.评价
            $cs = explode('.',$c);
            if(count($cs) < 2)
                continue;
            $codeid = $cs[0];
            $title = $cs[1];
            $csql = new ciy_sql($table);
            $csql->where('types',$types)->where('codeid',$codeid)->column('id');
            $chkrow = $mydata->getone($csql);
            $updata = array();
            if(is_array($chkrow))
            {
                $updata['title'] = $title;
                $csql = new ciy_sql($table);
                $csql->where('id',$chkrow['id']);
                $execute = $mydata->data($updata)->set($csql,'update');
            }
            else
            {
                $updata['types'] = $types;
                $updata['codeid'] = $codeid;
                $updata['title'] = $title;
                $csql = new ciy_sql($table);
                $execute = $mydata->data($updata)->set($csql,'insert');
            }
            if ($execute === false)
                return errjson('批量操作数据库失败.' . $mydata->error);
            savelogdb($table, $chkrow, $updata);
        }
    }
    else
    {
        foreach($codes as $c)
        {
            $cs = explode(',',$c);
            if(count($cs) < 2)
                continue;
            $title = $cs[0];
            $codeid = $cs[1];
            $extdata = @$cs[2].'';
            $csql = new ciy_sql($table);
            $csql->where('types',$types)->where('codeid',$codeid)->column('id');
            $chkrow = $mydata->getone($csql);
            if(is_array($chkrow))
                continue;
            $updata = array();
            $updata['types'] = $types;
            $updata['codeid'] = $codeid;
            $updata['title'] = $title;
            $updata['extdata'] = $extdata;
            $csql = new ciy_sql($table);
            $execute = $mydata->data($updata)->set($csql,'insert');
            if ($execute === false)
                return errjson('批量操作数据库失败.' . $mydata->error);
            savelogdb($table, null, $updata,"ID=0");
        }
    }
    return succjson();
}
function json_setact() {
    global $mydata;
    global $rsuser;
    global $table;
    if(nopower('admin'))
        return errjson('您无权操作');
    $post = new ciy_post();
    $act = $post->get('act');
    $ids = $post->get('ids');
    if($act === 'del')
    {
        $post = new ciy_post();
        $csql = new ciy_sql($table);
        $csql->where('id in',$ids);
        $execute = $mydata->delete($csql);
        if ($execute === false)
            return errjson('删除数据库失败.' . $mydata->error);
        savelog($table,'批量删除('.$ids.')');
        return succjson();
    }
    return succjson();
}