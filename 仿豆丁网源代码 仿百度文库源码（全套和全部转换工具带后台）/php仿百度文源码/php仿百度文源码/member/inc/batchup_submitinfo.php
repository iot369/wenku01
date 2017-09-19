<?php
require_once(dirname(__FILE__).'/../config.php');
CheckRank(0,0);
if(!is_numeric($pagenumber2)){
	$pagenumber2 = 0;
}
$arryarcid = explode(',', $arcids);
//会员ID
$mid = $cfg_ml->M_ID;
//注意只能更新自己上传的，不能随便来一个文档ID也更新了！！！文档是自己的才台更新
foreach($arryarcid as $arcid){
	//先判断文档是否属于当前会员
	$row = $dsql->GetOne("Select count(1) as totals From `#@__archives` where id='$arcid' and mid = '$mid' ");
	$num = (int)$row['totals'];
	if($num == 0){
		//echo "非法操作！";
		continue;
	}
	
	//更新文档栏目
	$dsql->ExecuteNoneQuery("Update `#@__archives` set typeid='$typeid' where id = '$arcid' ");
	$dsql->ExecuteNoneQuery("Update `#@__addonbook` set typeid='$typeid',  needmoney='$needmoney', pagenumber2='$pagenumber2' where aid = '$arcid' ");
	$dsql->ExecuteNoneQuery("Update `#@__arctiny` set typeid='$typeid' where id = '$arcid' ");
}
echo "0";
?>