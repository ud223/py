<?php

class Angel_ShowController extends Angel_Controller_Action {

    protected $login_not_required = array('detail', 'save-user-category', 'download-android', 'download-ios', 'upload-file', 'play', 'fi-add', 'fi-list', 'user-category-list', 'remove-user-category', 'phone-play', 'special-program-list', 'special-recommend', 'program-add-count', 'captions-get', 'comments-get');

    public function init() {
        parent::init();
        $this->_helper->layout->setLayout('main');
    }

     protected function getTmpFile($uid) {
         $commentsModel = $this->getModel('Comments');
         
        $result = $commentsModel->getSaveImagePath() . $uid;
        
        return $result;
    }
    
//    模糊查询使用范例
//    $parameters = array();
//    $options = array();
//
//    $parameters['name'] = new MongoRegex("/龙珠/i");
//
//    $result = $specialModel->getLikeQuery($parameters, $options);

    public function playAction() {
        $this->view->login_goto_url = $this->view->url(array(), 'login') . '?goto=' . $this->request->getRequestUri();
        $this->view->register_goto_url = $this->view->url(array(), 'register') . '?goto=' . $this->request->getRequestUri();

        $recommendModel = $this->getModel('recommend');
        $specialModel = $this->getModel('special');

        $special_id = $this->request->getParam('special');
        $program_id = $this->request->getParam('program');

        $specialBean = false;

        $played_special_id = $_COOKIE["sid"];
        $played_program_id = $_COOKIE["pid"];

        //如果没有专辑id或当前url 专辑id等于上一次的播放专辑id，重新获取推荐
        if (!$special_id) {
            // 未请求专辑ID
            //未登录且有一次播放记录
            if (!$this->me && $played_special_id) {
                $this->view->isLogin = 0;

                if ($this->isMobile()) {
                    $this->_helper->layout->setLayout('mobile');
                    $this->render('phone-play ');
                }

                $loginPath = $this->view->url(array(), 'login');

                $this->_redirect($loginPath);

                return;
            }
            // 随机获取一个新的专辑并且redirect到获取到的专辑地址
            // 如/play?special=xxxxxx
            $specialBean = $this->getRecommendSpecial($played_special_id);

            $playPath = $this->view->url(array(), 'show-play') . '/' . $specialBean->id;

            $this->_redirect($playPath);
        } else {
            $cur_program = false;

            $specialBean = $specialModel->getById($special_id);
            // 由于专辑ID一定存在， 而节目ID可能存在
            // 首先根据专辑ID获取专辑，以及所有专辑包含的节目
            // 如果获取到了节目ID，指示页面播放指定节目，否则播放第一首节目
            //如果当前专辑不存在或已被删除
            if (!$specialBean) {
                $this->_redirect($this->view->url(array(), 'not-found'));
            } else {
                $result = $this->getSpecialInfo($specialBean);
                $this->view->title = $this->bootstrap_options['site']['name'] . ' | ' . $result['name'];

                if (count($result["programs"])) {
                    //如果没有查询到节目id就直接播放当前专辑第一个
                    $cur_program = $result["programs"][0];
                    //根据program_id 获取当前要播放的节目
                    if ($program_id) {
                        foreach ($result["programs"] as $p) {
                            if ($p['id'] == $program_id) {
                                $cur_program = $p;
                                $this->view->title = $this->view->title . ' | ' . $cur_program['name'];
                                break;
                            }
                        }
                    }

                    if ($this->me) {
                        //获取当前需要推荐的用户ID
                        $user_id = $this->me->getUser()->id;
                        //保存推荐记录  可能调整一下位置
                        $recommendModel->addRecommend($specialBean->id, $user_id);
                    }
                    
                    $pid = $cur_program['id'];
                    
                    $this->programAddCount($pid);
                    
                    setcookie('sid', $specialBean->id, time() + 3600 * 24, "/");
                    setcookie('pid', $cur_program['id'], time() + 3600 * 24, "/");
                    $this->view->cur_program = $cur_program;
                    $this->view->resource = $result;
                    $this->view->isLogin = 1;
                } else {
                    //如果为假，就是没有根据专辑id找到对应的专辑，跳到404页面
                }
            }
        }
        
        // 判断用户来自于PC端还是手机端，render不同的模板和Layout
        if ($this->isMobile()) {
            $this->_helper->layout->setLayout('mobile');
            $this->render('phone-play ');
        }
    }

