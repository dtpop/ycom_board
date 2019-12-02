<?php

class rex_ycom_board_post
{
    private $data;
    private $user;

    public function __construct(array $data)
    {
        $this->data = $data;
        foreach ($data as $k=>$v) {
            if (strpos($k,'.')) {
                $_k = explode('.',$k);
                $this->data[$_k[1]] = $v;
            }
        }
    }

    public static function get($id)
    {
        $sql = rex_sql::factory();
        $sql->setQuery('SELECT * FROM rex_ycom_board_post WHERE status = 1 and id = ' . (int) $id);
        if (!$sql->getRows()) {
            return null;
        }
        return new self($sql->getRow());
    }

    public function getId()
    {
        /*
        if (isset ($this->data['rex_ycom_board_post.id'])) {
            return $this->data['rex_ycom_board_post.id'];
        }
         * 
         */
        return $this->data['id'];
    }

    public function getThreadId()
    {
        return $this->data['thread_id'] ?: $this->getId();
    }

    public function getTitle()
    {
        return $this->data['title'];
    }

    public function getMessage()
    {
        return $this->data['message'];
    }

    public function hasAttachment()
    {
        return (bool) $this->data['attachment'];
    }

    public function getAttachment()
    {
        if (!$this->hasAttachment()) {
            return null;
        }

        $parts = explode('_', $this->getRealAttachment(), 2);
        return isset($parts[1]) ? $parts[1] : null;
    }

    public function getRealAttachment()
    {
        return $this->data['attachment'];
    }

    /**
     * @return rex_ycom_user
     */
    public function getUser()
    {
        if (!$this->data['user_id']) {
            return false;
        }
        if (!$this->user) {
            $this->user = rex_yform_manager_table::get('rex_ycom_user')->query()->where('id',$this->data['user_id'])->findOne();
        }

        return $this->user;
    }
    
    public static function getUserById($id = 0) {
        if (!$id) {
            return false;
        }
        return rex_yform_manager_table::get('rex_ycom_user')->query()->where('id',$id)->findOne();
    }
    
    
    public function getUserFullName() {
        if (!$this->data['user_id']) {
            return '';
        }
        if (!$this->user) {
            $this->user = rex_yform_manager_table::get('rex_ycom_user')->query()->where('id',$this->data['user_id'])->findOne();
        }        
        return $this->user->firstname . ' ' . $this->user->name;
        
    }
    

    public function getCreated($format = null)
    {
        $timestamp = strtotime($this->data['created']);
        return $format ? strftime($format, $timestamp) : $timestamp;
    }

    public function getUpdated($format = null)
    {
        $timestamp = strtotime($this->data['updated']);

        return $format ? strftime($format, $timestamp) : $timestamp;
    }
}
