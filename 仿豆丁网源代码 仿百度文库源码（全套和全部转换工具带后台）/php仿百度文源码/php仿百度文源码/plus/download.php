<?php
require_once(dirname(__FILE__)."/../include/common.inc.php");
require_once(DEDEINC."/channelunit.class.php");
if(!isset($open)) $open = 0;
//读取链接列表
if($open==0)
{
	$aid = (isset($aid) && is_numeric($aid)) ? $aid : 0;
	if($aid==0) exit(' Request Error! ');

	$arcRow = GetOneArchive($aid);
	if($arcRow['aid']=='')
	{
		ShowMsg('无法获取未知文档的信息!','-1');
		exit();
	}
	extract($arcRow, EXTR_SKIP);

	$cu = new ChannelUnit($arcRow['channel'],$aid);
	if(!is_array($cu->ChannelFields))
	{
		ShowMsg('获取文档信息失败！','-1');
		exit();
	}

	$vname = '';
	foreach($cu->ChannelFields as $k=>$v)
	{
		if($v['type']=='softlinks'){ $vname=$k; break; }
	}
	$row = $dsql->GetOne("Select $vname From `".$cu->ChannelInfos['addtable']."` where aid='$aid'");

	include_once(DEDEINC.'/taglib/channel/softlinks.lib.php');
	$ctag = '';
	$downlinks = ch_softlinks($row[$vname], $ctag, $cu, '', true);

	require_once(DEDETEMPLATE.'/plus/download_links_templet.htm');
	exit();
}
/*------------------------
//提供文档给用户下载(旧模式)
function getSoft_old()
------------------------*/
else if($open==1)
{
	//更新下载次数
	$id = isset($id) && is_numeric($id) ? $id : 0;
	$link = base64_decode(urldecode($link));
	$hash = md5($link);
	$rs = $dsql->ExecuteNoneQuery2("Update `#@__downloads` set downloads = downloads+1 where hash='$hash' ");
	if($rs <= 0)
	{
		$query = " Insert into `#@__downloads`(`hash`,`id`,`downloads`) values('$hash','$id',1); ";
		$dsql->ExecNoneQuery($query);
	}
	header("location:$link");
	exit();
}
/*------------------------
//提供文档给用户下载(新模式)
function getSoft_new()
------------------------*/
else if($open==2)
{
	//文档ID
	$id = intval($id);
	//获得附加表信息
	$row = $dsql->GetOne("Select ch.addtable,arc.mid From `#@__arctiny` arc left join `#@__channeltype` ch on ch.id=arc.channel where arc.id='$id' ");
	if(empty($row['addtable']))
	{
		ShowMsg('找不到所需要的文档资源！', 'javascript:;');
		exit();
	}
	//文档发布者ID
	$mid = $row['mid'];
	
	//读取连接列表、下载权限信息
	$row = $dsql->GetOne("Select filetype, softlinks,daccess,needmoney From `{$row['addtable']}` where aid='$id' ");
	if(empty($row['softlinks']))
	{
		ShowMsg('找不到所需要的文档资源！', 'javascript:;');
		exit();
	}
	$softconfig = $dsql->GetOne("Select * From `#@__softconfig` ");
	$needRank = $softconfig['dfrank'];
	$needMoney = $softconfig['dfywboy'];
	if($softconfig['argrange']==0)
	{
		$needRank = $row['daccess'];
	  $needMoney = $row['needmoney'];
	}
	
	//分析连接列表
	require_once(DEDEINC.'/dedetag.class.php');
	$softUrl = '';
	$islocal = 0;
	$dtp = new DedeTagParse();
	$dtp->LoadSource($row['softlinks']);
	if( !is_array($dtp->CTags) )
	{
		$dtp->Clear();
		ShowMsg('找不到所需要的文档资源！', 'javascript:;');
		exit();
	}
	foreach($dtp->CTags as $ctag)
	{
		if($ctag->GetName()=='link')
		{
			$link = trim($ctag->GetInnerText());
			$islocal = $ctag->GetAtt('islocal');
			//分析本地链接
			if(!isset($firstLink) && $islocal==1) $firstLink = $link;
			if($islocal==1 && $softconfig['islocal'] != 1) continue;
			//支持http,迅雷下载,ftp,flashget
			if(!eregi('^http://|^thunder://|^ftp://|^flashget://', $link))
			{
				 $link = $cfg_mainsite.$link;
			}
			$dbhash = substr(md5($link), 0, 24);
			if($uhash==$dbhash) $softUrl = $link;
		}
	}
	$dtp->Clear();
	if($softUrl=='' && $softconfig['ismoresite']==1 
	&& $softconfig['moresitedo']==1 && trim($softconfig['sites'])!='' && isset($firstLink))
	{
		$firstLink = eregi_replace("http://([^/]*)/", '/', $firstLink);
		$softconfig['sites'] = ereg_replace("[\r\n]{1,}", "\n", $softconfig['sites']);
		$sites = explode("\n", trim($softconfig['sites']));
		foreach($sites as $site)
		{
			if(trim($site)=='') continue;
			list($link, $serverName) = explode('|', $site);
			$link = trim( ereg_replace("/$", "", $link) ).$firstLink;
			$dbhash = substr(md5($link), 0, 24);
			if($uhash == $dbhash) $softUrl = $link;
		}
	}
	if( $softUrl == '' )
	{
		ShowMsg('找不到所需要的文档资源！', 'javascript:;');
		exit();
	}
	//-------------------------
	// 读取文档信息，判断权限
	//-------------------------
	$arcRow = GetOneArchive($id);
	if($arcRow['aid']=='')
	{
		ShowMsg('无法获取未知文档的信息!','-1');
		exit();
	}
	extract($arcRow, EXTR_SKIP);

	//处理需要下载权限的文档
	if($needRank>0 || $needMoney>0)
	{
		require_once(DEDEINC.'/memberlogin.class.php');
		$cfg_ml = new MemberLogin();
		$arclink = $arcurl;
		$arctitle = $title;
		$arcLinktitle = "<a href=\"{$arcurl}\"><u>".$arctitle."</u></a>";
		$filetype = $row["filetype"];//文档类型 added by caozhiyang
		$pubdate = GetDateTimeMk($pubdate);
	
		//会员级别不足
		if(($needRank>1 && $cfg_ml->M_Rank < $needRank && $mid != $cfg_ml->M_ID))
		{
			$dsql->Execute('me' , "Select * From `#@__arcrank` ");
			while($row = $dsql->GetObject('me'))
			{
				$memberTypes[$row->rank] = $row->membername;
			}
			$memberTypes[0] = "游客";
			$msgtitle = "你没有权限下载文档：{$arctitle}！";
			$moremsg = "这个文档需要 <font color='red'>".$memberTypes[$needRank]."</font> 才能下载，你目前是：<font color='red'>".$memberTypes[$cfg_ml->M_Rank]."</font> ！<a href='".$cfg_memberurl."/login.php'>此处登录</a>";
			include_once(DEDETEMPLATE.'/plus/view_msg.htm');
			exit();
		}

		//以下为正常情况，自动扣点数
		//如果文章需要金币，检查用户是否浏览过本文档
		if($needMoney > 0  && $mid != $cfg_ml->M_ID)
		{
			$sql = "Select aid,money From `#@__member_operation` where buyid='ARCHIVE".$id."' And mid='".$cfg_ml->M_ID."'";
			$row = $dsql->GetOne($sql);
			//未购买过此文章
			if( !is_array($row) )
			{
		 	 		//没有足够的金币
					if( $needMoney > $cfg_ml->M_Money || $cfg_ml->M_Money=='')
	 				{
							$msgtitle = "你没有权限下载文档：{$arctitle}！";
							$moremsg = "这个文档需要 <font color='red'>".$needMoney." 金币</font> 才能下载，你目前拥有金币：<font color='red'>".$cfg_ml->M_Money." 个</font> ！<a href='".$cfg_memberurl."/buy.php'>此处兑换金币</a>";
							include_once(DEDETEMPLATE.'/plus/view_msg.htm');
							exit(0);
					}
					//有足够金币，记录用户信息
		 	 		$inquery = "INSERT INTO `#@__member_operation`(mid,oldinfo,money,mtime,buyid,product,pname,sta)
		              VALUES ('".$cfg_ml->M_ID."','$arctitle','$needMoney','".time()."', 'ARCHIVE".$id."', 'archive','下载文档', 2); ";
		 	 		//记录定单
		 	 		if( !$dsql->ExecuteNoneQuery($inquery) )
		 	 		{
		 	 			ShowMsg('记录定单失败, 请返回', '-1');
						exit(0);
		 	 		}
		    	//扣除金币
		    	$dsql->ExecuteNoneQuery("Update `#@__member` set money = money - $needMoney where mid='".$cfg_ml->M_ID."'");
				
				//文档所有者增加相应的金币
		    	$dsql->ExecuteNoneQuery("Update `#@__member` set money = money + $needMoney where mid='$mid'");

				//更新今日有收入者 added by caozhiyang 2010-08-12
				doSysStatistics("new_income");
			}
		}
	}
	//更新下载次数
	$hash = md5($softUrl);
	$rs = $dsql->ExecuteNoneQuery2("Update `#@__downloads` set downloads = downloads+1 where hash='$hash' ");
	if($rs <= 0)
	{
		$query = " Insert into `#@__downloads`(`hash`, `id`, `downloads`) values('$hash', '$id', 1); ";
		$dsql->ExecNoneQuery($query);
	}
	
	//添加下载记录 added by caozhiyang
	$dsql->ExecuteNoneQuery(" INSERT INTO `#@__member_download` (mid, aid, title) VALUES ('".$cfg_ml->M_ID."','$id','$arctitle')");

	//更新今日文档下载量 added by caozhiyang 2010-08-12
	doSysStatistics("doc_down");
	
	$arctitle = iconv("UTF-8", "GB2312", $arctitle);

	$file = fopen($cfg_basedir.$softUrl, "r"); // 打开文件
	// 输入文件标签
	Header("Content-type: application/octet-stream");
	Header("Accept-Ranges: bytes");
	Header("Accept-Length: ".filesize($cfg_basedir.$softUrl));
	Header("Content-Disposition: attachment; filename=".$arctitle);//用文档本来的名字
	// 输出文件内容
	fpassthru($file);
	//$content = fread($file, filesize($cfg_basedir.$softUrl));
	//echo $content;
	fclose($file);
	exit();

	//header("location:{$softUrl}");
	//exit();
}//opentype=2


