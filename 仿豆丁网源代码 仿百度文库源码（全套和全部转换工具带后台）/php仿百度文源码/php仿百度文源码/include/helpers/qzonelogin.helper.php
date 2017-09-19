<?php
if(!defined('DEDEINC')) exit('dedecms');
/**
 *  检查是否绑定
 *
 * @Intro		检查会员是否绑定OpenId     
 * @Update		2011-06-03 02:26:54
 * @Copyright	家饰吧（http://www.jiashi8.com）
 */
if (!function_exists('GetOneQzoneMember')){
    function GetOneQzoneMember($openid,$field='mid'){
		global $dsql;		
		$row = $dsql->GetOne("SELECT * FROM `#@__member_qzonelogin` WHERE openid = '{$openid}' LIMIT 0,1");
		if(!is_array($row)){
			$return = 'no';
		}else{
			$return = $row[$field];
		}
		return $return;
    }
}



/**
 *  绑定会员openid
 *
 * @Intro		绑定会员OpenId     
 * @Update		2011-06-03 02:26:54
 * @Copyright	家饰吧（http://www.jiashi8.com）
 */
if (!function_exists('InsertOneOpenId')){
    function InsertOneOpenId($mid, $accesstoken = array()){
		global $dsql;
		$openid = $accesstoken['openid'];
		$token = $accesstoken['token'];
		$secret = $accesstoken['secret'];
		$dsql->ExecuteNoneQuery("INSERT INTO `#@__member_qzonelogin` (mid,openid,token,secret,dtime) VALUES('$mid','$openid','$token','$secret',".time()."); ");
		return true;
    }	
}

/**
 *  删除会员openid
 *
 * @Intro		删除会员openid    
 * @Update		2011-06-03 02:26:54
 * @Copyright	家饰吧（http://www.jiashi8.com）
 */
if (!function_exists('DeleteOneOpenId')){
    function DeleteOneOpenId($mid){
		global $dsql;
		$dsql->ExecuteNoneQuery("DELETE FROM `#@__member_qzonelogin` WHERE mid='{$mid}'");
		return true;
    }	
}

/**
 *  查询指定会员信息
 *
 * @Intro		绑定会员OpenId     
 * @Update		2011-06-03 02:26:54
 * @Copyright	家饰吧（http://www.jiashi8.com）
 */
if (!function_exists('GetOneMemberInfo')){
    function GetOneMemberInfo($mid,$field='mid'){
		global $dsql;
		$row = $dsql->GetOne("SELECT * FROM `#@__member` WHERE mid = '{$mid}' LIMIT 0,1");
		if(is_array($row)){
			return $row[$field];
		}else{
			return false;
		}
    }	
}

/**
 *  查询指定会员信息
 *
 * @Intro		查询会员信息    
 * @Update		2011-06-03 02:26:54
 * @Copyright	家饰吧（http://www.jiashi8.com）
 */
if (!function_exists('GetOneQzoneMemberStat')){
    function GetOneQzoneMemberStat($stat,$type='view'){
		global $dsql;
		if($type == 'view'){
			$result = ($stat == 1) ? '正常' : '锁定';
		}else{
			$result = ($stat == 1) ? '锁定' : '正常';
		}
		return $result;
    }	
}

/**
 *  字符串加密解密函数
 *
 * @Intro		字符串加解密     
 * @Update		2011-06-03 02:26:54
 * @Copyright	家饰吧（http://www.jiashi8.com）
 */
