<?php
if(!defined('DEDEINC')) exit('Request Error!');
/**
 *网银在线接口类
 */
class cbpayment
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
  function cbpayment()
  {
  	global $dsql;
		$this->dsql = $dsql;
  }

  function __construct()
  {
    $this->cbpayment();
  }

  /**
   * 生成支付代码
   * @param   array   $order      订单信息
   * @param   array   $payment    支付方式信息
   */
  function GetCode($order, $payment)
  {
  	global $cfg_basehost;
	
	$v_mid = $payment['cbpayment_account'];
	$v_url = $cfg_basehost.'/member/paycenter/cbpayment/receive.php';
	$key   = $payment['cbpayment_key'];
	$v_moneytype = 'CNY'; //币种
	$v_rcvemail = $payment_email[3];//收货人EMAIL
	$v_post_url = 'https://pay3.chinabank.com.cn/PayGate';

	
	if($payment_exp[3] < 0) $payment_exp[3] = 0;

	$piice_ex = $price*$payment_exp[3];

	$v_oid = trim($buyid); //订单号
	if($piice_ex > 0) $price = $price+$piice_ex;
	$v_amount = sprintf("%01.2f", $price);                   //支付金额                 

	$text = $v_amount.$v_moneytype.$v_oid.$v_mid.$v_url.$key;        //md5加密拼凑串,注意顺序不能变
	$v_md5info = strtoupper(md5($text));                             //md5函数加密并转化成大写字母

	$remark1 = trim($ptype);//备注字段1
	$remark2 = trim($pname);//备注字段2

	$v_rcvname   = '站长';		// 收货人
	$v_rcvaddr   = '深圳';		// 收货地址
	$v_rcvtel    = '0755-83791960';		// 收货人电话
	$v_rcvpost   = '100080';		// 收货人邮编
	$v_rcvmobile = '13838384381';		// 收货人手机号

	$v_ordername   = $cfg_ml->M_UserName;	// 订货人姓名
	$v_orderaddr   = '深圳';	// 订货人地址
	$v_ordertel    = '0755-83791960';	// 订货人电话
	$v_orderpost   = 518000;	// 订货人邮编
	$v_orderemail  = 'service@nps.cn';	// 订货人邮件
	$v_ordermobile = 13838384581;	// 订货人手机号

	$strRequestUrl = $v_post_url.'?v_mid='.$v_mid.'&v_oid='.$v_oid.'&v_amount='.$v_amount.'&v_moneytype='.$v_moneytype
		.'&v_url='.$v_url.'&v_md5info='.$v_md5info.'&remark1='.$remark1.'&remark2='.$remark2;

    $button = '<div style="text-align:center"><input type="button" onclick="window.open(\''.$strRequestUrl.'\')" value="立即支付"/></div>';

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
		
	  #	解析返回参数.
		$return = $this->getCallBackValue($r0_Cmd,$r1_Code,$r2_TrxId,$r3_Amt,$r4_Cur,$r5_Pid,$r6_Order,$r7_Uid,$r8_MP,$r9_BType,$hmac);
		
		#	判断返回签名是否正确（True/False）
		$bRet = $this->CheckHmac($p1_MerId,$merchantKey,$r0_Cmd,$r1_Code,$r2_TrxId,$r3_Amt,$r4_Cur,$r5_Pid,$r6_Order,$r7_Uid,$r8_MP,$r9_BType,$hmac);
    
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
	
	#	取得返回串中的所有参数
	function getCallBackValue(&$r0_Cmd,&$r1_Code,&$r2_TrxId,&$r3_Amt,&$r4_Cur,&$r5_Pid,&$r6_Order,&$r7_Uid,&$r8_MP,&$r9_BType,&$hmac)
	{  
		$r0_Cmd		= $_REQUEST['r0_Cmd'];
		$r1_Code	= $_REQUEST['r1_Code'];
		$r2_TrxId	= $_REQUEST['r2_TrxId'];
		$r3_Amt		= $_REQUEST['r3_Amt'];
		$r4_Cur		= $_REQUEST['r4_Cur'];
		$r5_Pid		= $_REQUEST['r5_Pid'];
		$r6_Order	= $_REQUEST['r6_Order'];
		$r7_Uid		= $_REQUEST['r7_Uid'];
		$r8_MP		= $_REQUEST['r8_MP'];
		$r9_BType	= $_REQUEST['r9_BType']; 
		$hmac			= $_REQUEST['hmac'];
		return null;
	}
	
	function CheckHmac($p1_MerId,$merchantKey,$r0_Cmd,$r1_Code,$r2_TrxId,$r3_Amt,$r4_Cur,$r5_Pid,$r6_Order,$r7_Uid,$r8_MP,$r9_BType,$hmac)
	{
		if($hmac == $this->getCallbackHmacString($p1_MerId,$merchantKey,$r0_Cmd,$r1_Code,$r2_TrxId,$r3_Amt,$r4_Cur,$r5_Pid,$r6_Order,$r7_Uid,$r8_MP,$r9_BType))
			return true;
		else
			return false;
	}

	function getCallbackHmacString($p1_MerId,$merchantKey,$r0_Cmd,$r1_Code,$r2_TrxId,$r3_Amt,$r4_Cur,$r5_Pid,$r6_Order,$r7_Uid,$r8_MP,$r9_BType)
	{
		
		#取得加密前的字符串
		$sbOld = "";
		#加入商家ID
		$sbOld = $sbOld.$p1_MerId;
		#加入消息类型
		$sbOld = $sbOld.$r0_Cmd;
		#加入业务返回码
		$sbOld = $sbOld.$r1_Code;
		#加入交易ID
		$sbOld = $sbOld.$r2_TrxId;
		#加入交易金额
		$sbOld = $sbOld.$r3_Amt;
		#加入货币单位
		$sbOld = $sbOld.$r4_Cur;
		#加入产品Id
		$sbOld = $sbOld.$r5_Pid;
		#加入订单ID
		$sbOld = $sbOld.$r6_Order;
		#加入用户ID
		$sbOld = $sbOld.$r7_Uid;
		#加入商家扩展信息
		$sbOld = $sbOld.$r8_MP;
		#加入交易结果返回类型
		$sbOld = $sbOld.$r9_BType;
		
		return $this->HmacMd5($sbOld,$merchantKey,'gbk');
	
	}
	
	function HmacMd5($data,$key,$lang='utf-8')
	{
		// RFC 2104 HMAC implementation for php.
		// Creates an md5 HMAC.
		// Eliminates the need to install mhash to compute a HMAC
		// Hacked by Lance Rushing(NOTE: Hacked means written)
		
		//需要配置环境支持iconv，否则中文参数不能正常处理
		if($GLOBALS['cfg_soft_lang'] != 'utf-8' || $lang!='utf-8'){
			if(!function_exists('iconv')){
				exit('Not install iconv lib!');
			}else{
				$key = iconv("GB2312","UTF-8//IGNORE",$key);
				$data = iconv("GB2312","UTF-8//IGNORE",$data);
			}
		}
		$b = 64; // byte length for md5
		if (strlen($key) > $b) {
		$key = pack("H*",md5($key));
		}
		$key = str_pad($key, $b, chr(0x00));
		$ipad = str_pad('', $b, chr(0x36));
		$opad = str_pad('', $b, chr(0x5c));
		$k_ipad = $key ^ $ipad ;
		$k_opad = $key ^ $opad;
		
		return md5($k_opad . pack("H*",md5($k_ipad . $data)));
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