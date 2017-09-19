<?php
require_once(dirname(__FILE__).'/../config.php');
$svali = GetCkVdValue();
if(strtolower($vdcode)!=$svali || $svali=='')
{
	echo "-1";
}else{
	echo "0";
}
exit();
?>