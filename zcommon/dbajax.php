<?php
/* =================================================================================
 * 版权声明：保留开源作者及版权声明前提下，开源代码可进行修改及用于任何商业用途。
 * 开源作者：众产国际产业公会  http://ciy.cn/code
 * 版本：0.5.2
====================================================================================*/
/**
 * 远程数据操作类库
 * 与透传执行文件 dbdata.php配合使用。
*/
class ciy_dbajax {
    public $url;
    public $error;
    function __construct() {
        $this->error = '';
    }
    function connect($url,$user,$pass) {
        $this->url = $url.'?json=true&_user='.$user.'&_pass='.$pass;
        return true;
    }
    function getone($table, $where,$order='',$column='*') {
        $json = file_get_contents($this->url . '&func=getone&table='.urlencode($table).'&where='.urlencode($where).'&column='.urlencode($column).'&order='.urlencode($order));
        $json = json_decode($json,true);
        if(@$json['result'] != 'true')
            return $this->errmsg(null, @$json['msg']);
        if(count($json['data']) == 0)
            return $this->errmsg(null, '数据不存在');
        return $json['data'][0];
    }
    function getonescalar($table, $where, $column,$order) {
        $json = file_get_contents($this->url . '&func=sqlscalar&table='.urlencode($table).'&where='.urlencode($where).'&column='.urlencode($column).'&order='.urlencode($order));
        $json = json_decode($json,true);
        if(@$json['result'] != 'true')
            return $this->errmsg(false, @$json['msg']);
        return $json['data'];
    }
    function get($pageno,$pagecount, $table, $where, $order, $column) {
        $json = file_get_contents($this->url . '&func=sqlget&pageno='.$pageno.'&pagecount='.$pagecount.'&table='.urlencode($table).'&where='.urlencode($where).'&order='.urlencode($order).'&column='.urlencode($column));
        $json = json_decode($json,true);
        if(@$json['result'] != 'true')
            return $this->errmsg(false, @$json['msg']);
        return $json['data'];
    }
    function set($updata, $table, $where, $type, $insertdata) {
        $url = $this->url . '&func=sqlset&table='.urlencode($table).'&where='.urlencode($where).'&type='.urlencode($type);
        $url .= '&updata='.urlencode(json_encode($updata));
        if($insertdata !== null)
            $url .= '&insertdata='.urlencode(json_encode($insertdata));
        $json = file_get_contents($url);
        $json = json_decode($json,true);
        if(@$json['result'] != 'true')
            return $this->errmsg(false, @$json['msg']);
        return $json['data'];
    }
    function delete($table, $where, $type) {
        $json = file_get_contents($this->url . '&func=sqldelete&table='.urlencode($table).'&where='.urlencode($where));
        $json = json_decode($json,true);
        if(@$json['result'] != 'true')
            return $this->errmsg(false, @$json['msg']);
        return $json['data'];
    }
    function execute($sql) {
        $json = file_get_contents($this->url . '&func=sqlexecute&sql='.urlencode($sql));
        $json = json_decode($json,true);
        if(@$json['result'] != 'true')
            return $this->errmsg(false, @$json['msg']);
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
