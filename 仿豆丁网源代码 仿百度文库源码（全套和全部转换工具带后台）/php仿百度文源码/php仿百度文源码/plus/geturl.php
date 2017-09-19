<?php
require_once(dirname(__FILE__)."/../include/common.inc.php");
require_once(DEDEINC."/channelunit.class.php");
//文档ID
$id = intval($aid);

//获得附加表信息
$row = $dsql->GetOne("Select ch.addtable,arc.mid From `#@__arctiny` arc left join `#@__channeltype` ch on ch.id=arc.channel where arc.id='$id' ");

if(empty($row['addtable']))
{
	exit($cfg_cmsurl."/".$cfg_book_swfurl."/noonlineviewdoc.swf");
}

//获得在线阅读swf地址
$row = $dsql->GetOne("select onlineviewurl from `{$row['addtable']}` where aid = '$id'");
//$dsql->ExecuteNoneQuery("select onlineviewurl from `{$row['addtable']}` where aid = '$id'");

//如果为空，则显示默认文档
if(empty($row['onlineviewurl'])){
	exit($cfg_cmsurl."/".$cfg_book_swfurl."/noonlineviewdoc.swf");
}

exit($row['onlineviewurl']);
?>