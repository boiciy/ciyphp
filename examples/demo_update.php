<?php require 'init.php'; require PATH_PROGRAM.'/'.NAME_SELF.'.pro.php'; ?><!DOCTYPE html> <html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>数据提交</title>
<meta http-equiv="Content-type" content="text/html; charset=utf-8">
<script src="jscss/jquery-1.7.1.min.js" type="text/javascript"></script>
<script src="jscss/common.js" type="text/javascript"></script>
<script src="jscss/upload.js" type="text/javascript"></script>
<link href="jscss/upload.css" rel="stylesheet" type="text/css" />
<link href="jscss/style.css" rel="stylesheet" type="text/css" />
</head>
<body>

<div class="clearfix navout">
    <div class="indexout" style="width:100%;">
    <div class="navpage">当前位置： 测试</div>
        <div class="new_pub">
            <ul class="pub_navnone">
            </ul>
            <div class="mainview">
                <div class="mainbox">
    <table style="width:100%;" cellspacing="0">
        <tr>
            <td class="formlabel">
                <label>姓名</label>
            </td>
            <td>
                <input class="n" type="text" name="truename" value="<?php echo @$updaterow['truename'];?>" style="width:250px;"/>
            </td>
        </tr>
        <tr>
            <td class="formlabel">
                <label>头像</label>
            </td>
            <td>
                <div class="upload" id="upload_icon" data-num="1" data-name="icon" data-type="jpg,jpeg,png,gif" action="upload.php" data-save="/upload/icon/{Y}_{M}/{D}_{H}{I}{S}{Rnd}.{Ext}" data-value="<?php echo @$updaterow['icon'];?>"></div>
            </td>
        </tr>
        <tr>
            <td class="formlabel">
                <label>分数</label>
            </td>
            <td>
                <input class="n" type="text" name="scores" value="<?php echo @$updaterow['scores'];?>" style="width:50px;"/>
            </td>
        </tr>
        <tr>
            <td class="formbutton" colspan="2" style="position: relative;">
                <button class="n" type="button" onclick="javascript:formsubmit();"><?php echo $btnname;?></button>
                <input type="hidden" name="id" value="<?php echo $id;?>"/>
            </td>
        </tr>
    </table>
                </div>
            </div>
        </div> 
        <script type="text/javascript">
$(document).ready(function(){
        $("#upload_icon").upload();
});
function formsubmit()
{
    var postparam={};
    postparam.id = $("input[name=id]").val();
    postparam.truename = $("input[name=truename]").val();
    postparam.icon = $("input[name=icon]").val();
    postparam.scores = getint($("input[name=scores]").val());
    if (postparam.truename === "") {
        alert("请填写姓名");
        return false;
    }
    callfunc("update",postparam,function(json){
        alert('提交成功');
    });
}
        </script>
    </div>
</div>

</body></html>