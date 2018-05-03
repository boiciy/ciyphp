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
 * 如需使用PDO连接，请修改data.php，引用pdo.php，实例化new ciy_pdo();
 * 可以使用join，但不建议使用join等关系数据库功能。单一数据库服务器下容易实现某些功能，但不利于后期分库分表迭代。单独查询消耗的CPU非常少。
 * 可以使用union，但不建议使用，建议编程实现。
*/
class ciy_mysql {
    public $link;
    public $isconnected;
    public $error;
    public $sql;
    function __construct() {
        $this->isconnected = false;
        $this->error = '';
        $this->sql = '';
    }
    function connect($type,$host,$user,$pass,$name,$port,$charset) {
        if($this->isconnected)
            return true;
        if (!isset($this->link)) {
            $this->link = new mysqli();
        }
        if($this->isconnected !== true){
            $this->link->connect($host,$user,$pass,$name,$port);
            if($this->link->connect_errno)
                return $this->errmysql(false, $this->link->connect_error . '[' . __class__ . ':' . __FUNCTION__ . ']');
            $this->link->set_charset($charset);
            $this->isconnected = true;
            return true;
        }
    }
    function disconnect() {
        if($this->isconnected)
            $this->link->close();
        $this->isconnected = false;
        return true;
    }
    function getone($table, $where,$order,$column) {
        if(empty($column))
            $column = '*';
        $this->sql = 'select '.$column.' from '.$table;
        if(!empty($where))
            $this->sql.=' where '.$where;
        if(!empty($order))
            $this->sql.=' order by '.$order;
        $this->sql.=' limit 0,1';
        $rs = $this->link->query($this->sql);
        if($rs === false)
            return $this->errmysql(false, $this->link->error . '[' . __class__ . ':' . __FUNCTION__ . ']');
        $rowparam = $rs->fetch_assoc();
        if ($rowparam === null)
            return $this->errmysql(null, '表['.$table.']['.$where.']数据不存在'. '[' . __class__ . ':' . __FUNCTION__ . ']');
        $rs->free_result();
        return $rowparam;
    }
    function getonescalar($table, $where, $column,$order) {
        if(empty($column))
            $column = 'count(*)';
        $this->sql = 'select '.$column.' from '.$table;
        if(!empty($where))
            $this->sql.=' where '.$where;
        if(!empty($order))
            $this->sql.=' order by '.$order;
        $this->sql.=' limit 0,1';
        $rs = $this->link->query($this->sql);
        if($rs === false)
            return $this->errmysql(false, $this->link->error . '[' . __class__ . ':' . __FUNCTION__ . ']');
        $rowparam = $rs->fetch_row();
        if ($rowparam === null)
            return $this->errmysql(null, '表['.$table.']['.$where.']数据不存在'. '[' . __class__ . ':' . __FUNCTION__ . ']');
        $rs->free_result();
        return $rowparam[0];
    }
    function get($pageno, $pagecount, $table, $where, $order, $column) {
        if(stripos($table,'sql') !== 0)
        {
            if(empty($column))
                $column = '*';
            $this->sql = 'select '.$column.' from '.$table;
            if(!empty($where))
                $this->sql.=' where '.$where;
            if(!empty($order))
                $this->sql.=' order by '.$order;
            if($pageno > 0 && $pagecount > 0)
                $this->sql.=' limit ' . $pagecount * ($pageno - 1) . ',' . $pagecount;
        }
        else
            $this->sql = substr($table,3);
        $rs = $this->link->query($this->sql);
        if($rs === false)
            return $this->errmysql(false, 'get数据时出错'.$this->link->error . '[' . __class__ . ':' . __FUNCTION__ . ']');
        $rows = array();
        while ($row = $rs->fetch_assoc())
            $rows[] = $row;
        $rs->free_result();
        return $rows;
    }
    function set($updata, $table, $where, $type, $insertdata) {
        //auto时，先查询是否存在，存在update，不存在insert
        $id = 0;
        if (empty($where) && $type == 'auto')//防止auto模式下，不指定where条件，整表更新危险。
            $type = 'insert';
        if ($type === 'auto') {
            $row = $this->getone($table, $where,'','');
            if ($row === null)
                $type = 'insert';
            else
            {
                $type = 'update';
                $id = (int)@$row['id'];
            }
        }
        if ($type === 'insert') {
            $field = '';
            $value = '';
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
                else if(is_null($val))
                    $value.='null';
                else
                    $value.='\'' . addslashes($val) . '\'';
            }
            $this->sql = 'insert into ' . $table . ' (' . $field . ') values (' . $value . ')';
            $execute = $this->link->query($this->sql);
            if ($execute === false)
                return $this->errmysql(false, $this->link->error . '[' . __class__ . ':' . __FUNCTION__ . ']');
            return $this->link->insert_id;
        }
        else {
            $set = '';
            foreach ($updata as $key => $val) {
                if ($key == 'id')
                    continue;
                if (!empty($set))
                    $set.=',';
                if(is_array($val))
                    $set.='`'.$key . '`='. $val[0];
                else if(is_null($val))
                    $set.='`'.$key . '`=null';
                else
                    $set.='`'.$key . '`=\'' . addslashes($val) . '\'';
            }
            $this->sql = 'update ' . $table . ' set ' . $set;
            if (!empty($where))
                $this->sql.=' where ' . $where;
            $execute = $this->link->query($this->sql);
            if ($execute === false)
                return $this->errmysql(false, $this->link->error . '[' . __class__ . ':' . __FUNCTION__ . ']');
            if($id == 0)
                $id = (int)$this->getonescalar($table, $where,'id','');
            return $id;
        }
    }
    function delete($table, $where, $type) {
        $result = true;
        $event = false;
        if(empty($where))
            return $this->errmysql(false, '不能无where参数删除，如需全部删除，请使用deleteall.');
            
        if ($type === 'backup') {
            $fieldrs = $this->link->query('show full fields from '.$table);
            if($fieldrs === false)
                return $this->errmysql(false, '字段获取失败.'.$this->link->error . '[' . __class__ . ':' . __FUNCTION__ . ']');
            $fieldlt = '';
            while ($fieldrow = $fieldrs->fetch_assoc())
            {
                if(!empty($fieldlt))
                    $fieldlt.=','.$fieldrow['Field'];
                else
                    $fieldlt.=$fieldrow['Field'];
            }
            $fieldrs->free_result();
            $event = true;
            $this->link->query('BEGIN');
            $sql = 'insert into '.$table.'_bak ('.$fieldlt.') select '.$fieldlt.' from '.$table;
            if($where != 'deleteall')
                $sql.=' where ' . $where;
            $execute = $this->link->query($sql);
            if($execute === false)
                $result = $this->errmysql(false, '备份时插入失败.'.$this->link->error . '[' . __class__ . ':' . __FUNCTION__ . ']');
        }
        $this->sql = 'delete from '.$table;
        if($where != 'deleteall')
            $this->sql.=' where ' . $where;
        $execute = $this->link->query($this->sql);
        if($execute === false)
            $result = $this->errmysql(false, '删除时失败.'.$this->link->error . '[' . __class__ . ':' . __FUNCTION__ . ']');
        if ($event === true) {
            if ($result) {
                $this->link->query('COMMIT');
            } else {
                $this->link->query('ROLLBACK');
            }
        }
        return $this->link->affected_rows;
    }
    function execute($sql) {
        $execute = $this->link->query($sql);
        if ($execute === false)
            return $this->errmysql(null, 'execute执行时失败.'.$this->link->error . '[' . __class__ . ':' . __FUNCTION__ . ']');
        return $this->link->affected_rows;
    }
    function errmysql($ret,$msg)
    {
        $this->error = $msg;
        return $ret;
    }
    function __destruct() {
    }
}
