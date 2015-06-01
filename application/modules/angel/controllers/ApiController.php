<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/5/28 0028
 * Time: 下午 10:19
 */

class Angel_ApiController  extends Angel_Controller_Action {
    //创建聚会
    public function createMeet() {
        $meetModel = $this->getModel('meet');
        $userModel = $this->getModel('user');

        $user_id = $this->getParam('user_id');
        $start_date = $this->getParam('start_date');
        $end_date = $this->getParam('end_date');
        $meet_text = $this->getParam('meet_text');
        $address = $this->getParam('address');
        $remark = $this->getParam('remark');
        $time_range = null;
        //获取申请人
        $user = $userModel->getById($user_id);
        //参与人集合
        $proposer[] = array();
        //将申请人添加入参与人集合
        $proposer[] = $user;
        //日期范围
        $options_date = array();
        //将日期字符串转成 Timestamp
        $dt_start = strtotime($start_date);
        $dt_end   = strtotime($end_date);
        // 重复 Timestamp + 1 天(86400), 直至大于结束日期中止
        do {
            //将 Timestamp 转成 ISO Date添加到日期范围集合
            $options_date[] = date('Y-m-d', $dt_start);
        } while (($dt_start += 86400) <= $dt_end);

        //添加聚会申请
        $meetModel->addMeet($options_date, $time_range, $meet_text, $remark, $address, $proposer);
    }

    //修改聚会
    public function modifyMeet() {

    }

    //确认提交聚会日程
    public function acceptMeet() {

    }

    //提交留言
    public function createWord() {

    }
} 