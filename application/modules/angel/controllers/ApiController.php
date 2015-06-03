<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/5/28 0028
 * Time: 下午 10:19
 */

class Angel_ApiController extends Angel_Controller_Action {
    protected $login_not_required = array('index');

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
} 