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
App::uses('AutoReplyLocation', 'Model');
/**
 * The model of auto replay message.
 *
 * @package       app.Testcase
 */
class AutoReplyLocationTest extends CakeTestCase {
    
/**
 * The object of Model which is tested.
 * 
 * @var AutoReplyLocation
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
	    $this->model = ClassRegistry::init('AutoReplyLocation');
	    $this->latitude = 30.608939741848967;
	    $this->longitude = 104.31238558876964;
	    $this->userId = 1;
	}
	
	/**
	 * Expected = array(
        	(int) 0 => array(
        		'AutoReplyLocation' => array(
        			'id' => '2',
        			'user_id' => '1',
        			'image_attachment_id' => '1',
        			'title' => 'The second location',
        			'longitude' => '104.146',
        			'latitude' => '30.7438',
        			'map_url' => 'http://map.soso.com/2',
        			'address' => 'test address for second location',
        			'description' => null,
        			'created' => '2013-05-31 06:08:11',
        			'modified' => '2013-05-31 06:08:11'
        		),
        		'ImageAttachment' => array(
        			'id' => '1',
        			'user_id' => '1',
        			'title' => 'the first image uploaded',
        			'size' => '5000',
        			'type' => 'image/jpeg',
        			'original_url' => '/files/auto_replies/covers/1/test.jpg',
        			'thumbnail_url' => '/files/auto_replies/covers/1/thumbnail/test.jpg',
        			'delete_url' => '/upload/cover?file=test.jpg',
        			'delete_type' => 'DELETE',
        			'created' => '2013-05-31 06:08:11',
        			'modified' => '2013-05-31 06:08:11'
        		),
        		'AutoReplyLocationMessage' => array(
        			(int) 0 => array(
        				'id' => '2',
        				'auto_reply_message_id' => '1',
        				'auto_reply_location_id' => '2'
        			)
        		),
        		'AutoReplyMessage' => array(
        			(int) 0 => array(
        				'id' => '1',
        				'user_id' => '1',
        				'type' => 'custom',
        				'description' => 'The quick brown fox jumps over the lazy dog',
        				'created' => '2013-05-31 06:08:11',
        				'modified' => '2013-05-31 06:08:11',
        				'AutoReplyLocationMessage' => array(
        					'id' => '2',
        					'auto_reply_message_id' => '1',
        					'auto_reply_location_id' => '2'
        				)
        			)
        		)
        	),
        	(int) 1 => array(
        		'AutoReplyLocation' => array(
        			'id' => '1',
        			'user_id' => '1',
        			'image_attachment_id' => '1',
        			'title' => 'The first location',
        			'longitude' => '104.046',
        			'latitude' => '30.6438',
        			'map_url' => 'http://map.soso.com',
        			'address' => 'test address',
        			'description' => null,
        			'created' => '2013-05-31 06:08:11',
        			'modified' => '2013-05-31 06:08:11'
        		),
        		'ImageAttachment' => array(
        			'id' => '1',
        			'user_id' => '1',
        			'title' => 'the first image uploaded',
        			'size' => '5000',
        			'type' => 'image/jpeg',
        			'original_url' => '/files/auto_replies/covers/1/test.jpg',
        			'thumbnail_url' => '/files/auto_replies/covers/1/thumbnail/test.jpg',
        			'delete_url' => '/upload/cover?file=test.jpg',
        			'delete_type' => 'DELETE',
        			'created' => '2013-05-31 06:08:11',
        			'modified' => '2013-05-31 06:08:11'
        		),
        		'AutoReplyLocationMessage' => array(
        			(int) 0 => array(
        				'id' => '1',
        				'auto_reply_message_id' => '1',
        				'auto_reply_location_id' => '1'
        			)
        		),
        		'AutoReplyMessage' => array(
        			(int) 0 => array(
        				'id' => '1',
        				'user_id' => '1',
        				'type' => 'custom',
        				'description' => 'The quick brown fox jumps over the lazy dog',
        				'created' => '2013-05-31 06:08:11',
        				'modified' => '2013-05-31 06:08:11',
        				'AutoReplyLocationMessage' => array(
        					'id' => '1',
        					'auto_reply_message_id' => '1',
        					'auto_reply_location_id' => '1'
        				)
        			)
        		)
        	)
        );
	 */
	public function testGetLocations() {
	    $locations = $this->model->getLocations($this->latitude, $this->longitude, $this->userId, 3);
	    $this->assertEqual(count($locations), 2, 'Expect get 2 locations, but it seems not.');
	    $this->assertEqual(isset($locations[0]['AutoReplyLocation']), true, 'There is no found AutoReplyLocation');
	    $this->assertEqual(isset($locations[0]['ImageAttachment']), true, 'There is no found ImageAttachment');
	    $this->assertEqual(isset($locations[0]['AutoReplyLocationMessage']), true, 'There is no found AutoReplyLocationMessage');
	    $this->assertEqual(isset($locations[0]['AutoReplyMessage']), true, 'There is no found AutoReplyMessage');
	    $this->assertEqual(isset($locations[1]['AutoReplyLocation']), true, 'There is no found AutoReplyLocation');
	    $this->assertEqual(isset($locations[1]['ImageAttachment']), true, 'There is no found ImageAttachment');
	    $this->assertEqual(isset($locations[1]['AutoReplyLocationMessage']), true, 'There is no found AutoReplyLocationMessage');
	    $this->assertEqual(isset($locations[1]['AutoReplyMessage']), true, 'There is no found AutoReplyMessage');
	}
	
