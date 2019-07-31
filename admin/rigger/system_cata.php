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
                <div><input type="text" name="types" value="<?php echo get('types');?>" style="width:15em;"/></div>
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
                <a class="btn" onclick="multiadd()">批量新增代码</a>
            </div>
        </form>
    </div>
      <div class='table'>
          <table>
            <tr>
                <th>编号</th>
                <th style="width:80px;">上级ID</th>
                <th style="width:80px;">排序</th>
                <th>分类</th>
                <th>名称</th>
                <th>值(char10)</th>
                <th>扩展值</th>
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
        <td><div><input style="width:100%;" type="text" name="extdata" value="<?php echo @$row['extdata'];?>"/></div></td>
        <td><div>
                <?php
                if($id == 0)
                    echo '<a class="btn" onclick="update(this,\'reload\')">新增</a>';
                else
                    echo '<a class="btn" onclick="update(this)">更新</a>';
                ?>
        </div></td>
    </tr>
<?php } ?>
          </table>
          
      </div>
    <div class="ciy-tabbtn">
        <a class="btn btn-default" onclick="ciy_select_all('.table')">全选</a>
        <a class="btn btn-default" onclick="ciy_select_diff('.table')">反选</a>
        |
        <a class="btn btn-default" onclick="ciy_select_act('.table','del','是否批量删除？')">批量删除</a>
    </div>
      <?php echo showpage($pageno,$pagecount,$mainrowcount);?>
</div>
<div id="alert_multiadd" style="display:none;">
    <div class="form-group"><label style="width:3em;">分类</label><div><input name="multi_types" type="text"/></div></div>
    <div class="form-group"><label style="width:3em;">代码</label><div><textarea name="multi_code" style="width: 100%;height:10em;white-space:nowrap;"></textarea><br/><code>名称,值,扩展值</code> <code>一行一条</code></div></div>
</div>
<script src="/jscss/jquery-1.12.4.min.js" type="text/javascript"></script>
<script src="/jscss/ciy.js" type="text/javascript"></script>
<script type="text/javascript">
$(function(){
    ciy_table_adjust('.table');
    ciy_select_init('.table');
});
function update(dom,act)
{
    ciy_fastfunc('','update',ciy_getform(dom,'TR'),act);
}
function multiadd()
{
    ciy_alert({
        title:'批量新增代码',
        content:document.getElementById("alert_multiadd").innerHTML,
        cb:function(btn,inputs){
            if(btn != "新增")
                return;
            callfunc('multiadd',inputs,function(json){
                ciy_toast('操作成功',{done:function(){
                    location.reload();
                }});
            });
        },btns:["新增","取消"]
    });
}
</script>
</body></html>