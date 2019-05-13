<?php require 'init.php'; require PATH_PROGRAM.NAME_SELF.'.pro.php'; ?><!DOCTYPE html><html>
<head>
<title></title>
<meta http-equiv="Content-type" content="text/html; charset=utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1">
<meta http-equiv="X-UA-Compatible" content="IE=Edge，chrome=1">
<link href="/jscss/style.css" rel="stylesheet" type="text/css" />
<body>
<div class="container">
    <div class="crumbs">当前位置： 用户中心 → 用户管理</div>
    <div class="ciy-tab ciy-tab-card">
        <ul><?php echo create_li($code_user_level,$liid);?>
        </ul>
        <div class="ciy-tab-box">
            <form methodd="get" action="">
                <div class="form-group inline">
                    <label>ID</label>
                    <div><input type="text" name="eid" value="<?php echo get('eid');?>" style="width:10em;"/></div>
                </div>
                <div class="form-group inline">
                    <label>昵称</label>
                    <div><input type="text" name="nickname" value="<?php echo get('nickname');?>" style="width:10em;"/></div>
                </div>
                <div class="form-group inline">
                    <label>手机号</label>
                    <div><input type="text" name="mobile" value="<?php echo get('mobile');?>" style="width:10em;"/></div>
                </div>
                <div class="form-group inline">
                    <div>
                        <button class="btn" type="submit">查询</button>
                        <input type="hidden" name="liid" value="<?php echo $liid;?>"/>
                    </div>
                </div>
            </form>
        </div>
      </div>
    
<?php
if($rows === false)
    echo '<div class="table-nodata">查询失败:'.$mydata->error.'</div>';
else if(count($rows) == 0)
    echo '<div class="table-nodata">无数据</div>';
else{
?>
      <div class='table'>
          <table>
            <tr>
                <th style="width:50px;">头像</th>
                <th>EID</th>
                <th>UpID</th>
                <th>昵称</th>
                <th>手机号</th>
                <th>余额</th>
                <th>用户等级</th>
                <th>微信状态</th>
                <th>活跃时间</th>
                <th>注册时间</th>
                <th>地理位置</th>
            </tr>
<?php
foreach($rows as $row){
    $id = (int)$row['id'];
    $upid = (int)$row['upid'];
?>
    <tr data-id="<?php echo $id;?>">
        <td><div><?php echo '<img src="'.$row['headimg'].'" style="width:100%;" onerror="this.onerror=null;this.src=\'/jscss/nopic.png\'"/>';?></div></td>
        <td><div style="text-align: center;"><?php echo enid($id);?></div></td>
        <td><div style="text-align: center;"><?php echo ($upid>0)?enid($upid):'--';?></div></td>
        <td><div><?php echo $row['nickname'];?></div></td>
        <td><div><?php echo $row['mobile'];?></div></td>
        <td><div style="text-align:right;"><?php echo (int)($row['money']/100);?>元</div></td>
        <td><div style="text-align: center;"><?php echo ccode($code_user_level,$row['level']);?></div></td>
        <td><div style="text-align: center;"><?php echo ccode($code_user_wxstatus,$row['wxstatus']);?></div></td>
        <td><div><?php echo todate($row['logintime']);?></div></td>
        <td><div><?php echo todate($row['addtimes'],'d');?></div></td>
        <td><div style="text-align: center;"><?php echo ($row['lat'] == 0)?'--':'<a class="btn" onclick="showloc('.$row['lng'].','.$row['lat'].')">位置</a>';?></div></td>
        </tr>
<?php } ?>
          </table>
      </div>
    <div class="ciy_tabbtn">
        <a class="btn btn-default" onclick="ciy_select_all('.table')">全选</a>
        <a class="btn btn-default" onclick="ciy_select_diff('.table')">反选</a>
    </div>
      <?php echo showpage($pageno,$pagecount,$mainrowcount);?>
<?php } ?>
</div>
<script src="/jscss/jquery-1.12.4.min.js" type="text/javascript"></script>
<script src="/jscss/ciy.js" type="text/javascript"></script>
<script type="text/javascript">
'use strict';
$(function(){
    ciy_table_adjust('.table');
    ciy_select_init('.table');
});
function showloc(lng,lat){
    ciy_alert({title:'地图位置标注',contentstyle:'width:680px;height:40em;',frame:'/jscss/map_loc.html?lat='+lat+"&lng="+lng,nobutton:true});
}
</script>
</body>
</html>