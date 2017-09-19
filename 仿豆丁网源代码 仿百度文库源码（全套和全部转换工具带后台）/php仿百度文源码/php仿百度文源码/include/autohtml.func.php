<?php
require_once(DEDEINC."/autohtml.text.php");
define('CACHEFILE',DEDEDATA.'/cache/autohtml.global.php');
define('CACHELIST',DEDEDATA.'/cache/autohtml.list.php');
define('AUTOHTMLVER','V5.6.3');

function AutoHtml_Replace($name,$value,$id='N')
{
	if(is_numeric($id))
	{
		if(file_exists(CACHELIST))
		{
			$content = file_get_contents(CACHELIST);
		}
		else
		{
			$content = "<?php\r\n?>";
			file_put_contents(CACHELIST,$content); 
		}
		$content = AutoHtml_Insert($content,'/\$'.$name.'_'.$id.' \= \".*?\";/i','$'.$name.'_'.$id.' = "'.$value.'";');
		file_put_contents(CACHELIST,$content); 
	}
	else
	{
		if(file_exists(CACHEFILE))
		{
			$content = file_get_contents(CACHEFILE);
		}
		else
		{
			$content = "<?php\r\n?>";
			file_put_contents(CACHEFILE,$content); 
		}
		$content = AutoHtml_Insert($content,'/\$'.$name.' \= \".*?\";/i', '$'.$name.' = "'.$value.'";');
		file_put_contents(CACHEFILE,$content); 
	}
}

function AutoHtml_Insert($content,$find,$replace)
{
	if(preg_match($find,$content)) $content = preg_replace($find,$replace,$content);
	else $content .= " \r\n<?php".$replace."\r\n?>";
	$content = preg_replace("/\?>(.+?)\s<\?php/","",$content);
	return $content;
}

function AutoHtml_Arc($auto_arc_open,$auto_arc_reload,$auto_arc_startid,$auto_arc_endid,
			$auto_arc_starttime,$auto_arc_endtime,$auto_arc_uptime)
{
	AutoHtml_Replace(auto_arc_open,$auto_arc_open);
	AutoHtml_Replace(auto_arc_reload,$auto_arc_reload);
	AutoHtml_Replace(auto_arc_startid,$auto_arc_startid);
	AutoHtml_Replace(auto_arc_endid,$auto_arc_endid);
	AutoHtml_Replace(auto_arc_starttime,$auto_arc_starttime);
	AutoHtml_Replace(auto_arc_endtime,$auto_arc_endtime);
	AutoHtml_Replace(auto_arc_uptime,$auto_arc_uptime);
}

function AutoHtml_Freelist($auto_freelist_open,$auto_freelist_reload,$auto_freelist_uptime,$auto_freelist_pages)
{
	AutoHtml_Replace(auto_freelist_open,$auto_freelist_open);
	AutoHtml_Replace(auto_freelist_reload,$auto_freelist_reload);
	AutoHtml_Replace(auto_freelist_uptime,$auto_freelist_uptime);
	AutoHtml_Replace(auto_freelist_pages,$auto_freelist_pages);
}

function AutoHtml_List($auto_list_open,$auto_list_reload,$auto_list_uptime,$auto_list_pages)
{
	AutoHtml_Replace(auto_list_open,$auto_list_open);
	AutoHtml_Replace(auto_list_reload,$auto_list_reload);
	AutoHtml_Replace(auto_list_uptime,$auto_list_uptime);
	AutoHtml_Replace(auto_list_pages,$auto_list_pages);
}

function AutoHtml_Rss($auto_rss_open,$auto_rss_max,$auto_rss_uptime)
{
	AutoHtml_Replace(auto_rss_open,$auto_rss_open);
	AutoHtml_Replace(auto_rss_max,$auto_rss_max);
	AutoHtml_Replace(auto_rss_uptime,$auto_rss_uptime);
}

