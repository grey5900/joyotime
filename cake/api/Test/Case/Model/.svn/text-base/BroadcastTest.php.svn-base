<?php
APP::uses('AppTestCase', 'Test/Case/Model');
APP::uses('UserFixture', 'Test/Case/Fixture');
APP::uses('GiftBroadcastFixture', 'Test/Case/Fixture');
APP::uses('MessageBroadcastFixture', 'Test/Case/Fixture');
APP::uses('GiftBroadcast', 'Model');
APP::uses('MessageBroadcast', 'Model');
/**
 * The user model test class
 *
 * @package       app.Test.Case.Model
 */
class BroadcastTest extends AppTestCase {
    
    public $fixtures = array(
        'app.user',
        'app.gift_broadcast',
        'app.message_broadcast',
    );
    
    public function getModelName() {
        return 'Broadcast';
    }
    
    public function setUp() {
        parent::setUp();
        $this->User = ClassRegistry::init('User');
        
        $fixUser = new UserFixture();
        $this->fix['user1'] = $fixUser->records[0];
        $this->fix['user2'] = $fixUser->records[1];
        
        $fix = new GiftBroadcastFixture();
        $this->fix['gift'] = $fix->records[0];
        $fix = new MessageBroadcastFixture();
        $this->fix['message'] = $fix->records[0];
    }
    
    public function testNext() {
        $item = $this->model->next((string) $this->fix['user1']['_id'], GiftBroadcast::TYPE, new MongoDate());
        $this->assertEqual($item, false);
        $item = $this->model->next((string) $this->fix['user1']['_id'], GiftBroadcast::TYPE, new MongoDate(time() - 1000));
        $item = $item['Broadcast'];
        $this->assertEqual($item['type'], GiftBroadcast::TYPE);
        $this->assertEqual($item['message'], $this->fix['gift']['message']);
        $this->assertEqual(isset($item['readers']), false);
        $this->assertEqual($item['amount']['time'], $this->fix['gift']['amount']['time']);
        $this->assertEqual(isset($item['finished']), true);

        $item = $this->model->next((string) $this->fix['user1']['_id'], MessageBroadcast::TYPE, new MongoDate());
        $this->assertEqual($item, false);
        $item = $this->model->next((string) $this->fix['user1']['_id'], MessageBroadcast::TYPE, new MongoDate(time() - 1000));
        $item = $item['Broadcast'];
        $this->assertEqual($item['type'], MessageBroadcast::TYPE);
        $this->assertEqual($item['message'], $this->fix['message']['message']);
        $this->assertEqual(isset($item['readers']), false);
        $this->assertEqual(isset($item['finished']), true);
        
        $item = $this->model->next((string) $this->fix['user1']['_id'], GiftBroadcast::TYPE, new MongoDate(time() - 1000));
        $this->assertEqual($item, false);
        $item = $this->model->next((string) $this->fix['user1']['_id'], MessageBroadcast::TYPE, new MongoDate(time() - 1000));
        $this->assertEqual($item, false);
    }
    
    public function testChkFinished() {
    	$finished = time();
    	$this->assertEqual($this->model->chkFinished($finished), true);
    	$this->assertEqual($this->model->chkFinished(123456789), false);
    	$this->assertEqual($this->model->chkFinished(1234567890), true);
    	$this->assertEqual($this->model->chkFinished('not_numeric'), false);
    }
}