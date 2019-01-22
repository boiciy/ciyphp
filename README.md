# 众产未来 - IT工程 - 全栈工程师

## WEB PHP框架
这款PHP框架，经历了5年发展，开发了十几个商业项目。  
众产风格极易理解，目录结构清晰，文件极少，支持前后端分离，DBA与后端逻辑可分离。  
后端UI演示地址：[前往查看](http://ciyphp.ciy.cn/examples/index.html)

## 目录结构
>zcommon/  
>>common.php  
>>data.php  
>>mysql.php  
>>dbajax.php  
>>config.php  

>acommon.php  
>jscss/  
>examples/  
>>serverdata.php  
>>appcommon.php  
>>demo.php  
>>demo.pro.php  
>>demo_update.php  
>>demo_update.pro.php  
>>upload.php  
>>init.php  

### common.php 常用公共函数库。
封装了 Ajax函数调用、Url参数拼接函数、Application对象、CSV导出、用户安全输入、文件操作等  

`acommon.php`　　扩展的公共函数库，与项目数据库有关系。  
封装了 报错显示、数字/字符串加解密函数、log保存、页面分页显示等

### data.php 应用数据层类库。
封装了数据层，get/getone/set/execute/delete数据库接口。  
`mysql.php`　　MYSQL驱动层。由数据层data.php引用，set接口实现了insert和update SQL命令整合。  
`dbajax.php`　　跨服访问层。由数据层data.php引用，实现了可控的远程数据库接口，自定义授权函数。  
`serverdata.php`　　数据中间件。一般在项目目录内，用来被dbajax.php远程调用。  

### config.php 配置文件。
配置一个或多个数据库服务器连接参数，配置加密因子。
```php
class ciy_config {
    public static $conmmonkey = 'zid4Akto8';//做数据加解密时的加密因子，每个项目都不要相同。
    public static function getdb($index = 1)
    {
        //一般的，本地调试连接本地数据库，数据库密码一般会不同，您可以单独配置，便于本地调试。
        //如果您只有一个Web项目，可以访问localhost，多个web项目，建议使用 xx.local的本地域名，统一使用80端口调试。(配置C:\Windows\System32\drivers\etc\hosts)
        //如果您发现xx.local本地域名访问时很慢(延迟3-4秒)，请使用xx.local.ciy.cn作为本地域名，*.local.ciy.cn已经永久的指向到了127.0.0.1
        $ret = array();
        if($index == 1)
        {
            $ret['type'] = 'mysql';//mysql-tab 多主多从读写分离+分库模式；mysql-ms 单库多主多从读写分离模式。详见data.php注释
            $ret['charset'] = 'utf8';
            $ret['name'] = 'ciyphp';
            $ret['port'] = 3306;
            $ret['host'] = '127.0.0.1';//填写web URL地址，则为json方式访问远程数据库。远程服务器增加dbjson.php即可。localhost
            $ret['user'] = 'ciyphp';
            $ret['pass'] = 'CiyPHP';
            if(stripos($_SERVER['HTTP_HOST'],'local') !== false)
            {
                $ret['pass'] = 'CiyPHP';
            }
        }
        else if($index == 2)
        {
            //$ret['type']...   第二个数据库服务器集群
        }
        return $ret;
    }
}
```

### init.php 路径配置及框架引用。项目中所有php都应先引用init.php
特别的，目录默认使用`/`结尾
```php
defined('PATH_ROOT') || define('PATH_ROOT', $_SERVER['DOCUMENT_ROOT'].'/');  //web根目录。  
defined('PATH_PROGRAM') || define('PATH_PROGRAM', PATH_ROOT.'examples/');    //指定项目后端目录，可以实现前后端不同目录管理。  
defined('NAME_SELF') || define('NAME_SELF', $_SERVER['PHP_SELF']);  

require PATH_ROOT . 'zcommon/config.php';
require PATH_ROOT . 'zcommon/common.php';
require PATH_ROOT . 'zcommon/data.php';
require PATH_ROOT . 'acommon.php';
```

### demo.php demo.pro.php demo_update.php demo_update.pro.php
例子程序，演示框架基本代码编写流程。包含数据增删改查、上传文件、导出等基本功能。  
调用例子函数之前，请先用database.sql在mysql中建立d_user/d_test/d_test_bak表。  
```php
//demo.php
<?php
require 'init.php';
require PATH_PROGRAM . NAME_SELF . '.pro.php';
?><!DOCTYPE html>
<html>
...
```
```php
//demo.pro.php
<?php
$mydata = new ciy_data();
ciy_runJSON();
$pageno = (int)get('page', 1);
$pagecount = 20;
$where = '';
$rows = $mydata->get($pageno,$pagecount, 'd_test', $where,'id desc');

function json_setact() {//Ajax交互函数，ciy_runJSON()调用。
    global $mydata;
    $post = new ciy_post();
    $id = $post->getint('id');
    $act = $post->get('act');
    数据处理...
    return succjson();
}
```

## 部署方式
web根目录下拷贝zcommon目录，更改config.php配置文件。即可完成文件部署。  
后端代码目录可以非WEB目录。只需对init.php 中的PATH_PROGRAM进行修改。  

## 框架建议
与其他框架不同，前端无模板，而直接使用php。建议前端html化。  
文件名命名习惯：  
```php
*.php  /  *.pro.php     成对出现。*.php引用*.pro.php，前端Ajax调用。都在*.pro.php中完成。*.pro.php直接访问无效。  
*.html /  *.pro.php  　  成对出现。*.html通过Ajax初始化及数据请求。  
```

后端变量取名，建议getone函数使用$xx`row`表示一条数据，get函数使用$xx`rows`表示多条数据。  
```php
$xxrow = getone();//返回单条数据
$xxrows = get();//返回多条数据
foreach($xxrows as $row)
{
}
```

## 框架演进计划
#### 逐步支持各类库函数及SDK
如二维码生成、excel导入导出、html爬虫、短信邮件等等。  
数据库结构体文档生成工具、自动化代码生成工具、简易后端界面框架、定时执行任务。  
以上函数库都已完成（但未经大规模商用验证），如有需要可以直接留言，作者将源码发送至信箱。  

#### Socket后端框架及前端CiySocket.js库
基于Workerman的PHP Socket框架，采用端口并发机制，基本实现了无需变量锁的编程方法。降低开发难度，提升开发效率。  
无需变量锁编程，和没有锁意识的编程是两回事，很多程序员无奈的发现，用户量上来了，程序变的不稳定，原因在这里  

#### 后台管理练习框架及common.js库
提供一个后台管理UI框架。  
帮助程序员逐渐放弃使用大型复杂的前端框架，进而一步一步练习搭建UI框架，全面掌控。  
越大型复杂的框架集成度越高，基础开发成本极低，但个性化开发成本较高，精通难度大。  

## 适应众产监督的IT架构
为能够让无编程经验的普通人都能理解并监督程序代码块。  
我们将放弃一些酷炫前端界面效果的同时，尽量实现前端UI原生优化。  
如果您对界面效果有极致要求，请谨慎使用。  

## 版权声明
遵循MIT协议。  
在源代码上，保留开源作者及版权声明注释前提下，开源代码可进行修改及用于任何商业用途。  

[众产](http://ciy.cn) [众产IT工程](http://ciy.cn/code)
