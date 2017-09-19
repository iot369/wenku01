<?php
require_once(dirname(__FILE__)."/../include/common.inc.php");
$aid = (isset($aid) && is_numeric($aid)) ? $aid : 0;
$dsql->SetQuery("select distinct b.userid userid from t_member_download a, t_member b where a.mid = b.mid and a.aid = '$aid';");
$dsql->Execute();
$users = "";
while ($row = $dsql->GetArray())
{
	$users.=$row['userid']."；";
}
echo "document.write('{$users}');";
exit();
?>