<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/5/27 0027
 * Time: 下午 11:13
 */

class Angel_Model_Meet extends Angel_Model_AbstractModel {
    protected $_document_class = '\Documents\Meet';

    //新增聚会信息
    public function addMeet($options_date, $selected_date, $time_range, $meet_text, $remark, $address, $proposer_id, $users_id, $year, $month, $day, $status = 1, $identity = 1) {
        $data = array('options_date' => $options_date,
            'selected_date' => $selected_date,
//            'time_range' => $time_range,
            'meet_text' => $meet_text,
            'identity' => $identity,
            'remark' => $remark,
            'address' => $address,
            'status' => $status,
            'year' => $year,
            'month' => $month,
            'day' => $day,
            'proposer_id' => $proposer_id,
            'users_id' => $users_id);

        $result = $this->add($data);

        return $result;
    }

    //修改聚会信息
    public function saveMeet($id, $options_date, $selected_date, $selected_date, $time_range, $meet_text, $remark, $address, $proposer_id, $users_id, $words, $year, $month, $day, $status = 1, $identity = 1) {
        $data = array('options_date' => $options_date,
            'selected_date' => $selected_date,
//            'time_range' => $time_range,
            'selected_date' => $selected_date,
            'meet_text' => $meet_text,
            'identity' => $identity,
            'remark' => $remark,
            'address' => $address,
            'status' => $status,
            'year' => $year,
            'month' => $month,
            'day' => $day,
            'proposer_id' => $proposer_id,
            'users_id' => $users_id,
            'words' => $words);

        $result = $this->save($id, $data);

        return $result;
    }

    //获取某年某月日程安排集合
    public function getSchedule($user_id, $year, $month) {
        $query = $this->_dm->createQueryBuilder($this->_document_class)->field('year')->equals($year)->field('month')->equals($month)->field('proposer_id')->equals($user_id)->field('status')->equals('1')->sort("created_at", -1);//->field('year')->equals($year)->field('month')->equals($month)->field('proposer_id')->equals($user_id)->field('status')->equals('1')

        $result = $query->getQuery()->execute();

        return $result;
    }

    //获取某天日程安排集合
    public function getScheduleByDate($user_id, $date) {
        $query = $this->_dm->createQueryBuilder($this->_document_class)->field('selected_date')->equals($date)->field('proposer_id')->equals($user_id)->field('status')->equals(1);

        $result = $query ->getQuery()->execute();

        return $result;
    }
} 