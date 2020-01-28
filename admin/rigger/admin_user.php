<?php require 'init.php'; require PATH_PROGRAM.NAME_SELF.'.pro.php'; ?><!DOCTYPE html><html>
<head>
<title></title>
<meta http-equiv="Content-type" content="text/html; charset=utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1">
<meta http-equiv="X-UA-Compatible" content="IE=Edge，chrome=1">
<link href="/jscss/style.css" rel="stylesheet" type="text/css" />
<body>
<div class="container">
    <div class="crumbs">当前位置： 系统管理 → <a href="admin_user.php">管理员管理</a><?php echo $nav;?></div>
    <div class="ciy-tab ciy-tab-card">
        <ul><?php echo create_li($code_user,$liid);?></ul>
        <div class="ciy-tab-box">
            <form method="get" action="">
                <div class="form-group inline">
                    <label>ID</label>
                    <div><input type="text" name="id" value="<?php echo get('id');?>" style="width:5em;"/></div>
                </div>
                <div class="form-group inline">
                    <label>手机号</label>
                    <div><input type="text" name="mobile" value="<?php echo get('mobile');?>" style="width:10em;"/></div>
                </div>
                <div class="form-group inline">
                    <label>部门</label>
                    <div><input type="text" name="depart" value="<?php echo get('depart');?>" style="width:10em;"/></div>
                </div>
                <div class="form-group inline">
                    <label>姓名</label>
                    <div><input type="text" name="truename" value="<?php echo get('truename');?>" style="width:10em;"/></div>
                </div>
                <div class="form-group inline">
                    <button class="btn" type="submit">查询</button>
                    <input type="hidden" name="liid" value="<?php echo $liid;?>"/>
                    <a class="btn" onclick="edit(0)">添加新管理员</a>
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
                <th style="width:50px;">照片</th>
                <th>ID</th>
                <th>部门</th>
                <th>负责人</th>
                <th>姓名</th>
                <th>性别</th>
                <th>手机号</th>
                <th>角色</th>
                <th>活跃时间</th>
                <th>创建时间</th>
                <th>状态</th>
                <th>操作</th>
            </tr>
<?php
$code_sex = getcodes('user_sex');
foreach($rows as $row){
    $id = (int)$row['id'];
?>
    <tr data-id="<?php echo $id;?>">
        <td><div><?php echo '<img src="'.$row['icon'].'" style="width:100%;" onerror="this.onerror=null;this.src=\'/jscss/face.png\'"/>';?></div></td>
        <td><div><?php echo $id;?></div></td>
        <td><div data-departid="<?php echo $row['departid'];?>" style="cursor:pointer;"><?php echo $row['depart'];?></div></td>
        <td><div style="text-align: center;"><?php echo ($row['leader'] == 1)?'是':'';?></div></td>
        <td><div><?php echo $row['truename'];?></div></td>
        <td><div style="text-align: center;"><?php echo ccode($code_sex,$row['sex']);?></div></td>
        <td><div><?php echo $row['mobile'];?></div></td>
        <td><div><?php
        if($row['power'] == '.*.')
            echo '<kbd>超级管理员</kbd>';
        else
        {
            $csql = new ciy_sql('p_admin_urole');
            $csql->where('userid',$id)->where('status',10);
            $rolerows = $mydata->get($csql);
            foreach ($rolerows as $rolerow) {
                echo '<code>'.$rolerow['rolename'].'</code>';
            }
        }
        ?></div></td>
        <td><div><?php echo todate($row['activetime']);?></div></td>
        <td><div><?php echo todate($row['addtimes']);?></div></td>
        <td><div style="text-align: center;"><?php
        if($row['status'] == 1)
            echo '禁用';
        if($row['status'] == 10)
            echo '正常';
        ?></div></td>
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
    ciy_alert({title:'管理员管理',contentstyle:'width:680px;height:40em;',frame:'rigger/admin_user_update.php?id='+id,nobutton:true});
}
</script>
</body>
</html>
