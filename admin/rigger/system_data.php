<?php
require 'init.php';
$mydata = new ciy_data();
$rsuser = verifyadmin();
?><!DOCTYPE html><html>
<head>
<title>Demo</title>
<meta http-equiv="Content-type" content="text/html; charset=utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1">
<meta http-equiv="X-UA-Compatible" content="IE=Edge，chrome=1">
<link href="/jscss/style.css" rel="stylesheet" type="text/css" />
<body>
<div class="container">
    <div class="crumbs">当前位置： 系统管理 → 数据计算</div>
    
    <fieldset class="ciy-fieldset-tips">
      <legend>数据参数</legend>
      <div>
        <form method="get" action="">
            <div class="form-group">
                <label>调用函数</label>
                <div><input type="text" name="func" value="<?php echo get('func');?>" style="width:20em;"/></div>
            </div>
            <div class="form-group">
                <label>函数参数</label>
                <div><input type="text" name="param" value="<?php echo get('param');?>" style="width:40em;"/></div>
            </div>
            <div class="form-group">
                <label></label>
                <button class="btn" type="submit">开始执行</button>
            </div>
        </form>
<pre>
地区XML入库：calcataarea
</pre>
          <code>这里适合写一次性或不定期执行的代码，如数据修正、导入导出、统计等</code>
          <code>请注意权限限制</code>
      </div>
    </fieldset>
    <br/>
    <blockquote>运行反馈</blockquote>
<?php
$func = get('func');
$param = get('param');
if(!empty($func))
{
    pr($func.' 执行中...');
    if($func == 'calcataarea')
    {
    }
    pr($func.' 执行完成');
}
?>
</div>
<script src="/jscss/jquery-1.12.4.min.js" type="text/javascript"></script>
<script src="/jscss/ciy.js" type="text/javascript"></script>
<script type="text/javascript">
'use strict';
$(function(){
});
</script>
</body>
</html>