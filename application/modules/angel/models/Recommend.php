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
    protected $_document_class = '\Documents\Recommend';
    
    public function addRecommend($specialId, $userId) {
        $data = array("specialId" => $specialId, "userId" => $userId);
        $result = $this->add($data);
        
        return $result;
    }
    
    public function getRecommendIds($userId) {
        $query = $this->_dm->createQueryBuilder($this->_document_class)
                ->field('userId')->equals($userId)->sort('created_at', -1);

        $result = $query
                ->getQuery()
                ->execute();

        if (count($result) == 0) {
            return 0;
        }
        
//        $specialIds = "";
//        //将该用户推荐过的专辑id拼接成 id，id的条件形式
//        foreach ($result as $recommend) {
//            if ($specialIds != "")
//                $specialIds = $specialIds . ",";
//            
//            $specialIds = $specialIds . $recommend->specialId;
//        }

        //返回条件形式的id集合
//        return $specialIds;
        
        return $result;
    }
}
