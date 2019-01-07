<?php
APP::uses('AutoReplyFixcodeMessage', 'Model');
class AutoReplyFixcodeMessageFixture extends CakeTestFixture {
	public $import = array('model' => 'AutoReplyFixcodeMessage');

    public function init() {
        $this->records = array(
            array(
                'id' => 1,
                'auto_reply_fixcode_id' => 1,
                'auto_reply_message_id' => 2,
            ),
        );
        parent::init();
    }
}