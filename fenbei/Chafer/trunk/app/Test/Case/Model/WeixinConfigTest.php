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
 * The model of auto replay message.
 *
 * @package       app.Testcase
 */
class WeixinConfigTest extends CakeTestCase {
    
/**
 * The object of Model which is tested.
 * 
 * @var WeixinConfig
 */
    private $model;
    
/**
 * Pre-defined fixtrues want to load.
 * 
 * @var array
 */
    public $fixtures = array(
    	'app.weixin_config',
    	'app.weixin_location_config',
    	'app.image_attachment',
    );
    
/**
 * (non-PHPdoc)
 * @see CakeTestCase::setUp()
 */
	public function setUp() {
	    parent::setUp();
	    $this->model = ClassRegistry::init('WeixinConfig');
	    $this->userId = 1;
	}
	
	public function testIncreaseUserNum() {
	    // the initial user num is 2
	    $this->assertEqual($this->model->increaseUserNum($this->userId), true);
	    $expected = $this->model->find('first', array(
	        'conditions' => array(
	            'user_id' => $this->userId
	        )
	    ));
	    
	    $this->assertEqual($expected['WeixinConfig']['initial_user_num'], 3);
	}
	
	public function testDecreaseUserNum() {
	    // the initial user num is 2
	    $this->assertEqual($this->model->decreaseUserNum($this->userId), true);
	    $this->assertEqual($this->model->decreaseUserNum($this->userId), true);
	    $this->assertEqual($this->model->decreaseUserNum($this->userId), false);
	    $expected = $this->model->find('first', array(
	        'conditions' => array(
	            'user_id' => $this->userId
	        )
	    ));
	    
	    $this->assertEqual($expected['WeixinConfig']['initial_user_num'], 0);
	}
}