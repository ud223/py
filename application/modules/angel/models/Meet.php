<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/5/27 0027
 * Time: 下午 11:13
 */

class Angel_Model_Meet extends Angel_Model_AbstractModel {
    //新增聚会信息
    public function addMeet($options_date, $selected_date, $time_range, $meet_text, $remake, $address, $status = '申请中', $proposer) {
        $data = array('options_date' => $options_date,
            'selected_date' => $selected_date,
            '$time_range' => $time_range,
            '$meet_text' => $meet_text,
            '$remake' => $remake,
            '$address' => $address,
            '$status' => $status,
            'proposer' => $proposer);

        $result = $this->add($data);

        return $result;
    }

    //修改聚会信息
    public function saveMeet($id, $options_date, $selected_date, $time_range, $meet_text, $remake, $address, $status = '申请中', $proposer, $users, $log, $words) {
        $data = array('options_date' => $options_date,
            'selected_date' => $selected_date,
            '$time_range' => $time_range,
            '$meet_text' => $meet_text,
            '$remake' => $remake,
            '$address' => $address,
            '$status' => $status,
            'proposer' => $proposer,
            'log' => $log,
            'words' => $words,
            'users' => $users);

        $result = $this->save($id, $data);

        return $result;
    }
} 