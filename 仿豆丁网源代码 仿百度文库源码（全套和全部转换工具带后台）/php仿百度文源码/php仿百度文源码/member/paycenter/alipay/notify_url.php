<?php
///////////页面功能说明///////////////
//创建该页面文件时，请留心该页面文件中无任何HTML代码及空格。
//该页面不能在本机电脑测试，请到服务器上做测试。请确保外部可以访问该页面。
//该页面调试工具请使用写文本函数log_result，该函数已被默认关闭，见alipay_notify.php中的函数notify_verify
//TRADE_FINISHED(表示交易已经成功结束，通用即时到帐反馈的交易状态成功标志);
//TRADE_SUCCESS(表示交易已经成功结束，高级即时到帐反馈的交易状态成功标志);
//该服务器异步通知页面面主要功能是：对于返回页面（return_url.php）做补单处理。如果没有收到该页面返回的 success 信息，支付宝会在24小时内按一定的时间策略重发通知
/////////////////////////////////////

require_once(dirname(__FILE__)."/../../../include/common.inc.php");
require_once DEDEDATA.'/sys_pay.cache.php';
require_once(DEDEINC."/memberlogin.class.php");
require_once(dirname(__FILE__)."/alipay_config.php");
require_once(dirname(__FILE__)."/class/alipay_notify.php");
$cfg_ml = new MemberLogin();
$cfg_ml->PutLoginInfo($cfg_ml->M_ID);
if($cfg_ml->M_ID>0)
{
	$burl = $cfg_basehost.$cfg_memberurl."/control.php";
}
else
{
	$burl = "javascript:;";
}

$alipay = new alipay_notify($partner,$key,$sign_type,$_input_charset,$transport);    //构造通知函数信息
$verify_result = $alipay->notify_verify();  //计算得出通知验证结果

if($verify_result) {//验证成功
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//请在这里加上商户的业务逻辑程序代
	
	//——请根据您的业务逻辑来编写程序（以下代码仅作参考）——
    //获取支付宝的通知返回参数，可参考技术文档中服务器异步通知参数列表
    $dingdan           = $_POST['out_trade_no'];	//获取支付宝传递过来的订单号
    $total             = $_POST['total_fee'];		//获取支付宝传递过来的总价格

    if($_POST['trade_status'] == 'TRADE_FINISHED' ||$_POST['trade_status'] == 'TRADE_SUCCESS') {    //交易成功结束
		//支付成功
		$buyid = $dingdan;

		//获取订单信息，检查订单的有效性
		$row = $dsql->GetOne("Select * From #@__member_operation where buyid='$buyid' ");
		if(!is_array($row)||$row['sta']==2)
		{
			$oldinfo = $row['oldinfo'];
			exit("success");
		}
		$mid = $row['mid'];
		$pid = $row['pid'];

		//更新交易状态为已付款
		$dsql->ExecuteNoneQuery("Update #@__member_operation set sta=1 where buyid='$buyid' ");

		//-------------------------------------------
		//会员产品
		//-------------------------------------------
		if($row['product']=='member')
		{
			$row = $dsql->GetOne(" Select rank,exptime From #@__member_type where aid='{$row['pid']}' ");
			$rank = $row['rank'];
			$exptime = $row['exptime'];
			$equery =  " Update #@__member set
								membertype='$rank',exptime='$exptime',uptime='".time()."' where mid='$mid' ";
			$dsql->ExecuteNoneQuery($equery);

			//更新交易状态为已关闭
			$dsql->ExecuteNoneQuery(" Update #@__member_operation set sta=2,oldinfo='会员升级成功！' where buyid='$buyid' ");
			$dsql->Close();
		}

		//点卡产品
		else if($row['product']=='card')
		{
			$row = $dsql->GetOne("Select cardid From #@__moneycard_record where ctid='$pid' And isexp='0' ");

			//如果找不到某种类型的卡，直接为用户增加金币
			if(!is_array($row))
			{
				$nrow = $dsql->GetOne("Select num From  #@__moneycard_type where tid='$pid' ");
				$dnum = $nrow['num'];
				$equery =  " Update #@__member set money=money+".$dnum." where mid='$mid' ";
				$dsql->ExecuteNoneQuery($equery);
				//更新交易状态为已关闭
				$dsql->ExecuteNoneQuery(" Update #@__member_operation set sta=2,oldinfo='直接充值了 {$dnum} 金币到帐号！' where buyid='$buyid' ");
				exit();
			}
			else
			{
				$cardid = $row['cardid'];
				$dsql->ExecuteNoneQuery(" Update #@__moneycard_record set uid='$mid',isexp='1',utime='".time()."' where cardid='$cardid' ");

				//更新交易状态为已关闭
				$dsql->ExecuteNoneQuery(" Update #@__member_operation set sta=2,oldinfo='充值密码：{$cardid}' where buyid='$buyid' ");
			}
		}
		echo "success";
    }
    else {
        echo "success";		//其他状态判断。普通即时到帐中，其他状态不用判断，直接打印success。
    }

	log_result("verify_success"); //将验证结果存入文件
	
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
}
else {
    //验证失败
    echo "fail";

	log_result ("verify_failed");
}

?>