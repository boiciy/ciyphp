<!DOCTYPE html><html>
<head>
    <meta charset="UTF-8">
    <title>众产接口测试</title>
    <script src="http://apps.bdimg.com/libs/jquery/2.1.4/jquery.min.js"></script>
    <style type="text/css">
    body,div,dl,dt,dd,ul,ol,li,h1,h2,h3,h4,h5,h6,input,button,textarea,p,blockquote,th,td,form,pre{margin: 0; padding: 0; -webkit-tap-highlight-color:rgba(0,0,0,0);box-sizing: border-box;}
    input,button,textarea,select,optgroup,option,a:active,a:hover{outline:0}
    img{display: inline-block; border: none; vertical-align: middle;}
    li{list-style:none;}
    hr{height:1px;background-color:#cccccc;border:none;line-height:1px;clear:both;display:block;overflow:hidden;}
    table{border-collapse: collapse; border-spacing: 0;}
    body{font: 14px Helvetica Neue,Helvetica,PingFang SC,\5FAE\8F6F\96C5\9ED1,Tahoma,Arial,sans-serif;}
    code{padding: 2px 4px;font-size: 0.8em;color: #c7254e;background-color: #f9f2f4;border-radius: 4px;}
    .form-group{
        margin-top: 5px;
        margin-bottom: 5px;
        display:block;
    }
    .form-group>div{
        width:calc(100% - 7.5em);
        margin-left: 7.5em;
        padding:0.3em 0.5em;
        line-height: 2.5em;
    }
    .form-group>label{
        width:7.5em;
        float:left;
        display: inline-block;
        text-align:right;
        padding:0.3em 0;
        height:2.5em;
        line-height:2.5em;
    }
    .form-group>div>label{
        white-space: nowrap;
        display: inline-block;
        height: 2.5em;
        line-height: 2.5em;
    }
    .form-group:before,.form-group:after{
        display: table;
        content: " ";
        clear:both;
    }
    form:after {
        display: table;
        content: " ";
        clear:both;
    }
    textarea {
        padding:0.5em;
        line-height:1.6em;
        min-height:5em;
    }
    select,textarea,input[type=text],input[type=password],input[type=email],input[type=url],input[type=number],input[type=range],input[type=search],input[type=color],input[type=date],input[type=datetime],input[type=time]{
        display: inline-block;
        width: 100%;
        height: 2.5em;
        padding-left:0.5em;
        color: #555555;
        background-color: #ffffff;
        border: 1px solid #cccccc;
        border-radius: 4px;
        -webkit-box-shadow: inset 0 1px 1px rgba(0,0,0,.075);
        box-shadow: inset 0 1px 1px rgba(0,0,0,.075);
        transition: box-shadow .5s;-webkit-transition: box-shadow .5s;
    }
    textarea:focus,select:focus,input[type=text]:focus{
        border: 1px solid rgba(82,168,236,.8);
        -webkit-box-shadow: 0 0 8px rgba(82,168,236,.6);
        box-shadow: 0 0 8px rgba(82,168,236,.6);
    }

    blockquote{
        margin-bottom: 10px;
        padding: 15px;
        line-height: 22px;
        border-left: 5px solid #1E9FFF;
        border-radius: 0 2px 2px 0;
        background-color: #f6f6f6;
    }
    fieldset{
        border: none;
        padding: 0;
        border-top: 1px solid #eeeeee;
        margin:1em 2em;
    }
    fieldset.ciy-fieldset-box{
        padding: 0;
        border: 1px solid #cccccc;
        border-radius: 0.3em;
    }
    fieldset>legend {
        margin-left: 1em;
        padding: 0 0.3em;
        font-size: 1.5em;
        font-weight: 300;
    }
    fieldset.ciy-fieldset-tips{
        padding: 0;
        border: 1px solid #cccccc;
        border-radius: 8px;
    }
    fieldset.ciy-fieldset-tips>div{
        padding:1em 1em 0.5em 2em;
    }
    .btn {
        position: relative;
        background: #1E9FFF;
        color:#ffffff;
        display: inline-block;
        padding: 0.5em;
        font-size:1em;
        line-height:1em;
        margin:1px;
        text-align: center;
        white-space: nowrap;
        vertical-align: middle;
        -ms-touch-action: manipulation;
        touch-action: manipulation;
        cursor: pointer;
        border: 1px solid rgba(0,0,0,0.15);
        border-radius: 4px;
        transition: all .2s;-webkit-transition: all .2s;
    }
    .btn:hover {
        box-shadow: 0 0 2px #ffffff inset;
    }
    .btn:active {
        box-shadow: 0 0 5px rgba(0,0,0,0.2) inset;
    }
    .btn-llg {
        font-size:2em;margin-left:0.3em;
    }
    .abtn {
        cursor:pointer;
    }
    #id_result{margin:0.5em;padding:0.5em;}
    #Canvas {color: #000000;border: solid 1px #CECECE;margin:0.5em;padding:0.5em;}
    .ObjectBrace {color: #00AA00;font-weight: bold;}
    .ArrayBrace {color: #0033FF;font-weight: bold;}
    .PropertyName {color: #CC0000;font-weight: bold;}
    .String {color: #007777;}
    .Number {color: #AA00AA;}
    .Boolean {color: #0000FF;}
    .Function {color: #AA6633;text-decoration: italic;}
    .Null {color: #0000FF;}
    .Comma {color: #000000;font-weight: bold;}
    pre.CodeContainer {margin-top: 0px;margin-bottom: 0px;}
    </style>
</head>
<body>
    <fieldset class="ciy-fieldset-tips">
      <legend>众产接口测试</legend>
      <div>
        <form method="get" action="">
            <div class="form-group">
                <label>测试路径</label>
                <div><input type="text" class="imp" name="file" value="<?php echo @$_GET['file'];?>" style="width:20em;"/></div>
            </div>
            <div class="form-group">
                <label>函数入口</label>
                <div><input type="text" name="func" value="<?php echo @$_GET['func'];?>" onkeyup="$('code').text('json_'+$('input[name=func]').val()+'()')" style="width:20em;"/> <code>json_<?php echo @$_GET['func'];?>()</code></div>
            </div>
            <div class="form-group">
                <label>POST参数</label>
                <div><textarea name="param" style="width:40em;height:10em;"><?php echo str_replace('&',"\n",@$_GET['param']);?></textarea></div>
            </div>
            <div class="form-group">
                <label id="id_sec"></label>
                <button class="btn btn-llg" type="button" onclick="callrun()">接口测试</button>
                　　　　Cookie：<a onclick="readcookie()" class="abtn">读取</a>　<a onclick="clearcookie()" class="abtn">清空</a>　　　　LocalStorage：<a onclick="readStorage()" class="abtn">读取</a>　<a onclick="clearStorage()" class="abtn">清空</a>
            </div>
            <div class="form-group">
                <label><button class="btn" type="button" onclick="location.href=createurl()">生成地址Go</button></label>
                <div><input type="text" class="imp" name="url"/></div>
            </div>
        </form>
      </div>
    </fieldset>
    <br/>
    <blockquote id="id_title">准备好</blockquote>
    <div id="Canvas"></div>
    <div id="id_result"></div>
<script type="text/javascript">
var runtime = -1;
$(function(){
    $('input[name=url]').val(createurl());
    setInterval(function(){
        if(runtime == -1)
            return;
        runtime++;
        $("#id_sec").text(runtime+"〃");
    },1000);
});
function readStorage()
{
    var ls = localStorage.valueOf();
    var html = '';
    for(var x in ls)
        html+="<code>"+x+"</code> "+ls[x]+"<br/><br/>";
    if(html == '')
        html = 'No localStorage!';
    $("#Canvas").html(html);
}
function clearStorage()
{
    if(confirm("清空LocalStorage？") !=true)
        return;
    localStorage.clear();
    readStorage();
    alert('清空完成');
}
function readcookie()
{
    $("#Canvas").html(document.cookie);
}
function clearcookie()
{
    if(confirm("清空Cookie？") !=true)
        return;
    var keys=document.cookie.match(/[^ =;]+(?=\=)/g);
    if (keys) {
        for (var i = keys.length; i--;)
            document.cookie=keys[i]+'=0;expires=' + new Date( 0).toUTCString()
    }
    readcookie();
    alert('清空完成');
}
function createurl()
{
    return location.protocol+'//'+location.host+location.pathname+"?file="+encodeURIComponent($('input[name=file]').val())+"&func="+encodeURIComponent($('input[name=func]').val())+"&param="+encodeURIComponent($('textarea[name=param]').val());
}
function callrun()
{
    var file = $('input[name=file]').val();
    var func = $('input[name=func]').val();
    var param = $('textarea[name=param]').val();
    if(file == '')
        return alert('请填写测试路径');
    runtime = -1;
    var sptime = new Date().valueOf();
    $("#id_sec").html('');
    $("#id_title").html('执行中...');
    $("#id_result").html('');
    $("#Canvas").html('');
    $.ajax({type:'POST',url:file+'?json=true&func='+func,data:param.replace(/\n/g,'&'),complete:function(xhr,ts){
        sptime = new Date().valueOf()-sptime;
        runtime = 0;
        if(xhr.status != 200)
        {
            $("#id_title").html('<span style="color:red;">访问失败 【'+xhr.status+'】</span>　'+file+func);
            document.getElementById("Canvas").innerHTML = xhr.responseText;
        }
        else
        {
            try{
                var json = JSON.parse(xhr.responseText);
                Process(xhr.responseText);
                if(json.result)
                    $("#id_title").html('<span style="color:green;">请求成功</span>　'+file+'?func='+func+'<code style="float:right;">'+sptime+'ms</code>');
                else
                    $("#id_title").html('<span style="color:red;">返回错误</span>　'+file+'?func='+func);
            }catch(e){
                var errtxt = xhr.responseText;
                if(errtxt.substr(-1) == '}')
                {
                    var ind = errtxt.lastIndexOf('{');
                    Process(errtxt.substr(ind));
                    errtxt = errtxt.substr(0,ind);
                }
                $("#id_result").html(errtxt);
                $("#id_title").html('<span style="color:red;">调试信息</span>　'+file+'?func='+func);
            }
        }
    }});
}
window.TAB = "    ";
function IsArray(obj) {
  return obj &&
      typeof obj === 'object' &&  typeof obj.length === 'number' && !(obj.propertyIsEnumerable('length'));
}
function Process(json) {
    document.getElementById("Canvas").style.display = "block";
    var html = "";
    try {
        if (json == "") {
            json = '""';
        }
        var obj = eval("[" + json + "]");
        html = ProcessObject(obj[0], 0, false, false, false);
        document.getElementById("Canvas").innerHTML = "<PRE class='CodeContainer'>" + html + "</PRE>";
    } catch(e) {
        document.getElementById("Canvas").innerHTML = "json语法错误，不能格式化。<br/>错误信息:\n" + e.message;
    }
}
function ProcessObject(obj, indent, addComma, isArray, isPropertyContent) {
    var html = "";
    var comma = (addComma) ? "<span class='Comma'>,</span> ": "";
    var type = typeof obj;
    if (IsArray(obj)) {
        if (obj.length == 0) {
            html += GetRow(indent, "<span class='ArrayBrace'>[ ]</span>" + comma, isPropertyContent);
        } else {
            html += GetRow(indent, "<span class='ArrayBrace'>[</span>", isPropertyContent);
            for (var i = 0; i < obj.length; i++) {
                html += ProcessObject(obj[i], indent + 1, i < (obj.length - 1), true, false);
            }
            html += GetRow(indent, "<span class='ArrayBrace'>]</span>" + comma);
        }
    } else {
        if (type == "object" && obj == null) {
            html += FormatLiteral("null", "", comma, indent, isArray, "Null");
        } else {
            if (type == "object") {
                var numProps = 0;
                for (var prop in obj) {
                    numProps++;
                }
                if (numProps == 0) {
                    html += GetRow(indent, "<span class='ObjectBrace'>{ }</span>" + comma, isPropertyContent)
                } else {
                    html += GetRow(indent, "<span class='ObjectBrace'>{</span>", isPropertyContent);
                    var j = 0;
                    for (var prop in obj) {
                        html += GetRow(indent + 1, '<span class="PropertyName">"' + prop + '"</span>: ' + ProcessObject(obj[prop], indent + 1, ++j < numProps, false, true))
                    }
                    html += GetRow(indent, "<span class='ObjectBrace'>}</span>" + comma);
                }
            } else {
                if (type == "number") {
                    html += FormatLiteral(obj, "", comma, indent, isArray, "Number");
                } else {
                    if (type == "boolean") {
                        html += FormatLiteral(obj, "", comma, indent, isArray, "Boolean");
                    } else {
                        if (type == "function") {
                            obj = FormatFunction(indent, obj);
                            html += FormatLiteral(obj, "", comma, indent, isArray, "Function");
                        } else {
                            if (type == "undefined") {
                                html += FormatLiteral("undefined", "", comma, indent, isArray, "Null");
                            } else {
                                html += FormatLiteral(obj, '"', comma, indent, isArray, "String");
                            }
                        }
                    }
                }
            }
        }
    }
    return html;
};

function FormatLiteral(literal, quote, comma, indent, isArray, style) {
    if (typeof literal == "string") {
        literal = literal.split("<").join("&lt;").split(">").join("&gt;");
    }
    var str = "<span class='" + style + "'>" + quote + literal + quote + comma + "</span>";
    if (isArray) {
        str = GetRow(indent, str);
    }
    return str;
}
function FormatFunction(indent, obj) {
    var tabs = "";
    for (var i = 0; i < indent; i++) {
        tabs += window.TAB;
    }
    var funcStrArray = obj.toString().split("\n");
    var str = "";
    for (var i = 0; i < funcStrArray.length; i++) {
        str += ((i == 0) ? "": tabs) + funcStrArray[i] + "\n";
    }
    return str;
}
function GetRow(indent, data, isPropertyContent) {
    var tabs = "";
    for (var i = 0; i < indent && !isPropertyContent; i++) {
        tabs += window.TAB;
    }
    if (data != null && data.length > 0 && data.charAt(data.length - 1) != "\n") {
        data = data + "\n";
    }
    return tabs + data;
};
</script>
</body>
</html>