    protected function getRecommendSpecial($curSpecialId) {
        $specialModel = $this->getModel('special');
        $recommendModel = $this->getModel('recommend');
        $hotModel = $this->getModel("hot");

        if ($this->me) {

            $user = $this->me->getUser();
            //获取当前需要推荐的用户ID
            $userId = $user->id;

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
        }

        //没有热点，也没有没看过的视频，
        if (!$special) {
            //没有获取到当前视频id的极端情况
            if (!$curSpecialId) {
                $special = $specialModel->getLastOne();
            } else {
                $tmpSpecial = $specialModel->getById($curSpecialId);

                $special = $specialModel->getNext($tmpSpecial);

                if (!$special) {
                    $special = $specialModel->getLastOne();
                }
            }
        }

        return $special;
    }

    protected function getSpecialInfo($special) {
        $userModel = $this->getModel('user');
        $favouriteModel = $this->getModel('favourite');
        //获取该专辑上传达人
        $author = $userModel->getById($special->authorId);

        $like = 0;

        if ($this->me) {
            $favourites = $favouriteModel->getFavouriteByUserId($this->me->getUser()->id);

            foreach ($favourites->special as $p) {
                if ($p->id == $special->id) {
                    $like = 1;

                    break;
                }
            }
        }

        $result["id"] = $special->id;
        $result["name"] = "";
        
        if ($special->name)
            $result["name"] = $special->name;
        
        $result["like"] = $like;

        if ($author)
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

        return $result;
    }

    public function specialRecommendAction() {
        if ($this->request->isPost()) {
            $recommendModel = $this->getModel('recommend');

            $played_special_id = $this->request->getParam('special');

            $specialBean = false;

            if ($played_special_id == 'none')
                $played_special_id = false;

            $specialBean = $this->getRecommendSpecial($played_special_id);

            $result = $this->getSpecialInfo($specialBean);
            //获取当前需要推荐的用户ID
            $user_id = $this->me->getUser()->id;
            //保存推荐记录  可能调整一下位置
            $recommendModel->addRecommend($specialBean->id, $user_id);

            $this->_helper->json(array('data' => $result, 'code' => 200));
        }
    }

    public function saveUserCategoryAction() {
        if ($this->request->isPost()) {
            $categoryModel = $this->getModel('category');
            $userModel = $this->getModel('user');

            $user = $this->me->getUser();
            //获取当前需要推荐的用户ID
            $categories_id = $this->request->getParam('category');

            $categories = array();

            if ($categories_id != 'none') {
                $tmpCategorys_id = explode(";", $categories_id);

                if (is_array($tmpCategorys_id)) {
                    $categories = $categoryModel->getByIds($tmpCategorys_id);
                }
            }

            try {
//                $userModel->saveUser($user, $categories);
                $userModel->save($user->id, array('category' => $categories));
                $this->_helper->json(array('data' => 'save success!', 'code' => 200));
            } catch (Exception $e) {
                $this->_helper->json(array('data' => $e->getMessage(), 'code' => 0));
            }
        }
    }

