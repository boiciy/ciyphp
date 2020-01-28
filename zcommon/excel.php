<?php
/* =================================================================================
 * 版权声明：保留开源作者及版权声明前提下，开源代码可进行修改及用于任何商业用途。
 * 开源作者：众产国际产业公会  http://ciy.cn/code
 * 版本：0.1.0
====================================================================================*/
/*
 * excel.php Excel导入导出扩展库
 * 
 * ciy_ImportCSV        导入Excel CSV格式解析
 * ciy_runExcelCSV      导出到Excel CSV格式
 * ciy_runExcelxml      导出到Excel xml格式，样式可控
 * ciy_runExcelxlsx     导出到Excel xlsx格式（不建议实现）
 * 
 */

function ciy_ImportCSV($file){
    $csvret = array();
    $fh=file_get_contents($file);
    $dot = ',';
    if(ord($fh[0]) == 255)
    {
        $line = mb_convert_encoding(substr($fh,2), 'UTF-8', 'UTF-16LE');
        $dot = "\t";
    }
    else
        $line = mb_convert_encoding($fh, 'UTF-8', 'GBK');
    $csvlines = array();
    $csvfields = array();
    $csvdata = '';
    $bcontent = false;
    $linecnt = strlen($line)-1;
    if($line[$linecnt] != "\n"){
        $line .= "\n\n";
        $linecnt+=2;
    }else{
        $line .= "\n";
        $linecnt++;
    }
    for($i=0;$i<$linecnt;$i++){
        if($line[$i] == "\""){
            if($line[$i+1] == "\"")
                $csvdata .= $line[$i];
            else
            $bcontent = !$bcontent;
        }
        else if($bcontent){
            $csvdata .= $line[$i];
        }
        else if($line[$i] == $dot){
            $csvfields[] = $csvdata;
            $csvdata = '';
        }
        else if($line[$i] == "\n"){
            $csvfields[] = $csvdata;
            $csvdata = '';
            $csvlines[] = $csvfields;
            $csvfields = array();
        }
        else
            $csvdata .= $line[$i];
    }
    return $csvlines;
}

/**
 * CSV函数调用组件，用于数据导出到Excel操作
 * ?json=true&func=函数名
 * 将调用csv_函数名(){}
 * 函数返回array数组。第一行包含.csv，则为CSV文件名。
 */
