<?php

class Angel_Model_User extends Angel_Model_AbstractModel {
    protected $_document_class = '\Documents\User';

    //新增用户只保存用户的微信访问token和登陆时间
    public function addUser($openid, $subscribe, $nickname, $sex, $language, $city, $province, $country, $headimgurl, $subscribe_time, $last_time = 0, $access_token = "") {
        $data = array('openid' => $openid,
            'subscribe' => $subscribe,
            'nickname' => $nickname,
            'sex' => $sex,
            'language' => $language,
            'city' => $city,
            'province' => $province,
            'country' => $country,
            'headimgurl' => $headimgurl,
            'subscribe_time' => $subscribe_time,
            'last_time' => $last_time,
            'access_token' => $access_token);

        $result = $this->add($data);

        return $result;
    }

    //每次登陆时都重新保存用户的所有信息
    public function saveUser($id, $openid, $subscribe, $nickname, $sex, $language, $city, $province, $country, $headimgurl, $subscribe_time, $last_time = null, $access_token = null) {
        $data = array('openid' => $openid,
            'subscribe' => $subscribe,
            'nickname' => $nickname,
            'sex' => $sex,
            'language' => $language,
            'city' => $city,
            'province' => $province,
            'country' => $country,
            'headimgurl' => $headimgurl,
            'subscribe_time' => $subscribe_time,
            'last_time' => $last_time,
            'access_token' => $access_token);

        $result = $this->save($id, $data);

        return $result;
    }

    public function getUserByOpenId($openid) {
        $query = $this->_dm->createQueryBuilder($this->_document_class)->field('openid')->equals($openid)->sort("created_at", -1);

        $result = $query->getQuery()->execute();

        return $result;
    }

    public function getUserByOpenIds($openids) {
        return $openids;

        $query = $this->_dm->createQueryBuilder($this->_document_class)->field('openid')->in($openids)->sort("created_at", 1);

        $result = $query->getQuery();

        return $result;
    }

}
