<?php

/**
 * 文档digg处理ajax文件
 *
 */
require_once(dirname(__FILE__)."/../include/common.inc.php");

$action = isset($action) ? trim($action) : '';
$id = empty($id)? 0 : intval(preg_replace("/[^\d]/",'', $id));

if($id < 1)
{
	exit();
}
$maintable = '#@__archives';
if($action == 'good')
{
	$dsql->ExecuteNoneQuery("Update `$maintable` set scores = scores + {$cfg_caicai_add},goodpost=goodpost+1,lastpost=".time()." where id='$id'");
}
else if($action=='bad')
{
	$dsql->ExecuteNoneQuery("Update `$maintable` set scores = scores - {$cfg_caicai_sub},badpost=badpost+1,lastpost=".time()." where id='$id'");
}
$digg = '';
$row = $dsql->GetOne("Select goodpost,badpost,scores From `$maintable` where id='$id' ");
if(!is_array($row))
{
	exit();
}
if($row['goodpost']+$row['badpost'] == 0)
{
	$row['goodper'] = $row['badper'] = 0;
}
else
{
	$row['goodper'] = number_format($row['goodpost']/($row['goodpost']+$row['badpost']),3)*100;
	$row['badper'] = 100-$row['goodper'];
}

if(empty($formurl)) $formurl = '';
if($formurl=='caicai')
{
	if($action == 'good') $digg = $row['goodpost'];
	if($action == 'bad') $digg  = $row['badpost'];
}
else
{
	$row['goodper'] = trim(sprintf("%4.2f", $row['goodper']));
	$row['badper'] = trim(sprintf("%4.2f", $row['badper']));
	$digg = '<div class="good"><a href="javascript:postDigg(\'good\',\''.$id.'\')">
			<p>这个文档不错</p>
			<div class="bar">
                <div id="g_img" style="width:'.$row['goodper'].'%"></div>
              </div>
              <span class="barnum">'.$row['goodper'].'%('.$row['goodpost'].')</span></a></div>
			  
			 <div class="bad"><a href="javascript:postDigg(\'bad\',\''.$id.'\')" >
              <p>文档有待改进</p>
              <div class="bar">
                <div id="b_img" style="width:'.$row['badper'].'%"></div>
              </div>
              <span class="barnum">'.$row['badper'].'%('.$row['badpost'].')</span></a></div>';
}
AjaxHead();
echo $digg;
exit();
