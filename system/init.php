<?php
error_reporting(E_ALL);//0禁用错误输出；E_ALL打开错误输出
date_default_timezone_set('Asia/Shanghai');
if(__DIR__ != getcwd())
    chdir(__DIR__);//方便CLI执行
defined('PATH_ROOT') || define('PATH_ROOT', dirname(__DIR__).'/'); //web根目录。
defined('PATH_PROGRAM') || define('PATH_PROGRAM', __DIR__.'/');

require PATH_ROOT . 'zcommon/config.php';
require PATH_ROOT . 'zcommon/common.php';
require PATH_ROOT . 'zcommon/data.php';
require PATH_ROOT . 'acommon.php';
require PATH_ROOT . 'aitemfunc.php';
