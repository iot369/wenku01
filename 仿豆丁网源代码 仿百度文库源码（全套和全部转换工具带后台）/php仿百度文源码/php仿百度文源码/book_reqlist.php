<?php
require_once (dirname(__FILE__) . "/include/common.inc.php");
require_once (DEDEINC . "/arc.bookrequest.class.php");//нд╣╣пЭим
$PageNo = $pageno;
$dlist = new BookRequestList('bookrequest_list.htm');
$dlist->Display();
exit();
?>