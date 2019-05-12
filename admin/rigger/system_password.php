<?php require 'init.php'; require PATH_PROGRAM.NAME_SELF.'.pro.php'; ?><!DOCTYPE html><html>
<head>
<title></title>
<meta http-equiv="Content-type" content="text/html; charset=utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1, user-scalable=0, shrink-to-fit=no">
<meta http-equiv="X-UA-Compatible" content="IE=Edge，chrome=1">
<link href="/jscss/style.css" rel="stylesheet" type="text/css" />
</head>
<body>
  <div class="container">
      <a onclick="javascript:clearall();">清理缓存</a>
    <fieldset style="margin:8em auto 2em auto;width:22em;" class="box">
      <legend>修改密码</legend>
      <div style="padding:1em;">
      <form>
          <div class="form-group" data-check>
              <label>原密码</label>
              <div>
              <input type="password" name="oldpass"/>
              </div>
          </div>
          <div class="form-group" data-check>
              <label>新密码</label>
              <div>
              <input type="password" name="pass"/>
              </div>
          </div>
          <div class="form-group">
              <label>重复密码</label>
              <div>
              <input type="password" name="pass2"/>
              </div>
          </div>
          <div class="form-group">
              <div style='text-align:center;'>
                <button type="button" class="btn btn-lg" onclick="javascript:update(this);">修改密码</button>
              </div>
          </div>
      </form>
      </div>
    </fieldset>
  </div>
<div class="ciy-mask"></div>
<script src="/jscss/jquery-1.12.4.min.js" type="text/javascript"></script>
<script src="/jscss/ciy.js" type="text/javascript"></script>
<script type="text/javascript">
'use strict';
function clearall()
{
    window.localStorage.clear();
    ciy_toast('清理完成');
}
function update(dom)
{
    var postparam = ciy_getform(dom);
    if (postparam._check)
        return ciy_alert(postparam._check);
    if (postparam.pass != postparam.pass2)
        return ciy_alert("两次填写的密码要相同");
    callfunc("update",postparam,function(json){
        ciy_toast('密码修改成功');
    });
}
</script>
</body></html>