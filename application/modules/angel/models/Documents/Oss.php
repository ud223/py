<?php
namespace Documents;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/** @ODM\EmbeddedDocument */
class Oss extends AbstractDocument{
    
    /** @ODM\String */
    protected $name;
    
    /** @ODM\ReferenceOne(targetDocument="\Documents\User") */
    protected $owner;

    /** @ODM\String */
    protected $status;                          // OSS文件状态：online, offline
    
    /** @ODM\Int */
    protected $size;                            // 文件大小
    
    /** @ODM\String */
    protected $type = 'video';                  // OSS文件类型：video, audio

    /** @ODM\String */
    protected $ext = '.mp4';                    // OSS文件扩展名

}

?>
