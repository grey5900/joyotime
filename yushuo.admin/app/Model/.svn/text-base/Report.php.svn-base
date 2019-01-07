<?php
APP::uses('AppModel', 'Model');

class Report extends AppModel {
    
    public $name = 'Report';
    
    public $useTable = FALSE;
    
    public $results = array();
    
    public $count = 0;
    
    const STATUS_PENDING = 0;
    const STATUS_DONE = 1;
    
    public function paginate() {
        return $this->results;
    }
    
    public function paginateCount() {
        return $this->count;
    }
    
}