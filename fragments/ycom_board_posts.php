<?php
    /**
     * @var rex_ycom_board $this
     * @var rex_ycom_board_thread $thread
     * @var rex_ycom_board_post[] $posts
     */

     $thread = $this->thread;
     $posts = $this->posts;
     $boardthis = $this->boardthis;
?>

<a href="<?= $boardthis->getUrl() ?>">Zur Übersicht</a>

<h1><?= $thread->getTitle() ?></h1>

<?php if (rex_ycom_auth::getUser()) : ?>
    <p>
        <a href="<?= $boardthis->getCurrentUrl(array('function' => 'create_post')) ?>">Antworten</a>
        <?php if ($thread->isNotificationEnabled(rex_ycom_auth::getUser())): ?>
            <a href="<?= $boardthis->getCurrentUrl(['function' => 'disable_notifications']) ?>">Benachrichtigungen ausschalten</a>
        <?php else: ?>
            <a href="<?= $boardthis->getCurrentUrl(['function' => 'enable_notifications']) ?>">Benachrichtigungen einschalten</a>
        <?php endif ?>
    </p>
<?php endif ?>

<?php
$fragment = new rex_fragment();
$fragment->setVar('pager',$boardthis->getPager());
echo $fragment->parse('ycom_board_pagination.php');
?>
    
<?php // $boardthis->render('pagination.tpl.php') ?>

<table style="width: 100%">
    <tbody>
        <?php foreach($posts as $post): ?>
            <tr id="<?= $boardthis->getPostIdAttribute($post) ?>">
                <td>
                    <b><?= $post->getUserFullName() ?></b><br><br>
                    <a href="<?= $boardthis->getPostUrl($post) ?>"><?= $post->getCreated('%d.%m.%Y, %H:%M Uhr')?></a>
                    <?php if ($boardthis->isBoardAdmin()): ?>
                        <br><br>
                        <?php if ($post instanceof rex_ycom_board_thread): ?>
                            <a href="<?= $boardthis->getPostDeleteUrl($post) ?>" onclick="return confirm('Soll der gesamte Thread wirklich gelöscht werden?')">Thread löschen</a>
                        <?php else: ?>
                            <a href="<?= $boardthis->getPostDeleteUrl($post) ?>" onclick="return confirm('Soll der Beitrag wirklich gelöscht werden?')">Beitrag löschen</a>
                        <?php endif ?>
                    <?php endif ?>
                </td>
                <td>
                    <h2><?= htmlspecialchars($post->getTitle()) ?></h2>
                    <p><?= nl2br(htmlspecialchars($post->getMessage())) ?></p>
                    <?php if ($post->hasAttachment()): ?>
                        <h4>Anhang:</h4>
                        <?php $files = json_decode($post->getRealAttachment(),true) ?>
                        <?php foreach ($files as $file) : ?>
                            <figure>
                                <img src="/media/ycom_board/<?= $file['realname'] ?>">
                                <figcaption><a href="/media/ycom_board_full_size/<?= $file['realname'] ?>">Download</a></figcaption>
                            </figure>
                        <?php endforeach ?>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach ?>
    </tbody>
</table>

    
<?php echo $fragment->parse('ycom_board_pagination.php'); ?>
    
<?php // $boardthis->render('pagination.tpl.php') ?>

<?php if (rex_ycom_auth::getUser()) : ?>
<p>
    <a href="<?= $boardthis->getCurrentUrl(array('function' => 'create_post')) ?>">Antworten</a>
    <?php if ($thread->isNotificationEnabled(rex_ycom_auth::getUser())): ?>
        <a href="<?= $boardthis->getCurrentUrl(['function' => 'disable_notifications']) ?>">Benachrichtigungen ausschalten</a>
    <?php else: ?>
        <a href="<?= $boardthis->getCurrentUrl(['function' => 'enable_notifications']) ?>">Benachrichtigungen einschalten</a>
    <?php endif ?>
</p>
<?php endif ?>