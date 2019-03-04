<?php
/* =================================================================================
 * 版权声明：保留开源作者及版权声明前提下，开源代码可进行修改及用于任何商业用途。
 * 开源作者：众产国际产业公会  http://ciy.cn/code
 * 版本：0.5.5
====================================================================================*/
/**
 * PDO操作类库
 * 支持PHP7
 * 建议使用参数绑定模式
*/
class ciy_pdo {
    public $link;
    public $isconnected;
    public $error;
    public $setaction;
    function __construct() {
        $this->isconnected = false;
        $this->error = '';
    }
    function connect($conn) {
        if($this->isconnected)
            return true;
        if (!isset($this->link)) {
            try {
                $timeout = 5;
                if(isset($conn['timeout']))
                    $timeout = (int)$conn['timeout'];
                $charset = 'utf8';
                if(isset($conn['charset']))
                    $charset = $conn['charset'];
                $persistent = false;
                if(isset($conn['persistent']))
                    $persistent = (bool)$conn['persistent'];
                $opts = array(
                    PDO::ATTR_TIMEOUT => $timeout,
                    PDO::ATTR_PERSISTENT=>$persistent,
                    PDO::ATTR_ERRMODE=>PDO::ERRMODE_SILENT,
                    PDO::MYSQL_ATTR_INIT_COMMAND=>"SET CHARACTER SET {$charset}"
                    );
                $this->link = new PDO($conn['dsn'], $conn['user'],$conn['pass'],$opts);
            } catch (PDOException $e) {
                return $this->errsql(false, 'PDO连接失败:' . $e->GetMessage());
            }
        }
        $this->isconnected = true;
        return true;
    }
    function disconnect() {
        if($this->isconnected)
            $this->link = null;
        $this->isconnected = false;
        return true;
    }
    function get($type,$query,$data) {
        if(!$this->isconnected)
            return false;
        $rs = $this->_prepare($query,$data);
        if($rs === false)
            return false;
        if($type == 1)//返回全部数据
        {
            $retdata = $rs->fetchAll(PDO::FETCH_ASSOC);
            $rs->closeCursor();
            if($retdata === false)
                return $this->errsql(array(), "表1[{$query}]数据不存在");
            return $retdata;
        }
        else if($type == 2)//返回单行数据
        {
            $retdata = $rs->fetch(PDO::FETCH_ASSOC);
            $rs->closeCursor();
            if($retdata === false)
                return $this->errsql(null, "表2[{$query}]数据不存在");
            return $retdata;
        }
        else if($type == 3)//返回第一行第一列数据
        {
            $retdata = $rs->fetchColumn();
            $rs->closeCursor();
            if($retdata === false)
                return $this->errsql(null, "表3[{$query}]数据不存在");
            return $retdata;
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
            return $this->link->lastInsertId();
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
        $cnt = $rs->rowCount();
        $rs->closeCursor();
        return $cnt;
    }
    function begin() {
        if(!$this->isconnected)
            return false;
        $this->link->setAttribute(PDO::ATTR_AUTOCOMMIT,0);
        $execute = $this->link->beginTransaction();
        if ($execute === false)
            return $this->errsql(false, 'begin失败:'.$this->pdoerr());
        return $execute;
    }
    function commit() {
        if(!$this->isconnected)
            return false;
        $execute = $this->link->commit();
        $this->link->setAttribute(PDO::ATTR_AUTOCOMMIT,1);
        if ($execute === false)
            return $this->errsql(false, 'commit失败:'.$this->pdoerr());
        return $execute;
    }
    function rollback() {
        if(!$this->isconnected)
            return false;
        $execute = $this->link->rollback();
        $this->link->setAttribute(PDO::ATTR_AUTOCOMMIT,1);
        if ($execute === false)
            return $this->errsql(false, 'rollback失败:'.$this->pdoerr());
        return $execute;
    }
    function pdoerr()
    {
        $err = $this->link->errorInfo();
        return $this->link->errorCode().':'.@$err[1].':'.@$err[2];
    }
    function errsql($ret,$msg)
    {
        $this->error = $msg;
        return $ret;
    }
    function _prepare($query,$data){
        $rs = $this->link->prepare($query);
        if($rs === false)
            return $this->errsql(false, "SQL预处理出错[{$query}]:".$this->pdoerr());
        $execute = $rs->execute($data);
        if ($execute === false)
            return $this->errsql(false, "SQL执行出错[{$query}]:".$this->pdoerr());
        return $rs;
    }
}