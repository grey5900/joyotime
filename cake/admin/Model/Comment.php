<?php
APP::uses('AppModel', 'Model');

class Comment extends AppModel {
    
    public $name = 'Comment';
    
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