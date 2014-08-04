<?php

class Angel_ShowController extends Angel_Controller_Action {

    protected $login_not_required = array('detail', 'save-user-category', 'paypal-return', 'paypal-notify');

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

        $categorys = array();
        
        if ($categorys_id != 'none') {
            $tmpCategorys_id = explode(";",$categorys_id);
            
            if (is_array($tmpCategorys_id)) {
                foreach ($tmpCategorys_id as $id) {
                    $categorys[] = $categoryModel->getById($id);
                }
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
    
//    public function keywordGoodAction() {
//        $voteModel = $this->getModel('vote');
//        $programModel = $this->getModel('program');//$_POST['uid']
//        
//        $program_id = $this->getParam('pid');
//        $time = $this->getParam('time');
//        $user_id = $this->getParam('uid');
//
//        $program = $programModel->getById($program_id);
//
//        foreach ($program->keyword as $p) {
//            $vote = $voteModel->getByKeywordIdAndUid($p->id, $user_id);
//            $score = 0;
//
//            if ($vote) {
//                $score = $vote->score;
//
//                if (!$score)
//                    $score = 0;
//
//                $score = $score + 1;
//
//                try {
//                    $voteModel->saveVote($vote->id, $user_id, $p->id, $score);
//                }
//                catch (Exception $e){
//                    $this->_helper->json(array('data' => $e->getMessage(), 'code' => 0));
//                }
//            }
//            else {
//                $score = 1;
//
//                try {
//                    $voteModel->addVote($user_id, $p->id, $score);
//                }
//                catch (Exception $e){
//                    $this->_helper->json(array('data' => $e->getMessage(), 'code' => 0));
//                }
//            }  
//
//            $this->_helper->json(array('data' => 'save success!', 'code' => 200));
//        }
//    }
    
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
    
//    public function keywordBadAction() {
//        $voteModel = $this->getModel('vote');
//        $programModel = $this->getModel('program');
//
//        $program_id = $this->getParam('pid');
//        $user_id = $this->getParam('uid');
//
//        $program = $programModel->getById($program_id);
//
//        foreach ($program->keyword as $p) {
//            $vote = $voteModel->getByKeywordIdAndUid($p->id, $user_id);
//            $score = 0;
//
//            if ($vote) {
//                $score = $vote->score;
//
//                if (!$score)
//                    $score = 0;
//
//                $score = $score - 1;
//
//                try {
//                    $voteModel->saveVote($vote->id, $user_id, $p->id, $score);
//                }
//                catch (Exception $e){
//                    $this->_helper->json(array('data' => $e->getMessage(), 'code' => 0));
//                }
//            }
//            else {
//                $score = -1;
//
//                try {
//                    $voteModel->addVote($user_id, $p->id, $score);
//                }
//                catch (Exception $e){
//                    $this->_helper->json(array('data' => $e->getMessage(), 'code' => 0));
//                }
//            }  
//        }
//
//        $this->_helper->json(array('data' => 'save success!', 'code' => 200));
//    }
    
    public function paypalReturnAction() {
        //获取 PayPal 交易流水号 tx 
        $tx_token = $_GET['tx']; 
        //定义您的身份标记 
        $auth_token = "CHANGE-TO-YOUR-TOKEN"; 
        //形成验证字符串 
        $req = " cmd=_notify-synch&tx=$tx_token&at=$auth_token"; 
        //将交易流水号及身份标记返回 PayPal 验证 
        $header .= "POST /cgi-bin/webscr HTTP/1.0\r\n"; 
        $header .= "Content-Type: application/x-www-form-urlencoded\r\n"; 
        $header .= "Content-Length: " . strlen($req) . "\r\n\r\n"; 
        $fp = fsockopen ('www.paypal.com', 80, $errno, $errstr, 30); 

        if (!$fp) { 
        // HTTP ERROR 
        } 
        else { 
            fputs ($fp, $header . $req); 
            //获取返回数据 
            $res = ''; 
            $headerdone = false; 

            while (!feof($fp)) { 
                $line = fgets ($fp, 1024); 
                if (strcmp($line, "\r\n") == 0) { 
                    //获取头 
                    $headerdone = true; 
                }else if ($headerdone){ 
                //获取主体内容 
                    $res .= $line; 
                } 
            } 
            //解析获取内容 
            $lines = explode("\n", $res); 
            $keyarray = array(); 

            if (strcmp ($lines[0], "SUCCESS") == 0) { 

                for ($i=1; $i<count($lines);$i++){ 
                    list($key,$val) = explode("=", $lines[$i]); 
                    $keyarray[urldecode($key)] = urldecode($val); 
                 } 
                //检查交易付款状态 payment_status 是否为  „Completed‟ 
                //检查交易流水号 txn_id 是否已经被处理过 
                //检查接收 EMAIL receiver_email 是否为您的 PayPal 中已经注册的 EMAIL 
                //检查金额 mc_gross 是否正确 
                //…… 
                //处理此次付款明细 
                //该付款明细所有变量可参考： 
                //https://www.paypal.com/IntegrationCenter/ic_ipn-pdt-variable-reference.html 
                $name = $keyarray['first_name'] . ' ' . $keyarray['last_name']; 
                $itemname = $keyarray['item_name']; 
                $amount = $keyarray['mc_gross']; 

                echo ("<p><h3>Thank you for you purchase!</h3></p>"); 
                echo ("<b>Payment Details:</b><br>\n"); 
                echo ("<li>Name: $name</li>\n"); 
                echo ("<li>Item: $itemname</li>\n"); 
                echo ("<li>Amount: $amount</li>\n"); 
            }else if (strcmp ($lines[0], "FAIL") == 0) { 
                //获取付款明细失败，记录并检查 
            } 
        } 

        fclose ($fp);
    }
    
    public function paypalNotifyAction() {
        //从 PayPal 出读取 POST 信息同时添加变量„cmd‟ 
        $req = 'cmd=_notify-validate'; 

        foreach ($_POST as $key => $value) { 
            $value = urlencode(stripslashes($value)); 
            $req .= "&$key=$value"; 
        } 
        //建议在此将接受到的信息记录到日志文件中以确认是否收到 IPN 信息 
        //将信息 POST 回给 PayPal 进行验证 
        $header .= "POST /cgi-bin/webscr HTTP/1.0\r\n"; 
        $header .= "Content-Type:application/x-www-form-urlencoded\r\n"; 
        $header .= "Content-Length:" . strlen($req) ."\r\n\r\n"; 
        //在 Sandbox 情况下，设置： 
        //$fp = fsockopen(„www.sandbox.paypal.com‟,80,$errno,$errstr,30); 
        $fp = fsockopen ('www.paypal.com', 80, $errno, $errstr, 30); 
        //将 POST 变量记录在本地变量中 
        //该付款明细所有变量可参考： 
        //https://www.paypal.com/IntegrationCenter/ic_ipn-pdt-variable-reference.html 
        $item_name = $_POST['item_name']; 
        $item_number = $_POST['item_number']; 
        $payment_status = $_POST['payment_status']; 
        $payment_amount = $_POST['mc_gross']; 
        $payment_currency = $_POST['mc_currency']; 
        $txn_id = $_POST['txn_id']; 
        $receiver_email = $_POST['receiver_email']; 
        $payer_email = $_POST['payer_email']; 
        //… 
        //判断回复 POST 是否创建成功 
        if (!$fp) { 
        //HTTP 错误 
        }else { 
            //将回复 POST 信息写入 SOCKET 端口 
            fputs ($fp, $header .$req); 
            //开始接受 PayPal 对回复 POST 信息的认证信息 
            while (!feof($fp)) { 
                $res = fgets ($fp, 1024); 
        //已经通过认证 
                if (strcmp ($res, "VERIFIED") == 0) { 
                //检查付款状态 
                //检查 txn_id 是否已经处理过 
                //检查 receiver_email 是否是您的 PayPal 账户中的 EMAIL 地址 
                //检查付款金额和货币单位是否正确 
                //处理这次付款，包括写数据库 
                }else if (strcmp ($res, "INVALID") == 0) { 
                //未通过认证，有可能是编码错误或非法的 POST 信息 
                } 
            } 

            fclose ($fp); 
        } 
    } 
}
