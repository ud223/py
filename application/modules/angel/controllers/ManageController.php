<?php

class Angel_ManageController extends Angel_Controller_Action {

    protected $login_not_required = array(
        'login',
        'register',
        'logout'
    );
    protected $SEPARATOR = ';';

    protected function getTmpFile($uid) {
        $utilService = $this->_container->get('util');
        $result = $utilService->getTmpDirectory() . '/' . $uid;
        return $result;
    }

    public function init() {
        parent::init();

        $this->_helper->layout->setLayout('manage');
    }

    public function indexAction() {
        
    }

    public function registerAction() {
        $this->userRegister('manage-login', "注册成为管理员", "admin");
        
        $this->view->ismanage = true;
    }

    public function logoutAction() {
        $this->userLogout('manage-login');
    }

    public function loginAction() {
        $this->userLogin('manage-index', "管理员登录");
    }

    public function programListAction() {
        $page = $this->request->getParam('page');
        if (!$page) {
            $page = 1;
        }
        $programModel = $this->getModel('program');
        $paginator = $programModel->getAll();
        $paginator->setItemCountPerPage($this->bootstrap_options['default_page_size']);
        $paginator->setCurrentPageNumber($page);
        $resource = array();
        
        foreach ($paginator as $r) {
            $path = $this->bootstrap_options['image_broken_ico']['middle'];
            if (count($r->photo)) {
                try {
                    if ($r->photo[0]->name) {
                        $path = $this->view->photoImage($r->photo[0]->name . $r->photo[0]->type, 'main');
                    }
                } catch (Doctrine\ODM\MongoDB\DocumentNotFoundException $e) {
                    // 图片被删除的情况
                }
            }

            $resource[] = array('name' => $r->name,
                'id' => $r->id,
                'sub_title' => $r->sub_title,
                'path' => $path,
                'owner' => $r->owner,
                'oss_video' => $r->oss_video,
                'oss_audio' => $r->oss_audio);
        }

        // JSON FORMAT
        if ($this->getParam('format') == 'json') {
            $this->_helper->json(array('data' => $resource,
                'code' => 200,
                'page' => $paginator->getCurrentPageNumber(),
                'count' => $paginator->count()));
        } else {
            $this->view->paginator = $paginator;
            $this->view->resource = $resource;
            $this->view->title = "节目列表";
            $this->view->specialModel = $this->getModel('special');
        }
    }

    public function programCreateAction() {
        $ossModel = $this->getModel('oss');
        $keyWordModel = $this->getModel('keyword');

        if ($this->request->isPost()) {
            // POST METHOD
            $name = $this->request->getParam('name');
            $sub_title = $this->request->getParam('sub_title');
            $ossVideoId = $this->request->getParam('oss_video');
            $ossAudioId = $this->request->getParam('oss_audio');
            $duration = $this->request->getParam('duration');
            $status = $this->request->getParam('status');
            $description = $this->request->getParam('description');
            $photo = $this->decodePhoto();

            $keyword = $this->request->getParam('keyword');

            $keywords = array();
            
            if (is_array($keyword)) {
                foreach ($keyword as $p) {
                    $keywords[] = $keyWordModel->getById($p);
                }
            }

            $min = $this->request->getParam('min');
            $sec = $this->request->getParam('sec');

            if (empty($min))
                $min = 0;

            $time = $min * 60 + $sec;

            $captions = "";
            $file = $_FILES["captions"];

            if (is_uploaded_file($file["tmp_name"])) {
                $captions = file_get_contents($file["tmp_name"]);
                $isUtf8 = mb_detect_encoding($captions, "UTF-8,GB2312,EUC-CN");
                switch($isUtf8) {
                    case "UTF-8":
                        break;
                    case "EUC-CN":
                        $captions = iconv("EUC-CN", "UTF-8", $captions);
                        break;
                    case "GB2312":
                        $captions = iconv("GB2312", "UTF-8", $captions);
                        break;
                    default:
                        break;
                }
                
                if (!$isUtf8) {
                    $captions = utf8_encode(file_get_contents($file["tmp_name"]));
                }
            }

            $result = false;
            $error = "";
            try {
                $programModel = $this->getModel('program');
                $owner = $this->me->getUser();
                $oss_video = null;
                if ($ossVideoId) {
                    $oss_video = $ossModel->getById($ossVideoId);
                    if (!$oss_video) {
                        $this->_redirect($this->view->url(array(), 'manage-result') . '?error="notfound"');
                    }
                }
                $oss_audio = null;
                if ($ossAudioId) {
                    $oss_audio = $ossModel->getById($ossAudioId);
                    if (!$oss_audio) {
                        $this->_redirect($this->view->url(array(), 'manage-result') . '?error="notfound"');
                    }
                }
                $result = $programModel->addProgram($name, $sub_title, $oss_video, $oss_audio, $author, $duration, $description, $photo, $status, $owner, $keywords, $time, $captions);
            } catch (Angel_Exception_Program $e) {
                $error = $e->getDetail();
            } catch (Exception $e) {
                $error = $e->getMessage();
            }
            if ($result) {
                $this->_redirect($this->view->url(array(), 'manage-result') . '?redirectUrl=' . $this->view->url(array(), 'manage-program-list-home'));
            } else {
                $this->_redirect($this->view->url(array(), 'manage-result') . '?error=' . $error);
            }
        } else {
            // GET METHOD

            $this->view->title = "创建节目";
            $this->view->separator = $this->SEPARATOR;
            $this->view->oss_audio = $ossModel->getBy(false, array('type' => 'audio'));
            $this->view->oss_video = $ossModel->getBy(false, array('type' => 'video'));
            $this->view->keywords = $keyWordModel->getAll(false);
        }
    }

