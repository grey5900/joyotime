<?php
/**
 * The project of FenPay is a CRM platform based on Weixin MP API.
 *
 * Use it to communicates with Weixin MP.
 *
 * PHP 5
 *
 * FenPay(tm) : FenPay (http://fenpay.com)
 * Copyright (c) in.chengdu.cn. (http://in.chengdu.cn)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) in.chengdu.cn. (http://in.chengdu.cn)
 * @link          http://fenpay.com FenPay(tm) Project
 * @since         FenPay(tm) v 0.0.1
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
App::uses('AutoReplyMessageNews', 'Model');
/**
 * The testcase of Shop
 *
 * @package       app.Testcase.Model
 */
class ShopTest extends CakeTestCase {
    
/**
 * The object of Model which is tested.
 * 
 * @var Shop
 */
    private $model;
    
/**
 * Pre-defined fixtrues want to load.
 * 
 * @var array
 */
    public $fixtures = array(
    	'app.saler',
		'app.shop',
		'app.coupon_code',
    );
    
/**
 * (non-PHPdoc)
 * @see CakeTestCase::setUp()
 */
	public function setUp() {
	    parent::setUp();
	    $this->model = ClassRegistry::init('Shop');
	}
	
	public function testIncreaseSalerTotal() {
	    $shop_id = 1;
	    // the initialized saler_total is 2
	    $this->assertEqual($this->model->increaseSalerTotal($shop_id), true);
	    $this->assertEqual($this->model->increaseSalerTotal($shop_id), true);
	    $this->assertEqual($this->model->increaseSalerTotal($shop_id), true);
	    $shop = $this->model->read(null, $shop_id);
	    $this->assertEqual($shop['Shop']['saler_total'], 5);
	}
	
	public function testDecreaseSalerTotal() {
	    $shop_id = 1;
	    // the initialized saler_total is 2
	    $this->assertEqual($this->model->decreaseSalerTotal($shop_id), true);
	    $this->assertEqual($this->model->decreaseSalerTotal($shop_id), true);
	    $this->assertEqual($this->model->decreaseSalerTotal($shop_id), true);
	    $shop = $this->model->read(null, $shop_id);
	    $this->assertEqual($shop['Shop']['saler_total'], -1);
	}
	
	public function testIncreaseCouponTotal() {
	    $shop_id = 1;
	    // the initialized coupon_total is 0
	    $this->assertEqual($this->model->increaseCouponTotal($shop_id), true);
	    $this->assertEqual($this->model->increaseCouponTotal($shop_id), true);
	    $this->assertEqual($this->model->increaseCouponTotal($shop_id), true);
	    $shop = $this->model->read(null, $shop_id);
	    $this->assertEqual($shop['Shop']['coupon_total'], 3);
	}

	public function testIncreaseInvalidCouponTotal() {
		$shop_id = 1;
		// the initialized invalid_coupon_total is 0
		$this->assertEqual($this->model->increaseInvalidCouponTotal($shop_id), true);
		$this->assertEqual($this->model->increaseInvalidCouponTotal($shop_id), true);
		$this->assertEqual($this->model->increaseInvalidCouponTotal($shop_id), true);
		$shop = $this->model->read(null, $shop_id);
		$this->assertEqual($shop['Shop']['invalid_coupon_total'], 3);
	}
	
	public function testDecreaseInvalidCouponTotal() {
		$shop_id = 1;
		// the initialized invalid_coupon_total is 0
		$this->assertEqual($this->model->decreaseInvalidCouponTotal($shop_id), true);
		$this->assertEqual($this->model->decreaseInvalidCouponTotal($shop_id), true);
		$this->assertEqual($this->model->decreaseInvalidCouponTotal($shop_id), true);
		$shop = $this->model->read(null, $shop_id);
		$this->assertEqual($shop['Shop']['invalid_coupon_total'], -3);
	}
}