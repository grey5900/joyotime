<?php

class Subsidy extends \AppModel {
    
    public $name = 'Subsidy';
    
    public $useDbConfig = 'redis'; // Defined at app/Config/database.php
    
/**
 * @var Redis
 */
    protected $redis;
    
/**
 * @var string
 */
    private $key = 'subsidy:device_code:voice';
    
    public function __construct($id = false, $table = null, $ds = null) {
        parent::__construct($id, $table, $ds);
        $this->redis = $this->getDataSource()->getInstance();
    }
    
    public function exist($deviceCode, $voiceId) {
        if(!$deviceCode || !$voiceId) return true;
        return $this->redis->hExists($this->key, $this->hField($deviceCode, $voiceId)); 
    }
    
    public function add($deviceCode, $voiceId) {
        return $this->redis->hSet($this->key, $this->hField($deviceCode, $voiceId), 1);
    }
    
    private function hField($deviceCode, $voiceId) {
        return strtolower($deviceCode).'|'.strtolower($voiceId);
    }
}