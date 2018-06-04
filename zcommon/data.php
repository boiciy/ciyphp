<?php
/* =================================================================================
 * 版权声明：保留开源作者及版权声明前提下，开源代码可进行修改及用于任何商业用途。
 * 开源作者：众产国际产业公会  http://ciy.cn/code
 * 版本：0.5.2
====================================================================================*/
/**
 * 应用数据层类库   分两个库文件。调用方式一致。单数据库服务器请用单服版，效率较高。多数据库服务器使用多服版。
 * 1、type: mysql       [单服版]访问单台Mysql(Mariadb)服务器
 * 
 * 2、type: mysql-ms    [多服版]Mysql(Mariadb)支持一主多从和多主多从，不分库的读写分离模式。
 *        ...['host'] = array('m_ip','ip2','ip3');   m_*为写服务器。默认随机连接一个读服务器。
 * 
 * 3、type: mysql-tab   [多服版]Mysql(Mariadb)支持多主多从，分库的读写分离模式。
 *        ...['host'] = array(array('m_ip','m_ip2','ip3'),array('m_ip','ip2','ip3'));m_*为写服务器。支持加权选择空闲读服务器
 * 
 * 4、type: pgsql       [单服版]访问单台PostgreSQL服务器
 * 
 * 
 * getone       table,where,order,column获取一条数据。          无数据返回null。    出错返回false   判断is_array，确认数据有效
 * getonescalar table,where,column,order获取一条数据的单个字段。无数据返回null。    出错返回false   用(int)  或  .''  强制转换类型直接使用数据
 * get          table,where,order,column获取数据集合。          无数据返回array()。 出错返回false   判断is_array，确认数据有效  可以用sqlshow查询
 * set          更新或新增数据。        成功返回id。        出错返回false
 * delete       删除及备份数据。        成功返回影响行数。  出错返回false
 * execute      执行SQL语句。           成功返回影响行数。  出错返回false
 * 
 * 变量名取名，建议getone函数使用$xxrow，get函数使用$xxrows。
*/
class ciy_data {
    public $linkmaster;//写服务器驱动层类实例。
    public $dbindex;//指定数据库集群，参数在config.php中配置，可配置多个数据库服务器集群。
    public $error;

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
            if(stripos($cfg['host'],'http') === 0)
            {
                
                require_once 'dbajax.php';
                $this->linkmaster = new ciy_dbajax();
                $this->linkmaster->connect($cfg['host'],$cfg['user'], $cfg['pass']);
            }
            else
            {
                require_once 'mysql.php';
                $this->linkmaster = new ciy_mysql();
                $this->linkmaster->connect($cfg['type'],$cfg['host'], $cfg['user'], $cfg['pass'], $cfg['name'], $cfg['port'],$cfg['charset']);
            }
        }
        return $this->linkmaster;
    }
/**
 * 获取一条数据。
 * 返回array()，无数据返回null，出错返回false
 * 例：
 * $row = $mydata->getone('user','id=1');
 * if(is_array($row))
 * {
 *      $row['username']
 * }
 */
    function getone($table, $where='', $order='', $column='',$master=false) {
        $ret = $this->connect($master)->getone($table, $where,$order,$column);
        if($ret === false || $ret === null)
            return $this->errdata($ret,$master);
        return $ret;
    }

/**
 * 获取一条数据的单个字段。
 * 返回变量，无数据返回null，出错返回false
 * 用(int)  或  .''  强制转换类型直接使用数据
 * 例：
 * $usercount = (int)$mydata->getonescalar('user','');
 * $username = ''.$mydata->getonescalar('user','id=20','username');
 */
    function getonescalar($table, $where='', $column='', $order='',$master=false) {
        $ret = $this->connect($master)->getonescalar($table, $where, $column,$order);
        if($ret === false || $ret === null)
            return $this->errdata($ret,$master);
        return $ret;
    }
/**
 * 获取数据集合。
 * 返回array()，出错返回false
 * 判断is_array，确认数据有效
 * 特殊的，join/union/show等，可以直接使用SQL语句。
 * pageno       第pageno页
 * pagecount    每页pagecount条
 * table        表名
 * where        查询条件（不带where），请注意SQL注入的过滤。
 * order        排序条件（不带order by）
 * column       返回字段列表，默认 *
 * master       true返回写数据库数据，false返回读数据库数据
 * 例：
$rows = $mydata->get(1,20,'user');
if(is_array($rows))
{
     foreach($rows as $row)
     {
         $row['username']
     }
}
$rows = $mydata->get(1,20,'sqlshow full fields from table'); //执行 show full fields from table，获取表各个字段数据
 */
    function get($pageno,$pagecount, $table, $where='', $order = '', $column = '',$master=false) {
        $ret = $this->connect($master)->get($pageno,$pagecount, $table, $where, $order, $column);
        if($ret === false)
            return $this->errdata($ret,$master);
        return $ret;
    }
/**
 * 更新或新增数据。
 * 成功返回id(insert返回新增的id，update读取第一条数据id)，失败返回false
 * updata       待更新数据（数组）。
 * table        表名
 * where        查询条件（不带where），请注意SQL注入的过滤。
 * type         指定写入方式。   auto：自动识别[默认]；insert：只新增；update：只更新
 * insertdata   新增时附加的数据（数组），默认null。
 * 例：
$updata = array();
$updata['username'] = 'ciy.cn';
$id = $mydata->set($updata, 'users','id=20','update');//更新
$newid = $mydata->set($updata, 'users','id=20');//更新或新增
$updatainsert = array();
$updatainsert['addtimes'] = getnow();
$updatainsert['ip'] = getip();
$newid = $mydata->set($updata, 'users','id=20','auto',$updatainsert);//更新或新增
 */
    function set($updata, $table, $where, $type = 'auto', $insertdata = null) {
        $ret = $this->connect(true)->set($updata, $table, $where, $type, $insertdata);
        if($ret === false)
            return $this->errdata($ret,true);
        return $ret;
    }
/**
 * 删除表数据，支持备份到_bak。
 * 注意_bak建表时，应去掉id的自增属性。
 * 成功返回影响行数，失败返回false
 * table        表名
 * where        查询条件（不带where），请注意SQL注入的过滤。
 * type         指定删除方式。   空：直接删除；backup：先备份到table_bak，再删除。
 * 例：
$affected = $mydata->delete('users','id=20','backup');//备份后删除
 */
    function delete($table, $where, $type = '') {//backup
        $ret = $this->connect(true)->delete($table, $where, $type);
        if($ret === false)
            return $this->errdata($ret,true);
        return $ret;
    }
/**
 * 执行SQL语句。
 * 成功返回影响行数，失败返回false
 * sql      SQL语句。
 * 例：
$affected = $mydata->execute('update users set username=\'abc\' where id=30');
 */
    function execute($sql) {
        $ret = $this->connect(true)->execute($sql);
        if($ret === false)
            return $this->errdata($ret,true);
        return $ret;
    }
    function errdata($ret,$master)
    {
        $this->error = $this->linkmaster->error;
        return $ret;
    }
    function __destruct() {
    }
}