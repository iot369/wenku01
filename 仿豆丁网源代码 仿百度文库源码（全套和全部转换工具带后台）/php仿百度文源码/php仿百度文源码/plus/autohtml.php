<?php
/**
 * @自动更新HTML静态文件模块
 * 
 * @目前模块可以实现在网页上放上相应代码当有人访问即可自动更新HTML文件
 * 
 * @适合用于使用虚拟主机的个人站点
 * 
 * @作者	溪梦缘曦
 *
 * @版本	AutoHtml V5.6.3 Final FOR DedeCMS V56
 *
 * @起始	2008-02-01发布第一个版本
 *
 * @最后修改	2010-04-23修改为当前版本
 *
 * @特别鸣谢	织梦技术论坛的各位朋友
 *
 */
require_once(dirname(__FILE__).'/../include/common.inc.php');
require_once(DEDEINC."/autohtml.func.php");
if(empty($type)) $type = 'arc';

if(file_exists(CACHEFILE))
{
	require_once(CACHEFILE);
}
else
{
	$msg = '无法找到缓存文件，请后台重新设置，请先确定data目录有读写权限。';
	AutoHtml_Msg('error',$msg);
	exit();
}

$nowtime = Time();

if($type == 'arc')
{
	$aid = (isset($aid) && is_numeric($aid)) ? $aid : 0;
	$lastupdate = (isset($lastupdate) && is_numeric($lastupdate)) ? $lastupdate : 0;

	if($aid=='0')
	{
		$msg = '文章ID错误。';
		AutoHtml_Msg('arc',$msg);
		exit();
	}

	$isuptime = $nowtime - $lastupdate - $auto_arc_uptime;
	$sd = ( $aid > $auto_arc_startid || $auto_arc_startid == 0 ) ? 'Y' : 'N';
	$ed = ( $aid < $auto_arc_endid || $auto_arc_endid == 0 ) ? 'Y' : 'N';
	$st = ( $auto_arc_starttime < $nowtime || $auto_arc_starttime == 0 ) ? 'Y' : 'N';
	$et = ( $auto_arc_endtime > $nowtime || $auto_arc_endtime == 0 ) ? 'Y' : 'N';
	$gt = ( $sd == 'Y' && $ed == 'Y' && $st == 'Y' && $et == 'Y' ) ? 'Y' : 'N'; 

	if( $gt == 'Y' && $auto_arc_open == 'Y' && $isuptime > 0 )
	{
		require_once(DEDEINC.'/arc.archives.class.php');

		$auto_arc = new Archives($aid);
		if($auto_arc -> Fields['ismake']==-1||$auto_arc -> Fields['arcrank']!=0||
		$auto_arc -> Fields['typeid']==0||$auto_arc -> Fields['money']>0)
		{
			$msg = $title.'动态文章~未审核~被移入回收站~收费文章，无法生成，终止处理。';
			AutoHtml_Msg('arc',$msg);
		}
		else
		{
			$auto_arc -> Fields['lastupdate'] = $nowtime;
  			$reurl = $auto_arc -> MakeHtml();
			AutoHtml_UpArc($aid);
			$title = $auto_arc ->Fields['title'];

			$msg = '已经重新生成ID为：'.$aid.'，标题为：'.$title.'的文档的静态文件。';
			AutoHtml_Msg('arc',$msg,'reload');
		}
		$auto_arc->Close();
	}
	else if( $auto_arc_open == 'N')
	{
		$msg = '已关闭文章页面更新。';
		AutoHtml_Msg('arc',$msg);
	}
	else
	{
		$msg = '动态文章或者不在更新时间内，或者当前文章不需要更新。'.$sd.$st.$gt;
		AutoHtml_Msg('arc',$msg);
	}
	exit();
}
else if($type == 'list' || $type == 'rss' )
{
	$tid = (isset($tid) && is_numeric($tid)) ? $tid : 0;

	if($tid=='0')
	{
		$msg = '栏目ID错误，请后台设置处参考设置方法。';
		AutoHtml_Msg('list',$msg);
		exit();
	}

	if(file_exists(CACHELIST))
	{
		require_once(CACHELIST);
		$isuptime = $nowtime - ${'tid_uptime_'.$tid} - $auto_list_uptime;
		$isuptimerss = $nowtime - ${'rss_uptime_'.$tid} - $auto_rss_uptime;
	}
	else
	{
		$isuptime = $isuptimerss = 12345;
	}
	if( $isuptime > 0 && $auto_list_open == 'Y' )
	{
		AutoHtml_UpCache();
		require_once(DEDEINC.'/arc.listview.class.php');
		$auto_list = new ListView($tid);
		$isdefault = $auto_list->TypeLink->TypeInfos['isdefault'];
		$typename  = $auto_list->TypeLink->TypeInfos['typename'];

		if( $isdefault != '-1' )
		{
			$reurl = $auto_list->MakeHtml(1,$auto_list_pages);
			AutoHtml_UpTime('tid',$tid);
			$msg = '重新生成栏目 “'.$typename.'” 前 '.$auto_list_pages.' 页静态文件。';
			AutoHtml_Msg('list',$msg,'reload');
		}
		else
		{
			$msg = '“'.$typename.'”是动态栏目，不需要生成静态文件。';
			AutoHtml_Msg('list',$msg);
		}

		$auto_list->Close();
	}
	else if($auto_list_open == 'N' )
	{
		$msg = '已关闭栏目更新。';
		AutoHtml_Msg('list',$msg);
		exit();
	}
	else
	{
		$msg = '当前栏目不需要重新生成静态文件。';
		AutoHtml_Msg('list',$msg);
	}
	if( $isuptimerss > 0 && $auto_rss_open == 'Y' )
	{
		require_once(DEDEINC."/arc.rssview.class.php");
		$auto_rss = new RssView($tid,$auto_rss_max);
		$rssurl = $auto_rss->MakeRss();
		$auto_rss->Close();
		AutoHtml_UpTime('rss',$tid);
		$msg = '重新生成栏目 “'.$typename.'” RSS文件。';
		AutoHtml_Msg('rss',$msg,'reload');
	}
	else if($auto_rss_open == 'N' )
	{
		$msg = '已关闭RSS更新。';
		AutoHtml_Msg('rss',$msg);
		exit();
	}
	else
	{
		$msg = '当前栏目不需要重新生成RSS。';
		AutoHtml_Msg('rss',$msg);
	}
	exit();
}

