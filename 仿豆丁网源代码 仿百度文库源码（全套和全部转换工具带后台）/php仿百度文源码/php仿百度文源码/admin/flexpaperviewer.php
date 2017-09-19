<?php
require_once(dirname(__FILE__)."/config.php");
if(empty($aid)) $aid = '0';

//取文档的swf文件路径
$row = $dsql->GetOne("select onlineviewurl from #@__addonbook where aid = '$aid'");
$onlineviewurl = $row['onlineviewurl'];

if($onlineviewurl == ""){
	exit("对不起，该文档尚未转换成swf，请稍后再试，谢谢！");
}

//模板
include DedeInclude("templets/flexpaperviewer.htm");
exit();
?>