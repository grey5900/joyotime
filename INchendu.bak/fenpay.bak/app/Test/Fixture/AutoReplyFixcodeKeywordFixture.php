<?php
APP::uses('AutoReplyFixcodeKeyword', 'Model');
class AutoReplyFixcodeKeywordFixture extends CakeTestFixture {
	public $import = array('model' => 'AutoReplyFixcodeKeyword');

    public function init() {
        $this->records = array(
            array(
                'id' => 1,
                'auto_reply_fixcode_id' => 1,
                'auto_reply_keyword_id' => 1,
            ),
            array(
                'id' => 2,
                'auto_reply_fixcode_id' => 1,
                'auto_reply_keyword_id' => 2,
            ),
        );
        parent::init();
    }
}