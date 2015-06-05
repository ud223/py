<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/5/28 0028
 * Time: 下午 10:19
 */

class Angel_ApiController extends Angel_Controller_Action {
    public function init() {
        $this->_helper->layout->setLayout('main');
        parent::init();
    }

    public function NumOpt($n) {
        if (strlen($n) >  1) {
            if (substr($n, 0, 1) == "0") {
                return substr($n, 1, 1);
            }
            else {
                return $n;
            }
        }
        else {
            return $n;
        }
    }

    //创建聚会
    public function createMeetAction() {
        $meetModel = $this->getModel('meet');

        $code = 200;
        $message = "活动安排添加成功!";

        $user_id = $this->getParam('user_id');
        $start_date = $this->getParam('start_date');
        $end_date = $this->getParam('end_date');
        $selected_date = $this->getParam('selected_date');
        $meet_text = $this->getParam('meet_text');
        $address = $this->getParam('address');
        $remark = $this->getParam('remark');
        $time_range = 0;
        $year = "";
        $month = "";
        $day = "";

        if (!$selected_date) {
            $selected_date = "";
        }

        // 测试代码
//        $message = 'userid:'.$user_id .'start_date:'.$start_date .'end_date:'. end_date . 'selected_date:'.$selected_date . 'meet_text:'. $meet_text . 'address:'. $address. 'remark:'.$remark;
//        $this->_helper->json(array('data' => $message, 'code' => $code)); exit;

        //参与人集合
        $users_id = array();
        //将申请人添加入参与人集合
        $users_id[] = $user_id;
        //日期范围
        $options_date = array();

        if (!$selected_date) {
            $this->_helper->json(array('data' => '1', 'code' => $code)); exit;
            //将日期字符串转成 Timestamp
            $dt_start = strtotime($start_date);
            $dt_end   = strtotime($end_date);
            // 重复 Timestamp + 1 天(86400), 直至大于结束日期中止
            do {
                //将 Timestamp 转成 ISO Date添加到日期范围集合
                $options_date[] = date('Y-m-d', $dt_start);
            } while (($dt_start += 86400) <= $dt_end);
        }
        else {
            $options_date[] = $selected_date;

            $strDate = explode("-", $selected_date);

            $year = $strDate[0];
            $month = $this->NumOpt($strDate[1]);
            $day = $this->NumOpt($strDate[2]);
        }

        //添加聚会申请
        $result = $meetModel->addMeet($options_date, $selected_date, $time_range, $meet_text, $remark, $address, $user_id, $users_id, $year, $month, $day);

        if (!$result) {
            $code = 0;
            $message = "活动安排添加失败!";
        }

        $this->_helper->json(array('data' => $message, 'code' => $code));
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

    //获取某月日程安排表
    public function getScheduleAction() {
        $meetModel = $this->getModel('meet');

        $code = 200;
        $message = "日程安排获取成功!";

        $user_id = $this->getParam('user_id');
        $year = $this->getParam('year');
        $month = $this->getParam('month');

//        $this->_helper->json(array('data' => $year.'-' . $month . '|' . $user_id, 'code' => $code)); exit;

        $result = $meetModel->getSchedule($user_id, $year, $month);

        if ($result) {
            $schedule = array();

            foreach ($result as $r) {
                $schedule[] = array("date" => $r->selected_date, "year" => $r->year, "month" => $r->month, "day" => $r->day, "identity" => $r->identity);
            }

            $this->_helper->json(array('data' => $schedule, 'code' => $code));
        }
        else {
            $code = 0;
            $message = "获取日程安排失败!";

            $this->_helper->json(array('data' => $message, 'code' => $code));
        }
    }

    public function getUsersInfo($users_id) {
        $userModel = $this->getModel('user');

        $tmp_users_id = array();

        foreach ($users_id as $u) {
            $tmp_users_id[] = $u;
        }

        $result = $userModel->getUserByOpenIds($tmp_users_id);

        $this->_helper->json(array('data' => '查询', 'code' => 200)); exit;

        if (!$result) {
            $this->_helper->json(array('data' => "查询报错", 'code' => 200)); exit;

            return false;
        }

        $users = array();

        foreach ($result as $r) {
            $users[] = array("openid"=>$r->openid, "headimgurl"=>$r->$headimgurl, "nickname"=>$r->nickname);
        }

        $this->_helper->json(array('data' => $users, 'code' => 200)); exit;

        return $users;
    }

    public function getMeetAction() {
        $meetModel = $this->getModel('meet');

        $code = 200;
        $message = "日程安排获取成功!";

        $user_id = $this->getParam('user_id');
        $year = $this->getParam('year');
        $month = $this->getParam('month');
        $day = $this->getParam('day');

        $result = $meetModel->getScheduleByDate($user_id, $year, $month, $day);

        if ($result) {
            $meets = array();

            foreach ($result as $r) {
                //通过users_id集合得到用户集合
                $users = $this->getUsersInfo($r->users_id);

                $this->_helper->json(array('data' => $users, 'code' => $code)); exit;

                $meets[] = array("id"=>$r->id, "meet_text"=>$r->meet_text, "address"=>$r->address, "remark"=>$r->remark, "users"=>$r->users_id, "date"=>$r->selected_date, "year"=>$r->year, "month"=>$r->month, "day"=>$r->day, "identity"=>$r->identity);
            }

            $this->_helper->json(array('data' => $meets, 'code' => $code));
        }
        else {
            $code = 0;
            $message = "获取日程安排失败!";

            $this->_helper->json(array('data' => $message, 'code' => $code));
        }
    }

    public function loadMeetAction() {
        $meetModel = $this->getModel('meet');

        $id = $this->getParam('id');
        $user_id = $this->getParam('user_id');

        $code = 200;
        $message = "活动获取成功!";

//        $this->_helper->json(array('data' => $id, 'code' => $code)); exit;

        $result = $meetModel->getById($id);

        $data = array("meet_text"=>$result->meet_text, "selected_date"=>$result->selected_date, "year"=>$result->year, "month"=>$result->month, "day"=>$result->day,"address"=>$result->address, "remark"=>$result->remark);

        if ($result) {
            $this->_helper->json(array('data' => $data, 'code' => $code));
        }
        else {
            $code = 0;
            $message = "活动信息获取失败!";

            $this->_helper->json(array('data' => $message, 'code' => $code));
        }
    }

    public function createWordAction() {
        $wordModel = $this->getModel('word');

        $user_id = $this->getParam('user_id');
        $meet_id = $this->getParam('meet_id');
        $word_text = $this->getParam('word_text');

        $code = 200;
        $message = "留言成功!";

        $result = $wordModel->addWord($meet_id, $word_text, $user_id);

        if (!$result) {
            $code = 0;
            $message = "留言失败!";
        }

        $this->_helper->json(array('data' => $message, 'code' => $code));
    }

    public function  getWordAction() {
        $wordModel = $this->getModel('word');

        $code = 200;
        $message = "日程安排获取成功!";

        $meet_id = $this->getParam('meet_id');

//        $this->_helper->json(array('data' => $year.'-' . $month . '-' . $day.  '|' . $user_id, 'code' => $code)); exit;

        $result = $wordModel->getWordsByMeetId($meet_id);

        if ($result) {
            $words = array();

            foreach ($result as $r) {
                $words[] = array("id"=>$r->id, "text"=>$r->text, "date"=>$r->created_at, "user_id"=>$r->user_id);
            }

            $this->_helper->json(array('data' => $words, 'code' => $code));
        }
        else {
            $code = 0;
            $message = "获取日程安排失败!";

            $this->_helper->json(array('data' => $message, 'code' => $code));
        }
    }
} 