<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Recommend
 *
 * @author vince
 */
class Angel_Model_Recommend extends Angel_Model_AbstractModel {
    protected $_author_class = '\Documents\Recommend';
    
    public function addRecommend($special_id, $user_id) {
        $data = array("special_id" => $special_id, "user_id" => $user_id);
        $result = $this->add($data);
        
        return $result;
    }
    
    public function getRecommendIds($user_id) {
        $query = $this->_dm->createQueryBuilder($this->_document_class)
                ->field('user_id')->in($user_id)->sort('created_at', -1);
        
        $result = $query
                -->getQuery()
                ->execute();
        
        $specialIds = "";
        //将该用户推荐过的专辑id拼接成 id，id的条件形式
        foreach ($result as $recommend) {
            if ($Special_IDs == "")
                $Special_IDs = $Special_IDs . ",";
            
            $Special_IDs = $Special_IDs . $recommend->special_id;
        }
        //返回条件形式的id集合
        return $result;
    }
}
