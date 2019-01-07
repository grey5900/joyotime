<?php
APP::uses('User', 'Model');
class UserFixture extends CakeTestFixture {
	public $import = array('model' => 'User');

    public function init() {
        $this->records = array(
            array(
                'id' => 1,
                'username' => 'admin',
                'password' => 'edd0958f7542575a2a5714befe590a30a78f769a',
                'name' => 'I am administrator',
                'created' => date('Y-m-d H:i:s'),
                'modified' => date('Y-m-d H:i:s')
            ),
        );
        parent::init();
    }
}