<?php
	require_once(dirname(__FILE__).'/../config.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<script language="javascript" type="text/javascript" src="<?php echo $cfg_cmsurl;?>/js/jquery-1.3.2.min.js"></script>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<script type="text/javascript" src="<?php echo $cfg_cmspath; ?>/js/zDialog/zDrag.js"></script>
<script type="text/javascript" src="<?php echo $cfg_cmspath; ?>/js/zDialog/zDialog.js"></script>
<title>用户登录</title>
<script language="javascript">
function submitForm(){
	if($('#account').val() == ""){
		Dialog.alert("请输入支付宝账号！");
	}else if($('#accountcomfirm').val() == ""){
		Dialog.alert("请确认输入支付宝账号！");
	}else if($('#accountcomfirm').val() != $('#account').val()){
		Dialog.alert("两次输入的账号不一致，请确认！");
	}else if($('#vdcode').val() == ""){
		Dialog.alert("请输入验证码！");
	}else{
		parent.setAccount($('#account').val(), $('#vdcode').val());
	}
	
}
</script>
</head>
<style type="text/css">
	*{margin:0px;border:0px;padding:0px;}
	body{font:12px "宋体";}
	a{color:#000;text-decoration:none;}
	a:hover{color:#F30;text-decoration:underline;}
	input{padding:0px;margin:0px;}
	.txtbox{border:1px solid #E79F50;height:20px;line-height:22px;width:120px;}
	.vdcode{border:1px solid #E79F50;height:20px;line-height:22px;width:60px;}
	.bfont{font:700 12px "宋体"; width:200px;};
	#login{width:400px;height:100%;overflow:hidden;margin:0px auto;}
	#title{width:400px;height:25px;line-height:25px;color:#F60;font-weight:700;background-color:#FDEBD9;text-align:left;margin:0px auto;}
	#loginbox{width:265px;height:90px;margin:10px 20px;float:left;display:inline;}
	#btnlogin{width:55px;height:60px;float:right;margin:10px 30px 10px 5px;}
	#forget{width:200px;margin:0px auto;height:25px;line-height:25px;text-align:center;}
	#forget span{width:80px;display:inline;margin:0px 10px;}
</style>
<body>
<div id="login">
	<div id="title">请先设置您的提现账号（暂只支持支付宝账号）</div>
    <div style="width:420px;height:90px;margin:0px auto;">
        <table id="loginbox" border="0" cellpadding="0" cellspacing="0">
            <tr style="height:30px">
                <td class="bfont">请输入您的支付宝账号：</td>
                <td><input class="txtbox" type="text" name="account" id="account" /></td>
            </tr>
            <tr style="height:30px">
                <td class="bfont">请确认您的支付宝账号：</td>
                <td><input class="txtbox" type="text" name="accountcomfirm" id="accountcomfirm" /></td>
            </tr>
            <tr style="height:30px">
                <td class="bfont" align="right">验证码：</td>
                <td><input class="vdcode" type="text" name="vdcode" id="vdcode" /> <img id="vdimgck" src="<?php echo $cfg_cmspath; ?>/include/vdimgck.php" alt="看不清？点击更换" align="absmiddle" style="cursor:pointer" onClick="this.src=this.src+'?'" /></td>
            </tr>
        </table>
        <div id="btnlogin"><a href="#" title="设置"><img src="<?php echo $cfg_memberurl; ?>/templets/images/accountset.png" width="55" height="55" onclick="submitForm();" alt="设置"/></a></div>
    </div>
</div>
<script>
IMAGESPATH = "<?php echo $cfg_cmspath; ?>/js/zDialog/images/";
var images=["icon_alert.gif","icon_dialog.gif","icon_query.gif","window.gif","dialog_cb.gif","dialog_closebtn.gif","dialog_closebtn_over.gif"];
var dlgimgs=ielt7?["dialog_ct.gif","dialog_lb.gif","dialog_lt.gif","dialog_mlm.gif","dialog_mrm.gif","dialog_rb.gif","dialog_rt.gif"]:["dialog_cb.png",
"dialog_ct.png","dialog_lb.png","dialog_lt.png","dialog_mlm.png","dialog_mrm.png","dialog_rb.png","dialog_rt.png"]
var images=images.concat(dlgimgs);
var imgsHtml=[];
for(var i=0;i<images.length;i++){
	imgsHtml.push('<img src="'+IMAGESPATH+images[i]+'"/>')
}
document.write('<div id="imgsloader" style="text-align:right; display:none;">'+imgsHtml.join("")+'</div>')
</script>
</body>
</html>