    public function programSaveAction() {
        $id = $this->request->getParam('id');
        $copy = $this->request->getParam('copy');
        $ossModel = $this->getModel('oss');
        $keyWordModel = $this->getModel('keyword');

        if ($this->request->isPost()) {
            // POST METHOD
            $name = $this->request->getParam('name');
            $sub_title = $this->request->getParam('sub_title');
            $ossVideoId = $this->request->getParam('oss_video');
            $ossAudioId = $this->request->getParam('oss_audio');
            $duration = $this->request->getParam('duration');
            $status = $this->request->getParam('status');
            $description = $this->request->getParam('description');
            $photo = $this->decodePhoto();
            $captions = $this->request->getParam('captions');
            $min = $this->request->getParam('min');
            $sec = $this->request->getParam('sec');

            if (empty($min))
                $min = 0;

            $time = $min * 60 + $sec;
            
            $keyword = $this->request->getParam('keyword');

            $keywords = array();
            
            if (is_array($keyword)) {
                foreach ($keyword as $p) {
                    $keywords[] = $keyWordModel->getById($p);
                }
            }

            $result = false;
            $error = "";

            try {
                $programModel = $this->getModel('program');
                $oss_video = null;
                if ($ossVideoId) {
                    $oss_video = $ossModel->getById($ossVideoId);
                    if (!$oss_video) {
                        $this->_redirect($this->view->url(array(), 'manage-result') . '?error="notfound"');
                    }
                }
                $oss_audio = null;
                if ($ossAudioId) {
                    $oss_audio = $ossModel->getById($ossAudioId);
                    if (!$oss_audio) {
                        $this->_redirect($this->view->url(array(), 'manage-result') . '?error="notfound"');
                    }
                }
                if ($copy) {
                    $owner = $this->me->getUser();
                    $result = $programModel->addProgram($name, $sub_title, $oss_video, $oss_audio, $author, $duration, $description, $photo, $status, $owner, $keywords, $time, $captions);
                } else {
                    $result = $programModel->saveProgram($id, $name, $sub_title, $oss_video, $oss_audio, $author, $duration, $description, $photo, $status, $keywords, $time, $captions);
                }
            } catch (Angel_Exception_Program $e) {
                $error = $e->getDetail();
            } catch (Exception $e) {
                $error = $e->getMessage();
            }
            if ($result) {
                $this->_redirect($this->view->url(array(), 'manage-result') . '?redirectUrl=' . $this->view->url(array(), 'manage-program-list-home'));
            } else {
                $this->_redirect($this->view->url(array(), 'manage-result') . '?error=' . $error);
            }
        } else {
            // GET METHOD
            $notFoundMsg = '未找到目标节目';

            $this->view->title = "编辑节目";
            $this->view->separator = $this->SEPARATOR;
            $this->view->keywords = $keyWordModel->getAll(false);

            if ($id) {
                $programModel = $this->getModel('program');
                $photoModel = $this->getModel('photo');
                $target = $programModel->getById($id);
                if (!$target) {
                    $this->_redirect($this->view->url(array(), 'manage-result') . '?error=' . $notFoundMsg);
                }
                $this->view->oss_audio = $ossModel->getBy(false, array('type' => 'audio'));
                $this->view->oss_video = $ossModel->getBy(false, array('type' => 'video'));

                if ($copy) {
                    // 复制一个节目
                    $this->view->title = "复制并创建节目";
                    $this->view->copy = $copy;
                }

                $this->view->model = $target;

                $this->view->min = floor($target->time / 60);
                $this->view->sec = $target->time % 60;

                $photo = $target->photo;

                if ($photo) {
                    $saveObj = array();
                    foreach ($photo as $p) {
                        try {
                            $name = $p->name;
                        } catch (Doctrine\ODM\MongoDB\DocumentNotFoundException $e) {
                            $this->view->imageBroken = true;
                            continue;
                        }
                        $saveObj[$name] = $this->view->photoImage($p->name . $p->type, 'small');
                        if (!$p->thumbnail) {
                            $saveObj[$name] = $this->view->photoImage($p->name . $p->type);
                        }
                    }
                    if (!count($saveObj))
                        $saveObj = false;
                    $this->view->photo = $saveObj;
                }
            } else {
                $this->_redirect($this->view->url(array(), 'manage-result') . '?error=' . $notFoundMsg);
            }
        }
    }

    public function programRemoveAction() {
        if ($this->request->isPost()) {
            $result = 0;
            // POST METHOD
            $id = $this->getParam('id');
            if ($id) {
                $programModel = $this->getModel('program');
                $result = $programModel->remove($id);
            }
            echo $result;
            exit;
        }
    }

    protected function decodePhoto($paramName = 'photo') {
        $paramPhoto = $this->request->getParam($paramName);
        if ($paramPhoto) {
            $paramPhoto = json_decode($paramPhoto);
            $photoModel = $this->getModel('photo');
            $photoArray = array();
            foreach ($paramPhoto as $name => $path) {
                $photoObj = $photoModel->getPhotoByName($name);
                if ($photoObj) {
                    $photoArray[] = $photoObj;
                }
            }
            return $photoArray;
        } else {
            return null;
        }
    }

    public function resultAction() {
        $this->view->error = $this->request->getParam('error');
        $this->view->redirectUrl = $this->request->getParam('redirectUrl');
    }

    public function photoCreateAction() {
        $phototypeModel = $this->getModel('phototype');

        if ($this->request->isPost()) {
            $result = 0;
            // POST METHOD
            $tmp = $this->getParam('tmp');
            $title = $this->getParam('title');
            $description = $this->getParam('description');
            $phototypeId = $this->getParam('phototype');
            $thumbnail = $this->getParam('thumbnail') == "1" ? true : false;

            $phototype = null;
            if ($phototypeId) {
                $phototype = $phototypeModel->getById($phototypeId);
                if (!$phototype) {
                    $this->_redirect($this->view->url(array(), 'manage-result') . '?error="notfound"');
                }
            }
            $owner = $this->me->getUser();
            $photoModel = $this->getModel('photo');
            try {
                $destination = $this->getTmpFile($tmp);
                $result = $photoModel->addPhoto($destination, $title, $description, $phototype, $thumbnail, $owner);
                if ($result) {
                    $result = 1;
                }
            } catch (Exception $e) {
                // image is not accepted
                $result = 2;
            }
            echo $result;
            exit;
        } else {
            // GET METHOD
            $fs = $this->getParam('fs');

            if ($fs) {
                $this->view->fileList = array();
                $f = explode("|", $fs);
                foreach ($f as $k => $v) {
                    $this->view->fileList[] = array('v' => $v, 'p' => $this->getTmpFile($v));
                }
            }
            $this->view->title = "确认保存图片";
            $this->view->phototype = $phototypeModel->getAll(false);
        }
    }

