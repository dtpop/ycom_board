<?php

// module:com_board_basic_in

?>

Threads pro Seite:<br>
<input type="text" name="REX_INPUT_VALUE[1]" value="REX_VALUE[id=1 ifempty=10]"/><br><br>

Beiträge pro Seite:<br>
<input type="text" name="REX_INPUT_VALUE[2]" value="REX_VALUE[id=2 ifempty=10]"/><br><br>

E-Mail-Template für Benachrichtigungen:<br>
<?php
$select = new rex_select();
$select->setName('REX_INPUT_VALUE[3]');
$select->setSize(1);
$select->addOption('–', 0);
$select->addSqlOptions('SELECT name, name FROM rex_yform_email_template ORDER BY name');
$select->setSelected('REX_VALUE[3]');
$select->show();
?>
<br><br>

Benutzergruppe mit Adminfunktionen:<br>
<?php
$select = new rex_select();
$select->setName('REX_INPUT_VALUE[4]');
$select->setSize(1);
$select->addOption('–', 0);
$select->addSqlOptions('SELECT name, id FROM rex_ycom_group ORDER BY name');
$select->setSelected('REX_VALUE[4]');
$select->show();
?>
