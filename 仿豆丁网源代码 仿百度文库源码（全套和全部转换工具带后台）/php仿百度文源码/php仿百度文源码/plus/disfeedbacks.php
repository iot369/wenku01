<?php
require_once(dirname(__FILE__)."/../include/common.inc.php");
$aid = (isset($aid) && is_numeric($aid)) ? $aid : 0;
$row = $dsql->GetOne("Select count(1) as totals From `#@__feedback`  where aid='$aid' ");
if(empty($row['totals'])) $row['totals'] = 0;
echo "document.write('{$row['totals']}');";
exit();
/*-----------
如果想显示评论次数,即把下面ＪＳ调用放到文档模板适当位置
<script src="{dede:global name='cfg_phpurl'/}/disdls.php?aid={dede:field name='id'/}" language="javascript"></script>
------------*/
?>s