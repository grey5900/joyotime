<?php
APP::uses('AppControllerTestCase', 'Test/Case/Controller');
APP::uses('GiftBroadcastFixture', 'Test/Fixture');
APP::uses('MessageBroadcastFixture', 'Test/Fixture');

class StartupsControllerTest extends AppControllerTestCase {
    
    public $fixtures = array(
        'app.gift_broadcast',
        'app.message_broadcast',
        'app.user',
    );
    
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
    	return 'Startups';
    }
    
/**
 * (non-PHPdoc)
 * @see CakeTestCase::setUp()
 */
    public function setUp() {
    	parent::setUp();
    	
    	$fix = new GiftBroadcastFixture();
    	$this->fix['gift'] = $fix->records[0];
    	$fix = new MessageBroadcastFixture();
    	$this->fix['message'] = $fix->records[0];
    	$fix = new UserFixture();
    	$this->fix['user'] = $fix->records[0];
    	
    	$this->Notification = ClassRegistry::init('Notification');
    	$this->Gift = ClassRegistry::init('Gift');
    	$this->User = ClassRegistry::init('User');
    }
    
    public function testConnect() {
        $result = $this->testAction("/startups/connect.json?auth_token={$this->userToken}", array(
    		'method' => 'GET',
        ));
        $this->assertEqual($result, true);
        
        // Checking notification...
        $row = $this->Notification->find('all', array(
    		'conditions' => array(
    			'user_id' => $this->userId,
    		),
    		'order' => array('created' => 'desc')
        ));
        
        // Checking notifications...
        $this->assertEqual(count($row), 2);
        $this->assertEqual($row[0]['Notification']['type'], Notification::TYPE_BROADCAST);
        $this->assertEqual($row[0]['Notification']['message'], $this->fix['message']['message']);
        $this->assertEqual($row[1]['Notification']['type'], Notification::TYPE_BROADCAST);
        $this->assertEqual($row[1]['Notification']['message'], $this->fix['gift']['message']);
        
        // Checking checkout of gift...
        $row = $this->Gift->findByUserId($this->userId);
        $this->assertEqual($row['Gift']['amount']['time'], $this->fix['gift']['amount']['time']);
        $this->assertEqual($row['Gift']['message'], $this->fix['gift']['message']);
        
        // Checking amount of user...
        $row = $this->User->findById($this->userId);
        $this->assertEqual($row['User']['money'], $this->fix['user']['money'] + $this->fix['gift']['amount']['time']);
        $this->assertEqual($row['User']['income'], $this->fix['user']['income'] + $this->fix['gift']['amount']['time']);
        $this->assertEqual($row['User']['earn'], $this->fix['user']['earn'], 'Earn should not be modified');
    }
}