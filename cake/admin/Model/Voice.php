<?php

APP::uses('AppModel', 'Model');

class Voice extends AppModel {
    
    public $name = 'Voice';
    
    public $useTable = FALSE;
    
    public $results = array();
    
    public $count = 0;
    
    const STATUS_PENDING = 0;
    const STATUS_APPROVED = 1;
    const STATUS_INVALID = 2;
    const STATUS_UNAVAILABLE = 3;
    const RECOMMENDED = 4;
    const RECOMMEND = 5;
    const RECOMMEND_STATUS=0;
    const AD_STATUS=6;
    const AD_ADD=7;
    const RECOMMENDED_STATUS=1;
    const LANG_ZH = 'zh_CN';
    const LANG_EN = 'en_US';
    
    public function paginate() {
        return $this->results;
    }
    
    public function paginateCount() {
        return $this->count;
    }
    
}