function ciy_runExcelCSV($msql) {
    if (!isset($_GET['excel']) || $_GET['excel'] != 'csv')
        return;
    $funcname = 'excel_' . get('func');
    if (!function_exists($funcname))
    {
        pr('Excel CSV导出失败：'.$funcname.'函数未定义');
        exit;
    }
    $retarr = call_user_func($funcname,$msql);
    if(!is_array($retarr))
    {
        pr('Excel CSV导出失败：'.$retarr);
        exit;
    }
    $filename = $retarr[0];
    if(empty($filename))
        $filename = date('Y-m-d_H-i-s').rand(1000,9999);
    $filename.='.csv';
    $fields = $retarr[1];
    $datas = $retarr[2];
    header("Cache-Control: public");
    header("Pragma: public");
    header("Content-type: text/csv");
    header("Content-Disposition: attachment; filename=" . $filename);
    echo(chr(255) . chr(254));
    $bline = false;
    foreach ($fields as $field)
    {
        if(is_array($field))
            $d = $field['name'];
        else
            $d = $field;
        if($bline)
            echo mb_convert_encoding("\t", "UTF-16LE", "UTF-8");
        echo mb_convert_encoding('"'.$d.'"', "UTF-16LE", "UTF-8");
        $bline = true;
    }
    echo mb_convert_encoding("\r\n", "UTF-16LE", "UTF-8");
    foreach ($datas as $data)
    {
        $bline = false;
        foreach($data as $d)
        {
            if($bline)
                echo mb_convert_encoding("\t", "UTF-16LE", "UTF-8");
            echo mb_convert_encoding('"'.$d.'"', "UTF-16LE", "UTF-8");
            $bline = true;
        }
        echo mb_convert_encoding("\r\n", "UTF-16LE", "UTF-8");
    }
    exit;
}
function ciy_runExcelxml($msql) {
    if (!isset($_GET['excel']) || $_GET['excel'] != 'xml')
        return;
    $funcname = 'excel_' . get('func');
    if (!function_exists($funcname))
    {
        pr('Excel xml导出失败：'.$funcname.'函数未定义');
        exit;
    }
    $retarr = call_user_func($funcname,$msql);
    if(!is_array($retarr))
    {
        pr('Excel xml导出失败:'.$retarr);
        exit;
    }
    $filename = $retarr[0];
    if(empty($filename))
        $filename = date('Y-m-d_H-i-s').rand(1000,9999);
    $filename.='.xml';
    $fields = $retarr[1];
    $datas = $retarr[2];
    $styles = @$retarr[3];
    if(!is_array($styles))
        $styles = array();
    $exts = @$retarr[4];
    if(!is_array($exts))
        $exts = array();
    $sheetname = 'sheetCIY';
    if(isset($exts['sheetname']))
        $sheetname = $exts['sheetname'];
    $DefaultColumnWidth = 60;//默认宽度
    $DefaultRowHeight = 16;//默认高度
    $dat = '<?xml version="1.0"?><?mso-application progid="Excel.Sheet"?><Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet" xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet" xmlns:html="http://www.w3.org/TR/REC-html40">
<DocumentProperties xmlns="urn:schemas-microsoft-com:office:office">
<Author>CIYPHP</Author>
<Version>15.00</Version>
</DocumentProperties>
<OfficeDocumentSettings xmlns="urn:schemas-microsoft-com:office:office"><AllowPNG/></OfficeDocumentSettings>
<ExcelWorkbook xmlns="urn:schemas-microsoft-com:office:excel"><WindowTopX>0</WindowTopX><WindowTopY>0</WindowTopY>  <ProtectStructure>False</ProtectStructure><ProtectWindows>False</ProtectWindows></ExcelWorkbook>
<Styles>';
    foreach($styles as $id=>$style)
        $dat.='<Style ss:ID="'.$id.'">'.$style.'</Style>';
    $dat .= '</Styles><Worksheet ss:Name="'.$sheetname.'"><Table ss:ExpandedColumnCount="'.(count($fields)+10).'" ss:ExpandedRowCount="'.(count($datas)+20).'" x:FullColumns="1" x:FullRows="1" ss:DefaultColumnWidth="'.$DefaultColumnWidth.'" ss:DefaultRowHeight="'.$DefaultRowHeight.'">';
    foreach($fields as $field)
    {
        if(!is_array($field) || !isset($field['width']))
            $dat.='<Column ss:Width="'.$DefaultColumnWidth.'"/>';
        else
            $dat.='<Column ss:Width="'.$field['width'].'"/>';
    }
    $dat.=@$exts['rowstop'];//自定义表格头
    if(isset($exts['titleheight']))
        $dat.='<Row ss:Height="'.$exts['titleheight'].'">';
    else
        $dat.='<Row>';
    $cellpre = '<Cell';
    if(isset($styles['ts']))
        $cellpre .= '<Cell ss:StyleID="ts"';
    foreach($fields as $field)
    {
        if(is_array($field))
        {
            $dat.=$cellpre.'><Data ss:Type="String">'.@$field['name'].'</Data></Cell>';
        }
        else
            $dat.=$cellpre.'><Data ss:Type="String">'.$field.'</Data></Cell>';
    }
    $dat.='</Row>';
    foreach($datas as $data)
    {
        $dat.='<Row>';
        foreach($data as $ind=>$d)
        {
            $dat.='<Cell';
            $type = 'String';
            if(is_array($fields[$ind]))
            {
                if(isset($fields[$ind]['style']))
                    $dat.=' ss:StyleID="'.$fields[$ind]['style'].'"';
                if(isset($fields[$ind]['type']))
                    $type = $fields[$ind]['type'];
            }
            $dat.='><Data ss:Type="'.$type.'">'.$d.'</Data></Cell>';
        }
        $dat.='</Row>';
    }
    $dat.=@$exts['rowsfooter'];//自定义表格尾
   $dat.='</Table>
  <WorksheetOptions xmlns="urn:schemas-microsoft-com:office:excel">
   <PageSetup>';
    $dat.=@$exts['pagesetup'];
   $dat.='<Header x:Margin="0.3"/>
    <Footer x:Margin="0.3"/>
    <PageMargins x:Bottom="0.75" x:Left="0.7" x:Right="0.7" x:Top="0.75"/>
   </PageSetup>
   <Print>
    <ValidPrinterInfo/>
    <PaperSizeIndex>1</PaperSizeIndex>
    <HorizontalResolution>600</HorizontalResolution>
    <VerticalResolution>0</VerticalResolution>
   </Print>
   <Selected/>
   <ProtectObjects>False</ProtectObjects>
   <ProtectScenarios>False</ProtectScenarios>
  </WorksheetOptions>
 </Worksheet>
</Workbook>';

    header("Cache-Control: public");
    header("Pragma: public");
    header("Content-type: text/xml");
    header("Content-Disposition: attachment; filename=" . $filename);
    echo $dat;
    exit;
}
//直接导出xls/xlsx格式。
//分析具体格式，将xlsx修改为zip解压后，研究sheet1.xml/styles.xml。
//使用PHPExcel后端导出（服务器压力较大，可以自定义样式或导入样式）
//建议使用js-xlsx前端导出（服务器压力小，前端JS控制数据样式）
function ciy_runExcelxlsx($msql) {
    if (!isset($_GET['excel']) || $_GET['excel'] != 'xlsx')
        return;
}
