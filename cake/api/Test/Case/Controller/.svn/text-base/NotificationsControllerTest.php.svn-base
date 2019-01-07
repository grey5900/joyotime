<?php
APP::uses('AppControllerTestCase', 'Test/Case/Controller');
APP::uses('Notification', 'Model');

class NotificationsControllerTest extends AppControllerTestCase {
    
    public $fixtures = array(
    	'app.user',
//     	'app.voice',
//     	'app.follow',
    );
    
/**
 * (non-PHPdoc)
 * @see AppControllerTestCase::getModelName()
 */
    public function getModelName() {
    	return 'Notification';
    }
    
/**
 * (non-PHPdoc)
 * @see AppControllerTestCase::getControllerName()
 */
    public function getControllerName() {
    	return 'Notifications';
    }
    
/**
 * (non-PHPdoc)
 * @see CakeTestCase::setUp()
 */
    public function setUp() {
    	parent::setUp();
    	
    	$fix = new UserFixture();
    	$this->fix['user1'] = $fix->records[0];
    	$this->fix['user2'] = $fix->records[1];
    }
    
    public function testAdminAdd() {
        $message = 'mockup_message';
        $result = $this->testAction('/admin/notifications.json?auth_token='.$this->adminToken, array(
            'method' => 'POST',
            'data' => array(
                'user_id' => (string) $this->fix['user1']['_id'],
                'message' => $message
            )
        ));
        
        $this->assertEqual($result, true);
        $row = $this->model->findByUserId((string) $this->fix['user1']['_id']);
        $row = $row['Notification'];
        
        $this->assertEqual($row['message'], $message);
    }
    
}