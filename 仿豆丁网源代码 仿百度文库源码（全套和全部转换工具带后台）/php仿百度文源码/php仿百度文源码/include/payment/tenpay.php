<?php
if(!defined('DEDEINC')) exit('Request Error!');
/**
 * 财付通 网上支付接口接口类 added by caozhiyang 2010-10-01
 */
class tenpay
{
  var $dsql;
		
  /**
   * 构造函数
   *
   * @access  public
   * @param
   *
   * @return void
   */
  function tenpay()
  {
	global $dsql;
	$this->dsql = $dsql;
  }

  function __construct()
  {
    $this->tenpay();
  }

  /**
   * 生成支付代码
   * @param   array   $order      订单信息
   * @param   array   $payment    支付方式信息
   */
  function GetCode($order, $payment)
  {
  		global $cfg_basehost;
	
		/*这里替换为您的实际商户号*/
		$strSpid    = $payment['tenpay_account'];
		/*strSpkey是32位商户密钥, 请替换为您的实际密钥*/
		$strSpkey   = $payment['tenpay_key'];
		/*银行类型:	
				0		财付通
				1001	招商银行   
				1002	中国工商银行  
				1003	中国建设银行  
				1004	上海浦东发展银行   
				1005	中国农业银行  
				1006	中国民生银行  
				1008	深圳发展银行   
				1009	兴业银行   */
		if(!isset($BankType)) $BankType = 0;
		$BankType = ereg_replace("[^0-9]","",$BankType);
		if($BankType < 1) $BankType = 0;
		$strBankType= $BankType;
		$strCmdNo   = "1";
		$strBillDate= date('Ymd');
		/*商品名称*/
		if(!isset($pname)) $pname = '服务购买';
		$strDesc    = $pname;
		/*用户QQ号码, 现在置为空串*/
		$strBuyerId = "";
		/*商户号*/
		$strSaler   = $payment['tenpay_account'];
		//支付手续费
		if($payment_exp[0] < 0) $payment_exp[0] = 0;
		$piice_ex = $price*$payment_exp[0];
		$price 		= $price+$piice_ex;
		//支付金额
		$strTotalFee = $order['price']*100;
		if( $strTotalFee < 1){
			$dsql->Close();
			exit('金额不对');
		}
		$strSpBillNo = $order['out_trade_no'];
		/*重要: 交易单号
			  交易单号(28位): 商户号(10位) + 日期(8位) + 流水号(10位), 必须按此格式生成, 且不能重复
			  如果sp_billno超过10位, 则截取其中的流水号部分加到transaction_id后部(不足10位左补0)
			  如果sp_billno不足10位, 则左补0, 加到transaction_id后部*/
		$strTransactionId = $strSpid . $strBillDate . time();
		/*货币类型: 1 – RMB(人民币) 2 - USD(美元) 3 - HKD(港币)*/
		$strFeeType  = "1";
		/*财付通回调页面地址, 推荐使用ip地址的方式(最长255个字符)*/
		//$strRetUrl  = $cfg_basehost.$cfg_memberurl."/paycenter/tenpay/notify_handler.php";
		$strRetUrl  = $cfg_basehost."/plus/carbuyaction.php?dopost=return&code=".$payment['code'];

		/*商户私有数据, 请求回调页面时原样返回*/
		$strAttach  = "my_magic_string";
		/*生成MD5签名*/
		$strSignText = "cmdno=" . $strCmdNo . "&date=" . $strBillDate . "&bargainor_id=" . $strSaler .
				  "&transaction_id=" . $strTransactionId . "&sp_billno=" . $strSpBillNo .        
				  "&total_fee=" . $strTotalFee . "&fee_type=" . $strFeeType . "&return_url=" . $strRetUrl .
				  "&attach=" . $strAttach . "&key=" . $strSpkey;
		$strSign = strtoupper(md5($strSignText));

		/*请求支付串*/
		$strRequest = "cmdno=" . $strCmdNo . "&date=" . $strBillDate . "&bargainor_id=" . $strSaler .        
		"&transaction_id=" . $strTransactionId . "&sp_billno=" . $strSpBillNo .        
		"&total_fee=" . $strTotalFee . "&fee_type=" . $strFeeType . "&return_url=" . $strRetUrl .        
		"&attach=" . $strAttach . "&bank_type=" . $strBankType . "&desc=" . $strDesc .        
		"&purchaser_id=" . $strBuyerId .
		"&sign=" . $strSign ;
		$strRequestUrl = "https://www.tenpay.com/cgi-bin/v1.0/pay_gate.cgi?".$strRequest;
		$strRequestUrl = utf82gb($strRequestUrl);
		
		$button = '<form name="tenpay" action="paycenter/tenpay/tenpay_gbk_page.php?strReUrl='.urlencode($strRequestUrl).'" method="post"><input type="submit" value="立即支付"/></form>';

		/* 清空购物车 */
		require_once DEDEINC.'/shopcar.class.php';
		$cart 	= new MemberShops();
		$cart->clearItem();
		$cart->MakeOrders();
		return $button;
  }

