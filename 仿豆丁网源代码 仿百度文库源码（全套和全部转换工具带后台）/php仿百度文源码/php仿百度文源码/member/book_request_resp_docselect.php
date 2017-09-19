<?php
require_once(dirname(__FILE__)."/config.php");
CheckRank(0,0);
require_once(DEDEINC."/dedetag.class.php");
require_once(DEDEINC."/userlogin.class.php");
require_once(DEDEINC."/customfields.func.php");
require_once(DEDEMEMBER."/inc/inc_catalog_options.php");
require_once(DEDEMEMBER."/inc/inc_archives_functions.php");

$channelid = isset($channelid) && is_numeric($channelid) ? $channelid : 17;//书籍频道模型ID
$typeid = isset($typeid) && is_numeric($typeid) ? $typeid : 0;


//查询该投稿所对应的投稿信息和悬赏任务信息
$rowResp = $dsql->GetOne("Select * from `#@__member_response` where id='$respid' ");
$reqid = $rowResp["reqid"];//投稿所对应的需求ID

$rowReq = $dsql->GetOne("Select * from `#@__member_request` where id='$reqid' ");

//选标，更新投稿状态为已中标
$dsql->ExecuteNoneQuery("update `#@__member_response` set status = 1 where id='$respid'");
$dsql->ExecuteNoneQuery("update `#@__member_request` set status = 1, respid='$respid', selecttime=now() where id='$reqid'");

//以当前会员为身份发布该文档-------------------begin-----------------------------------

//处理各字段的默认值 beign by caozhiyang-------------------------------------
$tags = '';
$writer = $cfg_ml->M_UserName;//当前会员姓名
$language = '简体中文';
$softtype = '国产文档';
$accredit = '共享文档';
$os = 'Win2003,WinXP,Win2000,Win9X';
$softrank = '3';
$officialDemo = '';
$officialUrl = '';
$source = '';
//处理各字段的默认值 end-------------------------------------
$description = $title;

//取一个默认分类
	$cts = $dsql->GetOne("Select id From `#@__arctype` where channeltype='$channelid' and ispart = 0 ");
	$typeid = trim($cts['id']);
	
	//检测
	include('inc/archives_check_batchup.php');
	
   //生成文档ID
	$arcID = GetIndexKey($arcrank,$typeid,$sortrank,$channelid,$senddate,$mid);
	if(empty($arcID))
	{
		ShowMsg("无法获得主键，因此无法进行后续操作！","-1");
		exit();
	}

	$title = $rowResp["doctitle"];
	$softurl1 = $rowResp["fileurl"];
	$litpic = $rowResp["litpic"];
	$onlineviewurl = $rowResp["swfurl"];
	$pagenumber = $rowResp["pagenumber"];

	//自动获取关键字 added by caozhiyang
	if($keywords=='')
	{
		$subject = $title;
		$message = $body;
		include_once(DEDEINC.'/splitword.class.php');
		$keywords = '';
		$sp = new SplitWord($cfg_soft_lang, $cfg_soft_lang);
		$sp->SetSource($subject, $cfg_soft_lang, $cfg_soft_lang);
		$sp->StartAnalysis();
		$titleindexs = preg_replace("/#p#|#e#/",'',$sp->GetFinallyIndex());
		$sp->SetSource(Html2Text($message), $cfg_soft_lang, $cfg_soft_lang);
		$allindexs = preg_replace("/#p#|#e#/",'',$sp->GetFinallyIndex());
		if(is_array($allindexs) && is_array($titleindexs))
		{
			foreach($titleindexs as $k => $v)
			{
				if(strlen($keywords.$k)>=60)
				{
					break;
				}
				else
				{
					$keywords .= $k.',';
				}
			}
			foreach($allindexs as $k => $v)
			{
				if(strlen($keywords.$k)>=60)
				{
					break;
				}
				else if(!in_array($k,$titleindexs))
				{
					$keywords .= $k.',';
				}
			}
		}
		$sp = null;
		$keywords = addslashes($keywords);
	}
	
	//需要审核
	$arcrank = -1;

	//保存到主表
	$inQuery = "INSERT INTO `#@__archives`(id,typeid,sortrank,flag,ismake,channel,arcrank,click,money,title,shorttitle,
