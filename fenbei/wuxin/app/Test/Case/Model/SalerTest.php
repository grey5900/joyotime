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

/**
 * The testcase of Saler
 *
 * @package       app.Testcase.Model
 */
class SalerTest extends CakeTestCase {
    
/**
 * The object of Model which is tested.
 * 
 * @var Saler
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
	    $this->model = ClassRegistry::init('Saler');
	}
	
	public function testGeneToken() {
	    $id = 1;
	    $this->assertEqual(strlen($this->model->geneToken($id)), 8);
	}
}