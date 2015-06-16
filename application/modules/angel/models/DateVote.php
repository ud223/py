<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/6/6 0006
 * Time: 下午 2:28
 */

class Angel_Model_DateVote extends Angel_Model_AbstractModel {
    protected $_document_class = '\Documents\DateVote';

    //添加活动日期投票
    public function addDateVote($meet_id, $date, $user_id, $num = 1) {
        $data = array('meet_id' => $meet_id,
            'date' => $date,
            'user_id' => $user_id,
            'num' => $num);

        $result = $this->add($data);

        return $result;
    }

    //保存投票新结果
    public function saveDateVote($id, $meet_id, $date, $user_id, $num) {
        $data = array('meet_id' => $meet_id,
            'date' => $date,
            'user_id' => $user_id,
            'num' => $num);

        $result = $this->save($id, $data);

        return $result;
    }

    //根据活动id获取投票活动日期
    public function getVoteByMeetId($meet_id) {
        $query = $this->_dm->createQueryBuilder($this->_document_class)->field('meet_id')->equals($meet_id)->sort("created_at", -1);

        $result = $query->getQuery();

        return $result;
    }
} 