color,writer,source,litpic,pubdate,senddate,mid,description,keywords)
VALUES ('$arcID','$typeid','$sortrank','$flag','$ismake','$channelid','$arcrank','0','$money','$title','$shorttitle',
'$color','$writer','$source','$litpic','$pubdate','$senddate','$mid','$description','$keywords'); ";
	if(!$dsql->ExecuteNoneQuery($inQuery))
	{
		$gerr = $dsql->GetError();
		$dsql->ExecuteNoneQuery("Delete From `#@__arctiny` where id='$arcID' ");
		ShowMsg("把数据保存到数据库主表 `#@__archives` 时出错，请联系管理员。","javascript:;");
		exit();
	}

	//added by 曹志阳，这里要根据附件ID取附件路径和附件大小
	
	//本地地址不由用户前台传过来，后台根据附件ID从t_uploads表中取出来
	//$row = $dsql->GetOne("Select aid,title,url From `#@__uploads` where aid = ".$aid."; ");
	//$softurl1 = $row["url"];//取到上传文件的地址
	
	//文档链接列表（暂只考虑本地链接的情况）
	$softurl1 = stripslashes($softurl1);
	$urls = '';
	$softsize = '';
	if($softurl1!='')
	{
		$urls .= "{dede:link islocal='1' text='本地下载'} $softurl1 {/dede:link}\r\n";
		//取文件扩展名
		$file_part  = pathinfo($softurl1);
		$filetype = $file_part["extension"];
		$softsize = @filesize($cfg_basedir.$softurl1);
		if(empty($softsize)) $softsize = iconv("GB2312","UTF-8", '未知');
		else
		{
			$softsize = trim(sprintf("%0.2f", $softsize / 1024 / 1024));
			$softsize = $softsize." MB";
		}
	}
	/**其它链接暂不处理，全是本地链接
	for($i=2;$i<=12;$i++)
	{
		if(!empty(${'softurl'.$i}))
		{
			$servermsg = str_replace("'","",stripslashes(${'servermsg'.$i}));
			$softurl = stripslashes(${'softurl'.$i});
			if($servermsg=='')
			{
				$servermsg = '下载地址'.$i;
			}
			if($softurl!='' && $softurl!='http://')
			{
				$urls .= "{dede:link text='$servermsg'} $softurl {/dede:link}\r\n";
			}
		}
	}
	*/
	//软件大小
	$urls = addslashes($urls);

	//保存到附加表
	$needmoney = @intval($needmoney);
	if($needmoney > 100) $needmoney = 100;
	$cts = $dsql->GetOne("Select addtable From `#@__channeltype` where id='$channelid' ");
	$addtable = trim($cts['addtable']);
	if(empty($addtable))
	{
		$dsql->ExecuteNoneQuery("Delete From `#@__archives` where id='$arcID'");
		$dsql->ExecuteNoneQuery("Delete From `#@__arctiny` where id='$arcID'");
		ShowMsg("没找到当前模型[{$channelid}]的主表信息，无法完成操作！。","javascript:;");
		exit();
	}
	//默认需要注册会员才能下载 daccess保存相应的会员等级ID 10-注册会员；50-中级会员...
	$inQuery = "INSERT INTO `$addtable`(aid,typeid,filetype,language,softtype,accredit,
    os,softrank,officialUrl,officialDemo,softsize,softlinks,introduce,userip,templet,redirecturl,daccess,needmoney, onlineviewurl, pagenumber)
    VALUES ('$arcID','$typeid','$filetype','$language','$softtype','$accredit',
    '$os','$softrank','$officialUrl','$officialDemo','$softsize','$urls','$description','$userip','','','10','$needmoney', '$onlineviewurl', '$pagenumber');";
	if(!$dsql->ExecuteNoneQuery($inQuery))
	{
		$gerr = $dsql->GetError();
		$dsql->ExecuteNoneQuery("Delete From `#@__archives` where id='$arcID'");
		$dsql->ExecuteNoneQuery("Delete From `#@__arctiny` where id='$arcID'");
		echo $inQuery;
		exit();
		ShowMsg("把数据保存到数据库附加表 `{$addtable}` 时出错，请把相关信息提交给官方。".str_replace('"','',$gerr),"javascript:;");
		exit();
	}

	//更新今日文档上传 added by caozhiyang 2010-08-12
	doSysStatistics("new_doc");

	//更新统计
	countArchives($channelid);
	
	//生成HTML
	$tags = $keywords;//关键字作为标签
	InsertTags($tags,$arcID);
	
	$artUrl = MakeArt($arcID,true);
	
	//文档和附件关联
	//$dsql->ExecuteNoneQuery("Update `#@__uploads` set arcid='$arcID' where aid='$aid' ");
	
	//会员动态记录
	$cfg_ml->RecordFeeds('addsoft',$title,$description,$arcID);

//以当前会员为身份发布该文档-------------------end-----------------------------------

//为投稿用户增加积分
$addScores = $rowReq["score"];//该任务对应的悬赏积分
$respmid = $rowResp["mid"];//投稿者
$reqmid = $rowReq["mid"];//发布者
$dsql->ExecuteNoneQuery("update `#@__member` set scores=scores+{$addScores} where mid='$respmid'");
//同时减去任务发布者的积分
//$dsql->ExecuteNoneQuery("update `#@__member` set scores=scores-{$addScores} where mid='$reqmid'");
$dsql->ExecuteNoneQuery("update `#@__member` set scores=if((scores-{$addScores}})<scores, (scores-{$addScores}), 0) where mid='$reqmid'");

//echo "选标成功！系统从您的账户中扣除【".$addScores."】积分给投稿会员【".$respmid."】，同时该文档已经以您的身份发布，请进入会员中心设置此文档的相关信息！";

//查询投稿会员的信息
$rowMemberResp = $dsql->GetOne("Select * from `#@__member` where mid='$respmid' ");

$json .= "{result: '0',";
$json .= "title: '".$title."',";
$json .= "arcid:'".$arcID."',";
$json .= "score:'".$addScores."',";
$json .= "respmemname:'".$rowMemberResp["uname"]."'";
$json .= "}";
echo $json;

exit();
?>