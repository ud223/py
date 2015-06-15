<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/6/6 0006
 * Time: 下午 2:23
 */

namespace Documents;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/** @ODM\Document */
class DateVote extends AbstractDocument {
    //活动ID
    /** @ODM\String */
    protected $meet_id;
    //首选日期
    /** @ODM\String */
    protected $vote_date;
    //投票选中次数
    /** @ODM\Int */
    protected $vote_num;
//    //次选日期
//    /** @ODM\String */
//    protected $second_date;
    //建议人ID
    /** @ODM\String */
    protected $user_id;
} 