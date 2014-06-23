<?php

namespace Documents;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/** @ODM\Document */
class Recommend  extends AbstractDocument {
    /** @ODM\String */
    protected $special_id;
    /** @ODM\String */
    protected $user_id;
}
