<?php
/* =================================================================================
 * 版权声明：保留开源作者及版权声明前提下，开源代码可进行修改及用于任何商业用途。
 * 开源作者：众产国际产业公会  http://ciy.cn/code
 * 版本：0.5.6
====================================================================================*/
/**
 * 应用数据层类库（单服版）
 * 注：单数据库服务器请用单服版，效率较高。多数据库服务器使用多服版。
 * 1、type: mysql     使用MySQLi引擎
 *    type: pdo       使用PDO引擎
 *    type: http      使用http透传（注意安全控制，只建议临时数据迁移时使用，正常业务不要使用）
 * 
 * 2、mode: default    [单服版]
 *        ...['host'] = localhost;
 * 
 *    mode: ns         [多服版] 一主多从模式。
 *        ...['conn'] = [0]主，其他从;   第一个为写服务器。支持加权选择空闲读服务器
 * 
 *    mode: ms         [多服版] 单库多主多从模式。
 *        ...['conn'][n]['master'] = true;  master=true为写服务器。支持加权选择空闲读服务器
 * 
 * 
 * getone       获取一条数据。          无数据返回null      出错返回false   判断is_array，确认数据有效
 * get1         获取一条第一列数据      无数据返回null      出错返回false   
 * get          获取数据集合。          无数据返回array()   出错返回false   判断is_array，确认数据有效
 * getraw       原始方法获取数据集合。  无数据返回array()   出错返回false   判断is_array，确认数据有效
 * set          更新或新增数据。        成功返回id          出错返回false
 * delete       删除及备份数据。        成功返回影响行数    出错返回false
 * execute      执行SQL语句。           成功返回影响行数    出错返回false
 * begin        开始事务                成功返回true        出错返回false
 * commit       事务提交                成功返回true        出错返回false
 * rollback     事务回滚                成功返回true        出错返回false
 * tran         事务回调                成功返回true        出错返回false
 * 
 * 变量名取名尽量用s尾缀区分，建议getone函数使用$xxrow，get函数使用$xxrows。
*/
class ciy_sql{
    public $tsmt;
    public $table;
    public $column;
    public $where;
    public $order;
    public $group;
    public $having;
    public $join;
    public $on;

    function __construct($table = '') {
        $this->tsmt = array();
        $this->table = $table;
        $this->column = '*';
        $this->where = '';
        $this->order = '';
        $this->group = '';
        $this->having = '';
        $this->join = '';
        $this->on = '';
    }
    function table($table)
    {
        $this->table = $table;
        return $this;
    }
    function column($column)
    {
        $this->column = $column;
        return $this;
    }
    function join($table,$join = 'inner')
    {
        $this->join = " {$join} join {$table}";
            return $this;
    }
    function on($query,$data = null,$op = '=')
    {
        $this->on.=$this->_query($query,$data,$op);
        return $this;
    }
    function where($query,$data = null,$op = '=')
    {
        $this->where.=$this->_query($query,$data,$op);
        return $this;
    }
    function wherenumber($query,$data)
    {
        if(empty($data))
            return $this;
        if($data[0] == '[' && $data[strlen($data)-1] == ']')
        {
            $data = substr($data,1,-1);
            if(strpos($data,'-') !== false)
            {
                $ds = explode('-', $data);
                $this->where.=$this->_query($query,$ds[0],'>=');
                $this->where.=$this->_query($query,$ds[1],'<=');
            }
            else
            {
                $this->where.=$this->_query($query,$data,'in');
            }
        }
        else if($data[0] == '>' || $data[0] == '<')
        {
            $op = '';
            if(is_numeric(@$data[1]))
            {
                $op = substr($data,0,1);
                $data = (int)substr($data,1);
            }
            else if(is_numeric(@$data[2]))
            {
                $op = substr($data,0,2);
                $data = (int)substr($data,2);
            }
            else
                return $this;
            $this->where.=$this->_query($query,$data,$op);
        }
        else
        {
            $data = (int)trim(trim($data),'=');
            $this->where.=$this->_query($query,$data);
        }
        return $this;
    }
    function _query($query,$data = null,$op = '=')//like 模糊，in 数组，其他 操作符
    {
        if(empty($query))
            return '';
        if($data === null)
            return ' and '.$query;
        if($data === '' && $op !== '<>')
            return '';
        if($op == 'like')
        {
            if($data[0] != '%' && $data[strlen($data)-1] != '%')
                $data = "%{$data}%";
            $query = " and {$query} like ?";
        }
        else if($op == 'in')
        {
            if($query == 'id')
            {
                $datas = explode(',',$data);
                $ids = array();
                foreach($datas as $d)
                    $ids[] = (int)$d;
                $data = implode(',',$ids);
            }
            else
            {
                //如接受外部参数，请在这里检查
            }
            $query = " and {$query} in ({$data})";
        }
        else if($op == 'm'){}
        else
            $query = " and {$query}{$op}?";
        $cnt = substr_count($query,'?');
        if($cnt > 1)
        {
            if(!is_array($data))
                return $this;
            if(count($data) != $cnt)
                return $this;
            foreach($data as $d)
                $this->tsmt[] = $d;
        }
        else if($cnt == 1)
            $this->tsmt[] = $data;
        return $query;
    }
    function order($order)
    {
        $chks = explode(',',$order);
        foreach($chks as $chk)
        {
            if(substr($chk,-5) == ' desc')
                $chk = trim(substr($chk,0,-5));
            if(!preg_match('/^[0-9a-zA-Z_-]+$/',$chk))
                return $this;
        }
        $this->order = $order;
        return $this;
    }
    function group($group)
    {
        //检查group是否字母数字_-
        $this->group = $group;
        return $this;
    }
    function having($query,$data = null,$op = '=')
    {
        $this->having.=$this->_query($query,$data,$op);
            return $this;
        }
    function buildsql()
    {
        //where group having order
        if(empty($this->table))
            return null;
        $sql = "select {$this->column} from {$this->table}";
        if(!empty($this->join) && !empty($this->on))
        {
            $sql .= $this->join;
            if(strpos($this->on,' and ') === 0)
                $this->on = substr($this->on,5);
            $sql .=' on '.$this->on;
        }
        $sql .= $this->buildwhere();
        if(!empty($this->group))
            $sql .=' group by '.$this->group;
        if(!empty($this->having))
        {
            if(strpos($this->having,' and ') === 0)
                $this->having = substr($this->having,5);
            $sql .=' having '.$this->having;
        }
        if(!empty($this->order))
            $sql .=' order by '.$this->order;
        return $sql;
    }
    function buildwhere()
    {
        if(empty($this->where))
            return '';
        if(strpos($this->where,' and ') === 0)
            $this->where = substr($this->where,5);
        return ' where '.$this->where;
    }
}
class ciy_data {
    public $linkmaster;//写服务器驱动层类实例。
    public $dbindex;//指定数据库集群，参数在config.php中配置，可配置多个数据库服务器集群。
    public $error;
    public $master;
    public $dataupdate;
    public $datainsert;
    
