<?php

namespace Documents;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/** @ODM\Document */
class Fi extends AbstractDocument{
    /** @ODM\String */
    protected $name;
    
    /** @ODM\String */
    protected $email;
    
    /** @ODM\String */
    protected $phone;
}