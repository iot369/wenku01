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
 * @版本	AutoHtml V5.6.2 Final FOR DedeCMS V56
 *
 * @起始	2008-02-01发布第一个版本
 *
 * @最后修改	2010-04-23修改为当前版本
 *
 * @特别鸣谢	织梦技术论坛的各位朋友
 *
 */
!defined('DEDEINC') && exit("403 Forbidden!");

function lib_autohtml(&$ctag, &$refObj)
{
	global $dsql,$_sys_globals,$_arclistEnv,$cfg_phpurl;

	$author = $ctag->GetAtt('author');

	$type = $_sys_globals['curfile'];
	$type = $type == 'partview' ? $_arclistEnv == 'index' ? 'index' : 'freelist' : $type;

	if($type == 'archives')
	{
		$aid = $_sys_globals['aid'];
		$gquery = "Select lastupdate From #@__archives where id=$aid";
		$row = $dsql->GetOne($gquery);
	}

	$htmlcodes = array(
		'index'		=> "<script language=\"javascript\" type=\"text/javascript\" src=\"{$cfg_phpurl}/autohtml.php?type=index\"></script>",
		'list'		=> "<script language=\"javascript\" type=\"text/javascript\" src=\"{$cfg_phpurl}/autohtml.php?type=list&tid={$_sys_globals['typeid']}\"></script>",
		'freelist'	=> "<script language=\"javascript\" type=\"text/javascript\" src=\"{$cfg_phpurl}/autohtml.php?type=freelist&fid={$refObj->Fields['aid']}\"></script>",
		'archives'	=> "<script language=\"javascript\" type=\"text/javascript\" src=\"{$cfg_phpurl}/autohtml.php?aid={$refObj->Fields['aid']}&lastupdate={$refObj->Fields['lastupdate']}\"></script>",
	);

	if(ereg($type,'index|list|archives|freelist')) $htmlcode = $htmlcodes[$type];
	else $htmlcode = '';
	return $htmlcode;
}

?>


