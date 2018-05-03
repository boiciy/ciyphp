function OperateCookie(name, value, options) {
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
    ret = parseInt(val);
    if(isNaN(ret))
        return defval;
    else if(ret == undefined)
        return defval;
    else
        return ret;
}
function getfloat(val, defval) {
    ret = parseFloat(val);
    if(isNaN(ret))
        return defval;
    else
        return ret;
}
function getids()
{
    var array = new Array();
    $(".trlist").each(function () {
        if ($(this).hasClass("checked")) {
            array.push($(this).attr("id").replace("td",""));
        }
    })
    return array.join(",");
}
function selectall()
{
    $(".trlist").each(function () {
        $(this).addClass("checked");
        });
}
function selectdiff()
{
    $(".trlist").each(function () {
        $(this).toggleClass("checked");
        });
}
function setact(act,opt)
{
    var defopt = {
            data: ''
    };
    //data: 参数列表
    //confirmmsg：确认文字（继续取消）
    //success：成功调用

    var opt = $.extend(defopt, opt);
    postparam={};
    postparam.act = act;
    postparam.data = opt.data;
    postparam.ids = getids();
    if(postparam.ids == ""){
        showmsg("请先选择操作对象");
        return false;
    }
    if(defopt.confirmmsg !== undefined)
    {
        showmsg(defopt.confirmmsg,"继续,取消",function(btn){
            if(btn == "继续")
            {
                callfunc("setact",postparam,function(json){
                    if(typeof(defopt.success) === "function")
                        defopt.success();
                    else
                        location.reload();
                });
            }
        });
    }
    else
    {
        callfunc("setact",postparam,function(json){
            if(typeof(defopt.success) === "function")
                defopt.success();
            else
                location.reload();
        });
    }
}
function callfunc(funcname, post, successfunc,option){
    if(option === undefined)
        option = {};
    if(option.murl === undefined)
        option.murl = '';
      $.post(option.murl + "?cb=&json=true&func="+funcname, post, 
             function (msg) {
               if(msg.result)
                   successfunc(msg);
               else
                 alert(msg.msg);
        },"json").fail(function(err) {
               if(err.status == 200)
                   alert("服务器返回错误\n" + err.responseText);
               else
               {
                   if(err.status == 0)
                        alert("请求未响应，请重试");
                    else
                        alert("服务器返回错误码<br/>" + err.status + "," + err.statusText);
               }
  }).complete(function() {
        if(option.complete !== undefined)
            option.complete();
  });
}
function isMobile(s)
{
var patrn=/^[1][0-9]{10}$/;
if (!patrn.exec(s))
    return false;
return true;
}
function isEmail (theStr) {
var atIndex = theStr.indexOf('@');
var dotIndex = theStr.indexOf('.', atIndex);
var flag = true;
theSub = theStr.substring(0, dotIndex+1)

if ((atIndex < 1)||(atIndex != theStr.lastIndexOf('@'))||(dotIndex < atIndex + 2)||(theStr.length <= theSub.length)) 
{ return(false); }
else { return(true); }
}
function formattime(dt,bestr)
{
    if(bestr == undefined)
        bestr = "";
    diff = (new Date() - new Date(dt));
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