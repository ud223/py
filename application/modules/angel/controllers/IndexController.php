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

        $openid = $this->getParam('id');

        if ($openid) {
            $this->view->openid = $openid;
        }
        else {
            $this->view->openid = '';
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

//    public function visitorRegUser($meet_id, $user_id) {
//        $meetModel = $this->getModel('meet');
//
//        $meet = $meetModel->getById($meet_id);
//
//
//    }

    public function regUserAction() {
        $code = $_GET['code'];
        $meet_id = $this->getParam('id');

        $open_id = $this->getOpenId($code);
        $userInfo = $this->getUserInfo($open_id);
        $result = $this->addUser($userInfo);

        if ($result) {
            if ($meet_id) {
                header("Location: /meet/view/" . $meet_id . "/"  . $open_id); exit;
            }
            else {
                header("Location: /" . $open_id); exit;
            }
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
        $meet_id = $this->getParam('id');
        $user_id = $this->getParam('userid');

        $result = $meetModel->getById($meet_id);

        $users_id = "";

        foreach ($result->users_id as $id) {
            $users_id = $users_id . '|' . $id;
        }

        $this->view->meet_id = $meet_id;
        $this->view->proposer_id = $result->proposer_id;
        $this->view->users_id = $users_id;
        $this->view->user_id = $user_id;
        $this->view->appid = $this->app_id;
    }

    public function pendingMeetAction() {
        $this->_helper->layout->setLayout('detail');
    }

    public function voteMeetAction() {
        $this->_helper->layout->setLayout('detail');

        $meetModel = $this->getModel('meet');
        $voteModel = $this->getModel('vote');

        $meet_id = $this->getParam('id');
        $user_id = $this->getParam('userid');

        $result = $meetModel->getById($meet_id);

        $users_id = "";

        foreach ($result->users_id as $id) {
            $users_id = $users_id . '|' . $id;
        }

        $vote = $voteModel->getVoteByMeetId($meet_id);

        $this->view->meet_id = $meet_id;
        $this->view->proposer_id = $result->proposer_id;
        $this->view->nickname = $result->nickname;
        $this->view->headimgurl = $result->headimgurl;
        $this->view->craeted_at = $result->craeted_at;
        $this->view->users_id = $users_id;
        $this->view->user_id = $user_id;
        $this->view->appid = $this->app_id;
        $this->view->vote = count($vote);
    }


    public function resultAction() {
        $this->_helper->layout->setLayout('normal');

        $date = $this->getParam('date');
        $meet_id = $this->getParam('meet_id');

        $this->view->date = $date;
        $this->view->meet_id = $meet_id;
    }

    public function successAction() {
        $this->_helper->layout->setLayout('normal');

        $meetModel = $this->getModel('model');

        $meet_id = $this->getParam('meet_id');

        $result = $meetModel->getById($meet_id);

        $this->view->meet_text = $result->meet_text;
        $this->view->meet_id = $meet_id;
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
}
