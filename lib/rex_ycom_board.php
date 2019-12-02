<?php

class rex_ycom_board
{
    private $name = '';
    private $key = '';
    private $url = '';

    /** @var rex_pager */
    private $pager;
    private $threadsPerPage = 10;
    private $postsPerPage = 10;
    private $notificationTemplate;
    private $adminGroup;

    public function rex_ycom_board($key, $name = '')
    {
        $this->setKey($key);
        $this->setName($name);
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getKey()
    {
        return $this->key;
    }

    public function setKey($key)
    {
        $this->key = $key;
    }

    public function setUrl($url)
    {
        $this->url = $url;
    }

    public function getPager()
    {
        return $this->pager;
    }

    public function setThreadsPerPage($threadsPerPage)
    {
        $this->threadsPerPage = $threadsPerPage;
    }

    public function setPostsPerPage($postsPerPage)
    {
        $this->postsPerPage = $postsPerPage;
    }

    public function setNotificationTemplate($notificationTemplate)
    {
        $this->notificationTemplate = $notificationTemplate;
    }

    public function setAdminGroup($groupId)
    {
        $this->adminGroup = $groupId;
    }

    public function isBoardAdmin(rex_ycom_user $user = null)
    {
        $user = $user ?: rex_ycom_auth::getUser();

        if (!$user || !$this->adminGroup) {
            return false;
        }

        $groups = explode(',', $user->getValue('rex_ycom_group'));
        return in_array($this->adminGroup, $groups);
    }

    public function getUrl(array $params = array())
    {
        $url = $this->url;

        if ($params) {
            $url .= false === strpos($url, '?') ? '?' : '&amp;';
            $url .= http_build_query($params, null, '&amp;');
        }

        return $url;
    }

    public function getCurrentUrl(array $params = array())
    {
        $defaultParams = array(
            'function' => rex_get('function', 'string'),
            'thread' => rex_get('thread', 'int'),
            'start' => rex_get('start', 'int'),
        );

        $params = array_merge($defaultParams, $params);
        $params = array_filter($params);

        return $this->getUrl($params);
    }

    public function getPostUrl(rex_ycom_board_post $post)
    {
        return $this->getUrl(array('thread' => $post->getThreadId(), 'post' => $post->getId())) . '#' . $this->getPostIdAttribute($post);
    }

    public function getPostIdAttribute($post)
    {
        if ($post instanceof rex_ycom_board_post) {
            $post = $post->getId();
        }

        return sprintf('board-%s-post-%d', $this->getKey(), $post);
    }

    public function getPostDeleteUrl(rex_ycom_board_post $post)
    {
        return $this->getCurrentUrl(array(
            'post' => $post->getId(),
            'function' => 'delete',
        ));
    }

    public function getAttachmentUrl(rex_ycom_board_post $post)
    {
        return $this->getUrl(array('thread' => $post->getThreadId(), 'post' => $post->getId(), 'function' => 'attachment_download'));
    }

    /**
     * @return rex_ycom_board_thread[]
     */
    public function getThreads()
    {
        $this->pager = new rex_pager($this->threadsPerPage);

        return $this->getPosts(
            'thread_id = ""',
            '(SELECT MAX(created) FROM rex_ycom_board_post p2 WHERE (p2.thread_id = p.id OR p2.id = p.id) AND status = 1) DESC'
        );
    }

    /**
     * @param rex_ycom_board_thread $thread
     * @param int                  $findPost
     *
     * @return rex_ycom_board_post[]
     */
    public function getThreadPosts(rex_ycom_board_thread $thread, rex_ycom_board_post $findPost = null)
    {
        $this->pager = new rex_pager($this->postsPerPage);

        return $this->getPosts(
            sprintf('thread_id = %d OR id = %1$d', $thread->getId()),
            'created ASC',
            $findPost
        );
    }

    public function getPosts($where = '', $order = 'created ASC', rex_ycom_board_post $findPost = null)
    {
        $db = rex_sql::factory();
//        $db->setDebug();

        $where = sprintf(' WHERE board_key = "%s" AND status = 1 AND (%s)', $this->getKey(), $where);
        
        $db->setQuery('SELECT COUNT(*) as count FROM rex_ycom_board_post p '.$where);
        $count = $db->getValue('count');

        $this->pager->setRowCount($count);

        $order = 'ORDER BY '.$order;

        if ($findPost) {
            $db->setQuery('SELECT COUNT(*) as count FROM rex_ycom_board_post p '.$where.' AND created < (SELECT created FROM rex_ycom_board_post WHERE id = '.((int) $findPost->getId()).')'.$order);
            if ($db->getRows()) {
                $lessCount = $db->getValue('count');

                $cursor = ((int) ($lessCount / $this->pager->getRowsPerPage())) * $this->pager->getRowsPerPage();
                $cursor = $this->pager->validateCursor($cursor);
                $_REQUEST[$this->pager->getCursorName()] = $cursor;
            }
        }

        $limit = ' LIMIT '.$this->pager->getCursor().', '.$this->pager->getRowsPerPage();

        $posts_sql = $db->getArray('select * from rex_ycom_board_post p '.$where.$order.$limit);
        
        $posts = array();
        foreach ($posts_sql as $data) {
            if ($data['thread_id']) {
                $posts[] = new rex_ycom_board_post($data);
            } else {
                $posts[] = new rex_ycom_board_thread($data);
            }
        }
        return $posts;
    }

    /**
     * @param int $id
     * @return null|rex_ycom_board_post|rex_ycom_board_thread
     */
    public function getPost($id)
    {
        $sql = rex_sql::factory();
        $sql->setQuery('SELECT * FROM rex_ycom_board_post WHERE status = 1 and id = ' . (int) $id);

        if (!$sql->getRows()) {
            return null;
        }

        $data = $sql->getRow();
        if ($data['thread_id']) {
            return new rex_ycom_board_post($data);
        }

        return new rex_ycom_board_thread($data);
    }

    public function getView()
    {
        $thread = rex_get('thread', 'int');
        $function = rex_get('function', 'string');

        if ($thread) {
            $thread = rex_ycom_board_thread::get($thread);
        }

        if (!$thread) {
            if ('create_thread' === $function) {
                $yform = $this->getForm();
                $form = $yform->getForm();

                if ($yform->getObjectparams('actions_executed')) {
                    $thread = rex_ycom_board_thread::get($yform->getObjectparams('main_id'));

                    if (isset($yform->objparams['value_pool']['email']['notifications']) && $yform->objparams['value_pool']['email']['notifications']) {
                        $thread->addNotificationUser(rex_ycom_auth::getUser());
                    }

                    if ($thread && $thread->getId()) {
                        header('Location: ' . htmlspecialchars_decode($this->getUrl(array('thread' => $thread->getId()))));
                        exit;
                    }
                }

                return $this->render('thread.create.tpl.php', compact('form'));
            }

            $threads = $this->getThreads();
  //          dump($threads);
            return $this->render('threads.tpl.php', compact('threads'));
        }

        if ('enable_notifications' === $function) {
            $thread->addNotificationUser(rex_ycom_auth::getUser());
        }
        if ('disable_notifications' === $function) {
            $thread->removeNotificationUser(rex_ycom_auth::getUser());
        }

        if ('create_post' === $function) {
            $yform = $this->getForm();
            $yform->setValueField('hidden', array('thread_id', 'thread','REQUEST'));
            $yform->setValueField('objparams', array('value.title.default', 'Re: ' . $thread->getTitle(), ''));

            $form = $yform->getForm();

            if ($yform->getObjectparams('actions_executed')) {
                $post = rex_ycom_board_post::get($yform->getObjectparams('main_id'));

                $this->sendNotifications($thread, $post);

                if (isset($yform->objparams['value_pool']['email']['notifications']) && $yform->objparams['value_pool']['email']['notifications']) {
                    $thread->addNotificationUser(rex_ycom_auth::getUser());
                }

                header('Location: ' . htmlspecialchars_decode($this->getPostUrl($post)));
                exit;
            }

            return $this->render('post.create.tpl.php', compact('thread', 'form'));
        }

        if ('delete' === $function && $this->isBoardAdmin() && ($id = rex_get('post', 'int')) && $post = $this->getPost($id)) {
            $this->deletePost($post);

            $params = array();
            if (!$post instanceof rex_ycom_board_thread) {
                $params = array(
                    'thread' => rex_get('thread', 'int'),
                    'start' => rex_get('start', 'int'),
                );
            }
            header('Location: ' . htmlspecialchars_decode($this->getUrl($params)));
            exit;
        }

        $post = null;
        if ($postId = rex_get('post', 'int')) {
            $post = rex_ycom_board_post::get($postId);
        }

        if ($post && 'attachment_download' === $function) {
            $this->sendAttachment($post);
            exit;
        }

        
        $posts = $this->getThreadPosts($thread, $post);
  //      dump($posts);
        return $this->render('posts.tpl.php', compact('thread', 'posts'));
    }

    private function deletePost(rex_ycom_board_post $post)
    {
        $sql = rex_sql::factory();

        if (!$post instanceof rex_ycom_board_thread) {
            if ($post->hasAttachment()) {
                $file = rex_path::pluginData('community', 'board', 'attachments/'.$post->getRealAttachment());
                rex_file::delete($file);
            }

            $sql->setQuery('DELETE FROM rex_ycom_board_post WHERE id = '.(int) $post->getId());
            return;
        }

        $where = 'id = '.(int) $post->getId().' OR thread_id = '.(int) $post->getId();
        $attachments = $sql->getArray('SELECT attachment FROM rex_ycom_board_post WHERE attachment != "" AND ('.$where.')');
        foreach ($attachments as $attachment) {
            $file = rex_path::pluginData('community', 'board', 'attachments/'.$attachment['attachment']);
            rex_file::delete($file);
        }

        $sql->setQuery('DELETE FROM rex_ycom_board_post WHERE '.$where);
    }

    private function getForm()
    {
        $yform = new rex_yform();
        $yform->setObjectparams('real_field_names', true);
        $yform->setObjectparams('form_action', $this->getCurrentUrl());

        $yform->setValueField('hidden', array('board_key', $this->getKey()));
        $yform->setValueField('hidden', array('user_id', rex_ycom_auth::getUser()->id));
        $yform->setValueField('hidden', array('status', 1));

        $yform->setValueField('text', array('title', 'translate:com_board_title'));
        $yform->setValidateField('empty', array('title', 'translate:com_board_enter_title'));
        $yform->setValueField('textarea', array('message', 'translate:com_board_message'));
        $yform->setValidateField('empty', array('message', 'translate:com_board_enter_message'));
        $yform->setValueField('upload', array('attachment','translate:com_board_attachment','0,10000','.gif,.jpg,.jpeg,.png,.pdf','0',',translate:com_board_attachment_error_max_size,translate:com_board_attachment_error_type,,translate:com_board_attachment_delete','upload','rex_ycom_board_post'));
        
        $yform->setValueField('checkbox', array('notifications', 'translate:com_board_notifications', 'no_db' => 'no_db'));

        $yform->setValueField('datestamp', array('created', 'Created', 'mysql', '[no_db]','1'));
        $yform->setValueField('datestamp', array('updated', 'Updated', 'mysql', '[no_db]','0'));

        $yform->setActionField('db', array('rex_ycom_board_post'));

        return $yform;
    }

    private function sendNotifications(rex_ycom_board_thread $thread, rex_ycom_board_post $post)
    {
        if (!$this->notificationTemplate) {
            return;
        }

        $template = $this->notificationTemplate;
        $userIds = $thread->getNotificationUsers();

        foreach ($userIds as $id) {
            if ($id == rex_ycom_auth::getUser()->id) {
                continue;
            }
            $user = rex_ycom_board_post::getUserById($id);
            $yf = new rex_yform();
            $yf->setObjectparams('csrf_protection',false);
            $yf->setValueField('hidden', ['username',$user->firstname . ' ' . $user->name]);
            $yf->setValueField('hidden', ['post_url',htmlspecialchars_decode($this->getPostUrl($post))]);
            $yf->setValueField('hidden', ['thread_title',$thread->getTitle()]);
            
            // und weitere Felder ...
            $yf->setValueField('hidden', ['email',$user->email]);
            $yf->setActionField('tpl2email', [$template,"email",$user->email]);
            $yf->getForm();
            $yf->setObjectparams('send',1);
            $yf->executeActions();
        }
        
    }

    private function sendAttachment(rex_ycom_board_post $post)
    {
        while (ob_get_level()) {
            ob_end_clean();
        }

        $file = rex_path::pluginData('yform', 'manager/upload/frontend', $post->getId().'_'.$post->getRealAttachment());

        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename='.$post->getAttachment());
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($file));
        readfile($file);
        exit;
    }

    public function render($template, array $params = array())
    {
        extract($params);

        ob_start();
        include $this->findTemplate($template);
        return ob_get_clean();
    }

    private function findTemplate($template)
    {
        $paths[] = rex_path::pluginData('ycom', 'board', 'templates/' . $template);
        $paths[] = rex_path::plugin('ycom', 'board', 'templates/' . $template);

        foreach ($paths as $path) {
            if (file_exists($path)) {
                return $path;
            }
        }

        throw new \RuntimeException(sprintf('Template "%s" not found', $template));
    }
}
