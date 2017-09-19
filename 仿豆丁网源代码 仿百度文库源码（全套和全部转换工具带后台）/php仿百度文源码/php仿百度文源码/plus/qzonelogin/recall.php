<?php
require(dirname(__FILE__).'/../../include/common.inc.php');
require_once(DEDEINC.'/memberlogin.class.php');
$cfg_ml = new MemberLogin();
require_once(DEDEINC.'/helpers/qzonelogin.helper.php');
if(empty($action)) $action = '';
$gourl = empty($gourl) ? $cfg_cmsurl : $gourl;
if($cfg_mb_allowreg=='N')
{
    ShowMsg('系统关闭了新用户注册！', $cfg_cmsurl);
    exit();
}
if($action == ''){
	die('dir');
}else{
	if(!$openid || !$token || !$secret){
		ShowMsg('数据效验出错，请联系管理员',$cfg_cmsurl);
		exit();
	}
	$InsertTokenArr = array('openid'=>$openid, 'token'=>$token,'secret'=>$secret);
	if($action == 'reg'){
		require_once DEDEINC.'/membermodel.cls.php';
		$mtype = '个人';
		$nowtime = time();
		$userid = trim(HtmlReplace($userid));
		$pwd = md5($nowtime);
		$jointime = time();
		$logintime = time();
		$joinip = GetIP();
		$loginip = GetIP();
		//用户名效验
		if(empty($userid)){
			ShowMsg('用户名不能为空，请重新填写',-1);
			exit();
		}elseif(strlen($userid) < $cfg_mb_idmin || strlen($userid) > 20){
			ShowMsg("用户名长度应该在{$cfg_mb_idmin}至20之间",-1);
			exit();
		}else{
			$msg = CheckUserID($userid, '用户名');
			if($msg != 'ok'){
			   ShowMsg($msg,-1);
			   exit();				
			}
			#api{{
			if(defined('UC_API') && @include_once DEDEROOT.'/uc_client/client.php'){
				$ucresult = uc_user_checkname($userid);
				if($ucresult == -1){
                   ShowMsg("用户名{$userid}用户名不合法",-1);
				   exit();
                }
                elseif($ucresult == -2)                {
                   ShowMsg("用户名{$userid}包含禁止注册的词语",-1);
				   exit();
                }
                elseif($ucresult == -3){
                   ShowMsg("用户名{$userid}用户名已被人使用",-1);
				   exit();
                }
			}
			#/aip}}
		}
		// -- End 用户名效验
		if(empty($email)){
			ShowMsg('注册邮箱不能为空',-1);
			exit();
		}else{
			if(!CheckEmail($email)){
				ShowMsg('注册邮箱格式不正确',-1);
				exit();
			}
			if($cfg_md_mailtest=='Y'){
				$row = $dsql->GetOne("SELECT mid FROM `#@__member` WHERE email LIKE '$email' LIMIT 1");
				if(is_array($row)){
					ShowMsg('您的邮箱已被他人使用，请更换或重试',-1);
					exit();					
				}
			}
			#api{{
			if(defined('UC_API') && @include_once DEDEROOT.'/uc_client/client.php')
			{
				$ucresult = uc_user_checkemail($email);
				if($ucresult == -4) {
					ShowMsg('注册邮箱格式不正确',-1);
					exit();
				} elseif($ucresult == -5) {
					ShowMsg('您的邮箱被禁止注册',-1);
					exit();
				} elseif($ucresult == -6) {
					ShowMsg('您的邮箱已被他人使用，请更换或重试',-1);
					exit();	
				}
			}
			#/aip}} 			
		}
		// -- End email效验
		$face = '';
		if($use_qzone_avatar == 1){
			$face .= $facepic;
		}
		$dfscores = $dfmoney = 0;
		$dfrank = $dsql->GetOne("SELECT money,scores FROM `#@__arcrank` WHERE rank='10' ");				
		if(is_array($dfrank))
		{
			$dfmoney = $dfrank['money'];
			$dfscores = $dfrank['scores'];
		}
		$spaceSta = ($cfg_mb_spacesta < 0 ? $cfg_mb_spacesta : 0);
		$inQuery = "INSERT INTO `#@__member` (`mtype` ,`userid` ,`pwd` ,`uname` ,`sex` ,`rank` ,`money` ,`email` ,`scores` , `matt`, `spacesta` ,`face`,`safequestion`,`safeanswer` ,`jointime` ,`joinip` ,`logintime` ,`loginip` ) VALUES ('$mtype','$userid','$pwd','$uname','','10','$dfmoney','$email','$dfscores','0','$spaceSta','$face','','','$jointime','$joinip','$logintime','$loginip'); ";
		if($dsql->ExecuteNoneQuery($inQuery)){//如果注册成功
			$mid = $dsql->GetLastID();
			$membertjquery = "INSERT INTO `#@__member_tj` (`mid`,`article`,`album`,`archives`,`homecount`,`pagecount`,`feedback`,`friend`,`stow`) VALUES ('$mid','0','0','0','0','0','0','0','0'); ";
			$dsql->ExecuteNoneQuery($membertjquery);
			$spacequery = "INSERT INTO `#@__member_space`(`mid` ,`pagesize` ,`matt` ,`spacename` ,`spacelogo` ,`spacestyle`, `sign` ,`spacenews`) VALUES('{$mid}','10','0','{$uname}的空间','','person','',''); ";
			$dsql->ExecuteNoneQuery($spacequery);
			$dsql->ExecuteNoneQuery("INSERT INTO `#@__member_flink`(mid,title,url) VALUES('$mid','家饰吧','http://www.jiashi8.com'); ");					
            $membermodel = new membermodel($mtype);
            $modid=$membermodel->modid;
            $modid = empty($modid)? 0 : intval(preg_replace("/[^\d]/",'', $modid));
            $modelform = $dsql->getOne("SELECT * FROM #@__member_model WHERE id='$modid' ");         
            if(!is_array($modelform))
            {
                showmsg('模型表单不存在', '-1');
                exit();
            }else{
                $dsql->ExecuteNoneQuery("INSERT INTO `{$membermodel->table}` (`mid`) VALUES ('{$mid}');");
            }
			InsertOneOpenId($mid, $InsertTokenArr);//绑定会员数据
			PutCookie('DedeUserID',$mid,86400);
			PutCookie('DedeLoginTime',time(),86400);					
			if(defined('UC_API') && @include_once DEDEROOT.'/uc_client/client.php'){
				$uid = uc_user_register($userid, $nowtime, $email);
				if($uid > 0){
					$ucsynlogin = uc_user_synlogin($uid);
				}else{
					ShowMsg("ucenter中心创建帐号失败，请联系管理员",$cfg_cmsurl);
					exit();
				}
			}
			ShowMsg("注册成功，正在为您跳转！", $gourl);					
			exit();					  
		}			
	}elseif($action == 'bind'){
        if($userid == ''){
            ShowMsg("请输入您将要绑定的用户名","-1",0,2000);
            exit();		
		}
		if(CheckUserID($userid,'',false)!='ok'){
            ShowMsg("你输入的用户名 {$userid} 不合法！","-1");
            exit();
        }
        if($pwd==''){
            ShowMsg("请输入您的登录密码","-1",0,2000);
            exit();
        }
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
                        $data = array(
                        0 => "INSERT INTO `#@__member_person` SET `mid`='$mid', `onlynet`='1', `sex`='男', `uname`='$username', `qq`='', `msn`='', `tel`='', `mobile`='', `place`='', `oldplace`='0' , `birthday`='1980-01-01', `star`='1', `income`='0', `education`='0', `height`='160', `bodytype`='0', `blood`='0', `vocation`='0', `smoke`='0', `marital`='0', `house`='0', `drink`='0', `datingtype`='0', `language`='', `nature`='', `lovemsg`='', `address`='',`uptime`='0';",
                        1 => "INSERT INTO `#@__member_tj` SET `mid`='$mid',`article`='0',`album`='0',`archives`='0',`homecount`='0',`pagecount`='0',`feedback`='0',`friend`='0',`stow`='0';",
                        2 => "INSERT INTO `#@__member_space` SET `mid`='$mid',`pagesize`='10',`matt`='0',`spacename`='{$uname}的空间',`spacelogo`='',`spacestyle`='person', `sign`='',`spacenews`='';",
                        3 => "INSERT INTO `#@__member_flink` SET `mid`='$mid', `title`='家饰吧', `url`='http://www.jiashi8.com';"
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
            }else if($uid == -1) {
                //当UC不存在该用而CMS存在,就注册一个.
                if($rs) {
                    $row = $dsql->GetOne("SELECT `email` FROM #@__member WHERE userid='$userid'");                    
                    $uid = uc_user_register($userid, $pwd, $row['email']);
                    if($uid > 0) $ucsynlogin = uc_user_synlogin($uid);
                }else {
                    $rs = -1;
                }
            } else {
                $rs = -1;
            }
        }
        #/aip}} 
		if($rs == 0){
            ShowMsg("该用户不存在","-1");
            exit();			
		}
		elseif($rs==-1){
            ShowMsg("密码错误！", "-1", 0, 2000);
            exit();
        }
        elseif($rs==-2){
            ShowMsg("管理员帐号不允许从前台登录！", "-1", 0, 2000);
            exit();
        }
        else{
            //$cfg_ml->DelCache($cfg_ml->M_ID);
            if($use_qzone_avatar == 1){
				$dsql->ExecuteNoneQuery("UPDATE `#@__member` SET  `face` = '$facepic' WHERE mid = '{$cfg_ml->M_ID}'");
			}
			InsertOneOpenId($cfg_ml->M_ID, $InsertTokenArr);//绑定会员数据
			ShowMsg('绑定成功，感谢您使用QQ互联服务',$gourl);
            exit();
        }		 
	}
}
?>