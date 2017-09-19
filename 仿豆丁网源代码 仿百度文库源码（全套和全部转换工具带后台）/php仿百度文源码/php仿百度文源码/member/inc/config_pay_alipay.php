<?php
require_once(DEDEMEMBER."/paycenter/alipay/alipay_config.php");
require_once(DEDEMEMBER."/paycenter/alipay/class/alipay_service.php");

if($payment_exp[2] < 0) $payment_exp[2] = 0;
$piice_ex = $price*$payment_exp[2];

/**
$parameter = array(
"service" => "create_direct_pay_by_user", //交易类型 实物标准双接口服务名称：trade_create_by_buyer ； 中介担保交易（纯担保交易）服务名称：create_partner_trade_by_buyer；实时到账接口：create_direct_pay_by_user
"partner" => $partner,												//合作商户号
"return_url" => $return_url,  //同步返回
"notify_url" => $notify_url,  //异步返回
"_input_charset" => $_input_charset,          //字符集，默认为GBK
"subject" => $ptype,                          //商品名称，必填
"body" => $pname,                             //商品描述，必填
"out_trade_no" => $buyid,                     //商品外部交易号，必填,每次测试都须修改
"logistics_fee" => '0.00',                    //物流配送费用
"logistics_payment"=>'BUYER_PAY',             // 物流配送费用付款方式：SELLER_PAY(卖家支付)、BUYER_PAY(买家支付)、BUYER_PAY_AFTER_RECEIVE(货到付款)
"logistics_type"=>'EXPRESS',                  // 物流配送方式：POST(平邮)、EMS(EMS)、EXPRESS(其他快递)

"price" => sprintf("%01.2f", $price),         //商品单价，必填
"payment_type"=>"1",                          // 默认为1,不需要修改
"quantity" => "1",                            //商品数量，必填
"show_url" => $show_url,            					//商品相关网站
"seller_email" => $seller_email               //卖家邮箱，必填
);
*/

//扩展功能参数——默认支付方式
$pay_mode	  = $_POST['pay_bank'];
if ($pay_mode == "directPay") {
	$paymethod    = "directPay";	//默认支付方式，四个值可选：bankPay(网银); cartoon(卡通); directPay(余额); CASH(网点支付)
	$defaultbank  = "";
}
else {
	$paymethod    = "bankPay";		//默认支付方式，四个值可选：bankPay(网银); cartoon(卡通); directPay(余额); CASH(网点支付)
	$defaultbank  = $pay_mode;		//默认网银代号，代号列表见http://club.alipay.com/read.php?tid=8681379
}


//扩展功能参数——防钓鱼
//请慎重选择是否开启防钓鱼功能
//exter_invoke_ip、anti_phishing_key一旦被使用过，那么它们就会成为必填参数
//开启防钓鱼功能后，服务器、本机电脑必须支持远程XML解析，请配置好该环境。
//若要使用防钓鱼功能，请打开class文件夹中alipay_function.php文件，找到该文件最下方的query_timestamp函数，根据注释对该函数进行修改
//建议使用POST方式请求数据
$anti_phishing_key  = '';			//防钓鱼时间戳
$exter_invoke_ip = '';				//获取客户端的IP地址，建议：编写获取客户端IP地址的程序
//如：
//$exter_invoke_ip = '202.1.1.1';
//$anti_phishing_key = query_timestamp($partner);		//获取防钓鱼时间戳函数

//扩展功能参数——其他
$extra_common_param = '';			//自定义参数，可存放任何内容（除=、&等特殊字符外），不会显示在页面上
$buyer_email		= '';			//默认买家支付宝账号

//扩展功能参数——分润(若要使用，请按照注释要求的格式赋值)
$royalty_type		= "";			//提成类型，该值为固定值：10，不需要修改
$royalty_parameters	= "";
//提成信息集，与需要结合商户网站自身情况动态获取每笔交易的各分润收款账号、各分润金额、各分润说明。最多只能设置10条
//各分润金额的总和须小于等于total_fee
//提成信息集格式为：收款方Email_1^金额1^备注1|收款方Email_2^金额2^备注2
//如：
//royalty_type = "10"
//royalty_parameters	= "111@126.com^0.01^分润备注一|222@126.com^0.01^分润备注二"


/////////////////////////////////////////////////

//构造要请求的参数数组，无需改动
$parameter = array(
        "service"			=> "create_direct_pay_by_user",	////交易类型 实物标准双接口服务名称：trade_create_by_buyer ； 中介担保交易（纯担保交易）服务名称：create_partner_trade_by_buyer；实时到账接口：create_direct_pay_by_user
        "payment_type"		=> "1",//交易类型，不需要修改

        //获取配置文件(alipay_config.php)中的值
        "partner"			=> $partner,
        "seller_email"		=> $seller_email,
        "return_url"		=> $return_url,
        "notify_url"		=> $notify_url,
        "_input_charset"	=> $_input_charset,
        "show_url"			=> $show_url,

        //从订单数据中动态获取到的必填参数
        "out_trade_no"		=> $buyid,
        "subject"			=> $ptype,
        "body"				=> $pname,
        "total_fee"			=> sprintf("%01.2f", $price),

        //扩展功能参数——网银提前
        "paymethod"			=> $paymethod,
        "defaultbank"		=> $defaultbank,

        //扩展功能参数——防钓鱼
        "anti_phishing_key"	=> $anti_phishing_key,
		"exter_invoke_ip"	=> $exter_invoke_ip,

		//扩展功能参数——自定义参数
		"buyer_email"		=> $buyer_email,
        "extra_common_param"=> $extra_common_param,
		
		//扩展功能参数——分润
        "royalty_type"		=> $royalty_type,
        "royalty_parameters"=> $royalty_parameters
);


//构造请求函数
$alipay = new alipay_service($parameter,$key,$sign_type);
$sHtmlText = $alipay->build_form();

//$alipay = new alipay_service($parameter,$security_code,$sign_type);
//$link	= $alipay->create_url();
/**
echo '<html>
<head>
	<title>转到支付宝支付页面</title>
</head>
<body onLoad="document.alipay.submit();">
	<form name="alipay" action="'.$link.'" method="post">
	</form>
</body>
</html>';
exit;
*/
?>
<html>
    <head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <title>支付宝即时支付</title>
        <style type="text/css">
            .font_content{
                font-family:"宋体";
                font-size:14px;
                color:#FF6600;
            }
            .font_title{
                font-family:"宋体";
                font-size:16px;
                color:#FF0000;
                font-weight:bold;
            }
            table{
                border: 1px solid #CCCCCC;
            }
        </style>
    </head>
    <body>
        <table align="center" width="350" cellpadding="5" cellspacing="0">
            <tr>
                <td align="center" class="font_title" colspan="2">订单确认</td>
            </tr>
            <tr>
                <td class="font_content" align="right">订单号：</td>
                <td class="font_content" align="left"><?php echo $buyid; ?></td>
            </tr>
            <tr>
                <td class="font_content" align="right">付款总金额：</td>
                <td class="font_content" align="left"><?php echo sprintf("%01.2f", $price); ?></td>
            </tr>
            <tr>
                <td align="center" colspan="2"><?php echo $sHtmlText; ?></td>
            </tr>
        </table>
    </body>
</html>
