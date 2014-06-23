<?php

class Angel_IndexController extends Angel_Controller_Action {

    protected $login_not_required = array('index', 'login', 'register', 'email-validation', 'is-email-exist', 'forgot-password');

    public function init() {
        parent::init();
    }

    public function indexAction() {
        $this->_forward('login');
    }

    /**
     * 登陆 
     */
    public function loginAction() {
        $this->userLogin('forgot-password', "登录芝士电视");
    }

    /**
     * 注册 
     */
    public function registerAction() {
        $this->userRegister('login', "注册芝士电视", "user");
    }

    public function isEmailExistAction() {
        if ($this->request->isXmlHttpRequest() && $this->request->isPost()) {

            $email = $this->request->getParam('email');
            $result = false;
            try {
                $userModel = $this->getModel('user');
                $result = $userModel->isEmailExist($email);
            } catch (Angel_Exception_User $e) {
                $this->_helper->json(0);
            }
            // email已经存在返回1，不存在返回0
            $this->_helper->json(($result === false) ? 0 : 1);
        }
    }

    public function forgotPasswordAction() {
        if ($this->request->isXmlHttpRequest() && $this->request->isPost()) {

            $email = $this->request->getParam('email');
            $result = false;
            try {
                $userModel = $this->getModel('user');
                $result = $userModel->forgotPassword($email);
            } catch (Angel_Exception_User $e) {
                $this->_helper->json(0);
            }
            $this->_helper->json(($result === false) ? 0 : 1);
        }
    }

    public function logoutAction() {
        $this->userLogout('login');
    }

}
