<?php
APP::uses('Saler', 'Model');
class SalerFixture extends CakeTestFixture {
	public $import = array('model' => 'Saler');

    public function init() {
        $this->records = array(
            array(
                'id' => 1,
                'name' => 'saler1',
                'shop_id' => 1,
                'open_id' => '',
                'contact' => 'The contact of saler1.',
                'token' => 'a1b2c3d4',
                'created' => date('Y-m-d H:i:s'),
                'modified' => date('Y-m-d H:i:s')
            ),
            array(
                'id' => 2,
                'name' => 'saler2',
                'shop_id' => 1,
                'open_id' => 'fromUser',
                'contact' => 'The contact of saler2.',
                'token' => 'e1f2g3h4',
                'created' => date('Y-m-d H:i:s'),
                'modified' => date('Y-m-d H:i:s')
            ),
        );
        parent::init();
    }
}