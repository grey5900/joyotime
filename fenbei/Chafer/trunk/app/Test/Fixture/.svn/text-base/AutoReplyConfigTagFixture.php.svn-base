<?php
APP::uses('AutoReplyConfig', 'Model');
class AutoReplyConfigTagFixture extends CakeTestFixture {
	public $import = array('model' => 'AutoReplyConfigTag');

    public function init() {
        $this->records = array(
            array(
                'auto_reply_config_id' => 1,    // situation: subscribe
                'auto_reply_tag_id' => 1,       // tag_name: news_test
            ),
            array(
                'auto_reply_config_id' => 2,    // situation: no_answer
                'auto_reply_tag_id' => 1,       // tag_name: news_test
            ),
        );
        parent::init();
    }
}