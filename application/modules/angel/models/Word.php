<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/5/28 0028
 * Time: 下午 10:34
 */

class  Angel_Model_Word extends Angel_Model_AbstractModel {
    protected $_document_class = '\Documents\Word';

    public function addWord($meet_id, $text, $user_id) {
        $data = array('meet_id' => $meet_id,
            'text' => $text,
            'user_id' => $user_id);

        $result = $this->add($data);

        return $result;
    }

    public function getWordsByMeetId($meet_id) {
        $query = $this->_dm->createQueryBuilder($this->_document_class)->field('meet_id')->equals($meet_id)->sort("created_at", -1);

        $result = $query->getQuery()->execute();

        return $result;
    }
} 