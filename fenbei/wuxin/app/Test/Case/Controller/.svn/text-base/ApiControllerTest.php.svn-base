<?php
App::uses('ApiController', 'Controller');

class ApiControllerTest extends ControllerTestCase {
    
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
    
    public function setUp() {
    	parent::setUp();
    	
    	$this->received['text'] = "<xml>
            <ToUserName><![CDATA[toUser]]></ToUserName>
            <FromUserName><![CDATA[fromUser]]></FromUserName>
            <CreateTime>1348831860</CreateTime>
            <MsgType><![CDATA[text]]></MsgType>
            <Content><![CDATA[this is a test]]></Content>
            <MsgId>1234567890123456</MsgId>
            </xml>";
    }
    
    public function testHandle() {
        $GLOBALS["HTTP_RAW_POST_DATA"] = $this->received['text'];
        $this->testAction('/api/handle/');
        
        $xml = $this->xml2Object($this->result);
        $this->assertIsA($xml, 'SimpleXMLElement');
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
