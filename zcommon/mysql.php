<?php
/* =================================================================================
 * 版权声明：保留开源作者及版权声明前提下，开源代码可进行修改及用于任何商业用途。
 * 开源作者：众产国际产业公会  http://ciy.cn/code
 * 版本：0.5.2
====================================================================================*/
/**
 * mysql操作类库
 * 支持PHP7
 * 如需pconnect长连接，请适当修改connect函数。
 * 可以使用join，但不建议使用join等关系数据库功能。单一数据库服务器下容易实现某些功能，但不利于后期分库分表迭代。
 * 可以使用union，但不建议使用，建议编程实现。
*/
class ciy_mysql {
    public $link;
    public $isconnected;
    public $error;
    public $setaction;
    private $checkrs = true;
    function __construct() {
        $this->isconnected = false;
        $this->error = '';
    }
    function connect($conn) {
        if($this->isconnected)
            return true;
        if (!isset($this->link))
            $this->link = new mysqli();
        $timeout = 5;
        if(isset($conn['timeout']))
            $timeout = (int)$conn['timeout'];
        $charset = 'utf8';
        if(isset($conn['charset']))
            $charset = $conn['charset'];
        $port = 3306;
        if(isset($conn['port']))
            $port = (int)$conn['port'];
        $this->link->options(MYSQLI_OPT_CONNECT_TIMEOUT,$timeout);
        $this->link->connect($conn['host'],$conn['user'],$conn['pass'],$conn['name'],$port);
        if($this->link->connect_errno>0)
            return $this->errsql(false, 'mysqli连接失败:' . $this->link->connect_error);
        $this->link->set_charset($charset);
        $this->isconnected = true;
        return true;
    }
    function disconnect() {
        if($this->isconnected)
            $this->link->close();
        $this->isconnected = false;
        return true;
    }
    function get($type,$query,$data) {
        if(!$this->isconnected)
            return false;
        $rs = $this->_prepare($query,$data);
        if($rs === false)
            return false;
        if($this->checkrs)
        {
            if(!method_exists($rs,'get_result'))
                return $this->errsql(false, '服务器不支持mysqlnd模块，请编译安装该模块');
            $this->checkrs = false;
        }
        $result = $rs->get_result();
        if($result === false)
            return $this->errsql(false, 'SQL获取结果集出错:'.$this->link->error);
        if($type == 1)//返回全部数据
        {
            $retdata = array();
            while($row = $result->fetch_assoc())
                $retdata[] = $row;
            $result->free();
            $rs->free_result();
            return $retdata;
        }
        else if($type == 2)//返回单行数据
        {
            $retdata = $result->fetch_assoc();
            $result->free();
            $rs->free_result();
            return $retdata;
        }
        else if($type == 3)//返回第一行第一列数据
        {
            $retdata = $result->fetch_row();
            $result->free();
            $rs->free_result();
            if($retdata == null)
                return null;
            return $retdata[0];
        }
        return null;
    }
    function set($csql, $type,$updata,$insertdata) {
        if(!$this->isconnected)
            return false;
        //auto时，先查询是否存在，存在update，不存在insert
        $id = 0;
        if (empty($csql->where) && $type == 'auto')//防止auto模式下，不指定where条件，整表更新危险。
            $type = 'insert';
        if ($type === 'auto') {
            $row = $this->get(2,$csql->buildsql(),$csql->tsmt);
            if ($row === null)
                $type = 'insert';
            else
            {
                $type = 'update';
                $id = (int)@$row['id'];
            }
        }
        $data = array();
        if ($type === 'insert') {
            $field = '';
            $value = '';
            if (!is_array($updata))
                return false;
            if(is_array($insertdata))
                $updata = $insertdata + $updata;
            foreach ($updata as $key => $val) {
                if (!empty($field))
                    $field.=',';
                $field.='`'.$key.'`';
                if (!empty($value))
                    $value.=',';
                if(is_array($val))
                    $value.=$val[0];
                else
                {
                    $value.='?';
                    $data[] = $val;
                }
            }
            $execute = $this->execute("insert into {$csql->table} ({$field}) values ({$value})",$data);
            if ($execute === false)
                return false;
            $this->setaction = 'insert';
            return $this->link->insert_id;
        }
        else {
            $set = '';
            if (!is_array($updata))
                return false;
            foreach ($updata as $key => $val) {
                if ($key == 'id')
                {
                    $id = (int)$val;
                    continue;
                }
                if (!empty($set))
                    $set.=',';
                if(is_array($val))
                    $set.="`{$key}`=". $val[0];
                else
                {
                    $set.="`{$key}`=?";
                    $data[] = $val;
                }
            }
            $sql = "update {$csql->table} set {$set}".$csql->buildwhere();
            $execute = $this->execute($sql,array_merge($data,$csql->tsmt));
            if ($execute === false)
                return false;
            if($id == 0)
                $id = $execute;
            $this->setaction = 'update';
            return $id;
        }
    }
    function execute($query,$data) {
        if(!$this->isconnected)
            return false;
        $rs = $this->_prepare($query,$data);
        if($rs === false)
            return false;
        $rs->free_result();
        return $this->link->affected_rows;
    }
    function begin() {
        if(!$this->isconnected)
            return false;
        return $this->link->query('BEGIN');
    }
    function commit() {
        if(!$this->isconnected)
            return false;
        return $this->link->query('COMMIT');
    }
    function rollback() {
        if(!$this->isconnected)
            return false;
        return $this->link->query('ROLLBACK');
    }
    function errsql($ret,$msg)
    {
        $this->error = $msg;
        return $ret;
    }
    function refvalues($arr){
        if (version_compare(PHP_VERSION, '5.3.0') >= 0) {
            $refs = array();
            foreach($arr as $key => $value)
                $refs[$key] = &$arr[$key];
            return $refs;
        }
        return $arr;
    }
    function _prepare($query,$data)
    {
        $rs = $this->link->prepare($query);
        if($rs === false)
            return $this->errsql(false, 'SQL预处理出错:'.$this->link->error);
        if($rs->param_count != count($data))
            return $this->errsql(false, "参数数量不对:param_count={$rs->param_count},count(data)=".count($data));
        if (count($data)>0) {
            $sv = '';
            foreach($data as $d)
            {
                if(is_double($d))
                    $sv.='d';
                else if(is_long($d))
                    $sv.='i';
                else
                    $sv.='s';
            }
            array_unshift($data, $sv);
            call_user_func_array(array($rs, 'bind_param'),$this->refvalues($data));
        }
        $execute = $rs->execute();
        if($execute === false)
            return $this->errsql(false, 'SQL执行出错:'.$rs->error);
        return $rs;
    }
}
