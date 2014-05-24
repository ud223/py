<?php

class Angel_Model_Author extends Angel_Model_AbstractModel {

    protected $_document_class = '\Documents\Author';
    protected $_document_user_class = '\Documents\User';


    public function saveAuthor($id, $name, $description, $logo) {
        $data = array("name" => $name, "description" => $description, "logo" => $logo);
        $result = $this->save($id, $data, Angel_Exception_Author, Angel_Exception_Author::AUTHOR_NOT_FOUND);
        return $result;
    }

    public function addAuthor($name, $description, $logo) {
        $data = array("name" => $name, "description" => $description, "logo" => $logo);
        $result = $this->add($data);
        return $result;
    }


}
