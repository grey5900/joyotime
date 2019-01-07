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
 * The model of auto replay message.
 *
 * @package       app.Testcase
 */
class AutoReplyMessageNewsTest extends CakeTestCase {
    
/**
 * The object of Model which is tested.
 * 
 * @var AutoReplyMessageNews
 */
    private $model;
    
/**
 * Pre-defined fixtrues want to load.
 * 
 * @var array
 */
    public $fixtures = array(
    	'app.auto_reply_message',
        'app.auto_reply_location',
        'app.auto_reply_location_message',
        'app.auto_reply_tag',
    	'app.auto_reply_message_tag',
    	'app.auto_reply_message_custom',
    	'app.auto_reply_message_news',
    	'app.auto_reply_message_music',
    	'app.auto_reply_message_exlink',
    	'app.auto_reply_message_location',
    	'app.auto_reply_category',
    	'app.auto_reply_config',
    	'app.auto_reply_config_tag',
    	'app.image_attachment',
    );
    
/**
 * (non-PHPdoc)
 * @see CakeTestCase::setUp()
 */
	public function setUp() {
	    parent::setUp();
	    $this->model = ClassRegistry::init('AutoReplyMessageNews');
	}
	
	public function testIncreaseRequestTotal() {
	    $auto_reply_message_id = 1;
	    // the related view_total is 1
	    $this->assertEqual($this->model->increaseViewTotal($auto_reply_message_id), true);
	    $this->assertEqual($this->model->increaseViewTotal($auto_reply_message_id), true);
	    $this->assertEqual($this->model->increaseViewTotal($auto_reply_message_id), true);
	    $first = $this->model->read(null, $auto_reply_message_id);
	    $this->assertEqual($first['AutoReplyMessageNews']['view_total'], 4);
	}
}