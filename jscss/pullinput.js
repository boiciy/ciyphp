/*
 * 开源作者：众产国际产业公会  http://ciy.cn/code
 * 版本：0.1.1
 */
(function(a) {
    a.fn.pull = function(D) {
        var inputdom = this;
        var styleElement = document.getElementById('style_pull');
        if (!styleElement) {
            styleElement = document.createElement('style');
            styleElement.type = 'text/css';
            styleElement.id = 'style_pull';
            styleElement.innerText = '.pullselect{position: absolute;z-index:100;display:none;border:1px solid #e4e4e4;}'
            	+ '.pullselect>li{line-height:1.3em;padding:0.5em 1em;background:#f6f6f6;transition: all .5s;cursor:pointer;}'
            	+ '.pullselect>li.selected,.pullselect>li:hover{background:#ffffff;}'
            	+ '.pullselect>div{font-size:0.6em;line-height:1.2em;height:1.2em;overflow: hidden;background: #f0f0f0;padding: 0 1em;cursor:pointer;}'
            	+ '.input-multi{display:inline-table;white-space:nowrap;position:relative;padding:0;}'
            	+ '.input-multi td>span{display:inline-block;white-space: nowrap;border:1px solid #cdcdcd;border-radius:5px;margin: 0.35em 0.2em;padding: 0.5em;line-height: 1em;font-size:0.8em;}'
            	+ '.input-multi td>span .delete{border-radius:50%;cursor:pointer;background:#999999;color:#ffffff;margin-left:0.5em;display:inline-block;width:1em;height:1em;text-align:center;}'
            	+ '.input-multi td>span .delete::after{content:"X";transform:scale(0.6);display:inline-block;}'
            	+ '.input-multi td>input{border:none;background:none;box-shadow:none;}'
            	+ '.input-multi td>input:focus{border:none;background:none;box-shadow:none;}';
            document.getElementsByTagName('head')[0].appendChild(styleElement);
        }
        D.multi = D.multi||1;//单选填1，多选填最多选择个数
        if(D.multi == 1){
        	if(getComputedStyle(inputdom[0].parentNode)['position'] == 'static')
        		inputdom[0].parentNode.style.position="relative";
        	D.resultfunc = D.resultfunc||function(id,txt){
        		inputdom.val(txt);
        	}
        }else{
        	D._multi_addnodefunc = function(id,text){
            	node=document.createElement("SPAN");
            	node.setAttribute('data-index',id);
            	node.innerHTML = text.trim()+"<i class='delete'></i><input type='hidden' name='"+inputdom[0].name+"_data' value='"+text+"'/><input type='hidden' name='"+inputdom[0].name+"_index' value='"+id+"'/>";
            	return node;
        	}
        	if(getComputedStyle(inputdom[0].parentNode)['position'] == 'static')
        		inputdom[0].parentNode.style.position="relative";
        	var _parent_selspan = inputdom[0].parentNode.previousSibling;
        	var val = inputdom[0].value.split(',');
        	$(this).closest('div').addClass('input-multi');
            for(var v in val){
            	var bnone = true;
                for(var i in D.catadata){
                	if(D.catadata[i].title == val[v]){//默认使用title部分判断。
                		bnone = false;
            			_parent_selspan.appendChild(D._multi_addnodefunc(i,val[v]));
        				break;
                	}
                }
                if(bnone)
        			_parent_selspan.appendChild(D._multi_addnodefunc(-1,val[v]));
            }
            inputdom[0].value = '';
        	D.resultfunc = D.resultfunc||function(id,txt){
        		var count = _parent_selspan.childNodes.length;
        		if(count >= D.multi)
        			return ciy_toast('您最多可以添加'+D.multi+'项');
        		for (var si=0; si<count; si++){
        			if(_parent_selspan.childNodes[si].textContent == txt)
        				return ciy_toast('该项已添加');
        		}
        		_parent_selspan.appendChild(D._multi_addnodefunc(id,txt));
        		inputdom.val("");
        		inputdom.focus();
        	}
        	$(_parent_selspan).on('click','i',function(){
        		this.parentNode.parentNode.removeChild(this.parentNode);
            });
    	}
        	
        D.maxlist = D.maxlist||8;//最多显示列数
        //如果有拼音，则转化拼音
        if(typeof(ciyext_pinyin_first) === "function"){
            for(var i in D.catadata)
            	D.catadata[i]._py = ciyext_pinyin_first(D.catadata[i].title);
		}
        D.lifunc = D.lifunc||function(val,cata,index){
            if(val == '' || cata.title.toUpperCase().indexOf(val) > -1 || (cata._py && cata._py.indexOf(val) > -1) || (cata.codeid && cata.codeid.toUpperCase().indexOf(val) > -1))
                return '<li data-index="'+index+'">'+cata.title+'</li>';
            return '';
        };
        D.valfunc = D.valfunc||function(li){
            return $(li).text();
        };
        D.ulext = D.ulext||"";
        var uldom = $('<ul class="pullselect'+D.ulext+'"></ul>');
        inputdom.after(uldom);
        inputdom.on('focus',function(){
            setTimeout(function(){
                inputdom.trigger("keyup");
                $(document).one("click", function(){
                    uldom.slideUp(50);
                });
            },300);
        }).on('click',function(){
            inputdom.trigger("keyup");
        }).on('keyup',function(e){
            if(uldom.is(":hidden") && uldom.children().length > 0){
            	if(inputdom.offset().top-document.documentElement.scrollTop < document.documentElement.clientHeight>>2)
            		uldom.css('top',inputdom.height()+'px').css('bottom','auto');
            	else
            		uldom.css('bottom',inputdom.parent().height()+'px').css('top','auto');
                uldom.slideDown(100);
            }
            if(e.keyCode == 38){//up
            	var sli = $('li.selected',uldom);
            	if(sli.length == 0)
            		sli = $('li:nth-last-child(1)',uldom);
            	else
            		sli = sli.prev();
            	if(sli.length == 0)
            		return;
            	$('li.selected',uldom).removeClass('selected');
            	sli.addClass('selected');
            	return;
            }
            if(e.keyCode == 40){//down
            	var sli = $('li.selected',uldom);
            	if(sli.length == 0)
            		sli = $('li:nth-child(1)',uldom);
            	else
            		sli = sli.next();
            	if(sli.length == 0)
            		return;
            	$('li.selected',uldom).removeClass('selected');
            	sli.addClass('selected');
            	return;
            }
            var val = inputdom.val();
            if(e.keyCode == 13)
            {
            	var sli = $('li.selected',uldom);
            	if(sli.length == 0)
            		sli = $('li:hover',uldom);
            	if(sli.length == 0)
            		sli = $('li',uldom);
            	if(sli.length == 0 || sli.length > 1){
            		if(val.length > 0)
            			D.resultfunc(-1,val);
                    return;
            	}
                D.resultfunc(sli[0].getAttribute('data-index'),D.valfunc(sli));
                uldom.slideUp(50);
                return;
            }
            var html = '';
            var cnt = 0;
            for(var i in D.catadata)
            {
            	if(D.multi > 1){
            		var bexist = false;
            		var count = _parent_selspan.childNodes.length;
            		for (var si=0; si<count; si++){
            			if(_parent_selspan.childNodes[si].getAttribute('data-index') == i){
            				bexist = true;
            				break;
            			}
            		}
            		if(bexist)
            			continue;
            	}
            		
                if(cnt>=D.maxlist)
                {
                    html += '<div>■ ■ ■</div>';
                    break;
                }
                var lih = D.lifunc(val.toUpperCase(),D.catadata[i],i);
                if(lih != '')
                    cnt++;
                html += lih;
            }
            html = $(html).on('click',function(){
                if(this.nodeName == 'DIV')
                {
                    D.maxlist+=5;
                    setTimeout(function(){
                        inputdom.trigger("keyup");
                    },110);
                }
                else
                {
                    D.resultfunc(this.getAttribute('data-index'),D.valfunc(this));
                    uldom.slideUp(50);
                }
            });
            uldom.attr('data-cnt',cnt);
            uldom.html(html);
            if(cnt == 1)
            	html.addClass('selected');
        });
    }
})($);