<?php

class Angel_Model_Vote extends Angel_Model_AbstractModel {
    public  function addVote($keywordId, $score) {
        $data = array("keywordId" => $keywordId, "score" => $score);
        
        $result = $this->add($data);
        
        return $result;
    }
    
    public function saveVote($id, $keywordId, $score) {
        $data = array("keywordId" => $keywordId, "score" => $score);
        
        $result = $this->save($id, $data);
        
        return $result;
    }
    
    public function getByKeywordId($keyword_id) {
        $result = false;
        
        $vote = $this->_dm->createQueryBuilder($this->_document_class)
                ->field('keywordId')->equals($keyword_id)
                ->getQuery()
                ->getSingleResult();

        if (!empty($version)) {
            $result = $vote;
        }
        
        return $result;
    }
}
