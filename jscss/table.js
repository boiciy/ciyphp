/*
 * 开源作者：众产国际产业公会  http://ciy.cn/code
 * 版本：0.1.0
 */
function ciy_ctable(opn) {
	opn.dom = opn.dom||'.table';
    var divdom = document.querySelector(opn.dom);
    if(divdom == null)
        return ciy_alert('未找到容器');
    var thos = this;
    var _tabdom = null;
    var _tabfield = {};
    this.gettotal = 0;//已加载条数
    this.cachecount = -1;//缓存总条数
    thos.pathname = opn.pathname||location.pathname;
    thos.pathname = '_tb_'+thos.pathname;
    var localset = {};
    try{ localset = JSON.parse(localStorage.getItem(thos.pathname)); }catch{}
    if(localset == null)localset = {};
    opn.pagecount = localset.pagecount || opn.pagecount || 20;
    opn.pagetype = localset.pagetype || opn.pagetype || 1;//1分页加载，2增量加载
    opn.btnpageset = (opn.btnpageset === undefined)?true:opn.btnpageset;//true 显示设置按钮
    opn.btncolumnset = (opn.btncolumnset === undefined)?true:opn.btncolumnset;//true 显示列选择按钮
    opn.needtotal = localset.needtotal || opn.needtotal || 1;//1开启统计，2关闭不统计 不做精确统计，可减轻数据库处理压力。
    opn.ordertype = localset.ordertype || opn.ordertype || 1;//1本页优先， 页面+数据库排序，2重载排序， 仅数据库排序
    this.url = opn.url;
    if(this.url === undefined)
        return this.showtip('数据源URL未设置');
    this.getdata = function(page){
       	this.post.pagecount = opn.pagecount;
       	if(page === 'search'){
       		thos.cachecount = -1;
       		page = 1;
       	}
    	this.post.pageno = getint(page);
    	if(this.post.pageno<1)
    		return;
    	if(thos.cachecount == -1 && opn.needtotal === 1)
    		this.post.nototal = false;
    	else
    		this.post.nototal = true;
    	this.post.field = getint(divdom.getAttribute('_field'));
    	callfunc(this.url, this.post, function(json){
    		var html;
    		if(json.count !== false)
    			thos.cachecount = json.count;
    		else if(thos.cachecount > -1)
    			json.count = thos.cachecount;
    		if(json.field){
    			divdom.setAttribute('_field',1);
    			html = '<table>'+(json.headtr||'')+'<tr>';
    			var index = 0;
    			for(var key in json.field){
    				if(!json.field[key].c)
    					continue;
    				var colname = json.field[key].c;
        			var extind = colname.indexOf(',');
        			if(extind === 0)
        				continue;
    				if(!json.field[key].c)
    					continue;
        			html += '<th';
        			if(json.field[key].width)
            			html += ' style="width:'+json.field[key].width+';"';
        			else if(json.field[key].prop)
            			html += ' '+json.field[key].prop;
        			html += ' data-index="' + (++index) + '"' + ' data-key="' + key + '"';
        			if(extind > -1)
            			html += ' data-ext="'+colname.substr(extind+1)+'">'+colname.substr(0, extind);
        			else
            			html += '>'+colname;
            		if(json.field[key].order){
              			html += '<i data-order="' + key + '" class="asc" title="升序排序"></i>';
              			html += '<i data-order="' + key + ' desc" class="desc" title="降序排序"></i>';
            		}
           			html += '</th>';
    			}
    			html += '</tr></table>';
    			divdom.innerHTML = html;
				if(divdom.onclick == null)
					divdom.onclick = function(dom){
						if(dom.target.tagName == 'I'){
							if(opn.ordertype == 2){
								if(dom.target.className.indexOf('active') == -1){
									thos.post.order = dom.target.getAttribute('data-order');
							    	thos.getdata(1);
								}
							}
							else{
								if(dom.target.className.indexOf('active') > -1){
									thos.post.order = dom.target.getAttribute('data-order');
							    	thos.getdata(1);
								}
								else{
						   		    var orderdom = divdom.querySelector('th>i.active');
						   		    if(orderdom !== null)
						   		    	$(orderdom).removeClass('active');
						    		$(dom.target).addClass('active');
									var trs = divdom.querySelectorAll('[data-id]');
									var arr=[];
									for(var i=0;i<trs.length;i++){
										arr.push(trs[i]);
						  			}
									var fod = (dom.target.getAttribute('data-order').indexOf(' desc') > 0);
									var findex = dom.target.parentNode.getAttribute('data-index');
									arr.sort(function(div1,div2){//Intl
										var val1 = div1.querySelector('td:nth-child('+findex+')').getAttribute('data');
										var val2 = div2.querySelector('td:nth-child('+findex+')').getAttribute('data');
										if (/^(-?\d*)(\.\d+)?$/.test(val1) && /^(-?\d*)(\.\d+)?$/.test(val2)){
											if(fod)
												return getfloat(val2)-getfloat(val1);
											else
												return getfloat(val1)-getfloat(val2);
										}
										else{
											if(fod)
												return val2.localeCompare(val1,"zh");
											else
												return val1.localeCompare(val2,"zh");
										}
							  		});
						  		  for(var i=0;i<arr.length;i++)
						  			_tabdom.append(arr[i]);
								}
							}
						}
    				};
    		}
    		if(_tabdom == null){
				_tabdom = $('table',divdom);
				var trs = _tabdom[0].querySelectorAll('th[data-key]');
				for(var i=0;i<trs.length;i++){
					var tf = {};
					tf.index = trs[i].getAttribute('data-index');
					tf.comment = trs[i].textContent.trim();
					if(trs[i].getAttribute('data-ext'))
						tf.ext = trs[i].getAttribute('data-ext');
					_tabfield[trs[i].getAttribute('data-key')] = tf;
				}
    		}
			if(opn.pagetype == 1 || json.pageno == 1){
				var trs = _tabdom[0].querySelectorAll('[data-id]');
				if(trs.length > 0){
					var parent = trs[0].parentElement;
					for(var i=0;i<trs.length;i++)
						parent.removeChild(trs[i]);
				}
				trs = _tabdom[0].querySelectorAll('tr[data-none]');
				if(trs.length > 0)
					trs[0].parentElement.removeChild(trs[0]);
			}
			html = '';
			for(var i=0;i<json.data.length;i++){
    			html += '<tr data-id='+json.data[i]['id']+'>';
				for(var key in _tabfield){
					var str = json.data[i][key];
					if(_tabfield[key].ext){
	    				if(_tabfield[key].ext.substr(0,4) == 'DATE'){
	    					str = ciy_formatdatetime(json.data[i][key],_tabfield[key].ext.substr(5));
	    				}else if(_tabfield[key].ext.substr(0,5) == 'INTIP'){
	    					var ip = parseInt(json.data[i][key]);
	    					str = (ip >>> 24)+"."+((ip << 8) >>> 24)+"."+((ip << 16) >>> 24)+"."+((ip << 24) >>> 24);
	    				}else if(_tabfield[key].ext.substr(0,3) == 'IMG'){
	    					if(json.data[i][key])
	    						str = '<img src="'+json.data[i][key]+'" style="max-width:100%;"/>';
	    				}else{
	    					var slts = _tabfield[key].ext.split(',');
	    					if(slts.length > 1){
	    						var datalen = json.data[i][key].length;
    							str = '['+json.data[i][key]+']';
	    						for(var si=0;si<slts.length;si++){
		    						if(slts[si].substr(0,datalen+1) == json.data[i][key]+"."){
		    							str = slts[si].substr(datalen+1);
		    							break;
		    						}
	    						}
	    					}
	    				}
	    			}
	    			var fdata = undefined;
	    			if(typeof(opn.fieldcb) == 'function')
	    				fdata = opn.fieldcb(i, key, str, _tabfield[key], json.data[i]);
    				if(typeof(fdata) == 'object'){//自定义td单元格
        				html += '<td data="' + json.data[i][key].replace(/\"/g,'&quot;') + '" ' + (fdata.tdprop||'') + '><div ' + (fdata.divprop||'') + '>' + (fdata.str||str) + '</div></td>';
    				}
    				else{
	    				if(typeof(fdata) == 'string')
	    					str = fdata;
        				html += '<td data="' + json.data[i][key].replace(/\"/g,'&quot;') + '"><div>' + str + '</div></td>';
    				}
				}
    			html += '</tr>';
			}
			if(json.data.length == 0 && json.pageno == 1)
    			html += '<tr data-none><td colspan="'+Object.keys(_tabfield).length+'" style="text-align:center;padding:0.5em;">暂无数据</td></tr>';

			_tabdom.append(html);
    		if(typeof(opn.donecb) == 'function')
    			opn.donecb(json);
    		if(json.pageno == 1)
    			thos.gettotal = 0;
    		thos.gettotal+=json.data.length;
    		if(typeof(opn.pagecb) == 'function')
    	        opn.pagecb(thos,json);
    		else if(typeof(opn.pagedom) == 'object'){
    			if(opn.pagetype == 1){
    			    var showpages = 3;
    				var pagemax;
    				if(json.count !== false)
    					pagemax = Math.ceil(json.count/json.pagecount);
    				else if(json.data.length < json.pagecount){
    					pagemax = json.pageno;
    				}
    				else{
    					pagemax = json.pageno + showpages;
    				}
    				if(opn.needtotal != 1 && json.data.length < json.pagecount)
    					json.count = thos.cachecount = (json.pageno-1)*json.pagecount+json.data.length;
    					
    				html = '<div class="ciy-page-txt">';
    				if(json.count !== false)
    					html += json.count + '条 ';
					html += json.pagecount + '条/页</div>';
    			    if (json.pageno > 1){
    					html += '<a data-page="1">&lt;&lt;</a>';
    					html += '<a data-page="'+(json.pageno-1)+'">&lt;</a>';
    			    }
    			    var spage = 1;
    			    if (json.pageno > showpages)
    			        spage = json.pageno - showpages;
    			    var epage = pagemax;
    			    if (json.pageno < pagemax - showpages)
    			        epage = json.pageno + showpages;
    			    for (var i = spage; i <= epage; i++) {
    			        if (i == json.pageno)
        					html += '<a class="current">'+i+'</a>';
    			        else
        					html += '<a data-page="'+i+'">'+i+'</a>';
    			    }
    			    if (json.pageno < pagemax){
    					html += '<a data-page="'+(json.pageno+1)+'">&gt;</a>';
    					html += '<a data-page="'+pagemax+'">&gt;&gt;</a>';
    			    }
    			    if(pagemax > showpages)
    					html += '<input class="n" type="text" name="topage" value="'+json.pageno+'" style="width:3em;min-height:2.2em;height:2.2em;text-align:center;margin:0 4px;"/><button class="btn btn-default" data-act="pagego">GO</button>';
    			}else{
        			if(json.data.length < json.pagecount)
        				html = '<div class="ciy-page-txt">加载完成，共 '+thos.gettotal+' 条</div>';
        			else{
        				html = '<div class="ciy-page-txt">已加载 ' + thos.gettotal + ((json.count === false)?'':' / ' + json.count) + ' 条</div><a data-act="continue">继续加载</a>';
        			}
    			}
				if(opn.btnpageset)
					html += '<button class="btn btn-default btn-svg" data-act="pageset"><svg viewBox="0 0 1024 1024" version="1.1"><path d="M947.375 804.68h-393.3c-9.517-47.678-51.075-83.632-100.822-83.632-49.815 0-91.372 35.955-100.912 83.632h-269.752c-11.363 0-20.587 9.36-20.587 20.925s9.225 20.925 20.587 20.925h269.73c9.54 47.678 51.097 83.632 100.912 83.632 49.77 0 91.327-35.955 100.822-83.632h393.3c11.363 0 20.587-9.36 20.587-20.925s-9.202-20.925-20.565-20.925M453.23 888.267c-34.133 0-61.807-28.035-61.807-62.663s27.675-62.708 61.807-62.708c34.087 0 61.74 28.080 61.74 62.708s-27.652 62.663-61.74 62.663M82.587 219.298h105.030c9.54 47.722 51.053 83.632 100.845 83.632s91.327-35.932 100.867-83.632h558.023c11.363 0 20.587-9.337 20.587-20.903 0-11.543-9.225-20.925-20.587-20.925h-558c-9.54-47.678-51.053-83.61-100.867-83.61s-91.305 35.955-100.845 83.632h-105.053c-11.363 0-20.587 9.382-20.587 20.925 0 11.565 9.225 20.88 20.587 20.88M288.485 135.687c34.11 0 61.762 28.103 61.762 62.73s-27.653 62.708-61.762 62.708c-34.133 0-61.785-28.080-61.785-62.708 0-34.627 27.675-62.73 61.785-62.73M947.375 491.098h-63.877c-9.54-47.722-51.053-83.655-100.845-83.655s-91.35 35.933-100.868 83.655h-599.197c-11.363 0-20.587 9.338-20.587 20.88 0 11.565 9.225 20.925 20.587 20.925h599.175c9.54 47.7 51.075 83.633 100.867 83.633s91.328-35.955 100.845-83.633h63.877c11.363 0 20.587-9.36 20.587-20.925 0.023-11.543-9.202-20.88-20.565-20.88M782.653 574.708c-34.155 0-61.762-28.080-61.762-62.73 0-34.627 27.63-62.73 61.762-62.73 34.132 0 61.762 28.102 61.762 62.73 0 34.65-27.652 62.73-61.762 62.73z"></path></svg></button>';
				if(opn.btncolumnset)
					html += '<button class="btn btn-default btn-svg" data-act="columnset"><svg viewBox="0 0 1024 1024" version="1.1"><path d="M76.44148719 295.10637875h871.11702562v667.85638687H76.44148719z" fill="#f2f2f2"></path><path d="M918.52127844 266.06914531a29.03723438 29.03723438 0 1 0 0 58.07446782v85.31139468H105.47872156V324.14361313h578.94437531c16.11566531 0 29.03723438-13.00868063 29.03723438-29.03723438s-12.92156906-29.03723438-29.03723438-29.03723344H105.47872156c-32.02806938 0-58.07446875 26.04639938-58.07446875 58.07446781v609.78191813c0 32.05710656 26.04639938 58.07446875 58.07446875 58.07446875h813.04255688a58.07446875 58.07446875 0 0 0 58.07446875-58.07446875V324.14361313c0-32.02806938-26.01736219-58.07446875-58.07446875-58.07446782zM395.85106345 757.9018175v-116.14893656h232.29787312v116.14893656h-232.29787312z m232.29787312 58.07446874V933.92553125h-232.29787312v-117.97828219h232.29787312v0.0290372z m-522.67021501-174.2234053h232.29787312v116.14893656h-232.29787312v-116.14893656z m522.67021501-58.07446875h-232.29787312v-111.90950063c0-1.50993657-0.63881906-2.78757469-0.84208034-4.23943593h233.98203378c-0.2322975 1.42282406-0.84207937 2.72950031-0.84208032 4.21039875v111.93853781z m58.07446873 58.07446875h232.29787314v116.14893656h-232.29787314v-116.14893656z m232.29787314-58.07446875h-232.29787314v-111.90950063c0-1.50993657-0.60978187-2.78757469-0.8420803-4.23943593H918.52127844v116.14893656z m-579.90260344-116.14893656c-0.20326031 1.42282406-0.84207937 2.72950031-0.84208032 4.21039875v111.90950062h-232.29787312v-116.14893655l233.13995344 0.02903717z m-233.13995344 348.44681061h232.29787312V933.92553125h-232.29787312v-117.94924499zM686.22340532 933.92553125v-117.97828219h232.29787312V933.92553125h-232.29787312z" fill="#000000" p-id="4703"></path><path d="M780.01367188 284.79816125a28.95012281 28.95012281 0 0 0 41.05864874 0l86.58903282-86.64710719c11.41163344-11.35355812 11.41163344-29.70509063 0-41.05864875s-29.64701625-11.35355812-41.05864969 0l-36.90632437 36.87728719v-132.93245812a29.03723438 29.03723438 0 1 0-58.07446875 0v133.39705406l-36.87728718-36.87728813a29.00819719 29.00819719 0 1 0-41.05864876 41.0586497l86.32769718 86.18251124z" fill="#000000"></path></svg></button>';
				opn.pagedom.innerHTML = html;
				if(opn.pagedom.onclick == null)
    				opn.pagedom.onclick = function(dom){
					dom = dom.target;
					while(dom && dom.tagName != 'A' && dom.tagName != 'BUTTON')
						dom = dom.parentNode;
					if(!dom)
						return;
					var act = dom.getAttribute('data-act');
						if(act == 'pageset'){
					        var html = '';
				        	html += '<div class="form-group"><label style="width:5em;">每页条数&nbsp;</label><div><input type="text" name="pagecount" value="'+opn.pagecount+'" style="width:4em;text-align:center;"/></div></div>';
				        	html += '<div class="form-group"><label style="width:5em;">加载方式&nbsp;</label><div><label class="formswitch"><input type="checkbox" name="pagetype"'+((opn.pagetype === 1)?' checked="checked"':'')+'><y>分页</y><n>增量</n><i></i></label></div></div>';
				        	html += '<div class="form-group"><label style="width:5em;">统计条数&nbsp;</label><div><label class="formswitch"><input type="checkbox" name="needtotal"'+((opn.needtotal === 1)?' checked="checked"':'')+'><y>开启</y><n>关闭</n><i></i></label></div></div>';
				        	html += '<div class="form-group"><label style="width:5em;">排序方式&nbsp;</label><div><label class="formswitch char4"><input type="checkbox" name="ordertype"'+((opn.ordertype === 1)?' checked="checked"':'')+'><y>本页优先</y><n>重载排序</n><i></i></label></div></div>';
					        ciy_alert(html, function(btn,inputs){
					        	inputs.pagetype = inputs.pagetype?1:2;
					        	inputs.ordertype = inputs.ordertype?1:2;
					        	inputs.needtotal = inputs.needtotal?1:2;
					        	opn.pagecount = getint(inputs.pagecount);
					        	opn.pagetype = inputs.pagetype;
					        	opn.ordertype = inputs.ordertype;
					        	opn.needtotal = inputs.needtotal;
	    						thos.getdata(1);
					        	delete inputs.pagetype_name;
					        	delete inputs.ordertype_name;
					        	delete inputs.needtotal_name;
					            localStorage.setItem(thos.pathname, JSON.stringify(inputs));
					   	    },{btns:['设置'],title:'设置表格'});
						}
						else if(act == 'columnset'){
							ciy_table_shcolumn(opn.dom,'set');
						}
						else if(act == 'continue'){
    						thos.getdata(++json.pageno);
						}
						else if(opn.pagetype == 1){
	    					var pno = 0;
	    					if(dom.tagName == 'BUTTON'){
	    						pno = this.querySelector('input[name="topage"]').value;
	    					}
	    					else if(dom.tagName == 'A'){
	    						pno = dom.getAttribute('data-page');
	    					}
	    					pno = getint(pno);
	    					if(pno > 0)
	    						thos.getdata(pno);
						}
    				};
    		}
   		    var orderdom = divdom.querySelector('th>i.active');
   		    if(orderdom !== null)
   		    	$(orderdom).removeClass('active');
   		    orderdom = divdom.querySelector('i[data-order="'+json.order+'"]');
   		    if(orderdom !== null)
	    		$(orderdom).addClass('active');
    	});
    }
    //初始化执行
    this.post = {};
}
function ciy_table_shcolumn(domname,cmd,pathname){
    var dom = document.querySelector(domname);
    if(dom == null)
        return;
    pathname = pathname||location.pathname;
    pathname = '_tc_'+pathname;
    var itext = localStorage.getItem(pathname);
    var trs = [];
    if(dom.getAttribute('_adjust') != 1){
        var htrs = dom.querySelectorAll("tr");
        for(var i=0;i<htrs.length;i++){
        	if(htrs[i].querySelectorAll("th").length > 0)
        		trs = htrs[i].querySelectorAll("th");
        }
        dom.setAttribute('_adjust',1);
        for (var i = 0; i < trs.length; i++){
          trs[i].setAttribute('_canadjust',1);
        }
    }
    else
    	trs = dom.querySelectorAll("th[_canadjust='1']");
    if(cmd == "set"){
        var html = '<div>请选择需要显示/隐藏的列</div><div style="max-height:'+(window.outerHeight-300)+'px;">';
        var selths = (itext == null)?[]:itext.split(',');
        for(var i=0;i<trs.length;i++){
        	html += '<div class="form-group"><div><label class="formi icon"><input name="_sh_'+i+'" type="checkbox" value="true"' + ((selths[i] === undefined || selths[i] === '1')?' checked="true"':'') + '/><i></i>'+trs[i].textContent+'</label></div></div>';
        }
        html += '</div>';
        
        ciy_alert(html, function(btn,inputs){
        	var shs = [];
            for (var i = 0; i < trs.length; i++){
            	if(inputs['_sh_' + i] == 'true')
            		shs.push('1');
            	else
            		shs.push('0');
            }
            localStorage.setItem(pathname, shs.join(','));
    	    _shcolumn(shs);
   	    },{title:'显示隐藏列操作'});
   	}else{
        if(itext == null)
        	return;
        _shcolumn(itext.split(','));
    }
	//读取列头，显示列头列表，复选确认。确认后保存
	function _shcolumn(shs){
    	var trs = dom.querySelectorAll("th[_canadjust='1']");
        for(var i=0;i<trs.length;i++){
        	if(shs[i] === '1')
        		trs[i].style.display = "table-cell";
        	else
        		trs[i].style.display = "none";
        }
    	var dats = dom.querySelectorAll("tr");
	    for(var x=0;x<dats.length;x++){
	    	var tds = dats[x].querySelectorAll("td");
	    	if(tds.length < 2)
	    		continue;
	        for(var i=0;i<tds.length;i++){
	        	if(shs[i] === '1')
	        		tds[i].style.display = "table-cell";
	        	else
	        		tds[i].style.display = "none";
	        }
	    }
	}
}