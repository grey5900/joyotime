<?php
APP::uses('Receipt', 'Model');
class ReceiptFixture extends CakeTestFixture {
	public $import = array('model' => 'Receipt');
	
    public function init() {
        $this->records = array(
            array(
                '_id' => new MongoId('61fff30f6f159ddd6fad8ce3'),
                'user_id' => '51f0c30f6f159aec6fad8ce3',
    			'type' => Receipt::TYPE_ALIPAY,
                'status' => Receipt::STATUS_PENDING,
                'amount' => array(
    				'time' => 1200
    			),
                'created' => new MongoDate(),
                'modified' => new MongoDate(),
            ),
        );
        parent::init();
    } 
}