<?php
App::uses('AppControllerTestCase', 'Test/Case/Controller');

class AutoReplyLocationExtendsControllerTest extends AppControllerTestCase {
    
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
    
    public function setUp() {
    	parent::setUp();
    	$this->controller = $this->getMockController('AutoReplyLocationExtends');
    }
    
    public function testIndex() {
        $this->testAction('/auto_reply_location_extends/index');
        $this->assertEqual(!empty($this->vars['messages']), true);
        $this->assertTextContains('新建扩展信息', $this->view);
    }
    
    public function testDelete() {
    	$this->testAction('/auto_reply_location_extends/delete/');
    	$resp = json_decode($this->result);
    	$this->assertEqual($resp->result, false);
    	    	
    	$this->testAction('/auto_reply_location_extends/delete/1');
    	$resp = json_decode($this->result);
    	$this->assertEqual($resp->result, true);
    }
    
    public function testEdit() {
        $this->testAction('/auto_reply_location_extends/edit/1');
        $this->assertEqual(!empty($this->vars['messages']), true);
        $this->assertEqual(!empty($this->vars['selected_locations']), true);
        $this->assertEqual(!empty($this->vars['locations']), true);
        $this->assertTextContains('编辑扩展信息', $this->view);
    }
}
