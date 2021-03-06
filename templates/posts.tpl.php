<?php
    /**
     * @var rex_ycom_board $this
     * @var rex_ycom_board_thread $thread
     * @var rex_ycom_board_post[] $posts
     */
?>

<a href="<?= $this->getUrl() ?>">Zur Übersicht</a>

<h1><?= $thread->getTitle() ?></h1>

<?php if (rex_ycom_auth::getUser()) : ?>
    <p>
        <a href="<?= $this->getCurrentUrl(array('function' => 'create_post')) ?>">Antworten</a>
        <?php if ($thread->isNotificationEnabled(rex_ycom_auth::getUser())): ?>
            <a href="<?= $this->getCurrentUrl(['function' => 'disable_notifications']) ?>">Benachrichtigungen ausschalten</a>
        <?php else: ?>
            <a href="<?= $this->getCurrentUrl(['function' => 'enable_notifications']) ?>">Benachrichtigungen einschalten</a>
        <?php endif ?>
    </p>
<?php endif ?>

<?= $this->render('pagination.tpl.php') ?>

<table style="width: 100%">
    <tbody>
        <?php foreach($posts as $post): ?>
            <tr id="<?= $this->getPostIdAttribute($post) ?>">
                <td>
                    <b><?= $post->getUserFullName() ?></b><br><br>
                    <a href="<?= $this->getPostUrl($post) ?>"><?= $post->getCreated('%d.%m.%Y, %H:%M Uhr')?></a>
                    <?php if ($this->isBoardAdmin()): ?>
                        <br><br>
                        <?php if ($post instanceof rex_ycom_board_thread): ?>
                            <a href="<?= $this->getPostDeleteUrl($post) ?>" onclick="return confirm('Soll der gesamte Thread wirklich gelöscht werden?')">Thread löschen</a>
                        <?php else: ?>
                            <a href="<?= $this->getPostDeleteUrl($post) ?>" onclick="return confirm('Soll der Beitrag wirklich gelöscht werden?')">Beitrag löschen</a>
                        <?php endif ?>
                    <?php endif ?>
                </td>
                <td>
                    <h2><?= htmlspecialchars($post->getTitle()) ?></h2>
                    <p><?= nl2br(htmlspecialchars($post->getMessage())) ?></p>
                    <?php if ($post->hasAttachment()): ?>
                        <p>
                            Anhang: <a href="<?= $this->getAttachmentUrl($post) ?>"><?= $post->getAttachment() ?></a>
                        </p>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach ?>
    </tbody>
</table>

<?= $this->render('pagination.tpl.php') ?>

<?php if (rex_ycom_auth::getUser()) : ?>
<p>
    <a href="<?= $this->getCurrentUrl(array('function' => 'create_post')) ?>">Antworten</a>
    <?php if ($thread->isNotificationEnabled(rex_ycom_auth::getUser())): ?>
        <a href="<?= $this->getCurrentUrl(['function' => 'disable_notifications']) ?>">Benachrichtigungen ausschalten</a>
    <?php else: ?>
        <a href="<?= $this->getCurrentUrl(['function' => 'enable_notifications']) ?>">Benachrichtigungen einschalten</a>
    <?php endif ?>
</p>
<?php endif ?>