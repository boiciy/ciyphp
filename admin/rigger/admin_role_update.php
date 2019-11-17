<?php require 'init.php'; require PATH_PROGRAM.NAME_SELF.'.pro.php';?><!DOCTYPE html><html>
<head>
<title></title>
<meta http-equiv="Content-type" content="text/html; charset=utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1">
<meta http-equiv="X-UA-Compatible" content="IE=Edge，chrome=1">
<link href="/jscss/style.css" rel="stylesheet" type="text/css" />
</head>
<body>
<div class="container">
    <div class="crumbs">当前位置： 系统管理 → 角色管理 → <?php echo $btnname;?></div>
    <form>
        <div class="form-group">
            <label>角色分组</label>
            <div><?php echo create_select($code_rolegroup,@$updaterow['groups'],'groups');?></div>
        </div>
        <div class="form-group" data-check>
            <label>角色名称</label>
            <div><input type="text" name="title" style='width:15em;' value="<?php echo @$updaterow['title'];?>"/></div>
        </div>
        <div class="form-group">
            <label>角色权限</label>
            <div>
                  <?php 
                  echo create_checkbox($code_power,@$updaterow['power'],'power',array('dot'=>'.'));
                  ?>
            </div>
        </div>
        <div class="form-group">
            <label>角色说明</label>
            <div><input type="text" name="memo" style='width:100%;' value="<?php echo @$updaterow['memo'];?>"/></div>
        </div>
        <div class="form-group">
            <div style="text-align:center;">
            <button class="btn btn-lg" type="button" onclick="javascript:formsubmit(this);"><?php echo $btnname;?></button>
            <input type="hidden" name="id" value="<?php echo $id;?>"/>
            </div>
        </div>
    </form>
</div>
<script src="/jscss/jquery-1.12.4.min.js" type="text/javascript"></script>
<script src="/jscss/ciy.js" type="text/javascript"></script>
<script type="text/javascript">
$(document).ready(function(){
    ciy_alertautoheight();
});
function formsubmit(dom)
{
    var postparam = ciy_getform(dom);
    if (postparam._check)
        return ciy_alert(postparam._check);
    callfunc("update",postparam,function(json){
        ciy_refresh();
        ciy_toast('提交成功',{done:function(){
            ciy_alertclose();
        }});
    });
}
</script>
</body></html>