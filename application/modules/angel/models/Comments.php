<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of CaptionsText
 *
 * @author deanlu
 */
class Angel_Model_Comments  extends Angel_Model_AbstractModel {
    protected $_document_class = '\Documents\Comments';
    
    public function getCommentsPath($photoname) {
        return APPLICATION_PATH . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'public' . $this->_bootstrap_options['image']['comments_path'] . DIRECTORY_SEPARATOR . $photoname;
    }
    
    public function getSaveImagePath() {
        return APPLICATION_PATH . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'public' . $this->_bootstrap_options['image']['photo_path'] . '/';
    }
    
    public function photoHandle($image) {
        $result = false;
        
        $imageService = $this->_container->get('image');
                
        $extension = $imageService->getImageTypeExtension($image);
        $utilService = $this->_container->get('util');
        $filename = $utilService->generateFilename($extension);
        $destination = $this->getCommentsPath($filename);
        $sizes = getimagesize($image);                  

        if (copy($image, $destination)) {
            $generated = true;
            $scale = 1.0;

            if ($sizes[0] > 600) {
                $width = $sizes[0];
                $scale = floatval(600) / floatval($width);
            }

            if ($sizes[1] > 600) {
                $tmp_scale = 600 / floatval($sizes[1]);

                if ($scale > $tmp_scale)
                    $scale = $tmp_scale;
            }

            $generated = $imageService->resizeImage($destination, $sizes[0] * $scale, $sizes[1] * $scale);

            $result = $this->_bootstrap_options['image']['comments_path'] . '/'. $filename;
        }
        
        return $result;
    }
    
    public function addComments($image, $text, $program_id, $time_at, $user, $type) {
        $data = array("image" => $image, "text" => $text, "program_id" => $program_id, "time_at" => $time_at,  "up"=> 0, "user"=>$user, "type"=>$type, "hot"=>0);
        
        $result = $this->add($data);

        return $result;
    }
    
    public function getHotCommentsByProgramId($program_id) {
        $query = $this->_dm->createQueryBuilder($this->_document_class)->field('program_id')->equals($program_id)->field('hot')->equals(0)->sort('time_at', 1);

        $result = $query
                ->getQuery()
                ->execute();

        return $result;
    }
    
    public function setHot($id) {
        $result = false;

        try {
            $comments = $this->getById($id);

            $data = array("image" => $comments->image, "text" => $comments->text, "program_id" => $comments->program_id, "time_at" => $comments->time_at, "up" => $comments->up, "user" => $comments->user, "type" => $comments->type, "hot" => 1);

            $this->save($id, $data);

            $result = true;
        } catch (Exception $e) {
            $this->_logger->info(__CLASS__, __FUNCTION__, $e->getMessage() . "\n" . $e->getTraceAsString());
            throw new Angel_Exception_User(Angel_Exception_User::ADD_USER_FAIL);
        }

        return $result;
    } 
    
    public function unsetHot($id) {
        $result = false;

        try {
            $comments = $this->getById($id);

            $data = array("text" => $comments->text, "program_id" => $comments->program_id, "time_at" => $comments->time_at, "up" => $comments->up, "user" => $comments->user, "type" => $comments->type, "hot" => 0);

            $this->save($id, $data);

            $result = true;
        } catch (Exception $e) {
            $this->_logger->info(__CLASS__, __FUNCTION__, $e->getMessage() . "\n" . $e->getTraceAsString());
            throw new Angel_Exception_User(Angel_Exception_User::ADD_USER_FAIL);
        }

        return $result;
    }
    
//   public function getCommentsByUpUserId($id, $user_id) {
//       $query = $this->_dm->createQueryBuilder($this->_document_class)
//                ->field('id')->equals($id)->field('up_users.$id')->equals(new MongoId($user_id))->sort('created_at', -1);//
//
//        $result = $query
//                ->getQuery()
//                ->getSingleResult();
//
//        if (!empty($result))
//            return false;
//        
//        return true;
//   }
    
    public function up($id, $user) {
        $result = false;

        try {
            $comments = $this->getById($id);
            
            $users = array();
            
            foreach ($comments->up_users as $u) {
                $users[]  = $u;
            }
            
            $users[] = $user;

            $data = array("text" => $comments->text, "program_id" => $comments->program_id, "time_at" => $comments->time_at, "up" => $comments->up + 1, "up_users"=> $users, "user" => $comments->user, "type" => $comments->type, "hot" => $comments->hot);

            $this->save($id, $data);

            $result = true;
        } catch (Exception $e) {
            $this->_logger->info(__CLASS__, __FUNCTION__, $e->getMessage() . "\n" . $e->getTraceAsString());
            throw new Angel_Exception_User(Angel_Exception_User::ADD_USER_FAIL);
        }

        return $result;
    }
}
