<?php require 'init.php'; require PATH_PROGRAM.NAME_SELF.'.pro.php'; ?><!DOCTYPE html><html>
<head>
<title></title>
<meta http-equiv="Content-type" content="text/html; charset=utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1">
<meta http-equiv="X-UA-Compatible" content="IE=Edge，chrome=1">
<link href="/jscss/style.css" rel="stylesheet" type="text/css" />
<body>
<div class="container">
    <div class="crumbs">当前位置： 系统管理 → 系统日志</div>
    <div class="ciy-tab ciy-tab-card">
        <ul>
            <li<?php echo ($liid == 0)?' class="active"':''?>><a href="?liid=0">未处理日志</a></li>
            <li<?php echo ($liid == 1)?' class="active"':''?>><a href="?liid=1">已处理日志</a></li>
            <li<?php echo ($liid == 2)?' class="active"':''?>><a href="?liid=2">已锁定日志</a></li>
        </ul>
        <div class="ciy-tab-box">
            <form method="get" action="">
                <div class="form-group inline">
                    <label>用户ID</label>
                    <div><input type="text" name="userid" value="<?php echo get('userid');?>" style="width:6em;"/></div>
                </div>
                <div class="form-group inline">
                    <label>类型</label>
                    <div><input type="text" name="types" value="<?php echo get('types');?>" style="width:6em;"/></div>
                </div>
                <div class="form-group inline">
                    <label>详情</label>
                    <div><input type="text" name="logs" value="<?php echo get('logs');?>" style="width:15em;"/></div>
                </div>
                <div class="form-group inline">
                    <button class="btn" type="submit">查询</button>
                    <input name="liid" value="<?php echo get('liid');?>" type="hidden"/>
                    <a class="btn" onclick="ciy_fastfunc('确认清理？','clear');">清理已处理的过期日志</a>
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
                <th>ID</th>
                <th>用户ID</th>
                <th>处理人ID</th>
                <th>状态</th>
                <th>类型</th>
                <th>时间</th>
                <th>IP</th>
                <th>详情</th>
            </tr>
<?php
foreach($rows as $row){
    $id = (int)$row['id'];
?>
    <tr data-id="<?php echo $id;?>">
        <td><div><?php echo $id;?></div></td>
        <td><div onclick="ciy_ifropen('rigger/admin_user.php?id=<?php echo $row['userid'];?>','管理员管理');"><?php echo $row['userid'];?></div></td>
        <td><div onclick="ciy_ifropen('rigger/admin_user.php?id=<?php echo $row['readid'];?>','管理员管理');"><?php echo $row['readid'];?></div></td>
        <td><div style="text-align:center;"><?php
        if($row['status']==1)
            echo '√';
        elseif($row['status']==2)
            echo '锁定';
        else
            echo '';
        ?></div></td>
        <td><div><?php echo $row['types'];?></div></td>
        <td><div><?php echo todate($row['addtimes']);?></div></td>
        <td><div><?php echo long2ip($row['ip']);?></div></td>
        <td><div onclick="showlog(this)"><?php echo htmlspecialchars($row['logs']);?></div></td>
        </tr>
<?php }?>
          </table>
      </div>
    <div class="ciy-tabbtn">
        <a class="btn btn-default" onclick="ciy_select_all('.table')">全选</a>
        <a class="btn btn-default" onclick="ciy_select_diff('.table')">反选</a>
        |
      <?php if($liid == 2){?>
        <a class="btn btn-default" onclick="ciy_select_act('.table','unlock')">解锁</a>
      <?php }else{?>
        <a class="btn btn-default" onclick="ciy_select_act('.table','read')">已处理</a>
        <a class="btn btn-default" onclick="ciy_select_act('.table','lock')">锁定</a>
      <?php }?>
    </div>
      <?php echo showpage($pageno,$pagecount,$mainrowcount);?>
<?php }?>
</div>
<script src="/jscss/jquery-1.12.4.min.js" type="text/javascript"></script>
<script src="/jscss/ciy.js" type="text/javascript"></script>
<script type="text/javascript">
'use strict';
$(function(){
    ciy_table_adjust('.table');
    ciy_select_init('.table');
});
function showlog(dom)
{
    ciy_alert(dom.innerText,null,{max:true,nobutton:true,title:'LOG详情'});
}
</script>
</body>
</html>