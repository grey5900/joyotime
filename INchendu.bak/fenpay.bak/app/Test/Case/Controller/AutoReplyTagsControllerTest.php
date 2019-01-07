<?php
App::uses('AutoReplyTagsController', 'Controller');

class AutoReplyTagsControllerTest extends ControllerTestCase {
    
    public $fixtures = array(
		'app.auto_reply_tag',
    );
    
    public function setUp() {
    	parent::setUp();
    }
    
    public function testCheck() {
        $this->testAction('/auto_reply_tags/check/tag_tested');
        $this->assertTextContains(json_encode(array('result'=>true)), $this->contents);
    }
    
    public function testCheckWithoutTag() {
        $this->testAction('/auto_reply_tags/check');
        $this->assertTextContains('"result":false', $this->contents);
    }
}
