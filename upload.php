<?php
require 'init.php';
$mydata = new ciy_data();
$rsuser = verifyadmin(function($err){
    upload_outjson(errjson($err));
});
error_reporting(E_ALL^E_NOTICE);
$uploadcfg = ciy_config::getupload();
$path = get('filepath');
if(empty($path))
    upload_outjson(errjson('filepath错误'));
$path = str_replace('\\', '/', $path);
$pos = strrpos($path, '/');
$filename = (false === $pos) ? $path : substr($path, $pos + 1);
$filename = trim($filename);
if(empty($filename))
    $path.='{Y}_{M}/{D}_{H}_{I}_{S}{Rnd}';
if($filename[0] == '.')
    upload_outjson(errjson('文件名不合法'));
if(count($_FILES) == 0)
    upload_outjson(errjson('没有文件上传'));
$file = reset($_FILES);
if ($file['error'] > 0)
    upload_outjson(errjson('上传参数出错:' . $file['error']));
$name = $file['name'];
$name = str_replace('\\', '/', $name);
$pos = strrpos($name, '/');
$name = (false === $pos) ? $name : substr($name, $pos + 1);
$extfile = strtolower(pathinfo($name, PATHINFO_EXTENSION));
if($uploadcfg['checkext'] == 'exts')
{
    if(!in_array($extfile,$uploadcfg['exts']))
        upload_outjson(errjson("不允许上传{$extfile}类型文件"));
}
else
{
    if(in_array($extfile,$uploadcfg['noexts']))
        upload_outjson(errjson("不允许上传{$extfile}执行文件"));
}
$path = str_replace('{Y}', date('Y'), $path);
$path = str_replace('{M}', date('m'), $path);
$path = str_replace('{D}', date('d'), $path);
$path = str_replace('{H}', date('H'), $path);
$path = str_replace('{I}', date('i'), $path);
$path = str_replace('{S}', date('s'), $path);
$path = str_replace('{Rnd}', rand(100000,999999), $path);
if(strpos($path,'{') !== false || strpos($path,'}') !== false)
    upload_outjson(errjson('filepath文件名解析错误'.$path));
$path.='.'.$extfile;

$tpath = dirname(PATH_ROOT.$path);
makedir($tpath);
$tpath = realpath($tpath);
if($tpath === false)
    upload_outjson(errjson('文件夹不存在'));
$tpath.=DIRECTORY_SEPARATOR;
$tdpath = realpath(PATH_ROOT.$uploadcfg['dir']).DIRECTORY_SEPARATOR;
if(strpos($tpath,$tdpath) !== 0)
    upload_outjson(errjson('上传文件夹超范围'));
$tstr = file_get_contents($file["tmp_name"]);
if(strpos($tstr,'<?php') !== false)
    upload_outjson(errjson('文件内容不合法'));
move_uploaded_file($file['tmp_name'], PATH_ROOT.$path);
$ret = succjson(array('url'=>$path,'name'=>$name));
upload_outjson($ret);
function upload_outjson($json)
{
    if($json['result'])
        $json['error'] = 0;
    else
    {
        $json['error'] = 1;
        $json['message'] = $json['msg'];
    }
    echo json_encode($json);
    exit;
}