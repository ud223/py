<?php
/**
 *  @author powerdream5
 *  用户document，包含创业者，投资人和担保人 
 */

namespace Documents;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/** @ODM\Document */
class User extends AbstractDocument {
    /** @ODM\String */
    protected $openid;

    /** @ODM\Int */
    protected $subscribe;

    /** @ODM\String */
    protected $nickname;
    
    /** @ODM\Int */
    protected $sex;
    
    /** @ODM\String */
    protected $language;

    /** @ODM\String */
    protected $city;

    /** @ODM\String */
    protected $province;

    /** @ODM\String */
    protected $country;

    /** @ODM\String */
    protected $headimgurl;

    /** @ODM\String */
    protected $subscribe_time;

    /** @ODM\Int */
    protected $last_time;

    /** @ODM\String */
    protected $access_token;
//    /** @ODM\ReferenceMany(targetDocument="\Documents\Category") */
//    protected $category = array();
//
//    public function addCategory(\Documents\Category $category) {
//        $this->category[] = $category;
//    }
//
//    public function clearCategory() {
//        $this->category = array();
//    }
}
