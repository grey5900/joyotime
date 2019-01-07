<?php
App::uses('WeixinApi', 'Model');

class WeixinApiTest extends CakeTestCase {
    
/**
 * The object of Model which is tested.
 * 
 * @var WeixinApi
 */
    private $api;
    
/**
 * An array of pre-defined all types received package.
 * 
 * @var array
 */
    private $received;
    
/**
 * Mockup of weixin config array
 * In the real world, the config array is consisted 
 * with WeixinConfig and WeixinLocationConfig.
 * 
 * @var array
 */
    private $config;
    
/**
 * An array of pre-defined several kinds of 
 * error formatting received package.
 * 
 * @var array
 */
    private $errorFormatReceived;
    
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
	    $this->api = ClassRegistry::init('WeixinApi');
	    
	    $this->received['text'] = "<xml>
            <ToUserName><![CDATA[toUser]]></ToUserName>
            <FromUserName><![CDATA[fromUser]]></FromUserName>
            <CreateTime>1348831860</CreateTime>
            <MsgType><![CDATA[text]]></MsgType>
            <Content><![CDATA[this is a test]]></Content>
            <MsgId>1234567890123456</MsgId>
            </xml>";
	    $this->received['news_test'] = "<xml>
            <ToUserName><![CDATA[toUser]]></ToUserName>
            <FromUserName><![CDATA[fromUser]]></FromUserName>
            <CreateTime>1348831860</CreateTime>
            <MsgType><![CDATA[text]]></MsgType>
            <Content><![CDATA[news_test]]></Content>
            <MsgId>1234567890123456</MsgId>
            </xml>";
	    $this->received['link_test'] = "<xml>
            <ToUserName><![CDATA[toUser]]></ToUserName>
            <FromUserName><![CDATA[fromUser]]></FromUserName>
            <CreateTime>1348831860</CreateTime>
            <MsgType><![CDATA[text]]></MsgType>
            <Content><![CDATA[link_test]]></Content>
            <MsgId>1234567890123456</MsgId>
            </xml>";
	    $this->received['map_test'] = "<xml>
            <ToUserName><![CDATA[toUser]]></ToUserName>
            <FromUserName><![CDATA[fromUser]]></FromUserName>
            <CreateTime>1348831860</CreateTime>
            <MsgType><![CDATA[text]]></MsgType>
            <Content><![CDATA[map_test]]></Content>
            <MsgId>1234567890123456</MsgId>
            </xml>";
	    $this->received['text_test'] = "<xml>
            <ToUserName><![CDATA[toUser]]></ToUserName>
            <FromUserName><![CDATA[fromUser]]></FromUserName>
            <CreateTime>1348831860</CreateTime>
            <MsgType><![CDATA[text]]></MsgType>
            <Content><![CDATA[text_test]]></Content>
            <MsgId>1234567890123456</MsgId>
            </xml>";
	    $this->received['music_test'] = "<xml>
            <ToUserName><![CDATA[toUser]]></ToUserName>
            <FromUserName><![CDATA[fromUser]]></FromUserName>
            <CreateTime>1348831860</CreateTime>
            <MsgType><![CDATA[text]]></MsgType>
            <Content><![CDATA[music_test]]></Content>
            <MsgId>1234567890123456</MsgId>
            </xml>";
	    $this->received['default_test'] = "<xml>
            <ToUserName><![CDATA[toUser]]></ToUserName>
            <FromUserName><![CDATA[fromUser]]></FromUserName>
            <CreateTime>1348831860</CreateTime>
            <MsgType><![CDATA[text]]></MsgType>
            <Content><![CDATA[unknown_test]]></Content>
            <MsgId>1234567890123456</MsgId>
            </xml>";
	    $this->received['image'] = "<xml>
            <ToUserName><![CDATA[toUser]]></ToUserName>
            <FromUserName><![CDATA[fromUser]]></FromUserName>
            <CreateTime>1348831860</CreateTime>
            <MsgType><![CDATA[image]]></MsgType>
            <PicUrl><![CDATA[this is a url]]></PicUrl>
            <MsgId>1234567890123456</MsgId>
            </xml>";
	    $this->received['location'] = "<xml>
            <ToUserName><![CDATA[toUser]]></ToUserName>
            <FromUserName><![CDATA[fromUser]]></FromUserName>
            <CreateTime>1348831860</CreateTime>
            <MsgType><![CDATA[location]]></MsgType>
            <Location_X>23.134521</Location_X>
            <Location_Y>113.358803</Location_Y>
            <Scale>20</Scale>
            <Label><![CDATA[The information about location]]></Label>
            <MsgId>1234567890123456</MsgId>
            </xml>";
	    $this->received['test_location'] = "<xml>
            <ToUserName><![CDATA[toUser]]></ToUserName>
            <FromUserName><![CDATA[fromUser]]></FromUserName>
            <CreateTime>1348831860</CreateTime>
            <MsgType><![CDATA[location]]></MsgType>
            <Location_X>30.608939741848967</Location_X>
            <Location_Y>104.31238558876964</Location_Y>
            <Scale>20</Scale>
            <Label><![CDATA[The information about location]]></Label>
            <MsgId>1234567890123456</MsgId>
            </xml>";
	    $this->received['link'] = "<xml>
            <ToUserName><![CDATA[toUser]]></ToUserName>
            <FromUserName><![CDATA[fromUser]]></FromUserName>
            <CreateTime>1348831860</CreateTime>
            <MsgType><![CDATA[".AutoReplyMessageNews::LINK."]]></MsgType>
            <Title><![CDATA[The title of link]]></Title>
            <Description><![CDATA[The description of link]]></Description>
            <Url><![CDATA[url]]></Url>
            <MsgId>1234567890123456</MsgId>
            </xml>";
	    $this->received['event'] = "<xml>
            <ToUserName><![CDATA[toUser]]></ToUserName>
            <FromUserName><![CDATA[fromUser]]></FromUserName>
            <CreateTime>1348831860</CreateTime>
            <MsgType><![CDATA[event]]></MsgType>
            <Event><![CDATA[EVENT]]></Event>
            <EventKey><![CDATA[EVENTKEY]]></EventKey>
            <MsgId>1234567890123456</MsgId>
            </xml>";
	    $this->received['test_subscribe'] = "<xml>
	        <ToUserName><![CDATA[gh_251a88144711]]></ToUserName><br />
            <FromUserName><![CDATA[oAIbqjrHMCQM5usC9yFm_IBfePOI]]></FromUserName><br />
            <CreateTime>1370240776</CreateTime><br />
            <MsgType><![CDATA[event]]></MsgType><br />
            <Event><![CDATA[subscribe]]></Event><br />
            <EventKey><![CDATA[]]></EventKey><br />
            </xml>";
	    
