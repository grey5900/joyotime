<?php
App::uses('ApiController', 'Controller');

class ApiControllerTest extends ControllerTestCase {
    
/**
 * Pre-defined fixtrues want to load.
 *
 * @var array
 */
    public $fixtures = array(
    	'app.auto_reply_message',
    	'app.auto_reply_echo',
    	'app.auto_reply_echo_regexp',
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
    	'app.auto_reply_fixcode',
    	'app.auto_reply_keyword',
    	'app.auto_reply_fixcode_keyword',
    	'app.auto_reply_fixcode_message',
    	'app.auto_reply_history',
    	'app.image_attachment',
        'app.weixin_config',
        'app.weixin_location_config',
    );
    
/**
 * It's token for identify.
 * 
 * @var string
 */
    private $token;
    
    public function setUp() {
    	parent::setUp();
    	$this->token = 'geek_phone';
    	
    	$this->received['text'] = "<xml>
            <ToUserName><![CDATA[toUser]]></ToUserName>
            <FromUserName><![CDATA[fromUser]]></FromUserName>
            <CreateTime>1348831860</CreateTime>
            <MsgType><![CDATA[text]]></MsgType>
            <Content><![CDATA[this is a test]]></Content>
            <MsgId>1234567890123456</MsgId>
            </xml>";
    	$this->received['wrong_message'] = "<xml>
            <ToUserName><![CDATA[toUser]]></ToUserName>
            <FromUserName><![CDATA[fromUser]]></FromUserName>
            <CreateTime>1348831860</CreateTime>
            <MsgType><![CDATA[wrong]]></MsgType>
            <Content><![CDATA[this is a test]]></Content>
            <MsgId>1234567890123456</MsgId>
            </xml>";
    	$this->received['fixcode'] = "<xml>
            <ToUserName><![CDATA[toUser]]></ToUserName>
            <FromUserName><![CDATA[fromUser]]></FromUserName>
            <CreateTime>1348831860</CreateTime>
            <MsgType><![CDATA[text]]></MsgType>
            <Content><![CDATA[kw1]]></Content>
            <MsgId>1234567890123456</MsgId>
            </xml>";
    }
    
    public function testHandleFixcode() {
        $GLOBALS["HTTP_RAW_POST_DATA"] = $this->received['fixcode'];
        $this->testAction('/api/'.$this->token);
        
        $xml = $this->xml2Object($this->result);
        $this->assertIsA($xml, 'SimpleXMLElement');
    }
    
    public function testHandle() {
        $GLOBALS["HTTP_RAW_POST_DATA"] = $this->received['text'];
        $this->testAction('/api/'.$this->token);
        
        $xml = $this->xml2Object($this->result);
        $this->assertIsA($xml, 'SimpleXMLElement');
    }
    
    public function testHandleWithUnknownToken() {
        $GLOBALS["HTTP_RAW_POST_DATA"] = $this->received['text'];
        $this->testAction('/api/unknown_token');
        
        $this->assertEqual($this->result, false);
    }
    
    public function testHandleWithoutToken() {
        $GLOBALS["HTTP_RAW_POST_DATA"] = $this->received['text'];
        $this->testAction('/api');
        
        $this->assertEqual($this->result, false);
    }
    
    public function testHandleWithWrongMessage() {
        $GLOBALS["HTTP_RAW_POST_DATA"] = $this->received['wrong_message'];
        $this->testAction('/api/'.$this->token);
        
        $this->assertEqual($this->result, false);
    }
    
    public function testHandleWithValid() {
        $api = $this->generate('Api');
        $api->constructClasses();
        $api->Components->init($api);
        
        $query = array(
            'echostr' => '123456',
            'timestamp' => (string)time(),
            'nonce' => (string)rand(10000, 99999),
        );
        $query['signature'] = 
            $api->WeixinApi->encryptByValid($query, $this->token);
        
        $this->testAction('/api/'.$this->token.'/?echostr='.$query['echostr'].'&timestamp='.$query['timestamp'].'&nonce='.$query['nonce'].'&signature='.$query['signature']);
        $this->assertEqual($this->result, $query['echostr']);        
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
