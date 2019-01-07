<?php
class AutoReplyMessageTagTest extends CakeTestCase {
    
/**
 * The object of Model which is tested.
 * 
 * @var AutoReplyMessageTag
 */
    private $model;
    
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
    	'app.auto_reply_fixcode_message',
    	'app.auto_reply_location_message',
    	'app.auto_reply_location',
    );
    
/**
 * (non-PHPdoc)
 * @see CakeTestCase::setUp()
 */
	public function setUp() {
	    parent::setUp();
	    $this->model = ClassRegistry::init('AutoReplyMessageTag');
	    $this->data = array(
    		'AutoReplyTag' => array(
    		    'name' => 'tag1'
    		),
	        'AutoReplyMessage' => array(
	            'id' => '1',
	            'type' => 'custom',
	            'description' => 'desc_test',
	        ),
	    );
	    
	    
	}
	
	public function testSaveAssociated() {
	    $this->assertEqual($this->model->saveAssociated($this->data, array('deep' => true)), true, 'save associated failed.');
        $saved = $this->model->find('first', array(
            'conditions' => array(
                'AutoReplyTag.name' => 'tag1'
            )
        ));
        debug($saved);
        $this->assertEqual($saved['AutoReplyTag']['name'], $this->data['AutoReplyTag']['name']);
        $this->assertEqual($saved['AutoReplyMessage']['id'], $this->data['AutoReplyMessage']['id']);
        $this->assertEqual($saved['AutoReplyMessage']['type'], $this->data['AutoReplyMessage']['type']);
        $this->assertEqual($saved['AutoReplyMessage']['description'], $this->data['AutoReplyMessage']['description']);
	}
	
	public function testSaveAssociatedMultiTags() {
	    $this->model = ClassRegistry::init('AutoReplyMessage');
	    $this->assertEqual($this->model->saveAssociated($this->multiTags(), array('deep' => true)), true, 'save associated failed.');
	    $saved = $this->model->find('all');
	    debug($saved);
	}
	
	private function multiTags() {
	    return array(
            'AutoReplyMessageTag' => array(
                array(
                    'AutoReplyTag' => array(
                        'name' => 'tag1'
                    ),
                    'message_type' => 'custom'
                ),
                array(
                    'AutoReplyTag' => array(
                        'name' => 'tag2'
                    ),
                    'message_type' => 'custom'
                ),
            ),
            'AutoReplyMessage' => array(
                'id' => '1',
                'type' => 'custom',
                'description' => 'desc_test' 
            ),
	        'AutoReplyMessageCustom' => array(
	            'custom_content' => 'dddd'
	        ),
	        'AutoReplyMessageNews' => array(
	            'title' => 'dddd',
	            'image_attachment_id' => '80',
	            'auto_reply_category_id' => '19'
	        )
        );
	}
}