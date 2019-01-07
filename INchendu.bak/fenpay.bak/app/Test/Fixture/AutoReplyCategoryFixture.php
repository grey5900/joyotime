<?php
class AutoReplyCategoryFixture extends CakeTestFixture {
	public $import = array('model' => 'AutoReplyCategory');

    public function init() {
        $this->records = array(
            array(
                'id' => 1,
                'user_id' => 1,
                'title' => 'The first category item',
                'total' => 1,
                'created' => date('Y-m-d H:i:s'),
                'modified' => date('Y-m-d H:i:s') 
            ) 
        );
        parent::init();
    }
}