<?php
APP::uses('AppControllerTestCase', 'Test/Case/Controller');

class BroadcastsControllerTest extends AppControllerTestCase {
    
    public $fixtures = array(
        'app.user',
        'app.message_broadcast',
    );
    
/**
 * (non-PHPdoc)
 * @see AppControllerTestCase::getModelName()
 */
    public function getModelName() {
    	return 'MessageBroadcast';
    }
    
/**
 * (non-PHPdoc)
 * @see AppControllerTestCase::getControllerName()
 */
    public function getControllerName() {
    	return 'Broadcasts';
    }
    
/**
 * (non-PHPdoc)
 * @see CakeTestCase::setUp()
 */
    public function setUp() {
    	parent::setUp();
    	
    	$fix = new UserFixture();
    	$this->fix['user'] = $fix->records[0];
    	
    	$this->NotificationQueue = ClassRegistry::init('NotificationQueue');
    }
    
    public function testAdminAdd() {
        $message = 'mockup_message';
        $result = $this->testAction("/admin/broadcasts.json?auth_token={$this->adminToken}", array(
    		'method' => 'POST',
            'data' => array(
                'message' => $message
            )
        ));
        $this->assertEqual($result, true);
        
        // Checking NotificationQueue...
        $this->assertEqual($this->NotificationQueue->dequeue()->getMessage(), $message);
        $this->assertEqual($this->NotificationQueue->dequeue()->getMessage(), $message);
        $this->assertEqual($this->NotificationQueue->dequeue(), false);
    }
    
    public function testAdminIndex() {
        $result = $this->testAction("/admin/broadcasts.json?auth_token={$this->adminToken}", array(
    		'method' => 'GET',
        ));
        debug($this->vars);
        $this->assertEqual($this->vars['root']['result']['total'], 1);
        $this->assertEqual(count($this->vars['root']['result']['items']), 1);
    }
}