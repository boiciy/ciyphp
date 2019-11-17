<?php require 'init.php'; require PATH_PROGRAM.NAME_SELF.'.pro.php';?><!DOCTYPE html><html>
<head>
<title></title>
<meta http-equiv="Content-type" content="text/html; charset=utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1">
<meta http-equiv="X-UA-Compatible" content="IE=Edge，chrome=1">
<link href="/jscss/style.css" rel="stylesheet" type="text/css" />
<body>
<div class="container">
    <form methodd="get" action="">
        <div class="form-group inline">
            <label>部门名称</label>
            <div><input type="text" name="title" value="<?php echo get('title');?>" style="width:10em;"/></div>
        </div>
        <div class="form-group inline">
            <button class="btn" type="submit">检索</button>
        </div>
    </form>
    <kbd>注意</kbd><code>选择部门后，用户权限也将会重新分配</code>
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
                <th style="width:100%;">部门名称</th>
                <th>选择</th>
            </tr>
<?php
$treerows = treerows_sort($rows);
foreach($treerows as $row){
    $id = (int)$row['id'];
?>
    <tr data-id="<?php echo $id;?>" data-upid="<?php echo $row['upid'];?>">
        <td><div<?php echo ($row['_count'] > 0)?' data-treeid="'.$id.'"':'';?> class="ciy-tree-spread"><?php
        echo str_repeat('　',$row['_deep']);
        if($row['_count']>0)
            echo '<span class="ciy-tree-dot">▶</span> '.$row['title'].'<span style="font-size:0.7em;">('.$row['_count'].')</span>';
        else
            echo '　 '.$row['title'];
        ?></div></td>
        <td><div>
            <?php echo ($id == $selid?'已选中':'<a class="btn" onclick="selected('.$id.')">选中</a>');?>
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
    ciy_table_tree('.table');
});
function selected(id){
    window.alertcb(true,'选中',{id:id});
}
</script>
</body>
</html>