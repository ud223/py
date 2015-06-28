<?php

class Angel_IndexController extends Angel_Controller_Action {

    protected $login_not_required = array('index', 'product', 'category', 'login', 'register', 'email-validation', 'is-email-can-be-used', 'forgot-password');

    public function init() {
        $this->_helper->layout->setLayout('normal');
        parent::init();
    }

    public function indexAction() {
        $productModel = $this->getModel('product');

        $tmp_products = $productModel->getLastByCount("4");

        $products = array();

        foreach ($tmp_products as $p) {
            $path = "";

            if (count($p->photo)) {
                try {
                    if ($p->photo[0]->name) {
                        $path = $this->bootstrap_options['image.photo_path'];

                        $path = $this->view->photoImage($p->photo[0]->name . $p->photo[0]->type, 'main');
                    }
                } catch (Doctrine\ODM\MongoDB\DocumentNotFoundException $e) {
                    // 图片被删除的情况
                }
            }

            $products[] = array("id"=>$p->id, "name"=>$p->name, "name_en"=>$p->name_en, "photo"=> $path);
        }

//        var_dump($products); exit;

        $this->view->products = $products;
    }

    /***************************************************************
     * 用户处理
     *
     * *************************************************************/
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
        if ($this->request->isPost()) {
            $this->userLogin('show-play', "登录芝士电视");
        }
        else {
            //第一次请求先判断是否移动端浏览器,如果是移动端浏览器就跳转到移动端注册页面
            if ($this->isMobile()) {
                $loginPath = $this->view->url(array(), 'phone-login') ;

                $this->_redirect($loginPath);
            }
        }
    }

    /**
     * 注册
     */
    public function registerAction() {
        if ($this->request->isPost()) {
            $this->userRegister('login', "注册芝士电视", "user");
        }
        else {
            //第一次请求先判断是否移动端浏览器,如果是移动端浏览器就跳转到移动端注册页面
            if ($this->isMobile()) {
                $registerPath = $this->view->url(array(), 'phone-register') ;

                $this->_redirect($registerPath);
            }
        }
    }

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

    /**************************************************************
     * 前台产品处理
     *
     * ***********************************************************/
    public function categoryListAction() {
        $productModel = $this->getModel('product');
        $categoryModel = $this->getModel('category');

        $category_id = $this->getParam('category_id');

        $tmp_products = $productModel->getAll(false);
        $products = array();

        foreach ($tmp_products as $p) {
            $path = "";

            if ($category_id != "all" && $p->category_id != $category_id)
                continue;

            if (count($p->photo)) {
                try {
                    if ($p->photo[0]->name) {
                        $path = $this->bootstrap_options['image.photo_path'];

                        $path = $this->view->photoImage($p->photo[0]->name . $p->photo[0]->type, 'main');
                    }
                } catch (Doctrine\ODM\MongoDB\DocumentNotFoundException $e) {
                    // 图片被删除的情况
                }
            }

            $products[] = array("id"=>$p->id, "name"=>$p->name, "name_en"=>$p->name_en, "photo"=>$path);
        }

        $this->view->products = $products;
        $categorys = $categoryModel->getAll(false);

        foreach ($categorys as $c) {
            if ($c->id == $category_id) {
                $this->view->category_id = $c->id;
                $this->view->category = $c->name;
                $this->view->category_en = $c->name_en;

                break;
            }
        }

        $this->view->categorys = $categorys;
    }

    public function productInfoAction() {
        $notFoundMsg = '未找到目标产品';
        $productModel = $this->getModel('product');
        $categoryModel = $this->getModel('category');

        $id = $this->getParam('id');

        if ($id) {
            $target = $productModel->getById($id);

            if (!$target) {
                $this->_redirect($this->view->url(array(), 'manage-result') . '?error=' . $notFoundMsg);
            }

            $this->view->model = $target;
            $photo = $target->photo;
            $first_photo = false;

            if ($photo) {
                $saveObj = array();
                foreach ($photo as $p) {
                    try {
                        $name = $p->name;
                    } catch (Doctrine\ODM\MongoDB\DocumentNotFoundException $e) {
                        $this->view->imageBroken = true;
                        continue;
                    }

                    if (!$first_photo) {
                        $first_photo = $this->view->photoImage($p->name . $p->type);
                    }

                    $saveObj[] = $this->view->photoImage($p->name . $p->type, 'small');
                }
                if (!count($saveObj))
                    $saveObj = false;
                $this->view->photo = $saveObj;
            }

            $this->view->first_photo = $first_photo;
            $categorys = $categoryModel->getAll(false);

            foreach ($categorys as $c) {
                if ($c->id == $target->category_id) {
                    $this->view->category_id = $c->id;
                    $this->view->category = $c->name;
                    $this->view->category_en = $c->name_en;

                    break;
                }
            }
        } else {
            $this->_redirect($this->view->url(array(), 'manage-result') . '?error=' . $notFoundMsg);
        }
    }
}