	/**
	 * Expected = array(
        	'AutoReplyLocation' => array(
        		'id' => '2',
        		'user_id' => '1',
        		'image_attachment_id' => '1',
        		'title' => 'The second location',
        		'longitude' => '104.146',
        		'latitude' => '30.7438',
        		'map_url' => 'http://map.soso.com/2',
        		'address' => 'test address for second location',
        		'description' => null,
        		'created' => '2013-05-31 07:48:48',
        		'modified' => '2013-05-31 07:48:48'
        	),
        	'ImageAttachment' => array(
        		'id' => '1',
        		'user_id' => '1',
        		'title' => 'the first image uploaded',
        		'size' => '5000',
        		'type' => 'image/jpeg',
        		'original_url' => '/files/auto_replies/covers/1/test.jpg',
        		'thumbnail_url' => '/files/auto_replies/covers/1/thumbnail/test.jpg',
        		'delete_url' => '/upload/cover?file=test.jpg',
        		'delete_type' => 'DELETE',
        		'created' => '2013-05-31 07:48:48',
        		'modified' => '2013-05-31 07:48:48'
        	),
        	'AutoReplyMessage' => array(
        		(int) 0 => array(
        			'id' => '1',
        			'user_id' => '1',
        			'type' => 'custom',
        			'description' => 'The quick brown fox jumps over the lazy dog',
        			'created' => '2013-05-31 07:48:48',
        			'modified' => '2013-05-31 07:48:48',
        			'AutoReplyLocationMessage' => array(
        				'id' => '2',
        				'auto_reply_message_id' => '1',
        				'auto_reply_location_id' => '2'
        			),
        			'AutoReplyMessageNews' => array(
        				'id' => '1',
        				'auto_reply_message_id' => '1',
        				'image_attachment_id' => '1',
        				'auto_reply_category_id' => '1',
        				'title' => 'The tested title',
        				'ImageAttachment' => array(
        					'id' => '1',
        					'user_id' => '1',
        					'title' => 'the first image uploaded',
        					'size' => '5000',
        					'type' => 'image/jpeg',
        					'original_url' => '/files/auto_replies/covers/1/test.jpg',
        					'thumbnail_url' => '/files/auto_replies/covers/1/thumbnail/test.jpg',
        					'delete_url' => '/upload/cover?file=test.jpg',
        					'delete_type' => 'DELETE',
        					'created' => '2013-05-31 07:48:48',
        					'modified' => '2013-05-31 07:48:48'
        				)
        			)
        		)
        	)
        )
	 */
	public function testGetLocationExtends() {
	    $userId = 1;
	    $limit = 3;
	    $loc = $this->model->getLocationExtends($this->latitude, $this->longitude, $userId, $limit);
// 	    debug($loc);
	    
	    $this->assertEqual(!empty($loc['AutoReplyLocation']), true);
	    $this->assertEqual(!empty($loc['ImageAttachment']), true);
	    $this->assertEqual(count($loc['AutoReplyMessage']), 1);
	    $this->assertEqual(!empty($loc['AutoReplyMessage'][0]['AutoReplyMessageNews']), true);
	    $this->assertEqual(!empty($loc['AutoReplyMessage'][0]['AutoReplyMessageNews']['ImageAttachment']), true);
	}
	
	public function testIncreaseRequestTotal() {
	    $location_id = 1;
	    // initial of request_total is 1
	    $this->assertEqual($this->model->increaseRequestTotal($location_id), true);
	    $this->assertEqual($this->model->increaseRequestTotal($location_id), true);
	    $this->assertEqual($this->model->increaseRequestTotal($location_id), true);
	    $data = $this->model->read(null, $location_id);
	    $this->assertEqual($data['AutoReplyLocation']['request_total'], 4);
	}
}