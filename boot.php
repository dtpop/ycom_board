<?php

rex_extension::register('PACKAGES_INCLUDED',function() {
    if (rex::isFrontend()) {
        
        $output_dir = "media/ycom_board/";
        
        // File wurde hochgeladen ...
        
        if (isset($_FILES["ycom_board_file"])) {
            if (!rex_ycom_auth::getUser()) {
                echo 'Nicht erlaubt';
                return;
            }
            $ret = [];
            $files = [];

            //	This is for custom errors;	
            /* 	$custom_error= array();
              $custom_error['jquery-upload-file-error']="File already exists";
              echo json_encode($custom_error);
              die();
             */
            $error = $_FILES["ycom_board_file"]["error"];
            //You need to handle  both cases
            //If Any browser does not support serializing of multiple files using FormData() 
            if (!is_array($_FILES["ycom_board_file"]["name"])) { //single file
                $fileName = time().'_'.rex_string::normalize($_FILES["ycom_board_file"]["name"],'_','.');
                move_uploaded_file($_FILES["ycom_board_file"]["tmp_name"], $output_dir . $fileName);
                $ret[] = $fileName;
                $files[$fileName]['realname'] = $fileName;
                $files[$fileName]['origname'] = $_FILES["ycom_board_file"]["name"];               
            } else {  //Multiple files, file[]
                $fileCount = count($_FILES["ycom_board_file"]["name"]);
                for ($i = 0; $i < $fileCount; $i++) {
                    $fileName = time().'_'.rex_string::normalize($_FILES["ycom_board_file"]["name"][$i],'_','.');
                    move_uploaded_file($_FILES["ycom_board_file"]["tmp_name"][$i], $output_dir . $fileName);
                    $ret[] = $fileName;
                    $files[$fileName]['realname'] = $fileName;
                    $files[$fileName]['origname'] = $_FILES["ycom_board_file"]["name"];               
                }
            }
            rex_set_session('ycom_board_file_upload',array_merge(rex_session('ycom_board_file_upload','array'),$files));
//            rex_set_session('ycom_board_file_upload',$files);
            echo json_encode($ret);
            exit;
        }
        
        if (rex_request::isXmlHttpRequest() && rex_request('action') == 'ycom_board_file_delete') {
            $files = rex_session('ycom_board_file_upload','array');
            $deleted = '';
            foreach ($files as $k=>$file) {
                if ($file['origname'] == rex_post('name')) {
                    $filePath = $output_dir. $file['realname'];
                    if (file_exists($filePath)) {
                        unlink($filePath);
                        $deleted = $file['origname'];
                    }                    
                    unset($files[$k]);                    
                }
            }
            rex_set_session('ycom_board_file_upload',$files);            
            echo "Deleted File ".$deleted."<br>";
            exit;
        }
        
        if (rex_request::isXmlHttpRequest() && rex_request('action') == 'ycom_board_form_load') {
            $dir="media/ycom_board";
            $files = rex_session('ycom_board_file_upload','array');
            $ret= array();
            foreach($files as $k=>$file)
            {
                if($file['realname'] == "." || $file['realname'] == "..")
                        continue;
//                $filename = substr($file,strpos($file,'_')+1);
                $filename = $file['realname'];
                $filePath=$dir."/".$file['realname'];
                $details = array();
                $details['name']=$file['origname'];
                $details['path']='/'.$filePath;
                if (file_exists($filePath)) {
                    $details['size']=filesize($filePath);
                    $ret[] = $details;
                } else {
                    unset($files[$k]);
                }
            }
            rex_set_session('ycom_board_file_upload',$files);
            echo json_encode($ret);
            exit;            
        }
        
        rex_extension::register('REX_YFORM_SAVED',function($ep) {
            $params = $ep->getParams();
            if ($params['table'] == rex::getTable('ycom_board_post') && $params['action'] == 'insert' && $params['yform'] == true) {
                $id = $params['id'];
                $sql = rex_sql::factory();
//                $sql->setDebug();
                $sql->setTable(rex::getTable('ycom_board_post'));
                $sql->setValue('attachment',json_encode(rex_session('ycom_board_file_upload','array')));
                $sql->setWhere('id = :id',['id'=>$id]);
                $sql->update();
                rex_set_session('ycom_board_file_upload',[]);
//                exit;
            }            
        });
        
//        rex_extension::registerPoint(new rex_extension_point('YFORM_DATA_ADD', $yform, ['table' => $this->getTable(), 'data' => $this]));        
        
        
        
    }
});