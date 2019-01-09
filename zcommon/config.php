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
            $ret['type'] = 'mysql';//mysql-tab 多主多从读写分离+分库模式；mysql-ms 单库多主多从读写分离模式。详见data.php注释
            $ret['charset'] = 'utf8';
            $ret['name'] = 'ciyphp';
            $ret['port'] = 3306;
            $ret['host'] = '127.0.0.1';//填写web URL地址，则为json方式访问远程数据库。远程服务器增加serverdata.php即可。localhost
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
//echo ciy_config::$conmmonkey;