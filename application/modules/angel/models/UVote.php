<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/6/6 0006
 * Time: 下午 5:15
 */

class Angel_Model_UVote extends Angel_Model_AbstractModel {
    protected $_document_class = '\Documents\UVote';

    //记录用户活动日期投票
    public function addUserVote($meet_id, $first_date, $end_date, $user_id) {
        $data = array('meet_id'=> $meet_id,
            'first_date'=> $first_date,
            'end_date'=> $end_date,
            'user_id'=> $user_id);

        $result = $this->add($data);

        return $result;
    }
} 