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

class TestRemoteComponentController extends Controller {

}

class RemoteComponentTest extends CakeTestCase {
    
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
            <Content><![CDATA[2ju7k2n3]]></Content>
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
            ) 
        );
        
        // Setup our component and fake test controller
        $Collection = new ComponentCollection();
        $this->remote = new RemoteComponent($Collection);
        
        $CakeRequest = new CakeRequest();
        $CakeResponse = new CakeResponse();
        $this->Controller = new TestRemoteComponentController($CakeRequest, $CakeResponse);
        $this->Controller->Components->init($this->Controller);
        $this->remote->startup($this->Controller);
        $GLOBALS['HTTP_RAW_POST_DATA'] = $this->received['text'];
        
        $this->userId = 1;
    }
    
    public function testPost() {
        $this->remote->setRaw($this->received['text']);
        $this->remote->setUrl('http://wuxin.joyotime.com/api/handle');
        $resp = $this->remote->post();
        debug($resp);
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