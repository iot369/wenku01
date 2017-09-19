<?php 
require_once(dirname(__FILE__)."/config.php");
/**
$svali = GetCkVdValue();

if(strtolower($vdcode)!=$svali || $svali=="")
{
	ShowMsg("验证码错误！","-1");
	exit();
}
*/
//$cardid = ereg_replace("[^0-9A-Za-z-]","",$cardid);
if(empty($account))
{
	ShowMsg("您没有设置提现账号，请先设置！","-1");
	exit();
}

if(empty($money))
{
	ShowMsg("请输入要提现的金币数量！","-1");
	exit();
}
if(!is_numeric($money))
{
	ShowMsg("您输入的金币数量不正确，请重新输入！","-1");
	exit();
}
//用户当前所有金币数
$memberMoney = $cfg_ml->M_Money;
$money = (int)$money;//要兑换的数量

if($money > $memberMoney){
	ShowMsg("您的金币数量不足，请重新输入！","-1");
	exit();
}

//插入提现申请记录
$dsql->ExecuteNoneQuery("insert `#@__member_benefit`(mid, money, rmb, account) values ('$cfg_ml->M_ID', '$money', '$rmb', '$account')");
//减掉会员的金币数，如管理员审核不通过再退回来
$dsql->ExecuteNoneQuery("Update `#@__member` set money = money - $money where mid='".$cfg_ml->M_ID."'");

ShowMsg("提现申请成功，你本次申请提现的金币数为：{$money} 个，请等待管理员的审核！",-1);
exit();
?>