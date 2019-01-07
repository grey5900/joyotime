<?php
APP::uses('AppModel', 'Model');

class Tag extends AppModel {
    
    public $name = 'Voice';
    
    public $useTable = FALSE;
    
    public $results = array();
    
    public $count = 0;
    
   
    const LANG_ZH = 'zh_CN';
    const LANG_EN = 'en_US';
    
    public function paginate() {
        return $this->results;
    }
    
    public function paginateCount() {
        return $this->count;
    }
    
}