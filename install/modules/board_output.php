<?php

// module:com_board_basic_out


if (!"REX_VALUE[1]") {
    echo '<h3>Wert für Threads pro Seite nicht gesetzt!</<h3>';
    return;
}
if (!"REX_VALUE[2]") {
    echo '<h3>Wert für Beiträge pro Seite nicht gesetzt!</<h3>';
    return;
}
if (!"REX_VALUE[3]") {
    echo '<h3>Wert für Notification Template nicht gesetzt!</<h3>';
    return;
}
if (!"REX_VALUE[4]") {
    echo '<h3>Wert für Admin Group nicht gesetzt!</<h3>';
    return;
}

/*
if (rex::isFrontend() && !rex_ycom_auth::getUser()) {
    echo '<h3>Kein gültiger Benutzer eingeloggt.</h3>';
    return;
}
 */

$b = new rex_ycom_board('aid-REX_ARTICLE_ID', $this->getValue('name'));
$b->setUrl(rex_getUrl("REX_ARTICLE_ID"));
$b->setThreadsPerPage("REX_VALUE[1]");
$b->setPostsPerPage("REX_VALUE[2]");
$b->setNotificationTemplate("REX_VALUE[3]");
$b->setAdminGroup("REX_VALUE[4]");

echo $b->getView();

?>
