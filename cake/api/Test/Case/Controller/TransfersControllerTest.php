<?php
APP::uses('AppControllerTestCase', 'Test/Case/Controller');
APP::uses('Voice', 'Model');
APP::uses('Transfer', 'Model');

class TransfersControllerTest extends AppControllerTestCase {
    
    public $fixtures = array(
    	'app.user',
    	'app.voice',
    	'app.withdrawal',
    );
    
    /**
     * @var array
     */
    public $fix;
    
/**
 * (non-PHPdoc)
 * @see AppControllerTestCase::getModelName()
 */
    public function getModelName() {
    	return 'Transfer';
    }
    
/**
 * (non-PHPdoc)
 * @see AppControllerTestCase::getControllerName()
 */
    public function getControllerName() {
    	return 'Transfers';
    }
    
/**
 * (non-PHPdoc)
 * @see CakeTestCase::setUp()
 */
    public function setUp() {
    	parent::setUp();
    	$fixUser = new UserFixture();
    	$this->fix['user1'] = $fixUser->records[0];
    	$this->fix['user2'] = $fixUser->records[1];
    }
    
    public function testAdd() {
        // Assert success...
        $result = $this->testAction("/users/$this->userId/transfers.json?auth_token={$this->userToken}&api_key={$this->apikey}", array(
            'method' => 'POST',
            'data' => array(
                'payee' => (string)$this->fix['user2']['_id'],
                'seconds' => '100',
            )
        ));
        debug($this->vars);
        debug($this->headers);
        $this->assertEqual($result, true);
        $this->assertEqual(isset($this->headers['Location']), true);
    }
}