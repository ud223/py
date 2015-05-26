<?php

class Angel_IndexController extends Angel_Controller_Action {

    protected $login_not_required = array('index');

    public function init() {
        $this->_helper->layout->setLayout('normal');
        parent::init();
    }
    public function indexAction() {
        $this->_forward('login');
    }
}
