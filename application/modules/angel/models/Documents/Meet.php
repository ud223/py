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
    protected $options_date = array();

    /** @ODM\String */
    protected $selected_date;

    /** @ODM\String */
    protected $time_range;

    /** @ODM\String */
    protected $meet_text;

    /** @ODM\String */
    protected $remark;

    /** @ODM\String */
    protected $year;

    /** @ODM\String */
    protected $month;

    /** @ODM\String */
    protected $day;

    /** @ODM\Int */
    protected $identity;

    /** @ODM\String */
    protected $log;

    /** @ODM\String */
    protected $address;

    /** @ODM\String */
    protected $status;

    //申请人
    /** @ODM\String */
    protected $proposer_id;

    //参与人集合ID
    /** @ODM\Collection */
    protected $users_id = array();

    public function addOptionsDate($p) {
        $this->options_date[] = $p;
    }

    public function clearOptionsDate() {
        $this->options_date = arrray();
    }

    public function addUsersId($p) {
        $this->users_id[] = $p;
    }

    public function clearUsersId() {
        $this->users_id = array();
    }
} 