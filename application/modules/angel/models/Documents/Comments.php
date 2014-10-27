<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of comments
 *
 * @author deanlu
 */
namespace Documents;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/** @ODM\Document */
class Comments extends AbstractDocument {
    /** @ODM\String */
    protected $text;
    
    /** @ODM\String */
    protected $program_id;
    
    /** @ODM\Float */
    protected $time_at;
    
    /** @ODM\Int */
    protected $up;
    
     /** @ODM\ReferenceMany(targetDocument="\Documents\User") */
    protected $up_users;
    
    /** @ODM\ReferenceOne(targetDocument="\Documents\User") */
    protected $user;
    
    /** @ODM\String */
    protected $type;
    
    /** @ODM\Int */
    protected $hot;
    
    public function addProgram(\Documents\User $p) {
        $this->up_users[] = $p;
    }
    
    public function clearProgram() {
        $this->up_users = array();
    }
}