    public function photoUploadAction() {
        if ($this->request->isPost()) {
            // POST METHOD
            $result = 0;
            $upload = new Zend_File_Transfer();

            $upload->addValidator('Size', false, 5120000); //5M

            $uid = uniqid();
            $destination = $this->getTmpFile($uid);

            $upload->addFilter('Rename', $destination);

            if ($upload->isValid()) {
                if ($upload->receive()) {
                    $result = $uid;
                }
            }
            echo $result;
            exit;
        } else {
            // GET METHOD
            $this->view->title = "上传图片";
        }
    }

    public function photoClearcacheAction() {
        if ($this->request->isPost()) {
            // POST METHOD
            $result = 0;
            $utilService = $this->_container->get('util');
            $tmp = $utilService->getTmpDirectory();

            try {
                if ($od = opendir($tmp)) {
                    while ($file = readdir($od)) {
                        unlink($tmp . DIRECTORY_SEPARATOR . $file);
                    }
                }
                $result = 1;
            } catch (Exception $e) {
                $result = 0;
            }
            echo $result;
            exit;
        }
    }

    public function photoListAction() {
        $page = $this->request->getParam('page');
        $phototype = $this->request->getParam('phototype');
        if (!$page) {
            $page = 1;
        }
        $photoModel = $this->getModel('photo');

        $paginator = null;
        if (!$phototype) {
            $paginator = $photoModel->getAll();
        } else {
            $paginator = $photoModel->getPhotoByPhototype($phototype);
        }
        $paginator->setItemCountPerPage($this->bootstrap_options['default_page_size']);
        $paginator->setCurrentPageNumber($page);
        $resource = array();
        foreach ($paginator as $r) {
            $resource[] = array('path' => array('orig' => $this->view->photoImage($r->name . $r->type), 'main' => $this->view->photoImage($r->name . $r->type, 'main'), 'small' => $this->view->photoImage($r->name . $r->type, 'small'), 'large' => $this->view->photoImage($r->name . $r->type, 'large')),
                'name' => $r->name,
                'id' => $r->id,
                'type' => $r->type,
                'thumbnail' => $r->thumbnail,
                'owner' => $r->owner);
        }
        // JSON FORMAT
        if ($this->getParam('format') == 'json') {
            $this->_helper->json(array('data' => $resource,
                'code' => 200,
                'page' => $paginator->getCurrentPageNumber(),
                'count' => $paginator->count()));
        } else {
            $this->view->paginator = $paginator;
            $this->view->resource = $resource;
            $this->view->title = "图片列表";
            $this->view->specialModel = $this->getModel('special');
            $this->view->authorModel = $this->getModel('author');
        }
    }

    public function photoSaveAction() {
        $notFoundMsg = '未找到目标图片';
        $photoModel = $this->getModel('photo');
        $phototypeModel = $this->getModel('phototype');
        $id = $this->request->getParam('id');

        if ($this->request->isPost()) {
            $result = 0;
            // POST METHOD
            $title = $this->request->getParam('title');
            $description = $this->request->getParam('description');
            $phototypeId = $this->request->getParam('phototype');
            $phototype = null;
            if ($phototypeId) {
                $phototype = $phototypeModel->getById($phototypeId);
                if (!$phototype) {
                    $this->_redirect($this->view->url(array(), 'manage-result') . '?error="notfound"');
                }
            }
            try {
                $result = $photoModel->savePhoto($id, $title, $description, $phototype);
            } catch (Angel_Exception_Photo $e) {
                $error = $e->getDetail();
            } catch (Exception $e) {
                $error = $e->getMessage();
            }
            if ($result) {
                $this->_redirect($this->view->url(array(), 'manage-result') . '?redirectUrl=' . $this->view->url(array(), 'manage-photo-list-home'));
            } else {
                $this->_redirect($this->view->url(array(), 'manage-result') . '?error=' . $error);
            }
        } else {
            // GET METHOD
            $this->view->title = "编辑图片";

            if ($id) {
                $target = $photoModel->getById($id);
                $phototype = $phototypeModel->getAll(false);
                if (!$target) {
                    $this->_redirect($this->view->url(array(), 'manage-result') . '?error=' . $notFoundMsg);
                }
                $this->view->model = $target;
                $this->view->phototype = $phototype;
            } else {
                $this->_redirect($this->view->url(array(), 'manage-result') . '?error=' . $notFoundMsg);
            }
        }
    }

    public function photoRemoveAction() {
        if ($this->request->isPost()) {
            $result = 0;
            // POST METHOD
            $id = $this->getParam('id');
            if ($id) {
                $photoModel = $this->getModel('photo');
                $result = $photoModel->removePhoto($id);
            }
            echo $result;
            exit;
        }
    }

    public function phototypeListAction() {
        $page = $this->request->getParam('page');
        if (!$page) {
            $page = 1;
        }
        $phototypeModel = $this->getModel('phototype');
        $photoModel = $this->getModel('photo');
        $paginator = $phototypeModel->getAll();
        $paginator->setItemCountPerPage($this->bootstrap_options['default_page_size']);
        $paginator->setCurrentPageNumber($page);
        $resource = array();
        foreach ($paginator as $r) {
            $resource[] = array('id' => $r->id,
                'name' => $r->name,
                'description' => $r->description,
                'owner' => $r->owner);
        }
        // JSON FORMAT
        if ($this->getParam('format') == 'json') {
            $this->_helper->json(array('data' => $resource,
                'code' => 200,
                'page' => $paginator->getCurrentPageNumber(),
                'count' => $paginator->count()));
        } else {
            $this->view->paginator = $paginator;
            $this->view->resource = $resource;
            $this->view->title = "图片分类列表";
            $this->view->photoModel = $photoModel;
        }
    }