else if($type == 'freelist')
{
	$fid = (isset($fid) && is_numeric($fid)) ? $fid : 0;

	if($fid=='0')
	{
		$msg = '自由列表ID错误，请后台设置处参考设置方法。';
		AutoHtml_Msg($msg);
		exit();
	}

	if(file_exists(CACHELIST))
	{
		require_once(CACHELIST);
		$isuptime = $nowtime - ${'fid_uptime_'.$fid} - $auto_freelist_uptime;
	}
	else
	{
		$isuptime = 12345;
	}

	if( $isuptime > 0 && $auto_freelist_open == 'Y')
	{
		require_once(DEDEINC."/arc.freelist.class.php");
		$lv = new FreeList($fid);
		$lv->MakeHtml('1',$auto_freelist_pages);
		$lv->Close();
		AutoHtml_UpTime('fid',$fid);

		$msg = '重新生成自由列表 “'.$fid.'” 前 '.$auto_freelist_pages.' 条记录。';
		AutoHtml_Msg('freelist',$msg,'reload');
	}
	else if( $auto_freelist_open == 'N')
	{
		$msg = '已关闭自由列表更新。';
		AutoHtml_Msg('freelist',$msg);
	}
	else
	{
		$msg = '不需要更新当前自由列表。';
		AutoHtml_Msg('freelist',$msg);
	}

	exit();
}
else if($type == 'index')
{
	$index_uptime = (isset($index_uptime) && is_numeric($index_uptime)) ? $index_uptime : 0;
	$isuptime = $nowtime - $index_uptime - $auto_index_uptime;
	if( $auto_index_open == 'Y' && $isuptime > 0 )
	{
		require_once(DEDEINC."/arc.partview.class.php");

		$auto_index_templet = str_replace("{style}",$cfg_df_style,$auto_index_templet);
		$auto_index = new PartView();
		$GLOBALS['_arclistEnv'] = 'index';
		$auto_index->SetTemplet($cfg_basedir.$cfg_templets_dir."/".$auto_index_templet);
		$auto_index->SaveToHtml($auto_index_indexFile);
		$auto_index->Close();
		AutoHtml_UpTime('index');

		$msg = '已重新生成网站首页静态文件。';
		AutoHtml_Msg('index',$msg,'reload');
	}
	else if($auto_index_open == 'N' )
	{
		$msg = '已关闭首页更新。';
		AutoHtml_Msg('index',$msg);
	}
	else
	{
		$msg = '首页不需要更新。';
		AutoHtml_Msg('index',$msg);
	}
	exit();
}

	$msg = '代码错误，请联系作者。';
	AutoHtml_Msg('error',$msg);
	exit();

?>

