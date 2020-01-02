<?php
/**
 * @var rex_com_board $this
 * @var rex_com_board_thread $thread
 */


?>

<a href="<?= $this->boardthis->getUrl() ?>">Zur Übersicht</a>

<?php if ($this->thread) : ?>
    <h1><?= $this->thread->getTitle() ?>: Antworten</h1>
<?php else : ?>
    <h1>Neues Thema</h1>
<?php endif ?>

<?= $this->form ?>