    public function phototypeCreateAction() {
        if ($this->request->isPost()) {
            $result = 0;
            // POST METHOD
            $name = $this->request->getParam('name');
            $description = $this->request->getParam('description');
            $owner = $this->me->getUser();
            $phototypeModel = $this->getModel('phototype');
            try {
                $result = $phototypeModel->addPhototype($name, $description, $owner);
            } catch (Exception $e) {
                $error = $e->getMessage();
            }
            if ($result) {
                $this->_redirect($this->view->url(array(), 'manage-result') . '?redirectUrl=' . $this->view->url(array(), 'manage-phototype-list-home'));
            } else {
                $this->_redirect($this->view->url(array(), 'manage-result') . '?error=' . $error);
            }
        } else {
            // GET METHOD
            $this->view->title = "创建图片分类";
        }
    }

    public function phototypeSaveAction() {
        $notFoundMsg = '未找到目标图片分类';

        if ($this->request->isPost()) {
            $result = 0;
            // POST METHOD
            $id = $this->request->getParam('id');
            $name = $this->request->getParam('name');
            $description = $this->request->getParam('description');
            $phototypeModel = $this->getModel('phototype');
            try {
                $result = $phototypeModel->savePhototype($id, $name, $description);
            } catch (Angel_Exception_Phototype $e) {
                $error = $e->getDetail();
            } catch (Exception $e) {
                $error = $e->getMessage();
            }
            if ($result) {
                $this->_redirect($this->view->url(array(), 'manage-result') . '?redirectUrl=' . $this->view->url(array(), 'manage-phototype-list-home'));
            } else {
                $this->_redirect($this->view->url(array(), 'manage-result') . '?error=' . $error);
            }
        } else {
            // GET METHOD
            $this->view->title = "编辑图片分类";

            $id = $this->request->getParam("id");
            if ($id) {
                $phototypeModel = $this->getModel('phototype');
                $target = $phototypeModel->getById($id);
                if (!$target) {
                    $this->_redirect($this->view->url(array(), 'manage-result') . '?error=' . $notFoundMsg);
                }
                $this->view->model = $target;
            } else {
                $this->_redirect($this->view->url(array(), 'manage-result') . '?error=' . $notFoundMsg);
            }
        }
    }

    public function phototypeRemoveAction() {
        if ($this->request->isPost()) {
            $result = 0;
            // POST METHOD
            $id = $this->getParam('id');
            if ($id) {
                $phototypeModel = $this->getModel('phototype');
                $result = $phototypeModel->remove($id);
            }
            echo $result;
            exit;
        }
    }

    public function authorListAction() {
        $page = $this->request->getParam('page');
        if (!$page) {
            $page = 1;
        }
        $authorModel = $this->getModel('author');
        $programModel = $this->getModel('program');
        $paginator = $authorModel->getAll();
        $paginator->setItemCountPerPage($this->bootstrap_options['default_page_size']);
        $paginator->setCurrentPageNumber($page);
        $resource = array();
        foreach ($paginator as $r) {
            $resource[] = array('id' => $r->id,
                'name' => $r->name,
                'description' => $r->description,
                'logo' => $r->logo);
        }
        // JSON FORMAT
        if ($this->getParam('format') == 'json') {
            $this->_helper->json(array('data' => $resource,
                'code' => 200,
                'page' => $paginator->getCurrentPageNumber(),
                'count' => $paginator->count()));
        } else {
            $this->view->paginator = $paginator;
            $this->view->resource = $resource;
            $this->view->title = "作者列表";
            $this->view->programModel = $programModel;
            $this->view->specialModel = $this->getModel('special');
        }
    }

    public function authorCreateAction() {
        $snsOptions = $this->bootstrap_options['sns'];
        if ($this->request->isPost()) {
            $result = 0;
            // POST METHOD
            $name = $this->request->getParam('name');
            $description = $this->request->getParam('description');

            $intro = array();
            foreach ($snsOptions as $k => $v) {
                $intro[$k] = $this->request->getParam('sns-' . $k);
            }
            $logo = $this->decodePhoto('logo');
            if (is_array($logo) && count($logo) > 0) {
                $logo = $logo[0];
            } else {
                $logo = null;
            }
            $authorModel = $this->getModel('author');
            try {
                $result = $authorModel->addAuthor($name, $description, $intro, $logo);
            } catch (Exception $e) {
                $error = $e->getMessage();
            }
            if ($result) {
                $this->_redirect($this->view->url(array(), 'manage-result') . '?redirectUrl=' . $this->view->url(array(), 'manage-author-list-home'));
            } else {
                $this->_redirect($this->view->url(array(), 'manage-result') . '?error=' . $error);
            }
        } else {
            // GET METHOD
            $this->view->title = "创建作者分类";
            $this->view->snsOptions = $snsOptions;
        }
    }

    public function authorRemoveAction() {
        if ($this->request->isPost()) {
            $result = 0;
            // POST METHOD
            $id = $this->getParam('id');
            if ($id) {
                $authorModel = $this->getModel('author');
                $result = $authorModel->remove($id);
            }
            echo $result;
            exit;
        }
    }

