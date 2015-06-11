<?php

define("TOKEN", "yingxi");

class Angel_IndexController extends Angel_Controller_Action {
    private $app_id = 'wx1122a6c5539bca36';
    private $app_secret = '867e2d618574b9b9d648063f290ef326';
    private $access_token = '';
    //微信在验证
    public function validAction() {
        $this->valid();
    }
    //微信在验证
    public function valid() {
        $echoStr = $_GET["echostr"];

        //valid signature , option
        if($this->checkSignature()){
            echo $echoStr;
            exit;
        }
    }
    //微信在验证
    public function responseMsg() {
        //get post data, May be due to the different environments
        $postStr = $GLOBALS["HTTP_RAW_POST_DATA"];

        //extract post data
        if (!empty($postStr)){
            /* libxml_disable_entity_loader is to prevent XML eXternal Entity Injection,
               the best way is to check the validity of xml by yourself */
            libxml_disable_entity_loader(true);
            $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
            $fromUsername = $postObj->FromUserName;
            $toUsername = $postObj->ToUserName;
            $keyword = trim($postObj->Content);
            $time = time();
            $textTpl = "<xml>
							<ToUserName><![CDATA[%s]]></ToUserName>
							<FromUserName><![CDATA[%s]]></FromUserName>
							<CreateTime>%s</CreateTime>
							<MsgType><![CDATA[%s]]></MsgType>
							<Content><![CDATA[%s]]></Content>
							<FuncFlag>0</FuncFlag>
							</xml>";
            if(!empty( $keyword )) {
                $msgType = "text";
                $contentStr = "Welcome to wechat world!";
                $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
                echo $resultStr;
            }else{
                echo "Input something...";
            }

        }else {
            echo "";
            exit;
        }
    }
    //微信在验证
    private function checkSignature() {
        // you must define TOKEN by yourself
        if (!defined("TOKEN")) {
            throw new Exception('TOKEN is not defined!');
        }

        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];

        $token = TOKEN;
        $tmpArr = array($token, $timestamp, $nonce);
        // use SORT_STRING rule
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode( $tmpArr );
        $tmpStr = sha1( $tmpStr );

