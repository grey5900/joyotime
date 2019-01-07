<?php
APP::uses('MessageBroadcast', 'Model');
class MessageBroadcastFixture extends CakeTestFixture {
	public $import = array('model' => 'MessageBroadcast');
	
    public function init() {
        $this->records = array(
            array(
                '_id' => new MongoId('55f1c30f6f166aec6fad8ce3'),
                'type' => MessageBroadcast::TYPE,    
        	    /**
        	     * A list of user who has read it already
        	     */  
        	    'readers' => array(),
        	    'read_total' => 0,
        	    'message' => 'mockup_message',
        	    'finished' => new MongoDate(time() + 3600),
        	    'created' => new MongoDate(),
        	    'modified' => new MongoDate()
            ),
        );
        parent::init();
    } 
}