<?php
$rows = $mydata->getraw('show global status');
$status = array();
$html = '<div class="table"><table><tr><th>值</th><th>指标</th><th>说明</th></tr>';
foreach($rows as $row)
{
    $name = $row['Variable_name'];
    $val = $row['Value'];
    $status[$name] = $val;
    if($val == 0)
        continue;
    $help = '';
    if($name == 'Aborted_clients')
        $help = '由于客户端没有正确关闭连接导致客户端终止而中断的连接数';
    else if($name == 'Aborted_connects')
        $help = '试图连接到MySQL服务器而失败的连接数';
    else if($name == 'Binlog_cache_disk_use')
        $help = '使用临时二进制日志缓存但超过binlog_cache_size值并使用临时文件来保存事务中的语句的事务数量';
    else if($name == 'Binlog_cache_use')
        $help = '使用临时二进制日志缓存的事务数量';
    else if($name == 'Bytes_received')
        $help = '从所有客户端接收到的字节数';
    else if($name == 'Bytes_sent')
        $help = '发送给所有客户端的字节数';
    else if($name == 'Compression')
        $help = '客户端与服务器之间只否启用压缩协议';
    else if($name == 'Connections')
        $help = '试图连接到(不管是否成功)MySQL服务器的连接数';
    else if($name == 'Created_tmp_disk_tables')
        $help = '服务器执行语句时在硬盘上自动创建的临时表的数量';
    else if($name == 'Created_tmp_files')
        $help = 'mysqld已经创建的临时文件的数量';
    else if($name == 'Created_tmp_tables')
        $help = '服务器执行语句时自动创建的内存中的临时表的数量。<br/><code>如果Created_tmp_disk_tables较大，你可能要增加tmp_table_size值使临时 表基于内存而不基于硬盘</code>';
    else if($name == 'Delayed_errors')
        $help = '用INSERT DELAYED写的出现错误的行数(可能为duplicate key)';
    else if($name == 'Delayed_insert_threads')
        $help = '使用的INSERT DELAYED处理器线程数';
    else if($name == 'Delayed_writes')
        $help = '写入的INSERT DELAYED行数';
    else if($name == 'Flush_commands')
        $help = '执行的FLUSH语句数';
    else if($name == 'Handler_commit')
        $help = '内部提交语句数';
    else if($name == 'Handler_delete')
        $help = '行从表中删除的次数';
    else if($name == 'Handler_discover')
        $help = 'MySQL服务器可以问NDB CLUSTER存储引擎是否知道某一名字的表。<br/><code>这被称作发现。Handler_discover说明通过该方法发现的次数</code>';
    else if($name == 'Handler_prepare')
        $help = '预编译次数';
    else if($name == 'Handler_read_first')
        $help = '索引中第一条被读的次数。<br/><code>如果较高，它建议服务器正执行大量全索引扫描；例如，SELECT col1 FROM foo，假定col1有索引</code>';
    else if($name == 'Handler_read_key')
        $help = '根据键读一行的请求数。<br/><code>如果较高，说明查询和表的索引正确</code>';
    else if($name == 'Handler_read_next')
        $help = '按照键顺序读下一行的请求数。<br/><code>如果你用范围约束或如果执行索引扫描来查询索引列，该值增加</code>';
    else if($name == 'Handler_read_prev')
        $help = '按照键顺序读前一行的请求数。<br/><code>该读方法主要用于优化ORDER BY</code>';
    else if($name == 'Handler_read_rnd')
        $help = '根据固定位置读一行的请求数。<br/><code>如果你正执行大量查询并需要对结果进行排序该值较高。</code><br/><code>你可能使用了大量需要MySQL扫描整个表的查询或你的连接没有正确使用键</code>';
    else if($name == 'Handler_read_rnd_next')
        $help = '在数据文件中读下一行的请求数。<br/><code>如果你正进行大量的表扫描，该值较高。通常说明你的表索引不正确或写入的查询没有利用索引</code>';
    else if($name == 'Handler_read_last')
        $help = '从主键的最后一个位置开始读取';
    else if($name == 'Handler_rollback')
        $help = '内部ROLLBACK语句的数量';
    else if($name == 'Handler_savepoint')
        $help = '在一个存储引擎放置一个保存点的请求数量。';
    else if($name == 'Handler_savepoint_rollback')
        $help = '在一个存储引擎的要求回滚到一个保存点数目';
    else if($name == 'Handler_update')
        $help = '在表内更新一行的请求数';
    else if($name == 'Handler_write')
        $help = '在表内插入一行的请求数';
    else if($name == 'Innodb_buffer_pool_pages_data')
        $help = '包含数据的页数(脏或干净)';
    else if($name == 'Innodb_buffer_pool_pages_dirty')
        $help = '当前的脏页数';
    else if($name == 'Innodb_buffer_pool_pages_flushed')
        $help = '要求清空的缓冲池页数';
    else if($name == 'Innodb_buffer_pool_pages_free')
        $help = '空页数';
    else if($name == 'Innodb_buffer_pool_pages_latched')
        $help = '在InnoDB缓冲池中锁定的页数。<br/><code>这是当前正读或写或由于其它原因不能清空或删除的页数</code>';
    else if($name == 'Innodb_buffer_pool_pages_misc')
        $help = '忙的页数，因为它们已经被分配优先用作管理，例如行锁定或适用的哈希索引。<br/><code>=Innodb_buffer_pool_pages_total – Innodb_buffer_pool_pages_free – Innodb_buffer_pool_pages_data</code>';
    else if($name == 'Innodb_buffer_pool_pages_total')
        $help = '缓冲池总大小（页数）';
    else if($name == 'Innodb_buffer_pool_read_ahead_rnd')
        $help = 'InnoDB初始化的“随机”read-aheads数。<br/><code>当查询以随机顺序扫描表的一大部分时发生</code>';
    else if($name == 'Innodb_buffer_pool_read_ahead_seq')
        $help = 'InnoDB初始化的顺序read-aheads数。<br/><code>当InnoDB执行顺序全表扫描时发生</code>';
    else if($name == 'Innodb_buffer_pool_read_requests')
        $help = 'InnoDB已经完成的逻辑读请求数';
    else if($name == 'Innodb_buffer_pool_reads')
        $help = '不能满足InnoDB必须单页读取的缓冲池中的逻辑读数量';
    else if($name == 'Innodb_buffer_pool_wait_free')
        $help = '一般情况，通过后台向InnoDB缓冲池写。<br/><code>但是，如果需要读或创建页，并且没有干净的页可用，则它还需要先等待页面清空。</code><br/><code>该计数器对等待实例进行记数。如果已经适当设置缓冲池大小，该值应小。</code>';
    else if($name == 'Innodb_buffer_pool_write_requests')
        $help = '向InnoDB缓冲池的写数量';
    else if($name == 'Innodb_data_fsyncs')
        $help = 'fsync()操作数';
    else if($name == 'Innodb_data_pending_fsyncs')
        $help = '当前挂起的fsync()操作数';
    else if($name == 'Innodb_data_pending_reads')
        $help = '当前挂起的读数';
    else if($name == 'Innodb_data_pending_writes')
        $help = '当前挂起的写数';
    else if($name == 'Innodb_data_read')
        $help = '至此已经读取的数据数量（字节）';
    else if($name == 'Innodb_data_reads')
        $help = '数据读总数量';
    else if($name == 'Innodb_data_writes')
        $help = '数据写总数量';
    else if($name == 'Innodb_data_written')
        $help = '至此已经写入的数据量（字节）';
    else if($name == 'Innodb_dblwr_pages_written')
        $help = '已经执行的双写操作数量';
    else if($name == 'Innodb_dblwr_writes')
        $help = '双写操作已经写好的页数';
    else if($name == 'Innodb_log_waits')
        $help = '我们必须等待的时间，因为日志缓冲区太小，我们在继续前必须先等待对它清空';
    else if($name == 'Innodb_log_write_requests')
        $help = '日志写请求数';
    else if($name == 'Innodb_log_writes')
        $help = '向日志文件的物理写数量';
    else if($name == 'Innodb_os_log_fsyncs')
        $help = '向日志文件完成的fsync()写数量';
    else if($name == 'Innodb_os_log_pending_fsyncs')
        $help = '挂起的日志文件fsync()操作数量';
    else if($name == 'Innodb_os_log_pending_writes')
        $help = '挂起的日志文件写操作';
    else if($name == 'Innodb_os_log_written')
        $help = '写入日志文件的字节数';
    else if($name == 'Innodb_page_size')
        $help = '编译的InnoDB页大小(默认16KB)。<br/><code>许多值用页来记数；页的大小很容易转换为字节</code>';
    else if($name == 'Innodb_pages_created')
        $help = '创建的页数';
    else if($name == 'Innodb_pages_read')
        $help = '读取的页数';
    else if($name == 'Innodb_pages_written')
        $help = '写入的页数';
    else if($name == 'Innodb_row_lock_current_waits')
        $help = '当前等待的待锁定的行数';
    else if($name == 'Innodb_row_lock_time')
        $help = '行锁定花费的总时间，单位毫秒';
    else if($name == 'Innodb_row_lock_time_avg')
        $help = '行锁定的平均时间，单位毫秒';
    else if($name == 'Innodb_row_lock_time_max')
        $help = '行锁定的最长时间，单位毫秒';
    else if($name == 'Innodb_row_lock_waits')
        $help = '一行锁定必须等待的时间数';
    else if($name == 'Innodb_rows_deleted')
        $help = '从InnoDB表删除的行数';
    else if($name == 'Innodb_rows_inserted')
        $help = '插入到InnoDB表的行数';
    else if($name == 'Innodb_rows_read')
        $help = '从InnoDB表读取的行数';
    else if($name == 'Innodb_rows_updated')
        $help = 'InnoDB表内更新的行数';
    else if($name == 'Key_blocks_not_flushed')
        $help = '键缓存内已经更改但还没有清空到硬盘上的键的数据块数量';
    else if($name == 'Key_blocks_unused')
        $help = '键缓存内未使用的块数量。你可以使用该值来确定使用了多少键缓存';
    else if($name == 'Key_blocks_used')
        $help = '键缓存内使用的块数量。<br/><code>该值为高水平线标记，说明已经同时最多使用了多少块</code>';
    else if($name == 'Key_read_requests')
        $help = '从缓存读键的数据块的请求数';
    else if($name == 'Key_reads')
        $help = '从硬盘读取键的数据块的次数。<br/><code>如果Key_reads较大，则Key_buffer_size值可能太小。可以用Key_reads/Key_read_requests计算缓存损失率。</code>';
    else if($name == 'Key_write_requests')
        $help = '将键的数据块写入缓存的请求数';
    else if($name == 'Key_writes')
        $help = '向硬盘写入将键的数据块的物理写操作的次数';
    else if($name == 'Last_query_cost')
        $help = '用查询优化器计算的最后编译的查询的总成本。<br/><code>用于对比同一查询的不同查询方案的成本。默认值0表示还没有编译查询。默认值是0。Last_query_cost具有会话范围。</code>';
    else if($name == 'Max_used_connections')
        $help = '服务器启动后已经同时使用的连接的最大数量';
    else if($name == 'Not_flushed_delayed_rows')
        $help = '等待写入INSERT DELAY队列的行数';
    else if($name == 'Open_files')
        $help = '打开的文件的数目';
    else if($name == 'Open_streams')
        $help = '打开的流的数量(主要用于记录)';
    else if($name == 'Open_table_definitions')
        $help = '缓存的.frm文件数量';
    else if($name == 'Open_tables')
        $help = '当前打开的表的数量';
    else if($name == 'Opened_files')
        $help = '文件打开的数量。<br/><code>不包括诸如套接字或管道其他类型的文件。 也不包括存储引擎用来做自己的内部功能的文件</code>';
    else if($name == 'Opened_table_definitions')
        $help = '已经缓存的.frm文件数量';
    else if($name == 'Opened_tables')
        $help = '已经打开的表的数量。<br/><code>如果Opened_tables较大，table_cache 值可能太小</code>';
    else if($name == 'Prepared_stmt_count')
        $help = '当前的预处理语句的数量。<br/><code> (最大数为系统变量: max_prepared_stmt_count) </code>';
    else if($name == 'Qcache_free_blocks')
        $help = '查询缓存内自由内存块的数量';
    else if($name == 'Qcache_free_memory')
        $help = '用于查询缓存的自由内存的数量';
    else if($name == 'Qcache_hits')
        $help = '查询缓存被访问的次数';
    else if($name == 'Qcache_inserts')
        $help = '加入到缓存的查询数量';
    else if($name == 'Qcache_lowmem_prunes')
        $help = '由于内存较少从缓存删除的查询数量';
    else if($name == 'Qcache_not_cached')
        $help = '非缓存查询数(不可缓存，或由于query_cache_type设定值未缓存)';
    else if($name == 'Qcache_queries_in_cache')
        $help = '登记到缓存内的查询的数量';
    else if($name == 'Qcache_total_blocks')
        $help = '查询缓存内的总块数';
    else if($name == 'Queries')
        $help = '服务器执行的请求个数，包含存储过程中的请求';
    else if($name == 'Questions')
        $help = '已经发送给服务器的查询的个数';
    else if($name == 'Rpl_status')
        $help = '失败安全复制状态';
    else if($name == 'Select_full_join')
        $help = '没有使用索引的联接的数量。如果该值不为0,你应仔细检查表的索引';
    else if($name == 'Select_full_range_join')
        $help = '在引用的表中使用范围搜索的联接的数量';
    else if($name == 'Select_range')
        $help = '在第一个表中使用范围的联接的数量。一般情况不是关键问题，即使该值相当大。';
    else if($name == 'Select_range_check')
        $help = '在每一行数据后对键值进行检查的不带键值的联接的数量。如果不为0，你应仔细检查表的索引。';
    else if($name == 'Select_scan')
        $help = '对第一个表进行完全扫描的联接的数量';
    else if($name == 'Slave_heartbeat_period')
        $help = '复制的心跳间隔';
    else if($name == 'Slave_open_temp_tables')
        $help = '从服务器打开的临时表数量';
    else if($name == 'Slave_received_heartbeats')
        $help = '从服务器心跳数';
    else if($name == 'Slave_retried_transactions')
        $help = '本次启动以来从服务器复制线程重试次数';
    else if($name == 'Slave_running')
        $help = '如果该服务器是连接到主服务器的从服务器，则该值为ON';
    else if($name == 'Slow_launch_threads')
        $help = '创建时间超过slow_launch_time秒的线程数';
    else if($name == 'Slow_queries')
        $help = '查询时间超过long_query_time秒的查询的个数';
    else if($name == 'Sort_merge_passes')
        $help = '排序算法已经执行的合并的数量。<br/><code>如果这个变量值较大，应考虑增加sort_buffer_size系统变量的值</code>';
    else if($name == 'Sort_range')
        $help = '在范围内执行的排序的数量';
    else if($name == 'Sort_rows')
        $help = '已经排序的行数';
    else if($name == 'Sort_scan')
        $help = '通过扫描表完成的排序的数量';
    else if($name == 'Table_locks_immediate')
        $help = '立即获得的表的锁的次数';
    else if($name == 'Table_locks_waited')
        $help = '不能立即获得的表的锁的次数。<br/><code>如果该值较高，并且有性能问题，你应首先优化查询，然后拆分表或使用复制</code>';
    else if($name == 'Threads_cached')
        $help = '线程缓存内的线程的数量';
    else if($name == 'Threads_connected')
        $help = '当前打开的连接的数量';
    else if($name == 'Threads_created')
        $help = '创建用来处理连接的线程数。<br/><code>如果Threads_created较大，你可能要增加thread_cache_size值。缓存访问率的计算方法Threads_created/Connections。</code>';
    else if($name == 'Threads_running')
        $help = '激活的（非睡眠状态）线程数';
    else if($name == 'Uptime')
        $help = '服务器已经运行的时间（以秒为单位）';
    else if($name == 'Uptime_since_flush_status')
        $help = '最近一次使用FLUSH STATUS 的时间（以秒为单位）';
    
    $html .= '<tr><td><div style="text-align:right;">'.$val.'</div></td><td><div>'.$name.'</div></td><td><div>'.$help.'</div></td></tr>';
}
$html .= '</table></div>';
$uptime = (int)$status['Uptime'];
if($uptime>0)
{
    echo 'QPS='.sprintf('%.2f',$status['Questions']/$uptime).'<br/>';
    echo 'TPS='.sprintf('%.2f',($status['Com_commit']+$status['Com_rollback'])/$uptime).'<br/>';
}

echo $html;