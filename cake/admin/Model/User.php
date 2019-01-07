<?php
APP::uses('AppModel', 'Model');

class User extends AppModel {
    
    public $name = 'User';
    
    public $useTable = FALSE;
    
    public $results = array();
    
    public $count = 0;
    const VERIFIED =1;
    const UNVERIFIED =0;
    const RECOMMEND = 0;
    const RECOMMENDED = 1;
    const CONTRIBUTORED = 1;
    const CONTRIBUTOR = 0;
    public function paginate() {
        return $this->results;
    }
    
    public function paginateCount() {
        return $this->count;
    }
    
}