if (!function_exists('authcode')){
	function authcode($string, $operation = 'DECODE', $key = '', $expiry = 0){
		$ckey_length = 4;
		$key = md5($key != '' ? $key : getglobal('authkey'));
		$keya = md5(substr($key, 0, 16));
		$keyb = md5(substr($key, 16, 16));
		$keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length): substr(md5(microtime()), -$ckey_length)) : '';	
		$cryptkey = $keya.md5($keya.$keyc);
		$key_length = strlen($cryptkey);	
		$string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0).substr(md5($string.$keyb), 0, 16).$string;
		$string_length = strlen($string);	
		$result = '';
		$box = range(0, 255);	
		$rndkey = array();
		for($i = 0; $i <= 255; $i++) {
			$rndkey[$i] = ord($cryptkey[$i % $key_length]);
		}	
		for($j = $i = 0; $i < 256; $i++) {
			$j = ($j + $box[$i] + $rndkey[$i]) % 256;
			$tmp = $box[$i];
			$box[$i] = $box[$j];
			$box[$j] = $tmp;
		}
		for($a = $j = $i = 0; $i < $string_length; $i++) {
			$a = ($a + 1) % 256;
			$j = ($j + $box[$a]) % 256;
			$tmp = $box[$a];
			$box[$a] = $box[$j];
			$box[$j] = $tmp;
			$result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
		}	
		if($operation == 'DECODE') {
			if((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16)) {
				return substr($result, 26);
			} else {
				return '';
			}
		} else {
			return $keyc.str_replace('=', '', base64_encode($result));
		}
	}	
}


/**
 *  请求临时token
 *
 * @Intro		请求临时token     
 * @Update		2011-06-03 02:26:54
 * @Copyright	家饰吧（http://www.jiashi8.com）
 */
if (!function_exists('get_request_token')){
	function get_request_token($appid, $appkey){
		$url    = "http://openapi.qzone.qq.com/oauth/qzoneoauth_request_token?";
		$sigstr = "GET"."&".rawurlencode("http://openapi.qzone.qq.com/oauth/qzoneoauth_request_token")."&";
		$params = array();
		$params["oauth_version"]          = "1.0";
		$params["oauth_signature_method"] = "HMAC-SHA1";
		$params["oauth_timestamp"]        = time();
		$params["oauth_nonce"]            = mt_rand();
		$params["oauth_consumer_key"]     = $appid;
		$normalized_str = get_normalized_string($params);
		$sigstr        .= rawurlencode($normalized_str);
		$key = $appkey."&";
		$signature = get_signature($sigstr, $key);
		$url      .= $normalized_str."&"."oauth_signature=".rawurlencode($signature);
		return file_get_contents($url);
	}
}

/**
 *  跳转到QQ登录页面
 *
 * @Intro		跳转到QQ登录页面     
 * @Update		2011-06-03 02:26:54
 * @Copyright	家饰吧（http://www.jiashi8.com）
 */
if (!function_exists('redirect_to_login')){
	function redirect_to_login($appid, $appkey, $callback){
		$redirect = "http://openapi.qzone.qq.com/oauth/qzoneoauth_authorize?oauth_consumer_key=$appid&";
		$result = array();
		$request_token = get_request_token($appid, $appkey);		
		parse_str($request_token, $result);
		$_SESSION["token"]        = $result["oauth_token"];
		$_SESSION["secret"]       = $result["oauth_token_secret"];
		if ($result["oauth_token"] == ""){
			echo "empty token";
			exit;
		}
		$redirect .= "oauth_token=".$result["oauth_token"]."&oauth_callback=".rawurlencode($callback);
		header("Location:$redirect");
	}
}

/**
 *  获取access_token
 *
 * @Intro		获取access_token     
 * @Update		2011-06-03 02:26:54
 * @Copyright	家饰吧（http://www.jiashi8.com）
 */
if (!function_exists('get_access_token')){
	function get_access_token($appid, $appkey, $request_token, $request_token_secret, $vericode){
		$url    = "http://openapi.qzone.qq.com/oauth/qzoneoauth_access_token?";
		$sigstr = "GET"."&".rawurlencode("http://openapi.qzone.qq.com/oauth/qzoneoauth_access_token")."&";
		$params = array();
		$params["oauth_version"]          = "1.0";
		$params["oauth_signature_method"] = "HMAC-SHA1";
		$params["oauth_timestamp"]        = time();
		$params["oauth_nonce"]            = mt_rand();
		$params["oauth_consumer_key"]     = $appid;
		$params["oauth_token"]            = $request_token;
		$params["oauth_vericode"]         = $vericode;
		$normalized_str = get_normalized_string($params);
		$sigstr        .= rawurlencode($normalized_str);	
		$key = $appkey."&".$request_token_secret;
		$signature = get_signature($sigstr, $key);
		$url .= $normalized_str."&"."oauth_signature=".rawurlencode($signature);
		return file_get_contents($url);
	}
}

