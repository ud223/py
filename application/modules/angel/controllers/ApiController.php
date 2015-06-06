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

    /********************************************************************************************************************
     *  聚会日历代码块
     *
     * ******************************************************************************************************************/
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

        //参与人集合
        $users_id = array();
        //将申请人添加入参与人集合
        $users_id[] = $user_id;
        //日期范围
        $options_date = array();

        if ($selected_date == "false") {
            //将日期字符串转成 Timestamp
            $dt_start = strtotime($start_date);
            $dt_end   = strtotime($end_date);
            // 重复 Timestamp + 1 天(86400), 直至大于结束日期中止
            do {
                //将 Timestamp 转成 ISO Date添加到日期范围集合
                $options_date[] = date('Y-m-d', $dt_start);
            } while (($dt_start += 86400) <= $dt_end);

//            $this->_helper->json(array('data' => $options_date, 'code' => $code)); exit;
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

    //获取某月日程安排表
    public function getScheduleAction() {
        $meetModel = $this->getModel('meet');

        $code = 200;
        $message = "日程安排获取成功!";

        $user_id = $this->getParam('user_id');
        $year = $this->getParam('year');
        $month = $this->getParam('month');

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

    //获取特定日期的活动安排
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

                $meets[] = array("id"=>$r->id, "meet_text"=>$r->meet_text, "address"=>$r->address, "remark"=>$r->remark, "users"=>$users, "date"=>$r->selected_date, "year"=>$r->year, "month"=>$r->month, "day"=>$r->day, "identity"=>$r->identity);
            }

            $this->_helper->json(array('data' => $meets, 'code' => $code));
        }
        else {
            $code = 0;
            $message = "获取日程安排失败!";

            $this->_helper->json(array('data' => $message, 'code' => $code));
        }
    }

    public function pendingMeetAction() {
        $meetModel = $this->getModel('meet');

        $user_id = $this->getParam('user_id');

        $code = 200;
        $message = "等待中活动获取成功!";

        $result = $meetModel->getPendingMeets($user_id);

        if ($result) {
            $meets = array();

            foreach ($result as $r) {
                $length = count($r->options_date);
                $start_date = $r->options_date[0];
                $end_date = $r->options_date[$length - 1];
                //通过users_id集合得到用户集合
                $users = $this->getUsersInfo($r->users_id);

                $meets[] = array("id"=>$r->id, "start_date"=>$start_date, "start_date"=>$end_date, "meet_text"=>$r->meet_text, "users"=>$users);
            }

            $this->_helper->json(array('data' => $meets, 'code' => $code));
        }
        else {
            $code = 0;
            $message = "等待中活动获取失败!";

            $this->_helper->json(array('data' => $message, 'code' => $code));
        }
    }

    //根据活动id加载活动信息
    public function loadMeetAction() {
        $meetModel = $this->getModel('meet');

        $id = $this->getParam('id');
        $user_id = $this->getParam('user_id');

        $code = 200;
        $message = "活动获取成功!";

        $result = $meetModel->getById($id);


        if ($result) {
            $users = $this->getUsersInfo($result->users_id);
            $length = count($result->options_date);
            $start_date = $result->options_date[0];
            $end_date = $result->options_date[$length - 1];

            $data = array("meet_text"=>$result->meet_text, "start_date"=>$start_date, "end_date"=>$end_date, "selected_date"=>$result->selected_date, "year"=>$result->year, "month"=>$result->month, "day"=>$result->day,"address"=>$result->address, "remark"=>$result->remark, "users"=>$users);


            $this->_helper->json(array('data' => $data, 'code' => $code));
        }
        else {
            $code = 0;
            $message = "活动信息获取失败!";

            $this->_helper->json(array('data' => $message, 'code' => $code));
        }
    }

    //加入活动
    public function joinMeetAction() {
        $meetModel = $this->getModel('meet');

        $meet_id = $this->getParam('meet_id');
        $user_id = $this->getParam('user_id');

        $code = 200;
        $message = "参加成功!";

        $meet = $meetModel->getById($meet_id);

        if (!$meet) {
            $code = 0;
            $message = "获取活动信息错误,活动参加失败!". $meet_id . '|' . $user_id;

            $this->_helper->json(array('data' => $message, 'code' => $code)); exit;
        }

        $users_id = array();

        foreach ($meet->users_id as $u) {
            $users_id[] = $u;
        }

        $users_id[] = $user_id;

        $result = $meetModel->saveMeet($meet_id, $meet->options_date, $meet->selected_date, $meet->selected_date, $meet->time_range, $meet->meet_text, $meet->remark, $meet->address, $meet->proposer_id, $users_id, $meet->year, $meet->month, $meet->day);

        if (!$result) {
            $code = 0;
            $message = "活动参加失败!";
        }

        $this->_helper->json(array('data' => $message, 'code' => $code));
    }

    //离开活动
    public function leaveMeetAction() {
        $meetModel = $this->getModel('meet');

        $meet_id = $this->getParam('meet_id');
        $user_id = $this->getParam('user_id');

        $code = 200;
        $message = "退出成功!";

        $meet = $meetModel->getById($meet_id);

        if (!$meet) {
            $code = 0;
            $message = "获取活动信息错误,活动退出失败!";

            $this->_helper->json(array('data' => $message, 'code' => $code)); exit;
        }

        $users_id = array();

        foreach ($meet->users_id as $u) {
            if ($u != $user_id) {
                $users_id[] = $u;
            }
        }

        $result = $meetModel->saveMeet($meet_id, $meet->options_date, $meet->selected_date, $meet->selected_date, $meet->time_range, $meet->meet_text, $meet->remark, $meet->address, $meet->proposer_id, $users_id, $meet->year, $meet->month, $meet->day);

        if (!$result) {
            $code = 0;
            $message = "活动退出失败!";
        }

        $this->_helper->json(array('data' => $message, 'code' => $code));
    }

    //关闭活动
    public function closeMeetAction() {
        $meetModel = $this->getModel('meet');

        $meet_id = $this->getParam('meet_id');
        $user_id = $this->getParam('user_id');

        $code = 200;
        $message = "关闭成功!";

        $meet = $meetModel->getById($meet_id);

        if (!$meet) {
            $code = 0;
            $message = "获取活动信息错误,活动关闭失败!";

            $this->_helper->json(array('data' => $message, 'code' => $code)); exit;
        }

        //关闭活动需要踢掉所有参与用户,所以传一个空的用户数组
        $users_id = array();

        $result = $meetModel->saveMeet($meet_id, $meet->options_date, $meet->selected_date, $meet->selected_date, $meet->time_range, $meet->meet_text, $meet->remark, $meet->address, $meet->proposer_id, $users_id, $meet->year, $meet->month, $meet->day, 0);

        if (!$result) {
            $code = 0;
            $message = "活动关闭失败!";
        }

        $this->_helper->json(array('data' => $message, 'code' => $code));
    }

    //投票日期次数计算并保存
    public  function insertVote($date, $meet_id) {
        $voteModel = $this->getModel('vote');

        $result = $voteModel->getVoteByMeetIdAndDate($meet_id, $date);
        $b = count($result);

        //如果该日期已经被投票过，那么就在num数字上加1
        if ($b > 0) {
            foreach ($result as $r) {
                $vote = $r;

                break;
            }

            $result = $voteModel->saveDateVote($vote->id, $vote->meet_id, $vote->date, $vote->num + 1);
        }
        else {
            $result = $voteModel->addDateVote($meet_id, $date);
        }

        return $result;
    }

    //添加投票日期
    public function addVoteAction() {
        $meet_id = $this->getParam('meet-id');
        $user_id = $this->getParam('user_id');
        $date1 = $this->getParam('date1');
        $date2 = $this->getParam('date2');
        $this->_helper->json(array('data' => $date1 .$date2 , 'code' => $code)); exit;
        $code = 200;
        $message = "投票成功!";

        $result = $this->insertVote(date1, $meet_id);

        if (!result) {
            $code = 0;
            $message = "投票失败!";

            $this->_helper->json(array('data' => $message, 'code' => $code)); exit;
        }

        if ($date1 != date2) {
            $result = $this->insertVote(date2, $meet_id);

            if (!result) {
                $code = 0;
                $message = "投票失败!";

                $this->_helper->json(array('data' => $message, 'code' => $code)); exit;
            }
        }

        $this->_helper->json(array('data' => $message, 'code' => $code));;
    }

    public  function setVoteAction() {
        $voteModel = $this->getModel('vote');
        $meetModel = $this->getModel('meet');

        $meet_id = $this->getParam('meet_id');

        $code = 200;
        $message = "活动日期确认成功!";

        $voteDates = $voteModel->getVoteByMeetId($meet_id);

        if (!$voteDates) {
            $code = 0;
            $message = "请先投票活动日期!";

            $this->_helper->json(array('data' => $message, 'code' => $code)); exit;
        }

        $max_date = "";
        $max_num = 0;
        //冒泡获取最大投票数日期
        foreach ($voteDates as $r) {
            if ($max_num > $r->num) {
                $max_date = $r->vote_date;
            }
        }
        //获取当前活动
        $meet = $meetModel->getById($meet_id);
        $strDate = explode('-',$max_date);

        $result = $meetModel->saveMeet($meet_id, $meet->options_date, $max_date, $meet->time_range, $meet->meet_text, $meet->remark, $meet->address, $meet->proposer_id, $meet->users_id, $strDate[0], $strDate[1], $strDate[2]);

        if (!$result) {
            $code = 0;
            $message = "活动日期确认失败!";
        }

        $this->_helper->json(array('data' => $message, 'code' => $code));
    }

    /*******************************************************************************************************************
     *  留言代码块
     *
     * *****************************************************************************************************************/
    //添加留言
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
        $message = "留言获取成功!";

        $meet_id = $this->getParam('meet_id');

        $result = $wordModel->getWordsByMeetId($meet_id);

        if ($result) {
            $words = array();

            foreach ($result as $r) {
                $user = $this->getUserInfo($r->user_id);

                $words[] = array("id"=>$r->id, "text"=>$r->text, "date"=>$r->created_at, "user"=>$user);
            }

            $this->_helper->json(array('data' => $words, 'code' => $code));
        }
        else {
            $code = 0;
            $message = "获取留言失败!";

            $this->_helper->json(array('data' => $message, 'code' => $code));
        }
    }

    /*******************************************************************************************************************
     *  用户操作代码块
     *
     * *****************************************************************************************************************/

    //获取单个用户的详细数据
    public function getUserInfo($user_id) {
        $userModel = $this->getModel('user');

        $result = $userModel->getUserByOpenId($user_id);

        if (!$result) {
            return false;
        }

        foreach ($result as $r) {
            $user = array("openid"=>$r->openid, "headimgurl"=>$r->headimgurl, "nickname"=>$r->nickname);

            break;
        }

        return $user;
    }

    //获取用户集合的信息数据
    public function getUsersInfo($users_id) {
        $userModel = $this->getModel('user');

        $tmp_users_id = array();

        foreach ($users_id as $u) {
            $tmp_users_id[] = $u;
        }

        $result = $userModel->getUserByOpenIds($tmp_users_id);

        if (!$result) {
            return false;
        }

        $users = array();

        foreach ($result as $r) {
            $users[] = array("openid"=>$r->openid, "headimgurl"=>$r->headimgurl, "nickname"=>$r->nickname);
        }

        return $users;
    }
} 