    public function searchAction() {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $q = trim($this->getParam('q'));
        if ($q) {
            $specialModel = $this->getModel('special');
            $authorModel = $this->getModel('author');
            $userModel = $this->getModel('user');

            $param = array('name' => new MongoRegex("/" . $q . "/i"));

            $users = $userModel->getLikeQuery($param);

            $users_id = array();

            foreach ($users as $u) {
                $users_id[] = $u->id;
            }

            $author = $authorModel->getById($users_id[0]);

            $author_id = $author->id;
            //获取专辑名模糊查询结果集
            $result_1 = $specialModel->getLikeQuery($param);
            //获取作者名模糊查询结果集
            $result_2 = $specialModel->getSpecialByAuthorIds($users_id);

            $specials = array();

            foreach ($result_1 as $p) {
                $specials[] = $p;
            }
            //判断第二次循环中是否已经存在了第一次循环中的专辑，如果已经存在则跳过
            foreach ($result_2 as $p) {
                if (!in_array($p, $specials))
                    $specials[] = $p;
            }

            if (count($specials)) {
                $data = array();
                foreach ($specials as $special) {
                    $id = $special->id;
                    $name = $special->name;
                    $sharing_photo_path = $this->view->serverUrl() . $this->bootstrap_options['image_broken_ico']['small'];
                    if (count($special->photo)) {
                        $photo = $special->photo[0];
                        $sharing_photo_path = $this->view->serverUrl() . $this->view->photoImage($photo->name . $photo->type, 'small');
                    }
                    $data[] = array('id' => $id, 'name' => $name, 'sharing_photo' => $sharing_photo_path);
                }
                // 搜索到数据并返回
                $this->_helper->json(array('data' => $data, 'code' => 200));
            } else {
                // 没有搜索到数据
                $this->_helper->json(array('code' => 404));
            }
        } else {
            // 错误
            $this->_helper->json(array('code' => 500));
        }
    }

    public function userCategoryListAction() {
        $userModel = $this->getModel('user');
        $categoryModel = $this->getModel('category');

        $user = $this->me->getUser();
        $user_id = $user->id;

        $use_category_condition = array( 'isuse' => 1,  );
            
        $result = $categoryModel->getby(false, $use_category_condition);

        $category = array();

        foreach ($result as $p) {
            $like = 0;

            foreach ($user->category as $c) {
                if ($p->id == $c->id) {
                    $like = 1;

                    break;
                }
            }

            $category[] = array('id' => $p->id, 'name' => $p->name, 'like' => $like);
        }

        $this->_helper->json(array('data' => $category, 'code' => 200));
    }

    public function removeUserCategoryAction() {
        if ($this->request->isPost()) {
            $result = 0;
            // POST METHOD
            $category_id = $this->getParam('id');
            $user_id = $this->me->getUser()->id;

            if ($category_id) {
                $userModel = $this->getModel('user');

                $result = $userModel->removeUserCategory($user_id, $category_id);
            }
            echo $result;
            exit;
        }
    }

    public function categoryListAction() {
        if ($this->request->isPost()) {
            $categoryModel = $this->getModel('category');
            $resource = $categoryModel->getAll(false);
            $category = array();
            foreach ($resource as $c) {
                $category[] = array('id' => $c->id, 'name' => $c->name);
            }

            $this->_helper->json(array('data' => $category, 'code' => 200));
        }
    }

    public function keywordVoteAction() {
        if ($this->request->isPost()) {
            $voteModel = $this->getModel('vote');
            $programModel = $this->getModel('program');

            $program_id = $this->getParam('pid');
            $time = intval($this->getParam('time'));
            $user_id = $this->me->getUser()->id;

            $program = $programModel->getById($program_id);

            foreach ($program->keyword as $p) {
                $vote = $voteModel->getByKeywordIdAndUid($p->id, $user_id);
                $score = 0;

                $score = $vote->score;

                if (!$score)
                    $score = 0;

                if ($time < 20) {
                    $score = $score - 1;
                } elseif ($time > 49 && $time < 80) {
                    $score = $score + 1;
                } elseif ($time > 79) {
                    $score = $score + 2;
                } else {
                    $score = $score;
                }

                try {
                    if ($vote) {
                        $voteModel->saveVote($vote->id, $user_id, $p->id, $score);
                    } else {
                        $voteModel->addVote($user_id, $p->id, $score);
                    }
                } catch (Exception $e) {
                    $this->_helper->json(array('data' => $e->getMessage(), 'code' => 0));
                }

                $this->_helper->json(array('data' => 'success', 'code' => 200));
            }
        }
    }

    public function downloadAndroidAction() {
        $version = $this->getParam('version');
        $file_url = $_SERVER['DOCUMENT_ROOT'] . '/download/android/CheeseTV' . $version . '.apk';

        if (!file_exists($file_url)) {
            echo 'file not found!';

            exit;
        } else {
            echo 'file downloading...';
        }

        $file = fopen($file_url, "r");

        Header("Content-type:application/octet-stream");
        Header("Accept-Ranges:bytes");
        Header("Accept-Length:" . filesize($file_url));
        Header("Content-Disposition:attachment;filename=" . 'CheeseTV' . $version . '.apk');

        echo fread($file, filesize($file_url));

        fclose($file);

        exit();
    }