/**
 *  获取access_token
 *
 * @Intro		获取用户信息     
 * @Update		2011-06-03 02:26:54
 * @Copyright	家饰吧（http://www.jiashi8.com）
 */
if (!function_exists('get_user_info')){
	function get_user_info($appid, $appkey, $access_token, $access_token_secret, $openid)
	{
		$url    = "http://openapi.qzone.qq.com/user/get_user_info";
		$info   = do_get($url, $appid, $appkey, $access_token, $access_token_secret, $openid);
		$arr = array();
		$arr = json_decode($info, true);
		return $arr;
	}
}

/**
 *  参数字典排序
 *
 * @Intro		参数字典排序     
 * @Update		2011-06-03 02:26:54
 * @Copyright	家饰吧（http://www.jiashi8.com）
 */
if (!function_exists('get_normalized_string')){
	function get_normalized_string($params){
		ksort($params);
		$normalized = array();
		foreach($params as $key => $val){
			$normalized[] = $key."=".$val;
		}
		return implode("&", $normalized);
	}
}

/**
 *  生成签名值
 *
 * @Intro		生成签名值     
 * @Update		2011-06-03 02:26:54
 * @Copyright	家饰吧（http://www.jiashi8.com）
 */
if (!function_exists('get_signature')){ 
	function get_signature($str, $key){
		$signature = "";
		if (function_exists('hash_hmac')){
			$signature = base64_encode(hash_hmac("sha1", $str, $key, true));
		}
		else{
			$blocksize	= 64;
			$hashfunc	= 'sha1';
			if (strlen($key) > $blocksize){
				$key = pack('H*', $hashfunc($key));
			}
			$key	= str_pad($key,$blocksize,chr(0x00));
			$ipad	= str_repeat(chr(0x36),$blocksize);
			$opad	= str_repeat(chr(0x5c),$blocksize);
			$hmac 	= pack(
				'H*',$hashfunc(
					($key^$opad).pack(
						'H*',$hashfunc(
							($key^$ipad).$str
						)
					)
				)
			);
			$signature = base64_encode($hmac);
		}	
		return $signature;
	}
}



/**
 *  URL编码
 *
 * @Intro		对字符串进行URL编码，遵循rfc1738 urlencode    
 * @Update		2011-06-03 02:26:54
 * @Copyright	家饰吧（http://www.jiashi8.com）
 */
if (!function_exists('get_urlencode_string')){ 
	function get_urlencode_string($params){
		ksort($params);
		$normalized = array();
		foreach($params as $key => $val){
			$normalized[] = $key."=".rawurlencode($val);
		}	
		return implode("&", $normalized);
	}
}

/**
 * @brief 检查openid是否合法
 *
 * @param $openid  与用户QQ号码一一对应
 * @param $timestamp　时间戳
 * @param $sig　　签名值
 *
 * @return true or false
 */
if (!function_exists('is_valid_openid')){ 
	function is_valid_openid($openid, $timestamp, $sig){
		$key = $_SESSION["appkey"];
		$str = $openid.$timestamp;
		$signature = get_signature($str, $key);
		return $sig == $signature; 
	}
}


/**
 * @brief 所有Get请求都可以使用这个方法
 *
 * @param $url
 * @param $appid
 * @param $appkey
 * @param $access_token
 * @param $access_token_secret
 * @param $openid
 *
 * @return true or false
 */
