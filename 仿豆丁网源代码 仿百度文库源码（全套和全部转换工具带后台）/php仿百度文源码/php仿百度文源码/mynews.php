<?php
require_once (dirname(__FILE__) . "/include/common.inc.php");
require_once DEDEINC."/arc.partview.class.php";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title><?php echo $cfg_webname; ?>- 公告内容:</title>
<meta name="description" content="<?php echo $cfg_description; ?>" />
<meta name="keywords" content="<?php echo $cfg_keywords; ?>" />
<link href="<?php echo $cfg_templets_skin; ?>/style/common.css" rel="stylesheet" type="text/css" />
<link href="<?php echo $cfg_templets_skin; ?>/style/indexv7.css" type="text/css" rel="stylesheet"/>
<!-- /公共js引用 开始 -->
<?php
$pv = new PartView();
$pv->SetTemplet($cfg_basedir . $cfg_templets_skin . "/comm_js.htm");
$pv->Display();
?>
</head>
<body>
<?php
$pv = new PartView();
$pv->SetTemplet($cfg_basedir . $cfg_templets_skin . "/head.htm");
$pv->Display();
?>
<?php
if (! is_numeric($aid))
{
echo "浏览页面参数不正确";
exit;
}
global $dsql;
$row = $dsql->GetOne("Select * from #@__mynews where aid=$aid");
if(!is_array($row))
{
echo "对不起,没有找到您所查找到的公告信息";
exit;
}
?>
<div style="width: 960px; margin: auto; text-align: left;">
<?php
echo "<h1 style='text-align:center;margin-top:20px; margin-bottom: 10px; font-size:20px; border-bottom:#ccc 1px dashed;'>".$row["title"]."</h1>";
echo "<div style='padding:8px;'>".$row["body"]."</div>";

echo "<div style='padding:8px; text-align: right;'>【".$row["writer"]."】发布于：".date('Y-m-d H:i:s', $row["senddate"])."</div>";
?>
</div>
<?php
$pv = new PartView();
$pv->SetTemplet($cfg_basedir . $cfg_templets_skin . "/footer.htm");
$pv->Display();
?>
</body>
</html>
