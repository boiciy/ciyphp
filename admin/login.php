<?php require 'init.php'; require PATH_PROGRAM.NAME_SELF.'.pro.php'; ?><!DOCTYPE html><html>
<head>
<title>管理员登录</title>
<meta http-equiv="Content-type" content="text/html; charset=utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1, user-scalable=0, shrink-to-fit=no">
<meta http-equiv="X-UA-Compatible" content="IE=Edge，chrome=1">
<link href="/jscss/style.css" rel="stylesheet" type="text/css" />
</head>
<body style="background: #04c1fb url(/jscss/bglogin.png) no-repeat top">
  <div class="container" style="width:25em;margin:0 auto;">
    <fieldset style="margin:10em auto 2em auto;background:rgba(255,255,255,0.6);border:1px solid #ffffff;" class="tips">
      <legend style="background:rgba(255,255,255,0.9);border-radius: 0.2em;border:1px solid #ffffff;">管理员登录</legend>
      <div>
      <form onsubmit="javascript:formsubmit(this);return false;">
          <div class="form-group" data-check>
              <label style="width:3em;color:#666666;">手机号</label>
              <div>
              <input type="text" name="user"/>
              </div>
          </div>
          <div class="form-group" data-check>
              <label style="width:3em;color:#666666;">密　码</label>
              <div>
              <input type="password" name="pass"/>
              </div>
          </div>
          <div class="form-group">
              <label style="width:3em;"></label>
              <div>
                  <button type="submit" class="btn btn-lg">登录</button>
                  <code>10000000000</code> <code>123</code>
              </div>
          </div>
      </form>
      </div>
    </fieldset>
  </div>
<hr style="margin-top:2.5em;background-color:#ffffff;"/>
<div style="text-align:center;color:#ffffff;text-shadow: 0 0 2px #000000;">© 2019 众产国际 · 演示后台</div>
<div class="ciy-mask"></div>
<script src="/jscss/jquery-1.12.4.min.js" type="text/javascript"></script>
<script src="/jscss/ciy.js" type="text/javascript"></script>
<script type="text/javascript">
'use strict';
function formsubmit(dom)
{
    var postparam = ciy_getform(dom);
    if (postparam._check)
        return ciy_alert(postparam._check);
    callfunc("login",postparam,function(json){
        ciy_toast('登录成功',{done:function(){
            location.href='./';
        }});
    });
}
</script>
</body></html>