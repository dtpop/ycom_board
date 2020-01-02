<?php

/**
 * Description of ycom_board_message
 *
 * @author wolfgang
 */
class ycom_board_message {
    
    public static function send_messages ($thread) {
        
        // E-Mail Template muss definiert sein
        if (!rex_config::get('ycom_board','email_template_new_thread')) {
            return;
        }
        
        $not_groups = explode('|',trim(rex_config::get('ycom_board','groups_no_messages'),'|'));
        $where = [];
        foreach($not_groups as $ng) {
            $where[] = 'NOT FIND_IN_SET("'.$ng.'",`ycom_groups`)';
        } 
        
        $items = rex_yform_manager_table::get(rex::getTable('ycom_user'))
                ->query()
                ->where('status','0','>')
                ->whereRaw(implode(' AND ',$where))
                ->find();
        
        foreach ($items as $item) {
            if (!$item->email) {
                continue;
            }            
            $yform = new rex_yform();
            $yform->setObjectparams('csrf_protection',false);
            $yform->setValueField('hidden', ['firstname',$item->firstname]);
            $yform->setValueField('hidden', ['name',$item->name]);
            $yform->setValueField('hidden', ['email',$item->email]);
            $yform->setValueField('hidden', ['url',rex_getUrl()]);
            $yform->setValueField('hidden', ['title',$thread->getTitle()]);
            $yform->setValueField('hidden', ['message',$thread->getMessage()]);            
            $yform->setActionField('tpl2email', [rex_config::get('ycom_board','email_template_new_thread'),"email",$item->email]);
            $yform->getForm();
            $yform->setObjectparams('send',1);
            $yform->executeActions();        
        }
        
        
        
        
        /*
            "rex_ycom_board_post.id" => "7"
            "rex_ycom_board_post.board_key" => "aid-57"
            "rex_ycom_board_post.thread_id" => ""
            "rex_ycom_board_post.title" => "asdfasdfasdfa"
            "rex_ycom_board_post.message" => "safdasfdasfdsafd"
            "rex_ycom_board_post.user_id" => "5"
            "rex_ycom_board_post.status" => "1"
            "rex_ycom_board_post.created" => "2019-12-04 18:00:10"
            "rex_ycom_board_post.updated" => "2019-12-04 18:00:10"
            "rex_ycom_board_post.notifications" => ""
            "rex_ycom_board_post.attachment" => ""
            "id" => "7"
            "board_key" => "aid-57"
            "thread_id" => ""
            "title" => "asdfasdfasdfa"
            "message" => "safdasfdasfdsafd"
            "user_id" => "5"
            "status" => "1"
            "created" => "2019-12-04 18:00:10"
            "updated" => "2019-12-04 18:00:10"
            "notifications" => ""
            "attachment" => ""
        */

        
        
        
    }
    
    
}
