<?php require 'init.php'; require PATH_PROGRAM.NAME_SELF.'.pro.php'; ?><!DOCTYPE html><html>
<head>
<title></title>
<meta http-equiv="Content-type" content="text/html; charset=utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1">
<meta http-equiv="X-UA-Compatible" content="IE=Edge，chrome=1">
<link href="/jscss/style.css" rel="stylesheet" type="text/css" />
<body>
<div class="container">
    <div class="crumbs">当前位置： 信息中心 → 验证码管理</div>
    <form methodd="get" action="">
        <div class="form-group inline">
            <label>手机号</label>
            <div><input type="text" name="mobile" value="<?php echo get('mobile');?>" style="width:10em;"/></div>
        </div>
        <div class="form-group inline">
            <div>
                <button class="btn" type="submit">查询</button>
                <a class="btn" onclick="addnewmobile();">添加测试手机号</a>
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
                <th>手机号</th>
                <th>验证码</th>
                <th>时间</th>
                <th>IP</th>
            </tr>
<?php
foreach($rows as $row){
    $id = (int)$row['id'];
?>
    <tr data-id="<?php echo $id;?>">
        <td><div><?php echo $id;?></div></td>
        <td><div><?php echo $row['mobile'];?></div></td>
        <td><div><?php echo $row['code'];?></div></td>
        <td><div><?php echo todate($row['addtimes']);?></div></td>
        <td><div><?php echo long2ip($row['ip']);?></div></td>
        </tr>
<?php } ?>
          </table>
      </div>
    <div class="ciy_tabbtn">
        <a class="btn btn-default" onclick="ciy_select_all('.table')">全选</a>
        <a class="btn btn-default" onclick="ciy_select_diff('.table')">反选</a>
        |
        <a class="btn btn-default" onclick="ciy_select_act('.table','del')">删除</a>
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
    ciy_select_init('.table');
});
function addnewmobile()
{
    ciy_alert({
        content:'<div class="form-group"><label style="width:4em;">手机号</label><div><input type="text" name="mobile"/></div></div>\n\
<div class="form-group"><label style="width:4em;">验证码</label><div><input type="text" name="code" value="1234"/></div></div>',
        title:'添加测试手机号',
        btns:['添加'],
        cb:function(btn,inputs){
            if(!ciy_check('mobile',inputs.mobile))
                return ciy_alert('请输入手机号');
            ciy_fastfunc('','newtest',inputs,'reload');
        }
    });

}
</script>
</body>
</html>