    public function authorSaveAction() {
        $notFoundMsg = '未找到目标作者';
        $snsOptions = $this->bootstrap_options['sns'];

        if ($this->request->isPost()) {
            $result = 0;
            // POST METHOD
            $id = $this->request->getParam('id');
            $name = $this->request->getParam('name');
            $description = $this->request->getParam('description');
            $logo = $this->decodePhoto('logo');
            $intro = array();
            foreach ($snsOptions as $k => $v) {
                $intro[$k] = $this->request->getParam('sns-' . $k);
            }
            if (is_array($logo) && count($logo) > 0) {
                $logo = $logo[0];
            } else {
                $logo = null;
            }
            $authorModel = $this->getModel('author');
            try {
                $result = $authorModel->saveAuthor($id, $name, $description, $intro, $logo);
            } catch (Angel_Exception_Author $e) {
                $error = $e->getDetail();
            } catch (Exception $e) {
                $error = $e->getMessage();
            }
            if ($result) {
                $this->_redirect($this->view->url(array(), 'manage-result') . '?redirectUrl=' . $this->view->url(array(), 'manage-author-list-home'));
            } else {
                $this->_redirect($this->view->url(array(), 'manage-result') . '?error=' . $error);
            }
        } else {
            // GET METHOD
            $this->view->title = "编辑作者";
            $this->view->snsOptions = $snsOptions;

            $id = $this->request->getParam("id");
            if ($id) {
                $authorModel = $this->getModel('author');
                $target = $authorModel->getById($id);
                if (!$target) {
                    $this->_redirect($this->view->url(array(), 'manage-result') . '?error=' . $notFoundMsg);
                }
                $this->view->model = $target;

                $logo = $target->logo;
                $saveObj = array();
                if ($logo) {
                    try {
                        $name = $logo->name;
                        $saveObj[$name] = $this->view->photoImage($logo->name . $logo->type, 'small');
                        if (!$logo->thumbnail) {
                            $saveObj[$name] = $this->view->photoImage($logo->name . $logo->type);
                        }
                        if (!count($saveObj))
                            $saveObj = false;
                    } catch (Doctrine\ODM\MongoDB\DocumentNotFoundException $e) {
                        $this->view->imageBroken = true;
                    }
                }
                $this->view->logo = $saveObj;
            } else {
                $this->_redirect($this->view->url(array(), 'manage-result') . '?error=' . $notFoundMsg);
            }
        }
    }

    public function categoryCreateAction() {

        $categoryModel = $this->getModel('category');

        if ($this->request->isPost()) {
            $result = 0;
            // POST METHOD
            $name = $this->request->getParam('name');
            $description = $this->request->getParam('description');
            $parentId = $this->request->getParam('parent');
            try {
                $result = $categoryModel->addCategory($name, $description, $parentId, $level);
            } catch (Exception $e) {
                $error = $e->getMessage();
            }
            if ($result) {
                $this->_redirect($this->view->url(array(), 'manage-result') . '?redirectUrl=' . $this->view->url(array(), 'manage-category-list-home'));
            } else {
                $this->_redirect($this->view->url(array(), 'manage-result') . '?error=' . $error);
            }
        } else {
            // GET METHOD
            $this->view->title = "创建分类";
            $this->view->categories = $categoryModel->getAll(false);
        }
    }

    public function categoryListAction() {
        $categoryModel = $this->getModel('category');
        $programModel = $this->getModel('program');
        $root = $categoryModel->getRoot();
        $this->view->title = "分类列表";
        $this->view->categoryModel = $categoryModel;
        $this->view->programModel = $programModel;
        if (count($root)) {
            $resource = array();
            foreach ($root as $r) {
                $resource[] = array('root' => $r, 'children' => $categoryModel->getByParent($r->id));
            }
            // JSON FORMAT
            if ($this->getParam('format') == 'json') {
                $this->_helper->json(array('data' => $resource,
                    'code' => 200));
            } else {
                $this->view->resource = $resource;
                $this->view->specialMode = $this->getModel('special');
            }
        }
    }

    public function categoryRemoveAction() {
        if ($this->request->isPost()) {
            $result = 0;
            // POST METHOD
            $id = $this->getParam('id');

            if ($id) {
                $categoryModel = $this->getModel('category');
                $result = $categoryModel->remove($id);
            }
            echo $result;
            exit;
        }
    }

    public function categorySaveAction() {
        $notFoundMsg = '未找到目标分类';
        $categoryModel = $this->getModel('category');

        if ($this->request->isPost()) {
            $result = 0;
            // POST METHOD
            $id = $this->request->getParam('id');
            $name = $this->request->getParam('name');
            $description = $this->request->getParam('description');
            $parentId = $this->request->getParam('parent');
            try {
                $result = $categoryModel->saveCategory($id, $name, $description, $parentId);
            } catch (Angel_Exception_Category $e) {
                $error = $e->getDetail();
            } catch (Exception $e) {
                $error = $e->getMessage();
            }
            if ($result) {
                $this->_redirect($this->view->url(array(), 'manage-result') . '?redirectUrl=' . $this->view->url(array(), 'manage-category-list-home'));
            } else {
                $this->_redirect($this->view->url(array(), 'manage-result') . '?error=' . $error);
            }
        } else {
            // GET METHOD
            $this->view->title = "编辑分类";
            $this->view->categories = $categoryModel->getAll(false);

            $id = $this->request->getParam("id");
            if ($id) {
                $categoryModel = $this->getModel('category');
                $target = $categoryModel->getById($id);
                if (!$target) {
                    $this->_redirect($this->view->url(array(), 'manage-result') . '?error=' . $notFoundMsg);
                }
                $this->view->model = $target;
            } else {
                $this->_redirect($this->view->url(array(), 'manage-result') . '?error=' . $notFoundMsg);
            }
        }
    }

    public function ossCreateAction() {
        if ($this->request->isPost()) {
            $result = 0;
            // POST METHOD
            $name = $this->request->getParam('name');
            $description = $this->request->getParam('description');
            $status = $this->request->getParam('status');
            $key = $this->request->getParam('key');
            $fsize = $this->request->getParam('fsize');
            $type = $this->request->getParam('type');
            $ext = $this->request->getParam('ext');
            $owner = $this->me->getUser();
            $ossModel = $this->getModel('oss');
            try {
                $result = $ossModel->addOss($name, $description, $status, $key, $fsize, $type, $ext, $owner);
            } catch (Exception $e) {
                $error = $e->getMessage();
            }
            if ($result) {
                $this->_redirect($this->view->url(array(), 'manage-result') . '?redirectUrl=' . $this->view->url(array(), 'manage-oss-list-home'));
            } else {
                $this->_redirect($this->view->url(array(), 'manage-result') . '?error=' . $error);
            }
        } else {
            // GET METHOD
            $this->view->title = "上传多媒体文件";
            $this->view->oss_prefix = $this->bootstrap_options['oss_prefix'];
        }
    }

