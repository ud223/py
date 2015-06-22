<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/6/22
 * Time: 0:52
 */

class Angel_ManageController extends Angel_Controller_Action  {
    public function init() {
        $this->_helper->layout->setLayout('manage');
        parent::init();
    }

    public function manageMainAction() {

    }
} 