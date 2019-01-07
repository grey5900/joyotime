<?php
APP::uses('AutoReplyEchoRegexp', 'Model');
class AutoReplyEchoRegexpFixture extends CakeTestFixture {
	public $import = array('model' => 'AutoReplyEchoRegexp');

    public function init() {
        $this->records = array(
            array(
                'id' => 1,
                'auto_reply_echo_id' => 1,
                'regexp' => '[0-9a-zA-Z]{12,}',
            ),
            array(
                'id' => 2,
                'auto_reply_echo_id' => 2,
                'regexp' => '(.*)',
            ),
        );
        parent::init();
    }
}