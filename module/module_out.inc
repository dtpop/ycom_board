<?php

// module:com_board_basic_out

$b = new rex_ycom_board('aid-REX_ARTICLE_ID', $this->getValue('name'));
$b->setUrl(rex_getUrl(REX_ARTICLE_ID));
$b->setThreadsPerPage("REX_VALUE[1]");
$b->setPostsPerPage("REX_VALUE[2]");
$b->setNotificationTemplate("REX_VALUE[3]");
$b->setAdminGroup("REX_VALUE[4]");

echo $b->getView();

?>
