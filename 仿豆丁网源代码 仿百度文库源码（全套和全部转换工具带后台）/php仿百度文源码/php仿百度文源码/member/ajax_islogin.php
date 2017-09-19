<?php
require_once(dirname(__FILE__)."/config.php");
AjaxHead();
if($myurl == '')
{
	exit('0');
}
else
{
	exit('1');	
}
?>
