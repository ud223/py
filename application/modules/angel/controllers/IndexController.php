<?php

class Angel_IndexController extends Angel_Controller_Action {

    protected $login_not_required = array('index', 'upgrade', 'subscribe', 'login', 'register', 'email-validation', 'is-email-can-be-used', 'forgot-password', 'version-get', 'device', 'device-count');

    public function init() {
        $this->_helper->layout->setLayout('normal');
        parent::init();
    }

    public function indexAction() {
        $this->_forward('login');
    }

    public function upgradeAction() {
        $this->_helper->layout->setLayout('upgrade');
    }
    
    public function subscribeAction() {
        if ($this->request->isXmlHttpRequest() && $this->request->isPost()) {
            try {
                $email = $this->request->getParam("email");
                $subscribeModel = $this->getModel('subscribe');
                $subscribeModel->addSubscribe($email);
                echo 1;
                exit;
            } catch (Angel_Exception_User $e) {
                echo 0;
                exit;
            }
        } else {
            
        }
    }

    /**
     * 登录
     */
    public function loginAction() {
        $this->userLogin('show-play', "登录芝士电视");
    }

    /**
     * 注册 
     */
    public function registerAction() {
        $this->userRegister('login', "注册芝士电视", "user");
    }

//    public function isEmailExistAction() {
//        if ($this->request->isXmlHttpRequest() && $this->request->isPost()) {
//
//            $email = $this->request->getParam('email');
//            $result = false;
//            try {
//                $userModel = $this->getModel('user');
//                $result = $userModel->isEmailExist($email);
//            } catch (Angel_Exception_User $e) {
//                $this->_helper->json(0);
//            }
//            // email已经存在返回true，不存在返回false
//            $this->_helper->json(($result === false) ? false : true);
//        }
//    }


    public function isEmailCanBeUsedAction() {
        if ($this->request->isXmlHttpRequest() && $this->request->isPost()) {

            $email = $this->request->getParam('email');
            $result = false;
            try {
                $userModel = $this->getModel('user');
                $result = $userModel->isEmailExist($email);
            } catch (Angel_Exception_User $e) {
                $this->_helper->json(0);
            }
            // email已经存在返回false，不存在返回true
            $this->_helper->json($result ? false : true);
        }
    }

    public function forgotPasswordAction() {
        if ($this->request->isPost()) {

            $email = $this->request->getParam('email');
            $result = false;
            try {
                $userModel = $this->getModel('user');
                $result = $userModel->forgotPassword($email);
            } catch (Angel_Exception_User $e) {
                $this->view->error = $e->getDetail();
                $this->view->re_email = $email;
                $result = false;
            }
            if ($result) {
                $this->view->send = "success";
            }
        }
        $this->view->title = "找回密码";
    }

    public function logoutAction() {
        $this->userLogout('login');
    }
    
    public function deviceAction() {
        $deviceModel = $this->getModel('device');
        $name = $this->request->getParam('name');
        
        $result = $deviceModel->getByName($name);
        
        if ($result) {
            $deviceModel->saveDevice($result->id, $result->name, $result->count);
        }
        else {
            $deviceModel->addDevice($name);
        }
        
         $this->_helper->json(array('data' => 'success', 'code' => 200));
    }
 
    public function deviceCountAction() {
        $deviceModel = $this->getModel('device');
        
        $sys = $this->request->getParam('name');
        
        $result = $deviceModel->getByName($sys);  
        
        if ($result) {
            $this->_helper->json(array('data' => $result->count, 'code' => 200));
        }
        else {
            $this->_helper->json(array('data' => '0', 'code' => 200));
        }
    }
    
    public function versionGetAction() {
        $versionModel = $this->getModel('version');
        
        $sys = $this->request->getParam('sys');
        
        $result = $versionModel->getNewVersion($sys);
        
        if ($result) {
            $version = array();
            
            $version["id"] = $result->id;
            $version["name"] = $result->name;
            $version["sys"] = $result->sys;
            $version["fix"] = $result->fix;
            $version["update"] = $result->update;
            $version["url"] = $result->url;
            
            $this->_helper->json(array('data' => $result->name, 'code' => 200));
        }
        else {
            $this->_helper->json(array('data' => '1.0', 'code' => 0));
        }
    }
}
