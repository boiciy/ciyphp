<?php require 'init.php'; require PATH_PROGRAM.NAME_SELF.'.pro.php'; ?><!DOCTYPE html><html>
<head>
<title></title>
<meta http-equiv="Content-type" content="text/html; charset=utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1">
<meta http-equiv="X-UA-Compatible" content="IE=Edge，chrome=1">
<link href="/jscss/style.css" rel="stylesheet" type="text/css" />
</head>
<body>
<div class="container">
    <div class="crumbs">当前位置： 系统管理 → 代码维护</div>
    <div class="ciy-tab-box">
        <form method="get" action="">
            <div class="form-group inline">
                <label>上级ID</label>
                <div><input type="text" name="upid" value="<?php echo get('upid');?>" style="width:4em;"/></div>
            </div>
            <div class="form-group inline">
                <label>分类</label>
                <div><input type="text" name="types" value="<?php echo get('types');?>" style="width:10em;"/></div>
            </div>
            <div class="form-group inline">
                <label>名称</label>
                <div><input type="text" name="title" value="<?php echo get('title');?>" style="width:10em;"/></div>
            </div>
            <div class="form-group inline">
                <label>值</label>
                <div><input type="text" name="codeid" value="<?php echo get('codeid');?>" style="width:6em;"/></div>
            </div>
            <div class="form-group inline">
                <button class="btn" type="submit">查询</button>
            </div>
        </form>
    </div>
      <div class='table'>
          <table>
            <tr>
                <th>编号</th>
                <th>上级ID</th>
                <th>排序</th>
                <th>分类</th>
                <th>名称</th>
                <th>值(char10)</th>
                <th>操作</th>
            </tr>
<?php
foreach($rows as $row){
    $id = (int)$row['id'];
?>
    <tr data-id="<?php echo $id;?>">
        <td><div style="text-align: center;"><?php echo $id;?><input type="hidden" name="id" value="<?php echo $id;?>"/></div></td>
        <td><div><input style="width:100%;" type="text" name="upid" value="<?php echo @$row['upid'];?>"/></div></td>
        <td><div><input style="width:100%;" type="text" name="nums" value="<?php echo @$row['nums'];?>"/></div></td>
        <td><div><input style="width:100%;" type="text" name="types" value="<?php echo @$row['types'];?>"/></div></td>
        <td><div><input style="width:100%;" type="text" name="title" value="<?php echo @$row['title'];?>"/></div></td>
        <td><div><input style="width:100%;" type="text" name="codeid" value="<?php echo @$row['codeid'];?>"/></div></td>
        <td><div>
                <?php
                if($id == 0)
                    echo '<a class="btn" onclick="update(this,\'reload\')">新增</a>';
                else
                    echo '<a class="btn" onclick="update(this,\'reload\')">更新</a> <a class="btn" onclick="del('.$id.')">删除</a>';
                ?>
        </div></td>
    </tr>
<?php } ?>
          </table>
          
      </div>
      <?php echo showpage($pageno,$pagecount,$mainrowcount);?>
</div>
<script src="/jscss/jquery-1.12.4.min.js" type="text/javascript"></script>
<script src="/jscss/ciy.js" type="text/javascript"></script>
<script type="text/javascript">
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
</body></html>