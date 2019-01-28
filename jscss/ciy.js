'use strict';
function uperr(err,msg){
    console.error(err);
    var postparam = {};
    postparam.tit = 'admin';
    postparam.err = err.message;
    postparam.stack = err.stack;
    postparam.msg = msg;
    ciy_ajax({
        url:"ajax.php?json=true&func=uperr",
        data:postparam
    });
}
function ciy_fastfunc(confirmmsg,func,postparam,succfunc)
{
    if(!confirmmsg)
        return cfunc();
    ciy_alert(confirmmsg,function(btn){
        if(btn == "继续")
            cfunc();
    },{btns:["继续","取消"]});
    function cfunc()
    {
        callfunc(func,postparam,function(json){
            if(succfunc == 'reload')
            {
                ciy_toast('操作成功',{done:function(){
                    location.reload();
                }});
            }
            else if(typeof(succfunc) == 'function')
                succfunc(json);
            else
                ciy_toast('操作成功');
        });
    }
}
function callfunc(funcname, post, successfunc, opt)//opt  fail,complete,headers,timeout
{
    opt = opt || {};
    opt.murl = opt.murl || "";
    opt.url = opt.murl + "?json=true&func="+funcname;
    opt.data = post;
    opt.success = function(data,xhr){
        try{
        var json = JSON.parse(data);
        }catch(err){
            uperr(err,data);
        }
        if(json === undefined)
        {
            ciy_loadclose('fail');
            if(typeof opt.fail === 'function')
                opt.fail(data,xhr);
            else
                ciy_alert(data);
        }
        else if(json.result)
        {
            ciy_loadclose('succ');
            successfunc(json,xhr);
        }
        else
        {
            ciy_loadclose('fail');
            if(typeof(opt.fail) === 'function')
                opt.fail(json.msg,xhr);
            else
                ciy_alert(json.msg);
        }
    }
    ciy_loading();
    ciy_ajax(opt);
}
function ciy_ajax(opt)//IE8 OK
{
    opt = opt || {};
    var header = opt.header || {};
    if(!header['Content-Type'])//header:{'Content-Type':'application/x-www-form-urlencoded'},
        header['Content-Type'] = 'application/json';
    var url = opt.url || '';
    var timeout = opt.timeout || 10000;
    if(timeout < 1000)
        timeout = 3000;
    var method = opt.method || 'POST';
    method = method.toUpperCase();
    if(method == 'GET' && typeof(opt.data) == 'object')
    {
        var datastr = "";
        for (var p in opt.data)
            datastr += "&" + encodeURIComponent(p) + "=" + encodeURIComponent(opt.data[p]);
        if(url.indexOf('?') == -1)
            url += '?' + datastr.substr(1);
        else
            url += datastr;
    }
    var request = new XMLHttpRequest();
    request.open(method,url,true);
    if(typeof(header) == 'object')
    {
        for (var i in header) {
            if (header[i] !== undefined)
                request.setRequestHeader(i, "" + header[i]);
        }
    }
    var sendstr = null;
    if(method == 'POST' || method == 'PUT')
    {
        if(typeof(opt.data) == 'object')
        {
            if(header['Content-Type'] == 'application/x-www-form-urlencoded')
            {
                var sendstr = "";
                for (var p in opt.data)
                    sendstr += "&" + encodeURIComponent(p) + "=" + encodeURIComponent(opt.data[p]);
                sendstr = sendstr.substr(1);
            }
            else
                sendstr = JSON.stringify(opt.data);
        }
        else if(opt.data)
        {
            if(header['Content-Type'] == 'application/json')
            {
                var json = {};
                var ds = opt.data.split('&');
                for(var d in ds)
                {
                    var ind = ds[d].indexOf('=');
                    if(ind > 0)
                        json[ds[d].substr(0,ind)] = ds[d].substr(ind+1);
                }
                sendstr = JSON.stringify(json);
            }
            else
                sendstr = "" + opt.data;
        }
    }
    request.send(sendstr);
    request.onreadystatechange = function() {
        if(this.readyState === 4){
            clearTimeout(aborttime);
            if(this.status >= 200 && this.status < 400){
                if(typeof opt.success === 'function')
                    opt.success(this.responseText,this);
            }else{
                if(typeof opt.fail === 'function')
                {
                    var errtxt = '';
                    if(this.status == 200)
                        errtxt = 'Server Error: '+this.responseText;
                    else if(this.status == 404)
                        errtxt ="404 Not Found: "+this.responseURL;
                    else if(this.status == 0)
                        errtxt ="Server No Response.";
                    else
                        errtxt = 'ErrCode:'+this.status+","+this.statusText;
                    opt.fail(errtxt,this);
                }
            }
            if(typeof opt.complete === 'function')
                opt.complete(this);
        }
    }
    var aborttime = window.setTimeout( function() {request.abort("timeout");}, timeout);
}
function ciy_getform(dom,parentTag)
{
    while(true)
    {
        if(parentTag && dom.tagName == parentTag)
            break;
        if(dom.tagName == 'BODY' || dom.tagName == 'FORM' || dom == null)
            break;
        dom = dom.parentNode;
    }
    var retdata = {};
    var els = dom.querySelectorAll("input,textarea,select");
    for (var i = 0; i < els.length; i++)
    {
        if(els[i].tagName == 'SELECT')
        {
            retdata[els[i].name] = els[i].value;
            if(els[i].options.selectedIndex == -1)
                retdata[els[i].name+"_name"] = '';
            else
                retdata[els[i].name+"_name"] = els[i].options[els[i].options.selectedIndex].text;
        }
        else if(els[i].getAttribute('type') == 'radio')
        {
            if(els[i].checked)
            {
                retdata[els[i].name] = els[i].value;
                retdata[els[i].name+"_name"] = els[i].parentNode.textContent;
            }
            else
            {
                if(retdata[els[i].name] === undefined)
                {
                    retdata[els[i].name] = '';
                    retdata[els[i].name+"_name"] = '';
                }
            }
        }
        else if(els[i].getAttribute('type') == 'checkbox')
        {
            if(els[i].nextElementSibling != null && els[i].nextElementSibling.tagName == 'Y')
            {
                retdata[els[i].name] = els[i].checked;
                if(els[i].checked)
                    retdata[els[i].name+"_name"] = els[i].nextElementSibling.textContent;
                else
                    retdata[els[i].name+"_name"] = els[i].nextElementSibling.nextElementSibling.textContent;
            }
            else if(els[i].checked)
            {
                if(retdata[els[i].name] === undefined)
                {
                    retdata[els[i].name] = els[i].value;
                    retdata[els[i].name+"_name"] = els[i].parentNode.textContent;
                }
                else
                {
                    retdata[els[i].name] += "," + els[i].value;
                    retdata[els[i].name+"_name"] += "," + els[i].parentNode.textContent;
                }
            }
        }
        else
        {
            retdata[els[i].name] = els[i].value;
        }
    }
    return retdata;
}
function ciy_urlparam(url){
    var obj = {};
    url = url||document.location.search;
    if(url[0] != '?')
        return obj;
    var pairs = url.substring(1).split('&');
    for(var p in pairs)
    {
        var ind = pairs[p].indexOf('=');
        if(ind > -1)
            obj[decodeURIComponent(pairs[p].substring(0,ind))] = decodeURIComponent(pairs[p].substring(ind + 1));
        else
            obj[pairs[p]] = '';
    }
    return obj; 
}
function ciy_layout(act){
    var lmenuact = act;
    $('#id_headertabs_ul').on("click","i",function(ev){
        var domtab = $(this).parents('li');
        if(domtab.hasClass("active"))
        {
            var domltab = domtab.next();
            if(domltab.length == 0)
                domltab = domtab.prev();
            ciy_ifropen('',domltab.attr('data-tit'));
        }
        ciy_ifrclose(domtab);
        var e = ev || event;
        e.stopPropagation();
    });
    $('#id_headertabs_ul').on("click","li",function(ev){
        $("#id_headertabs_ul>li").removeClass('active');
        $("#id_ifms>iframe").removeClass('active');
        $(this).addClass('active');
        $("#id_ifms>iframe[data-tit='"+$(this).attr('data-tit')+"']").addClass('active');
    });
    $('.ciy-menu-nav').on("click","li",function(ev){
        if($('#id_body').hasClass("ciy-menu-shrink") && window.innerWidth > 992)
        {
            $('.ciy-menu-nav ._ulshow').slideUp(400);//收缩的情况下，隐藏所有菜单展开
            $('.ciy-menu-nav .show').removeClass("show");//同上
            $('#id_body').removeClass("ciy-menu-shrink");
        }
        if($(this).hasClass("show"))
        {
            $(this).children('ul').slideUp(400);
            $(this).removeClass("show");
        }
        else
        {
            //关闭上次打开菜单
            if(lmenuact != 'close')
            {
                if($(this).parents('ul').length == 1)
                {
                    $('.ciy-menu-nav ._ulshow').removeClass("_ulshow").slideUp(400);
                    $('.ciy-menu-nav .show').removeClass("show");
                    $(this).children('ul').addClass("_ulshow");
                }
            }
            //关闭上次打开菜单 end
            $(this).children('ul').slideDown(400);
            $(this).addClass("show");
        }
        var href = $(this).children('a').attr('data-href');
        if(href !== undefined)
        {
            var txt = $(this).children('a').attr('data-title');
            if(txt === undefined)
            {
                if($(this).children('a').children('cite').length==1)
                    txt = $(this).children('a').children('cite').text();
                else
                    txt = $(this).children('a').text();
            }
            ciy_ifropen(href,txt);
            $('.ciy-menu-nav li').removeClass("active");
            $(this).addClass("active");
            if(window.innerWidth < 992)
                ciy_shrink();
        }
        var e = ev || event;
        e.stopPropagation();
    });
    $('.ciy-menu-nav').on("mousemove",function(ev){
        if($('#id_body').hasClass("ciy-menu-shrink"))
        {
            var el = ev.target;
            if(ev.target.tagName != 'LI')
                el = $(ev.target).parents('li');
            var txt = $(el).find('cite').text();
            $(el).find('i').attr('title',txt);
        }
        else
        {
            var top = $(".ciy-side-scroll").scrollTop();
            if(ev.target.tagName != 'LI')
                top += $(ev.target).parents('li').offset().top;
            else
                top += $(ev.target).offset().top;
            $(".ciy-nav-bar").css('top',top);
        }
    });
}
function ciy_layoutclose(act){
    if(act == 'me')
    {
        var domtab = $('#id_headertabs_ul>li.active');
        if(domtab.length == 0)
            alert('domtab出现错误');
        if(domtab.find('i').length == 0)
            return;
        
        var domltab = domtab.next();
        if(domltab.length == 0)
            domltab = domtab.prev();
        ciy_ifropen('',domltab.attr('data-tit'));
        ciy_ifrclose(domtab);
    }
    if(act == 'oth')
    {
        var domtabs = $('#id_headertabs_ul>li');
        domtabs.each(function(index,item){
            item = $(item);
            if(item.find('i').length == 0)
                return;
            if(item.hasClass('active'))
                return;
            ciy_ifrclose(item);
        });
    }
    if(act == 'all')
    {
        var domtabs = $('#id_headertabs_ul>li');
        domtabs.each(function(index,item){
            item = $(item);
            if(item.find('i').length == 0)
                return;
            ciy_ifrclose(item);
        });
        var domltab = $('#id_headertabs_ul>li:first');
        ciy_ifropen('',domltab.attr('data-tit'));
    }
}
function ciy_ifrclose(domtab)
{
    if(window.parent != window)
    {
        //关闭自己
        var frames = window.parent.document.getElementsByTagName("iframe");
        for (var i = 0; i < frames.length; i++) {
            if (frames[i].contentWindow == window)
            {
                window.parent.ciy_ifrclose(frames[i].getAttribute('data-tit'));
                if(typeof(frames[i].closecb) == 'function')
                    frames[i].closecb();
                return;
            }
        }
        return;
    }
    if(typeof(domtab) == 'string')
    {
        domtab = $("#id_headertabs_ul>li[data-tit='"+domtab+"']");
        if(domtab.length == 0)
            return;
        var domltab = domtab.next();
        if(domltab.length == 0)
            domltab = domtab.prev();
        ciy_ifropen('',domltab.attr('data-tit'));
    }
    var txt = domtab.attr('data-tit');
    var domifm = $("#id_ifms>iframe[data-tit='"+txt+"']");
    domifm[0].src = 'about:blank';
    domifm[0].contentWindow.close();
    setTimeout(function(){
        domifm.remove();
    },100);
    domtab.remove();
}
function ciy_ifropen(url,txt,ableclose,closecb){
    if(window.parent != window)
        return window.parent.ciy_ifropen(url,txt,ableclose,closecb);
    var elifms = document.getElementById("id_ifms");
    var eltabs = document.getElementById("id_headertabs_ul");
    var elifm = elifms.querySelector("[data-tit='"+txt+"']");
    if(elifm == null)
    {
        if(url == "")
            return;
        $("#id_headertabs_ul>li").removeClass('active');
        $("#id_ifms>iframe").removeClass('active');
        $(elifms).append("<iframe class='active' src='"+url+"' data-tit='"+txt+"' frameborder='0'></iframe>");
        if(ableclose)
            ableclose = '';
        else
            ableclose = '<i><svg t="1527035202927" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" p-id="1146" xmlns:xlink="http://www.w3.org/1999/xlink"><defs></defs><path d="M512 0C229.216 0 0 229.216 0 512s229.216 512 512 512 512-229.216 512-512S794.784 0 512 0zM723.2 642.752c22.112 22.112 22.112 58.336 0 80.448s-58.336 22.112-80.448 0L512 592.448 381.248 723.2c-22.112 22.112-58.336 22.112-80.448 0s-22.112-58.336 0-80.448L431.552 512 300.8 381.248c-22.112-22.112-22.112-58.336 0-80.448s58.336-22.112 80.448 0L512 431.552 642.752 300.8c22.112-22.112 58.336-22.112 80.448 0s22.112 58.336 0 80.448L592.448 512 723.2 642.752z" p-id="1147"></path></svg></i>';
        $(eltabs).append("<li class='active' data-tit='"+txt+"' title='"+url+"'><a>"+txt+"</a>"+ableclose+"</li>");
        //滚动到最后
        var div = document.getElementById('id_headertabs');
        div.scrollLeft = div.clientWidth+$(div).width();
        var domifm = $("#id_ifms>iframe[data-tit='"+txt+"']");
        domifm[0].closecb = closecb;
    }
    else
    {//激活
        var eltab = eltabs.querySelector("[data-tit='"+txt+"']");
        if(eltab == null)
            alert('eltab出现错误');
        $("#id_headertabs_ul>li").removeClass('active');
        $("#id_ifms>iframe").removeClass('active');
        $(eltab).addClass('active');
        $(elifm).addClass('active');
        if(url != '')
            elifm.src = url;
        //自动滚动到能看到选中
        var div = document.getElementById('id_headertabs');
        var vsta = $(eltab).offset().left+div.scrollLeft-$(div).offset().left;
        if(div.scrollLeft>vsta)
            div.scrollLeft=vsta;
        else
        {
            var vend = vsta-$(div).width()+$(eltab).width()+$(eltab).width();
            if(div.scrollLeft<vend)
                div.scrollLeft=vend;
        }
        //自动滚动到能看到选中 end
    }
}
function ciy_shrink(){
    $('#id_body').toggleClass("ciy-menu-shrink");
}
function ciy_headertabscroll(act){
    var div = document.getElementById('id_headertabs');
    var width = $(div).width()*2/3;
    var sl = div.scrollLeft;
    if(act == 'left')
        sl -=width;
    else
        sl +=width;
    div.scrollLeft = sl;
}
function ciy_refresh(){
    if(window.parent != window)
        return window.parent.ciy_refresh();
    var domifm = $("#id_ifms>iframe.active");
    if(domifm.length == 1)
        domifm[0].contentWindow.location.reload();//domifm.attr('src', domifm.attr('src'));
}
function ciy_fix(){
    if (navigator.userAgent.match(/iPad|iPhone/i))
       document.body.style.width = window.screen.availWidth+'px';/*解决IOS被撑大问题*/
}
function ciy_repre(){
    var els = document.getElementsByTagName("pre");
    for (var i = 0; i < els.length; i++)
        els[i].innerHTML = els[i].innerHTML.replace(/&(?!#?[a-zA-Z0-9]+;)/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/'/g, '&#39;').replace(/"/g, '&quot;');
}

function ciy_table_adjust(domname,pathname){
    var dom = document.querySelector(domname);
    if(dom == null)
        return;
    pathname = pathname||location.pathname;
    pathname = 'table_'+pathname;
    var style = document.createElement('style');
    style.type = 'text/css';
    dom.appendChild(style);
    var itext = localStorage.getItem(pathname);
    var trs = dom.querySelectorAll("th");
    if(itext == null)
    {
        itext = '';
        for(var i=0;i<trs.length;i++)
        {
            if(trs[i].style.width != '')
                itext+=domname+" tr > td:nth-child("+(i+1)+") > div { width: "+trs[i].style.width+" }";
        }
    }
    else
    {
        var itms = itext.split(',');
        itext = '';
        for(var i=0;i<trs.length;i++)
        {
            if(itms[i] && itms[i] != '')
                itext+=domname+" tr > td:nth-child("+(i+1)+") > div { width: "+itms[i]+" }";
        }
    }
    for (var i = 0; i < trs.length; i++)
      trs[i].style.width = null;
    style.innerText=itext;
    if(dom.scrollWidth <= dom.clientWidth)
        dom.style.borderRight = null;
    else
        dom.style.borderRight = '1px solid #cccccc';
    if('ontouchend' in window)
    {
        var longtime = null;
        dom.addEventListener("touchstart",function(ev){
            if(ev.target.tagName != 'TH')
                return;
            longtime = setTimeout(function(){
                var index = 0;
                for (var i = 0; i < trs.length; i++)
                {
                    if(trs[i] == ev.target)
                    {
                        index = i;
                        break;
                    }
                }
                var sheet = style.sheet || style.styleSheet || {};
                var rules = sheet.cssRules || sheet.rules;
                var opstyle = null;
                for (var i = 0; i < rules.length; i++)
                {
                    var item = rules[i];
                    if(item.selectorText !== (domname+" tr > td:nth-child("+(index+1)+") > div"))
                        continue;
                    opstyle = item;
                    break;
                }
                if(opstyle == null)
                {
                    var i = 0;
                    if("insertRule" in sheet)
                        i = sheet.insertRule(domname+" tr > td:nth-child("+(index+1)+") > div{}", 0);
                    else if("addRule" in sheet)
                        i = sheet.addRule(domname+" tr > td:nth-child("+(index+1)+") > div", "");
                    opstyle = rules[i];
                }
                if(ev.touches[0].clientX-ev.target.offsetLeft+dom.scrollLeft > (ev.target.clientWidth>>1))
                    opstyle.style.width = parseInt(ev.target.clientWidth*1.2)+"px";
                else
                    opstyle.style.width = parseInt(ev.target.clientWidth*0.8)+"px";
                
                var wids = [];
                for (var i = 0; i < rules.length; i++)
                {
                    var tmpstr = rules[i].selectorText.substr(rules[i].selectorText.indexOf('nth-child(')+10);
                    wids[parseInt(tmpstr.substr(0,tmpstr.indexOf(')')))-1] = rules[i].style.width;
                }
                var csstxt = '';
                for (var i = 0; i < trs.length; i++)
                {
                    csstxt += ',';
                    if(wids[i])
                        csstxt += wids[i];
                }
                localStorage.setItem(pathname, csstxt.substr(1));
                
            },1000);
        });
        dom.addEventListener("touchmove",function(ev){
            clearTimeout(longtime);
        });
        dom.addEventListener("touchend",function(ev){
            clearTimeout(longtime);
        });
    }
    else
    {
        var dodrag = null;
        document.addEventListener("mousedown",function(ev){
            if(dodrag == null)
                return;
            if(ev.target.tagName != 'TH')
                return;
            var index = 0;
            for (var i = 0; i < trs.length; i++)
            {
                if(trs[i] == dodrag)
                {
                    index = i;
                    break;
                }
            }
            var sheet = style.sheet || style.styleSheet || {};
            var rules = sheet.cssRules || sheet.rules;
            dodrag.opstyle = null;
            for (var i = 0; i < rules.length; i++)
            {
                var item = rules[i];
                if(item.selectorText !== (domname+" tr > td:nth-child("+(index+1)+") > div"))
                    continue;
                dodrag.opstyle = item;
                break;
            }
            if(dodrag.opstyle == null)
            {
                var i = 0;
                if("insertRule" in sheet)
                    i = sheet.insertRule(domname+" tr > td:nth-child("+(index+1)+") > div{}", 0);
                else if("addRule" in sheet)
                    i = sheet.addRule(domname+" tr > td:nth-child("+(index+1)+") > div", "");
                dodrag.opstyle = rules[i];
            }
            dodrag.mouseDown = true;
        });
        document.addEventListener("mouseup",function(ev){
            if(dodrag == null)
                return;
            if(ev.target.tagName != 'TH')
                return;
            dodrag.mouseDown = false;
            dodrag = null;
            var sheet = style.sheet || style.styleSheet || {};
            var rules = sheet.cssRules || sheet.rules;
            var wids = [];
            for (var i = 0; i < rules.length; i++)
            {
                var tmpstr = rules[i].selectorText.substr(rules[i].selectorText.indexOf('nth-child(')+10);
                wids[parseInt(tmpstr.substr(0,tmpstr.indexOf(')')))-1] = rules[i].style.width;
            }
            var csstxt = '';
            for (var i = 0; i < trs.length; i++)
            {
                csstxt += ',';
                if(wids[i])
                    csstxt += wids[i];
            }
            localStorage.setItem(pathname, csstxt.substr(1));
        });
        document.addEventListener("mousemove",function(ev){
            if(dodrag != null && dodrag.mouseDown)
            {
                var e = ev||event;
                dodrag.opstyle.style.width = (e.clientX-dodrag.getBoundingClientRect().left) + "px";
                if(dom.scrollWidth <= dom.clientWidth)
                    dom.style.borderRight = null;
                else
                    dom.style.borderRight = '1px solid #cccccc';
                return;
            }
            if(ev.target.tagName == 'TH')
            {
                if(ev.target.clientWidth-ev.offsetX<5)
                {
                    dodrag = ev.target;
                    ev.target.style.cursor='col-resize';
                }
                else
                {
                    if(dodrag != null)
                        dodrag.style.cursor=null;
                    dodrag = null;
                    if(ev.offsetX<5)
                    {
                        dodrag = ev.target.previousElementSibling;
                        if(dodrag != null)
                            ev.target.style.cursor='col-resize';
                    }
                    else
                    {
                        ev.target.style.cursor=null;
                    }
                }
            }
        });
    }
}
function ciy_table_tree(domname)
{
    $(domname).on("click",'div[data-treeid]',function(ev){
        $(ev.currentTarget).toggleClass('ciy-tree-spread');
        var id = $(ev.currentTarget).attr('data-treeid');
        var min=9999,max=0;
        $('tr[data-upid='+id+']').each(function(e){
            var index = $(domname+" tr").index(this);
            if(min>index)
                min = index;
            if(max<index)
                max = index;
        });
        var open = false;
        if($(ev.currentTarget).hasClass('ciy-tree-spread'))
            open = true;
        for(var i=min;i<=max;i++)
        {
            if(open)
            {
                $(domname+" tr").eq(i).show();
                $(domname+" tr").eq(i).find('[data-treeid]').addClass('ciy-tree-spread');
            }
            else
            {
                $(domname+" tr").eq(i).hide();
                $(domname+" tr").eq(i).find('[data-treeid]').removeClass('ciy-tree-spread');
            }
        }
    });
}
function ciy_select_init(dom)
{
    $(dom).on("click",'tr[data-id]',function(ev){
        $(ev.currentTarget).toggleClass('selected');
    });
}
function ciy_select_all(dom)
{
    $('tr[data-id]',dom).each(function () {
        $(this).addClass("selected");
    });
}
function ciy_select_diff(dom)
{
    $('tr[data-id]',dom).each(function () {
        $(this).toggleClass("selected");
    });
}
function ciy_select_act(dom,act,confirmmsg,postparam,successfunc)
{
    if(typeof(postparam) != 'object')
        postparam = {};
    postparam.act = act;
    var array = new Array();
    $('tr[data-id]',dom).each(function () {
        if ($(this).hasClass("selected"))
            array.push($(this).attr("data-id"));
    })
    postparam.ids = array.join(",");
    if(postparam.ids == "")
        return ciy_toast("请至少选择一条信息");
    if(confirmmsg !== undefined)
    {
        ciy_alert(confirmmsg,function(btn){
            if(btn == "继续")
            {
                callfunc("setact",postparam,function(json){
                    if(typeof(successfunc) === "function")
                        successfunc();
                    else
                        location.reload();
                });
            }
        },{btns:["继续","取消"]});
    }
    else
    {
        callfunc("setact",postparam,function(json){
            if(typeof(successfunc) === "function")
                successfunc();
            else
                location.reload();
        });
    }
}
function ciy_alert(content, cb, option){
    if(window.parent != window)
        return window.parent.ciy_alert(content, cb, option);
    if(typeof(content) == 'object')
    {
        option = content;
        content = option.content||"";
        cb = option.cb;
    }
    option = option||{};
    var htmldom = '<div class="ciy-layer ciy-dialog" style="z-index: 2000;">';
    if(option.notitle !== true)
    {
        option.title = option.title||"温馨提示";
        htmldom += '<div class="title">'+option.title+'</div>';
        htmldom += '<a class="close"><svg t="1526719117410" style="width:1em;height:1em;" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg"><defs><style/></defs><path d="M1024 91.093333L932.906667 0 512 420.906667 91.093333 0 0 91.093333 420.906667 512 0 932.906667 91.093333 1024 512 603.093333 932.906667 1024 1024 932.906667 603.093333 512 1024 91.093333z" fill="" p-id="1147"></path></svg></a>';
    }
    
    if(option.frame !== undefined)
    {
        htmldom += '<iframe src="'+option.frame+'"';
        if(option.contentstyle !== undefined)
            htmldom += ' style="'+option.contentstyle+'"';
        htmldom += ' frameborder="0"></iframe>';
    }
    else
    {
        htmldom += '<div class="content"';
        if(option.contentstyle !== undefined)
            htmldom += ' style="'+option.contentstyle+'"';
        htmldom += '>'+content+'</div>';
    }
    if(option.nobutton !== true)
    {
        option.btns = option.btns||["确定"];
        if(!$.isArray(option.btns))
            option.btns = ["确定"];
        var btn = '';
        for(var i=0; i<option.btns.length; i++)
        {
            if(option.btns[i][0] != '<')
                btn+="<a class='btn'>"+option.btns[i]+"</a>";
            else
                btn += option.btns[i];
        }
        htmldom += '<div class="buttons">'+btn+'</div>';
    }
    htmldom += '</div>';
    htmldom = $(htmldom);
    var domifm = $("iframe",htmldom);
    if(domifm.length > 0)
    {
        $(domifm).load(function(e){
            e.target.contentWindow.alertautoheight = function(height){
                var bodyheight = document.body.scrollHeight;
                if(bodyheight == 0)
                    bodyheight = document.documentElement.scrollHeight;
                if(bodyheight == 0)
                    return;
                if(height>bodyheight - e.target.offsetTop - 60)
                    height = bodyheight - e.target.offsetTop - 60;
                domifm.css("height",height+"px");
            };
            e.target.contentWindow.alertcb = function(isclose,btn,data){
                if(isclose)
                    alertclose();
                if(typeof(cb) == 'function')
                    return cb(btn,data);
            };
        });
        
    }
    htmldom.on('click','.btn',function(){
        var btntit = this.textContent;
        var inputs = [];
        var xx = htmldom.find('input,select,textarea');
        xx.each(function(){
            inputs[this.name] = this.value;
        });
        alertclose();
        if(typeof(cb) == 'function')
            cb(btntit,inputs);
    });
    htmldom.on('click','.close',function(){
        alertclose();
    });
    
    $('.ciy-mask').css('opacity',0.2).show();
    if(option.nomaskclose !== true)
    {
        $('.ciy-mask').on('click',function(){
            alertclose();
        });
    }
    $('body').append(htmldom);
    if(option.max === true)
    {
        htmldom.find('.content').outerWidth(window.innerWidth);
        var hei = window.innerHeight;
        if(option.notitle !== true)
            hei-=getint(htmldom.find('.title').outerHeight());
        if(option.nobutton !== true)
            hei-=getint(htmldom.find('.buttons').outerHeight());
        htmldom.find('.content').outerHeight(hei);
        $('body').css('overflow','hidden');
    }
    else
    {
        if(option.align === 'right')
            htmldom.css('left',window.innerWidth-htmldom.outerWidth()-15);
        else if(option.align === 'left')
            htmldom.css('left',15);
        else
        htmldom.css('left',(window.innerWidth-htmldom.outerWidth())/2);
        if(window.innerHeight>htmldom.height())
            htmldom.css('top',(window.innerHeight-htmldom.outerHeight())/3);
        else
        {
            htmldom.css('top',0);
            htmldom.find('.content').outerHeight(window.innerHeight - getint(htmldom.find('.buttons').outerHeight()) - getint(htmldom.find('.title').outerHeight()));
        }
        //增加拖动效果
        if(option.notitle === true)
            return;
        if('ontouchstart' in window)
            return;
        var dodrag = null;
        htmldom.on('mousedown','.title',function(ev){
            dodrag = {};
            dodrag.offsetX = ev.offsetX;
            dodrag.offsetY = ev.offsetY;
            setTimeout(function(){htmldom.fadeTo(200,0.8);},100);
            $(document).on('mouseup',function(){
                dodrag = null;
                htmldom.fadeTo(200,1);
                htmldom.off('mouseup');
                htmldom.off('mousemove');
            }).on('mousemove',function(ev){
                if(dodrag == null)
                    return;
                htmldom.css({'left': ev.pageX - dodrag.offsetX,'top': ev.pageY - dodrag.offsetY});
            });
        });
    }
    function alertclose()
    {
        $('.ciy-mask').off('click').hide();
        if(option.max === true)
            $('body').css('overflow','');
        
        var domifm = $("iframe",htmldom);
        if(domifm.length > 0)
        {
            domifm[0].src = 'about:blank';
            domifm[0].contentWindow.close();
        }
        htmldom.remove();
    }
    return false;
}
function ciy_alertclose()
{
    if(window.parent != window)
        return window.parent.ciy_alertclose();
    $('.ciy-dialog>.close').trigger('click');
}
function ciy_alertautoheight()
{
    var sitime = setInterval(function(){
       if(!window.alertautoheight)
           return;
       clearInterval(sitime);
       window.alertautoheight(document.body.clientHeight);
   },100);
}
function ciy_toast(content, option){
    if(window.parent != window)
        return window.parent.ciy_toast(content, option);
    if(option === undefined) option = {};
    var icon = '';
    if(option.icon === 1)
    {
        icon += '<svg class="whirl" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 40 40" enable-background="new 0 0 40 40" xml:space="preserve">';
        icon += '<path opacity="0.7" fill="#ffffff" d="M20.201,5.169c-8.254,0-14.946,6.692-14.946,14.946c0,8.255,6.692,14.946,14.946,14.946 s14.946-6.691,14.946-14.946C35.146,11.861,28.455,5.169,20.201,5.169z M20.201,31.749c-6.425,0-11.634-5.208-11.634-11.634 c0-6.425,5.209-11.634,11.634-11.634c6.425,0,11.633,5.209,11.633,11.634C31.834,26.541,26.626,31.749,20.201,31.749z"/>';
        icon += '<path fill="#ffffff" d="M26.013,10.047l1.654-2.866c-2.198-1.272-4.743-2.012-7.466-2.012h0v3.312h0 C22.32,8.481,24.301,9.057,26.013,10.047z"></path>';
        icon += '</svg>';
    }
    if(option.icon === 2)
    {
        icon += '<svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 50 50" enable-background="new 0 0 50 50" xml:space="preserve">';
        icon += '<path fill="#ffffff" d="M25.251,6.461c-10.318,0-18.683,8.365-18.683,18.683h4.068c0-8.071,6.543-14.615,14.615-14.615V6.461z">';
        icon += '<animateTransform attributeType="xml" attributeName="transform" type="rotate" from="0 25 25" to="360 25 25" dur="1s" repeatCount="indefinite"/>';
        icon += '</path>';
        icon += '</svg>';
    }
    if(option.icon === 3)
    {
        icon += '<svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 50 50" enable-background="new 0 0 50 50" xml:space="preserve">';
        icon += '<path fill="#ffffff" d="M43.935,25.145c0-10.318-8.364-18.683-18.683-18.683c-10.318,0-18.683,8.365-18.683,18.683h4.068c0-8.071,6.543-14.615,14.615-14.615c8.072,0,14.615,6.543,14.615,14.615H43.935z">';
        icon += '<animateTransform attributeType="xml" attributeName="transform" type="rotate" from="0 25 25" to="360 25 25" dur="1s" repeatCount="indefinite"/>';
        icon += '</path>';
        icon += '</svg>';
    }
    if(option.icon === 4)
    {
        icon += '<svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 24 30" enable-background="new 0 0 40 40" xml:space="preserve">';
        icon += '<rect x="0" y="13" width="4" height="5" fill="#ffffff"><animate attributeName="height" attributeType="XML" values="5;21;5" begin="0s" dur="0.6s" repeatCount="indefinite" /><animate attributeName="y" attributeType="XML" values="13; 5; 13" begin="0s" dur="0.6s" repeatCount="indefinite" /></rect>';
        icon += '<rect x="10" y="13" width="4" height="5" fill="#ffffff"><animate attributeName="height" attributeType="XML" values="5;21;5" begin="0.15s" dur="0.6s" repeatCount="indefinite" /><animate attributeName="y" attributeType="XML" values="13; 5; 13" begin="0.15s" dur="0.6s" repeatCount="indefinite" /></rect>';
        icon += '<rect x="20" y="13" width="4" height="5" fill="#ffffff"><animate attributeName="height" attributeType="XML" values="5;21;5" begin="0.3s" dur="0.6s" repeatCount="indefinite" /><animate attributeName="y" attributeType="XML" values="13; 5; 13" begin="0.3s" dur="0.6s" repeatCount="indefinite" /></rect>';
        icon += '</svg>';
    }
    if(option.icon === 5)
    {
        icon += '<svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 24 30" enable-background="new 0 0 40 40" xml:space="preserve">';
        icon += '<rect x="0" y="10" width="4" height="10" fill="#ffffff" opacity="0.2"><animate attributeName="opacity" attributeType="XML" values="0.2; 1; .2" begin="0s" dur="0.6s" repeatCount="indefinite" /><animate attributeName="height" attributeType="XML" values="10; 20; 10" begin="0s" dur="0.6s" repeatCount="indefinite" /><animate attributeName="y" attributeType="XML" values="10; 5; 10" begin="0s" dur="0.6s" repeatCount="indefinite" /></rect>';
        icon += '<rect x="8" y="10" width="4" height="10" fill="#ffffff" opacity="0.2"><animate attributeName="opacity" attributeType="XML" values="0.2; 1; .2" begin="0.15s" dur="0.6s" repeatCount="indefinite" /><animate attributeName="height" attributeType="XML" values="10; 20; 10" begin="0.15s" dur="0.6s" repeatCount="indefinite" /><animate attributeName="y" attributeType="XML" values="10; 5; 10" begin="0.15s" dur="0.6s" repeatCount="indefinite" /></rect>';
        icon += '<rect x="16" y="10" width="4" height="10" fill="#ffffff" opacity="0.2"><animate attributeName="opacity" attributeType="XML" values="0.2; 1; .2" begin="0.3s" dur="0.6s" repeatCount="indefinite" /><animate attributeName="height" attributeType="XML" values="10; 20; 10" begin="0.3s" dur="0.6s" repeatCount="indefinite" /><animate attributeName="y" attributeType="XML" values="10; 5; 10" begin="0.3s" dur="0.6s" repeatCount="indefinite" /></rect>';
        icon += '</svg>';
    }
    var htmldom = '<div class="ciy-layer ciy-toast" style="z-index: 2001;">'+icon+content+'</div>';
    htmldom = $(htmldom);
    $('.ciy-mask').css('opacity',0.1).show();
    if(option.nomaskclose !== true)
    {
        $('.ciy-mask').on('click',function(){
            ciy_toastclose();
        });
    }
    if(!option.timeout)
        option.timeout = 1000;
    setTimeout(function(){
        ciy_toastclose();
        if(typeof(option.done) == 'function')
            option.done();
    },option.timeout);
    $('body').append(htmldom);
    var iw = window.innerWidth, ih = window.innerHeight;
    htmldom.css('left',(iw-htmldom.outerWidth())/2);
    htmldom.css('top',(ih-htmldom.outerHeight())/3);
    return false;
}
function ciy_toastclose(){
    if(window.parent != window)
        return window.parent.ciy_toastclose();
    $('.ciy-mask').off('click').hide();
    $('.ciy-toast').remove();
}
function ciy_loading(){
    if(window.parent != window)
        return window.parent.ciy_loading();
    var htmldom = '<div class="ciy-layer ciy-loading" style="z-index: 2001;"></div>';
    htmldom = $(htmldom);
    $('body').append(htmldom);
    setTimeout(function(){htmldom.addClass("start")},50);
}
function ciy_loadclose(cls){
    if(window.parent != window)
        return window.parent.ciy_loadclose(cls);
    $('.ciy-loading').addClass(cls);
    setTimeout(function(){$('.ciy-loading').remove();},600);
    //
}
function ciy_menu(dom){
    if('ontouchend' in window)
    {
        $(dom).on("click",function(ev){
            var that = $(this);
            if(that.hasClass('show'))
                $(dom).removeClass('show');
            else
            {
                $(dom).removeClass('show');
                that.addClass('show');
            }
        });
    }
    else
    {
        $(dom).on("mouseenter",function(ev){
            $(dom).removeClass('show');
            $(this).addClass('show');
            
        });
        $(dom).on("mouseleave",function(ev){
            $(dom).removeClass('show');
        });
    }
}
function ciy_tab(afterfunc){
    $(".ciy-tab>ul>li").on("click",function(ev){
        var tab = $(this).parents('.ciy-tab');
        $(this).siblings(".active").removeClass('active');
        $(this).addClass('active');
        var index = $(this).prevAll().length;
        tab.children('div').children('div').removeClass('active');
        tab.children('div').children('div').eq(index).addClass('active');
        if(typeof(afterfunc) == 'function')
            afterfunc(index,this);
    });
    $(".ciy-tab>ul").each(function(){
        var uldom = $(this);
        if(uldom.children('li.active').length == 0)
            uldom.children('li:first').addClass('active');
        uldom.children('li').each(function(){
            if($(this).hasClass("active"))
                $(this).trigger('click');
            if(this.offsetTop>2)
                uldom.css('height','6.1em');
            if(this.offsetTop>45)
                uldom.css('height','9.1em');
        });
    });
}
function ciy_cookie(name, value, options) {
    if (typeof value != 'undefined') { // name and value given, set cookie
        options = options || {};
        if (value === null) {
            value = '';
            options.expires = -1;
        }
        var expires = '';
        if (options.expires && (typeof options.expires == 'number' || options.expires.toUTCString)) {
            var date;
            if (typeof options.expires == 'number') {
                date = new Date();
                date.setTime(date.getTime() + (options.expires * 1000));
            } else {
                date = options.expires;
            }
            expires = '; expires=' + date.toUTCString(); // use expires attribute, max-age is not supported by IE
        }
        var path = options.path ? '; path=' + options.path : '';
        var domain = options.domain ? '; domain=' + options.domain : '';
        var secure = options.secure ? '; secure' : '';
        document.cookie = [name, '=', encodeURIComponent(value), expires, path, domain, secure].join('');
    } else { // only name given, get cookie
        var cookieValue = '';
        if (document.cookie && document.cookie != '') {
            var cookies = document.cookie.split(';');
            for (var i = 0; i < cookies.length; i++) {
                var cookie = jQuery.trim(cookies[i]);
                // Does this cookie string begin with the name we want?
                if (cookie.substring(0, name.length + 1) == (name + '=')) {
                    cookieValue = decodeURIComponent(cookie.substring(name.length + 1));
                    break;
                }
            }
        }
        return cookieValue;
    }
}
function getint(val, defval) {
    if(defval == undefined)
        defval = 0;
    var ret = parseInt(val);
    if(isNaN(ret))
        return defval;
    else if(ret == undefined)
        return defval;
    else
        return ret;
}
function getfloat(val, defval) {
    var ret = parseFloat(val);
    if(isNaN(ret))
        return defval;
    else
        return ret;
}
function formattime(dt,bestr){
    if(bestr == undefined)
        bestr = "";
    var diff = (new Date() - new Date(dt));
    diff = parseInt(diff/1000);
    if(diff < 10)//10秒以内
        return "刚刚";
    if(diff < 60)//60秒以内
        return diff +"秒" + bestr;
    if(diff < 3600)//60分以内
        return parseInt(diff/60) +"分" + bestr;
    if(diff < 86400)//24小时以内
        return parseInt(diff/3600) +"小时";
    if(diff < 2592000)//30天以内
        return parseInt(diff/86400) +"天" + bestr;
    if(diff < 31536000)//12月以内
        return parseInt(diff/2592000) +"月" + bestr;
    return parseInt(diff/31536000) +"年" + bestr;
}