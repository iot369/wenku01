<?php

/**
 * Enter description here...
 *
 * @author Administrator
 * @package defaultPackage
 * @rcsfile 	$RCSfile: receive.php,v $
 * @revision 	$Revision: 1.2 $
 * @date 	$Date: 2008/12/29 02:30:43 $
 */
require_once(dirname(__FILE__)."/../../../include/common.inc.php");
require_once DEDEDATA.'/sys_pay.cache.php';
require_once(dirname(__FILE__)."/cbpayment_config.php");
require_once(DEDEINC."/memberlogin.class.php");
$cfg_ml = new MemberLogin();
$cfg_ml->PutLoginInfo($cfg_ml->M_ID);
if($cfg_ml->M_ID>0) $burl = $cfg_basehost."/member/control.php";
else $burl = "javascript:;";

$v_oid     =trim($_POST['v_oid']);       // 商户发送的v_oid定单编号   
$v_pmode   =trim($_POST['v_pmode']);    // 支付方式（字符串）   
$v_pstatus =trim($_POST['v_pstatus']);   //  支付状态 ：20（支付成功）；30（支付失败）
$v_pstring =trim($_POST['v_pstring']);   // 支付结果信息 ： 支付完成（当v_pstatus=20时）；失败原因（当v_pstatus=30时,字符串）； 
$v_amount  =trim($_POST['v_amount']);     // 订单实际支付金额
$v_moneytype  =trim($_POST['v_moneytype']); //订单实际支付币种    
$remark1   =trim($_POST['remark1' ]);      //备注字段1
$remark2   =trim($_POST['remark2' ]);     //备注字段2
$v_md5str  =trim($_POST['v_md5str' ]);   //拼凑后的MD5校验值  

$md5string=strtoupper(md5($v_oid.$v_pstatus.$v_amount.$v_moneytype.$key));

if ($v_md5str==$md5string)
{
	if($v_pstatus=="20"){
		//支付成功
		$buyid = $v_oid;
		//获取订单信息，检查订单的有效性
		$row = $dsql->GetOne("Select * From #@__member_operation where buyid='$buyid' ");
		if(!is_array($row)||$row['sta']==2){
			$oldinfo = $row['oldinfo'];
			$msg = "本交易已经完成！，系统返回信息( $oldinfo ) <br><br> <a href='$burl' target='_bank'>返回主页</a> ";
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
			if(!is_array($row)){
				$nrow = $dsql->GetOne("Select num From  #@__moneycard_type where tid='$pid' ");
				$dnum = $nrow['num'];
				$equery =  " Update #@__member set money=money+".$dnum." where mid='$mid' ";
				$dsql->ExecuteNoneQuery($equery);
				//更新交易状态为已关闭
				$dsql->ExecuteNoneQuery(" Update #@__member_operation set sta=2,oldinfo='直接充值了 {$dnum} 金币到帐号！' where buyid='$buyid' ");
				ShowMsg("由于此点卡已经卖完，系统直接为你的帐号增加了：{$dnum} 个金币！",$burl);
				$dsql->Close();
				exit();
			}else{
				$cardid = $row['cardid'];
				$dsql->ExecuteNoneQuery(" Update #@__moneycard_record set uid='$mid',isexp='1',utime='".time()."' where cardid='$cardid' ");
				//更新交易状态为已关闭
				$dsql->ExecuteNoneQuery(" Update #@__member_operation set sta=2,oldinfo='充值密码：{$cardid}' where buyid='$buyid' ");
				ShowMsg("交易成功！<a href='$burl' target='_bank'><u>[返回]</u></a><br> 充值密码：{$cardid}","javascript:;");
				$dsql->Close();
				exit();
			}
		}
	}else{
		ShowMsg("支付失败","javascript:;");
		exit;
	}
}else{
	ShowMsg("校验失败,数据可疑!","javascript:;");
	exit;
}
?>