<?php
    /**
     * @var rex_com_board $this
     * @var rex_com_board_thread[] $threads
     */
?>

<?php if (rex_ycom_auth::getUser()) : ?>
<p>
    <a href="<?= $this->getUrl(array('function' => 'create_thread')) ?>">Neues Thema</a>
</p>
<?php endif ?>

<?= $this->render('pagination.tpl.php') ?>

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
                    <b><a href="<?= $this->getUrl(array('thread' => $thread->getId())) ?>"><?= htmlspecialchars($thread->getTitle()) ?></a></b><br>
                    von <?= htmlspecialchars($thread->getUserFullName()) ?> am <?= $thread->getCreated('%d.%m.%Y, %H:%M Uhr') ?>
                </td>
                <td><?= $thread->countReplies() ?></td>
                <td>
                    <?php if ($thread->countReplies()) : ?>
                    <a href="<?= $this->getPostUrl($thread->getRecentPost()) ?>">
                        von <?= $thread->getRecentPost()->getUserFullName() ?><br>
                        am <?= $thread->getRecentPost()->getCreated('%d.%m.%Y, %H:%M Uhr') ?>
                    </a>
                    <?php endif ?>
                </td>
            </tr>
        <?php endforeach ?>
    </tbody>
</table>

<?= $this->render('pagination.tpl.php') ?>
