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

//    //构造函数，获取Access Token
//    public function getAccessToken($appid = NULL, $appsecret = NULL) {
//        if($appid) {
//            $this->$app_id = $appid;
//        }
//        if($appsecret) {
//            $this->$app_secret = $appsecret;
//        }

//        $this->lasttime = 1395049256;
//        $this->access_token = "nRZvVpDU7LxcSi7GnG2LrUcmKbAECzRf0NyDBwKlng4nMPf88d34pkzdNcvhqm4clidLGAS18cN1RTSK60p49zIZY4aO13sF-eqsCs0xjlbad-lKVskk8T7gALQ5dIrgXbQQ_TAesSasjJ210vIqTQ";
//        //这里需要修改成用户初次登陆或登陆超时重新登录的判断
//        //如果当前时间大于过期时间就重新获取一次用户的access_token和获取时间
//        if (time() > ($this->lasttime + 7200)) {
//            $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$this->$app_id."&secret=".$this->$app_secret;
//            $res = $this->https_request($url);
//            $result = json_decode($res, true);
//
//            $this->$access_token = $result["access_token"];
//            $lasttime = time();
//        }
//    }
//    //获取用户基本信息
//    public function get_user_info($openid) {
//        $url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=".$this->access_token."&openid=".$openid."&lang=zh_CN";
//        $res = $this->https_request($url);
//
//        //这里需修改成用户重新保存信息
//        return json_decode($res, true);
//    }
//
//    //http请求
//    public function https_request($url, $data = null) {
//        $curl = curl_init();
//        curl_setopt($curl, CURLOPT_URL, $url);
//        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
//        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
//        if (!empty($data)){
//            curl_setopt($curl, CURLOPT_POST, 1);
//            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
//        }
//        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
//        $output = curl_exec($curl);
//        curl_close($curl);
//        return $output;
//    }

    public function init() {
        $this->_helper->layout->setLayout('main');
        parent::init();
    }

    public function isLogin() {

    }

    public function Login() {

    }

    public function regUser() {
        //用户信息
//        $userModel = $this->getModel('user');
//
//        $openid = '12345678';
//        $subscribe = 1;
//        $nickname = 'test_1';
//        $sex = 1;
//        $language = 'zh_CN';
//        $city = '武汉';
//        $province = '湖北';
//        $country = '中国';
//        $headimgurl = 'http://img.woyaogexing.com/2015/06/02/41cd866f421fb447!200x200.jpg'; //http://img.woyaogexing.com/2015/06/01/d0cb2cbac54701ce!200x200.jpg
//        $subscribe_time = '1386160805';
//
//
//        $userModel->addUser($openid, $subscribe, $nickname, $sex, $language, $city, $province, $country, $headimgurl, $subscribe_time, $last_time = null, $access_token = null);
    }

    public function indexAction() {
        //根据返回路径获取open_id
        $open_id = "";
        //获取用户的access_token
//        $this->getAccessToken();
        //重新获取用户的基本信息
//        $this->get_user_info($open_id);
//        $this->view->

        $this->view->appid = $this->app_id;
    }

    public function getOpenId($code) {
        $weixin = file_get_contents("https://api.weixin.qq.com/sns/oauth2/access_token?appid=". $this->app_id ."&secret=". $this->app_secret ."&code". $code ."&grant_type=authorization_code");

        $jsondecode = json_decode($weixin);
        $array = get_object_vars($jsondecode);
        $open_id = $array['openid'];
        $this->access_token = $array['access_token'];

        return $open_id;
    }

    public function getUserInfo($open_id) {
        $weixin = file_get_contents("https://api.weixin.qq.com/sns/userinfo?access_token=". $this->access_token ."&openid=". $open_id ."&lang=zh_CN");

        $jsondecode = json_decode($weixin);
        $array = get_object_vars($jsondecode);

        var_dump($array); exit;

        return $array;
    }

    public function regAction() {
        $code = $_GET['code'];
        $open_id = $this->getOpenId($code);

        $this->getUserInfo($open_id);
    }

    public function addMeetAction() {
        $this->_helper->layout->setLayout('detail');
    }

    public function viewMeetAction() {
        $this->_helper->layout->setLayout('detail');

        $id = $this->getParam('id');

        $this->view->meet_id = $id;
    }

    public function voteMeetAction() {
        $this->_helper->layout->setLayout('detail');
    }
}
