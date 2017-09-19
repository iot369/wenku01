<?php
require_once(dirname(__FILE__).'/../config.php');
require_once(DEDEINC."/userlogin.class.php");
//提供给flash浏览器的接口文件：
//传入参数：aid，表示文档的ID，这里根据ID查询出该文档的相关信息
$cInfos = $dsql->GetOne("select a.title, a.writer, b.onlineviewurl, b.needmoney, b.pagenumber, b.pagenumber2 from #@__archives a, #@__addonbook b where a.id = b.aid and a.id = '$aid'; ");

$onlineviewurl = $cfg_cmsurl."/".$cfg_book_swfurl."/noonlineviewdoc.swf";
//如果为空，则显示默认文档
if(!empty($cInfos['onlineviewurl'])){
	$onlineviewurl = $cInfos['onlineviewurl'];
}

//文档总页数
$totalpagenumber = (int)$cInfos["pagenumber"];
//可免费页数
$freepagenumber = (int)$cInfos["pagenumber2"];
if($freepagenumber == 0 || $freepagenumber > $totalpagenumber){//为0表示不限制页数，或者免费页超过总页，显示全部
	$freepagenumber = $totalpagenumber;
}

if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml") ){ 
	header("Content-type: application/xhtml+xml;charset=utf-8"); 
} else { 
	header("Content-type: text/xml;charset=utf-8"); 
}
echo "<?xml version='1.0' encoding='utf-8'?>\n";
?>
 <documentInfo>
	<title><?php echo $cInfos["title"];?></title>
	<autor><?php echo $cInfos["writer"];?></autor>
	<swfFile><?php echo $onlineviewurl;?></swfFile>
	<needMoney><?php echo $cInfos["needmoney"];?></needMoney>
	<totalPage><?php echo $cInfos["pagenumber"];?></totalPage>
	<freepagenumber><?php echo $freepagenumber;?></freepagenumber>
	<isLogin><?php echo $cfg_ml->IsLogin(); ?></isLogin>
	<loginId><?php echo $cfg_ml->M_ID; ?></loginId>
	<loginName><?php echo $cfg_ml->M_UserName; ?></loginName>
</documentInfo>