<?php

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
            $ret['type'] = 'pdo';
            $ret['mode'] = '';//空 单服务器模式；ns 一主多从模式；ms 单库多主多从模式。请替换专用data.php文件
            $ret['conn'] = array();
            $ret['conn'][] = array(
                'dsn'=>'mysql:host=127.0.0.1;dbname=ciyphp;port=3306;',
                //mysql:host=127.0.0.1;dbname=ciyphp;port=3306;                     MySQL/MariaDB
                //pgsql:host=localhost;port=5432;dbname=ciyphp;                     PostgreSQL
                //odbc:Driver={SQL Server};Server=127.0.0.1;Database=ciyphp         ODBC MSSQL
                //oci:dbname=127.0.0.1:1521/ciyphp         ODBC MSSQL
                'user'=>'ciyphp',
                'pass'=>'CiyPHP',
                'timeout'=>5,//数据库连接超时时间，默认5秒
                'persistent'=>false,//持久连接，默认false
                'charset'=>'utf8'//编码方式，默认utf8
                );
            if(isset($_SERVER['HTTP_HOST']) && stripos($_SERVER['HTTP_HOST'],'.local') !== false)
            {
                $ret['conn'][0]['pass'] = 'CiyPHP';
            }
        }
        else if($index == 2)//第二个数据源，此处仅为多种驱动配置例程
        {
            $ret['type'] = 'mysql';//需要mysqlnd支持
            $ret['conn'] = array();
            $ret['conn'][] = array(
                'host'=>'127.0.0.1',
                'name'=>'ciyphp',
                'user'=>'ciyphp',
                'pass'=>'CiyPHP',
                'port'=>3306,//端口号，默认3306
                'timeout'=>5,//数据库连接超时时间，默认5秒
                'charset'=>'utf8'//编码方式，默认utf8
                );
            if(isset($_SERVER['HTTP_HOST']) && stripos($_SERVER['HTTP_HOST'],'.local') !== false)
            {
                $ret['conn'][0]['pass'] = 'CiyPHP';
            }
        }
        else if($index == 3)
        {
            $ret['type'] = 'http';
            $ret['conn'] = array();
            $ret['conn'][] = array(
                'host'=>'http://ciyphp.ciy.cn/serverdata.php',//填写web URL地址，访问远程数据库（仅限于临时内部数据分析使用）
                'user'=>'ciyphp',
                'pass'=>'ciy'
                );
        }
        return $ret;
    }
}
