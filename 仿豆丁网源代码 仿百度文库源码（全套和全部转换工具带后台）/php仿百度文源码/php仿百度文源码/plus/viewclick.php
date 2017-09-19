<?php
require_once(dirname(__FILE__)."/../include/common.inc.php");
if(isset($aid))
{
	$arcID = $aid;
}
$cid = empty($cid)? 1 : intval(preg_replace("/[^-\d]+[^\d]/",'', $cid));
$arcID = $aid = empty($arcID)? 0 : intval(preg_replace("/[^\d]/",'', $arcID));

$maintable = '#@__archives';$idtype='id';
if($aid==0)
{
	exit();
}

//获得频道模型ID
if($cid < 0)
{
	$row = $dsql->GetOne("SELECT addtable FROM `#@__channeltype` WHERE id='$cid' AND issystem='-1';");
	$maintable = empty($row['addtable'])? '' : $row['addtable'];
	$idtype='aid';
}
$mid = (isset($mid) && is_numeric($mid)) ? $mid : 0;

//UpdateStat();
//更新今日文档点击量 added by caozhiyang 2010-08-12
doSysStatistics("doc_click");


if(!empty($view))
{
	$row = $dsql->GetOne(" Select click From `{$maintable}`  where {$idtype}='$aid' ");
	if(is_array($row))
	{
		echo "document.write('".$row['click']."');\r\n";
	}
}
exit();
/*-----------
如果想显示点击次数,请增加view参数,即把下面ＪＳ调用放到文档模板适当位置
<script src="{dede:field name='phpurl'/}/count.php?view=yes&aid={dede:field name='id'/}&mid={dede:field name='mid'/}" language="javascript"></script>
普通计数器为
<script src="{dede:field name='phpurl'/}/count.php?aid={dede:field name='id'/}&mid={dede:field name='mid'/}" language="javascript"></script>
------------*/
?>