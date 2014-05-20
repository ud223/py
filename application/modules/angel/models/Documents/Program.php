<?php
namespace Documents;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/** @ODM\EmbeddedDocument */
class Program extends AbstractDocument{
    
    /** @ODM\String */
    protected $name;

    /** @ODM\ReferenceOne(targetDocument="\Documents\Oss") */
    protected $oss_video;
    
    /** @ODM\ReferenceOne(targetDocument="\Documents\Oss") */
    protected $oss_audio;
    
    /** @ODM\ReferenceOne(targetDocument="\Documents\User") */
    protected $owner;
    
    /** @ODM\String */
    protected $description;

    /** @ODM\ReferenceMany(targetDocument="\Documents\Photo") */
    protected $photo = array();
    
    /** @ODM\String */
    protected $status;                      // 节目状态：online, offline
}