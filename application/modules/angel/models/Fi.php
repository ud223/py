<?php

class Angel_Model_Fi extends Angel_Model_AbstractModel {
    protected $_document_class = '\Documents\Fi';

    public function addFi($name, $email, $phone) {
        $fi = new $this->_document_class();
        
        $fi->name = $name;
        $fi->email = $email;
        $fi->phone = $phone;
        
        try {
            $this->_dm->persist($fi);
            $this->_dm->flush();

            $result = $fi->id;
        } catch (Exception $e) {
            $this->_logger->info(__CLASS__, __FUNCTION__, $e->getMessage() . "\n" . $e->getTraceAsString());
            throw new Angel_Exception_Program(Angel_Exception_Program::ADD_PROGRAM_FAIL);
        }
        
        return $result;
    }
    
    public function getAll() {
        $query = $this->_dm->createQueryBuilder($this->_document_class)->sort('created_at', -1);

        $result = $query
                ->getQuery()
                ->execute();
        
        return $result;
    }
}
