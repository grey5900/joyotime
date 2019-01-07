<?php
class AutoReplyMessageTagFixture extends CakeTestFixture {
	public $import = array('model' => 'AutoReplyMessageTag');
	
    public function init() {
        $this->records = array(
            array(
                'id' => 1,
                'auto_reply_tag_id' => 1,        // tag_name: news_test
                'auto_reply_message_id' => 1,    // message_type: custom
                'message_type' => 'custom',
            ),
            array(
                'id' => 2,
                'auto_reply_tag_id' => 2,        // tag_name: text_test
                'auto_reply_message_id' => 2,    // message_type: text
                'message_type' => 'text',
            ),
            array(
                'id' => 3,
                'auto_reply_tag_id' => 3,        // tag_name: music_test
                'auto_reply_message_id' => 3,    // message_type: music
                'message_type' => 'music',
            ),
            array(
                'id' => 4,
                'auto_reply_tag_id' => 4,        // tag_name: link_test
                'auto_reply_message_id' => 4,    // message_type: link
                'message_type' => 'link',
            ),
            array(
                'id' => 5,
                'auto_reply_tag_id' => 5,        // tag_name: map_test
                'auto_reply_message_id' => 5,    // message_type: map
                'message_type' => 'map',
            ),
        );
        parent::init();
    } 
}