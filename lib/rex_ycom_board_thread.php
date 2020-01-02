<?php

class rex_ycom_board_thread extends rex_ycom_board_post
{
    private $countReplies;
    private $recentPost;
    private $notificationUsers;

    public static function get($id)
    {
        $sql = rex_sql::factory();
        $sql->setQuery('SELECT * FROM rex_ycom_board_post WHERE status = 1 and thread_id = "" and id = ' . (int) $id);
        if (!$sql->getRows()) {
            return null;
        }
        return new self($sql->getRow());
    }

    public function countReplies()
    {
        if (null !== $this->countReplies) {
            return $this->countReplies;
        }

        $sql = rex_sql::factory();
        $sql->setQuery('SELECT COUNT(*) as count FROM rex_ycom_board_post WHERE status = 1 and thread_id = ' . (int) $this->getId());
        return $this->countReplies = (int) $sql->getValue('count');
    }

    public function getRecentPost()
    {
        if (null !== $this->recentPost) {
            return $this->recentPost;
        }

        $sql = rex_sql::factory();
//        $sql->setDebug();
        $sql->setQuery(sprintf('SELECT * FROM rex_ycom_board_post WHERE status = 1 AND (thread_id = %d OR id = %1$d) ORDER BY created DESC LIMIT 1', (int) $this->getId()));
        return $this->recentPost = new rex_ycom_board_post($sql->getRow());
    }

    public function getNotificationUsers()
    {
        if (null !== $this->notificationUsers) {
            return $this->notificationUsers;
        }

        $sql = rex_sql::factory();
        $data = $sql->getArray(sprintf('SELECT user_id FROM rex_ycom_board_thread_notification WHERE thread_id = %d', $this->getId()));
        $this->notificationUsers = array();
        foreach ($data as $row) {
            $this->notificationUsers[] = $row['user_id'];
        }
        return $this->notificationUsers;
    }

    public function addNotificationUser(rex_ycom_user $user)
    {
        if (in_array($user->id, $this->getNotificationUsers())) {
            return;
        }
        
        $sql = rex_sql::factory();
        $sql->setTable('rex_ycom_board_thread_notification');
        $sql->setValue('thread_id', $this->getId());
        $sql->setValue('user_id', $user->id);
        $sql->insert();

        $this->notificationUsers[] = $user->id;
    }

    public function removeNotificationUser(rex_ycom_user $user)
    {
        $sql = rex_sql::factory();
        $sql->setTable('rex_ycom_board_thread_notification');
        $sql->setWhere(sprintf('thread_id = %d AND user_id = %d', $this->getId(), $user->id));
        $sql->delete();

        $this->notificationUsers = null;
    }

    public function isNotificationEnabled($user)
    {
        if (!$user) {
            return false;
        }
        return in_array($user->id, $this->getNotificationUsers());
    }
}
