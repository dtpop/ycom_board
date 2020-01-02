<?php

$lang = 'de_de';


$content = rex_file::get(rex_path::addon('ycom_board','doc/'.$lang.'/readme.md'));
if ($content == '') {
    $content = '<p class="alert alert-warning">'.rex_i18n::rawMsg('yform_docs_filenotfound').'</p>';
}

if (class_exists('rex_markdown')) {
    $md = rex_markdown::factory();
    $content = $md->parse($content);
}


$fragment = new rex_fragment();
$fragment->setVar('title', 'YCom Board - Dokumentation', false);
$fragment->setVar('body', $content, false);
echo $fragment->parse('core/page/section.php');



