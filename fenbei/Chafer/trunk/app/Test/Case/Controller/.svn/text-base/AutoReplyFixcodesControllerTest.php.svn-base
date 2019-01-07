<?php
App::uses('AutoReplyFixcodesController', 'Controller');
App::uses('AppControllerTestCase', 'Test/Case/Controller');

class AutoReplyFixcodesControllerTest extends AppControllerTestCase {
    
/**
 * Pre-defined fixtrues want to load.
 *
 * @var array
 */
    public $fixtures = array(
    	'app.auto_reply_message',
    	'app.auto_reply_message_custom',
    	'app.auto_reply_message_news',
    	'app.auto_reply_fixcode',
    	'app.auto_reply_keyword',
    	'app.auto_reply_fixcode_keyword',
    	'app.auto_reply_fixcode_message',
    	'app.auto_reply_tag',
    	'app.auto_reply_location',
    	'app.auto_reply_message_tag',
    	'app.auto_reply_location_message',
    	'app.auto_reply_message_music',
    	'app.auto_reply_message_exlink',
    	'app.auto_reply_message_location',
    );
    
    public function setUp() {
    	parent::setUp();
    	$this->controller = $this->getMockController('AutoReplyFixcodes');
    }
    
    public function testIndex() {
        $this->testAction('/auto_reply_fixcodes/index');
        $this->assertEqual(!empty($this->vars['messages']), true);
        $this->assertTextContains('新建指令', $this->view);
    }

    public function testAdd() {
        $this->testAction('/auto_reply_fixcodes/add', array(
            'data' => array(
                'AutoReplyFixcode' => array(
                    'id' => '',
                    'noanswer' => '1',
                    'subscribe' => '1' 
                ),
                'AutoReplyFixcodeKeyword' => array(
                    '1370421551771' => array(
                        'AutoReplyKeyword' => array(
                            'name' => 'sss' 
                        ) 
                    ),
                    '1370421553988' => array(
                        'AutoReplyKeyword' => array(
                            'name' => 'AAA' 
                        ) 
                    ) 
                ),
                'AutoReplyFixcodeMessage' => array(
                    array(
                        'AutoReplyMessage' => array(
                            'type' => 'text',
                            'user_id' => 1,
                            'description' => 'message1' 
                        ) 
                    ) 
                ) 
            ) 
        ));
        
        $this->assertEqual($this->controller->Session->read('Message.flash.message'), '保存成功');
        
        $result = $this->controller->AutoReplyFixcode->read(null, 2);
        $this->assertEqual(!empty($result['AutoReplyFixcode']), true);
        $this->assertEqual(count($result['AutoReplyFixcodeKeyword']), 2);
        $this->assertEqual(!empty($result['AutoReplyFixcodeMessage']), true);
        $this->assertEqual(count($result['AutoReplyKeyword']), 2);
        $this->assertEqual(!empty($result['AutoReplyMessage']), true);
    }

/**
 * The news might be multi items and have existed in DB.
 */
    public function testAddNews() {
        $this->testAction('/auto_reply_fixcodes/add', array(
            'data' => array(
                'AutoReplyFixcode' => array(
                    'noanswer' => '1',
                    'subscribe' => '1',
                    'message_ids' => array(
                        '4',
                        '5' 
                    ) 
                ),
                'AutoReplyFixcodeKeyword' => array(
                    '1370421551771' => array(
                        'AutoReplyKeyword' => array(
                            'name' => 'key1' 
                        ) 
                    ),
                    '1370421553988' => array(
                        'AutoReplyKeyword' => array(
                            'name' => 'key2' 
                        ) 
                    ) 
                ) 
            ) 
        ));
        $this->assertEqual($this->controller->Session->read('Message.flash.message'), '保存成功');
        
        $result = $this->controller->AutoReplyFixcode->read(null, 2);
        $this->assertEqual(!empty($result['AutoReplyFixcode']), true);
        $this->assertEqual(count($result['AutoReplyFixcodeKeyword']), 2);
        $this->assertEqual(count($result['AutoReplyFixcodeMessage']), 2);
        $this->assertEqual(count($result['AutoReplyKeyword']), 2);
        $this->assertEqual(count($result['AutoReplyMessage']), 2);
    }
    
    public function testEdit() {
        $this->testAction('/auto_reply_fixcodes/add', array(
            'data' => array(
                'AutoReplyFixcode' => array(
                    'id' => '1',
                    'noanswer' => '1',
                    'subscribe' => '1' 
                ),
                'AutoReplyFixcodeKeyword' => array(
                    '1370421551771' => array(
                        'AutoReplyKeyword' => array(
                            'id' => '1',
                            'name' => 'kw1' 
                        ) 
                    ),
                    '1370421553988' => array(
                        'AutoReplyKeyword' => array(
                            'id' => '2',
                            'name' => 'kw2' 
                        ) 
                    ) 
                ),
                'AutoReplyFixcodeMessage' => array(
                    array(
                        'AutoReplyMessage' => array(
                            'id' => '2',
                            'type' => 'text',
                            'user_id' => 1,
                            'description' => 'message1' 
                        ) 
                    ) 
                ) 
            ) 
        ));
        
        $this->assertEqual($this->controller->Session->read('Message.flash.message'), '保存成功');
        
        $result = $this->controller->AutoReplyFixcode->read(null, 1);
        $this->assertEqual(!empty($result['AutoReplyFixcode']), true);
        $this->assertEqual(count($result['AutoReplyFixcodeKeyword']), 2);
        $this->assertEqual(count($result['AutoReplyFixcodeMessage']), 1);
        $this->assertEqual(count($result['AutoReplyKeyword']), 2);
        $this->assertEqual(count($result['AutoReplyMessage']), 1);
    }
    
    public function testEditing() {
    	$this->testAction('/auto_reply_fixcodes/edit/1');
    	$this->assertEqual(!empty($this->controller->request->data), true);
    	$this->assertEqual(!empty($this->vars['messages']) && is_array($this->vars['messages']), true);
    	$this->assertEqual(is_array($this->vars['selected_messages']), true);
    	$this->assertTextContains('编辑固定指令', $this->view);
    }
    
    public function testDelete() {
    	$this->testAction('/auto_reply_fixcodes/delete/1');
    	$resp = json_decode($this->result);
    	$this->assertEqual($resp->result, true);
    }
    
    public function testDeleteWithoutId() {
    	$this->testAction('/auto_reply_fixcodes/delete/');
    	$resp = json_decode($this->result);
    	$this->assertEqual($resp->result, false);
    }
    
    public function testDeleteNoPermission() {
    	$this->testAction('/auto_reply_fixcodes/delete/999');
    	$resp = json_decode($this->result);
    	$this->assertEqual($resp->result, false);
    }
}
