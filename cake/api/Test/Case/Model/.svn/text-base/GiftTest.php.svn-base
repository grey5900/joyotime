<?php
APP::uses('AppTestCase', 'Test/Case/Model');
APP::uses('Gift', 'Model');
APP::uses('UserFixture', 'Test/Case/Fixture');
/**
 * The user model test class
 *
 * @package       app.Test.Case.Model
 */
class GiftTest extends AppTestCase {
    
    public $fixtures = array(
        'app.user',
    );
    
    public function getModelName() {
        return 'Gift';
    }
    
    public function setUp() {
        parent::setUp();
        $this->User = ClassRegistry::init('User');
        
        $fixUser = new UserFixture();
        $this->fix['user1'] = $fixUser->records[0];
        $this->fix['user2'] = $fixUser->records[1];
    }
    
    public function testAdd() {
        $userId = (string)$this->fix['user1']['_id'];
        $seconds = 100;
        $message = 'mockup message';
        $result = $this->model->add($userId, $seconds, $message);
        $result = $result['Gift'];
        $this->assertEqual($result['user_id'], $userId);
        $this->assertEqual($result['type'], Gift::TYPE);
        $this->assertEqual($result['amount']['time'], $seconds);
        $this->assertEqual($result['message'], $message);
    }
}