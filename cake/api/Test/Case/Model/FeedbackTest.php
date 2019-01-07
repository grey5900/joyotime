<?php
APP::uses('AppTestCase', 'Test/Case/Model');
APP::uses('Feedback', 'Model');
/**
 * The user model test class
 *
 * @package       app.Test.Case.Model
 */
class FeedbackTest extends AppTestCase {
    
    public $fixtures = array(
    );
    
    public function getModelName() {
        return 'Feedback';
    }
    
    public function setUp() {
        parent::setUp();
    }
    
    public function getData() {
        return array(
            'user_id' => '51f0c30f6f159aec6fad8ce3',
    		'email' => 'baohanddd@gmail.com',
    		'content' => 'mockup content',
    		'contact' => '13880799177',
    		'user_agent' => 'Android',
    	    'status' => Feedback::STATUS_PENDING    
        );
    }
    
    public function testEnqueue() {
        $this->assertEqual($this->model->enqueue($this->data()), true);
        $data = $this->model->dequeue();
        $this->assertEqual($data, $this->data());
    }
}