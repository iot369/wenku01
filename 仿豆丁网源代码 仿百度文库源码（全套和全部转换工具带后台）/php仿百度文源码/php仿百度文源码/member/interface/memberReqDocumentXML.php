<?php
require_once(dirname(__FILE__).'/../config.php');
require_once(DEDEINC."/userlogin.class.php");
//提供给flash浏览器的接口文件：
//传入参数：aid，表示文档的ID，这里根据ID查询出该文档的相关信息
$cInfos = $dsql->GetOne("select a.doctitle, a.swfurl, a.pagenumber from #@__member_response a where a.id = '$id'; ");

$onlineviewurl = $cfg_cmsurl."/".$cfg_resp_swfurl."/noonlineviewdoc.swf";
//如果为空，则显示默认文档
if(!empty($cInfos['swfurl'])){
	$onlineviewurl = $cInfos['swfurl'];
}

//文档总页数
$totalpagenumber = (int)$cInfos["pagenumber"];
//可免费页数
$freepagenumber = $totalpagenumber;

if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml") ){ 
	header("Content-type: application/xhtml+xml;charset=utf-8"); 
} else { 
	header("Content-type: text/xml;charset=utf-8"); 
}
echo "<?xml version='1.0' encoding='utf-8'?>\n";
?>
 <documentInfo>
	<swfFile><?php echo $onlineviewurl;?></swfFile>
	<totalPage><?php echo $totalpagenumber;?></totalPage>
	<freepagenumber><?php echo $freepagenumber;?></freepagenumber>
	<isLogin><?php echo $cfg_ml->IsLogin(); ?></isLogin>
	<loginId><?php echo $cfg_ml->M_ID; ?></loginId>
	<loginName><?php echo $cfg_ml->M_UserName; ?></loginName>
</documentInfo>