<?php

namespace Documents;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/** @ODM\Document */
class Keyword extends AbstractDocument {
    /** @ODM\String */
    protected $keycode;

    /** @ODM\String */
    protected $attributes = '';
}
