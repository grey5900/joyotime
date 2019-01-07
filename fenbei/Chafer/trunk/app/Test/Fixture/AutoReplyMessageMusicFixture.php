<?php
class AutoReplyMessageMusicFixture extends CakeTestFixture {

    public $import = array(
        'model' => 'AutoReplyMessageMusic' 
    );

    public function init() {
        $this->records = array(
            array(
                'id' => 1,
                'auto_reply_message_id' => 3,
                'title' => 'The title for testing music',
                'music_url' => 'http://www.fenpay.com/test.mp3',
            ) 
        );
        parent::init();
    }
}