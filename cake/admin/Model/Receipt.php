<?php
APP::uses('AppModel', 'Model');

class Receipt extends AppModel {
    
    public $name = 'Receipt';
    
    public $useTable = FALSE;
    
    public $results = array();
    
    public $count = 0;

    const TYPE_ALIPAY = 1000;
    const TYPE_IOS    = 1001;
    const RECHARGE = 1002;
    const STATUS_PENDING = 2000;
    const STATUS_PAID = 2001;
    const STATUS_PRICE_EXCEPTION = 2002;
    
    public function paginate() {
        return $this->results;
    }
    
    public function paginateCount() {
        return $this->count;
    }
    
}