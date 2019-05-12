<?php
require 'init.php';
require PATH_PROGRAM . NAME_SELF . '.pro.php';
?><!DOCTYPE html><html>
<head>
<title>Demo</title>
<meta http-equiv="Content-type" content="text/html; charset=utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1">
<meta http-equiv="X-UA-Compatible" content="IE=Edge，chrome=1">
<link href="/jscss/style.css" rel="stylesheet" type="text/css" />
<body>
<div class="container">
    <div>当前位置： 增删改查</div>
    <form method="get">
        <div class="form-group inline">
            <label>姓名</label>
            <div><input type="text" name="truename" value="<?php echo get('truename');?>"/></div>
        </div>
        <div class="form-group inline">
            <label>分数</label>
            <div><input type="text" name="scores" value="<?php echo get('scores');?>" style="width:5em;"/><code>支持>、<、n,n、n-n</code></div>
        </div>
        <div class="form-group inline">
            <label>日期</label>
            <div><input type="text" name="activetime" value="<?php echo get('activetime');?>" style="width:13em;"/></div>
        </div>
        <div class="form-group inline">
            <div>
            <button class="btn" type="submit">查询</button>
            <a class="btn" onclick="edit(0)">新增(弹窗)</a>
            <a class="btn" onclick="ciy_ifropen('demo_update.php','新增数据',false,function(){location.reload();})">新增(新标签)</a>
            <label class="btn">
                导出
                <input type="checkbox">
                <ul class="ciy-popmenu" style="top:2em;">
                    <li><a href="<?php echo urlparam('', array('excel' => 'csv','func' => 'cc','prefix' => '导出'));?>" target="_blank">导出到CSV</a></li>
                    <li><a href="<?php echo urlparam('', array('excel' => 'xml','func' => 'cc','prefix' => '导出'));?>" target="_blank">导出到Excel</a></li>
                    <li><a href="<?php echo urlparam('', array('excel' => 'xml','func' => 'ccsimple','prefix' => '简化'));?>" target="_blank">导出到Excel(简化)</a></li>
                </ul>
                <span class="caret"></span>
            </label>
            </div>
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
                <th>头像</th>
                <th>姓名<?php echo showorder('truename');?></th>
                <th>分数<?php echo showorder('scores');?></th>
                <th>日期<?php echo showorder('activetime');?></th>
                <th>单选框</th>
                <th>复选框</th>
                <th>下拉列表</th>
                <th>开关</th>
                <th>多行</th>
                <th>IP</th>
                <th>操作</th>
            </tr>
<?php
foreach($rows as $row)
{
    $id = (int)$row['id'];
?>
    <tr data-id="<?php echo $id;?>">
        <td><div><?php echo enid($id);?></div></td>
        <td><div><img src="<?php echo $row['icon'];?>" style="width:100%;"/></div></td>
        <td><div><?php echo $row['truename'];?></div></td>
        <td><div style="text-align:center;"><?php echo $row['scores'];?></div></td>
        <td><div><?php echo todate($row['activetime']);?></div></td>
        <td><div><?php echo $row['dxk'];?></div></td>
        <td><div><?php echo $row['fxk'];?></div></td>
        <td><div><?php echo $row['lbk'];?></div></td>
        <td><div><?php echo ($row['kg'] == 1)?'ON':'OFF';?></div></td>
        <td><div><?php echo $row['dh'];?></div></td>
        <td><div><?php echo long2ip($row['ip']);?></div></td>
        <td><div>
                <a class="btn" onclick="edit(<?php echo $id;?>)">编辑</a>
                <a class="btn" onclick="del(<?php echo $id;?>)">删除1</a>
                <a class="btn" onclick="ciy_fastfunc('确认是否删除？','del','id=<?php echo $id;?>','reload');">删除2</a>
                
        </div></td>
        </tr>
<?php } ?>
          </table>
          <style type="text/css"></style>
      </div>
    <div class="ciy_tabbtn">
        <a class="btn btn-default" onclick="ciy_select_all('.table')">全选</a>
        <a class="btn btn-default" onclick="ciy_select_diff('.table')">反选</a>
        |
        <a class="btn btn-default" onclick="ciy_select_act('.table','read')">批量分数+1</a>
    </div>
      <?php echo showpage($pageno,$pagecount,$mainrowcount);?>
<?php } ?>
</div>
<script src="/jscss/jquery-1.12.4.min.js" type="text/javascript"></script>
<script src="/jscss/ciy.js" type="text/javascript"></script>
<script src="/jscss/laydate.js" type="text/javascript"></script>
<script type="text/javascript">
'use strict';
$(function(){
    ciy_table_adjust('.table');
    ciy_select_init('.table');
    laydate.render({
      elem: document.getElementsByName('activetime')[0]
      ,type: 'date'
      ,range: '~'
    });
});
function edit(id)
{
    ciy_alert({contentstyle:'width:50em;height:40em;',frame:'demo_update.php?id='+id,nobutton:true,title:"数据更新"});
}
function del(id)
{
    ciy_alert('确定删除？',function(btn){
            if(btn != '删除')
                return;
            callfunc('del','id='+id,function(){
                ciy_toast('已删除',{done:function(){
                    location.reload();
                }});
            });
        },{btns:['删除','<a class="btn btn-default">取消</a>']});
}
</script>
</body>
</html>