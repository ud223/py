<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/6/6 0006
 * Time: 下午 2:28
 */

class Angel_Model_Vote extends Angel_Model_AbstractModel {
    protected $_document_class = '\Documents\Vote';

    //添加活动日期投票
    public function addDateVote($meet_id, $date, $num = 1) {
        $data = array('meet_id'=> $meet_id,
            'date'=> $date,
            'num'=> $num);

        $result = $this->add($data);

        return $result;
    }

    //保存投票新结果
    public function saveDateVote($id, $meet_id, $date, $num) {
        $data = array('meet_id'=> $meet_id,
            'date'=> $date,
            'num'=> $num);

        $result = $this->save($id, $data);

        return $result;
    }

    public function getVoteByMeetIdAndDate($meet_id, $date) {
        $query = $this->_dm->createQueryBuilder($this->_document_class)->field('date')->equals($date)->field('meet_id')->equals($meet_id)->sort("created_at", -1);

        $result = $query->getQuery();

        return $result;
    }

    //根据活动id获取投票活动日期
    public function getVoteByMeetId($meet_id) {
        $query = $this->_dm->createQueryBuilder($this->_document_class)->field('meet_id')->equals($meet_id)->sort("created_at", -1);

        $result = $query->getQuery();

        return $result;
    }
} 