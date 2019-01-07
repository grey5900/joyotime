<?php
APP::uses('Checkout', 'Model');

class CheckoutFixture extends CakeTestFixture {
	public $import = array('model' => 'Checkout');
	
    public function init() {
        $this->records = array(
            array(
                '_id' => new MongoId('52302e996f159a11294ef446'),
                'voice_id' => '51f225e26f159afa43e76aff',            
                'user_id' => '51f0c30f6f159aec6fad8ce3',
                'title' => 'Test following sorting',
                'type' => Checkout::TYPE_VOICE_COST,
                'cover' => array(
                    'source' => 'FuNNuNJtvN5fIP_um5IKU1ae5F-k',
                    'x80' => 'FuNNuNJtvN5fIP_um5IKU1ae5F-k?imageView\/2\/w\/80\/h\/80',
                    'x160' => 'FuNNuNJtvN5fIP_um5IKU1ae5F-k?imageView\/2\/w\/160\/h\/160',
                    'x640' => 'FuNNuNJtvN5fIP_um5IKU1ae5F-k?imageView\/2\/w\/640\/h\/640'
                ),
                'amount' => array(
                    'time' => 0
                ),
                'created' => new MongoDate(),
                'modified' => new MongoDate(),
            )
        );
        parent::init();
    } 
}