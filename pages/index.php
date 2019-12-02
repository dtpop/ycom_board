<?php

// ------------------------------- Ist Modul schon vorhanden ?

if (rex::getUser()->isAdmin()) {
    $content = '';
    $searchtext = 'module:com_board_basic_out';

    $gm = rex_sql::factory();
    $gm->setQuery('select * from '.rex::getTable('module').' where output LIKE "%' . $searchtext . '%"');

    $module_id = 0;
    $board_module_name = 'rex - board';
    foreach ($gm->getArray() as $module) {
        $module_id = $module['id'];
        $board_module_name = $module['name'];
    }

    if (rex_request('install', 'integer') == 1) {
        $input = rex_file::get(rex_path::plugin('ycom', 'board','install/modules/board_input.php'));
        $output = rex_file::get(rex_path::plugin('ycom', 'board', 'install/modules/board_output.php'));

        $mi = rex_sql::factory();
        // $mi->debugsql = 1;
        $mi->setTable(rex::getTable('module'));
        $mi->setValue('input', $input);
        $mi->setValue('output', $output);

        if ($module_id == rex_request('module_id', 'integer', -1)) {
            $mi->setWhere('id="' . $module_id . '"');
            $mi->update();
            echo rex_view::success('Modul "' . $module_name . '" wurde aktualisiert');
        } else {
            $mi->setValue('name', $board_module_name);
            $mi->insert();
            $module_id = (int) $mi->getLastId();
            $module_name = $board_module_name;
            echo rex_view::success('board Modul wurde angelegt unter "' . $board_module_name . '"');
        }
    }

    $content .= '<p>'.$this->i18n('ycom_board_modul_description').'</p>';

//    dump($module_id);
    
    if ($module_id > 0) {
        $content .= '<p><a class="btn btn-primary" href="index.php?page=ycom/board&amp;install=1&amp;module_id=' . $module_id . '" class="rex-button">' . $this->i18n('ycom_board_update_module', htmlspecialchars($board_module_name)) . '</a></p>';
    } else {
        $content .= '<p><a class="btn btn-primary" href="index.php?page=ycom/board&amp;install=1" class="rex-button">' . $this->i18n('ycom_board_install_module', $board_module_name) . '</a></p>';
    }

    $fragment = new rex_fragment();
    $fragment->setVar('title', $this->i18n('ycom_board_install_modul'), false);
    $fragment->setVar('body', $content, false);
    echo $fragment->parse('core/page/section.php');
}

