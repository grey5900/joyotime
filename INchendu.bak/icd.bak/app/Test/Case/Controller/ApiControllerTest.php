<?php
App::uses('ApiController', 'Controller');

class ApiControllerTest extends ControllerTestCase {
    
/**
 * Pre-defined fixtrues want to load.
 *
 * @var array
 */
    public $fixtures = array(
    );
    
    public function setUp() {
    	parent::setUp();
    	
    	$this->received['text'] = "<xml>
            <ToUserName><![CDATA[toUser]]></ToUserName>
            <FromUserName><![CDATA[fromUser]]></FromUserName>
            <CreateTime>1348831860</CreateTime>
            <MsgType><![CDATA[text]]></MsgType>
            <Content><![CDATA[a]]></Content>
            <MsgId>1234567890123456</MsgId>
            </xml>";
    	$this->received['location'] = "<xml>
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
    }
    
    public function testHandle() {
        // text testing...
        $GLOBALS["HTTP_RAW_POST_DATA"] = $this->received['text'];
        $this->testAction('/api/handle/');
        $xml = $this->xml2Object($this->result);
        $this->assertIsA($xml, 'SimpleXMLElement');
        // location testing...
        $GLOBALS["HTTP_RAW_POST_DATA"] = $this->received['location'];
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