    function data($dataupdate)
    {
        $this->dataupdate = $dataupdate;
        return $this;
    }
    function datainsert($datainsert)
    {
        $this->datainsert = $datainsert;
        return $this;
    }
    function master($master)
    {
        $this->master = $master;
        return $this;
    }

    function __construct($dbindex = 1) {
        $this->linkmaster = false;
        $this->dbindex = $dbindex;
        $this->error = '';
    }
/**
 * 连接到服务器。
*/
    function connect($master) {
        if($this->linkmaster === false)
        {
            $cfg = ciy_config::getdb($this->dbindex);
            if($cfg['type'] == 'http')
            {
                require_once 'dbajax.php';
                $this->linkmaster = new ciy_dbajax();
                $this->linkmaster->connect($cfg['conn'][0]);
            }
            else if($cfg['type'] == 'mysql')
            {
                require_once 'mysql.php';
                $this->linkmaster = new ciy_mysql();
                $this->linkmaster->connect($cfg['conn'][0]);
            }
            else if($cfg['type'] == 'pdo')
            {
                //mode = ns/ms集群扩展，请引用集群data文件
                require_once 'pdo.php';
                $this->linkmaster = new ciy_pdo();
                $this->linkmaster->connect($cfg['conn'][0]);
            }
        }
        return $this->linkmaster;
    }
/**
 * 获取一条数据。
 * 返回array()，无数据返回null，出错返回false
 * csql         SQL拼接类
 * 例：
 * $csql = new ciy_sql();
 * $csql->...
 * $row = $mydata->getone($csql);
 * if(is_array($row))
 *      $row['username']...
 */
    function getone($csql) {
        $ret = $this->connect($this->master)->get(2,$csql->buildsql().' limit 0,1',$csql->tsmt);
        if($ret === false || $ret === null)
            return $this->errdata($ret);
        return $ret;
    }

/**
 * 获取一条数据的单个字段。
 * 返回变量，无数据返回null，出错返回false
 * csql         SQL拼接类
 * 例：
 * $csql = new ciy_sql();
 * $usercount = (int)$mydata->get1($csql);
 * $csql->column('truename');
 * $username = $mydata->get1($csql);
 */
    function get1($csql) {
        if($csql->column == '*')
            $csql->column = 'count(*)';
        $ret = $this->connect($this->master)->get(3,$csql->buildsql().' limit 0,1',$csql->tsmt);
        if($ret === false || $ret === null)
            return $this->errdata($ret);
        return $ret;
    }
/**
 * 获取数据集合。
 * 返回array()，出错返回false
 * 判断is_array，确认数据有效
 * csql         SQL拼接类
 * pageno       第pageno页，不分页传null，默认null
 * pagecount    每页pagecount条，默认20
 * rowcount     返回总数据条数(数据量大，查询耗时较高)，不填不查询。
 * 例：
 * $rows = $mydata->get($csql);//查询全部数据
 * $rows = $mydata->get($csql,1,$pagecount,$rowcount);
if(is_array($rows)){
     foreach($rows as $row)
         $row['username']...
}
 */
    function get($csql,$pageno = null,&$pagecount=20,&$rowcount = false) {//$pageno=null 查询全部
        if($pagecount === null)
            $pagecount = 20;
        $rowcount = 0;
        $sql = $csql->buildsql();
        if($pageno !== null)
        {
            if($pageno < 1)
                $pageno = 1;
            if($pagecount < 2)
                $pagecount = 20;
            $pageid = $pagecount * ($pageno - 1);
            $sql .= " limit {$pageid},{$pagecount}";
        }
        $ret = $this->connect($this->master)->get(1,$sql,$csql->tsmt);
        if($ret === false)
            return $this->errdata($ret);
        if($rowcount !== false)
        {
            $csql->column = 'count(*)';
            $rowcount = (int)$this->connect($this->master)->get(3,$csql->buildsql(),$csql->tsmt);
        }
        return $ret;
    }
/**
 * 原始参数获取数据集合。
 * 返回array()，出错返回false
 * sql          SQL prepare字符串
 * tsmt         array ?对应数据集
 * 例：
 * $row = $mydata->getraw('select * from xxx where id=?',[3]);
 */
    function getraw($sql,$tsmt) {
        $ret = $this->connect($this->master)->get(1,$sql,$tsmt);
        if($ret === false)
            return $this->errdata($ret);
        return $ret;
    }
/**
 * 更新或新增数据。
 * 成功返回id(insert返回新增的id，update读取第一条数据id)，失败返回false
 * csql         SQL拼接类
 * type         指定写入方式。   auto：自动识别[默认]；insert：只新增；update：只更新
 * 例：
$updata = array();
$updata['username'] = 'ciy.cn';
$updatainsert = array();
$updatainsert['addtimes'] = getnow();
$updatainsert['ip'] = getip();
$id = $mydata->data($updata)->set($csql);//更新
$id = $mydata->data($updata)->datainsert($updatainsert)->set($csql);//可能新增
 */
    function set($csql, $type = 'auto') {
        $ret = $this->connect(true)->set($csql, $type,$this->dataupdate,$this->datainsert);
        $this->dataupdate = null;
        $this->datainsert = null;
        if($ret === false)
            return $this->errdata($ret);
        $this->setaction = $this->connect(true)->setaction;
        return $ret;
    }
/**
 * 删除表数据，支持备份到_bak。（注意_bak建表时，应去掉id的自增属性）
 * 成功返回影响行数，失败返回false
 * csql         SQL拼接类
 * backup       false:直接删除；true：先备份到table_bak，再删除。
 * 例：
$affected = $mydata->delete($csql,true);//备份后删除
 */
    function delete($csql,$backup = false)
    {
        if($backup)
        {
            $fields = $this->connect(true)->get(1,'show full fields from '.$csql->table,array());
            $fieldlts = array();
            if($fields === false)
                return $this->errdata($fields);
            foreach ($fields as $row)
                $fieldlts[] = $row['Field'];
            $fieldlt = implode(',',$fieldlts);
            $sql = "insert into {$csql->table}_bak ({$fieldlt}) select {$fieldlt} from {$csql->table}".$csql->buildwhere();
            $ret = $this->connect(true)->execute($sql, $csql->tsmt);
            if($ret === false)
                return $this->errdata($ret);
        }
        $ret = $this->connect(true)->execute("delete from {$csql->table}".$csql->buildwhere(), $csql->tsmt);
        if($ret === false)
            return $this->errdata($ret);
        return $ret;
    }
/**
 * 执行SQL语句。
 * 成功返回影响行数，失败返回false
 * sql      SQL语句。
 * 例：
$affected = $mydata->execute('update users set username=? where id=?',['aaa',12]);
 */
    function execute($sql,$tsmt=array()) {
        $ret = $this->connect(true)->execute($sql,$tsmt);
        if($ret === false)
            return $this->errdata($ret);
        return $ret;
    }
    function begin() {
        $ret = $this->connect(true)->begin();
        if($ret === false)
            return $this->errdata($ret);
        return $ret;
    }
    function commit() {
        $ret = $this->connect(true)->commit();
        if($ret === false)
            return $this->errdata($ret);
        return $ret;
    }
    function rollback() {
        $ret = $this->connect(true)->rollback();
        if($ret === false)
            return $this->errdata($ret);
        return $ret;
    }
    function tran($func)
    {
        if (!($func instanceof Closure))
        {
            $this->error = '没有传递正确的闭包函数';
            return false;
        }
        $this->begin();
        $ret = false;
        try{
            $ret = $func();
        }catch(Exception $ex){
            $ret = false;
            $this->error = $ex->getMessage();
        }
        if ($ret === false)
            $this->rollback();
        else
            $this->commit();
        return $ret;
    }
    function errdata($ret)
    {
        $this->error = $this->linkmaster->error;
        return $ret;
    }
    function __destruct() {
    }
}