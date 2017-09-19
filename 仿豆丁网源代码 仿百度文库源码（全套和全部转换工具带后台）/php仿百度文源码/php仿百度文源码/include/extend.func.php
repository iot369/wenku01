<?php
function litimgurls($imgid=0){
   global $lit_imglist;
   $dsql = new DedeSql(false);
   //获取附加表
   $row = $dsql->GetOne("SELECT c.addtable FROM #@__archives AS a LEFT JOIN #@__channeltype AS c ON a.channel=c.id where a.id='$imgid'");
   $addtable = trim($row['addtable']);
   //获取图片附加表imgurls字段内容进行处理
   $row = $dsql->GetOne("Select imgurls From `$addtable` where aid='$imgid'");
   //调用inc_channel_unit.php中ChannelUnit类
   $ChannelUnit = new ChannelUnit(2,$imgid);
   //调用ChannelUnit类中GetlitImgLinks方法处理缩略图
   $lit_imglist = $ChannelUnit->GetlitImgLinks($row['imgurls']);
   //返回结果
   return $lit_imglist;
}


//前台会员上传书籍函数 added by caozhiyang 2010-06-24
//$upname 是文件上传框的表单名，而不是表单的变量
//$handname 允许用户手工指定网址情况下的网址
function MemberUploadBook($upname,$handname,$userid=0,$utype='image',$exname='',$maxwidth=0,$maxheight=0,$water=false,$isadmin=false)
{
	global $cfg_imgtype,$cfg_mb_addontype,$cfg_mediatype,$cfg_user_dir,$cfg_basedir,$cfg_dir_purview;
	
	//当为游客投稿的情况下，这个 id 为 0
	if( empty($userid) ) $userid = 0;
	if(!is_dir($cfg_basedir.$cfg_user_dir."/$userid"))
	{
			MkdirAll($cfg_basedir.$cfg_user_dir."/$userid", $cfg_dir_purview);
			CloseFtp();
	}
	//有上传文件
	$allAllowType = str_replace('||', '|', $cfg_imgtype.'|'.$cfg_mediatype.'|'.$cfg_mb_addontype);
	if(!empty($GLOBALS[$upname]) && is_uploaded_file($GLOBALS[$upname]))
	{
		$nowtme = time();

		$GLOBALS[$upname.'_name'] = trim(ereg_replace("[ \r\n\t\*\%\\/\?><\|\":]{1,}",'',$GLOBALS[$upname.'_name']));
		
		//再次严格检测文件扩展名是否符合系统定义的类型
		$fs = explode('.', $GLOBALS[$upname.'_name']);
		$sname = $fs[count($fs)-1];
		$alltypes = explode('|', $allAllowType);
		if(!in_array(strtolower($sname), $alltypes))
		{
			ShowMsg('你所上传的文件类型不被允许！', '-1');
			exit();
		}
		//强制禁止的文件类型
		if(eregi("\.(asp|php|pl|cgi|shtm|js)", $sname))
		{
			ShowMsg('你上传的文件为系统禁止的类型！', '-1');
			exit();
		}
		if($exname=='')
		{
			$filename = $cfg_user_dir."/$userid/".dd2char($nowtme.'-'.mt_rand(1000,9999)).'.'.$sname;
		}
		else
		{
			$filename = $cfg_user_dir."/{$userid}/{$exname}.".$sname;
		}
		move_uploaded_file($GLOBALS[$upname], $cfg_basedir.$filename) or die("上传文件到 {$filename} 失败！");
		@unlink($GLOBALS[$upname]);
		
		if(@filesize($cfg_basedir.$filename) > $GLOBALS['cfg_mb_upload_size'] * 1024)
		{
			@unlink($cfg_basedir.$filename);
			ShowMsg('你上传的文件超出系统大小限制！', '-1');
			exit();
		}
		
		return $filename;
	}
	//没有上传文件
	else
	{
		//强制禁止的文件类型
		if($handname=='')
		{
			return $handname;
		}
		else if(eregi("\.(asp|php|pl|cgi|shtm|js)", $handname))
		{
			exit('Not allow filename for not safe!');
		}
		else if( !eregi("\.(".$allAllowType.")$", $handname) )
		{
			exit('Not allow filename for filetype!');
		}
		else if( !eregi('^http:', $handname) && !eregi('^'.$cfg_user_dir.'/'.$userid, $handname) && !$isadmin )
		{
			exit('Not allow filename for not userdir!');
		}
		return $handname;
	}
}

