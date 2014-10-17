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
    
    public function addComments($text, $program_id, $time_at, $release_date, $user, $type) {
        $data = array("text" => $text, "program_id" => $program_id, "time_at" => $time_at,  "up"=> 0, "user"=>$user, "type"=>$type, "hot"=>0);
        
        $result = $this->add($data);
        
        return $result;
    }
    
//    public function getCommentsByProgramId($program_id) {
//        $query = $this->_dm->createQueryBuilder($this->_document_class)->field('program_id')->equals($program_id)->sort('time_at', 1);
//
//        $result = $query
//                ->getQuery()
//                ->execute();
//
//        return $result;
//    }
    
    public function getHotCommentsByProgramId($program_id) {
        $query = $this->_dm->createQueryBuilder($this->_document_class)->field('program_id')->equals($program_id)->field('hot')->equals(1)->sort('time_at', 1);

        $result = $query
                ->getQuery()
                ->execute();

        return $result;
    }
}
