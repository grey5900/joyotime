<?php
APP::uses('AppTestCase', 'Test/Case/Model');
APP::uses('UserFixture', 'Test/Fixture');

/**
 * The user model test class
 *
 * @package       app.Test.Case.Model
 */
class NotificationCounterTest extends AppTestCase {
    
    private $fix = array();
    
    public function getModelName() {
        return 'NotificationCounter';
    }
    
    public function setUp() {
        parent::setUp();
        
        $this->user1 = '51f0c30f6f159aec6fad8ce3';
        $this->user2 = '51f0c30f6f159aec6fad8ce3';
        
        $fix = new UserFixture();
        $this->fix['user1'] = (string)$fix->records[0]['_id'];
        $this->fix['user2'] = (string)$fix->records[1]['_id'];
    }
    
    public function testIncr() {
        $this->assertEqual($this->model->incr($this->fix['user1']), 1);
        $this->assertEqual($this->model->incr($this->fix['user1']), 2);
        $this->assertEqual($this->model->incr($this->fix['user1']), 3);
        $this->assertEqual($this->model->count($this->fix['user1']), 3);
        $this->assertEqual($this->model->clean($this->fix['user1']), true);
        $this->assertEqual($this->model->count($this->fix['user1']), 0);
    }
}