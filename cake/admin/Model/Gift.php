<?php
APP::uses('AppModel', 'Model');

class Gift extends AppModel {
    
    public $name = 'Gift';
    
    public $useTable = FALSE;
    
    public $results = array();
    
    public $count = 0;
    
    public function paginate() {
        return $this->results;
    }
    
    public function paginateCount() {
        return $this->count;
    }
    
}