function AutoHtml_Index($auto_index_open,$auto_index_reload,$auto_index_templet,$auto_index_indexFile,$auto_index_uptime)
{
	AutoHtml_Replace(auto_index_open,$auto_index_open);
	AutoHtml_Replace(auto_index_reload,$auto_index_reload);
	AutoHtml_Replace(auto_index_templet,$auto_index_templet);
	AutoHtml_Replace(auto_index_indexFile,$auto_index_indexFile);
	AutoHtml_Replace(auto_index_uptime,$auto_index_uptime);
}

function AutoHtml_UpArc($aid)
{
	global $dsql;
	$nowtime = Time();
	$lastupdate = ( $auto_arc_endtime < $nowtime ) ? $nowtime : $auto_arc_endtime;
	$updatesql = "Update `#@__archives` set lastupdate='{$lastupdate}' where id='{$aid}'";
	$dsql->ExecuteNoneQuery($updatesql);
}

function AutoHtml_UpTime($type,$id)
{
	if($type=='index') AutoHtml_Replace(index_uptime,Time());
	else if(ereg($type,'tid|fid')) AutoHtml_Replace($type.'_uptime',Time(),$id);
}

function AutoHtml_UpCache()
{
	global $dsql;
	$dsql->ExecuteNoneQuery("Delete From `#@__arccache`");
}

function AutoHtml_CheckVer()
{
	require(DEDEINC.'/dedehttpdown.class.php');
	global $autohtml_code,$autohtml_ver;
	foreach($autohtml_code as $v)
	$vu .= ParCv($v);
	$dhd = new DedeHttpDown();
	$dhd->OpenUrl($vu);
	$newver = trim($dhd->GetHtml());
	$dhd->Close();

	$newcode = $nowcode = '';
	$newvers = explode('.',ereg_replace('V','',$newver));
	$nowvers = explode('.',ereg_replace('V','',AUTOHTMLVER));
	foreach($nowvers as $v) $nowcode .= is_numeric($v)?$v:'';
	foreach($newvers as $v) $newcode .= is_numeric($v)?$v:'';
	$msg = ($newcode!=''&&$nowcode!='')?$newcode>$nowcode?$autohtml_ver['hnew']:$autohtml_ver['nonew']:$autohtml_ver['error'];
	$msg = ereg_replace('#nowver#',AUTOHTMLVER.$nowver,$msg);
	$msg = ereg_replace('#newver#','V'.$newver,$msg);
	echo $msg;
}

function AutoHtml_Msg($mod,$msg,$reload='')
{
	if(file_exists(CACHEFILE))require_once(CACHEFILE);
	if(${'auto_'.$mod.'_reload'}=='Y' && $reload == 'reload')
	echo "document.write(\"<script> setTimeout(\\\"location.reload();\\\",\\\"1000\\\");</script>\");\r\n";
	else echo "document.write(\"<meta name='autohtml' content='".$msg."' />\");\r\n";
}

function AutoHtml_Text($auto_mod,$auto_type='',$auto_num='N')
{
	global $autohtml_text,$cfg_df_style;

	$rehtml = '';
	$rehtml .= "<style>a,a:link,a:visited,a:hover,.autohtmlbox{font-size:14px;color:red;text-decoration:none;}";
	$rehtml .= ".autohtmlbox{color:#000;}</style><div class='autohtmlbox'>";
	if(in_array($auto_mod,array('index','list','freelist','rss','arc')))
	{
		$rehtml .= ereg_replace('#modname#',$autohtml_text['name'][$auto_mod],$autohtml_text['open'][$auto_type]);
		$rehtml .= is_numeric($auto_num) ? ereg_replace('#autohtmlnum#',$auto_num,$autohtml_text['htmlnum']) : '';
		$rehtml .= ($auto_type == 'Y' && $auto_mod != 'RSS') ? '<br />'.$autohtml_text['templetstishi'] : '';
	}
	else if($auto_mod=='templets')
	{
		$rehtml .= $autohtml_text['name'][$auto_type].$autohtml_text['xx'];
		$rehtml .= "templets/".$cfg_df_style."/".$autohtml_text['templets'][$auto_type]."\r\n<br /><br />";
	}
	else
	{
		$rehtml .= $auto_type==''?$autohtml_text[$auto_mod]:$autohtml_text[$auto_mod][$auto_type];
	}
	echo $rehtml.'</div>';
}

?>