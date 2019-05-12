<?php
require 'init.php';
set_time_limit(0);
if(isset($_SERVER['HTTP_HOST']))
    ob_end_clean();
$mydata = new ciy_data();
echo '<!DOCTYPE html> <html><head>';
echo '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">';
echo '<title>SYSTEM Running</title>';
echo '</head><body>';
$runid = (int)get('runid');
$table = 'p_system';
$csql = new ciy_sql($table);
$csql->where('status',1)->where('exptime<=',time());
$exprows = $mydata->get($csql);
foreach($exprows as $exprow)
{
    savelog('AUTO',$exprow['name'].'['.$exprow['id'].']自动执行超时，请适当增加调用running');
    $mydata->execute('update '.$table.' set status=0 where id='.$exprow['id']);
}
if($runid > 0)
{
    $csql = new ciy_sql($table);
    $csql->where('id',$runid);
    $systemrow = $mydata->getone($csql);
    if($systemrow === null)
        pr('指定任务ID不存在');
    if(is_array($systemrow))
        run($systemrow);
    else
        pr('为找到指定任务');
}
else
{
    while(true)
    {
        $csql = new ciy_sql($table);
        $csql->where('status',0)->where('nexttime<=',time());
        $systemrow = $mydata->getone($csql);
        if(is_array($systemrow))
            run($systemrow);
        else
            break;
    }
}
pr('全部执行结束');
echo '</body></html>';
//exit
function run($systemrow)
{
    global $mydata;
    global $table;
    $expsec = 180;//超时时间3分钟
    //自动累加，累加到大于当前时间
    $sysid = $systemrow['id'];
    $nexttime = $systemrow['nexttime'];
    $nextsec = (int)$systemrow['nextsec'];
    if($nextsec <= 0)
        $nextsec = 12;//错误设置一年影响一次
    while(true)
    {
        if($nexttime > time())
            break;
        if($nextsec < 60)
            $nexttime = strtotime('+'.$nextsec.' months', $nexttime);
        else
            $nexttime += $nextsec;
        if($nexttime > time())
            break;
    }
    $mydata->execute('update '.$table.' set status=1,runcnt=runcnt+1,nexttime='.$nexttime.',exptime='.(time()+$expsec).' where id='.$sysid);
    $errmsg = '';
    try{
        pr($systemrow['name'].' 开始执行...');
        if(!empty($systemrow['runrequire']))
        {
            if(!is_file(PATH_PROGRAM.$systemrow['runrequire']))
                $errmsg = '缺少引用文件'.$systemrow['runrequire'];
            else
                require_once PATH_PROGRAM.$systemrow['runrequire'];
        }
        if (!function_exists($systemrow['runfunc']))
            $errmsg .= '缺少函数'.$systemrow['runfunc'];
        else
            $errmsg = call_user_func($systemrow['runfunc'],$systemrow);

        pr($systemrow['name'].' 执行完成。'.$errmsg);
    } catch (Exception $ex) {
        $errmsg = 'try:'.print_r($ex,true);
    }
    $mydata->execute('update '.$table.' set status=0,errmsg=? where id='.$sysid,array($errmsg));
    if(!empty($errmsg))
        savelog('AUTO',$systemrow['name'].'['.$sysid.']自动执行失败：'.$errmsg);
}
function running($systemrow,$errmsg = '')
{
    global $mydata;
    global $table;
    $mydata->execute('update '.$table.' set status=1,exptime=unix_timestamp()+180,errmsg=? where id='.$systemrow['id'],array($errmsg));
}
?>