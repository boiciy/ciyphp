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
    <div class="crumbs">当前位置： 系统管理 → 配置管理</div>
      <div class='table'>
          <table>
            <tr>
                <th>编号</th>
                <th>标题</th>
                <th>代码</th>
                <th>设定值1</th>
                <th>设定值2</th>
                <th>设定值3</th>
                <th>操作</th>
            </tr>
<?php
foreach($rows as $row){
    $id = (int)$row['id'];
?>
    <tr data-id="<?php echo $id;?>">
        <td><div style="text-align: center;"><?php echo $id;?><input type="hidden" name="id" value="<?php echo $id;?>"/></div></td>
        <td><div><input style="width:100%;" type="text" name="title" value="<?php echo @$row['title'];?>"/></div></td>
        <td><div><input style="width:100%;" type="text" name="types" value="<?php echo @$row['types'];?>"/></div></td>
        <td><div><input style="width:100%;" type="text" name="params" value="<?php echo str_replace('"','&quot;',@$row['params']);?>"/></div></td>
        <td><div><input style="width:100%;" type="text" name="param2" value="<?php echo str_replace('"','&quot;',@$row['param2']);?>"/></div></td>
        <td><div><input style="width:100%;" type="text" name="param3" value="<?php echo str_replace('"','&quot;',@$row['param3']);?>"/></div></td>
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
<fieldset class="tips">
  <legend>配置说明</legend>
  <div>
    <ul>
        <li>建议使用getconfig函数调用。<br/>
            <code>例1:</code><br/>
            $data = getconfig('代码','默认设定值1');<br/>
            结果字符串: $data = <kbd>值1</kbd><br/>
            <code>例2:</code><br/>
            $data = getconfig('代码','默认设定值1','默认设定值2');<br/>
            结果数组: $data = <kbd>array(值1,值2)</kbd><br/>
            <code>例3:</code><br/>
            $data = getconfig('代码','默认设定值1','默认设定值2','默认设定值3');<br/>
            结果数组: $data = <kbd>array(值1,值2,值3)</kbd><br/>
        </li>
        <li>getconfig函数在acommon.php中</li>
    </ul>
  </div>
</fieldset>
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