    public function downloadIosAction() {
        $version = $this->getParam('version');
        $file_url = $_SERVER['DOCUMENT_ROOT'] . '/download/ios/CheeseTV' . $version . '.ipa';

        if (!file_exists($file_url)) {
            echo 'file not found!';

            exit;
        } else {
            echo 'file downloading...';
        }

        $file = fopen($file_url, "r");

        Header("Content-type:application/octet-stream");
        Header("Accept-Ranges:bytes");
        Header("Accept-Length:" . filesize($file_url));
        Header("Content-Disposition:attachment;filename=" . 'CheeseTV' . $version . '.ipa');

        echo fread($file, filesize($file_url));

        fclose($file);

        exit();
    }

    public function favouriteAddAction() {
        if ($this->request->isPost()) {
            $favouriteModel = $this->getModel('favourite');
            $specialModel = $this->getModel('special');

            $special_id = $this->getParam('sid');
            $user_id = $this->me->getUser()->id;

            $favourite = $favouriteModel->getFavouriteByUserId($user_id);

            foreach ($favourite->special as $p) {
                if ($p->id == $special_id) {
                    $this->_helper->json(array('data' => '该专辑已被收藏!', 'code' => 0));

                    return;
                }
            }

            $special = $specialModel->getById($special_id);

            try {
                if ($favourite) {
                    $favouriteModel->saveFavourite($favourite, $special);
                } else {
                    $favouriteModel->addFavourite($user_id, $special);
                }
            } catch (Exception $e) {
                $this->_helper->json(array('data' => $e->getMessage(), 'code' => 0));
            }

            $this->_helper->json(array('data' => 'success', 'code' => 200));
        }
    }

    public function favouriteRemoveAction() {
        if ($this->request->isPost()) {
            $favouriteModel = $this->getModel('favourite');
            $specialModel = $this->getModel('special');

            $special_id = $this->getParam('sid');
            $user_id = $this->me->getUser()->id;

            $favourite = $favouriteModel->getFavouriteByUserId($user_id);

            try {
                if ($favourite) {
                    $favouriteModel->RemoveFavouriteByUserId($favourite, $special_id);
                } else {
                    $this->_helper->json(array('data' => '没有找到任何所属收藏！', 'code' => 0));
                }
            } catch (Exception $e) {
                $this->_helper->json(array('data' => $e->getMessage(), 'code' => 0));
            }

            $this->_helper->json(array('data' => 'success', 'code' => 200));
        }
    }

    //根据当前用户的id来获取收藏专辑
    public function favouriteListAction() {
        $favouriteModel = $this->getModel('favourite');
        if ($this->me) {
            $user_id = $this->me->getUser()->id;
            $favourite = $favouriteModel->getFavouriteByUserId($user_id);

            if ($favourite) {
                foreach ($favourite->special as $p) {
                    $sharing_photo_path = $this->view->serverUrl() . $this->bootstrap_options['image_broken_ico']['small'];
                    if (count($p->photo)) {
                        $photo = $p->photo[0];
                        $sharing_photo_path = $this->view->serverUrl() . $this->view->photoImage($photo->name . $photo->type, 'small');
                    }
                    $result["specials"][] = array("id" => $p->id, "name" => $p->name, "sharing_photo" => $sharing_photo_path);
                }

                $this->_helper->json(array('data' => $result, 'code' => 200));
            } else {
                $this->_helper->json(array('data' => "没有找到任何收藏！", 'code' => 0));
            }
        } else {
            $this->_helper->json(array('data' => "didn't login", 'code' => 0));
        }
    }

    // 判断用户所否收藏了某个专辑
    public function isUserFavouriteAction() {
        if ($this->request->isPost()) {
            if ($this->me) {
                $favouriteModel = $this->getModel('favourite');
                $user_id = $this->me->getUser()->id;
                $special_id = $this->request->getParam('sid');

                $result = $favouriteModel->isUserFavourite($user_id, $special_id);
                if ($result) {
                    $this->_helper->json(array('code' => 200));
                }
            }
            $this->_helper->json(array('code' => 404));
        }
    }

