<?php require 'init.php'; require PATH_PROGRAM.NAME_SELF.'.pro.php'; ?><!DOCTYPE html><html>
<head>
<title></title>
<meta http-equiv="Content-type" content="text/html; charset=utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1">
<meta http-equiv="X-UA-Compatible" content="IE=Edge，chrome=1">
<link href="/jscss/style.css" rel="stylesheet" type="text/css" />
<body>
<div class="container">
    <div class="crumbs">当前位置： 系统管理 → 部门管理</div>
    <form methodd="get" action="">
        <div class="form-group inline">
            <label>部门名称</label>
            <div><input type="text" name="title" value="<?php echo get('title');?>" style="width:10em;"/></div>
        </div>
        <div class="form-group inline">
            <button class="btn" type="submit">查询</button>
            <a class="btn" onclick="edit(0,0)">添加根部门</a>
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
                <th style="width:200px;">部门名称</th>
                <th style="width:200px;">默认权限</th>
                <th style="width:200px;">负责人权限</th>
                <th>操作</th>
            </tr>
<?php
$rows = treerows_sort($rows);
$code_power = getcodes('user.power');
foreach($rows as $row){
    $id = (int)$row['id'];
?>
    <tr data-id="<?php echo $id;?>" data-upid="<?php echo $row['upid'];?>">
        <td><div<?php echo ($row['_count'] > 0)?' data-treeid="'.$id.'"':'';?> class="ciy-tree-spread"><?php
        echo str_repeat('　',$row['_deep']);
        if($row['_count']>0)
            echo '<span class="ciy-tree-dot">▶</span> '.$row['title'].'<span style="font-size:0.5em;">('.$row['_count'].')</span>';
        else
            echo '　 '.$row['title'];
        ?></div></td>
        <td><div><?php echo power_trans($code_power,$row['power']);?></div></td>
        <td><div><?php echo power_trans($code_power,$row['powerleader']);?></div></td>
        <td><div>
            <a class="btn" onclick="edit(0,<?php echo $id;?>)">添加子部门</a>
            <a class="btn" onclick="edit(<?php echo $id;?>,<?php echo $row['upid'];?>)">编辑</a>
            <a class="btn" onclick="ciy_fastfunc('确认是否删除？','del','id=<?php echo $id;?>','reload');">删除</a>
        </div></td>
        </tr>
<?php } ?>
          </table>
      </div>
<?php } ?>
</div>
<script src="/jscss/jquery-1.12.4.min.js" type="text/javascript"></script>
<script src="/jscss/ciy.js" type="text/javascript"></script>
<script type="text/javascript">
'use strict';
$(function(){
    ciy_table_adjust('.table');
    ciy_table_tree('.table');
});
function edit(id,upid)
{
    ciy_alert({title:'部门管理',contentstyle:'width:680px;height:40em;',frame:'rigger/admin_user_depart_update.php?id='+id+"&upid="+upid,nobutton:true});
}
</script>
</body>
</html>