<?php require 'init.php'; require PATH_PROGRAM.NAME_SELF.'.pro.php'; ?><!DOCTYPE html><html>
<head>
<title></title>
<meta http-equiv="Content-type" content="text/html; charset=utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1">
<meta http-equiv="X-UA-Compatible" content="IE=Edge，chrome=1">
<link href="/jscss/style.css" rel="stylesheet" type="text/css" />
<style type="text/css">
.message{
    margin:-0.5em;
}
.message>div{
    padding:0.5em;
    margin:0;
}
.message>div>div.selected{
    border:1px solid #666666;
    background:#ffffeb;
}
.message>div>div{
    border:1px solid #dddddd;
    border-radius:0.2em;
    transition: all .8s;
}
.message .message_cap{
    border-bottom:1px solid #dddddd;
    padding:0.5em;
}
.message .message_cont{
    padding:0.5em;
}
</style>
<body>
<div class="container">
    <div class="crumbs">当前位置： 消息通知</div>
    <div class="ciy-tab ciy-tab-card">
        <ul>
            <li<?php echo ($liid == 1)?' class="active"':''?>><a href="?liid=1">未读消息</a></li>
            <li<?php echo ($liid == 10)?' class="active"':''?>><a href="?liid=10">已读消息</a></li>
        </ul>
        <div class="ciy-tab-box">
            <form method="get" action="">
                <div class="form-group inline">
                    <label>分类</label>
                    <div><input type="text" name="types" value="<?php echo get('types');?>" style="width:10em;"/></div>
                </div>
                <div class="form-group inline">
                    <label>内容</label>
                    <div><input type="text" name="content" value="<?php echo get('content');?>" style="width:15em;"/></div>
                </div>
                <div class="form-group inline">
                    <button class="btn" type="submit">查询</button>
                    <input name="liid" value="<?php echo get('liid');?>" type="hidden"/>
                    <button class="btn" type="button" onclick="ciy_fastfunc('是否发送消息给自己？','testsend','','reload');">测试发送</button>
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
<div class="row message">
<?php
$i=0;
foreach($rows as $row){
    $id = (int)$row['id'];
?>
    <div class="col-md-6 col-xs-12">
    <div data-id="<?php echo $id;?>" data-status="<?php echo $row['status'];?>" onclick="setread(this)">
        <div class="message_cap"><?php
        echo '<span class="message_status">';
        if($row['status']==1)
            echo '<kbd>未读</kbd>';
        elseif($row['status']==10)
            echo '<code>已读</code>';
        echo '</span> <code>'.todate($row['addtimes']).'</code>';
        if(!empty($row['types']))
            echo '<kbd>'.$row['types'].'</kbd>';
        echo $row['frommsg'];
        ?></div>
        <div class="message_cont"><?php echo $row['content'];?></div>
    </div>
    </div>
    <?php if($i++%2 == 1) echo '<span class="clearfix"></span><span></span>';?>
<?php }?>
</div>
	<div class="clearfix"></div>
    <div class="ciy-tabbtn">
        <a class="btn btn-default" onclick="ciy_select_all('.message')">全选</a>
        <a class="btn btn-default" onclick="ciy_select_diff('.message')">反选</a>
        |
        <a class="btn btn-default" onclick="ciy_select_act('.message','del','是否删除?')">删除</a>
    </div>
      <?php echo showpage($pageno,$pagecount,$mainrowcount);?>
<?php }?>
</div>
<script src="/jscss/jquery-1.12.4.min.js" type="text/javascript"></script>
<script src="/jscss/ciy.js" type="text/javascript"></script>
<script type="text/javascript">
'use strict';
$(function(){
    ciy_select_init('.message');
});
function setread(dom){
    if (dom.getAttribute('data-status') != '1')
        return;
    var postparam = {};
    postparam.id = dom.getAttribute('data-id');
    callfunc("setread",postparam,function(){
    	dom.setAttribute('data-status','10');
    	$('.message_status',dom).html('<code>已读</code>');
    });
}
</script>
</body>
</html>