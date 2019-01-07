<?php
APP::uses('AppTestCase', 'Test/Case/Model');
APP::uses('UpdaterComponent', 'Controller/Component');
APP::uses('Controller', 'Controller');
APP::uses('Notification', 'Model');


/**
 * The user model test class
 *
 * @package       app.Test.Case.Model
 */
class NotificationTest extends AppTestCase {
	
	public $fixtures = array(
		'app.voice',
		'app.user',
		'app.comment',
	);
    
    public function getModelName() {
        return 'Notification';
    }
    
    public function setUp() {
        parent::setUp();
        
        $this->voiceId = '51f225e26f159afa43e76aff';
        $this->userId = '51f0c30f6f159aec6fad8ce3';
    }
    
    public function testNewComment() {
    	$counter = ClassRegistry::init('NotificationCounter');
    	$comment = ClassRegistry::init('Comment');
    	$comment->read(null, '71f0c30f6f159aec6fad8ce3');
    	$event = new CakeEvent('mock_event', $comment);
    	
    	$total = $counter->count($comment->data[$comment->name]['user_id']);
    	
        $result = $this->model->newComment($event);
        $this->assertEqual($result['Notification']['merged'], 1);
        
        $this->assertEqual($counter->count($comment->data[$comment->name]['user_id']), 1);
        
        $result = $this->model->newComment($event);
        $this->assertEqual($result['Notification']['merged'], 2);
        $result = $this->model->newComment($event);
        $this->assertEqual($result['Notification']['merged'], 3);
        
        $this->assertEqual($counter->count($comment->data[$comment->name]['user_id']), 1);
    }
    
    public function testSpecified() {
        $message1 = 'mockup_message_1';
        $message2 = 'mockup_message_2';
        $result = $this->model->official($this->userId, $message1, Notification::TYPE_GIFT);
        $result = $this->model->official($this->userId, $message2, Notification::TYPE_GIFT);
        $rows = $this->model->findAllByUserId($this->userId);
        $this->assertEqual(count($rows), 2);
        $row = $rows[0]['Notification'];
        $this->assertEqual($row['type'], Notification::TYPE_GIFT);
        $this->assertEqual($row['user_id'], $this->userId);
        $this->assertEqual($row['message'], $message1);
    }
    
    public function testChkType() {
        $check = array('type' => Notification::TYPE_GIFT);
        $check_mock = array('type' => 'unknown');
        $this->assertEqual($this->model->chkType($check), true);
        $this->assertEqual($this->model->chkType($check_mock), false);
    }
}