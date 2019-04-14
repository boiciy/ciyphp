<?php
require 'init.php';
error_reporting(E_ALL^E_NOTICE);
$path = get('delfile');
if(!empty($path))
{
    if(is_file(PATH_ROOT.$path))
        delfile(PATH_ROOT.$path);
    echo json_encode(succjson());
    exit;
}
$path = get('filepath');
if (@$_FILES['file'] == null)
    $ret = errjson('没有文件上传');
else if ($_FILES['file']['error'] > 0)
    $ret = errjson('上传参数出错:' . $_FILES['file']['error']);
else {
    $extfile = strtolower(pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION));
    $path = str_replace('{Y}', date('Y'), $path);
    $path = str_replace('{M}', date('m'), $path);
    $path = str_replace('{D}', date('d'), $path);
    $path = str_replace('{H}', date('H'), $path);
    $path = str_replace('{I}', date('i'), $path);
    $path = str_replace('{S}', date('s'), $path);
    $path = str_replace('{Rnd}', rand(1000,9999), $path);
    $path = str_replace('{Ext}', $extfile, $path);
    if(strpos($path,'{') !== false || strpos($path,'}') !== false)
        $ret = errjson('filepath文件名解析错误'.$path);
    else
    {
        makedir(dirname(PATH_ROOT.$path)); 
        move_uploaded_file($_FILES['file']['tmp_name'], PATH_ROOT.$path);
        $ret = succjson(array('msg'=>$path,'name'=>$_FILES['file']['name']));
    }
}
echo json_encode($ret);
?>