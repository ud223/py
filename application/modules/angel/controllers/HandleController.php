<?php

class Angel_HandleController extends Angel_Controller_Action {

    protected $login_not_required = array('error', 'forbidden', 'not-found', 'sharing');

    public function init() {
        parent::init();
        $this->_helper->layout->setLayout('handle');
    }

    public function errorAction() {
        
    }

    public function forbiddenAction() {
        
    }

    public function notFoundAction() {
        
    }

    public function sharingAction() {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        try {
            $pid = $this->request->getParam('pid');
            $sid = $this->request->getParam('sid');
            $channel = $this->request->getParam('channel');

            $sharingModel = $this->getModel('sharing');
            $data = array('sid' => $sid, 'channel' => $channel);
            $data->sid = $sid;
            if ($pid)
                $data['pid'] = $pid;
            $data->channel = $channel;
            if ($this->me)
                $data['owner'] = $this->me->getUser();
            $sharingModel->add($data);
            $this->_helper->json(array('code' => 200));
        } catch (Exception $ex) {
            $this->_helper->json(array('code' => 500));
        }
    }

}
