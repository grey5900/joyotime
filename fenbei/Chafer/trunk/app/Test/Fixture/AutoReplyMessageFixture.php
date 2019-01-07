<?php
APP::uses('AutoReplyMessageNews', 'Model');
class AutoReplyMessageFixture extends CakeTestFixture {
	public $import = array('model' => 'AutoReplyMessage');

    public function init() {
        $this->records = array(
            array(
                'id' => 1,
                'user_id' => 1,
                'type' => 'custom',
                'description' => 'The quick brown fox jumps over the lazy dog',
                'request_total' => 0,
                'created' => date('Y-m-d H:i:s'),
                'modified' => date('Y-m-d H:i:s') 
            ),
            array(
                'id' => 2,
                'user_id' => 1,
                'type' => 'text',
                'description' => 'The is a text message used for testing.',
                'request_total' => 0,
                'created' => date('Y-m-d H:i:s'),
                'modified' => date('Y-m-d H:i:s') 
            ),
            array(
                'id' => 3,
                'user_id' => 1,
                'type' => 'music',
                'description' => 'The is a music message used for testing.',
                'request_total' => 0,
                'created' => date('Y-m-d H:i:s'),
                'modified' => date('Y-m-d H:i:s') 
            ),
            array(
                'id' => 4,
                'user_id' => 1,
                'type' => AutoReplyMessageNews::LINK,
                'description' => 'The is a link message used for testing.',
                'request_total' => 0,
                'created' => date('Y-m-d H:i:s'),
                'modified' => date('Y-m-d H:i:s') 
            ),
            array(
                'id' => 5,
                'user_id' => 1,
                'type' => AutoReplyMessageNews::MAP,
                'description' => 'The is a map message used for testing.',
                'request_total' => 0,
                'created' => date('Y-m-d H:i:s'),
                'modified' => date('Y-m-d H:i:s') 
            ),
        );
        parent::init();
    }
}