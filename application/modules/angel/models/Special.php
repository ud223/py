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
    
    public function addSpecial($special_name, $author_id, $cover_path, $photo, $programs_id) {
        $data = array("special_name" => $special_name, "author_id" => $author_id, "cover_path" => $cover_path, "photo" => $photo, "programs_id"=>$programs_id);
        $result = $this->add($data);
        
        return $result;
    }

    public function saveSpecial($id, $special_name, $author_id, $cover_path, $photo, $programs_id) {
        $data = array("special_name" => $special_name, "author_id" => $author_id, "cover_path" => $cover_path, "photo" => $photo, "programs_id"=>$programs_id);
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
    
    public function getOwnProgramID() {
        $OwnPrograms_ID = null;
        $result = null;
        $result = $this->_dm->createQueryBuilder($this->_document_class)
            ->getQuery()
            ->execute();
        
        foreach ($result as $special) {
            if (strpos($OwnPrograms_ID, $special->id) > -1) {
                continue;
                
                if ($OwnPrograms_ID == null)
                    $OwnPrograms_ID = $OwnPrograms_ID . ",";
                
                $OwnPrograms_ID = $OwnPrograms_ID . $special->id;
            }
        }
        
        return $OwnPrograms_ID;
    }
}