<?php
/**
 *  QQ互联管理模块
 *
 * @Intro		检查会员是否绑定OpenId     
 * @Update		2011-06-03 02:26:54
 * @Copyright	家饰吧（http://www.jiashi8.com）
 */
require_once(dirname(__FILE__)."/config.php");
CheckPurview('member_List');
require_once(DEDEINC."/datalistcp.class.php");
setcookie("ENV_GOBACK_URL",$dedeNowurl,time()+3600,"/");
require_once(DEDEINC.'/helpers/qzonelogin.helper.php');
if(empty($dopost)) $dopost == '';
if($dopost == ''){
	$sql  = "SELECT * FROM `#@__member_qzonelogin` ORDER BY id DESC ";
	$dlist = new DataListCP();
	$dlist->SetTemplet(DEDEADMIN."/templets/qzonelogin_main.htm");
	$dlist->SetSource($sql);
	$dlist->display();
}elseif($dopost == 'lock'){
	$id = isset($id) && is_numeric($id) ? $id : 0;
	$row = $dsql->GetOne("SELECT stat FROM `#@__member_qzonelogin` WHERE id = '{$id}' ORDER BY id DESC LIMIT 0,1");
	$statold = $row['stat'];
	$statnew = ($statold == 1) ? '0' : '1';
	$sqlquery = "UPDATE `#@__member_qzonelogin` SET stat = '$statnew' WHERE id = '{$id}'";
	$rs = $dsql->ExecuteNoneQuery($sqlquery);
	if($rs){
		ShowMsg("状态设置成功，正在返回","qzonelogin_main.php");
		exit();
	}else{
		ShowMsg("状态设置失败，正在返回","qzonelogin_main.php");
		exit();		
	}
}elseif($dopost == 'delete'){
	$id = isset($id) && is_numeric($id) ? $id : 0;
	$dsql->ExecuteNoneQuery("DELETE FROM `#@__member_qzonelogin` WHERE id='{$id}'");
	ShowMsg("删除成功，正在返回","qzonelogin_main.php");
	exit();
}
?>