  /**
   * 响应操作
   */
  function respond()
  {
   
    /* 引入配置文件 */
    require_once DEDEDATA.'/payment/'.$_REQUEST['code'].'.php';
		
		$p1_MerId=trim($payment['yp_account']);
		$merchantKey=trim($payment['yp_key']);
		
	  
    
    #	校验码正确.
		if($bRet)
		{
			if($r1_Code=="1")
			{
			  /*判断订单类型*/
		    if(preg_match ("/S-P[0-9]+RN[0-9]/",$r6_Order)) {
					$ordertype="goods";
				}elseif(preg_match ("/M[0-9]+T[0-9]+RN[0-9]/",$r6_Order)){
					$row = $this->dsql->GetOne("SELECT * FROM #@__member_operation WHERE buyid = '{$r6_Order}'");
					
					//获取订单信息，检查订单的有效性
					if(!is_array($row)||$row['sta']==2) return $msg = "您的订单已经处理，请不要重复提交!";
				  
					$ordertype = "member";
					$product =	$row['product'];
					$pname= $row['pname'];
					$pid=$row['pid'];
					
				}else{	
					return $msg = "支付失败，您的订单号有问题!";
				}
	
	
				#	需要比较返回的金额与商家数据库中订单的金额是否相等，只有相等的情况下才认为是交易成功.
				#	并且需要对返回的处理进行事务控制，进行记录的排它性处理，防止对同一条交易重复发货的情况发生.      	  	
				if($r9_BType=="1" || $r9_BType=="3"){
					if($ordertype=="goods"){ 
		    	  if($this->success_db($r6_Order))  return $msg = "支付成功!<br> <a href='/'>返回主页</a> <a href='/member'>会员中心</a>";
		     		else  return $msg = "支付失败!<br> <a href='/'>返回主页</a> <a href='/member'>会员中心</a>";
		     	}elseif($ordertype=="member") {
		      	if($this->success_mem($r6_Order,$pname,$product,$pid))  return $msg = "支付成功!<br> <a href='/'>返回主页</a> <a href='/member'>会员中心</a>";
		     		else  return $msg = "支付失败!<br> <a href='/'>返回主页</a> <a href='/member'>会员中心</a>";
		     	}
				}elseif($r9_BType=="2"){
					#如果需要应答机制则必须回写流,以success开头,大小写不敏感.
					echo "success";
					if($ordertype=="goods"){ 
		    	  if($this->success_db($r6_Order))  return $msg = "支付成功!<br> <a href='/'>返回主页</a> <a href='/member'>会员中心</a>";
		     		else  return $msg = "支付失败!<br> <a href='/'>返回主页</a> <a href='/member'>会员中心</a>";
		     	}elseif($ordertype=="member") {
		      	if($this->success_mem($r6_Order,$pname,$product,$pid))  return $msg = "支付成功!<br> <a href='/'>返回主页</a> <a href='/member'>会员中心</a>";
		     		else  return $msg = "支付失败!<br> <a href='/'>返回主页</a> <a href='/member'>会员中心</a>";
		     	}
				}
			}
			
		}else{
			$this->log_result ("verify_failed");
			return 	$msg = "交易信息被篡!<br> <a href='/'>返回主页</a> ";
		}
  }
  
	
	#签名函数生成签名串
	function getReqHmacString($p1_MerId,$merchantKey,$p2_Order,$p3_Amt,$p4_Cur,$p5_Pid,$p6_Pcat,$p7_Pdesc,$p8_Url,$pa_MP,$pd_FrpId,$pr_NeedResponse)
	{	
		#进行签名处理，一定按照文档中标明的签名顺序进行
		$sbOld = "";
		#加入业务类型
		$sbOld = $sbOld.$this->p0_Cmd;
		#加入商户编号
		$sbOld = $sbOld.$p1_MerId;
		#加入商户订单号
		$sbOld = $sbOld.$p2_Order;     
		#加入支付金额
		$sbOld = $sbOld.$p3_Amt;
		#加入交易币种
		$sbOld = $sbOld.$p4_Cur;
		#加入商品名称
		$sbOld = $sbOld.$p5_Pid;
		#加入商品分类
		$sbOld = $sbOld.$p6_Pcat;
		#加入商品描述
		$sbOld = $sbOld.$p7_Pdesc;
		#加入商户接收支付成功数据的地址
		$sbOld = $sbOld.$p8_Url;
		#加入送货地址标识
		$sbOld = $sbOld.$this->p9_SAF;
		#加入商户扩展信息
		$sbOld = $sbOld.$pa_MP;
		#加入银行编码
		$sbOld = $sbOld.$pd_FrpId;
		#加入是否需要应答机制
		$sbOld = $sbOld.$pr_NeedResponse;
		
		return $this->HmacMd5($sbOld,$merchantKey);
	  
	} 
	
	
	/*处理商品交易*/
	function success_db($buyid)
	{
		require_once DEDEINC.'/memberlogin.class.php';
  	$cfg_ml = new MemberLogin();
		$cfg_ml->PutLoginInfo($cfg_ml->M_ID);
		//获取订单信息，检查订单的有效性
		$row = $this->dsql->GetOne("Select state From #@__shops_orders where oid='$buyid' ");
		if($row['state'] > 0)
		{
			return true;
		}	
		$sql = "UPDATE `#@__shops_orders` SET `state`='1' WHERE `oid`='$buyid' AND `userid`='".$cfg_ml->M_ID."';";
		if($this->dsql->ExecuteNoneQuery($sql))
		{
			return true;
		}else{
			return false;
		}	
		return false;
	}
	
