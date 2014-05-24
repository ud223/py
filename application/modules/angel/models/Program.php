<?php

class Angel_Model_Program extends Angel_Model_AbstractModel {

    protected $_document_class = '\Documents\Program';

    public function getProgramByOss($oss_id, $return_as_paginator = true) {
        $query = $this->_dm->createQueryBuilder($this->_document_class)
                ->field('oss.$id')->equals(new MongoId($oss_id))
                ->sort('created_at', -1);
        $result = null;
        if ($return_as_paginator) {
            $result = $this->paginator($query);
        } else {
            $result = $query->getQuery()->execute();
        }
        return $result;
    }
}