    public function ossSaveAction() {
        $notFoundMsg = '未找到目标文件';

        if ($this->request->isPost()) {
            $result = 0;
            // POST METHOD
            $id = $this->request->getParam('id');
            $name = $this->request->getParam('name');
            $description = $this->request->getParam('description');
            $status = $this->request->getParam('status');
            $key = $this->request->getParam('key');
            $fsize = $this->request->getParam('fsize');
            $type = $this->request->getParam('type');
            $ext = $this->request->getParam('ext');
            $ossModel = $this->getModel('oss');
            try {
                $result = $ossModel->saveOss($id, $name, $description, $status, $key, $fsize, $type, $ext);
            } catch (Angel_Exception_Oss $e) {
                $error = $e->getDetail();
            } catch (Exception $e) {
                $error = $e->getMessage();
            }
            if ($result) {
                $this->_redirect($this->view->url(array(), 'manage-result') . '?redirectUrl=' . $this->view->url(array(), 'manage-oss-list-home'));
            } else {
                $this->_redirect($this->view->url(array(), 'manage-result') . '?error=' . $error);
            }
        } else {
            // GET METHOD
            $this->view->title = "编辑多媒体文件";
            $this->view->oss_prefix = $this->bootstrap_options['oss_prefix'];

            $id = $this->request->getParam("id");
            if ($id) {
                $ossModel = $this->getModel('oss');
                $target = $ossModel->getById($id);
                if (!$target) {
                    $this->_redirect($this->view->url(array(), 'manage-result') . '?error=' . $notFoundMsg);
                }
                $this->view->model = $target;
            } else {
                $this->_redirect($this->view->url(array(), 'manage-result') . '?error=' . $notFoundMsg);
            }
        }
    }

    public function ossListAction() {
        $page = $this->request->getParam('page');
        if (!$page) {
            $page = 1;
        }
        $ossModel = $this->getModel('oss');
        $programModel = $this->getModel('program');
        $paginator = $ossModel->getAll();
        $paginator->setItemCountPerPage($this->bootstrap_options['default_page_size']);
        $paginator->setCurrentPageNumber($page);
        $resource = array();
        foreach ($paginator as $r) {
            $resource[] = array('id' => $r->id,
                'name' => $r->name,
                'type' => $r->type,
                'description' => $r->description);
        }
        // JSON FORMAT
        if ($this->getParam('format') == 'json') {
            $this->_helper->json(array('data' => $resource,
                'code' => 200,
                'page' => $paginator->getCurrentPageNumber(),
                'count' => $paginator->count()));
        } else {
            $this->view->paginator = $paginator;
            $this->view->resource = $resource;
            $this->view->title = "多媒体文件列表";
            $this->view->programModel = $programModel;
        }
    }

    public function ossRemoveAction() {
        if ($this->request->isPost()) {
            $result = 0;
            // POST METHOD
            $id = $this->getParam('id');
            if ($id) {
                $ossModel = $this->getModel('oss');
                $result = $ossModel->remove($id);
            }
            echo $result;
            exit;
        }
    }

    public function keywordCreateAction() {
        $keyWordModel = $this->getModel('keyword');

        if ($this->request->isPost()) {
            $result = 0;
            // POST METHOD
            $name = $this->request->getParam('name');

            try {
                $result = $keyWordModel->addKeyWord($name);
            } catch (Exception $e) {
                $error = $e->getMessage();
            }
            if ($result) {
                $this->_redirect($this->view->url(array(), 'manage-result') . '?redirectUrl=' . $this->view->url(array(), 'manage-keyword-list-home'));
            } else {
                $this->_redirect($this->view->url(array(), 'manage-result') . '?error=' . $error);
            }
        } else {
            // GET METHOD
            $this->view->title = "创建关键词";
        }
    }

    public function keywordListAction() {
        $keyWordModel = $this->getModel('keyword');
        $page = $this->request->getParam('page');

        if (!$page) {
            $page = 1;
        }

//        $root = $keyWordModel->getRoot();
        $paginator = $keyWordModel->getAll();
        $paginator->setItemCountPerPage($this->bootstrap_options['default_page_size']);
        $paginator->setCurrentPageNumber($page);

        $resource = array();

        foreach ($paginator as $r) {
            $resource[] = array(
                'id' => $r->id,
                'name' => $r->name
            );
        }
        // JSON FORMAT
        if ($this->getParam('format') == 'json') {
            $this->_helper->json(array('data' => $resource,
                'code' => 200));
        } else {
            $this->view->resource = $resource;
            $this->view->title = "关键词列表";
            $this->view->paginator = $paginator;
            $this->view->programModel = $this->getModel('program');
        }
    }

    public function keywordRemoveAction() {
        if ($this->request->isPost()) {
            $result = 0;
            // POST METHOD
            $id = $this->getParam('id');

            if ($id) {
                $keyWordModel = $this->getModel('keyword');
                $result = $keyWordModel->remove($id);
            }

            echo $result;
            exit;
        }
    }

    public function keywordSaveAction() {
        $notFoundMsg = '未找到目标分类';
        $keyWordModel = $this->getModel('keyword');

        if ($this->request->isPost()) {
            $result = 0;
            // POST METHOD
            $id = $this->request->getParam('id');
            $name = $this->request->getParam('name');

            try {
                $result = $keyWordModel->saveKeyword($id, $name);
            } catch (Angel_Exception_Keyword $e) {
                $error = $e->getDetail();
            } catch (Exception $e) {
                $error = $e->getMessage();
            }
            if ($result) {
                $this->_redirect($this->view->url(array(), 'manage-result') . '?redirectUrl=' . $this->view->url(array(), 'manage-keyword-list-home'));
            } else {
                $this->_redirect($this->view->url(array(), 'manage-result') . '?error=' . $error);
            }
        } else {
            // GET METHOD
            $this->view->title = "编辑关键词";
            $id = $this->request->getParam("id");

            if ($id) {
                $target = $keyWordModel->getById($id);

                if (!$target) {
                    $this->_redirect($this->view->url(array(), 'manage-result') . '?error=' . $notFoundMsg);
                }

                $this->view->model = $target;
            } else {
                $this->_redirect($this->view->url(array(), 'manage-result') . '?error=' . $notFoundMsg);
            }
        }
    }

    //--------------------------------------------------------------------

