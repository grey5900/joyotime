<?php
APP::uses('AutoReplyEcho', 'Model');
class AutoReplyEchoFixture extends CakeTestFixture {
	public $import = array('model' => 'AutoReplyEcho');

    public function init() {
        $this->records = array(
            array(
                'id' => 1,
                'user_id' => 1,
                'url' => 'http://wx.joyotime.com',
                'enabled_regexp' => 1,
                'enabled_location' => 0,
                'sent_num' => 0,
                'created' => date('Y-m-d H:i:s'),
                'modified' => date('Y-m-d H:i:s') 
            ),
            array(
                'id' => 2,
                'user_id' => 1,
                'url' => 'http://incd.joyotime.com/api/handle/',
                'enabled_regexp' => 1,
                'enabled_location' => 1,
                'sent_num' => 0,
                'created' => date('Y-m-d H:i:s'),
                'modified' => date('Y-m-d H:i:s') 
            ),
        );
        parent::init();
    }
}