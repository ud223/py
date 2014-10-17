<?php

namespace Documents;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/** @ODM\Document */
class Comments  extends AbstractDocument{
    /** @ODM\String */
    protected $text;
    
    /** @ODM\String */
    protected $program_id;
    
    /** @ODM\Float */
    protected $time_at;
    
    /** @ODM\Int */
    protected $up;
    
    /** @ODM\ReferenceOne(targetDocument="\Documents\User") */
    protected $user;
    
    /** @ODM\String */
    protected $type;
    
    /** @ODM\Int */
    protected $hot;
}
