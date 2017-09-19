<?php
require_once(dirname(__FILE__)."/config.php");
function showJSON($msg, $code){
	echo "{code:'".$code."', msg:'".$msg."'}";
}


if(!isset($vdcode))
{
	$vdcode = '';
}
$svali = GetCkVdValue();
if(preg_match("/2/",$safe_gdopen)){
	if(strtolower($vdcode)!=$svali || $svali=='')
	{
		ResetVdValue();
		showJSON('验证码错误！', '-1');
		exit();
	}
	
}
if(CheckUserID($userid,'',false)!='ok')
{
	showJSON("你输入的用户名 {$userid} 不合法！","-1");
	exit();
}
if($pwd=='')
{
	showJSON("密码不能为空！","-1",0,2000);
	exit();
}

//检查帐号
$rs = $cfg_ml->CheckUser($userid,$pwd);		

#api{{
if(defined('UC_API') && @include_once DEDEROOT.'/uc_client/client.php')
{
	//检查帐号
	list($uid, $username, $password, $email) = uc_user_login($userid, $pwd);
	if($uid > 0) {
		$password = md5($password);
		//当UC存在用户,而CMS不存在时,就注册一个	
		if(!$rs) {
			//会员的默认金币
			$row = $dsql->GetOne("SELECT `money`,`scores` FROM `#@__arcrank` WHERE `rank`='10' ");
			$scores = is_array($row) ? $row['scores'] : 0;
			$money = is_array($row) ? $row['money'] : 0;
			$logintime = $jointime = time();
			$loginip = $joinip = GetIP();
			$res = $dsql->ExecuteNoneQuery("INSERT INTO #@__member SET `mtype`='个人',`userid`='$username',`pwd`='$password',`uname`='$username',`sex`='男' ,`rank`='10',`money`='$money', `email`='$email', `scores`='$scores', `matt`='0', `face`='',`safequestion`='0',`safeanswer`='', `jointime`='$jointime',`joinip`='$joinip',`logintime`='$logintime',`loginip`='$loginip';");
			if($res) {
				$mid = $dsql->GetLastID();
				$data = array
				(
				0 => "INSERT INTO `#@__member_person` SET `mid`='$mid', `onlynet`='1', `sex`='男', `uname`='$username', `qq`='', `msn`='', `tel`='', `mobile`='', `place`='', `oldplace`='0' ,
						 `birthday`='1980-01-01', `star`='1', `income`='0', `education`='0', `height`='160', `bodytype`='0', `blood`='0', `vocation`='0', `smoke`='0', `marital`='0', `house`='0',
			   `drink`='0', `datingtype`='0', `language`='', `nature`='', `lovemsg`='', `address`='',`uptime`='0';",
				1 => "INSERT INTO `#@__member_tj` SET `mid`='$mid',`article`='0',`album`='0',`archives`='0',`homecount`='0',`pagecount`='0',`feedback`='0',`friend`='0',`stow`='0';",
				2 => "INSERT INTO `#@__member_space` SET `mid`='$mid',`pagesize`='10',`matt`='0',`spacename`='{$uname}的空间',`spacelogo`='',`spacestyle`='person', `sign`='',`spacenews`='';",
				3 => "INSERT INTO `#@__member_flink` SET `mid`='$mid', `title`='维软内容管理系统', `url`='http://www.uqbook.cn';"
				);						
				foreach($data as $val) $dsql->ExecuteNoneQuery($val);
			}
		}
		$rs = 1;
		$row = $dsql->GetOne("SELECT `mid`, `pwd` FROM #@__member WHERE `userid`='$username'");
		if(isset($row['mid']))
		{
			$cfg_ml->PutLoginInfo($row['mid']);
			if($password!=$row['pwd']) $dsql->ExecuteNoneQuery("UPDATE #@__member SET `pwd`='$password' WHERE mid='$row[mid]'");
		}
		//生成同步登录的代码
		$ucsynlogin = uc_user_synlogin($uid);
	} elseif($uid == -1) {
		//当UC不存在该用而CMS存在,就注册一个.
		if($rs) {
			$row = $dsql->GetOne("SELECT `email` FROM #@__member WHERE userid='$userid'");					
			$uid = uc_user_register($userid, $pwd, $row['email']);
			if($uid > 0) $ucsynlogin = uc_user_synlogin($uid);
		} else {
			$rs = -1;
		}
	} else {
		$rs = -1;
	}
}
#/aip}}		

if($rs==0)
{
	showJSON("用户名不存在！","-1",0,2000);
	exit();
}
else if($rs==-1) {
	showJSON("密码错误！","-1",0,2000);
	exit();
}
else if($rs==-2) {
	showJSON("管理员帐号不允许从前台登录！","-1",0,2000);
	exit();
}
else
{
	showJSON("成功登录",0);//登录成功
	exit();
}
?>