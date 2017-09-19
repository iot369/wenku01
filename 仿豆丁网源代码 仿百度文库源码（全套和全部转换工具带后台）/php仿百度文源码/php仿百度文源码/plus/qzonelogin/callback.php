<?php
require_once("config.php");
if(!is_valid_openid($_REQUEST["openid"], $_REQUEST["timestamp"], $_REQUEST["oauth_signature"])){
    echo "###invalid openid\n";
    echo "sig:".$_REQUEST["oauth_signature"]."\n";
    exit;
}
//用授权的request token换取access token
$access_str = get_access_token($_SESSION["appid"], $_SESSION["appkey"], $_REQUEST["oauth_token"], $_SESSION["secret"], $_REQUEST["oauth_vericode"]);
$result = array();
parse_str($access_str, $result);
//错误处理
if (isset($result["error_code"]))
{
    echo "get access token error<br>";
    echo "error msg: {$result['error_code']}<br>";
    echo "点击<a href=\"http://wiki.opensns.qq.com/wiki/%E3%80%90QQ%E7%99%BB%E5%BD%95%E3%80%91%E5%85%AC%E5%85%B1%E8%BF%94%E5%9B%9E%E7%A0%81%E8%AF%B4%E6%98%8E\" target=_blank>查看错误码</a>";
    exit;
}
$openid = $result['openid'];
$token = $result['oauth_token'];
$secret = $result['oauth_token_secret'];
$tokenUserInfo = get_user_info($_SESSION["appid"], $_SESSION["appkey"], $token, $secret, $openid);
//$NickName = utf82gb($tokenUserInfo['nickname']);//GBK需要转码
$NickName = $tokenUserInfo['nickname'];
$Face30 = $tokenUserInfo['figureurl'];
$Face50 = $tokenUserInfo['figureurl_1'];
$Face100 = $tokenUserInfo['figureurl_2'];
if(empty($type)) $type = 'login';
if(!empty($webcall)) $gourl = base64_decode($webcall);
$gourl = explode('?',$gourl);
$gourl = $gourl[0];
if($type == 'login'){
	if(GetOneQzoneMember($openid) == 'no'){//若没有绑定会员
		if($openid && $token && $secret){
			require_once('templets/qzonelogin.htm');
		}else{
			ShowMsg('未知错误，请联系管理员',$cfg_cmsurl);
			exit();
		}
	}else{
		$stat = GetOneQzoneMember($openid,'stat');
		if($stat == 0){
			ShowMsg("您当前的QQ互联权限已被锁定，请联系系统管理员", $cfg_cmsurl);
			exit();
		}	
		$mid = GetOneQzoneMember($openid);
		PutCookie('DedeUserID',$mid,86400);
		PutCookie('DedeLoginTime',time(),86400);
		if(defined('UC_API') && @include_once DEDEROOT.'/uc_client/client.php'){				
			$row = $dsql->GetOne("SELECT userid from #@__member where mid = '{$mid}';");
			$username = $row['userid'];
			if($data = uc_get_user($username)){
				list($uid, $username, $email) = $data;
				$ucsynlogin = uc_user_synlogin($uid);
				ShowMsg("登录成功，感谢使用QQ互联！", $gourl);
				exit();
			}else{
				ShowMsg("论坛同步登录失败，感谢使用QQ互联！", $gourl);
				exit();
			}
		}else{
			ShowMsg("登录成功，感谢使用QQ互联！", $gourl);
			exit();
		}
	}
}elseif($type == 'user'){//会员中心解绑
	$InsertTokenArr = array('openid'=>$openid, 'token'=>$token,'secret'=>$secret);
	if(GetOneQzoneMember($openid) == 'no'){//若没有绑定会员		
		if($cfg_ml->M_ID > 0){
			InsertOneOpenId($cfg_ml->M_ID, $InsertTokenArr);//绑定会员数据
			ShowMsg("帐号与QQ号码绑定成功，感谢您的使用", $cfg_memberurl."/qzonelogin_bind.php");
			exit();
		}else{
			ShowMsg("您尚未登录，无法进行此操作", $cfg_memberurl);
			exit();
		}
	}else{
		$mid = GetOneQzoneMember($openid);
		if($cfg_ml->M_ID != $mid || $openid != GetOneQzoneMember($openid,'openid') || $secret != GetOneQzoneMember($openid,'secret')){
			ShowMsg('数据效验不正确，无法解绑', $cfg_memberurl."/qzonelogin_bind.php");
			exit();
		}else{
			DeleteOneOpenId($mid);//绑定会员数据
			ShowMsg('帐号于QQ号码解绑成功，感谢您的使用', $cfg_memberurl);
			exit();
		}
	}
}else{
	die('error');
}
?>