    public function uploadFileAction() {
        $ossModel = $this->getModel('oss');

        $name = $this->getParam('filename');
        $fsize = $this->getParam('size');
        $key = $this->getParam('key');
        $type = $this->getParam('type');
        $ext = $this->getParam('ext');

        try {
            $ossModel->addOss($name, '', $status, $key, $fsize, $type, $ext, null);
        } catch (Exception $e) {
            $this->_helper->json(array('data' => $e->getMessage(), 'code' => 0));
        }

        $this->_helper->json(array('data' => 'success', 'code' => 200));
    }

    public function specialProgramListAction() {
        if ($this->request->isPost()) {
            $specialModel = $this->getModel('special');
            $userModel = $this->getModel('user');
            $favouriteModel = $this->getModel('favourite');

            $special_id = $this->getParam('sid');
            $like = 0;
            $result = array();

            if ($special_id) {
                $special = $specialModel->getById($special_id);

                if ($special) {
                    $author = $userModel->getById($special->authorId); //$authorModel->getAuthorById($special->authorId);
                    //首先判断当前用户是否登录，如果登录再判断当前专辑是否当前已收藏的专辑
                    if ($this->me) {
                        $user_id = $this->me->getUser()->id;

                        $favourites = $favouriteModel->getFavouriteByUserId($user_id);

                        foreach ($favourites->special as $p) {
                            if ($p->id == $special->id) {
                                $like = 1;

                                break;
                            }
                        }
                    }

                    $result["id"] = $special->id;
                    $result["name"] = $special->name;

                    if ($author)
                        $result["author"] = $author->name;

                    $result["photo"] = $this->bootstrap_options['image_broken_ico']['small'];
                    $result["photo_main"] = $this->bootstrap_options['image_broken_ico']['big'];

                    if (count($special->photo)) {
                        $photo = $special->photo[0];
                        $result["photo"] = $this->view->photoImage($photo->name . $photo->type, 'small');
                        $result["photo_main"] = $this->view->photoImage($photo->name . $photo->type, 'main');
                    }

                    foreach ($special->program as $program) {
                        $result["programs"][] = array("id" => $program->id, "name" => $program->name, "time" => $program->time, "like" => $like, "oss_video" => $this->bootstrap_options['oss_prefix'] . $program->oss_video->key, "oss_audio" => $this->bootstrap_options['oss_prefix'] . $program->oss_audio->key);
                    }
                }
            }

            $this->_helper->json(array('data' => $result, 'code' => 200));
        }
    }

    public function getLinkAction() {
        if ($this->request->isPost()) {
            $sid = $this->request->getParam('special');
            $pid = $this->request->getParam('program');
            if ($sid) {
                $url = $this->view->url(array(special => $sid), 'show-play-special');
                if ($pid) {
                    $url = $this->view->url(array(special => $sid, program => $pid), 'show-play-program');
                }
                $url = $this->view->serverUrl() . $url;
                $this->_helper->json(array('url' => $url, 'code' => 200));
            } else {
                $this->_helper->json(array('data' => 'need special id', 'code' => 500));
            }
        }
    }
    
    private function programAddCount($pid) {
        $programModel = $this->getModel('program');

        $result = $programModel->programAddCount($pid);

        if ($result) {
            return true;
        }
        else {
            return false;
        }            
    }

    public function  programAddCountAction() {
        if ($this->request->isPost()) {
            $pid = $this->request->getParam('pid');

            if ($pid) {
                $return = $this->programAddCount($pid);
                
                if ($return) {
                    $this->_helper->json(array('data' => 'success', 'code' => 200));
                }
                else {
                    $this->_helper->json(array('data' => 'is failed', 'code' => 500));
                }
            }
            else {
                $this->_helper->json(array('data' => 'need program id', 'code' => 500));
            }
        }
    }
    
