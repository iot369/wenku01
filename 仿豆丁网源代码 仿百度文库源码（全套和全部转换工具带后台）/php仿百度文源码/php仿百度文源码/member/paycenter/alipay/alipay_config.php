<?php
//↓↓↓↓↓↓↓↓↓↓请在这里配置您的基本信息↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓
//合作身份者ID\
$partner		= $payment_userid[2];

//安全检验码，以数字和字母组成的32位字符
$key   			= $payment_key[2];

//签约支付宝账号或卖家支付宝帐户
$seller_email	= $payment_email[2];

//交易过程中服务器通知的页面 要用 http://格式的完整路径，不允许加?id=123这类自定义参数
$notify_url		= $cfg_basehost.$cfg_memberurl."/paycenter/alipay/notify_url.php";// 异步返回地址 需要填写完整的路径

//付完款后跳转的页面 要用 http://格式的完整路径，不允许加?id=123这类自定义参数
$return_url		= $cfg_basehost.$cfg_memberurl."/paycenter/alipay/return_url.php"; //同步返回地址  需要填写完整大额路径

//网站商品的展示地址，不允许加?id=123这类自定义参数
$show_url		= "http://www.alipay.com";

//收款方名称，如：公司名称、网站名称、收款人姓名等
$mainname		= "在线文档分享";

//↑↑↑↑↑↑↑↑↑↑请在这里配置您的基本信息↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑



//签名方式 不需修改
$sign_type		= "MD5";

//字符编码格式 目前支持 GBK 或 utf-8
$_input_charset	= "utf-8";

//访问模式,根据自己的服务器是否支持ssl访问，若支持请选择https；若不支持请选择http
$transport		= "http";

?>