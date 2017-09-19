<?php
///////////页面功能说明///////////////
//该页面可在本机电脑测试
//该页面称作“页面跳转同步通知页面”，是由支付宝服务器同步调用，可当作是支付完成后的提示信息页，如“您的某某某订单，多少金额已支付成功”。
//可放入HTML等美化页面的代码和订单交易完成后的数据库更新程序代码
//该页面可以使用PHP开发工具调试，也可以使用写文本函数log_result进行调试，该函数已被默认关闭，见alipay_notify.php中的函数return_verify
//TRADE_FINISHED(表示交易已经成功结束，为普通即时到帐的交易状态成功标识);
//TRADE_SUCCESS(表示交易已经成功结束，为高级即时到帐的交易状态成功标识);
///////////////////////////////////
require_once(dirname(__FILE__)."/../../../include/common.inc.php");
require_once DEDEDATA.'/sys_pay.cache.php';
require_once(DEDEINC."/memberlogin.class.php");
require_once(dirname(__FILE__)."/class/alipay_notify.php");
require_once(dirname(__FILE__)."/alipay_config.php");
$cfg_ml = new MemberLogin();
$cfg_ml->PutLoginInfo($cfg_ml->M_ID);
if($cfg_ml->M_ID>0) $burl = $cfg_basehost.$cfg_memberurl."/control.php";
else $burl = "javascript:;";

//构造通知函数信息
$alipay = new alipay_notify($partner,$key,$sign_type,$_input_charset,$transport);
//计算得出通知验证结果
$verify_result = $alipay->return_verify();

if($verify_result) {//验证成功
	
    //获取支付宝的通知返回参数，可参考技术文档中页面跳转同步通知参数列表
    $dingdan           = $_GET['out_trade_no'];    //获取订单号
    $total_fee         = $_GET['total_fee'];	    //获取总价格

    if($_GET['trade_status'] == 'TRADE_FINISHED' || $_GET['trade_status'] == 'TRADE_SUCCESS') {
		//支付成功
		$buyid = $dingdan;

		//获取订单信息，检查订单的有效性
		$row = $dsql->GetOne("Select * From #@__member_operation where buyid='$buyid' ");
		if(!is_array($row)||$row['sta']==2)
		{
			$oldinfo = $row['oldinfo'];
			$msg = "本交易已经完成！系统返回信息( $oldinfo ) <br><br> <a href='$burl' target='_bank'>返回主页</a> ";
			ShowMsg($msg,"javascript:;");
			$dsql->Close();
			exit();
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
			ShowMsg("成功完成交易！",$burl);
			exit();
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
				ShowMsg("由于此点卡已经卖完，系统直接为你的帐号增加了：{$dnum} 个金币！",$burl);
				exit();
			}
			else
			{
				$cardid = $row['cardid'];
				$dsql->ExecuteNoneQuery(" Update #@__moneycard_record set uid='$mid',isexp='1',utime='".time()."' where cardid='$cardid' ");

				//更新交易状态为已关闭
				$dsql->ExecuteNoneQuery(" Update #@__member_operation set sta=2,oldinfo='充值密码：{$cardid}' where buyid='$buyid' ");
				ShowMsg("交易成功！<a href='$burl' target='_bank'><u>[返回]</u></a><br> 充值密码：{$cardid}","javascript:;");
				exit();
			}
		}
		log_result("verify_success"); //将验证结果存入文件
    }
    else {
      //echo "trade_status=".$_GET['trade_status'];
	  $msg = "支付失败："."trade_status=".$_GET['trade_status'];
	  ShowMsg($msg,"javascript:;");

	  log_result ("verify_failed");
	  exit;
    }
}
else {
    //验证失败
    //如要调试，请看alipay_notify.php页面的return_verify函数，比对sign和mysign的值是否相等，或者检查$veryfy_result有没有返回true
    //echo "fail";
	$msg = "支付失败，验证结果失败！";
	ShowMsg($msg,"javascript:;");

	log_result ("verify_failed");
	exit;
}

?>