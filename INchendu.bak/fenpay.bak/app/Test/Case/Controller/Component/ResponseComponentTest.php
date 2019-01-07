<?php
APP::uses('WeixinApiComponent', 'Controller/Component');
APP::uses('ResponseComponent', 'Controller/Component');
APP::uses('RemoteComponent', 'Controller/Component');
APP::uses('Controller', 'Controller');
APP::uses('AppController', 'Controller');
APP::uses('ApiController', 'Controller');
App::uses('CakeRequest', 'Network');
App::uses('CakeResponse', 'Network');
App::uses('ComponentCollection', 'Controller');
App::uses('AutoReplyConfig', 'Model');

class TestResponseComponentController extends Controller {

}

class ResponseComponentTest extends CakeTestCase {
    
/**
 * @var ResponseComponent
 */
    public $response;
    
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
		'app.auto_reply_echo',
		'app.auto_reply_echo_regexp',
		'app.auto_reply_fixcode_keyword',
		'app.auto_reply_keyword',
		'app.auto_reply_fixcode',
		'app.auto_reply_fixcode_message',
		'app.image_attachment',
		'app.weixin_config',
		'app.weixin_location_config',
    );
    
    private function getMockController() {
    	$contrller = $this->generate('AutoReply');
    	$contrller->constructClasses();
    	$contrller->Components->init($contrller);
    	$contrller->Session->write('Auth.User', array(
    		'id' => 1,
    	));
    	return $contrller;
    }
    
    public function setUp() {
        parent::setUp();
        $this->received['text'] = "<xml>
            <ToUserName><![CDATA[toUser]]></ToUserName>
            <FromUserName><![CDATA[fromUser]]></FromUserName>
            <CreateTime>1348831860</CreateTime>
            <MsgType><![CDATA[text]]></MsgType>
            <Content><![CDATA[123456789012]]></Content>
            <MsgId>1234567890123456</MsgId>
            </xml>";
        
        $this->received['test_location_by_keyword'] = "<xml>
            <ToUserName><![CDATA[toUser]]></ToUserName>
            <FromUserName><![CDATA[fromUser]]></FromUserName>
            <CreateTime>1348831860</CreateTime>
            <MsgType><![CDATA[text]]></MsgType>
            <Content><![CDATA[a]]></Content>
            <MsgId>1234567890123456</MsgId>
            </xml>";
        $this->received['test_location_by_point'] = "<xml>
              <ToUserName><![CDATA[toUser]]></ToUserName>
              <FromUserName><![CDATA[fromUser]]></FromUserName>
              <CreateTime>1351776360</CreateTime>
              <MsgType><![CDATA[location]]></MsgType>
              <Location_X>30.65740439438564</Location_X>
              <Location_Y>104.06618814468385</Location_Y>
              <Scale>20</Scale>
              <Label><![CDATA[位置信息]]></Label>
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
        
        // Setup our component and fake test controller
        $Collection = new ComponentCollection();
        $this->response = new ResponseComponent($Collection);
        $this->api = new WeixinApiComponent($Collection);
        
        $CakeRequest = new CakeRequest();
        $CakeResponse = new CakeResponse();
        $this->Controller = new TestResponseComponentController($CakeRequest, $CakeResponse);
        $this->Controller->Components->init($this->Controller);
        $this->response->startup($this->Controller);
        $this->api->startup($this->Controller);
        $GLOBALS['HTTP_RAW_POST_DATA'] = $this->received['text'];
        $this->response->setReceivePackage($this->api->getReceivedMessage($this->config));
        
        $this->userId = 1;
        $this->situation = AutoReplyConfig::EVT_NOANSWER;
    }
    
    public function testGetRemoteByRegexp() {
        // assert success...
        $content = '123456789012';
        $remote = $this->response->getRemoteByRegexp($content, $this->userId);
        $this->assertIsA($remote, 'RemoteHttp');
        // assert not match....
        $content = '1234567890';
        $remote = $this->response->getRemoteByRegexp($content, $this->userId);
        $this->assertIsA($remote, 'RemoteHttp');
        
        $GLOBALS['HTTP_RAW_POST_DATA'] = $this->received['test_location_by_keyword'];
        $this->response->setReceivePackage($this->api->getReceivedMessage($this->config));
        // assert success...
        $content = 'a';
        $remote = $this->response->getRemoteByRegexp($content, $this->userId);
        $this->assertIsA($remote, 'RemoteHttp');
    }
    
    public function testGetRemoteByLocation() {
        $GLOBALS['HTTP_RAW_POST_DATA'] = $this->received['test_location_by_point'];
        $this->response->setReceivePackage($this->api->getReceivedMessage($this->config));
        $remote = $this->response->getRemoteByLocation($this->userId);
        $this->assertIsA($remote, 'RemoteHttp');
        debug($remote->post());
    }
    
    public function testGetFixcode() {
        $send = $this->response->getFixcode('kw1', $this->userId);
        $this->assertIsA($send, 'WeixinSendPackage');
        $send = $this->response->getFixcode('kw2', $this->userId);
        $this->assertIsA($send, 'WeixinSendPackage');
        $send = $this->response->getFixcode('no_match_kw', $this->userId);
        $this->assertEqual($send, null);
    }
    
    public function testResponsePager() {
        $send = $this->response->getFixcode('kw2', $this->userId);
        $this->assertIsA($send, 'WeixinSendPackage');
        
        $pager = ResponsePager::getFromCache($this->userId);
        $this->assertIsA($pager, 'ResponsePager');
        $this->assertEqual($pager->openId, $this->userId);
        $this->assertEqual($pager->curPage, 1);
        $this->assertEqual($pager->maxPage, 1);
        $this->assertEqual($pager->reducer, 'FixcodeSendPackageReducer');
    }
    
    public function testGetNews() {
        $send = $this->response->getNews('news_test', $this->userId);
        $this->assertIsA($send, 'WeixinNewsSendPackage');
        $send = $this->response->getNews('text_test', $this->userId);
        $this->assertEqual($send, null);
    }
    
    public function tearDown() {
    	parent::tearDown();
    	unset($this->response);
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