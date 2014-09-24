<?php

namespace Documents;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/** @ODM\Document */
class Sharing extends AbstractDocument{
    /** @ODM\String */
    protected $sid;
    
    /** @ODM\String */
    protected $pid;
    
    /** @ODM\String */
    protected $channel;
    
    /** @ODM\ReferenceMany(targetDocument="\Documents\User") */
    protected $owner;
    
}