        if( $tmpStr == $signature ){
            return true;
        }else{
            return false;
        }
    }

    public function init() {
        $this->_helper->layout->setLayout('main');
        parent::init();
    }

    public function indexAction() {
        $this->view->appid = $this->app_id;

        $userModel = $this->getModel('user');

        $openid = $this->getParam('id');
        $share_id = $this->getParam('share_id');

        if ($openid) {
            $this->view->openid = $openid;

            $users = $userModel->getUserByOpenId($openid);

            foreach ($users as $u) {
                $user = $u;
                break;
            }

            $this->view->nickname = $user->nickname;
            $this->view->headimgurl = $user->headimgurl;
        }
        else {
            $this->view->openid = '';
            $this->view->nickname = '';
            $this->view->headimgurl = '';
        }

        if ($share_id) {
            $this->view->share_id = $share_id;
        }
        else {
            $this->view->share_id = '';
        }
    }

    public function getOpenId($code) {
        $url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=". $this->app_id ."&secret=". $this->app_secret ."&code=". $code ."&grant_type=authorization_code";

        $weixin = file_get_contents($url);
        $jsondecode = json_decode($weixin);
        $array = get_object_vars($jsondecode);

        $open_id = $array['openid'];
        $this->access_token = $array['access_token'];

        return $open_id;
    }

    public function getUserInfo($open_id) {
        $url = "https://api.weixin.qq.com/sns/userinfo?access_token=". $this->access_token ."&openid=". $open_id ."&lang=zh_CN";

        $weixin = file_get_contents($url);

        $jsondecode = json_decode($weixin);
        $array = get_object_vars($jsondecode);

        return $array;
    }

    public function addUser($data) {
        $userModel = $this->getModel('user');

//        echo $data['openid']; exit;

        $openid = $data['openid'];
        $nickname = $data['nickname'];
        $sex = $data['sex'];
//        $language = $data['language'];
        $city = $data['city'];
        $province = $data['province'];
        $country = $data['country'];
        $headimgurl = $data['headimgurl'];

        $result = $userModel->getUserByOpenId($openid);

        //如果该openid用户已经添加
        if (count($result) == 1) {
//            echo "added"; exit;
            return true;
        }

        $result = $userModel->addUser($openid, 0, $nickname, $sex, "", $city, $province, $country, $headimgurl, "");

        return $result;
    }

    public function regUserAction() {
        $code = $_GET['code'];
        $id = $this->getParam('id');

        $tmp_web_url = $this->getParam('web_url');
        $web_url = urldecode($tmp_web_url);
//        exit($web_url);
        if ($web_url == "http://cbook.test.angelhere.cn/") {
            $web_url = "/";
        }
        else {
            $web_url = str_replace("http://cbook.test.angelhere.cn/", "/", $web_url);
        }
//        exit($web_url);
        $open_id = $this->getOpenId($code);
        $userInfo = $this->getUserInfo($open_id);
        $result = $this->addUser($userInfo);
//        exit($web_url);


        if ($web_url == "/") {
            $web_url = $web_url . $open_id;
        }
        else {
            if (substr($web_url, strlen($web_url) - 1, 1) != "/") {
                $web_url = $web_url . "/"  . $open_id;
            }
            else {
                $web_url = $web_url . $open_id;
            }
        }

//        exit($web_url);
//
//        $web_url = $web_url;

        if ($result) {
//            if ($meet_id) {
//                header("Location: /meet/view/" . $meet_id . "/"  . $open_id); exit;
//            }
//            else {
//                header("Location: /" . $open_id); exit;
//            }
//            exit($web_url);
            header("Location:". $web_url); exit;
        }
        else {
            exit("注册或登陆失败,请重试!");
        }
    }

    public function addMeetAction() {
        $this->_helper->layout->setLayout('detail');
    }

    public function viewMeetAction() {
        $this->_helper->layout->setLayout('detail');

        $meetModel = $this->getModel('meet');
        $userModel = $this->getModel('user');

        $meet_id = $this->getParam('id');
        $user_id = $this->getParam('userid');

        $result = $meetModel->getById($meet_id);

        $users_id = "";

        foreach ($result->users_id as $id) {
            $users_id = $users_id . '|' . $id;
        }

        $users = $userModel->getUserByOpenId($result->proposer_id);

        foreach ($users as $u) {
            $user = $u;

            break;
        }

        $this->view->meet_id = $meet_id;
        $this->view->proposer_id = $result->proposer_id;
        $this->view->users_id = $users_id;
        $this->view->user_id = $user_id;
        $this->view->appid = $this->app_id;
        $this->view->nickname = $user->nickname;
        $this->view->headimgurl = $user->headimgurl;
        $this->view->create_date = date_format($user->created_at, 'Y-m-d');
    }

    public function pendingMeetAction() {
        $this->_helper->layout->setLayout('detail');
    }

    public function voteMeetAction() {
        $this->_helper->layout->setLayout('detail');

        $meetModel = $this->getModel('meet');
        $voteModel = $this->getModel('vote');
        $userModel = $this->getModel('user');

        $meet_id = $this->getParam('id');
        $user_id = $this->getParam('userid');

        $result = $meetModel->getById($meet_id);

        $users_id = "";

        foreach ($result->users_id as $id) {
            $users_id = $users_id . '|' . $id;
        }

        $vote = $voteModel->getVoteByMeetId($meet_id);

        $users = $userModel->getUserByOpenId($result->proposer_id);

        foreach ($users as $u) {
            $user = $u;

            break;
        }

        $this->view->meet_id = $meet_id;
        $this->view->proposer_id = $result->proposer_id;
        $this->view->nickname = $user->nickname;
        $this->view->headimgurl = $user->headimgurl;
        $this->view->create_date = date_format($user->created_at, 'Y-m-d');
        $this->view->users_id = $users_id;
        $this->view->user_id = $user_id;
        $this->view->appid = $this->app_id;
        $this->view->vote = count($vote);
        $this->view->title = "活动:" . $result->meet_text;
    }


    public function resultAction() {
        $this->_helper->layout->setLayout('normal');

        $meetModel = $this->getModel('meet');

        $meet_id = $this->getParam('meet_id');

        $meet = $meetModel->getById($meet_id);

        $strDate = explode('-', $meet->selected_date);

        $this->view->date = $strDate[0] . '年' . $this->strToIntFormat($strDate[1]) . '月' . $this->strToIntFormat($strDate[2]) . '日';
        $this->view->meet_id = $meet_id;
    }

    public function successAction() {
        $this->_helper->layout->setLayout('normal');

        $meetModel = $this->getModel('meet');

        $meet_id = $this->getParam('meet_id');

        $result = $meetModel->getById($meet_id);

        $this->view->meet_text = $result->meet_text;
        $this->view->meet_id = $meet_id;
    }

    public function shareMasterAction() {
        $this->_helper->layout->setLayout('main');
        $userModel = $this->getModel('user');

        $proposer_id = $this->getParam('id');
        $user_id = $this->getParam('userid');

        $users = $userModel->getUserByOpenId($proposer_id);

        foreach ($users as $u) {
            $user = $u;

            break;
        }

        $this->view->appid = $this->app_id;
        $this->view->proposer_id = $proposer_id;
        $this->view->nickname = $user->nickname;
        $this->view->headimgurl = $user->headimgurl;
        $this->view->user_id = $user_id;
    }

    public function calendarVisitorAction() {
        $userModel = $this->getModel('user');

        $user_id = $this->getParam('user_id');

        $result = $userModel->getUserByOpenId($user_id);

        foreach ($result as $r) {
            $user = $r;

            break;
        }

        $this->view->appid = $this->app_id;

        $openid = $this->getParam('id');

        if ($openid) {
            $this->view->user_id = $openid;
        }
        else {
            $this->view->user_id = '';
        }

        $this->view->headimgurl = $user->headimgurl;
        $this->view->ncikname = $user->ncikname;
        $this->view->openid = $user_id;
    }

    public function cbookerVisitorAction() {
        $this->_helper->layout->setLayout('detail');
    }

    //本地测试注册用户方法
    public function testRegAction() {
        $userModel = $this->getModel('user');

        $result = $userModel->addUser("1234567", 0, "smple", 1, "", "aaa", "bbb", "ccc", "img", "");

        exit('添加测试数据成功');
    }
    //本地测试查询用户方法
    public function testGetAction() {
        $userModule = $this->getModel('user');

//        $users_id = array();
//        $users_id[] = "123456";
//        $users_id[] = "1234567";
//        var_dump($users_id); exit;

        $user_id = "123456";
//        $result = $userModule->getUserByOpenIds($users_id);
        $result = $userModule->getUserByOpenId($user_id);

        if (!$result) {
            exit('failed');
        }

        $msg = "";

        foreach ($result as $r) {
            $msg = $msg . $r->nickname . '|';
        }

        exit($msg);
    }

    public function strToIntFormat($n) {
        if (substr($n, 0, 1) == '0') {
            return substr($n, 1, 1);
        }

        return $n;
    }
}
