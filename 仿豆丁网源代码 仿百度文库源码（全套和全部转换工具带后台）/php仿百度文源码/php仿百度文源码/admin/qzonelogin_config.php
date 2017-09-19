<?php
/**
 * 系统密码提示问
 *
 * @version        $Id: sys_safe.php 1 22:28 2010年7月20日Z tianya $
 * @package        DedeCMS.Administrator
 * @copyright      Copyright (c) 2007 - 2010, DesDev, Inc.
 * @license        http://help.dedecms.com/usersguide/license.html
 * @link           http://www.dedecms.com
 */
require_once(dirname(__FILE__)."/config.php");
CheckPurview('sys_Safe');
$configfile = DEDEDATA."/qzonelogin_config.php";
if(empty($dopost)) $dopost = "";

if($dopost == "save")
{
    
	$configstr = "";
	$appid = empty($appid)? '' : $appid;
	$appkey = empty($appkey)? '' : $appkey;
	if(!$appid || !$appkey){
		ShowMsg("AppId、AppKey均为必填，请完善信息",-1);
		exit;		
	}	
	$configstr = "\$appid = '{$appid}';\r\n";
	$configstr .= "\$appkey = '{$appkey}';\r\n";   
    $configstr = "<"."?php\r\n".$configstr."?".">\r\n";  
    $fp = fopen($configfile, "w") or die("写入文件 $configfile 失败，请检查权限！");
    fwrite($fp, $configstr);
    fclose($fp);
    ShowMsg("修改配置成功！","qzonelogin_config.php");
    exit;
}

require_once($configfile);
include DedeInclude('templets/qzonelogin_config.htm');
?>