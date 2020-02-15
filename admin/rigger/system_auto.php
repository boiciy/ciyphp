<?php require 'init.php'; require PATH_PROGRAM.NAME_SELF.'.pro.php'; ?><!DOCTYPE html><html>
<head>
<title>Demo</title>
<meta http-equiv="Content-type" content="text/html; charset=utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1">
<meta http-equiv="X-UA-Compatible" content="IE=Edge，chrome=1">
<link href="/jscss/style.css" rel="stylesheet" type="text/css" />
<body>
<div class="container">
    <div class="crumbs">当前位置： 系统管理 → 自动化任务</div>
    <form method="get" action="">
        <div class="form-group inline">
            <label>ID</label>
            <div><input type="text" name="id" value="<?php echo get('id');?>" style="width:6em;"/></div>
        </div>
        <div class="form-group inline">
            <label>任务名称</label>
            <div><input type="text" name="name" value="<?php echo get('name');?>" style="width:9em;"/></div>
        </div>
        <div class="form-group inline">
            <button class="btn" type="submit">查询</button>
        </div>
    </form>
<?php
if($rows === false)
    echo '<div class="table-nodata">查询失败:'.$mydata->error.'</div>';
else if(count($rows) == 0)
    echo '<div class="table-nodata">无数据</div>';
else{
?>
      <div class='table'>
          <table>
            <tr>
                <th>ID</th>
                <th>任务名称</th>
                <th>入口函数</th>
                <th>引用文件</th>
                <th>执行参数</th>
                <th>下一次执行时间</th>
                <th>执行周期(s/m)</th>
                <th>状态</th>
                <th>次数</th>
                <th>执行信息</th>
                <th>操作</th>
            </tr>
<?php
foreach($rows as $row){
    $id = (int)$row['id'];
    $status = (int)$row['status'];
?>
    <tr>
        <td><div style="text-align:center;"><?php echo $id;?><input type="hidden" name="id" value="<?php echo $id;?>"/></div></td>
        <td><div><input style="width:100%;" type="text" name="name" value="<?php echo @$row['name'];?>"/></div></td>
        <td><div><input style="width:100%;" type="text" name="runfunc" value="<?php echo @$row['runfunc'];?>"/></div></td>
        <td><div><input style="width:100%;" type="text" name="runrequire" value="<?php echo @$row['runrequire'];?>"/></div></td>
        <td><div><input style="width:100%;" type="text" name="runparam" value="<?php echo @$row['runparam'];?>"/></div></td>
        <td><div><input style="width:100%;" type="text" name="nexttime" value="<?php echo todate(@$row['nexttime']);?>"/></div></td>
        <td><div><input style="width:100%;text-align:right;" type="text" name="nextsec" value="<?php echo @$row['nextsec'];?>"/></div></td>
        <td><div>
            <select name="status" style="width:6em;">
                <option value="0"<?php echo ($status==0)?' selected':'';?>>待命</option>
                <option value="1"<?php echo ($status==1)?' selected':'';?>>执行中</option>
                <option value="10"<?php echo ($status==10)?' selected':'';?>>禁用</option>
            </select>
        </div></td>
        <td><div style="text-align:right;"><?php echo @$row['runcnt'];?></div></td>
        <td><div><?php echo @$row['errmsg'];?></div></td>
        <td><div>
            <?php
            if($id == 0)
                echo '<a class="btn" onclick="update(this,\'reload\')">新增</a>';
            else
                echo '<a class="btn" onclick="update(this,\'reload\')">更新</a> <a class="btn" onclick="del('.$id.')">删除</a> <a class="btn" href="/system/main.php?runid='.$id.'&must=true" target="_blank">手动执行</a>';
            ?>
            
        </div></td>
        </tr>
<?php } ?>
          </table>
      </div>
<?php }?>
<fieldset class="tips">
  <legend>配置说明</legend>
  <div>
    <ul>
        <li>Linux自动执行请配置crond。<br/>
            在<kbd>/var/spool/cron/root</kbd>文件中增加一行：（切记不要使用curl）<br/>
            * * * * * wget -O mm.php -q http://127.0.0.1/system/main.php<br/>
            <code>或</code><br/>
            * * * * * /usr/bin/php /data/website/system/main.php<br/>
        </li>
        <li>执行周期支持“秒”和“月”两种，设置小于60的数字，按月计算</li>
        <li>引用文件请放置在<kbd>/system/</kbd>目录下。</li>
        <li>入口函数，请尽量设置不同的函数名，防止同时引用冲突。</li>
        <li>每天/每周/每月等大周期执行的任务，请尽量岔开时间。</li>
        <li>可以通过手动执行调试任务代码，建议用<code>pr()</code>打印变量。</li>
        <li>nginx+fpm模式，请注意设置fpm的request_terminate_timeout参数，防止运行超时。</li>
    </ul>
  </div>
</fieldset>
</div>
<script src="/jscss/jquery-1.12.4.min.js" type="text/javascript"></script>
<script src="/jscss/ciy.js" type="text/javascript"></script>
<script type="text/javascript">
'use strict';
$(function(){
    ciy_table_adjust('.table');
});
function update(dom,act)
{
    ciy_fastfunc('确认更新？','update',ciy_getform(dom,'TR'),act);
}
function del(id)
{
    ciy_fastfunc('确认是否删除？','del','id='+id,'reload');
}
</script>
</body>
</html>