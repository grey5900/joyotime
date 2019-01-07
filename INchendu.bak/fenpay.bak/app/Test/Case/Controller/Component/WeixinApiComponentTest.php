<?php
APP::uses('WeixinApiComponent', 'Controller/Component');
APP::uses('Controller', 'Controller');
App::uses('CakeRequest', 'Network');
App::uses('CakeResponse', 'Network');
App::uses('ComponentCollection', 'Controller');

class TestApiController extends Controller {
    
}

class WeixinApiComponentTest extends CakeTestCase {
    
    /**
     * @var WeixinApiComponent
     */
    public $api;
    
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
		'app.weixin_config',
		'app.weixin_location_config',
    );
    
    public function setUp() {
        parent::setUp();
        // Setup our component and fake test controller
        $Collection = new ComponentCollection();
        $this->api = new WeixinApiComponent($Collection);
        $CakeRequest = new CakeRequest();
        $CakeResponse = new CakeResponse();
        $this->Controller = new TestApiController($CakeRequest, $CakeResponse);
        $this->api->startup($this->Controller);
        
        $this->received['test_location'] = "<xml>
            <ToUserName><![CDATA[toUser]]></ToUserName>
            <FromUserName><![CDATA[fromUser]]></FromUserName>
            <CreateTime>1348831860</CreateTime>
            <MsgType><![CDATA[location]]></MsgType>
            <Location_X>30.7438</Location_X>
            <Location_Y>104.146</Location_Y>
            <Scale>20</Scale>
            <Label><![CDATA[The information about location]]></Label>
            <MsgId>1234567890123456</MsgId>
            </xml>";
        
        // mockup for config array
        // In the real world, the config array is consisted
        // with WeixinConfig and WeixinLocationConfig.
        $this->config = array(
            'WeixinConfig' => array(
                'id' => 1,
                'name' => 'test_account',
                'user_id' => 1,
                'weixin_id' => 'GeekPhone',
                'auto_reply_sign_id' => '',
                'initial_user_num' => 20,
                'interface' => 'http://www.fenpay.com/api/geek_phone',
                'token' => 'geek_phone',
                'image_attachment_id' => '',
                'message_type' => 'hybird',
                'location_num' => 'single',
                'created' => date('Y-m-d H:i:s'),
                'modified' => date('Y-m-d H:i:s') 
            ),
            'WeixinLocationConfig' => array(
                'id' => 1,
                'weixin_config_id' => 1,
                'image_attachment_id' => 1,
                'type' => 'single',
                'title' => 'test title for mulitply location',
                'ImageAttachment' => array(
                    'id' => 1,
                    'user_id' => 1,
                    'title' => 'the first image uploaded',
                    'size' => 5000,
                    'type' => 'image/jpeg',
                    'original_url' => '/files/auto_replies/covers/1/test.jpg',
                    'thumbnail_url' => '/files/auto_replies/covers/1/thumbnail/test.jpg',
                    'delete_url' => '/upload/cover?file=test.jpg',
                    'delete_type' => 'DELETE',
                    'created' => date('Y-m-d H:i:s'),
                    'modified' => date('Y-m-d H:i:s') 
                ) 
            ) 
        );
    }
    
    public function testDistance() {
        $GLOBALS["HTTP_RAW_POST_DATA"] = $this->received['test_location'];
        $post = $this->api->getReceivedMessage($this->config);
        $resp = $post->getMessage();
//         $obj = $this->xml2Object($resp->toXML());
    	var_dump($resp->toXML());
    }
    
    
    public function tearDown() {
    	parent::tearDown();
    	// Clean up after we're done
    	unset($this->api);
    	unset($this->Controller);
    }
    
/**
 * Helper method.
 *
 * It uses to convert xml string to object as SimpleXMLElement.
 *
 * @param string $xml
 * @return SimpleXMLElement
 */
    private function xml2Object($xml) {
    	return simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA);
    }
}