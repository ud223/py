<?php

class Angel_ShowController extends Angel_Controller_Action {

    protected $login_not_required = array('detail');

    public function init() {
        parent::init();
        $this->_helper->layout->setLayout('main');
    }

    public function detailAction() {
        $id = $this->request->getParam('id');
        if ($id) {
            $programModel = $this->getModel('program');
            $program = $programModel->getById($id);
            $this->view->model = $program;
            $this->view->title = $program->name;
//            if ($program->oss_video) {
//                $this->view->video_url = $this->bootstrap_options['oss_prefix'] . $program->oss_video->key;
//            }
//            if ($program->oss_audio) {
//                $this->view->audio_url = $this->bootstrap_options['oss_prefix'] . $program->oss_audio->key;
//            }
        }
        
        if (!$this->request->isPost()) {
            if ($_COOKIE["userId"] == null|| $_COOKIE["userId"] == "") {
                $guidModel = $this->getModel('guid');

                setcookie('userId', $guidModel->toString());
            }
        }
    }

    
    
    public function playAction() {
        
        if (!$this->request->isPost()) {
            if ($_COOKIE["userId"] == null|| $_COOKIE["userId"] == "") {
                $guidModel = $this->getModel('guid');

                setcookie('userId', $guidModel->toString());
            }
            
            setcookie('specialId', '123456');
        }
    }
}
