<?php
	require_once(dirname(__FILE__)."/../../../include/common.inc.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<script language="javascript" type="text/javascript" src="<?php echo $cfg_cmsurl;?>/js/jquery-1.3.2.min.js"></script>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>用户登录</title>
</head>
<style type="text/css">
	*{margin:0px;border:0px;padding:0px;}
	body{font:12px "宋体";}
	a{color:#000;text-decoration:none;}
	a:hover{color:#F30;text-decoration:underline;}
	input{padding:0px;margin:0px;}
	.txtbox{border:1px solid #E79F50;height:20px;line-height:22px;width:120px;}
	.vdcode{border:1px solid #E79F50;height:20px;line-height:22px;width:60px;}
	.bfont{font:700 12px "宋体"};
	#login{width:400px;height:100%;overflow:hidden;margin:0px auto;}
	#title{width:400px;height:25px;line-height:25px;color:#F60;font-weight:700;background-color:#FDEBD9;text-align:left;margin:0px auto;}
	#loginbox{width:185px;height:90px;margin:10px 20px;float:left;display:inline;}
	#btnlogin{width:55px;height:60px;float:right;margin:10px 30px 10px 5px;}
	#forget{width:200px;margin:0px auto;height:25px;line-height:25px;text-align:center;}
	#forget span{width:80px;display:inline;margin:0px 10px;}
</style>
<body>
<div id="login">
	<div id="title">个人登录后才下载或收藏本站文档资料。</div>
    <div style="width:320px;height:90px;margin:0px auto;">
        <table id="loginbox" border="0" cellpadding="0" cellspacing="0">
            <tr style="height:30px">
                <td class="bfont">用户名：</td>
                <td><input class="txtbox" type="text" name="userid" id="userid" /></td>
            </tr>
            <tr style="height:30px">
                <td class="bfont">密&nbsp;&nbsp;码：</td>
                <td><input class="txtbox" type="password" name="pwd" id="pwd" /></td>
            </tr>
            <tr style="height:30px">
                <td class="bfont">验证码：</td>
                <td><input class="vdcode" type="text" name="vdcode" id="vdcode" /> <img id="vdimgck" src="<?php echo $cfg_cmspath; ?>/include/vdimgck.php" alt="看不清？点击更换" align="absmiddle" style="cursor:pointer" onClick="this.src=this.src+'?'" /></td>
            </tr>
        </table>
        <div id="btnlogin"><a href="#"><img src="/templets/jxdoc/images/login.gif" width="55" height="55" onclick="parent.winLogin($('#userid').val(), $('#pwd').val(), $('#vdcode').val(), '<?php echo $todo; ?>');" /></a></div>
    </div>
    
    <div style="float:right; padding-right:10px;"><a style="color:#06F;text-decoration:underline;font-weight:700" href="#" onclick="parent.reguser();">没有注册？</a></div>
</div>
</body>
</html>