    public function commentsAddAction() {
        if ($this->request->isPost()) {
            $commentsModel = $this->getModel('Comments');
            
            $program_id = $this->request->getParam('pid');
            $time_at = $this->request->getParam('time_at');
            $text = $this->request->getParam('text');
            $type = $this->request->getParam('type');
    
            $user = $this->me->getUser();
            
            $code = 200;
            $message = 'success';
            
            try {
                $commentsModel->addComments('', $text, $program_id, $time_at, $user, $type);
            }
            catch (Exception $e) {
                $code = 500;
                $message = $e->getMessage();
            }
            
            $this->_helper->json(array('data' => $message, 'code' => $code));
        }
    }
    
    public function uploadTestAction() {
        if ($this->request->isPost()) {
            $photo = $_FILES['file'];
        }
    }
    
    public function commentsImageAction() {
        if ($this->request->isPost()) {
            // POST METHOD
            $result = 0;
            $message = 'upload is fail';
            
            $commentsModel = $this->getModel('Comments');
            $upload = new Zend_File_Transfer();

            $upload->addValidator('Size', false, 5120000); //5M

            $uid = uniqid();
            $tmp_file = $this->getTmpFile($uid);

            $upload->addFilter('Rename', $tmp_file);

            if ($upload->isValid()) {
                if ($upload->receive()) {
                    try {
                        $result = $commentsModel->photoHandle($tmp_file);
                    }
                    catch (Exception $e) {
                        $message = $e->getMessage();
                    }
                }
            }
            
            if (!$result) {
                $return = array('data' => $message, 'code' => 500);
                echo json_encode($return);
                exit;
            }
            
            $user = $this->me->getUser();
            $program_id = $this->request->getParam('pid');
            $time_at = $this->request->getParam('time_at');
            $text = $this->request->getParam('text');
            $type = $this->request->getParam('type');
            
            try {
                $result = $commentsModel->addComments($result, $text, $program_id, $time_at, $user, $type);
                
                if ($result) {
                    $result = 200;
                    $message = 'success';
                }
                else {
                    $result = 500;
                }
            }
            catch (Exception $e) {
                $result = 500;
                $message = $e->getMessage();
            }
            
            $return = array('data' => $message, 'code' => $result);
            echo json_encode($return);
            exit;
        }
    }
    
    public function commentsGetAction() {
        $commentsModel = $this->getModel('Comments');

        $program_id = $this->request->getParam('pid');

        $code = 200;
        $message = "success";
        
        if (!$program_id) {
            $code = 500;
            $message = "节目id不能为空!";       
        }
        else {
            try {
                $result = $commentsModel->getHotCommentsByProgramId($program_id);
                
                if ($result) {
                    foreach ($result as $c) {
                        if (!$c->image)
                            $c->image = "";
                        
                        $comments[] = array("id" => $c->id, "image"=>$c->image ,"text" => $c->text, "time_at" => $c->time_at, "pid" => $c->program_id, "up" => $c->up, "hot"=> $c->hot, "type"=> $c->type, "uid"=>$c->user->id, "username"=>$c->user->username);
                    }
                }
            } catch (Exception $e) {
                $code = 500;
                $message = $e->getMessage();   
            }
        }
        
        if ($code != 200) {
            $this->_helper->json(array('data' => $message, 'code' => $code));
        }
        else {
            if (!$comments) {
                $comments = array();
            }
                
            $this->_helper->json(array('data' => $comments, 'code' => $code));
        }
    }
    
    public function commentsUpAction() {
        if ($this->request->isPost()) {
            $commentsModel = $this->getModel('Comments');
            $id = $this->request->getParam('id');

            $code = 200;
            $message = "success";
            $user = $this->me->getUser();
                     
            if ($id) {
                try {
                    $user_up_condition = array('id' => $id, 'up_users.id' => $user->id);
        
                    $comments = $commentsModel->getby(false, $user_up_condition);

                    if (count($comments) == 0) {
                        $return = $commentsModel->up($id, $user);
                    }
                    else {
                        $code = 500;
                        $message = "is upped";
                    }
                }
                catch (Exception $e) {
                    $code = 500;
                    $message = $e->getMessage();
                }
            }
            else {
               $code = 500;
               $message = "need id";
            }
            
            $this->_helper->json(array('data' => $message, 'code' => $code));
        }
    }
}
