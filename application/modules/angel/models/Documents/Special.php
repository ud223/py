<?php

namespace Documents;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/** @ODM\Document */
class Special extends AbstractDocument {

    /** @ODM\String */
    protected $special_name;

    /** @ODM\String */
    protected $author_id;

    /** @ODM\ReferenceMany(targetDocument="\Documents\Photo") */
    protected $photo = array();
    
    /** @ODM\String */
    protected $cover_path;

    /** @ODM\String */
    protected $programs_id;

}
