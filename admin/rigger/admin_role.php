<?php require 'init.php'; require PATH_PROGRAM.NAME_SELF.'.pro.php'; ?><!DOCTYPE html><html>
<head>
<title></title>
<meta http-equiv="Content-type" content="text/html; charset=utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1">
<meta http-equiv="X-UA-Compatible" content="IE=Edge，chrome=1">
<link href="/jscss/style.css" rel="stylesheet" type="text/css" />
<body>
<div class="container">
    <div class="crumbs">当前位置： 系统管理 → 角色管理</div>
    <div class="ciy-tab ciy-tab-card">
        <ul><?php echo create_li($code_rolegroup,$liid);?></ul>
        <div class="ciy-tab-box">
            <form method="get" action="">
                <div class="form-group inline">
                    <label>角色名称</label>
                    <div><input type="text" name="title" value="<?php echo get('title');?>" style="width:10em;"/></div>
                </div>
                <div class="form-group inline">
                    <button class="btn" type="submit">查询</button>
                    <input type="hidden" name="liid" value="<?php echo $liid;?>"/>
                    <a class="btn" onclick="edit(0)">添加新角色</a>
                    <a class="btn" onclick="ciy_ifropen('rigger/system_cata.php?types=user_power','权限管理');">权限管理</a>
                </div>
            </form>
        </div>
      </div>
    
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
                <th>分组</th>
                <th>角色名称</th>
                <th style="width:300px;">权限</th>
                <th>角色说明</th>
                <th>操作</th>
            </tr>
<?php
$code_power = getcodes('user_power');
foreach($rows as $row){
    $id = (int)$row['id'];
?>
    <tr data-id="<?php echo $id;?>">
        <td><div style="text-align: center;"><?php echo ccode($code_rolegroup,$row['groups']);?></div></td>
        <td><div><?php echo $row['title'];?></div></td>
        <td><div><?php echo power_trans($code_power,$row['power']);?></div></td>
        <td><div><?php echo $row['memo'];?></div></td>
        <td><div>
            <a class="btn" onclick="edit(<?php echo $id;?>)">编辑</a>
            <a class="btn" onclick="ciy_fastfunc('确认是否删除？','del','id=<?php echo $id;?>','reload');">删除</a>
        </div></td>
        </tr>
<?php } ?>
          </table>
      </div>
      <?php echo showpage($pageno,$pagecount,$mainrowcount);?>
<?php } ?>
</div>
<script src="/jscss/jquery-1.12.4.min.js" type="text/javascript"></script>
<script src="/jscss/ciy.js" type="text/javascript"></script>
<script type="text/javascript">
'use strict';
$(function(){
    ciy_table_adjust('.table');
    $('[data-departid]').on('click',function(){
        location.href='?departid='+$(this).attr('data-departid');
    });
});
function edit(id)
{
    ciy_alert({title:'角色管理',contentstyle:'width:680px;height:40em;',frame:'rigger/admin_role_update.php?id='+id,nobutton:true});
}
</script>
</body>
</html>