	    $this->errorFormatReceived['without_msgtype'] = "<xml>
            <ToUserName><![CDATA[toUser]]></ToUserName>
            <FromUserName><![CDATA[fromUser]]></FromUserName>
            <CreateTime>1348831860</CreateTime>
            <MsgType><![CDATA[]]></MsgType>
            <Event><![CDATA[EVENT]]></Event>
            <EventKey><![CDATA[EVENTKEY]]></EventKey>
            <MsgId>1234567890123456</MsgId>
            </xml>";
	    $this->errorFormatReceived['with_unknown_msgtype'] = "<xml>
            <ToUserName><![CDATA[toUser]]></ToUserName>
            <FromUserName><![CDATA[fromUser]]></FromUserName>
            <CreateTime>1348831860</CreateTime>
            <MsgType><![CDATA[unknown]]></MsgType>
            <Event><![CDATA[EVENT]]></Event>
            <EventKey><![CDATA[EVENTKEY]]></EventKey>
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
    	        'modified' => date('Y-m-d H:i:s'),
	        ),
	        'WeixinLocationConfig' => array(
	            'id' => 1,
	            'weixin_config_id' => 1,
	            'image_attachment_id' => 1,
	            'type' => 'multiply',
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
/**
 * assert success
 */
	public function testGetReceivedPackageWithText() {
	    $GLOBALS["HTTP_RAW_POST_DATA"] = $this->received['text'];
	    
	    $post = $this->api->getReceivedMessage($this->config);
	    $this->assertEqual($post->getToUserName(), 'toUser');
	    $this->assertEqual($post->getFromUserName(), 'fromUser');
	    $this->assertEqual($post->getCreateTime(), '1348831860');
	    $this->assertEqual($post->getMsgType(), 'text');
	    $this->assertEqual($post->getContent(), 'this is a test');
	    $this->assertEqual($post->getMsgId(), '1234567890123456');
	}
	
/**
 * assert success
 */
	public function testGetReceivedPackageWithImage() {
	    $GLOBALS["HTTP_RAW_POST_DATA"] = $this->received['image'];
	    
	    $post = $this->api->getReceivedMessage($this->config);
	    $this->assertEqual($post->getToUserName(), 'toUser');
	    $this->assertEqual($post->getFromUserName(), 'fromUser');
	    $this->assertEqual($post->getCreateTime(), '1348831860');
	    $this->assertEqual($post->getMsgType(), 'image');
	    $this->assertEqual($post->getPicUrl(), 'this is a url');
	    $this->assertEqual($post->getMsgId(), '1234567890123456');
	}
	
/**
 * assert success
 */
	public function testGetReceivedPackageWithLocation() {
	    $GLOBALS["HTTP_RAW_POST_DATA"] = $this->received['location'];
	    
	    $post = $this->api->getReceivedMessage($this->config);
	    $this->assertEqual($post->getToUserName(), 'toUser');
	    $this->assertEqual($post->getFromUserName(), 'fromUser');
	    $this->assertEqual($post->getCreateTime(), '1348831860');
	    $this->assertEqual($post->getMsgType(), 'location');
	    $this->assertEqual($post->getLocation()['x'], '23.134521');
	    $this->assertEqual($post->getLocation()['y'], '113.358803');
	    $this->assertEqual($post->getScale(), '20');
	    $this->assertEqual($post->getLabel(), 'The information about location');
	    $this->assertEqual($post->getMsgId(), '1234567890123456');
	}
	
/**
 * assert success
 */
	public function testGetReceivedPackageWithLink() {
	    $GLOBALS["HTTP_RAW_POST_DATA"] = $this->received['link'];
	    
	    $post = $this->api->getReceivedMessage($this->config);
	    $this->assertEqual($post->getToUserName(), 'toUser');
	    $this->assertEqual($post->getFromUserName(), 'fromUser');
	    $this->assertEqual($post->getCreateTime(), '1348831860');
	    $this->assertEqual($post->getMsgType(), AutoReplyMessageNews::LINK);
	    $this->assertEqual($post->getTitle(), 'The title of link');
	    $this->assertEqual($post->getDescription(), 'The description of link');
	    $this->assertEqual($post->getUrl(), 'url');
	    $this->assertEqual($post->getMsgId(), '1234567890123456');
	}
	
/**
 * assert success
 */
	public function testGetReceivedPackageWithEvent() {
	    $GLOBALS["HTTP_RAW_POST_DATA"] = $this->received['event'];
	    
	    $post = $this->api->getReceivedMessage($this->config);
	    $this->assertEqual($post->getToUserName(), 'toUser');
	    $this->assertEqual($post->getFromUserName(), 'fromUser');
	    $this->assertEqual($post->getCreateTime(), '1348831860');
	    $this->assertEqual($post->getMsgType(), 'event');
	    $this->assertEqual($post->getEvent(), 'EVENT');
	    $this->assertEqual($post->getEventKey(), 'EVENTKEY');
	    $this->assertEqual($post->getMsgId(), '1234567890123456');
	}
	
/**
 * assert failure
 */
	public function testGetMessageWithoutMsgType() {
	    $GLOBALS["HTTP_RAW_POST_DATA"] = $this->errorFormatReceived['without_msgtype'];
	    
	    try {
	        $post = $this->api->getReceivedMessage($this->config);
	    } catch(WeixinApiException $wxae) {
	        
	    }
	    
	    $this->assertEqual($wxae->getMessage(), "There is no found 'MsgType' in data package received.");
	    $this->assertIsA($wxae->getLogData(), 'SimpleXMLElement', 'The saved post data is SimpleXMLElement');
	}
	
/**
 * assert failure
 */
	public function testGetMessageWithUnknownMsgType() {
	    $GLOBALS["HTTP_RAW_POST_DATA"] = $this->errorFormatReceived['with_unknown_msgtype'];
	    
	    try {
	        $post = $this->api->getReceivedMessage($this->config);
	    } catch(WeixinApiException $wxae) {
	        
	    }
	    
	    $this->assertEqual($wxae->getMessage(), "The api named 'unknown' looks like not be supported yet.");
	    $this->assertIsA($wxae->getLogData(), 'SimpleXMLElement', 'The saved post data is SimpleXMLElement');
	}
	
/**
 * assert success
 */
	public function testValid() {
	    $echostr = '123456';
	    $timestamp = (string)time();
	    $nonce = (string)rand(10000, 99999);
	    $token = 'weixin_api_test';
	    $data = compact('echostr', 'timestamp', 'nonce');
	    $signature = $this->api->encryptByValid($data, $token);
	    $data['signature'] = $signature;
	    
	    $this->assertEqual($this->api->valid($data, $token), $echostr);
	    unset($data['signature']);
	    $this->assertEqual($this->api->valid($data, $token), FALSE);
	}
	
	public function testSendPackageWithNews() {
	    $GLOBALS["HTTP_RAW_POST_DATA"] = $this->received['news_test'];
	     
	    $post = $this->api->getReceivedMessage($this->config);
	    $resp = $post->getMessage();
        $obj = $this->xml2Object($resp->toXML());
        
        $this->assertEqual($obj->MsgType, 'news');
        $this->assertEqual($obj->ArticleCount, '1');
        $this->assertEqual($obj->Articles[0]->item->Title, 'The tested title');
        $this->assertEqual($obj->Articles[0]->item->Description, 'The quick brown fox jumps over the lazy dog');
        $this->assertEqual($obj->Articles[0]->item->PicUrl, Configure::read('Domain.main').'/files/auto_replies/covers/1/test.jpg');
        $this->assertEqual($obj->Articles[0]->item->Url, Configure::read('Domain.main').'/auto_reply_messages/news/1');
	}
	
	public function testSendPackageWithLink() {
	    $GLOBALS["HTTP_RAW_POST_DATA"] = $this->received['link_test'];
	     
	    $post = $this->api->getReceivedMessage($this->config);
	    $resp = $post->getMessage();
        $obj = $this->xml2Object($resp->toXML());
        
        $this->assertEqual($obj->MsgType, 'news');
        $this->assertEqual($obj->ArticleCount, '1');
        $this->assertEqual($obj->Articles[0]->item->Title, 'The tested title for link');
        $this->assertEqual($obj->Articles[0]->item->Description, 'The is a link message used for testing.');
        $this->assertEqual($obj->Articles[0]->item->PicUrl, Configure::read('Domain.main').'/files/auto_replies/covers/1/test.jpg');
        $this->assertEqual($obj->Articles[0]->item->Url, 'http://www.fenpay.com');
	}
	
	public function testSendPackageWithMap() {
	    $GLOBALS["HTTP_RAW_POST_DATA"] = $this->received['map_test'];
	     
	    $post = $this->api->getReceivedMessage($this->config);
	    $resp = $post->getMessage();
        $obj = $this->xml2Object($resp->toXML());
        
        $this->assertEqual($obj->MsgType, 'news');
        $this->assertEqual($obj->ArticleCount, '1');
        $this->assertEqual($obj->Articles[0]->item->Title, 'The tested title for map');
        $this->assertEqual($obj->Articles[0]->item->Description, 'The is a map message used for testing.');
        $this->assertEqual($obj->Articles[0]->item->PicUrl, Configure::read('Domain.main').'/files/auto_replies/covers/1/test.jpg');
        $this->assertEqual($obj->Articles[0]->item->Url, 'http://map.soso.com');
	}
	
	public function testSendPackageWithText() {
	    $GLOBALS["HTTP_RAW_POST_DATA"] = $this->received['text_test'];
	     
	    $post = $this->api->getReceivedMessage($this->config);
	    $resp = $post->getMessage();
        $obj = $this->xml2Object($resp->toXML());
        
        $this->assertEqual($obj->MsgType, 'text');
        $this->assertEqual($obj->Content, 'The is a text message used for testing.');
	}
	
	public function testSendPackageWithMusic() {
	    $GLOBALS["HTTP_RAW_POST_DATA"] = $this->received['music_test'];
	     
	    $post = $this->api->getReceivedMessage($this->config);
	    $resp = $post->getMessage();
        $obj = $this->xml2Object($resp->toXML());
        
        $this->assertEqual($obj->MsgType, 'music');
        $this->assertEqual($obj->Music->Title, 'The title for testing music');
        $this->assertEqual($obj->Music->Description, 'The is a music message used for testing.');
        $this->assertEqual($obj->Music->MusicUrl, 'http://www.fenpay.com/test.mp3');
        $this->assertEqual($obj->Music->HQMusicUrl, 'http://www.fenpay.com/test.mp3');
	}
	
	public function testSendPackageWithDefault() {
	    $GLOBALS["HTTP_RAW_POST_DATA"] = $this->received['default_test'];
	     
	    $post = $this->api->getReceivedMessage($this->config);
	    $resp = $post->getMessage();
        $obj = $this->xml2Object($resp->toXML());
        
        $this->assertEqual($obj->MsgType, 'news');
        $this->assertEqual($obj->ArticleCount, '1');
        $this->assertEqual($obj->Articles[0]->item->Title, 'The tested title');
        $this->assertEqual($obj->Articles[0]->item->Description, 'The quick brown fox jumps over the lazy dog');
        $this->assertEqual($obj->Articles[0]->item->PicUrl, Configure::read('Domain.main').'/files/auto_replies/covers/1/test.jpg');
        $this->assertEqual($obj->Articles[0]->item->Url, Configure::read('Domain.main').'/auto_reply_messages/news/1');
	}
	
	public function testSendPackageWithLocationExtends() {
	    $GLOBALS["HTTP_RAW_POST_DATA"] = $this->received['test_location'];
	    $this->config['WeixinLocationConfig']['type'] = WeixinLocationConfig::TYPE_SINGLE;
	    
	    $post = $this->api->getReceivedMessage($this->config);
	    $resp = $post->getMessage();
        $obj = $this->xml2Object($resp->toXML());
        
        $this->assertEqual($obj->MsgType, 'news');
        $this->assertEqual($obj->ArticleCount, '2');
        $this->assertEqual($obj->Articles->item[0]->Title, 'The second location');
        $this->assertEqual($obj->Articles->item[0]->Description, 'test description for second location');
        $this->assertEqual($obj->Articles->item[0]->PicUrl, Configure::read('Domain.main').'/files/auto_replies/covers/1/test.jpg');
        $this->assertEqual($obj->Articles->item[0]->Url, 'http://map.soso.com/2');
        $this->assertEqual($obj->Articles->item[1]->Title, 'The tested title');
        $this->assertEqual($obj->Articles->item[1]->Description, 'The quick brown fox jumps over the lazy dog');
        $this->assertEqual($obj->Articles->item[1]->PicUrl, Configure::read('Domain.main').'/files/auto_replies/covers/1/test.jpg');
        $this->assertEqual($obj->Articles->item[1]->Url, Configure::read('Domain.main').'/auto_reply_messages/news/1');
	}
	
	public function testSendPackageWithLocation() {
	    $GLOBALS["HTTP_RAW_POST_DATA"] = $this->received['test_location'];
	    $this->config['WeixinConfig']['location_num'] = 'multiply';
	    
	    $post = $this->api->getReceivedMessage($this->config);
	    $resp = $post->getMessage();
        $obj = $this->xml2Object($resp->toXML());
        
        $this->assertEqual($obj->MsgType, 'news');
        $this->assertEqual($obj->ArticleCount, '3');
        $this->assertEqual($obj->Articles->item[0]->Title, $this->config['WeixinLocationConfig']['title']);
        $this->assertEqual($obj->Articles->item[0]->Description, '');
        $this->assertEqual($obj->Articles->item[0]->PicUrl, Configure::read('Domain.main').'/files/auto_replies/covers/1/test.jpg');
        $this->assertEqual($obj->Articles->item[0]->Url, '');
        $this->assertEqual($obj->Articles->item[1]->Title, 'The second location');
        $this->assertEqual($obj->Articles->item[1]->Description, 'test description for second location');
        $this->assertEqual($obj->Articles->item[1]->PicUrl, Configure::read('Domain.main').'/files/auto_replies/covers/1/test.jpg');
        $this->assertEqual($obj->Articles->item[1]->Url, Configure::read('Domain.main').'/auto_reply_locations/extend/2');
        $this->assertEqual($obj->Articles->item[2]->Title, 'The first location');
        $this->assertEqual($obj->Articles->item[2]->Description, 'test description for first location');
        $this->assertEqual($obj->Articles->item[2]->PicUrl, Configure::read('Domain.main').'/files/auto_replies/covers/1/test.jpg');
        $this->assertEqual($obj->Articles->item[2]->Url, Configure::read('Domain.main').'/auto_reply_locations/extend/1');
	}
	
	public function testSendPackageWithEventSubscribe() {
	    $GLOBALS["HTTP_RAW_POST_DATA"] = $this->received['test_subscribe'];
	    
	    $post = $this->api->getReceivedMessage($this->config);
	    $resp = $post->getMessage();
        $obj = $this->xml2Object($resp->toXML());
        
        $this->assertEqual($obj->MsgType, 'news');
        $this->assertEqual($obj->ArticleCount, '1');
        $this->assertEqual($obj->Articles->item[0]->Title, 'The tested title');
        $this->assertEqual($obj->Articles->item[0]->Description, 'The quick brown fox jumps over the lazy dog');
        $this->assertEqual($obj->Articles->item[0]->PicUrl, Configure::read('Domain.main').'/files/auto_replies/covers/1/test.jpg');
        $this->assertEqual($obj->Articles->item[0]->Url, Configure::read('Domain.main').'/auto_reply_messages/news/1');
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