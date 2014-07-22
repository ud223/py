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
            if ($_COOKIE["userId"] == null || $_COOKIE["userId"] == "") {
                $guidModel = $this->getModel('guid');

                setcookie('userId', $guidModel->toString());
            }
        }
    }

    public function playAction() {
        $uid = "";

        if ($_COOKIE["userId"] == null || $_COOKIE["userId"] == "") {

            $guidModel = $this->getModel('guid');
            $uid = $guidModel->toString();

            setcookie('userId', $uid);

            $this->view->uid = $uid;
        } else {
            $uid = $_COOKIE["userId"];
            $this->view->uid = $uid;
        }
    }

    public function specialRecommendAction() {
        $specialModel = $this->getModel('special');
        $recommendModel = $this->getModel('recommend');
        $programModel = $this->getModel('program');
        $authorModel = $this->getModel('author');
        $categoryModel = $this->getModel('category');
        $hotModel = $this->getModel("hot");
        
        //获取当前需要推荐的用户ID
        $userId = $this->me->getUser()->id;

        if ($userId == null || $userId == "") {
            $userId = $this->request->getParam('uid');
        }

        $curSpecialId = $this->request->getParam('sid');

        if ($curSpecialId == "none")
            $curSpecialId = false;
       
        //获取该用户已经推荐过的专辑ID集合
        $recommends = $recommendModel->getRecommend($userId);       
        $hots = $hotModel->getAll();

        $special = false;    
        $hot_specials = array();
        //获取热点专辑
        foreach ($hots as $hot) {
            foreach ($hot->special as $p) {
                $hot_specials[] = $p;
            }
        }
        
        //获取还没有推荐过的热点专辑
        if (count($hot_specials) > 0) { 
            foreach ($hot_specials as $p) {
                $isRecommend = false;
//                echo $p->id . '<br>';
                if ($recommends) {
                    foreach ($recommends as $r) {
                        if ($p->id == $r->specialId) {
                            $isRecommend = true;

                            break;
                        }
                    } 

                    if (!$isRecommend) {
                        $special = $p;

                        break;
                    }
                }
            }
        }

        //如果没有得到还没有看过的热点专辑
        if (!$special) {
            $recommendIds = array();
            
            foreach ($recommends as $r) {
                $recommendIds[] = $r->specialId;
            }
            
            //获取一个没有推荐过的专辑
            $special = $specialModel->getNotRecommendSpecial($recommendIds);
        }
        
        if (!$special) {    
            //没有热点，也没有没看过的视频，同时还没有获取到当前视频id的极端情况
            if (!$curSpecialId) {
                $special = $specialModel->getLastOne();
            }
            else {
                $tmpSpecial = $specialModel->getById($curSpecialId);
                
                $special = $specialModel->getNext($tmpSpecial);

                if (!$special)
                    $special = $specialModel->getLastOne();
            }
        }
        
        //获取该专辑作者
        $author = $authorModel->getAuthorById($special->authorId);
        
        $result["id"] = $special->id;
        $result["name"] = $special->name;

        if ($author == "")
            $result["author"] = "";
        else
            $result["author"] = $author->name;

        $result["photo"] = $this->bootstrap_options['image_broken_ico']['small'];
        $result["photo_main"] = $this->bootstrap_options['image_broken_ico']['big'];
        
        if (count($special->photo)) {
            $photo = $special->photo[0];
            $result["photo"] = $this->view->photoImage($photo->name . $photo->type, 'small');
            $result["photo_main"] = $this->view->photoImage($photo->name . $photo->type, 'main');
        }

        foreach ($special->program as $program) {
            $result["programs"][] = array("id" => $program->id, "name" => $program->name, "time" => $program->time, "oss_video" => $this->bootstrap_options['oss_prefix'] . $program->oss_video->key, "oss_audio" => $this->bootstrap_options['oss_prefix'] . $program->oss_audio->key);
        }
        
        //保存推荐记录
        $recommendModel->addRecommend($special->id, $userId);
        setcookie('specialId', $special->id);
        $this->_helper->json(array('data' => $result, 'code' => 200));
    }  
}