	 /*处理点卡，会员升级*/
  function success_mem($order_sn,$pname,$product,$pid){
  	require_once DEDEINC.'/memberlogin.class.php';
  	$cfg_ml = new MemberLogin();
		$cfg_ml->PutLoginInfo($cfg_ml->M_ID);
    
    //更新交易状态为已付款
		$sql = "UPDATE `#@__member_operation` SET `sta`='1' WHERE `buyid`='$order_sn' AND `mid`='".$cfg_ml->M_ID."'";
		$this->dsql->ExecuteNoneQuery($sql);

		/* 改变点卡订单状态_支付成功 */
		if($product=="card"){
			$row = $this->dsql->GetOne("Select cardid From #@__moneycard_record where ctid='$pid' And isexp='0' ");
			
			//如果找不到某种类型的卡，直接为用户增加金币
			if(!is_array($row))
			{
				$nrow = $this->dsql->GetOne("SELECT num FROM #@__moneycard_type WHERE pname = '{$pname}'");
				$dnum = $nrow['num'];
				$sql1 = "UPDATE `#@__member` SET `money`=money+'{$nrow['num']}' WHERE `mid`='".$cfg_ml->M_ID."'";
				$oldinf="直接充值了".$nrow['num']."金币到帐号！";
			}else{
				$cardid = $row['cardid'];
				$sql1=" Update #@__moneycard_record set uid='".$cfg_ml->M_ID."',isexp='1',utime='".time()."' where cardid='$cardid' ";
				$oldinf="充值密码：".$cardid;
			}
			//更新交易状态为已关闭
			$sql2=" Update #@__member_operation set sta=2,oldinfo='$oldinf' where buyid='$order_sn'";
			if($this->dsql->ExecuteNoneQuery($sql1) && $this->dsql->ExecuteNoneQuery($sql2)){
		    $this->dsql->Close();
		    $this->log_result("verify_success,订单号:".$order_sn); //将验证结果存入文件
			  return true;
			}else{
				$this->dsql->Close();
				$this->log_result ("verify_failed,订单号:".$order_sn);//将验证结果存入文件
			  return false;
			}
	  /* 改变会员订单状态_支付成功 */
		}elseif($product=="member"){
			$row = $dsql->GetOne("Select rank,exptime From #@__member_type where aid='$pid' ");
			$rank = $row['rank'];
			$exptime = $row['exptime'];
			/*计算原来升级剩余的天数*/
			$rs = $this->dsql->GetOne("Select uptime,exptime From #@__member where mid='".$cfg_ml->M_ID."'");
			if($rs['uptime']!=0 && $rs['exptime']!=0 ) {
				$nowtime = time();
  			$mhasDay = $rs['exptime'] - ceil(($nowtime - $rs['uptime'])/3600/24) + 1;
  			$mhasDay=($mhasDay>0)? $mhasDay : 0;
			}
			$sql1 = "Update #@__member set rank='$rank',exptime='$exptime',uptime='".time()."' where mid='".$cfg_ml->M_ID."'";
			//更新交易状态为已关闭
			$sql2=" Update #@__member_operation set sta='2',oldinfo='会员升级成功!' where buyid='$order_sn' ";
			if($this->dsql->ExecuteNoneQuery($sql1) && $this->dsql->ExecuteNoneQuery($sql2)){
		    $this->dsql->Close();
		    $this->log_result("verify_success,订单号:".$order_sn); //将验证结果存入文件
			  return true;
			}else{
				$this->dsql->Close();
				$this->log_result ("verify_failed,订单号:".$order_sn);//将验证结果存入文件
			  return false;
			}
		}	
  }
	
  function  log_result($word) {
  	global $cfg_cmspath;
		$fp = fopen(dirname(__FILE__)."/../../data/payment/log.txt","a");
		flock($fp, LOCK_EX) ;
		fwrite($fp,$word.",执行日期:".strftime("%Y-%m-%d %H:%I:%S",time())."\r\n");
		flock($fp, LOCK_UN);
		fclose($fp);
	}
	
}
?>