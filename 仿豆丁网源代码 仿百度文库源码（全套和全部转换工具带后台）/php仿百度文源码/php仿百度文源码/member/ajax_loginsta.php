<?php
require_once(dirname(__FILE__)."/config.php");
AjaxHead();
if($myurl == '')
{
	exit('');
}
$uid  = $cfg_ml->M_LoginID;

!$cfg_ml->fields['face'] && $face = ($cfg_ml->fields['sex'] == '女')? 'dfgirl' : 'dfboy';
$facepic = empty($face)? $cfg_ml->fields['face'] : $GLOBALS['cfg_memberurl'].'/templets/images/'.$face.'.png';

?>
<!--用户信息begin-->
<div>
      <strong><font color=red><?php echo $cfg_ml->M_UserName; ?></font></strong>
        <a href="<?php echo $cfg_memberurl; ?>/content_list.php?channelid=17">会员中心</a>
            <a href="/member/upload_book.php">上传文档</a>
        <a href="<?php echo $cfg_memberurl; ?>/edit_fullinfo.php">修改资料</a>
        <a href="<?php echo $myurl;?>">我的空间</a>
        <a href="/bbs">社区</a>
        <a href="<?php echo $cfg_memberurl; ?>/index_do.php?fmdo=login&dopost=exit">退出登录</a> 
</div>
<!--用户信息 end-->
