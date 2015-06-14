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
//        $data = array('options_date' => $options_date,
//            'selected_date' => $selected_date,
////            'time_range' => $time_range,
//            'meet_text' => $meet_text,
//            'identity' => $identity,
//            'remark' => $remark,
//            'address' => $address,
//            'status' => $status,
//            'year' => $year,
//            'month' => $month,
//            'day' => $day,
//            'proposer_id' => $proposer_id,
//            'users_id' => $users_id);
//
//        $result = $this->add($data);
//
//        return $result;

        $result = false;

        $meet = new $this->_document_class();

        $meet->options_date = $options_date;
        $meet->selected_date = $selected_date;
        $meet->meet_text = $meet_text;
        $meet->identity = $identity;
        $meet->remark = $remark;
        $meet->address = $address;
        $meet->status = $status;
        $meet->year = $year;
        $meet->month = $month;
        $meet->day = $day;
        $meet->proposer_id = $proposer_id;
        $meet->users_id = $users_id;

        try {
            $this->_dm->persist($meet);
            $this->_dm->flush();

            $result = $meet->id;
        }
        catch (Exception $e) {
            $this->_logger->info(_CLASS_, _FUNCTION_, $e->getMessage()."\n".$e->getTraceAsString());
        }

        return $result;
    }

    //修改聚会信息
    public function saveMeet($id, $options_date, $selected_date, $time_range, $meet_text, $remark, $address, $proposer_id, $users_id, $year, $month, $day, $status = 1, $identity = 1) {
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

        $result = $this->save($id, $data);

        return $result;
    }

    //获取某年某月日程安排集合
    public function getSchedule($user_id, $year, $month) {
        $query = $this->_dm->createQueryBuilder($this->_document_class)->field('year')->equals($year)->field('month')->equals($month)->field('users_id')->all($user_id)->field('status')->equals('1')->sort("created_at", -1);

        $result = $query->getQuery()->execute();

        return $result;
    }

    //获取某天日程安排集合
    public function getScheduleByDate($user_id, $year, $month, $day) {
        $query = $this->_dm->createQueryBuilder($this->_document_class)->field('year')->equals($year)->field('month')->equals($month)->field('day')->equals($day)->field('users_id')->all($user_id)->field('status')->equals('1')->sort("created_at", 1);

        $result = $query ->getQuery()->execute();

        return $result;
    }

    public function getPendingMeets($user_id) {
        $query = $this->_dm->createQueryBuilder($this->_document_class)->field('users_id')->all($user_id)->field('selected_date')->equals("false")->field('status')->equals('1')->sort("created_at", 1);

        $result = $query ->getQuery()->execute();

        return $result;
    }
} 