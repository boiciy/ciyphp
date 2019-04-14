<?php
/* =================================================================================
 * 版权声明：保留开源作者及版权声明前提下，开源代码可进行修改及用于任何商业用途。
 * 开源作者：众产国际产业公会  http://ciy.cn/code
 * 版本：0.5.3
====================================================================================*/
/**
 * 远程数据操作类库
 * 与透传执行文件 serverdata.php配合使用。
*/
class ciy_dbajax {
    public $url;
    public $user;
    public $token;
    public $error;
    function __construct() {
        $this->error = '';
    }
    function connect($conn) {
        $this->url = $conn['url'];
        $this->user = $conn['user'];
        $this->token = $conn['pass'];
        return true;
    }
    function gettoken()
    {
        $t = time().'.'.rand(1000,9999);
        $token = md5($this->token.$t);
        return "{$this->url}?json=true&_user={$this->user}&_t={$t}&_token={$token}";
    }
    function cpost($url,$data)
    {
        $context = [
            'http' => [
                'method' => 'POST',
                'header'  => 'Content-type: application/json',
                'content' => $data
                ]];
        return file_get_contents($url,false,stream_context_create($context));
    }
    function get($type,$query,$data) {
        $json = $this->cpost($this->gettoken() . '&func=get&type='.$type.'&query='.urlencode($query), json_encode($data));
        $json = json_decode($json,true);
        if(@$json['result'] != 'true')
            return $this->errmsg(null, @$json['msg']);
        if($type == 3)
        {
            if(count($json['data']) == 0)
                return null;
            return reset($json['data'][0]);
        }
        if($type == 2)
        {
            if(count($json['data']) == 0)
                return null;
            return $json['data'][0];
        }
        return $json['data'];
    }
    function set($csql, $type,$updata,$insertdata) {
        $data = array();
        $data['table'] = $csql->table;
        $data['where'] = $csql->where;
        $data['order'] = $csql->order;
        $data['column'] = $csql->column;
        $data['group'] = $csql->group;
        $data['tsmt'] = $csql->tsmt;
        $data['updata'] = $updata;
        $data['insertdata'] = $insertdata;
        $json = $this->cpost($this->gettoken() . '&func=set&type='.$type, json_encode($data));
        $json = json_decode($json,true);
        if(@$json['result'] != 'true')
            return $this->errmsg(null, @$json['msg']);
        $this->setaction = @$json['setaction'];
        return $json['data'];
    }
    function execute($query,$data) {
        $json = $this->cpost($this->gettoken() . '&func=execute&query='.urlencode($query), json_encode($data));
        $json = json_decode($json,true);
        if(@$json['result'] != 'true')
            return $this->errmsg(null, @$json['msg']);
        return $json['data'];
    }
    function errmsg($ret,$msg)
    {
        $this->error = $msg;
        return $ret;
    }
    function __destruct() {
    }
}
