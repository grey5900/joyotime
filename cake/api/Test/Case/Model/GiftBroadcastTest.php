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
class GiftBroadcastTest extends AppTestCase {
    
    public $fixtures = array(
        'app.user',
        'app.gift_broadcast',
        'app.message_broadcast',
    );
    
    public function getModelName() {
        return 'GiftBroadcast';
    }
    
    public function setUp() {
        parent::setUp();
        $this->User = ClassRegistry::init('User');
        
        $fixUser = new UserFixture();
        $this->fix['user1'] = $fixUser->records[0];
        $this->fix['user2'] = $fixUser->records[1];
        $fixGB = new GiftBroadcastFixture();
        $this->fix['gb1'] = $fixGB->records[0];
    }
    
    public function testSave() {
        $seconds = 1000;
        $message = 'mockup_message';
        $finished = time() + Configure::read('Broadcast.Finished.TTL');
        
        // Assert success... gift
        $data = array(
            'message' => $message,
            'amount' => array(
                'time' => $seconds
            )
        );
        $this->model->create();
        $result = $this->model->save($data);
        $result = $result['GiftBroadcast'];
        $this->assertEqual($result['type'], GiftBroadcast::TYPE);
        $this->assertEqual($result['message'], $message);
        $this->assertEqual($result['amount']['time'], $seconds);
        $this->assertEqual($result['readers'], array());
        $this->assertEqual($result['finished']->sec, $finished);
        
        // Assert fails without message...
        $data = array(
            'amount' => array(
            	'time' => $seconds
            )
        );
        $this->model->create();
        $result = $this->model->save($data);
        $this->assertEqual($result, false);
        
        // Assert fails with invalid seconds...
        $data = array(
            'message' => $message,
            'amount' => array(
            	'time' => -2
            )
        );
        $this->model->create();
        $result = $this->model->save($data);
        $this->assertEqual($result, false);
    }
    
    public function testNext() {
        $item = $this->model->next((string) $this->fix['user1']['_id'], GiftBroadcast::TYPE, new MongoDate(time() - 1000));
        $item = $item['GiftBroadcast'];
        debug($item);
        $this->assertEqual($item['type'], GiftBroadcast::TYPE);
        $this->assertEqual($item['message'], $this->fix['gb1']['message']);
        $this->assertEqual(!isset($item['readers']), true);
        $this->assertEqual($item['amount']['time'], $this->fix['gb1']['amount']['time']);
        $this->assertEqual(isset($item['finished']), true);
        
        // assert fails in second time...
        $item = $this->model->next((string) $this->fix['user1']['_id'], GiftBroadcast::TYPE, new MongoDate(time() - 1000));
        $this->assertEqual($item, false);
    }
}