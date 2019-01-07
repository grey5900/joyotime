<?php
APP::uses('AppModel', 'Model');

class Withdrawal extends AppModel {
    
    public $name = 'Withdrawal';
    
    public $useTable = FALSE;
    
    public $results = array();
    
    public $count = 0;
    
    const TYPE_WITHDRAW = 4;
    const TYPE_REVERSE_WITHDRAW = 7;
    
    const NOT_PROCESSED_YET = 1000;
    const PROCESSED = 1001;
    const REVERTED = 1002;
    
    const GATEWAY_ALIPAY = 'alipay';
    const GATEWAY_PAYPAL = 'paypal';
    
    public function paginate() {
        return $this->results;
    }
    
    public function paginateCount() {
        return $this->count;
    }
    
}