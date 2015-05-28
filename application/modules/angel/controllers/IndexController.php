<?php

class Angel_IndexController extends Angel_Controller_Action {

    protected $login_not_required = array('index');

    private $app_id = 'wx1122a6c5539bca36';
    private $app_secret = '867e2d618574b9b9d648063f290ef326';

    //构造函数，获取Access Token
    public function getAccessToken($appid = NULL, $appsecret = NULL) {
        if($appid) {
            $this->$app_id = $appid;
        }
        if($appsecret) {
            $this->$app_secret = $appsecret;
        }
        //这里需要修改成用户初次登陆或登陆超时重新登录的判断
        //如果当前时间大于过期时间就重新获取一次用户的access_token和获取时间
        if (time() > ($this->lasttime + 7200)) {
            $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$this->$app_id."&secret=".$this->$app_secret;
            $res = $this->https_request($url);
            $result = json_decode($res, true);

            $access_token = $result["access_token"];
            $lasttime = time();
        }
    }
    //获取用户基本信息
    public function get_user_info($openid) {
        $url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=".$this->access_token."&openid=".$openid."&lang=zh_CN";
        $res = $this->https_request($url);

        //这里需修改成用户重新保存信息
        return json_decode($res, true);
    }

    //http请求
    public function https_request($url, $data = null) {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        if (!empty($data)){
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($curl);
        curl_close($curl);
        return $output;
    }

    public function init() {
        $this->_helper->layout->setLayout('normal');
        parent::init();
    }
    public function indexAction() {
        $this->_forward('login');
        //获取用户的access_token
        $this->getAccessToken();
        //重新获取用户的基本信息
        $this->get_user_info();
//        $this->view->
    }
}
