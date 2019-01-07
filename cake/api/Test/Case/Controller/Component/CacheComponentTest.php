<?php
APP::uses('AppControllerTestCase', 'Test/Case/Controller');
APP::uses('CacheComponent', 'Controller/Component');
APP::uses('Controller', 'Controller');

class CacheComponentTest extends AppControllerTestCase {
    
/**
 * @var CacheComponent
 */
    private $cache;
/**
 * @var BoughtVoiceCache
 */
    private $bought;
/**
 * @var string JSON string come from appstore
 */
    private $raw;
    
    /**
     * (non-PHPdoc)
     * @see AppControllerTestCase::getModelName()
     */
    public function getModelName() {
    	return 'User';
    }
    
    /**
     * (non-PHPdoc)
     * @see AppControllerTestCase::getControllerName()
     */
    public function getControllerName() {
    	return 'Users';
    }
    
/**
 * (non-PHPdoc)
 * @see CakeTestCase::setUp()
 */
    public function setUp() {
    	parent::setUp();
    	$this->bought = $this->controller->Cache->voice()->bought();
    	$this->redis = $this->bought->getRedisInstance();
    }
    
    public function testBoughtVoiceCache_push() {
        $this->userId = '51f0c30f6f159aec6fad8ce3';
        $this->voiceId = '51f225e26f159afa43e76aff';
        $year = 2013;
        $month = 9;
        $this->bought->push($this->userId, $this->voiceId, $year, $month);
        $kTrack = $this->bought->key('track', $this->userId);
        $result = $this->redis->zRangeByScore($kTrack, 0, 201500, array('withscores' => TRUE));
        $kGroup = $this->bought->key('group', $this->userId, $year, $month);
        $this->assertEqual($result[$kGroup], 201309);
    }
}