<?php
$mydata = new ciy_data();
$table = 'd_test';
ciy_runJSON();
$msql = new ciy_sql($table);
$msql->where('truename like',get('truename'));
$msql->wherenumber('scores',get('scores'));
$val = explode('~',get('activetime'));
if(count($val) == 2)
{
    $msql->where('activetime>=',strtotime(trim($val[0].' 00:00:00')));
    $msql->where('activetime<=',strtotime(trim($val[1].' 23:59:59')));
}
$msql->order(get('order','id desc'));
ciy_runExcelxml($msql);
ciy_runExcelcsv($msql);
$pageno = getint('pageno', 1);$pagecount = 20;
$msql->limit($pageno,$pagecount);
$rows = $mydata->get($msql,$mainrowcount);
function excel_cc($msql)//Excel CSV数据导出函数，ciy_runCSV()调用。
{
    global $mydata;
    global $table;
    $data = array();
    $filename = get('prefix').'_' . date('Y-m-d_h-i-s');
    $field = array();
    $field[] = array('name'=>'编号','width'=>50,'style'=>'s1');
    $field[] = array('name'=>'姓名','width'=>80,'style'=>'s1');
    $field[] = array('name'=>'分数','width'=>60,'style'=>'s1','type'=>'Number');
    $field[] = array('name'=>'日期','width'=>150,'style'=>'s0');
    $field[] = array('name'=>'IP','width'=>100,'style'=>'s1');
    $rows = $mydata->get($msql);
    $count = count($rows);
    for($i=0;$i<$count;$i++)
    {
        $data[] = array(' '.enid($rows[$i]['id']),$rows[$i]['truename'],$rows[$i]['scores'],date('Y-m-d H:i',$rows[$i]['addtimes']),long2ip($rows[$i]['ip']));
    }
    $style = array();//Default默认样式，ts默认列头样式
    $style['Default'] = '<Alignment ss:Vertical="Center"/>
   <Font ss:FontName="微软雅黑" x:Family="Swiss" ss:Size="12"/>
   <Interior/>
   <NumberFormat/>
   <Protection/>';
    $style['s0'] = '<Alignment ss:Horizontal="Center"/><Font ss:Color="#003300" ss:Bold="1"/><NumberFormat ss:Format="yyyy/m/d\ h:mm;@"/>';
    $style['s1'] = '<Alignment ss:Horizontal="Center"/>';
    $style['ts'] = '<Alignment ss:Horizontal="Center"/><Interior ss:Color="#0FF000" ss:Pattern="Solid"/><Borders>
    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1"/>
   </Borders>';
    $style['cap'] = '<Alignment ss:Horizontal="Center" ss:Vertical="Center"/>
   <Font ss:Size="24" ss:Color="#00AA00" ss:Bold="1"/>
   <Interior ss:Color="#FFFF00" ss:Pattern="Solid"/>';
    
    $exts = array();
    $exts['sheetname'] = '导出数据';//Sheet名称
    $exts['titleheight'] = '20';//列头高度
    //$exts['pagesetup'] = '<Layout x:Orientation="Landscape"/>';//横向打印
    $exts['rowstop'] = '<Row ss:Height="45"><Cell ss:MergeAcross="4" ss:StyleID="cap"><Data ss:Type="String">众产CIYPHP</Data></Cell></Row>';//顶部行
    $exts['rowsfooter'] = '<Row><Cell ss:MergeAcross="1"><Data ss:Type="String">合计</Data></Cell><Cell ss:Formula="=SUM(R[-'.$count.']C:R[-1]C)"><Data ss:Type="Number"></Data></Cell></Row>';//底部行
    
    return array($filename,$field,$data,$style,$exts);
}
function excel_ccsimple($msql)//Excel CSV数据导出函数，ciy_runCSV()调用。
{
    global $mydata;
    global $table;
    $data = array();
    $filename = get('prefix').'_' . date('Y-m-d_h-i-s');
    //$field = array('编号','姓名','分数','日期','IP');//简化
    $field = array();
    $field[] = array('name'=>'编号','width'=>50);
    $field[] = array('name'=>'姓名','width'=>80);
    $field[] = array('name'=>'分数','width'=>60);
    $field[] = array('name'=>'日期','width'=>150);
    $field[] = array('name'=>'IP','width'=>100);
    $rows = $mydata->get($msql);
    $count = count($rows);
    for($i=0;$i<$count;$i++)
    {
        $data[] = array(' '.enid($rows[$i]['id']),$rows[$i]['truename'],$rows[$i]['scores'],date('Y-m-d H:i',$rows[$i]['addtimes']),long2ip($rows[$i]['ip']));
    }
    return array($filename,$field,$data);
}

function json_del() {//Ajax交互函数，ciy_runJSON()调用。
    global $mydata;
    global $table;
    $post = new ciy_post();
    $id = $post->getint('id');
    $csql = new ciy_sql($table);
    $csql->where('id',$id);
    $oldrow = $mydata->getone($csql);
    $ret = $mydata->tran(function()use($mydata,$csql){
        return $mydata->delete($csql,true);
    });
    if($ret === false)
        return errjson('删除失败:'.$mydata->error);
    return succjson();
}
function json_setact() {
    global $mydata;
    global $table;
    $post = new ciy_post();
    $act = $post->get('act');
    $ids = $post->get('ids');
    if($act === 'read')
    {
        $execute = $mydata->execute('update '.$table.' set scores=scores+1 where id in ('.$ids.')');
        if ($execute === false)
            return errjson('操作失败:'.$mydata->error);
    }
    return succjson();
}