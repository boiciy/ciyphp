<?php require 'init.php'; require PATH_PROGRAM.NAME_SELF.'.pro.php'; ?><!DOCTYPE html> <html>
<head>
<title>数据提交</title>
<meta http-equiv="Content-type" content="text/html; charset=utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1">
<meta http-equiv="X-UA-Compatible" content="IE=Edge，chrome=1">
<link href="jscss/style.css" rel="stylesheet" type="text/css" />
<link href="jscss/upload.css" rel="stylesheet" type="text/css" />
</head>
<body>
<div class="container">
    <div>当前位置： 增删改查</div>
    <form>
        <div class="form-group">
            <label>姓名</label>
            <div><input type="text" name="truename" value="<?php echo @$updaterow['truename'];?>"/></div>
        </div>
        <div class="form-group">
            <label>头像</label>
            <div>
                <div class="upload" id="upload_icon" data-num="1" data-name="icon" data-type="jpg,jpeg,png,gif" action="upload.php" data-save="/upload/icon/{Y}_{M}/{D}_{H}{I}{S}{Rnd}.{Ext}" data-value="<?php echo @$updaterow['icon'];?>"></div>
            </div>
        </div>
        <div class="form-group">
            <label>分数</label>
            <div class="form-group-inline form-input-group"><input type="text" style='width:50px;' name="scores" value="<?php echo @$updaterow['scores'];?>"/><div>分</div></div>
        </div>
          <div class="form-group">
              <label>复选框</label>
              <div>
                  <label class="formi"><input name="chkname" value="爱迪生value" type="checkbox"/><i></i>爱迪生</label>
                  <label class="formi"><input name="chkname" value="特斯拉value" type="checkbox" checked="checked"/><i></i>特斯拉</label>
                  <label class="formi"><input name="chkname" value="收银台value" type="checkbox" disabled/><i></i>收银台</label>
              </div>
          </div>
          <div class="form-group">
              <label>单选框</label>
              <div>
                  <label class="formi"><input type="radio" value="爱迪生value" name="vradio"/><i></i>爱迪生</label>
                  <label class="formi"><input type="radio" value="天鹅绒value" name="vradio"/><i></i>天鹅绒</label>
              </div>
          </div>
          <div class="form-group">
              <label>列表框</label>
              <div style="width:8em;">
                  <select name="selccc">
                      <option value="3">上海</option>
                      <option value="4" disabled>杭州(不可选)</option>
                      <option value="5">武汉</option>
                  </select>
              </div>
          </div>
          <div class="form-group">
              <label>开关</label>
              <div>
                  <label class="formswitch"><input type="checkbox" name="switch"/><y>ON</y><n>OFF</n><i></i></label>
              </div>
          </div>
          <div class="form-group">
              <label>多行</label>
              <div>
                  <textarea name="inptext" style="height:5em;"></textarea>
              </div>
          </div>
        <div class="form-group" style="text-align:center;">
            <button class="btn" type="button" onclick="javascript:formsubmit(this);"><?php echo $btnname;?></button>
            <input type="hidden" name="id" value="<?php echo $id;?>"/> 请按 <kbd>F12</kbd>，查看调试界面打印LOG，包含了选项标题。
        </div>
    </form>
</div>
<script src="jscss/jquery-1.12.4.min.js" type="text/javascript"></script>
<script src="jscss/ciy.js" type="text/javascript"></script>
<script src="jscss/upload.js" type="text/javascript"></script>
<script type="text/javascript">
$(document).ready(function(){
    $("#upload_icon").upload();
});
function formsubmit(dom)
{
                ciy_ifrclose();
                return;
    var postparam = ciy_getform(dom);
    console.log(postparam);
    if (postparam.truename === "") {
        ciy_alert("请填写姓名");
        return false;
    }
    callfunc("update",postparam,function(json){
        if(postparam.id == 0)
        {
            ciy_toast('新增成功',{done:function(){
                ciy_ifrclose();
            }});
        }
        else
        {
            ciy_refresh();
            ciy_toast('更新成功',{done:function(){
                ciy_alertclose();
            }});
        }
    });
}
</script>
</body></html>