<?php require 'init.php'; require PATH_PROGRAM.NAME_SELF.'.pro.php'; ?><!DOCTYPE html><html>
<head>
<title></title>
<meta http-equiv="Content-type" content="text/html; charset=utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1">
<meta http-equiv="X-UA-Compatible" content="IE=Edge，chrome=1">
<link href="/jscss/style.css" rel="stylesheet" type="text/css" />
</head>
<body>
<div class="container">
    <div class="crumbs">当前位置： 系统管理 → 管理员管理 → <?php echo $btnname;?></div>
    <form>
        <div class="form-group inline" id="inp_img">
            <label>　上传照片</label>
            <div style="width:13em;">
                <div class="upload" id="upload_icon" data-num="1" data-name="icon" data-type="jpg,jpeg,png,gif" action="/upload.php" data-save="/upload/icon/{Y}_{M}/{D}_{H}{I}{S}{Rnd}" data-value="<?php echo @$updaterow['icon'];?>"></div>
                正方形照片(100×100px)
            </div>
        </div>
        <div class="form-group inline" data-check>
            <label style="width:6em;">姓名</label>
            <div><input type="text" name="truename" style='width:15em;' value="<?php echo @$updaterow['truename'];?>"/></div>
        </div>
        <div class="form-group inline" data-check="mobile">
            <label style="width:6em;">登录手机号</label>
            <div><input type="text" name="mobile" style='width:15em;' value="<?php echo @$updaterow['mobile'];?>"/></div>
        </div>
        <div class="form-group inline">
            <label style="width:6em;"><?php echo ($id== 0)?'初始密码':'重设密码'?></label>
            <div><input type="text" name="password" style='width:15em;' value=""/></div>
        </div>
        <div class="form-group inline">
            <label style="width:6em;">性别</label>
            <div>
                  <label class="formswitch"><input type="checkbox" name="sex"<?php if($updaterow['sex'] == 1) echo ' checked="true"'; ?>/><y>男</y><n>女</n><i></i></label>
            </div>
        </div>
        <div class="form-group inline">
            <label>状态</label>
            <div>
                  <label class="formswitch"><input type="checkbox" name="status"<?php if($updaterow['status'] == 1) echo ' checked="true"'; ?>/><y>禁用</y><n>正常</n><i></i></label>
            </div>
        </div>
        <div class="clearfix"></div>
        <div class="form-group">
            <label>部门领导</label>
            <div>
                  <label class="formswitch"><input type="checkbox" name="leader"<?php if($updaterow['leader'] == 1) echo ' checked="true"'; ?>/><y>是</y><n>否</n><i></i></label>
            </div>
        </div>
        <div class="form-group">
            <label>选择部门</label>
            <div>
                <input type="hidden" name="departid" value="<?php echo @$updaterow['departid'];?>"/>
                <a class="btn" onclick="javascript:select_depart(this);">选择部门</a>
                <span id="departname"><?php echo @$updaterow['depart'];?></span>
            </div>
        </div>
        <div class="form-group">
            <label>选择角色</label>
            <div>
                  <?php 
                    if(@$updaterow['power'] == '.*.')
                        echo '超级管理员';
                    else
                    {
                        $ret = '';
                        foreach ($roles as $row) {
                            $ret.='<label class="formi"><input type="checkbox" name="role" value="'.$row['id'].'"';
                            foreach ($urole as $urrow) {
                                if($row['id'] == $urrow['roleid'])
                                    $ret.=' checked="checked"';
                            }
                            $ret.='/><i></i>'.$row['title'].'</label>';
                        }
                        echo $ret;
                    }
                  ?>
            </div>
        </div>
        <div class="form-group">
            <div style="text-align:center;">
            <button class="btn btn-lg" type="button" onclick="javascript:formsubmit(this);"><?php echo $btnname;?></button>
            <input type="hidden" name="id" value="<?php echo $id;?>"/>
            </div>
        </div>
    </form>
</div>
<script src="/jscss/jquery-1.12.4.min.js" type="text/javascript"></script>
<script src="/jscss/ciy.js" type="text/javascript"></script>
<script src="/jscss/upload.js" type="text/javascript"></script>
<script type="text/javascript">
$(document).ready(function(){
    ciy_alertautoheight();
    $("#upload_icon").upload();
});
function select_depart(dom)
{
    var departid = $(dom).prev('input').val();
    ciy_alert({
        title:'选择部门',
        contentstyle:'width:400px;height:30em;',
        frame:'rigger/admin_depart_select.php?id='+departid,
        nobutton:true,
        cb:function(btn,data){
            callfunc("admin_depart_select.php?json=true&func=getdepart",'id='+data.id,function(json){
                $('#departname').text(json.depart);
                $('input[name=departid]').val(data.id);
                var roles = json.defroles.split(',');
                var inprole = $('input[name=role]');
                inprole.each(function(res){
                    var sel = false;
                    for(var i in roles){
                        if(roles[i]==inprole[res].value){
                            sel = true;
                            break;
                        }
                    }
                    inprole[res].checked = sel;
                });
            });
        }
    });
}
function formsubmit(dom)
{
    var postparam = ciy_getform(dom);
    if (postparam._check)
        return ciy_alert(postparam._check);
    if(postparam.id == 0){
        if (postparam.password === "")
            return ciy_alert("请填写密码");
    }
    if(!postparam.role){
        return ciy_alert("请选择角色");
    }
    callfunc("update",postparam,function(json){
        ciy_refresh();
        ciy_toast('提交成功',{done:function(){
            ciy_alertclose();
        }});
    });
}
</script>
</body></html>
