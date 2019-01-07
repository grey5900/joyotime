<?php
APP::uses('Shop', 'Model');
class ShopFixture extends CakeTestFixture {
	public $import = array('model' => 'Shop');

    public function init() {
        $this->records = array(
            array(
                'id' => 1,
                'name' => 'shop1',
                'saler_total' => 2,
                'coupon_total' => 0,
                'invalid_coupon_total' => 0,
                'created' => date('Y-m-d H:i:s'),
                'modified' => date('Y-m-d H:i:s')
            ),
        );
        parent::init();
    }
}