    public function specialCreateAction() {
        $specialModel = $this->getModel('special');
        $authorModel = $this->getModel('author');
        $programModel = $this->getModel('program');
        $categoryModel = $this->getModel('category');

        if ($this->request->isPost()) {
            $result = 0;
            // POST METHOD
            $name = $this->request->getParam('name');
            $authorId = $this->request->getParam('authorId');
            $photo = $this->decodePhoto();
            $categoryId = $this->request->getParam('categoryId');
            $tmp_program_id = $this->request->getParam('programs');

            $programs_id = explode(",", $tmp_program_id);
            
            $programs = array();
            
            if (is_array($programs_id)) {
                foreach ($programs_id as $p) {
                    $programs[] = $programModel->getById($p);
                }
            }

            try {
                $result = $specialModel->addSpecial($name, $authorId, $photo, $programs, $categoryId);
            } catch (Exception $e) {
                $error = $e->getMessage();
            }
            if ($result) {
                $this->_redirect($this->view->url(array(), 'manage-result') . '?redirectUrl=' . $this->view->url(array(), 'manage-special-list-home'));
            } else {
                $this->_redirect($this->view->url(array(), 'manage-result') . '?error=' . $error);
            }
        } else {
            $result = $specialModel->getAll(false);
                
            $ownProgramIds = "";

            foreach ($result as $special) {
                if (!$special->program) {
                    continue;
                }

                foreach ($special->program as $p) {
                    if ($ownProgramIds != "")
                        $ownProgramIds = $ownProgramIds . ",";

                    $ownProgramIds = $ownProgramIds . $p->id;
                }
            }

            $programIds = explode(",", $ownProgramIds);
            
            $not_own_programs = $programModel->getProgramNotOwn($programIds);
            
            $this->view->title = "创建专辑";
            $this->view->authors = $authorModel->getAll(false);
            $this->view->not_own_programs = $not_own_programs;
            $this->view->categorys = $categoryModel->getRoot();
        }
    }

    public function specialListAction() {
        $specialModel = $this->getModel('special');
        $page = $this->request->getParam('page');

        if (!$page) {
            $page = 1;
        }

        $root = $specialModel->getRoot();
        $paginator = $specialModel->getAll();
        $paginator->setItemCountPerPage($this->bootstrap_options['default_page_size']);
        $paginator->setCurrentPageNumber($page);

        $resource = array();
        setcookie("userId", "");
        foreach ($root as $r) {
            $resource[] = array(
                'id' => $r->id,
                'name' => $r->name//,
                    // 'photo' => $r->cover_path
            );
        }

        // JSON FORMAT
        if ($this->getParam('format') == 'json') {
            $this->_helper->json(array('data' => $resource,
                'code' => 200));
        } else {
            $this->view->resource = $resource;
            $this->view->title = "专辑列表";
            $this->view->paginator = $paginator;
        }
    }

    public function specialRemoveAction() {
        if ($this->request->isPost()) {
            $result = 0;
            // POST METHOD
            $id = $this->getParam('id');
            if ($id) {
                $specialModel = $this->getModel('special');
                $result = $specialModel->remove($id);
            }
            echo $result;
            exit;
        }
    }

    public function specialSaveAction() {
        $notFoundMsg = '未找到目标分类';
        $specialModel = $this->getModel('special');
        $authorModel = $this->getModel('author');
        $programModel = $this->getModel('program');
        $categoryModel = $this->getModel('category');

        if ($this->request->isPost()) {
            $result = 0;
            // POST METHOD
            
            $id = $this->request->getParam('id');
            $name = $this->request->getParam('name');
            $authorId = $this->request->getParam('authorId');

            $photo = $this->decodePhoto();
            $categoryId = $this->request->getParam('categoryId');
            $tmp_program_id = $this->request->getParam('programs');
            
            $programs_id = explode(",", $tmp_program_id);
            
            $programs = array();
            
            if (is_array($programs_id)) {
                foreach ($programs_id as $p) {
                    $programs[] = $programModel->getById($p);
                }
            }

            try {
                $result = $specialModel->saveSpecial($id, $name, $authorId, $photo, $programs, $categoryId);
            } catch (Angel_Exception_Special $e) {
                $error = $e->getDetail();
            } catch (Exception $e) {
                $error = $e->getMessage();
            }

            if ($result) {
                $this->_redirect($this->view->url(array(), 'manage-result') . '?redirectUrl=' . $this->view->url(array(), 'manage-special-list-home'));
            } else {
                $this->_redirect($this->view->url(array(), 'manage-result') . '?error=' . $error);
            }
        } else {
            // GET METHOD
            $this->view->title = "编辑专辑";

            $id = $this->request->getParam("id");

            if ($id) {
                $target = $specialModel->getById($id);

                if (!$target) {
                    $this->_redirect($this->view->url(array(), 'manage-result') . '?error=' . $notFoundMsg);
                }

                $result = $specialModel->getAll(false);
                
                $ownProgramIds = "";

                foreach ($result as $special) {
                    if (!$special->program) {
                        continue;
                    }
                    
                    foreach ($special->program as $p) {
                        if ($ownProgramIds != "")
                            $ownProgramIds = $ownProgramIds . ",";

                        $ownProgramIds = $ownProgramIds . $p->id;
                    }
                }

                $programIds = explode(",", $ownProgramIds);

                $this->view->model = $target;
                $photo = $target->photo;

                if ($photo) {
                    $saveObj = array();
                    foreach ($photo as $p) {
                        try {
                            $name = $p->name;
                        } catch (Doctrine\ODM\MongoDB\DocumentNotFoundException $e) {
                            $this->view->imageBroken = true;
                            continue;
                        }
                        $saveObj[$name] = $this->view->photoImage($p->name . $p->type, 'small');
                        if (!$p->thumbnail) {
                            $saveObj[$name] = $this->view->photoImage($p->name . $p->type);
                        }
                    }
                    if (!count($saveObj))
                        $saveObj = false;
                    $this->view->photo = $saveObj;
                }

                $own_programs = $programModel->getProgramOwn($programIds);
                $not_own_programs = $programModel->getProgramNotOwn($programIds);
                
                $this->view->authors = $authorModel->getAll(false);
                $this->view->own_programs = $target->program;
                $this->view->not_own_programs = $not_own_programs;
                $this->view->categorys = $categoryModel->getAll();
            } else {
                $this->_redirect($this->view->url(array(), 'manage-result') . '?error=' . $notFoundMsg);
            }
        }
    }
    
