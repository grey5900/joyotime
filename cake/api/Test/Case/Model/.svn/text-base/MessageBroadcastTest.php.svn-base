<?php
APP::uses('AppTestCase', 'Test/Case/Model');
APP::uses('UserFixture', 'Test/Case/Fixture');
APP::uses('GiftBroadcastFixture', 'Test/Case/Fixture');
APP::uses('GiftBroadcast', 'Model');
/**
 * The user model test class
 *
 * @package       app.Test.Case.Model
 */
class MessageBroadcastTest extends AppTestCase {
    
    public $fixtures = array(
        'app.user',
        'app.message_broadcast',
    );
    
    public function getModelName() {
        return 'MessageBroadcast';
    }
    
    public function setUp() {
        parent::setUp();
        $this->User = ClassRegistry::init('User');
        
        $fixUser = new UserFixture();
        $this->fix['user1'] = $fixUser->records[0];
        $this->fix['user2'] = $fixUser->records[1];
        $fixMB = new MessageBroadcastFixture();
        $this->fix['mb1'] = $fixMB->records[0];
    }
    
    public function testSave() {
        $message = 'mockup_message';
        $finished = time();
        
        // Assert success... gift
        $data = array(
            'message' => $message,
        );
        $this->model->create();
        $result = $this->model->save($data);
        $result = $result['MessageBroadcast'];
        $this->assertEqual($result['type'], MessageBroadcast::TYPE);
        $this->assertEqual($result['message'], $message);
        $this->assertEqual($result['readers'], array());
        $this->assertEqual($result['read_total'], 0);
        $this->assertEqual(isset($result['finished']), true);
        
        // Assert fails without message...
        $data = array(
            'finished' => $finished
        );
        $this->model->create();
        $result = $this->model->save($data);
        $this->assertEqual($result, false);
        
        // Assert fails with invalid message...
        $data = array(
            'message' => '',
            'finished' => $finished
        );
        $this->model->create();
        $result = $this->model->save($data);
        $this->assertEqual($result, false);
    }
    
    public function testNext() {
        $item = $this->model->next((string) $this->fix['user1']['_id'], MessageBroadcast::TYPE, new MongoDate(time() - 1000));
        $item = $item['MessageBroadcast'];
        $this->assertEqual($item['type'], MessageBroadcast::TYPE);
        $this->assertEqual($item['message'], $this->fix['mb1']['message']);
        $this->assertEqual($item['read_total'], 0);
        $this->assertEqual(!isset($item['readers']), true);
        $this->assertEqual(isset($item['finished']), true);
        
        $row = $this->model->findById($item['_id']);
        $row = $row['MessageBroadcast'];
        debug($row);
        $this->assertEqual($row['read_total'], 1);
        
        // assert fails in second time...
        $item = $this->model->next((string) $this->fix['user1']['_id'], MessageBroadcast::TYPE, new MongoDate(time() - 1000));
        $this->assertEqual($item, false);
    }
}