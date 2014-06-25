<?php

namespace Documents;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/** @ODM\Document */
class Special extends AbstractDocument {

    /** @ODM\String */
    protected $name;

    /** @ODM\String */
    protected $authorId;

    /** @ODM\ReferenceMany(targetDocument="\Documents\Photo") */
    protected $photo = array();

    /** @ODM\String */
    protected $programsId;

    /** @ODM\String */
    protected $categoryId;
}
