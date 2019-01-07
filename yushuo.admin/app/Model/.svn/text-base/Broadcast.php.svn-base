<?php
APP::uses('AppModel', 'Model');

class Broadcast extends AppModel {
    
    public $name = 'Broadcast';
    
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