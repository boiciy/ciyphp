<?php require 'init.php'; require PATH_PROGRAM.NAME_SELF.'.pro.php'; ?><!DOCTYPE html><html>
<head>
<title>数据提交</title>
<meta http-equiv="Content-type" content="text/html; charset=utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1">
<meta http-equiv="X-UA-Compatible" content="IE=Edge，chrome=1">
<link href="/jscss/style.css" rel="stylesheet" type="text/css" />
</head>
<body>
<div class="container">
    <div>当前位置： 增删改查</div>
    <form>
        <div class="form-group" data-check>
            <label>姓名</label>
            <div><input type="text" name="truename" value="<?php echo @$updaterow['truename'];?>" style="width:15em;"/></div>
        </div>
        <div class="form-group">
            <label>头像</label>
            <div>
                <div class="upload" id="upload_icon" data-num="1" data-name="icon" data-type="jpg,jpeg,png,gif" action="/upload.php" data-save="/upload/icon/{Y}_{M}/{D}_{H}{I}{S}{Rnd}" data-value="<?php echo @$updaterow['icon'];?>"></div>
            </div>
        </div>
        <div class="form-group">
            <label>分数</label>
            <div class="form-group-inline form-input-group"><input type="text" style='width:4em;' name="scores" value="<?php echo @$updaterow['scores'];?>"/> 分</div>
        </div>
          <div class="form-group">
              <label>复选框</label>
              <div>
                  <?php $fxks = explode(',',@$updaterow['fxk']);?>
                  <label class="formi"><input name="fxk" value="1" type="checkbox"<?php if(in_array(1,$fxks)) echo ' checked="checked"'; ?>/><i></i>爱迪生</label>
                  <label class="formi"><input name="fxk" value="2" type="checkbox"<?php if(in_array(2,$fxks)) echo ' checked="checked"'; ?>/><i></i>特斯拉</label>
                  <label class="formi"><input name="fxk" value="3" type="checkbox"<?php if(in_array(3,$fxks)) echo ' checked="checked"'; ?> disabled/><i></i>收银台</label>
              </div>
          </div>
          <div class="form-group">
              <label>单选框</label>
              <div>
                  <label class="formi"><input type="radio" value="1" name="dxk"<?php if(@$updaterow['dxk'] == 1) echo ' checked="checked"'; ?>/><i></i>爱迪生</label>
                  <label class="formi"><input type="radio" value="2" name="dxk"<?php if(@$updaterow['dxk'] == 2) echo ' checked="checked"'; ?>/><i></i>天鹅绒</label>
              </div>
          </div>
          <div class="form-group">
              <label>列表框</label>
              <div>
                  <select name="lbk" style="width:8em;">
                      <option value="3"<?php if(@$updaterow['lbk'] == 3) echo ' selected="true"'; ?>>上海</option>
                      <option value="4"<?php if(@$updaterow['lbk'] == 4) echo ' selected="true"'; ?> disabled>武汉(不可选)</option>
                      <option value="5"<?php if(@$updaterow['lbk'] == 5) echo ' selected="true"'; ?>>杭州</option>
                  </select>
              </div>
          </div>
          <div class="form-group">
              <label>开关</label>
              <div>
                  <label class="formswitch"><input type="checkbox" name="kg"<?php if(@$updaterow['kg'] == 1) echo ' checked="true"'; ?>/><y>ON</y><n>OFF</n><i></i></label>
              </div>
          </div>
          <div class="form-group">
              <label>日期</label>
            <div><input type="text" name="activetime" value="<?php echo date('Y-m-d H:i:s',@$updaterow['activetime']);?>" style="width:15em;"/></div>
          </div>
          <div class="form-group">
              <label>多行</label>
              <div>
                  <textarea name="dh" style="height:5em;"><?php echo @$updaterow['dh'];?></textarea>
              </div>
          </div>
        <div class="form-group">
            <div style="text-align:center;">
            <button class="btn btn-lg" type="button" onclick="javascript:formsubmit(this);"><?php echo $btnname;?></button>
            <input type="hidden" name="id" value="<?php echo $id;?>"/> 请按 <kbd>F12</kbd>，查看调试界面打印LOG，包含了选项标题。
            </div>
        </div>
    </form>
</div>
<script src="/jscss/jquery-1.12.4.min.js" type="text/javascript"></script>
<script src="/jscss/ciy.js" type="text/javascript"></script>
<script src="/jscss/upload.js" type="text/javascript"></script>
<script src="/jscss/laydate.js" type="text/javascript"></script>
<script type="text/javascript">
$(document).ready(function(){
    ciy_alertautoheight();
    $("#upload_icon").upload();
    laydate.render({
      elem: document.getElementsByName('activetime')[0]
      ,type: 'datetime'
    });

});
function formsubmit(dom)
{
    var postparam = ciy_getform(dom);
    console.log(postparam);
    if (postparam._check)//检查data-check的输入限制
        return ciy_alert(postparam._check);
    if (postparam.scores < 10)
        return ciy_alert("数字不能小于10");
    callfunc("update",postparam,function(json){
        ciy_refresh();//弹窗新增时使用，刷新父窗口
        ciy_toast('更新成功',{done:function(){
            ciy_alertclose();//弹窗新增时使用，关闭弹窗
            //ciy_ifrclose();//关闭当前标签
        }});
    });
}
</script>
</body></html>