if (!function_exists('do_get')){  
	function do_get($url, $appid, $appkey, $access_token, $access_token_secret, $openid){
		$sigstr = "GET"."&".rawurlencode("$url")."&";
		$params = $_GET;
		$params["oauth_version"]          = "1.0";
		$params["oauth_signature_method"] = "HMAC-SHA1";
		$params["oauth_timestamp"]        = time();
		$params["oauth_nonce"]            = mt_rand();
		$params["oauth_consumer_key"]     = $appid;
		$params["oauth_token"]            = $access_token;
		$params["openid"]                 = $openid;
		unset($params["oauth_signature"]);
		$normalized_str = get_normalized_string($params);
		$sigstr        .= rawurlencode($normalized_str);
		$key = $appkey."&".$access_token_secret;
		$signature = get_signature($sigstr, $key);
		$url      .= "?".$normalized_str."&"."oauth_signature=".rawurlencode($signature);
		return file_get_contents($url);
	}
}

/**
 * @brief 所有multi-part post 请求都可以使用这个方法
 *
 * @param $url
 * @param $appid
 * @param $appkey
 * @param $access_token
 * @param $access_token_secret
 * @param $openid
 *
 */
if (!function_exists('do_multi_post')){  
	function do_multi_post($url, $appid, $appkey, $access_token, $access_token_secret, $openid){
		$sigstr = "POST"."&"."$url"."&";
		$params = $_POST;
		$params["oauth_version"]          = "1.0";
		$params["oauth_signature_method"] = "HMAC-SHA1";
		$params["oauth_timestamp"]        = time();
		$params["oauth_nonce"]            = mt_rand();
		$params["oauth_consumer_key"]     = $appid;
		$params["oauth_token"]            = $access_token;
		$params["openid"]                 = $openid;
		unset($params["oauth_signature"]);
		foreach ($_FILES as $filename => $filevalue){
			if ($filevalue["error"] != UPLOAD_ERR_OK){
				//echo "upload file error $filevalue['error']\n";
				//exit;
			} 
			$params[$filename] = file_get_contents($filevalue["tmp_name"]);
		}	
		$sigstr .= get_normalized_string($params);
		$key = $appkey."&".$access_token_secret;
		$signature = get_signature($sigstr, $key);
		$params["oauth_signature"] = $signature; 
		foreach ($_FILES as $filename => $filevalue){
			$tmpfile = dirname($filevalue["tmp_name"])."/".$filevalue["name"];
			move_uploaded_file($filevalue["tmp_name"], $tmpfile);
			$params[$filename] = "@$tmpfile";
		}
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE); 
		curl_setopt($ch, CURLOPT_POST, TRUE); 
		curl_setopt($ch, CURLOPT_POSTFIELDS, $params); 
		curl_setopt($ch, CURLOPT_URL, $url);
		$ret = curl_exec($ch);
		curl_close($ch);
		unlink($tmpfile);
		return $ret;
	}
}

/**
 * @brief 所有post 请求都可以使用这个方法
 *
 * @param $url
 * @param $appid
 * @param $appkey
 * @param $access_token
 * @param $access_token_secret
 * @param $openid
 *
 */
if (!function_exists('do_post')){  
	function do_post($url, $appid, $appkey, $access_token, $access_token_secret, $openid){
		$sigstr = "POST"."&".rawurlencode($url)."&";
		$params = $_POST;
		$params["oauth_version"]          = "1.0";
		$params["oauth_signature_method"] = "HMAC-SHA1";
		$params["oauth_timestamp"]        = time();
		$params["oauth_nonce"]            = mt_rand();
		$params["oauth_consumer_key"]     = $appid;
		$params["oauth_token"]            = $access_token;
		$params["openid"]                 = $openid;
		unset($params["oauth_signature"]);
		$sigstr .= rawurlencode(get_normalized_string($params));
		$key = $appkey."&".$access_token_secret;
		$signature = get_signature($sigstr, $key); 
		$params["oauth_signature"] = $signature; 	
		$postdata = get_urlencode_string($params);
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE); 
		curl_setopt($ch, CURLOPT_POST, TRUE); 
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata); 
		curl_setopt($ch, CURLOPT_URL, $url);
		$ret = curl_exec($ch);
		curl_close($ch);
		return $ret;
	}
}
?>