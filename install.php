<?php

$import = [
    'ycom_board',
    'ycom_board_thread_notification'
];


foreach ($import as $imp_file) {
    $content = rex_file::get(rex_path::addon('ycom_board','install/tablesets/'.$imp_file.'.json'));
    rex_yform_manager_table_api::importTablesets($content);
}


