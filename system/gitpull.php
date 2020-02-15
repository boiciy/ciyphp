<?php
/* =================================================================================
 * 版权声明：保留开源作者及版权声明前提下，开源代码可进行修改及用于任何商业用途。
 * 开源作者：众产国际产业公会  http://ciy.cn/code
 * 版本：0.7.0
====================================================================================*/
/*
 * 支持github和码云，需设置webhooks网址
 * 运行原理:
 * 先从git上拉取代码。手动在目录中执行git pull无错。
 * 设置webhooks，选择application/json
 * 有push时，git库访问本页面，在upload目录下建立gitpull.x文件。
 * crond定时执行<gitpull.sh 仓库目录 [反馈地址]>，如果gitpull.x文件存在，则执行git pull拉取，成功后删除文件。
 * gitpull.sh脚本将拉取记录到upload/gitpull.log文件。如果设置反馈地址，则会进一步通知成功或失败消息。
 * 该方案无需增加php执行权限，相对安全，还可以实现集群部署。
 * 该方案也可用于golang等编译语言更新，成功后自动执行平滑重启。
 * 
 */
require 'init.php';
if(@$_SERVER['HTTP_X_GITHUB_EVENT'] == 'push' || @$_SERVER['HTTP_X_GITEE_EVENT'] == 'Push Hook'){
    $uploadcfg = ciy_config::getupload();
    if ($fp = fopen(PATH_ROOT.$uploadcfg['dir'].'/gitpull.x', 'w')) {
        @fwrite($fp, date('Y-m-d H:i:s'));
        fclose($fp);
        echo 'OK';
    }
    else
        echo 'NG';
}
else{
    ciy_runJSON();
}
function json_git(){
    if(get('run') == 'success'){
        //成功处理
    }
    else{
        //失败处理，拉取失败过一分钟将自动重试。
    }
    return succjson();
}
