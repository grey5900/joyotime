<?php
APP::uses('Withdrawal', 'Model');

class WithdrawalFixture extends CakeTestFixture {
	public $import = array('model' => 'Withdrawal');
	public $table = 'checkouts';
	
    public function init() {
        $this->records = array(
            /**
             * Checkout of withdraw don't processed yet...
             */
            array(
                '_id' => new MongoId('52302e996f159a11294ef447'),
                'user_id' => '51f0c30f6f159aec6fad8ce3',
                
                'type' => Withdrawal::TYPE,
                'amount' => array(
            		'time' => 5000,
            		'currency' => 'CNY',
            		'money' => 4940,
            		'fee' => 60,
            		'gateway' => 'alipay',
                ),
                'processed' => Withdrawal::NOT_PROCESSED_YET,
                'created' => new MongoDate(),
                'modified' => new MongoDate(),
            ),
        );
        parent::init();
    } 
}