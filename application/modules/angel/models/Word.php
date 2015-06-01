<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/5/28 0028
 * Time: 下午 10:34
 */

class  Angel_Model_Word extends Angel_Model_AbstractModel {
    protected $_document_class = '\Documents\Word';

    public function addWord($meet_id, $text, $users) {
        $data = array('meet_id' => $meet_id,
            'text' => $text,
            'users' => $users);

        $result = $this->add($data);

        return $result;
    }

    public function getLastWordByMeetId($meet_id) {

    }
} 