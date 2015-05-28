<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/5/27 0027
 * Time: 下午 11:10
 */

namespace Documents;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/** @ODM\Document */
class Word extends AbstractDocument {
    /** @ODM\String */
    protected $text;

    /** @ODM\String */
    protected $date;

    /** @ODM\ReferenceOne(targetDocument="\Documents\User") */
    protected $users;
} 