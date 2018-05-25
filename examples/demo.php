<?php
require 'init.php';
require PATH_PROGRAM . '/' . NAME_SELF . '.pro.php';
?><!DOCTYPE html>
<html>
<head>
<title>Demo</title>
<meta http-equiv="Content-type" content="text/html; charset=utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1">
<meta http-equiv="X-UA-Compatible" content="IE=Edge，chrome=1">
<link href="jscss/style.css" rel="stylesheet" type="text/css" />
<body>
<div class="container">
    <div>当前位置： 增删改查</div>
    <form method="get" action="">
        <div class="form-group form-group-inline form-group-short">
            <label>姓名</label>
            <div><input type="text" name="truename" value="<?php echo get('truename');?>"/></div>
        </div>
        <div class="form-group form-group-inline form-group-btn">
            <button class="btn" type="submit">查询</button>
            <a class="btn" onclick="ciy_ifropen('demo_update.php','新增数据')">新增</a>
            <a href="<?php echo urlparam('', array('csv' => 'true','func' => 'cc','prefix' => '导出'));?>" class="btn" target="_blank">导出到Excel</a>
        </div>
    </form>
    
      <div class='table'>
          <table>
<?php
if(count($rows) == 0)
    echo '<tr><td style="border-top: 1px solid #cccccc;text-align:center;"><div>无数据</div></td></tr>';
else{
?>
            <tr>
                <th>ID</th>
                <th>头像</th>
                <th>姓名</th>
                <th>分数</th>
                <th>日期</th>
                <th>IP</th>
                <th>操作</th>
            </tr>
<?php
foreach($rows as $row)
{
    $id = (int)$row['id'];
?>
    <tr>
        <td><div><?php echo enid($id);?></div></td>
        <td><div><img src="<?php echo $row['icon'];?>" style="max-height:3em;"/></div></td>
        <td><div><?php echo $row['truename'];?></div></td>
        <td><div><?php echo $row['scores'];?></div></td>
        <td><div><?php echo $row['addtimes'];?></div></td>
        <td><div><?php echo long2ip($row['ip']);?></div></td>
        <td><div>
                <a class="btn" onclick="edit(<?php echo $id;?>)">编辑</a>
                <a class="btn" onclick="del(<?php echo $id;?>)">删除</a>
        </div></td>
        </tr>
<?php }} ?>
          </table>
          <style type="text/css"></style>
      </div>
      <?php echo '第'.$pageno.'页/共'.$pagecount.'页 '.$mainrowcount.'条'; ?>
</div>
<script src="jscss/jquery-1.12.4.min.js" type="text/javascript"></script>
<script src="jscss/ciy.js" type="text/javascript"></script>
<script type="text/javascript">
'use strict';
$(function(){
    ciy_retable('.table');
});
function edit(id)
{
    ciy_alert('',''
        ,function(btn){}
        ,{contentstyle:'width:50em;height:40em;',frame:'demo_update.php?id='+id,nobutton:true}
    );
}
function del(id)
{
    ciy_alert('确定删除？'
        ,['删除','<a class="btn btn-default">取消</a>']
        ,function(btn){
            if(btn != '删除')
                return;
            callfunc('del','id='+id,function(){
                ciy_toast('已删除',{done:function(){
                    location.reload();
                }});
            });
        }
    );
}
</script>
</body>
</html>