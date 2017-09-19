<?php
require_once(dirname(__FILE__)."/config.php");
require_once(DEDEINC."/autohtml.func.php");
CheckPurview('sys_plus,sys_MakeHtml');

$nowtime = Time();
if(!isset($mod)) $mod = 'index';
if(!isset($dopost)) $dopost = 'abcd';

if($dopost=="save")
{
	if($mod=='index')
	{
		if(empty($auto_index_open)) $auto_index_open = "N";
		if(empty($auto_index_reload)) $auto_index_reload = "N";
		if(empty($auto_index_uptime)) $auto_index_uptime = 60;
		$auto_index_uptime = $auto_index_uptime * 60;

		$row[] = array();
		if($auto_index_open == 'Y')
		$row = $dsql->GetOne("Select * From #@__homepageset");
		AutoHtml_Text($mod,$auto_index_open);
		AutoHtml_Index("{$auto_index_open}","{$auto_index_reload}","{$row['templet']}","{$row['position']}","{$auto_index_uptime}");
	}
	else if($mod=='list')
	{
		if(empty($auto_list_open)) $auto_list_open= "N";
		if(empty($auto_list_reload)) $auto_list_reload= "N";
		if(empty($auto_list_uptime)) $auto_list_uptime= 60;
		if(empty($auto_list_pages)) $auto_list_pages= 50;
		$auto_list_uptime = $auto_list_uptime * 60;
		$auto_list_pages = $auto_list_pages > 50 ? 50 : $auto_list_pages;

		AutoHtml_Text($mod,$auto_list_open,$auto_list_pages);
		AutoHtml_List($auto_list_open,$auto_list_reload,$auto_list_uptime,$auto_list_pages);
	}
	else if($mod=='rss')
	{
		if(empty($auto_rss_open)) $auto_rss_open= "N";
		if(empty($auto_rss_max)) $auto_rss_max= 50;
		if(empty($auto_rss_uptime)) $auto_rss_uptime= 60;
		$auto_rss_uptime = $auto_rss_uptime * 60;

		AutoHtml_Text($mod,$auto_rss_open,$auto_rss_max);
		AutoHtml_Rss($auto_rss_open,$auto_rss_max,$auto_rss_uptime);
	}
	else if($mod=='freelist')
	{
		if(empty($auto_freelist_open)) $auto_freelist_open= "N";
		if(empty($auto_freelist_reload)) $auto_freelist_reload= "N";
		if(empty($auto_freelist_uptime)) $auto_freelist_uptime= 60;
		if(empty($auto_freelist_pages)) $auto_freelist_pages= 100;
		$auto_freelist_uptime = $auto_freelist_uptime * 60;
		$auto_freelist_pages = $auto_freelist_pages > 100 ? 100 : $auto_freelist_pages;

		AutoHtml_Text($mod,$auto_freelist_open,$auto_freelist_pages);
		AutoHtml_Freelist($auto_freelist_open,$auto_freelist_reload,$auto_freelist_uptime,$auto_freelist_pages);
	}
	else if($mod=='arc')
	{
		if(empty($auto_arc_open)) $auto_arc_open= "N";
		if(empty($auto_arc_reload)) $auto_arc_reload= "N";
		if(empty($auto_arc_startid)) $auto_arc_startid= 0;
		if(empty($auto_arc_endid)) $auto_arc_endid= 0;
		if(empty($auto_arc_starttime)) $auto_arc_starttime= 0;
		if(empty($auto_arc_endtime)) $auto_arc_endtime= 0;
		if(empty($auto_arc_uptime)) $auto_arc_uptime= 60;

		$auto_arc_uptime = $auto_arc_uptime * 60;
		$auto_arc_starttime = strtotime("$auto_arc_starttime");
		$auto_arc_endtime = strtotime("$auto_arc_endtime");
		$auto_arc_starttime = ($auto_arc_starttime < 0 ) ? 0 : $auto_arc_starttime;
		$auto_arc_endtime = ($auto_arc_endtime > 0 ) ? $auto_arc_endtime : 0;
		$auto_arc_endid = (isset($auto_arc_endid) && is_numeric($auto_arc_endid)) ? $auto_arc_endid : 0;
		$auto_arc_startid = (isset($auto_arc_startid) && is_numeric($auto_arc_startid)) ? $auto_arc_startid : 0;
		$auto_reload = ($auto_arc_endtime < $nowtime ) && !empty($auto_reload) ? 'N' : $auto_reload ;

		AutoHtml_Text($mod,$auto_arc_open);
		AutoHtml_Arc($auto_arc_open,$auto_arc_reload,$auto_arc_startid,$auto_arc_endid,
				$auto_arc_starttime,$auto_arc_endtime,$auto_arc_uptime);
	}
	else
	{
		AutoHtml_Text('error','mod');
	}
	exit();
}
else if($dopost=="view")
{
	$chtml = '';
	$chtml .= AutoHtml_Text('templets','index').AutoHtml_Text('templets','list');
	$chtml .= AutoHtml_Text('templets','freelist').AutoHtml_Text('templets','arc');
	$chtml .= AutoHtml_Text('templetstishi');
	exit();
}
else if($dopost=="checkver")
{
	AutoHtml_CheckVer();
	exit();
}
else if($dopost=="abcd")
{
	if(file_exists(CACHEFILE))
	{
		require_once(CACHEFILE);
	}
	else
	{
		$auto_index_open = "N";
		$auto_index_reload = "N";
		$auto_index_uptime = 3600;

		$auto_list_open = "N";
		$auto_list_reload = "N";
		$auto_list_uptime = 3600;
		$auto_list_pages = 100;

		$auto_rss_open = "N";
		$auto_rss_max = 100;
		$auto_rss_uptime = 3600;
		$auto_rss_pages = 100;

		$auto_freelist_open = "N";
		$auto_freelist_reload = "N";
		$auto_freelist_uptime = 3600;
		$auto_freelist_pages = 50;

		$auto_arc_open = "N";
		$auto_arc_reload = "N";
		$auto_arc_startid = $auto_arc_endid = 0;
		$auto_arc_starttime = $auto_arc_endtime = 0;
		$auto_arc_uptime = 3600;
	}
	$auto_index_uptime = $auto_index_uptime / 60;
	$auto_list_uptime = $auto_list_uptime / 60;
	$auto_rss_uptime = $auto_rss_uptime / 60;
	$auto_freelist_uptime = $auto_freelist_uptime / 60;
	$auto_arc_uptime = $auto_arc_uptime / 60;
	require_once(DEDEADMIN."/templets/autohtml_admin.htm");
	exit();
}
else
{
	AutoHtml_Text('error','dopost');
	exit();
}
?>