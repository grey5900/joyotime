<?php
APP::uses('AutoReplyConfig', 'Model');
class AutoReplyConfigFixture extends CakeTestFixture {
	public $import = array('model' => 'AutoReplyConfig');

    public function init() {
        $this->records = array(
            array(
                'id' => 1,
                'user_id' => 1,
                'situation' => AutoReplyConfig::EVT_SUBSCRIBE,
                'created' => date('Y-m-d H:i:s'),
                'modified' => date('Y-m-d H:i:s') 
            ),
            array(
                'id' => 2,
                'user_id' => 1,
                'situation' => AutoReplyConfig::EVT_NOANSWER,
                'created' => date('Y-m-d H:i:s'),
                'modified' => date('Y-m-d H:i:s') 
            ),
        );
        parent::init();
    }
}