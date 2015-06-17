<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/6/6 0006
 * Time: 下午 5:15
 */

class Angel_Model_Uvote extends Angel_Model_AbstractModel {
    protected $_document_class = '\Documents\Uvote';

    //记录用户活动日期投票
    public function addUserVote($meet_id, $first_date, $second_date, $user_id) {
        $data = array('meet_id'=> $meet_id,
            'first_date'=> $first_date,
            'second_date'=> $second_date,
            'user_id'=> $user_id);

        $result = $this->add($data);

        return $result;
    }

    public function getMeetDateByUserId($meet_id, $user_id) {
        $query = $this->_dm->createQueryBuilder($this->_document_class)->field('user_id')->equals($user_id)->field('meet_id')->equals($meet_id)->sort("created_at", -1);

        $result = $query->getQuery();

        return $result;
    }
} 