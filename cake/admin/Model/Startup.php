<?php
APP::uses('AppModel', 'Model');

class Startup extends AppModel {
    
    public $name = 'Startup';
    
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