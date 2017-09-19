<?php
require_once(dirname(__FILE__)."/config.php");
//考虑安全原因不管是否开启游客投稿功能，都不允许用户投稿
CheckRank(0,0);
if($cfg_mb_lit=='Y')
{
	ShowMsg("由于系统开启了精简版会员空间，你访问的功能不可用！","-1");
	exit();
}
require_once(DEDEINC."/dedetag.class.php");
require_once(DEDEINC."/userlogin.class.php");
require_once(DEDEINC."/customfields.func.php");
require_once(DEDEMEMBER."/inc/inc_catalog_options.php");
require_once(DEDEMEMBER."/inc/inc_archives_functions.php");
$channelid = isset($channelid) && is_numeric($channelid) ? $channelid : 17;//书籍频道模型ID
$typeid = isset($typeid) && is_numeric($typeid) ? $typeid : 0;
$menutype = 'content';

/*-------------
function _ShowForm(){  }
--------------*/
if(empty($dopost))
{
	$cInfos = $dsql->GetOne("Select * From `#@__channeltype`  where id='$channelid'; ");
	if(!is_array($cInfos))
	{
		ShowMsg('模型不正确', '-1');
		exit();
	}

	//如果限制了会员级别或类型，则允许游客投稿选项无效
	if($cInfos['sendrank']>0 || $cInfos['usertype']!='')
	{
		CheckRank(0,0);
	}

	//检查会员等级和类型限制
	if($cInfos['sendrank'] > $cfg_ml->M_Rank)
	{
		$row = $dsql->GetOne("Select membername From `#@__arcrank` where rank='".$cInfos['sendrank']."' ");
		ShowMsg("对不起，需要[".$row['membername']."]才能在这个频道发布文档！","-1","0",5000);
		exit();
	}
	if($cInfos['usertype']!='' && $cInfos['usertype'] != $cfg_ml->M_MbType)
	{
		ShowMsg("对不起，需要[".$cInfos['usertype']."帐号]才能在这个频道发布文档！","-1","0",5000);
		exit();
	}
	include(DEDEMEMBER."/templets/book_add.htm");
	exit();
}

/*------------------------------
function _SaveArticle(){  }
------------------------------*/
else if($dopost=='save')
{
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
	
	$description = '';
	include(DEDEMEMBER.'/inc/archives_check.php');
	
	//生成文档ID
	$arcID = GetIndexKey($arcrank,$typeid,$sortrank,$channelid,$senddate,$mid);
	if(empty($arcID))
	{
		ShowMsg("无法获得主键，因此无法进行后续操作！","-1");
		exit();
	}
	
	//分析处理附加表数据
	$inadd_f = '';
	$inadd_v = '';
	if(!empty($dede_addonfields))
	{
		$addonfields = explode(';',$dede_addonfields);
		$inadd_f = '';
		$inadd_v = '';
		if(is_array($addonfields))
		{
			foreach($addonfields as $v)
			{
				if($v=='')
				{
					continue;
				}else if($v == 'templet')
				{
					ShowMsg("你保存的字段有误,请检查！","-1");
					exit();	
				}
				$vs = explode(',',$v);
				if(!isset(${$vs[0]}))
				{
					${$vs[0]} = '';
				}
				else if($vs[1]=='htmltext'||$vs[1]=='textdata')

				//HTML文本特殊处理
				{
					${$vs[0]} = AnalyseHtmlBody(${$vs[0]},$description,$litpic,$keywords,$vs[1]);
				}
				else
				{
					if(!isset(${$vs[0]}))
					{
						${$vs[0]} = '';
					}
					${$vs[0]} = GetFieldValueA(${$vs[0]},$vs[1],$arcID);
				}
				$inadd_f .= ','.$vs[0];
				$inadd_v .= " ,'".${$vs[0]}."' ";
			}
		}
	}

	//处理图片文档的自定义属性
	if($litpic!='')
	{
		$flag = 'p';
	}
	$body = HtmlReplace($body,-1);
	
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
	$row = $dsql->GetOne("Select aid,title,url From `#@__uploads` where aid = ".$uploadId."; ");
	$softurl1 = $row["url"];//取到上传文件的地址
	
	//文档链接列表（暂只考虑本地链接的情况）
	$softurl1 = stripslashes($softurl1);
	$urls = '';
	$softsize = '';
	if($softurl1!='')
	{
		$urls .= "{dede:link islocal='1' text='本地下载'} $softurl1 {/dede:link}\r\n";
		$softsize = @filesize($cfg_basedir.$softurl1);
		if(empty($softsize)) $softsize = '未知';
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
    os,softrank,officialUrl,officialDemo,softsize,softlinks,introduce,userip,templet,redirecturl,daccess,needmoney{$inadd_f})
    VALUES ('$arcID','$typeid','$filetype','$language','$softtype','$accredit',
    '$os','$softrank','$officialUrl','$officialDemo','$softsize','$urls','$body','$userip','','','10','$needmoney'{$inadd_v});";
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

	//增加积分
	$dsql->ExecuteNoneQuery("Update `#@__member` set scores=scores+{$cfg_sendarc_scores} where mid='".$cfg_ml->M_ID."' ; ");

	//更新今日文档上传 added by caozhiyang 2010-08-12
	doSysStatistics("new_doc");

	//更新统计
	countArchives($channelid);
	
	//生成HTML
	$tags = $keywords;//关键字作为标签
	InsertTags($tags,$arcID);
	
	$artUrl = MakeArt($arcID,true);
	
	if($artUrl=='')
	{
		$artUrl = $cfg_phpurl."/view.php?aid=$arcID";
	}

	
	//会员动态记录
	$cfg_ml->RecordFeeds('addsoft',$title,$description,$arcID);
	
	//这里面会将附件信息与文档关联起来 by caozhiyang
	ClearMyAddon($arcID, $title);
	
	//返回成功信息
	/**
	$msg = "
		请选择你的后续操作：
		<a href='book_add.php?cid=$typeid'><u>继续发布文档</u></a>
		&nbsp;&nbsp;
		<a href='$artUrl' target='_blank'><u>查看文档</u></a>
		&nbsp;&nbsp;
		<a href='book_edit.php?channelid=$channelid&aid=$arcID'><u>更改文档</u></a>
		&nbsp;&nbsp;
		<a href='content_list.php?channelid={$channelid}'><u>已发布文档管理</u></a>
		";
	$wintitle = "成功发布文档！";
	$wecome_info = "文档管理::发布文档";
	$win = new OxWindow();
	$win->AddTitle("成功发布文档：");
	$win->AddMsgItem($msg);
	$winform = $win->GetWindow("hand","&nbsp;",false);
	$win->Display();
	*/
	ShowMsg("文档发布成功，正在等待管理员的审核！返回我发布的文档列表...","content_list.php?channelid={$channelid}", "0", 3000);
}

?>