function file_resume($file){

   //First, see if the file exists
   if (!is_file($file)) { die("<b>404 File not found!</b>"); }
  
   //Gather relevent info about file
   $len = filesize($file);
   $filename = basename($file);
   $file_extension = strtolower(substr(strrchr($filename,"."),1));
  
   //This will set the Content-Type to the appropriate setting for the file
   switch( $file_extension ) {
       case "exe": $ctype="application/octet-stream"; break;
       case "zip": $ctype="application/x-zip-compressed"; break;
       case "rar": $ctype="application/x-rar"; break;
       default: $ctype="application/force-download";
   }
  
   //Begin writing headers
   header("Cache-Control:");
   header("Cache-Control: public");
  
   //Use the switch-generated Content-Type
   header("Content-Type: $ctype");
   if (strstr($_SERVER['HTTP_USER_AGENT'], "MSIE")) {
       # workaround for IE filename bug with multiple periods / multiple dots in filename
       # that adds square brackets to filename - eg. setup.abc.exe becomes setup[1].abc.exe
       $iefilename = preg_replace('/\./', '%2e', $filename, substr_count($filename, '.') - 1);
       header("Content-Disposition: attachment; filename=\"$iefilename\"");
       //header("Content-Range: $from-$to fsize");  加上压缩包头信息不正确
       //header("Content-Length: $content_size");   加上压缩包头信息不正确
   } else {
       header("Content-Disposition: attachment; filename=\"$filename\"");
       //header("Content-Range: $from-$to fsize");  加上压缩包头信息不正确
       //header("Content-Length: $content_size");   加上压缩包头信息不正确
   }
   header("Accept-Ranges: bytes");
   //header('Expires: '.gmdate("D, d M Y H:i:s", mktime(date("H")+2, date("i"), date("s"), date("m"), date("d"), date("Y"))).' GMT');
  
   $size=filesize($file);
   //open the file
   $fp=fopen("$file","rb");
   //ek to start of missing part
   fseek($fp,$range);
   //start buffered download
   while(!feof($fp)){
       //reset time limit for big files
       set_time_limit(0);
       print(fread($fp,1024*8));
       //flush();   这个是多余的函数,加上会使压缩包下载不完整
       //ob_flush();  这个也是多余的函数,加上会使压缩包下载不完整
   }
   fclose($fp);
   exit;
}
?>