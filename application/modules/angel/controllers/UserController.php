<?php

class Angel_UserController extends Angel_Controller_Action {

    protected $login_not_required = array('player-mode');
    
    public function init() {
        $this->_helper->layout->setLayout('normal');
        parent::init();
    }

    public function indexAction() {
        $usermodel = $this->getModel('user');
        $this->view->user = $this->me->getUser();

        if ($this->me->isStartup()) {
            $this->startUpIndex();
        } else {
            $this->investorIndex();
        }
    }

    public function hobbyAction() {
        $categoryModel = $this->getModel('category');
        $user = $this->me->getUser();
        $uid = $user->id; //$this->request->getParam('uid');

        $this->view->model = $user;
        $this->view->userId = $uid;
        $this->view->categories = $categoryModel->getAll(false);
        $this->view->title = "我的兴趣";
        
        $this->view->goto = $this->request->getParam('goto');
    }

    /**
     * 更改保存用户收看/收听习惯
     */
    public function playerModeAction() {
        if ($this->request->isPost()) {
            $key = "player_mode";
            $value = $this->request->getParam('value');
            $result = false;
            if (in_array($value, array('audio', 'video'))) {
                $result = $userMode = $this->getModel('user')->setAttribute($this->me->getUser(), $key, $value);
            }

            // JSON FORMAT RESPONSE
            $code = 200;
            if (!result) {
                $code = 500;
            }
            $this->_helper->json(array('code' => $code));
        }
    }

    public function resetPasswordAction() {
        if ($this->request->isPost()) {
            $old_pwd = $this->_request->getParam('password');
            $new_pwd = $this->_request->getParam('new-password');

            try {
                $result = $this->getModel('user')->resetPassword($this->me->getId(), $old_pwd, $new_pwd);
            } catch (\Angel_Exception_User $e) {
                $result = $e->getDetail();
            }

            if ($result == 1) {
                $this->view->reset = 'success';
            } else {
                $this->view->error = $result;
            }
        }
        $this->view->title = "修改密码";
    }

    public function personalThumbnailAction() {
        $this->_helper->layout->disableLayout();

        $result = 0;
        if ($this->request->isPost()) {
            $upload = new Zend_File_Transfer();

            $upload->addValidator('Size', false, 5120000); //5M

            $utilService = $this->_container->get('util');
            $destination = $utilService->getTmpDirectory() . '/' . uniqid();

            $upload->addFilter('Rename', $destination);

            if ($upload->isValid()) {
                if ($upload->receive()) {
                    $userModel = $this->getModel('user');
                    try {
                        $result = $userModel->addProfileImage($this->me->getUser(), $destination);
                        if ($result) {
                            $result = 1;
                            $this->view->path = $this->view->url(array('image' => $this->me->getProfileImage()), 'profile-image');
                        }
                    } catch (Exception $e) {
                        // image is not accepted
                        $result = 2;
                    }
                }
            }
        }

        $this->view->result = $result;
    }

    public function cropThumbnailAction() {
        $userModel = $this->getModel('user');

        $orig = $userModel->getProfileImagePath($this->me->getProfileImage());

        $x = $this->request->getParam('x', 0);
        $y = $this->request->getParam('y', 0);
        $w = $this->request->getParam('w', 180);
        $h = $this->request->getParam('h', 180);

        $coord = array($x, $y, $w, $h);
        $userModel->generateProfileThumbnail($orig, $coord);

        $imageurl_large = $this->view->url(array('image' => $userModel->getProfileImage($this->me->getProfileImage(), 180)), 'profile-image');
        $imageurl_small = $this->view->url(array('image' => $userModel->getProfileImage($this->me->getProfileImage(), 50)), 'profile-image');

        $this->_helper->json(array("large" => $imageurl_large, "small" => $imageurl_small));
    }


    public function profileAction() {
        
    }

}
