<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Special
 *
 * @author vince
 */
class Angel_Model_Special extends Angel_Model_AbstractModel {
    protected $_document_class = '\Documents\Special';
    protected $_author_class = '\Documents\Author';
    
    public function addSpecial($name, $authorId, $photo, $programsId, $categoryId) {
        $data = array("name" => $name, "authorId" => $authorId, "photo" => $photo, "programsId"=>$programsId, "categoryId"=>$categoryId);
        $result = $this->add($data);
        
        return $result;
    }

    public function saveSpecial($id, $name, $authorId, $photo, $programsId, $categoryId) {
        
        $data = array("name" => $name, "authorId" => $authorId, "photo" => $photo, "programsId"=>$programsId, "categoryId"=>$categoryId);
        
        $result = $this->save($id, $data);
        
        return $result;
    }

    public function getRoot() {
        $query = $this->_dm->createQueryBuilder($this->_document_class)->sort('created_at', -1);

        $result = $query
                ->getQuery()
                ->execute();

        return $result;
    }
    
    public function getlastOne() {
        $query = $this->_dm->createQueryBuilder($this->_document_class)->sort('created_at', -1);

        $result = $query
                ->getQuery()
                ->getSingleResult();

        return $result;
    }
    
    /**
     * 根据id获取Keyword document
     * 
     * @param string $id
     * @return mix - when the user found, return the user document
     */
    public function getById($id) {
        $result = false;
        
        $special = $this->_dm->createQueryBuilder($this->_document_class)
                ->field('id')->equals($id)
                ->getQuery()
                ->getSingleResult();

        if (!empty($special)) {
            $result = $special;
        }
        
        return $result;
    }
    
    public function getByIds($ids) {
        
        $result = $this->_dm->createQueryBuilder($this->_document_class)
                ->field('id')->in($id)->getQuery();
        echo $result->name; exit;
        return $result;
    }
    
    public function getOwnProgramId() {
        $ownProgramsId = null;
        $result = null;
        $result = $this->_dm->createQueryBuilder($this->_document_class)
            ->getQuery()
            ->execute();
        
        foreach ($result as $special) {
            if (strpos($ownProgramsId, $special->id) > -1) {
                continue;
                
                if ($ownProgramsId == null)
                    $ownProgramsId = $ownProgramsId . ",";
                
                $ownProgramsId = $ownProgramsId . $special->id;
            }
        }
        
        return $ownProgramsId;
    }
    
    public function getNotRecommendSpecial($recommendIds, $curSpecialId) {
        
        
        $query = null;
        $query = $this->_dm->createQueryBuilder($this->_document_class)->sort('created_at', -1);
//        if ($recommendIds == "") {
//            $query = $this->_dm->createQueryBuilder($this->_document_class)->sort('created_at', -1);
//        }
//        else {
//            $query = $this->_dm->createQueryBuilder($this->_document_class)->field('id')->notIn($recommendIds)->sort('created_at', -1);
//        }
        
        $result = $query->getQuery();//->getSingleResult();
        $special = null;
        $isRecommend = FALSE;
        
        $specials = array();
        
        foreach ($result as $tmp) {
            $specials[] = $tmp;
        }
        
        foreach ($specials as $tmpSpecial) {
            if (count($specials) < count($recommendIds) || $recommendIds[0] == "") {
                
                $index = rand(0, count($specials) - 1);
                
                $special = $specials[$index];
                
                if ($curSpecialId != null) {
                    if ($special->id == $curSpecialId) {
                        $index = rand(0, count($specials) - 1);
                
                        $special = $specials[$index];
                        
                        if ($special->id == $curSpecialId) {
                            $index = rand(0, count($specials) - 1);
                
                            $special = $specials[$index];
                            
                            if ($special->id == $curSpecialId) {
                                $index = rand(0, count($specials) - 1);

                                $special = $specials[$index];

                                if ($special->id == $curSpecialId) {
                                    $index = rand(0, count($specials) - 1);

                                    $special = $specials[$index];
                            
                                }
                            }
                        }
                    }
                }
                
                break;
            }
            
            $isRecommend = FALSE;
            
            foreach ($recommendIds as $recommendId) {
                if ($tmpSpecial->id == $recommendId) {
                    $isRecommend = TRUE;
                    
                    break;
                }
            }

            if ($isRecommend == FALSE) {
                $special = $tmpSpecial;
                
                return $special;
            }
        }
        
        return $special;
    }
    
    public function getLikeNotRecommendSpecial($recommendIds, $categoryId) {
        $query = $this->_dm->createQueryBuilder($this->_document_class)
                ->field('id')->notIn($recommendIds)->field('categoryId')->equals($categoryId)->sort('created_at', -1);

        $result = $query
                ->getQuery()
                ->getSingleResult();

        if (!empty($result))
            return false;
        
        return $result;
    }
    
    public function getSpecialByPhoto($photoId) {
        $query = $this->_dm->createQueryBuilder($this->_document_class)->sort('created_at', -1);

        $result = $query
                ->getQuery();
        if (count($result) == 0)
            return false;

        foreach ($result as $special) {
            $photoes = $special->photo;
            
            if (count($photoes) == 0)
                continue;
            
            foreach ($photoes as $photo) {
                if ($photo->id == $photoId)
                    return true;
            }
        }
        
        return false;
    }
    
    public function getSpecialByCategory($categoryId) {
        $query = $this->_dm->createQueryBuilder($this->_document_class)->sort('created_at', -1);

        $result = $query
                ->getQuery();
       
        if (count($result) == 0)
            return false;

        foreach ($result as $special) {
            $tmpCategoryId = $special->categoryId;
            
            if ($tmpCategoryId == $categoryId)
                return true;
        }
        
        return false;
    }
    
    public function getSpecialByAuthor($authorId) {
        $query = $this->_dm->createQueryBuilder($this->_document_class)->sort('created_at', -1);

        $result = $query
                ->getQuery();
       
        if (count($result) == 0)
            return false;

        foreach ($result as $special) {
            $tmpAuthorId = $special->authorId;
            
            if ($tmpAuthorId == $authorId)
                return true;
        }
        
        return false;
    }
    
    public function getSpecialByProgram($programId) {
        $query = $this->_dm->createQueryBuilder($this->_document_class)->sort('created_at', -1);

        $result = $query
                ->getQuery();
       
        if (count($result) == 0)
            return false;
        
        foreach ($result as $special) {
            $tmpPrograms = $special->programsId;
            
            if ($tmpPrograms == "")
                continue;
            
            $programIds = explode(",", $tmpPrograms);

            foreach ($programIds as $program) {
                if ($program == $programId)
                    return true;
            }
        }
        
        return false;
    }
}