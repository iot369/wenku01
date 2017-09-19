<?php
require_once(dirname(__FILE__)."/config.php");
CheckRank(0,0);
$query = "Select * From `#@__member_request` a, `#@__member` b where a.mid = b.mid order by id desc";

$tpl = new DedeTemplate();
$tpl->LoadTemplate(DEDEMEMBER.'/templets/book_request_resp_docview.htm');
$tpl->Display();
exit();

?>