<?php
App::uses('AutoReplyMessagesController', 'Controller');

class AutoReplyMessagesControllerTest extends ControllerTestCase {
    
/**
 * Pre-defined fixtrues want to load.
 *
 * @var array
 */
    public $fixtures = array(
    	'app.auto_reply_message',
        'app.auto_reply_tag',
    	'app.auto_reply_message_tag',
    	'app.auto_reply_message_custom',
    	'app.auto_reply_message_news',
    	'app.auto_reply_message_music',
    	'app.auto_reply_message_exlink',
    	'app.auto_reply_message_location',
    	'app.auto_reply_category',
        'app.auto_reply_location',
        'app.auto_reply_location_message',
        'app.auto_reply_fixcode',
        'app.auto_reply_fixcode_message',
    	'app.image_attachment',
    );
    
/**
 * It's token for identify.
 * 
 * @var string
 */
    private $token;
    
    public function setUp() {
    	parent::setUp();
    	
    }
    
    public function testIndex() {
        $controller = $this->getMockController();
        $this->testAction('/auto_reply_messages/index');
        $this->assertEqual(!empty($this->vars['messages']), true);
        $this->assertEqual(!empty($this->vars['cates']), true);
        $this->assertTextContains('新建图文内容', $this->view);
    }
    
    public function testIndexWithFilter() {
        $controller = $this->getMockController();
        // -1 means non-category
        $this->testAction('/auto_reply_messages/index/-1');
        $this->assertEqual(count($this->vars['messages']), 1);
        $this->assertEqual(!empty($this->vars['cates']), true);
        $this->assertTextContains('新建图文内容', $this->view);
        // 1 means category id
        $this->testAction('/auto_reply_messages/index/1');
        $this->assertEqual(count($this->vars['messages']), 2);
        $this->assertEqual(!empty($this->vars['cates']), true);
        $this->assertTextContains('新建图文内容', $this->view);
    }
    
    public function testAdd() {
        $controller = $this->getMockController();
        $controller->request
            ->expects($this->once())
            ->method('is')
            ->will($this->returnValue('POST'));
        
        // inserting new one.
        $controller->data = array(
        	'AutoReplyMessage' => array(
        	    'id' => '',
				'type' => 'text',
			    'user_id' => 1,
				'description' => 'message1'
			),
            'AutoReplyMessageNews' => array(
                'auto_reply_category_id' => null
            )
        );
        $controller->params = Router::parse('/auto_reply_messages/add/');
        $controller->beforeFilter();
        $controller->add();
        
        $result = $controller->AutoReplyMessage->read(null, 6);
        $this->assertEqual($result['AutoReplyMessage']['user_id'], '1');
        $this->assertEqual($result['AutoReplyMessage']['description'], 'message1');
        $this->assertEqual($result['AutoReplyMessageNews']['auto_reply_category_id'], null);
    }

    public function testEdit() {
        $controller = $this->getMockController();
        $controller->request
            ->expects($this->once())
            ->method('is')
            ->will($this->returnValue('POST'));
        
        // editing...
        $controller->data = array(
        	'AutoReplyMessage' => array(
        	    'id' => '6',
				'type' => 'text',
			    'user_id' => 1,
				'description' => 'message1'
			),
            'AutoReplyMessageNews' => array(
                'auto_reply_category_id' => 1
            )
        );
        $controller->params = Router::parse('/auto_reply_messages/add/');
        $controller->beforeFilter();
        $controller->add();
        
        $result = $controller->AutoReplyMessage->read(null, 6);
        $this->assertEqual($result['AutoReplyMessage']['user_id'], '1');
        $this->assertEqual($result['AutoReplyMessage']['description'], 'message1');
        $this->assertEqual($result['AutoReplyMessageNews']['auto_reply_category_id'], 1);
    }
    
    public function testEditing() {
        $controller = $this->getMockController();
        $this->testAction('/auto_reply_messages/edit/1');
        $this->assertEqual(!empty($controller->request->data), true);
        $this->assertEqual(!empty($this->vars['cates']), true);
        $this->assertTextContains('编辑图文内容', $this->view);
    }
    
    public function testDelete() {
        $controller = $this->getMockController();
        $this->testAction('/auto_reply_messages/delete/1');
        $resp = json_decode($this->result);
        $this->assertEqual($resp->result, true);
    }
    
    public function testDeleteWithoutId() {
        $controller = $this->getMockController();
        $this->testAction('/auto_reply_messages/delete/');
        $resp = json_decode($this->result);
        $this->assertEqual($resp->result, false);
    }
    
    public function testDeleteNoPermission() {
        $controller = $this->getMockController();
        $this->testAction('/auto_reply_messages/delete/999');
        $resp = json_decode($this->result);
        $this->assertEqual($resp->result, false);
    }
    
    public function testDeleteFailure() {
        $controller = $this->getMockController();
        $AutoReplyMessage = $this->getMockForModel('AutoReplyMessage', array('delete'));
        $AutoReplyMessage
            ->expects($this->once())
            ->method('delete')
            ->will($this->returnValue(false));
        $controller->AutoReplyMessage = $AutoReplyMessage;
        
        $this->testAction('/auto_reply_messages/delete/1');
        $resp = json_decode($this->result);
        $this->assertEqual($resp->result, false);
    }
    
    public function getMockController() {
        $controller = $this->generate('AutoReplyMessages');
        $controller->constructClasses();
        $controller->Components->init($controller);
        $controller->Session->write('Auth.User', array(
        	'id' => 1,
        ));
        return $controller;
    }

    public function tearDown() {
        parent::tearDown();
    }
}
