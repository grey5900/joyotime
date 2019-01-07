<?php
APP::uses('CouponCode', 'Model');
class CouponCodeFixture extends CakeTestFixture {
	public $import = array('model' => 'CouponCode');

    public function init() {
        $this->records = array(
            array(
                'id' => 1,
                'saler_id' => 2,
                'shop_id' => 1,
                'coupon' => '123456789012',
                'invalid' => 0,
                'created' => date('Y-m-d H:i:s'),
                'modified' => date('Y-m-d H:i:s') 
            ),
        );
        parent::init();
    }
}