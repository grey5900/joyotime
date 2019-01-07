<?php
APP::uses('WeixinApiComponent', 'Controller/Component');
APP::uses('Controller', 'Controller');
APP::uses('AppController', 'Controller');
APP::uses('ApiController', 'Controller');
App::uses('CakeRequest', 'Network');
App::uses('CakeResponse', 'Network');
App::uses('ComponentCollection', 'Controller');
App::uses('AutoReplyConfig', 'Model');

class TestApiController extends Controller {

}

class WeixinApiComponentTest extends CakeTestCase {
    
    public function setUp() {
        $from_user = 'from_user_1';
        parent::setUp();
        $this->received['first_request'] = "<xml>
            <ToUserName><![CDATA[toUser]]></ToUserName>
            <FromUserName><![CDATA[".$from_user."]]></FromUserName>
            <CreateTime>1348831860</CreateTime>
            <MsgType><![CDATA[text]]></MsgType>
            <Content><![CDATA[火锅]]></Content>
            <MsgId>1234567890123456</MsgId>
            </xml>";
        $this->received['second_page'] = "<xml>
            <ToUserName><![CDATA[toUser]]></ToUserName>
            <FromUserName><![CDATA[".$from_user."]]></FromUserName>
            <CreateTime>1348831860</CreateTime>
            <MsgType><![CDATA[text]]></MsgType>
            <Content><![CDATA[n]]></Content>
            <MsgId>1234567890123456</MsgId>
            </xml>";
        $this->received['first_location'] = "<xml>
            <ToUserName><![CDATA[toUser]]></ToUserName>
            <FromUserName><![CDATA[".$from_user."]]></FromUserName>
            <CreateTime>1348831860</CreateTime>
            <MsgType><![CDATA[location]]></MsgType>
            <Location_X>30.7438</Location_X>
            <Location_Y>104.146</Location_Y>
            <Scale>20</Scale>
            <Label><![CDATA[The information about location]]></Label>
            <MsgId>1234567890123456</MsgId>
            </xml>";
        $this->received['no_found'] = "<xml>
            <ToUserName><![CDATA[toUser]]></ToUserName>
            <FromUserName><![CDATA[".$from_user."]]></FromUserName>
            <CreateTime>1348831860</CreateTime>
            <MsgType><![CDATA[text]]></MsgType>
            <Content><![CDATA[no_12_found_dd]]></Content>
            <MsgId>1234567890123456</MsgId>
            </xml>";
        $this->received['key_is_english'] = "<xml>
            <ToUserName><![CDATA[toUser]]></ToUserName>
            <FromUserName><![CDATA[".$from_user."]]></FromUserName>
            <CreateTime>1348831860</CreateTime>
            <MsgType><![CDATA[text]]></MsgType>
            <Content><![CDATA[tw]]></Content>
            <MsgId>1234567890123456</MsgId>
            </xml>";
        $this->received['key_is_character'] = "<xml>
            <ToUserName><![CDATA[toUser]]></ToUserName>
            <FromUserName><![CDATA[".$from_user."]]></FromUserName>
            <CreateTime>1348831860</CreateTime>
            <MsgType><![CDATA[text]]></MsgType>
            <Content><![CDATA[i]]></Content>
            <MsgId>1234567890123456</MsgId>
            </xml>";
        
        // Setup our component and fake test controller
        $Collection = new ComponentCollection();
        $this->api = new WeixinApiComponent($Collection);
        
        $CakeRequest = new CakeRequest();
        $CakeResponse = new CakeResponse();
        $this->Controller = new TestApiController($CakeRequest, $CakeResponse);
        $this->Controller->Components->init($this->Controller);
        $this->api->startup($this->Controller);
    }
    
//     public function testPaginateBySearchKeyword() {
//         $GLOBALS['HTTP_RAW_POST_DATA'] = $this->received['first_request'];
//         $message = $this->api->getReceivedMessage()->getMessage();
//         $xml = $message->toXML();
//         $obj = $this->xml2Object($xml);
//         debug($obj);
        
//         $GLOBALS['HTTP_RAW_POST_DATA'] = $this->received['second_page'];
//         $message = $this->api->getReceivedMessage()->getMessage();
//         $xml = $message->toXML();
//         $obj = $this->xml2Object($xml);
//         debug($obj);
//     }
    
//     public function testPaginateBySearchLocation() {
//         $GLOBALS['HTTP_RAW_POST_DATA'] = $this->received['first_location'];
//         $message = $this->api->getReceivedMessage()->getMessage();
//         $xml = $message->toXML();
//         $obj = $this->xml2Object($xml);
//         debug($obj);
        
//         $GLOBALS['HTTP_RAW_POST_DATA'] = $this->received['second_page'];
//         $message = $this->api->getReceivedMessage()->getMessage();
//         $xml = $message->toXML();
//         $obj = $this->xml2Object($xml);
//         debug($obj);
//     }
    
//     public function testPaginateByNofound() {
//         $GLOBALS['HTTP_RAW_POST_DATA'] = $this->received['no_found'];
//         $message = $this->api->getReceivedMessage()->getMessage();
//         $xml = $message->toXML();
//         $obj = $this->xml2Object($xml);
//         debug($obj);
        
//         $GLOBALS['HTTP_RAW_POST_DATA'] = $this->received['second_page'];
//         $message = $this->api->getReceivedMessage()->getMessage();
//         $xml = $message->toXML();
//         $obj = $this->xml2Object($xml);
//         debug($obj);
        
//         $GLOBALS['HTTP_RAW_POST_DATA'] = $this->received['second_page'];
//         $message = $this->api->getReceivedMessage()->getMessage();
//         $xml = $message->toXML();
//         $obj = $this->xml2Object($xml);
//         debug($obj);
//     }
    
//     public function testLocationAndKeyword() {
//         // first search location
//         $GLOBALS['HTTP_RAW_POST_DATA'] = $this->received['first_location'];
//         $message = $this->api->getReceivedMessage()->getMessage();
//         $xml = $message->toXML();
//         $obj = $this->xml2Object($xml);
//         debug($obj);
        
//         // secord search text as keyword
//         $GLOBALS['HTTP_RAW_POST_DATA'] = $this->received['first_request'];
//         $message = $this->api->getReceivedMessage()->getMessage();
//         $xml = $message->toXML();
//         $obj = $this->xml2Object($xml);
//         debug($obj);
//     }
    
    public function testLocationAndKeywordWithEnglish() {
        // first search location
        $GLOBALS['HTTP_RAW_POST_DATA'] = $this->received['first_location'];
        $message = $this->api->getReceivedMessage()->getMessage();
        $xml = $message->toXML();
        $obj = $this->xml2Object($xml);
        debug($obj);
        
        // secord search text as keyword
        $GLOBALS['HTTP_RAW_POST_DATA'] = $this->received['key_is_english'];
        $message = $this->api->getReceivedMessage()->getMessage();
        $xml = $message->toXML();
        $obj = $this->xml2Object($xml);
        debug($obj);
        
        // secord search text as keyword
        $GLOBALS['HTTP_RAW_POST_DATA'] = $this->received['key_is_character'];
        $message = $this->api->getReceivedMessage()->getMessage();
        $xml = $message->toXML();
        $obj = $this->xml2Object($xml);
        debug($obj);
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