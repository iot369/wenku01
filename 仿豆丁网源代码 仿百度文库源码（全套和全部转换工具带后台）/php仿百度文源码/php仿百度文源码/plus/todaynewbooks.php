<?php
require_once(dirname(__FILE__)."/../include/common.inc.php");
$row = $dsql->GetOne("SELECT count( * ) AS totals FROM t_archives WHERE pubdate > UNIX_TIMESTAMP( CURDATE( ) ) ");
if(empty($row['totals'])) $row['totals'] = 0;
echo "document.write('{$row['totals']}');";
exit();
/*-----------
如果想显示收藏次数,即把下面ＪＳ调用放到文档模板适当位置
<script src="{dede:global name='cfg_phpurl'/}/disdls.php?aid={dede:field name='id'/}" language="javascript"></script>
------------*/
?>