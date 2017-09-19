<?php
require_once(dirname(__FILE__)."/config.php");
//抓取文档的缩略图
//获得在线阅读swf地址，注：aid是附件ID，需要查找到对应的文档ID
//先查找对应的文档ID
$row = $dsql->GetOne("select arcid from #@__uploads where aid = '$aid'");

$aid = $row["arcid"];//以下就表示文档ID了。


$row = $dsql->GetOne("select onlineviewurl from `#@__addonbook` where aid = '$aid'");

if($row["onlineviewurl"] == ""){//没有找到Swf，退出 
	echo "can't find the onlineviewurl!";
	exit();
}
$flash2jpeg = new COM("SunCN.Flash2Jpeg");
if ($flash2jpeg){
	$filename = '1'.'-'.dd2char(MyDate('ymdHis', time())).$rnddd.'-L';

	$filedir = $cfg_image_dir.'/'.MyDate($cfg_addon_savetype, time());
	
	//如果目录不存在，则先创建
	if(!file_exists($cfg_basedir.$filedir)){
		mkdir($cfg_basedir.$filedir, $cfg_dir_purview);
	}
	
	$fileurl = $filedir.'/'.$filename.'.jpg';
	
	$filepath = $cfg_basedir.$fileurl;

	$a = $flash2jpeg->Flash2Jpeg($cfg_basedir.$row['onlineviewurl'], 130, 140, $filepath);

	if ($a){ //提取失败
		$show_message.="Creat smallPic error!";

		$fileurl = "";

		echo $show_message;
	}else{ //提取成功
		$show_message.="Creat smallPic OK.";

		//更新缩略图字段
		$dsql->ExecuteNoneQuery("Update `#@__archives` set litpic='$fileurl' where id='$aid' ");

		echo $show_message;
	}
	//$flash2jpeg->Release();
	$flash2jpeg = null;
	
}else{
	echo "Creat Flash2Jpeg error!";
}
?>