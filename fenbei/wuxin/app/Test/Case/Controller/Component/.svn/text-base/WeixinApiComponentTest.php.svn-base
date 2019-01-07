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
    
/**
 * @var RemoteComponent
 */
    public $remote;
    
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
    
    private function getMockController() {
    	$contrller = $this->generate('Api');
    	$contrller->constructClasses();
    	$contrller->Components->init($contrller);
    	$contrller->Session->write('Auth.User', array(
    		'id' => 1,
    	));
    	return $contrller;
    }
    
    public function setUp() {
        parent::setUp();
        $this->received['validate_fail'] = "<xml>
            <ToUserName><![CDATA[toUser]]></ToUserName>
            <FromUserName><![CDATA[new_user_need_validate]]></FromUserName>
            <CreateTime>1348831860</CreateTime>
            <MsgType><![CDATA[text]]></MsgType>
            <Content><![CDATA[2ju7k2n3]]></Content>
            <MsgId>1234567890123456</MsgId>
            </xml>";
        $this->received['validate_success'] = "<xml>
            <ToUserName><![CDATA[toUser]]></ToUserName>
            <FromUserName><![CDATA[new_user_need_validate]]></FromUserName>
            <CreateTime>1348831860</CreateTime>
            <MsgType><![CDATA[text]]></MsgType>
            <Content><![CDATA[a1b2c3d4]]></Content>
            <MsgId>1234567890123456</MsgId>
            </xml>";
        $this->received['validate_repeat'] = "<xml>
            <ToUserName><![CDATA[toUser]]></ToUserName>
            <FromUserName><![CDATA[new_user_need_validate]]></FromUserName>
            <CreateTime>1348831860</CreateTime>
            <MsgType><![CDATA[text]]></MsgType>
            <Content><![CDATA[e1f2g3h4]]></Content>
            <MsgId>1234567890123456</MsgId>
            </xml>";
        $this->received['coupon_success'] = "<xml>
            <ToUserName><![CDATA[toUser]]></ToUserName>
            <FromUserName><![CDATA[fromUser]]></FromUserName>
            <CreateTime>1348831860</CreateTime>
            <MsgType><![CDATA[text]]></MsgType>
            <Content><![CDATA[111111111111]]></Content>
            <MsgId>1234567890123456</MsgId>
            </xml>";
        $this->received['coupon_repeat'] = "<xml>
            <ToUserName><![CDATA[toUser]]></ToUserName>
            <FromUserName><![CDATA[fromUser]]></FromUserName>
            <CreateTime>1348831860</CreateTime>
            <MsgType><![CDATA[text]]></MsgType>
            <Content><![CDATA[123456789012]]></Content>
            <MsgId>1234567890123456</MsgId>
            </xml>";
        $this->received['coupon_wrong_format1'] = "<xml>
            <ToUserName><![CDATA[toUser]]></ToUserName>
            <FromUserName><![CDATA[fromUser]]></FromUserName>
            <CreateTime>1348831860</CreateTime>
            <MsgType><![CDATA[text]]></MsgType>
            <Content><![CDATA[12345678901]]></Content>
            <MsgId>1234567890123456</MsgId>
            </xml>";
        $this->received['coupon_wrong_format2'] = "<xml>
            <ToUserName><![CDATA[toUser]]></ToUserName>
            <FromUserName><![CDATA[fromUser]]></FromUserName>
            <CreateTime>1348831860</CreateTime>
            <MsgType><![CDATA[text]]></MsgType>
            <Content><![CDATA[12345678901a]]></Content>
            <MsgId>1234567890123456</MsgId>
            </xml>";
        $this->received['coupon_wrong_format3'] = "<xml>
            <ToUserName><![CDATA[toUser]]></ToUserName>
            <FromUserName><![CDATA[fromUser]]></FromUserName>
            <CreateTime>1348831860</CreateTime>
            <MsgType><![CDATA[text]]></MsgType>
            <Content><![CDATA[1234567890123]]></Content>
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
    
    public function testGetReceivedMessageWithValidateSalerFail() {
        $GLOBALS['HTTP_RAW_POST_DATA'] = $this->received['validate_fail'];
        $message = $this->api->getReceivedMessage()->getMessage();
        $xml = $message->toXML();
        $obj = $this->xml2Object($xml);
        $this->assertEqual($obj->Content, '验证码错误，身份认证失败');
    }
    
    public function testGetReceivedMessageWithValidateSalerSuccess() {
        $GLOBALS['HTTP_RAW_POST_DATA'] = $this->received['validate_success'];
        $message = $this->api->getReceivedMessage()->getMessage();
        $xml = $message->toXML();
        $obj = $this->xml2Object($xml);
        $this->assertEqual($obj->Content, '收银员[saler1]身份认证成功！');
    }
    
    public function testGetReceivedMessageWithValidateSalerRepeat() {
        $GLOBALS['HTTP_RAW_POST_DATA'] = $this->received['validate_repeat'];
        $message = $this->api->getReceivedMessage()->getMessage();
        $xml = $message->toXML();
        $obj = $this->xml2Object($xml);
        $this->assertEqual($obj->Content, '验证码已经使用过了，身份认证失败');
    }
    
    public function testGetReceivedMessageWithCouponCode() {
        $GLOBALS['HTTP_RAW_POST_DATA'] = $this->received['coupon_success'];
        $message = $this->api->getReceivedMessage()->getMessage();
        $xml = $message->toXML();
        $obj = $this->xml2Object($xml);
        $this->assertEqual($obj->Content, '兑换码有效。使用成功！');
    }
    
    public function testGetReceivedMessageWithCouponCodeRepeat() {
        $GLOBALS['HTTP_RAW_POST_DATA'] = $this->received['coupon_repeat'];
        $message = $this->api->getReceivedMessage()->getMessage();
        $xml = $message->toXML();
        $obj = $this->xml2Object($xml);
        $this->assertEqual($obj->Content, '兑换码已经使用过了, 请勿重复使用');
    }
    
    public function testGetReceivedMessageWithCouponCodeWrongFormat1() {
        $GLOBALS['HTTP_RAW_POST_DATA'] = $this->received['coupon_wrong_format1'];
        $message = $this->api->getReceivedMessage()->getMessage();
        $xml = $message->toXML();
        $obj = $this->xml2Object($xml);
        $this->assertEqual($obj->Content, '');
    }
    
    public function testGetReceivedMessageWithCouponCodeWrongFormat2() {
        $GLOBALS['HTTP_RAW_POST_DATA'] = $this->received['coupon_wrong_format2'];
        $message = $this->api->getReceivedMessage()->getMessage();
        $xml = $message->toXML();
        $obj = $this->xml2Object($xml);
        $this->assertEqual($obj->Content, '');
    }
    
    public function testGetReceivedMessageWithCouponCodeWrongFormat3() {
        $GLOBALS['HTTP_RAW_POST_DATA'] = $this->received['coupon_wrong_format3'];
        $message = $this->api->getReceivedMessage()->getMessage();
        $xml = $message->toXML();
        $obj = $this->xml2Object($xml);
        $this->assertEqual($obj->Content, '');
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