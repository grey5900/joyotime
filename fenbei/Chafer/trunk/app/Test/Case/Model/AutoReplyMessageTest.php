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
App::uses('AutoReplyMessageTag', 'Model');
/**
 * The model of auto replay message.
 *
 * @package       app.Testcase
 */
class AutoReplyMessageTest extends CakeTestCase {
    
/**
 * The object of Model which is tested.
 * 
 * @var AutoReplyMessage
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
	    $this->model = ClassRegistry::init('AutoReplyMessage');
	    $this->data = array(
    		'AutoReplyMessageTag' => array(
                array(
                    'AutoReplyTag' => array(
                        'id' => 1,
                        'name' => 'news_test'
                    ),
                    'message_type' => 'custom'
                ),
                array(
                    'AutoReplyTag' => array(
                        'name' => 'news_test_2'
                    ),
                    'message_type' => 'custom'
                ),
            ),
            'AutoReplyMessage' => array(
                'type' => 'custom',
                'user_id' => 1,
                'description' => 'desc_test' 
            ),
	        'AutoReplyMessageCustom' => array(
	            'custom_content' => 'test_custom_content'
	        ),
	        'AutoReplyMessageNews' => array(
	            'title' => 'test_title',
	            'image_attachment_id' => '1',
	            'auto_reply_category_id' => '1'
	        )
	    );
	    
	    $this->latitude = '30.608939741848967';
	    $this->longitude = '104.31238558876964';
	}
	
	public function testSaveAssociated() {
	    $this->assertEqual($this->model->saveAssociated(
	            $this->data, array('deep' => true)), true, 'save associated failed.');
        
        $saved = $this->model->find('first', array(
            'order' => array(
                'AutoReplyMessage.id desc'
            ),
            'contain' => array(
                'AutoReplyMessageNews',
                'AutoReplyMessageCustom',
                'AutoReplyTag',
            )
        ));
        
        $this->assertEqual($saved['AutoReplyTag'][0]['name'], 'news_test');
        $this->assertEqual($saved['AutoReplyTag'][1]['name'], 'news_test_2');
        $this->assertEqual($saved['AutoReplyMessage']['type'], 'custom');
        $this->assertEqual($saved['AutoReplyMessage']['description'], 'desc_test');
        $this->assertEqual($saved['AutoReplyMessage']['user_id'], '1');
        $this->assertEqual($saved['AutoReplyMessageCustom']['custom_content'], 'test_custom_content');
        $this->assertEqual($saved['AutoReplyMessageNews']['title'], 'test_title');
        $this->assertEqual($saved['AutoReplyMessageNews']['image_attachment_id'], '1');
        $this->assertEqual($saved['AutoReplyMessageNews']['auto_reply_category_id'], '1');
	}
	
// 	public function testGetNews() {
// 	    $tag = 'news_test';
// 	    $userId = 1;
	    
// 	    $saved = $this->model->getNews($tag, $userId);
// // 	    debug($saved);
	    
// 		$expected = array(
//             array(
//         		'AutoReplyMessage' => array(
//         			'id' => '1',
//         			'user_id' => '1',
//         			'type' => 'custom',
//         			'description' => 'The quick brown fox jumps over the lazy dog',
//         		    'request_total' => '0',
//         		),
//         		'AutoReplyMessageCustom' => array(
//         			'id' => '1',
//         			'auto_reply_message_id' => '1',
//         			'custom_content' => 'The quick brown fox jumps over the lazy dog'
//         		),
//         		'AutoReplyMessageNews' => array(
//         			'id' => '1',
//         			'auto_reply_message_id' => '1',
//         			'image_attachment_id' => '1',
//         			'auto_reply_category_id' => '1',
//         			'title' => 'The tested title',
//         			'ImageAttachment' => array(
//         				'id' => '1',
//         				'user_id' => '1',
//         				'title' => 'the first image uploaded',
//         				'size' => '5000',
//         				'type' => 'image/jpeg',
//         				'original_url' => '/files/auto_replies/covers/1/test.jpg',
//         				'thumbnail_url' => '/files/auto_replies/covers/1/thumbnail/test.jpg',
//         				'delete_url' => '/upload/cover?file=test.jpg',
//         				'delete_type' => 'DELETE',
//         			),
//         			'view_total' => '0'
//         		),
//         		'AutoReplyMessageExlink' => array(
//         			'id' => null,
//         			'auto_reply_message_id' => null,
//         			'exlink' => null
//         		),
//         		'AutoReplyMessageLocation' => array(
//         			'id' => null,
//         			'auto_reply_message_id' => null,
//         			'auto_reply_location_id' => null
//         		)
//         	)
//         );
// 		foreach($saved as &$item) {
// 			unset($item['AutoReplyMessage']['created']);
// 			unset($item['AutoReplyMessage']['modified']);
// 			unset($item['AutoReplyMessageNews']['ImageAttachment']['created']);
// 			unset($item['AutoReplyMessageNews']['ImageAttachment']['modified']);
// 		}
// 		$this->assertEqual($saved, $expected);
// 	}
	
// 	public function testGetNonews() {
// 		$tag = 'text_test';
// 		$userId = 1;
		
// 		$saved = $this->model->getNonews($tag, $userId);
// // 		debug($saved);
		
// 		$expected = array(
//         	'AutoReplyMessage' => array(
//         		'id' => '2',
//         		'user_id' => '1',
//         		'type' => 'text',
//         		'description' => 'The is a text message used for testing.',
//         	    'request_total' => '0'
//         	),
//         	'AutoReplyMessageMusic' => array(
//         		'id' => null,
//         		'auto_reply_message_id' => null,
//         		'title' => null,
//         		'music_url' => null
//         	)
//         );
// 		unset($saved['AutoReplyMessage']['created']);
// 		unset($saved['AutoReplyMessage']['modified']);
// 		$this->assertEqual($saved, $expected);
		
// 		$tag = 'music_test';
// 		$userId = 1;
		
// 		$saved = $this->model->getNonews($tag, $userId);
		
//         $expected = array(
//             'AutoReplyMessage' => array(
//         		'id' => '3',
//         		'user_id' => '1',
//         		'type' => 'music',
//         		'description' => 'The is a music message used for testing.',
//                 'request_total' => '0'
//         	),
//         	'AutoReplyMessageMusic' => array(
//         		'id' => '1',
//         		'auto_reply_message_id' => '3',
//         		'title' => 'The title for testing music',
//         		'music_url' => 'http://www.fenpay.com/test.mp3'
//         	)
//         );
// 		unset($saved['AutoReplyMessage']['created']);
// 		unset($saved['AutoReplyMessage']['modified']);
// 		$this->assertEqual($saved, $expected);
// 	}
	
// 	public function testGetDefault() {
// 	    $userId = 1;
	    
// 	    $saved = $this->model->getDefault($userId);
// // 	    debug($saved);
	    
//         $expected = array(
//         	'AutoReplyMessage' => array(
//         		'id' => '1',
//         		'user_id' => '1',
//         		'type' => 'custom',
//         		'description' => 'The quick brown fox jumps over the lazy dog',
//         	    'request_total' => '0'
//         	),
//         	'AutoReplyMessageCustom' => array(
//         		'id' => '1',
//         		'auto_reply_message_id' => '1',
//         		'custom_content' => 'The quick brown fox jumps over the lazy dog'
//         	),
//         	'AutoReplyMessageNews' => array(
//         		'id' => '1',
//         		'auto_reply_message_id' => '1',
//         		'image_attachment_id' => '1',
//         		'auto_reply_category_id' => '1',
//         		'title' => 'The tested title',
//         		'ImageAttachment' => array(
//         			'id' => '1',
//         			'user_id' => '1',
//         			'title' => 'the first image uploaded',
//         			'size' => '5000',
//         			'type' => 'image/jpeg',
//         			'original_url' => '/files/auto_replies/covers/1/test.jpg',
//         			'thumbnail_url' => '/files/auto_replies/covers/1/thumbnail/test.jpg',
//         			'delete_url' => '/upload/cover?file=test.jpg',
//         			'delete_type' => 'DELETE',
//         		),
//         		'view_total' => '0'
//         	),
//         	'AutoReplyMessageMusic' => array(
//         		'id' => null,
//         		'auto_reply_message_id' => null,
//         		'title' => null,
//         		'music_url' => null
//         	),
//         	'AutoReplyMessageExlink' => array(
//         		'id' => null,
//         		'auto_reply_message_id' => null,
//         		'exlink' => null
//         	),
//         	'AutoReplyMessageLocation' => array(
//         		'id' => null,
//         		'auto_reply_message_id' => null,
//         		'auto_reply_location_id' => null
//         	)
//         );
        
// 	    unset($saved['AutoReplyMessage']['created']);
// 	    unset($saved['AutoReplyMessage']['modified']);
// 	    unset($saved['AutoReplyMessageNews']['ImageAttachment']['created']);
// 	    unset($saved['AutoReplyMessageNews']['ImageAttachment']['modified']);
// 	    $this->assertEqual($saved, $expected);
// 	}
	
	public function testIncreaseRequestTotal() {
	    $auto_reply_message_id = 1;
	    $this->assertEqual($this->model->increaseRequestTotal($auto_reply_message_id), true);
	    $this->assertEqual($this->model->increaseRequestTotal($auto_reply_message_id), true);
	    $this->assertEqual($this->model->increaseRequestTotal($auto_reply_message_id), true);
	    $first = $this->model->find('first', array(
	        'conditions' => array(
	            'AutoReplyMessage.id' => $auto_reply_message_id
	        ),
	        'recursive' => -1
	    ));
// 	    debug($first); 
	    $this->assertEqual($first['AutoReplyMessage']['request_total'], 3);
	}
	
	public function testIncreaseViewTotal() {
	    $auto_reply_message_id = 1;
	    // initial view_total is 1
	    $this->model->AutoReplyMessageNews->increaseViewTotal($auto_reply_message_id);
	    $one = $this->model->AutoReplyMessageNews->read(null, $auto_reply_message_id);
	    $this->assertEqual($one['AutoReplyMessageNews']['view_total'], 2);
	}
}