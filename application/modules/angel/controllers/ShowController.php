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
        }
    }
    
    public function specialRecommendAction() {     
        $specialModel = $this->getModel('special');
        $recommendModel = $this->getModel('recommend');
        $programModel = $this->getModel('program');
        $authorModel = $this->getModel('author');
        $categoryModel = $this->getModel('category');
        echo '1';exit;
        //获取当前需要推荐的用户ID
        $userId = $this->me->getUser()->id;
        
        if ($userId == null || $userId == "") {
            $userId = $this->request->getParam('uid');
        }
        
        $curSpecialId = $this->request->getParam('sid');
        
        if ($curSpecialId == "none")
               $curSpecialId = null; 
        //获取该用户已经推荐过的专辑ID集合
        $recommends = $recommendModel->getRecommendIds($userId);
        
        $special = null;
        $recommendIds = "";
        //获取推荐专辑的次数
        $viewCount = count($recommends);
        
        //将该用户推荐过的专辑id拼接成 id，id的条件形式
        foreach ($recommends as $recommend) {
            if ($recommendIds != "")
                $recommendIds = $recommendIds . ",";

            $recommendIds = $recommendIds . $recommend->specialId;
        }
         
        $recommendSpecialIds = "";
        
        if ($recommendIds != "")
            $recommendSpecialIds = explode(",", $recommendIds);
        
//        $this->_helper->json(array('data' => $recommendSpecialIds, 'code' => 200)); exit;
//        if ($viewCount <  1 || $recommends == 0) {//00
        //获取一个没有推荐过的专辑
        $special = $specialModel->getNotRecommendSpecial($recommendSpecialIds, $curSpecialId);
        
 //       $this->_helper->json(array('data' => $special->name, 'code' => 200)); exit;
//        echo $special->id; exit;
        
//        }
//        else {

// 
//            //获取推荐过的专辑集合
//            $tmpSpecials = $specialModel->getByIds($recommendIds);
//            echo count($tmpSpecials); exit;
//            $categoryCount = array();
//            //整理推荐过专辑的集合分类id
//            foreach ($tmpSpecials as $tmpSpecial) {
//                 
//                array_push($categoryCount, $tmpSpecial->categoryId);
//            }
//             
//            $tmpCategoryCount = array_count_values($categroys);
//        
//            $tmpCount = 0;
//            $maxCategory = "";
//            
//            //循环计算推荐最多的专辑ID
//            foreach ($tmpCategoryCount as $categoryCount) {
//                if ($categoryCount[$categoryCount] > $tmpCount) {
//                    $tmpCount = $categoryCount[$categoryCount];
//                    $maxCategory = $categoryCount;
//                }
//            }
//            
//            //得到推荐最多的分类却还未推荐过的专辑
//            $special = $specialModel->getLikeNotRecommendSpecial($recommendIds, $maxCategory);
//            
//            //如果没有在喜欢的分类中找到还没有推荐的专辑，就任意从未推荐的专辑中找一个专辑
//            if (!$special) {
//                $special = $specialModel->getNotRecommendSpecial($recommendIds);
//            }
//        }
        //如果最后都没有找到专辑就推荐最后添加的专辑--极端情况          
        if (empty($special)) {
            $special = $specialModel->getlastOne();
        }
        
        //获取该专辑拥有的节目ID集合
        $ownProgramIds = explode(",", $special->programsId);
        //获取该专辑节目列表
        $programs = $programModel->getProgramBySpecialId($ownProgramIds);
        //获取该专辑作者
        $author = $authorModel->getAuthorById($special->authorId);

        $result["id"] = $special->id;
        $result["name"] = $special->name;
        
        if ($author == "")
            $result["author"] = "";
        else
            $result["author"] = $author->name;
        
        $photo = null;
        
        foreach ($special->photo as $p) {
            $photo = $p;
            
            break;
        }

        if ($photo != null) {
            $result["photo"] = $this->view->photoImage($photo->name . $photo->type, 'small');
        }
        else {
            $result["photo"] = $this->bootstrap_options['image_broken_ico']['small'];
        }
        
        foreach ($programs as $program) {
            $result["programs"][] = array("id" => $program->id, "name" => $program->name, "time" => $program->time, "oss_video" => $this->bootstrap_options['oss_prefix'] . $program->oss_video->key, "oss_audio" => $this->bootstrap_options['oss_prefix'] . $program->oss_audio->key);
        }
        
        //保存推荐记录
        $recommendModel->addRecommend($special->id, $userId);

        $this->_helper->json(array('data' => $result, 'code' => 200));
    }
}
