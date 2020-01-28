<?php require 'init.php'; require PATH_PROGRAM.NAME_SELF.'.pro.php'; ?><!DOCTYPE html><html>
<head>
<title></title>
<meta http-equiv="Content-type" content="text/html; charset=utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1">
<meta http-equiv="X-UA-Compatible" content="IE=Edge，chrome=1">
<link href="/jscss/style.css" rel="stylesheet" type="text/css" />
<body>
<div class="container">
    <div class="crumbs">当前位置： 系统管理 → 授权管理</div>
    <div class="ciy-tab ciy-tab-card">
        <ul><?php echo create_li($code_urole,$liid);?></ul>
        <div class="ciy-tab-box">
            <form method="get" action="">
                <div class="form-group inline">
                    <label>角色名称</label>
                    <div><input type="text" name="rolename" value="<?php echo get('rolename');?>" style="width:10em;"/></div>
                </div>
                <div class="form-group inline">
                    <label>用户名</label>
                    <div><input type="text" name="username" value="<?php echo get('username');?>" style="width:10em;"/></div>
                </div>
                <div class="form-group inline">
                    <button class="btn" type="submit">查询</button>
                    <input type="hidden" name="liid" value="<?php echo $liid;?>"/>
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
                <th>角色名称</th>
                <th>用户名</th>
                <th>状态</th>
                <th>授权人</th>
                <th>授权时间</th>
                <th>操作</th>
            </tr>
<?php
foreach($rows as $row){
    $id = (int)$row['id'];
?>
    <tr data-id="<?php echo $id;?>">
        <td><div><?php echo $row['rolename'];?></div></td>
        <td><div><?php echo $row['username'];?></div></td>
        <td><div style="text-align:center;"><?php echo ccode($code_urole, $row['status']);?></div></td>
        <td><div><?php
        $csql = new ciy_sql('p_admin');
        $csql->where('id',$row['adminid'])->column('truename');
        echo $mydata->get1($csql);
        ?></div></td>
        <td><div><?php echo todate($row['addtimes']);?></div></td>
        <td><div>
                <?php
                if($row['status'] == 10)
                    echo '<a class="btn" onclick="ciy_fastfunc(\'确认是否停用？\',\'status\',\'status=9&id='.$id.'\',\'reload\');">停用</a>';
                else if($row['status'] == 9)
                    echo '<a class="btn" onclick="ciy_fastfunc(\'确认是否启用？\',\'status\',\'status=10&id='.$id.'\',\'reload\');">启用</a>';
                else
                {
                    if($row['status'] != 2)
                        echo '<a class="btn" onclick="ciy_fastfunc(\'确认是否拒绝？\',\'status\',\'status=2&id='.$id.'\',\'reload\');">拒绝</a>';
                    echo '<a class="btn" onclick="ciy_fastfunc(\'确认是否接受？\',\'status\',\'status=10&id='.$id.'\',\'reload\');">接受</a>';
                }
                ?>
            
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
});
</script>
</body>
</html>