    public function versionCreateAction() {
        $versionModel = $this->getModel('version');

        if ($this->request->isPost()) {
            $result = 0;
            // POST METHOD
            $name = $this->request->getParam('name');
            $sys = $this->request->getParam('sys');
            $fix = $this->request->getParam('fix');
            $update = $this->request->getParam('update');
            $url = $this->request->getParam('url');

            try {
                $result = $versionModel->addVersion($name, $sys, $fix, $update, $url);
            } catch (Exception $e) {
                $error = $e->getMessage();
            }
            if ($result) {
                $this->_redirect($this->view->url(array(), 'manage-result') . '?redirectUrl=' . $this->view->url(array(), 'manage-version-list'));
            } else {
                $this->_redirect($this->view->url(array(), 'manage-result') . '?error=' . $error);
            }
        } else {
            $result = $versionModel->getAll();

            $this->view->title = "创建版本";
        }
    }
    
    public function versionSaveAction() {
        $notFoundMsg = '未找到目标分类';
        $versionModel = $this->getModel('version');

        if ($this->request->isPost()) {
            $result = 0;
            // POST METHOD
            $id = $this->request->getParam('id');
            $name = $this->request->getParam('name');
            $sys = $this->request->getParam('sys');
            $fix = $this->request->getParam('fix');
            $update = $this->request->getParam('update');
            $url = $this->request->getParam('url');
            
            try {
                $result = $versionModel->saveVersion($id, $name, $sys, $fix, $update, $url);
            } catch (Angel_Exception_version $e) {
                $error = $e->getDetail();
            } catch (Exception $e) {
                $error = $e->getMessage();
            }

            if ($result) {
                $this->_redirect($this->view->url(array(), 'manage-result') . '?redirectUrl=' . $this->view->url(array(), 'manage-version-list'));
            } else {
                $this->_redirect($this->view->url(array(), 'manage-result') . '?error=' . $error);
            }
        } else {
            // GET METHOD
            $this->view->title = "编辑版本";

            $id = $this->request->getParam("id");

            if ($id) {
                $target = $versionModel->getById($id);

                if (!$target) {
                    $this->_redirect($this->view->url(array(), 'manage-result') . '?error=' . $notFoundMsg);
                }
                
                $this->view->model = $target;
            } else {
                $this->_redirect($this->view->url(array(), 'manage-result') . '?error=' . $notFoundMsg);
            }
        }
    }
    
    public function versionListAction() {
        $versionModel = $this->getModel('version');
        $page = $this->request->getParam('page');
        
        if (!$page) {
            $page = 1;
        }
        
        $paginator = $versionModel->getAll();
        $paginator->setItemCountPerPage($this->bootstrap_options['default_page_size']);
        $paginator->setCurrentPageNumber($page);

        $resource = array();

        foreach ($paginator as $r) {
            $resource[] = array(
                'id' => $r->id,
                'name' => $r->name, 
                'sys' => $r->sys
            );
        }

        $this->view->resource = $resource;
        $this->view->title = "版本列表";
        $this->view->paginator = $paginator;
    }

    public function versionRemoveAction() {
        if ($this->request->isPost()) {
            $result = 0;
            // POST METHOD
            $id = $this->getParam('id');
            if ($id) {
                $versionModel = $this->getModel('version');
                
                $result = $versionModel->remove($id);
            }
            echo $result;
            exit;
        }
    }
    
    public function hotListAction() {
        $categoryModel = $this->getModel('category');
        $page = $this->request->getParam('page');
        
        if (!$page) {
            $page = 1;
        }
        
        $paginator = $categoryModel->getAll();
        $result = $categoryModel->getRoot();
        $paginator->setItemCountPerPage($this->bootstrap_options['default_page_size']);
        $paginator->setCurrentPageNumber($page);

//        $resource = array();
//
//        foreach ($paginator as $r) {
//           echo $r->id; exit;
////            $resource[] = array(
////                'id' => $r->id,
////                'name' => $r->name
////            );
//        }
        
        $this->view->resource = $result;
        $this->view->title = "分类列表";
        $this->view->paginator = $paginator;
    }
    
    public function hotSaveAction() {
        $notFoundMsg = '未找到目标分类';
        $categroyModel = $this->getModel("category");
        $hotModel = $this->getModel('hot');
        $specialModel = $this->getModel("special");

        if ($this->request->isPost()) {
            $result = false;
            // POST METHOD
            $id = $this->request->getParam('id');
            
            $specials_id = $this->request->getParam('specials');
            
            $specials = $specialModel->getByIds($specials_id);
            $hot = $hotModel->getById($id);

            try {
                if (!$hot) {
                    $result = $hotModel->addHot($id, $specials);
                }
                else {
                    $result = $hotModel->saveHot($hot->id, $id, $specials);
                }
            } catch (Angel_Exception_version $e) {
                $error = $e->getDetail();
            } catch (Exception $e) {
                $error = $e->getMessage();
            }

            if ($result) {
                $this->_redirect($this->view->url(array(), 'manage-result') . '?redirectUrl=' . $this->view->url(array(), 'manage-hot-list'));
            } else {
                $this->_redirect($this->view->url(array(), 'manage-result') . '?error=' . $error);
            }
        } else {
            // GET METHOD
            $this->view->title = "编辑热点";

            $id = $this->request->getParam("id");

            if ($id) {
                $target = $categroyModel->getById($id);

                if (!$target) {
                    $this->_redirect($this->view->url(array(), 'manage-result') . '?error=' . $notFoundMsg);
                }
 
                $specials = $specialModel->getByCategory($id);
                $result = $hotModel->getByCategoryId($id);
    
                $this->view->specials = $specials;
                $this->view->model = $target;
                $this->view->ownSpecials = $result->special;
            } else {
                $this->_redirect($this->view->url(array(), 'manage-result') . '?error=' . $notFoundMsg);
            }
        }
    }
    
    public function ossPreviewAction() {
        $video = urldecode($this->request->getParam('video'));
 
        $this->view->video = $video;
    }
}
