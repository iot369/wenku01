<?php
require_once(dirname(__FILE__).'/../include/common.inc.php');

//获得当前脚本名称，如果你的系统被禁用了$_SERVER变量，请自行更改这个选项
$dedeNowurl = $s_scriptName = '';
$dedeNowurl = GetCurUrl();
$dedeNowurls = explode('?', $dedeNowurl);
$s_scriptName = $dedeNowurls[0];
$menutype = '';
$menutype_son = '';
$gourl=empty($gourl)? "" : RemoveXSS($gourl);
?>