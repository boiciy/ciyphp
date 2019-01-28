<?php
require 'init.php';
require PATH_PROGRAM . '/' . NAME_SELF . '.pro.php';
?><!DOCTYPE html><html>
<head>
<title>管理中心</title>
<meta http-equiv="Content-type" content="text/html; charset=utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1, user-scalable=0, shrink-to-fit=no">
<meta http-equiv="X-UA-Compatible" content="IE=Edge，chrome=1">
<link href="/jscss/style.css" rel="stylesheet" type="text/css" />
</head>
<body>
  <div class="container" style="width:25em;margin:0 auto;">
    <fieldset style="margin:10em auto 2em auto;" class="box">
      <legend>管理员登录</legend>
      <div style="padding:1em;">
      <form onsubmit="formsubmit(this);return false;">
          <div class="form-group">
              <label style="width:4em;">账　号</label>
              <div>
              <input type="text" name="user"/>
              </div>
          </div>
          <div class="form-group">
              <label style="width:4em;">密　码</label>
              <div>
              <input type="password" name="pass"/>
              </div>
          </div>
          <div class="form-group">
              <div style='text-align:center;'>
                <button type="submit" class="btn btn-lg">登录</button>
              </div>
          </div>
      </form>
      </div>
    </fieldset>
      <code>推荐整体部署http2，ssl加密，密码部分无需JS加密。</code><br/>
      <code>如果仍使用http部署，建议使用md5/sha等做密文处理。</code>
  </div>
    <hr/>
    
    <div style="text-align:center">
      <p>© 2018 CiyPHP管理系统</p>
    </div>
<div class="ciy-mask"></div>
<script src="/jscss/jquery-1.12.4.min.js"></script>
<script src="/jscss/ciy.js"></script>
<script type="text/javascript">
'use strict';
function formsubmit(dom)
{
    var postparam = ciy_getform(dom);
    if (postparam.user == "")
        return ciy_toast("请填写账号");
    if (postparam.pass == "")
        return ciy_alert("请填写密码");
    callfunc("login",postparam,function(json){
        ciy_toast('登录成功',{done:function(){
            location.href='index.html';
        }});
    });
    return false;
}
</script>
</body></html>