<?php
App::uses('AppControllerTestCase', 'Test/Case/Controller');

class AutoReplyLocationsControllerTest extends AppControllerTestCase {
    
/**
 * Pre-defined fixtrues want to load.
 *
 * @var array
 */
    public $fixtures = array(
    	'app.auto_reply_message',
        'app.auto_reply_location',
        'app.auto_reply_location_message',
        'app.auto_reply_tag',
    	'app.auto_reply_message_tag',
    	'app.auto_reply_message_custom',
    	'app.auto_reply_message_news',
    	'app.auto_reply_message_music',
    	'app.auto_reply_message_exlink',
    	'app.auto_reply_message_location',
    	'app.auto_reply_category',
    	'app.auto_reply_config',
    	'app.auto_reply_config_tag',
    	'app.image_attachment',
    );
    
    public function setUp() {
    	parent::setUp();
    	$this->controller = $this->getMockController('AutoReplyLocations');
    }
    
    public function testIndex() {
        $this->testAction('/auto_reply_locations');
        
        $this->assertTextContains('The second location', $this->result);
        $this->assertEqual($this->vars['messages'][0]['AutoReplyLocation']['title'], 'The second location');
        $this->assertEqual($this->vars['messages'][0]['AutoReplyLocation']['longitude'], '104.146');
        $this->assertEqual($this->vars['messages'][0]['AutoReplyLocation']['latitude'], '30.7438');
        $this->assertEqual($this->vars['messages'][0]['AutoReplyLocation']['address'], 'test address for second location');
        $this->assertEqual($this->vars['messages'][0]['AutoReplyLocation']['description'], 'test description for second location');
        $this->assertEqual($this->vars['messages'][0]['ImageAttachment']['title'], 'the first image uploaded');
        $this->assertEqual($this->vars['messages'][0]['ImageAttachment']['original_url'], '/files/auto_replies/covers/1/test.jpg');
        $this->assertEqual($this->vars['messages'][0]['ImageAttachment']['thumbnail_url'], '/files/auto_replies/covers/1/thumbnail/test.jpg');
    }
    
    public function testAdding() {
        $this->testAction('/auto_reply_locations/add', array(
            'method' => 'GET'
        ));
        $this->assertTextContains('编辑地点', $this->view);
    }
    
    public function testAdd() {
        $this->testAction('/auto_reply_locations/add', array(
            'data' => array(
                'AutoReplyLocation' => array(
                    'id' => '',
                    'title' => 'title',
                    'image_attachment_id' => '',
                    'map_url' => 'map_url',
                    'longitude' => '123456.7890',
                    'latitude' => '789.1234560',
                    'address' => 'address',
                    'description' => 'description',
                )
            )
        ));
        $this->assertEqual($this->controller->Session->read('Message.flash.message'), '创建地理位置回复成功。');
    }
}
