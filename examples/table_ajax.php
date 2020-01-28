<?php
require 'init.php';
require PATH_PROGRAM . NAME_SELF . '.pro.php';
$pagecount = getint('pagecount',20);
$ordertype = getint('ordertype',1);
$pagetype = getint('pagetype',1);
$needtotal = getint('needtotal',1);
?><!DOCTYPE html><html>
<head>
<title>Items</title>
<meta http-equiv="Content-type" content="text/html; charset=utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1">
<meta http-equiv="X-UA-Compatible" content="IE=Edge，chrome=1">
<link href="/jscss/style.css" rel="stylesheet" type="text/css" />
</head>
<body>
<div class='container'>
	<form onsubmit="search(this,'btn');return false;">
		<div class="ciy-tab ciy-tab-card">
			<ul>
				<li data-liid="0" onclick="search(this,'li')"><a>全部</a></li>
				<li data-liid="1" onclick="search(this,'li')"><a>关</a></li>
				<li data-liid="2" onclick="search(this,'li')"><a>开</a></li>
			</ul>
			<div class="ciy-tab-box">
				<div class="form-group inline">
					<label>姓名</label>
					<div>
						<input type="text" name="truename" value="" style="width: 15em;" />
					</div>
				</div>
				<div class="form-group inline">
					<label>分数</label>
					<div>
						<input type="text" name="scores" value="" style="width: 5em;" />
					</div>
				</div>
				<div class="form-group inline">
					<button class="btn" type="submit">查询</button>
				</div>
			</div>
		</div>
	</form>
	<div class='table'>
    </div>
    <div class="ciy-tabbtn">
        <a class="btn btn-default" onclick="ciy_select_all('.table')">全选</a>
        <a class="btn btn-default" onclick="ciy_select_diff('.table')">反选</a>
        |
        <a class="btn btn-default" onclick="ciy_select_act('.table','addscores','确认加分？','',function(){ciy_alert('返回成功<br/>建议写页面数据修改代码')})">批量加分</a>
    </div>
    <div class="ciy-page"></div>
    <div class="clearfix"></div>

	<fieldset class="tips" style="min-width:35em;">
        <legend>使用说明</legend>
        <div>
            使用ES5模式独立写了一个类ciy_ctable，用于Ajax动态加载数据，力求简洁。<br/>
            支持页面+数据库排序，数字自适应排序，支持增量加载和分页加载，支持精确统计开关，减轻数据库压力。
            <ul>
                <li>建议建立一个全局变量，初始化类，实现类函数调用。</li>
                <li>注意示例的isinit变量，用来标识第一次加载完成，执行后续动作。</li>
                <li>页面条件搜索支持Ajax，请参考实现search函数，donecb自行实现选项卡选择。</li>
                <li>配合数据库字段备注信息，熟练后开发更快。</li>
                <li>fieldcb自定义函数，用于自定义显示，支持HTML，取代模板。</li>
                <li>pagecb自定义函数，用于自定义分页显示。</li>
                <li>ciy_ctable类只有一个函数getdata，请求分页数据。</li>
            </ul>
        </div>
    </fieldset>
    <form method="get">
    	<div class="form-group inline">
    		<label>每页</label>
    		<div>
    			<input type="text" name="pagecount" value="<?php echo $pagecount;?>" style="width: 3em;" />
    		</div>
    	</div>
    	<div class="form-group inline">
    		<label>排序方式</label>
    		<div>
        		<select name="ordertype">
            		<option value="1"<?php if($ordertype == 1)echo ' selected';?>>1 页面+数据库排序</option>
            		<option value="2"<?php if($ordertype == 2)echo ' selected';?>>2 仅数据库排序</option>
        		</select>
    		</div>
    	</div>
    	<div class="form-group inline">
    		<label>分页方式</label>
    		<div>
        		<select name="pagetype">
            		<option value="1"<?php if($pagetype == 1)echo ' selected';?>>1 分页加载</option>
            		<option value="2"<?php if($pagetype == 2)echo ' selected';?>>2 增量加载</option>
        		</select>
    		</div>
    	</div>
    	<div class="form-group inline">
    		<label>统计</label>
    		<div>
                <label class="formswitch"><input type="checkbox" name="needtotal"<?php if($needtotal)echo ' checked="checked"';?>/><y>精确</y><n>估算</n><i></i></label>
    		</div>
    	</div>
        <div class="form-group inline">
            <div>
            <button class="btn" type="submit">重新设置</button>
            </div>
        </div>
    </form>
</div>
<script src="/jscss/jquery-1.12.4.min.js"></script>
<script src="/jscss/ciy.js"></script>
<script src="/jscss/table.js"></script>
<script type="text/javascript">
'use strict';
var table;
$(function(){
	table = new ciy_ctable({
		dom:".table",
		url:'init',
		needtotal: <?php echo $needtotal;?>,//1统计，2不统计。不做精确统计可减轻数据库处理压力。
		pagecount: <?php echo $pagecount;?>,
		ordertype: <?php echo $ordertype;?>,//1页面+数据库排序，2仅数据库排序
		pagetype: <?php echo $pagetype;?>,//1分页加载，2增量加载
		pagedom:document.querySelector('.ciy-page'),
		donecb:function(json){
			if(!table.isinit){
				table.isinit = true;
			    ciy_table_adjust(".table");
			    ciy_select_init('.table');
			}
		    ciy_table_shcolumn(".table");
			var lidoms = document.querySelectorAll('li.active[data-liid]');
			for(var i=0;i<lidoms.length;i++)
				lidoms[i].className = '';
			var lidom = document.querySelector('li[data-liid="'+getint(table.post.liid)+'"]');
			if(lidom != null)
				lidom.className = 'active';
		},
		fieldcb:function(rowindex, key, content, field, data){
			if(key == 'scores')
				return content+'分';
			if(key == 'kg')
				return {tdprop:' style="background:#4dc31e;color:#ffffff;"',divprop:' style="text-align:center;"'};
		},
		//pagecb:function(thos,json){}   //自定义分页(请注意看源码，一旦启用，预设分页功能失效)
	});
	table.getdata(1);
});
function search(dom,act){
	var form = ciy_getform(dom);
	if(act == 'li')
		form.liid = dom.getAttribute('data-liid');
	else
		form.liid = document.querySelector('li.active[data-liid]').getAttribute('data-liid');
	Object.assign(table.post, form);//ES6
    table.getdata('search');
}
</script>
</body></html>