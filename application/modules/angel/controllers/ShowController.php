<?php

class Angel_ShowController extends Angel_Controller_Action {

    protected $login_not_required = array('detail', 'save-user-category', 'paypal-return', 'paypal-notify', 'paypal-pay', 'download-android');

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
        $uid = $this->me->getUser()->id;
        $this->view->uid = $uid;
        setcookie('userId', $uid);
    }

    public function specialRecommendAction() {
        $specialModel = $this->getModel('special');
        $recommendModel = $this->getModel('recommend');
        $programModel = $this->getModel('program');
        $authorModel = $this->getModel('author');
        $categoryModel = $this->getModel('category');
        $hotModel = $this->getModel("hot");
        $userModel = $this->getModel('user');
        //获取当前需要推荐的用户ID
        $userId = $this->me->getUser()->id;

        if ($userId == null || $userId == "") {
            $userId = $this->request->getParam('uid');
        }
        
        $user = $userModel->getUserById($userId);

        $curSpecialId = $this->request->getParam('sid');

        if ($curSpecialId == "none")
            $curSpecialId = false;
       
        $special = false; 
        
        //获取该用户已经推荐过的专辑ID集合
        $recommends = $recommendModel->getRecommend($userId);       
        
        $recommend_special_id = array();
        
        if ($recommends) {
            foreach ($recommends as $r) {
                $recommend_special_id[] = $r->specialId;
            }
        }

        $like_category_id = array();
        
        foreach ($user->category as $c) {
            $like_category_id[] = $c->id;
        }
        
        //获取喜好热点专辑
        $hot = $hotModel->getLikeNotRecommendHot($like_category_id);

        if ($hot) {
            foreach ($hot as $h) {
                foreach ($h->special as $p) {
                    $isRecommend = false;

                    foreach ($recommend_special_id as $r) {
                        if ($p->id == $r) {
                            $isRecommend = true;
                        }
                    }

                    if (!$isRecommend) {
                        $special = $p;

                        break;
                    }
                }
                
                if ($special)
                    break;
            }
        }
        
        //获取非喜好热点专辑
        if (!$special) {
            //获取喜好热点专辑
            $hot = $hotModel->getNotRecommendHot($like_category_id);

            if ($hot) {
                foreach ($hot as $h) {
                    foreach ($h->special as $p) {
                        $isRecommend = false;
                        
                        foreach ($recommend_special_id as $r) {
                            if ($p->id == $r) {
                                
                                $isRecommend = true;
                                
                                break;
                            }
                        }

                        if (!$isRecommend) {
                            $special = $p;

                            break;
                        }
                    }
                    
                    if ($special) {
                        break;
                    }
                }
            }
        }
        
        //获取喜好分类专辑
        if (!$special) {
            $special = $specialModel->getSpecialByCategoryId($recommend_special_id, $like_category_id);
        }
        
        //获取非喜好分类专辑
        if (!$special) {
            $special = $specialModel->getSpecialByNotCategoryId($recommend_special_id, $like_category_id);
        }
        
        //没有热点，也没有没看过的视频，
        if (!$special) {    
            //没有获取到当前视频id的极端情况
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
    
    public function saveUserCategoryAction() {
        $categoryModel = $this->getModel('category');
        $userModel = $this->getModel('user');
        
        //获取当前需要推荐的用户ID
        $userId = $this->request->getParam('uid');
        $categorys_id = $this->request->getParam('category');

        $categorys = null;
        
        if ($categorys_id != 'none') {
            $tmpCategorys_id = explode(";",$categorys_id);
            
            if (is_array($tmpCategorys_id)) {
                $categorys = $categoryModel->getByIds($tmpCategorys_id);
            }
        }

        try {
            $userModel->saveUser($userId, $categorys);
            
            $this->_helper->json(array('data' => 'save success!', 'code' => 200));
        }
        catch (Exception $e){
            $this->_helper->json(array('data' => $e->getMessage(), 'code' => 0));
        }
    }
 
    public function keywordVoteAction() {
        $voteModel = $this->getModel('vote');
        $programModel = $this->getModel('program');
        
        $program_id = $this->getParam('pid');
        $time = $this->getParam('time');
        $user_id = $this->me->getUser()->id;

        $program = $programModel->getById($program_id);

        foreach ($program->keyword as $p) {
            $vote = $voteModel->getByKeywordIdAndUid($p->id, $user_id);
            $score = 0;

            if ($vote) {
                $score = $vote->score;

                if (!$score)
                    $score = 0;
                
                if ($time <  20)
                    $score = $score - 1;
                else if ($time > 49)
                    $score = $score + 1;
                else if ($time > 79)
                    $score = $score + 2;
                else
                    $score = $score;
                    
                try {
                    $voteModel->saveVote($vote->id, $user_id, $p->id, $score);
                }
                catch (Exception $e){
                    $this->_helper->json(array('data' => $e->getMessage(), 'code' => 0));
                }
            }
            else {
                $score = 1;

                try {
                    $voteModel->addVote($user_id, $p->id, $score);
                }
                catch (Exception $e){
                    $this->_helper->json(array('data' => $e->getMessage(), 'code' => 0));
                }
            }  

            $this->_helper->json(array('data' => 'success', 'code' => 200));
        }
    }
    
    public function downloadAndroidAction() {
        $version = $this->getParam('version');
        $file_url = $_SERVER['DOCUMENT_ROOT'] . '/download/android/CheeseTV'. $version . '.apk';//'../controllers/download/android/'. 
        
        if (!file_exists($file_url)) {
            echo 'file not found!';
            
            exit;
        }
        
        $file = fopen($file_url, "r");
        
        Header("Content-type:application/octet-stream");
        Header("Accept-Ranges:bytes");
        Header("Accept-Length:".  filesize($file_url));
        Header("Content-Disposition:attachment;filename=".'CheeseTV'. $version . '.apk');
        
        echo fread($file, filesize($file_url));
        
        fclose($file);
        
        exit();
    }
}
