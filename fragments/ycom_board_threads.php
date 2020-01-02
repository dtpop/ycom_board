<?php
    /**
     * @var rex_com_board $this
     * @var rex_com_board_thread[] $threads
     */
     $boardthis = $this->boardthis;
     $threads = $this->threads;

?>

<?php if (rex_ycom_auth::getUser()) : ?>
<p>
    <a href="<?= $boardthis->getUrl(array('function' => 'create_thread')) ?>">Neues Thema</a>
</p>
<?php endif ?>

<?php
$fragment = new rex_fragment();
$fragment->setVar('pager',$boardthis->getPager());
$fragment->setVar('boardthis',$boardthis);
echo $fragment->parse('ycom_board_pagination.php');
?>

<table style="width: 100%">
    <thead>
        <tr>
            <th>Themen</th>
            <th>Antworten</th>
            <th>Letzter Beitrag</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($threads as $thread): ?>
            <tr>
                <td>
                    <b><a href="<?= $boardthis->getUrl(array('thread' => $thread->getId())) ?>"><?= htmlspecialchars($thread->getTitle()) ?></a></b><br>
                    von <?= htmlspecialchars($thread->getUserFullName()) ?> am <?= $thread->getCreated('%d.%m.%Y, %H:%M Uhr') ?>
                </td>
                <td><?= $thread->countReplies() ?></td>
                <td>
                    <?php if ($thread->countReplies()) : ?>
                    <a href="<?= $boardthis->getPostUrl($thread->getRecentPost()) ?>">
                        von <?= $thread->getRecentPost()->getUserFullName() ?><br>
                        am <?= $thread->getRecentPost()->getCreated('%d.%m.%Y, %H:%M Uhr') ?>
                    </a>
                    <?php endif ?>
                </td>
            </tr>
        <?php endforeach ?>
    </tbody>
</table>

<?php echo $fragment->parse('ycom_board_pagination.php'); ?>

