<?php
APP::uses('AppControllerTestCase', 'Test/Case/Controller');
App::uses('ComponentCollection', 'Controller');
App::uses('CacheComponent', 'Controller/Component');

class FeedbacksControllerTest extends AppControllerTestCase {
    
    public $fixtures = array(
        'app.feedback'
    );
    
    private $fix = array();
    
/**
 * (non-PHPdoc)
 * @see AppControllerTestCase::getModelName()
 */
    public function getModelName() {
    	return 'Feedback';
    }
    
/**
 * (non-PHPdoc)
 * @see AppControllerTestCase::getControllerName()
 */
    public function getControllerName() {
    	return 'Feedbacks';
    }
    
/**
 * (non-PHPdoc)
 * @see CakeTestCase::setUp()
 */
    public function setUp() {
    	parent::setUp();
    	$fix = new FeedbackFixture();
    	$this->fix = $fix->records[0];
    }

    public function testAdd() {
        $user_id = '51f0c30f6f159aec6fad8ce3';
        $content = 'hahahaha';
        $status = Feedback::STATUS_PENDING; 
        // Missing user id...
        $this->testAction('/feedbacks.json', array(
            'method' => 'post',
            'data' => array(
//                 'user_id' => $user_id,
                'content' => $content,
                'status' => $status
            ) 
        ));
        $this->assertEqual($this->result, false);
        $this->assertEqual($this->vars['result']['code'], 500);
        $this->assertEqual($this->vars['result']['message'], 'Invalid user_id supplied');
        
        // Assert success...
        $this->testAction('/feedbacks.json', array(
            'method' => 'post',
            'data' => array(
                'user_id' => $user_id,
                'content' => $content,
                'status' => $status
            ) 
        ));
        $this->assertEqual($this->result, true);
        $this->assertEqual(isset($this->vars['root']['result']['_id']), true);
    }
    
    public function testAdminEdit() {
        $_id = '61d0c30f6f159aec6fad8ce3';
        $unknownId = '61d0c30f6f159aec6fad8ce4';
        
        // Invalid _id...
        $result = $this->testAction("/admin/feedbacks/$unknownId.json?auth_token={$this->adminToken}", array(
            'method' => 'PUT',
            'data' => array(
                'status' => Feedback::STATUS_DONE
            )
        ));
        $this->assertEqual($result, false);
        $this->assertEqual($this->vars['result']['code'], 500);
        
        // Invalid status value...
        $result = $this->testAction("/admin/feedbacks/$_id.json?auth_token={$this->adminToken}", array(
            'method' => 'PUT',
            'data' => array(
                'status' => 'unknown'
            )
        ));
        $this->assertEqual($result, false);
        $this->assertEqual($this->vars['result']['code'], 500);
        $this->assertEqual($this->vars['result']['message'], 'Invalid status supplied');
        
        // Assert success...
        $result = $this->testAction("/admin/feedbacks/$_id.json?auth_token={$this->adminToken}", array(
            'method' => 'PUT',
            'data' => array(
                'status' => Feedback::STATUS_DONE
            )
        ));
        $this->assertEqual($result, true);
    }
    
    public function testDelete() {
        $_id = '61d0c30f6f159aec6fad8ce3';
        $unknownId = '61d0c30f6f159aec6fad8ce4';
        
        // Invalid id...
        $result = $this->testAction("/admin/feedbacks/$unknownId.json?auth_token={$this->adminToken}", 
            array('method' => 'DELETE'));
        $this->assertEqual($result, false);
        $this->assertEqual($this->vars['result']['code'], 500);
        
        // Assert success...
        $result = $this->testAction("/admin/feedbacks/$_id.json?auth_token={$this->adminToken}", 
            array('method' => 'DELETE'));
        $this->assertEqual($result, true);
    }
    
    public function testIndex() {
        // find by user_id...
        $this->testAction("/admin/feedbacks.json?user_id={$this->fix['user_id']}&auth_token={$this->adminToken}", 
            array('method' => 'GET'));
        $row = $this->vars['root']['result']['items'][0];
        $this->assertEqual($row['_id'], $this->fix['_id']);
        $this->assertEqual($row['user_id'], $this->fix['user_id']);
        $this->assertEqual($row['content'], $this->fix['content']);
        $this->assertEqual($row['status'], $this->fix['status']);
        $this->assertEqual(isset($row['created']), true);
        $this->assertEqual(isset($row['modified']), true);
    }
    
    public function testView() {
        $_id = '61d0c30f6f159aec6fad8ce3';
        
        // Nothing related with the Id...
        $mockId = '61d0c30f6f159aec6fad8ce2';
        
        // Valid id...
        $this->testAction("/admin/feedbacks/$_id.json?auth_token={$this->adminToken}", 
            array('method' => 'GET'));
        $row = $this->vars['root']['result'];
        $this->assertEqual($row['_id'], $this->fix['_id']);
        $this->assertEqual($row['user_id'], $this->fix['user_id']);
        $this->assertEqual($row['content'], $this->fix['content']);
        $this->assertEqual($row['status'], $this->fix['status']);
        $this->assertEqual(isset($row['created']), true);
        $this->assertEqual(isset($row['modified']), true);
        
        // invalid id...
        $this->testAction("/admin/feedbacks/$mockId.json?auth_token={$this->adminToken}", 
            array('method' => 'GET'));
        $row = $this->vars['root']['result'];
        $this->assertEqual($row, array());
    }
}