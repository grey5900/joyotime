<?php
APP::uses('Broadcast', 'Model');
APP::uses('GiftBroadcast', 'Model');

class GiftBroadcastFixture extends CakeTestFixture {
	public $import = array('model' => 'GiftBroadcast');
	
    public function init() {
        $this->records = array(
            array(
                '_id' => new MongoId('55f0c30f6f166aec6fad8ce3'),
                'type' => GiftBroadcast::TYPE,    
        	    /**
        	     * A list of user who has read it already
        	     */  
        	    'readers' => array(),
        	    'amount' => array(
        	        'time' => 1000
        	    ),
        	    'message' => 'mockup_gift',
        	    'finished' => new MongoDate(time() + 3600),
        	    'created' => new MongoDate(),
        	    'modified' => new MongoDate()
            ),
        );
        parent::init();
    } 
}