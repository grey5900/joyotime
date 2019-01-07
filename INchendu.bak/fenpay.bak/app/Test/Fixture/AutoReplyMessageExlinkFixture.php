<?php
class AutoReplyMessageExlinkFixture extends CakeTestFixture {
	public $import = array('model' => 'AutoReplyMessageExlink');
	
	public function init() {
        $this->records = array(
            array(
                'id' => 1,
                'auto_reply_message_id' => 4,
                'exlink' => 'http://www.fenpay.com',
            )
        );
        parent::init();
    }
}