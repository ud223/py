<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/5/27 0027
 * Time: 下午 11:04
 */

namespace Documents;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/** @ODM\Document */
class Meet extends AbstractDocument {
    //可选日期集合
    /** @ODM\Collection */
    protected $options_date;

    /** @ODM\String */
    protected $selected_date;

    /** @ODM\String */
    protected $time_range;

    /** @ODM\String */
    protected $meet_text;

    /** @ODM\String */
    protected $remake;

    /** @ODM\String */
    protected $log;

    /** @ODM\String */
    protected $address;

    /** @ODM\String */
    protected $status;

    //申请人
    /** @ODM\ReferenceOne(targetDocument="\Documents\User") */
    protected $proposer;

    /** @ODM\ReferenceMany(targetDocument="\Documents\User") */
    protected $users = array();

    public function addUser(\Documents\User $user) {
        $this->users[] = $user;
    }

    public function clearCategory() {
        $this->users = array();
    }
} 