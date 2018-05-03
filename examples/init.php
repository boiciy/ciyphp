<?php
error_reporting(E_ALL);//0禁用错误输出；E_ALL打开错误输出
date_default_timezone_set('Asia/Shanghai');

defined('PATH_ROOT') || define('PATH_ROOT', $_SERVER['DOCUMENT_ROOT'].'/'); //web根目录。  
defined('PATH_DIR') || define('PATH_DIR', PATH_ROOT.'examples/');           //指定项目目录  
defined('PATH_PROGRAM') || define('PATH_PROGRAM', PATH_DIR.'');             //指定项目后端目录，可以实现前后端不同目录管理。  
defined('NAME_SELF') || define('NAME_SELF', substr($_SERVER['PHP_SELF'],strrpos($_SERVER['PHP_SELF'],'/')+1,-4));

require PATH_ROOT . 'zcommon/config.php';
require PATH_ROOT . 'zcommon/common.php';
require PATH_ROOT . 'zcommon/data.php';
require PATH_PROGRAM . 'appcommon.php';
