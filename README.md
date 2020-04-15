# 众产未来 - IT工程 - 全栈工程师

## WEB PHP框架
极简封装的软件架构，附带一个后台UI，实现快速开发。  
众产风格，能简化的，绝不繁杂，摒弃概念，回归本质。  
1个目录，最少4个文件组成框架结构，函数式代码风格。  
支持前端、后端、DBA协同开发，类似但有别于MVC架构。  

提供了一套演示代码  
后台UI演示地址：[前往查看](http://ciyphp.ciy.cn/examples/)

一套管理后台脚手架  
带管理的后台演示地址：[前往查看](http://ciyphp.ciy.cn/admin/)


## 目录结构
>zcommon/  
>>common.php  
>>data.php  
>>pdo.php  
>>dbajax.php  
>>config.php  

>acommon.php  
>jscss/  
>examples/  
>upload.php  

### common.php 常用公共函数库。
封装了 Ajax函数调用、Url参数拼接函数、CSV导出、用户安全输入、文件操作等  

`acommon.php`　　扩展的公共函数库，与耦合了部分数据库。  
封装了 报错显示、数字/字符串加解密函数、log保存、页面分页显示等

### data.php 应用数据层类库。
封装了数据层，get/getone/set/execute/delete数据库接口。  
`pdo.php`　　PDO驱动层。由数据层data.php引用，set接口实现了insert和update SQL命令整合。  
`mysql.php`　　MYSQL驱动层。由数据层data.php引用，功能同pdo，建议使用pdo。  
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
        $ret = array();
        if($index == 1)
        {
            $ret['type'] = 'pdo';
            $ret['mode'] = '';//空 单服务器模式；ns 一主多从模式；ms 单库多主多从模式。需替换专用data.php文件
            $ret['conn'] = array();
            $ret['conn'][] = array(
                'dsn'=>'mysql:host=127.0.0.1;dbname=ciyphp;port=3306;',
                'user'=>'ciyphp',
                'pass'=>'CiyPHP',
                'timeout'=>5,//数据库连接超时时间，默认5秒
                'persistent'=>false,//持久连接，默认false
                'charset'=>'utf8'//编码方式，默认utf8
            );
            if(isset($_SERVER['HTTP_HOST']) && stripos($_SERVER['HTTP_HOST'],'local') !== false)
            {
                $ret['conn'][0]['pass'] = 'CiyPHP';
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
defined('PATH_PROGRAM') || define('PATH_PROGRAM', __DIR__.'/');    //指定项目后端目录，可以实现前后端不同目录管理。  
defined('NAME_SELF') || define('NAME_SELF', $_SERVER['PHP_SELF']);  

require PATH_ROOT . 'zcommon/config.php';
require PATH_ROOT . 'zcommon/common.php';
require PATH_ROOT . 'zcommon/data.php';
require PATH_ROOT . 'acommon.php';
```

### /examples (demo.php demo.pro.php demo_update.php demo_update.pro.php)
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
$table = 'd_test';
$msql = new ciy_sql($table);
$msql->where('truename like',get('truename'));
$msql->order(get('order','id desc'));
$pageno = getint('pageno', 1);$pagecount = 20;
$msql->limit($pageno,$pagecount);
$rows = $mydata->get($msql,$mainrowcount); //不需页码的无限上拉加载，不要带mainrowcount参数，提升性能。

function json_update() {//Ajax交互函数，ciy_runJSON()调用。本质是json数据交换。
    global $mydata;
    $post = new ciy_post();
    $id = $post->getint('id');
    数据处理...
    return succjson();
}
```

## 部署方式
web根目录下拷贝zcommon目录，更改config.php配置文件。即可完成文件部署。  
后端代码目录可以非WEB目录，提高安全性。只需对init.php 中的PATH_PROGRAM进行修改。  

## 框架建议
与其他框架不同，前端无模板，而直接使用php。建议前端html化。  
文件名命名习惯：  
```php
*.php  /  *.pro.php     成对出现。*.php引用*.pro.php，前端Ajax调用。都在*.pro.php中完成。*.pro.php直接访问无效。  
*.html /  *.pro.php  　  成对出现。*.html通过Ajax初始化及数据请求。  
```

后端变量取名，建议getone函数使用`$xxrow`表示一条数据，get函数使用`$xxrows`表示多条数据。  
```php
$count = get1();//返回第一行第一列数据
$xxrow = getone();//返回单条数据
$xxrows = get();//返回多条数据
foreach($xxrows as $row){
}
```

## 适应众产监督的IT架构
为能够让无编程经验的普通人都能理解并监督程序代码块。  
我们将放弃一些酷炫前端界面效果的同时，尽量实现前端UI原生优化。  
如果您对界面效果有极致要求，请谨慎使用。  

[众产](http://ciy.cn) [众产IT工程](http://ciy.cn/code)
