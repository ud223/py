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
    
    public function addComments($text, $program_id, $time_at, $user, $type) {
        $data = array("text" => $text, "program_id" => $program_id, "time_at" => $time_at,  "up"=> 0, "user"=>$user, "type"=>$type, "hot"=>0);
        
        $result = $this->add($data);
        
        return $result;
    }
    
    public function getHotCommentsByProgramId($program_id) {
        $query = $this->_dm->createQueryBuilder($this->_document_class)->field('program_id')->equals($program_id)->field('hot')->equals(1)->sort('time_at', 1);

        $result = $query
                ->getQuery()
                ->execute();

        return $result;
    }
    
    public function setHot($id) {
        $result = false;

        try {
            $comments = $this->getById($id);

            $data = array("text" => $comments->text, "program_id" => $comments->program_id, "time_at" => $comments->time_at, "up" => $comments->up, "user" => $comments->user, "type" => $comments->type, "hot" => 1);

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

            $data = array("text" => $comments->text, "program_id" => $comments->program_id, "time_at" => $comments->time_at, "up" => $comments->up + 1, "up_users"=> $users, "user" => $comments->user, "type" => $comments->type, "hot" => 0);

            $this->save($id, $data);

            $result = true;
        } catch (Exception $e) {
            $this->_logger->info(__CLASS__, __FUNCTION__, $e->getMessage() . "\n" . $e->getTraceAsString());
            throw new Angel_Exception_User(Angel_Exception_User::ADD_USER_FAIL);
        }

        return $result;
    }
}