//后台管理员上传书籍函数 added by caozhiyang 2010-08-11
//$upname 是文件上传框的表单名，而不是表单的变量
//$handname 允许用户手工指定网址情况下的网址
function adminUploadBook($upname,$handname,$userid=0,$utype='image',$exname='',$maxwidth=0,$maxheight=0,$water=false,$isadmin=false)
{
	global $cfg_imgtype,$cfg_mb_addontype,$cfg_mediatype,$cfg_user_dir,$cfg_basedir,$cfg_dir_purview;
	
	//当为游客投稿的情况下，这个 id 为 0
	if( empty($userid) ) $userid = 0;
	if(!is_dir($cfg_basedir.$cfg_user_dir."/$userid"))
	{
			MkdirAll($cfg_basedir.$cfg_user_dir."/$userid", $cfg_dir_purview);
			CloseFtp();
	}
	//有上传文件
	$allAllowType = str_replace('||', '|', $cfg_imgtype.'|'.$cfg_mediatype.'|'.$cfg_mb_addontype);
	if(!empty($GLOBALS[$upname]) && is_uploaded_file($GLOBALS[$upname]))
	{
		$nowtme = time();

		$GLOBALS[$upname.'_name'] = trim(ereg_replace("[ \r\n\t\*\%\\/\?><\|\":]{1,}",'',$GLOBALS[$upname.'_name']));
		
		//再次严格检测文件扩展名是否符合系统定义的类型
		$fs = explode('.', $GLOBALS[$upname.'_name']);
		$sname = $fs[count($fs)-1];
		$alltypes = explode('|', $allAllowType);
		if(!in_array(strtolower($sname), $alltypes))
		{
			ShowMsg('你所上传的文件类型不被允许！', '-1');
			exit();
		}
		//强制禁止的文件类型
		if(eregi("\.(asp|php|pl|cgi|shtm|js)", $sname))
		{
			ShowMsg('你上传的文件为系统禁止的类型！', '-1');
			exit();
		}
		if($exname=='')
		{
			$filename = $cfg_user_dir."/$userid/".dd2char($nowtme.'-'.mt_rand(1000,9999)).'.'.$sname;
		}
		else
		{
			$filename = $cfg_user_dir."/{$userid}/{$exname}.".$sname;
		}
		move_uploaded_file($GLOBALS[$upname], $cfg_basedir.$filename) or die("上传文件到 {$filename} 失败！");
		@unlink($GLOBALS[$upname]);
		
		if(@filesize($cfg_basedir.$filename) > $GLOBALS['cfg_mb_upload_size'] * 1024)
		{
			@unlink($cfg_basedir.$filename);
			ShowMsg('你上传的文件超出系统大小限制！', '-1');
			exit();
		}
		
		return $filename;
	}
	//没有上传文件
	else
	{
		//强制禁止的文件类型
		if($handname=='')
		{
			return $handname;
		}
		else if(eregi("\.(asp|php|pl|cgi|shtm|js)", $handname))
		{
			exit('Not allow filename for not safe!');
		}
		else if( !eregi("\.(".$allAllowType.")$", $handname) )
		{
			exit('Not allow filename for filetype!');
		}
		else if( !eregi('^http:', $handname) && !eregi('^'.$cfg_user_dir.'/'.$userid, $handname) && !$isadmin )
		{
			exit('Not allow filename for not userdir!');
		}
		return $handname;
	}
}

/**
* 读取文件前几个字节 判断文件类型
*
* @return String
*/
function checkTitle($filename) {
	$file     = fopen($filename, 'rb');
	$bin      = fread($file, 2); //只读2字节
	fclose($file);
	$strInfo  = @unpack('c2chars', $bin);
	$typeCode = intval($strInfo['chars1'].$strInfo['chars2']);
	$fileType = '';
	switch ($typeCode)
	{
		case 7790:
			$fileType = 'exe';
			break;
		case 7784:
			$fileType = 'midi';
			break;
		case 8297:
			$fileType = 'rar';
			break;
		case 255216:
			$fileType = 'jpg';
			break;
		case 7173:
			$fileType = 'gif';
			break;
		case 6677:
			$fileType = 'bmp';
			break;
		case 13780:
			$fileType = 'png';
			break;
		default:
			$fileType = 'unknown'.$typeCode;
	}
	//Fix
	if ($strInfo['chars1']=='-1' && $strInfo['chars2']=='-40' ) {
		return 'jpg';
	}
	if ($strInfo['chars1']=='-119' && $strInfo['chars2']=='80' ) {
		return 'png';
	}
	return $fileType;
}

//更新今日的统计数据
function doSysStatistics($colName){
	global $dsql;
	//更新相应的数据
	$dsql->ExecuteNoneQuery("insert into `t_sys_statistics` (`data_date`, `$colName`) values (curdate(), 1) ON DUPLICATE KEY UPDATE `$colName`=`$colName`+1");
}
?>