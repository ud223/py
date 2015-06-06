<?php

namespace Documents;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/** @ODM\Document */
class Uvote extends AbstractDocument {
    //活动ID
    /** @ODM\String */
    protected $meet_id;
    //首选日期
    /** @ODM\String */
    protected $first_date;
    //次选日期
    /** @ODM\String */
    protected $second_date;
    //建议人ID
    /** @ODM\String */
    protected $user_id;
} 