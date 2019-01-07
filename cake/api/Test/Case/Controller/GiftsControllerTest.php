<?php
APP::uses('AppControllerTestCase', 'Test/Case/Controller');

class GiftsControllerTest extends AppControllerTestCase {
    
    public $fixtures = array(
    	'app.user',
    );
    
/**
 * (non-PHPdoc)
 * @see AppControllerTestCase::getModelName()
 */
    public function getModelName() {
    	return 'GiftLog';
    }
    
/**
 * (non-PHPdoc)
 * @see AppControllerTestCase::getControllerName()
 */
    public function getControllerName() {
    	return 'Gifts';
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
    	
    	$this->User = ClassRegistry::init('User');
    	$this->Gift = ClassRegistry::init('Gift');
    	$this->GiftLog = ClassRegistry::init('GiftLog');
    	$this->Notification = ClassRegistry::init('Notification');
    	$this->GiftBroadcast = ClassRegistry::init('GiftBroadcast');
    	$this->NotificationQueue = ClassRegistry::init('NotificationQueue');
    }
    
    public function testAdminAdd() {
        $seconds = 1000;
        $message = 'mockup_message';
        
        // Assert success...
        $result = $this->testAction("/admin/gifts.json?auth_token={$this->adminToken}", array(
            'method' => 'POST',
            'data' => array(
                'user_id' => (string) $this->fix['user1']['_id'],
                'message' => $message,
                'seconds' => $seconds
            )
        ));
        $this->assertEqual($result, true);
        
        // Checking user data...
        $user = $this->User->findById((string) $this->fix['user1']['_id']);
        $this->assertEqual($user['User']['income'], $this->fix['user1']['income'] + $seconds);
        $this->assertEqual($user['User']['money'], $this->fix['user1']['money'] + $seconds);
        $this->assertEqual($user['User']['earn'], $this->fix['user1']['earn']);
        
        // Checking checkout of gift...
        $gift = $this->Gift->findByUserId((string) $this->fix['user1']['_id']);
        $this->assertEqual($gift['Gift']['user_id'], (string)$this->fix['user1']['_id']);
        $this->assertEqual($gift['Gift']['amount']['time'], $seconds);
        
        // Checking giftlog...
        $log = $this->GiftLog->findByUserId((string) $this->fix['user1']['_id']);
        $this->assertEqual($log['GiftLog']['user_id'], (string)$this->fix['user1']['_id']);
        $this->assertEqual($log['GiftLog']['amount']['time'], $seconds);
        
        // Checking Notification...
        $notify = $this->Notification->findByUserId((string) $this->fix['user1']['_id']);
        $this->assertEqual($notify['Notification']['type'], Notification::TYPE_BROADCAST);
        $this->assertEqual($notify['Notification']['message'], $message);
        
        // Checking NotificationQueue...
        $this->assertEqual($this->NotificationQueue->dequeue(), false);
        
        // Checking GiftBroadcast...
        $this->assertEqual($this->GiftBroadcast->next(
                (string) $this->fix['user1']['_id'], GiftBroadcast::TYPE), false);
    }
    
    public function testAdminAddWithBroadcast() {
        $seconds = 1000;
        $message = 'mockup_message';
        
        // Assert success...
        $result = $this->testAction("/admin/gifts.json?auth_token={$this->adminToken}", array(
            'method' => 'POST',
            'data' => array(
                'message' => $message,
                'seconds' => $seconds
            )
        ));
        $this->assertEqual($result, true);
        
        // Checking user data...
        $user = $this->User->findById((string) $this->fix['user1']['_id']);
        $this->assertEqual($user['User']['income'], $this->fix['user1']['income']);
        $this->assertEqual($user['User']['money'], $this->fix['user1']['money']);
        
        // Checking checkout of gift...
        $this->assertEqual($this->Gift->findByUserId((string) $this->fix['user1']['_id']), array());
        
        // Checking giftlog...
        $this->assertEqual($this->GiftLog->findByUserId((string) $this->fix['user1']['_id']), array());
        
        // Checking Notification...
        $this->assertEqual($this->Notification->findByUserId((string) $this->fix['user1']['_id']), array());
        
        // Checking NotificationQueue...
        $this->assertEqual($this->NotificationQueue->dequeue()->getMessage(), $message);
        $this->assertEqual($this->NotificationQueue->dequeue()->getMessage(), $message);
        $this->assertEqual($this->NotificationQueue->dequeue(), false);
        
        // Checking GiftBroadcast...
        $row = $this->GiftBroadcast->next(
        		(string) $this->fix['user1']['_id'], GiftBroadcast::TYPE);
        $this->assertEqual($row['GiftBroadcast']['amount']['time'], $seconds);
        $this->assertEqual($row['GiftBroadcast']['message'], $message);
    }
}