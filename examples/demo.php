<?php
require 'init.php';
require PATH_PROGRAM . '/' . NAME_SELF . '.pro.php';
?><!DOCTYPE html>
<html>
<head>
<title>测试</title>
<meta http-equiv="Content-type" content="text/html; charset=utf-8">
<script src="jscss/jquery-1.7.1.min.js" type="text/javascript"></script>
<script src="jscss/common.js" type="text/javascript"></script>
<link href="jscss/style.css" rel="stylesheet" type="text/css" />
<body>
<div class="clearfix navout">
<div class="indexout">
    <div class="navpage">当前位置： 测试</div>
    <div class="new_pub">
            <ul class="pub_navnone">
            </ul>
        <div class="mainview">
            <div class="mainbox">
                <form id="formbase" method="get" action="">
                    <table class="table_query">
                        <tr>
                            <td><label>姓名</label></td>
                            <td><input class="n" type="text" name="truename" style="width:160px" value="<?php echo get('truename');?>"/></td>
                            <td><button class="n" type="submit">查询</button></td>
                            <td><a class="btn" href="demo_update.php">新增</a></td>
                        </tr></table>
                    <input type="hidden" name="search" value="true"/>
                </form>
                <div class="clear"></div>
            </div>
        </div>
<table border="1" cellpadding="8" class="record">
    <tr>
        <th>ID</th>
        <th>头像</th>
        <th>姓名</th>
        <th>分数</th>
        <th>日期</th>
        <th>IP</th>
        <th>操作</th>
    </tr>
<?php
foreach($rows as $row)
{
    $id = (int)$row['id'];
?>
    <tr class="tdcenter trlist" id="td<?php echo $id;?>" onclick="javascript:$('#td<?php echo $id;?>').toggleClass('checked');">
        <td><?php echo enid($id);?></td>
        <td><img src="<?php echo $row['icon'];?>" style="height:3em;"/></td>
        <td><?php echo $row['truename'];?></td>
        <td><?php echo $row['scores'];?></td>
        <td><?php echo $row['addtimes'];?></td>
        <td><?php echo long2ip($row['ip']);?></td>
        <td>
            <a class="btn" href="demo_update.php?id=<?php echo $id;?>" target="_blank">编辑</a>
        </td>
        </tr>
<?php
}
if(count($rows) == 0){
?>
    <tr>
        <td colspan="7" class="tdnone">无</td>
    </tr>
<?php
}
?>
</table>
<div class="page">
    <div class="page_lop l">
        <table>
            <tr>
                <td width="40px;"><a class="btnsmall" onclick="selectall()">全选</a></td>
                <td width="40px;"><a class="btnsmall" onclick="selectdiff()">反选</a></td>
                <td width="80px;"><a class="btnsmall" onclick="setact('del',{confirmmsg:'是否删除？'})">删除</a></td>
                <td><a href="<?php echo urlparam('', array('csv' => 'true','func' => 'cc','prefix' => '外呼系统'));?>" class="btnsmall" target="_blank">导出到Excel</a></td>
            </tr>
        </table>
        </div> 
                <div class="page_txt">
                            <div class="digg">
<?php
echo showpage($pageno,$pagecount, $mainrowcount);
?>
                                </div> </div> </div> 
            </div> 
</div>
</div>
</body>
</html>