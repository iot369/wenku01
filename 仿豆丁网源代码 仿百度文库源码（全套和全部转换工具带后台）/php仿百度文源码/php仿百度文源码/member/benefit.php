<?php
require_once(dirname(__FILE__).'/config.php');
CheckRank(0,0);
$myurl = $cfg_basehost.$cfg_member_dir.'/index.php?uid='.$cfg_ml->M_LoginID;
//判断用户是否设置了用于提现的银行卡信息
$row = $dsql->GetOne("select banktype, account from `#@__member_bank_account` where mid = '$cfg_ml->M_ID'");
$banktype = $row["banktype"];//类型
$account = $row["account"];//卡号

$tpl = new DedeTemplate();
$tpl->LoadTemplate(DEDEMEMBER.'/templets/benefit.htm');
$tpl->Display();
?>