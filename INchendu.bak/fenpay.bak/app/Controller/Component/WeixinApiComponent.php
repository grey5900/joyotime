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
 * @package       app.Model
 * @since         FenPay(tm) v 0.0.1
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
APP::uses('AutoReplyMessage', 'Model');
APP::uses('AutoReplyConfig', 'Model');
APP::uses('WeixinLocationConfig', 'Model');
/**
 * Weixin API component class.
 * You can use it to talk with server of Weixin MP.
 *
 * Weixin MP provides several types of message could send to us:
 * 
 * 1. Based on text.
 * <xml>
 *   <ToUserName><![CDATA[toUser]]></ToUserName>
 *   <FromUserName><![CDATA[fromUser]]></FromUserName> 
 *   <CreateTime>1348831860</CreateTime>
 *   <MsgType><![CDATA[text]]></MsgType>
 *   <Content><![CDATA[this is a test]]></Content>
 *   <MsgId>1234567890123456</MsgId>
 * </xml>
 * 
 * 2. Based on hybrid mode with images and text.
 * <xml>
 *   <ToUserName><![CDATA[toUser]]></ToUserName>
 *   <FromUserName><![CDATA[fromUser]]></FromUserName>
 *   <CreateTime>1348831860</CreateTime>
 *   <MsgType><![CDATA[image]]></MsgType>
 *   <PicUrl><![CDATA[this is a url]]></PicUrl>
 *   <MsgId>1234567890123456</MsgId>
 * </xml>
 * 
 * 3. Based on LBS.
 * <xml>
 *     <ToUserName><![CDATA[toUser]]></ToUserName>
 *     <FromUserName><![CDATA[fromUser]]></FromUserName>
 *     <CreateTime>1351776360</CreateTime>
 *     <MsgType><![CDATA[location]]></MsgType>
 *     <Location_X>23.134521</Location_X>
 *     <Location_Y>113.358803</Location_Y>
 *     <Scale>20</Scale>
 *     <Label><![CDATA[位置信息]]></Label>
 *     <MsgId>1234567890123456</MsgId>
 * </xml>
 * 
 * 4. Based on link.
 * <xml>
 *     <ToUserName><![CDATA[toUser]]></ToUserName>
 *     <FromUserName><![CDATA[fromUser]]></FromUserName>
 *     <CreateTime>1351776360</CreateTime>
 *     <MsgType><![CDATA[link]]></MsgType>
 *     <Title><![CDATA[公众平台官网链接]]></Title>
 *     <Description><![CDATA[公众平台官网链接]]></Description>
 *     <Url><![CDATA[url]]></Url>
 *     <MsgId>1234567890123456</MsgId>
 * </xml> 
 * 
 * 5. Based on event.
 * <xml>
 *     <ToUserName><![CDATA[toUser]]></ToUserName>
 *     <FromUserName><![CDATA[FromUser]]></FromUserName>
 *     <CreateTime>123456789</CreateTime>
 *     <MsgType><![CDATA[event]]></MsgType>
 *     <Event><![CDATA[EVENT]]></Event>
 *     <EventKey><![CDATA[EVENTKEY]]></EventKey>
 * </xml>
 * 
 * We just can send three kinds of sendback message:
 * 
 * 1. Based on text.
 * <xml>
 *     <ToUserName><![CDATA[toUser]]></ToUserName>
 *     <FromUserName><![CDATA[fromUser]]></FromUserName>
 *     <CreateTime>12345678</CreateTime>
 *     <MsgType><![CDATA[text]]></MsgType>
 *     <Content><![CDATA[content]]></Content>
 *     <FuncFlag>0</FuncFlag>
 * </xml>
 * 
 * 2. Based on music.
 * <xml>
 *     <ToUserName><![CDATA[toUser]]></ToUserName>
 *     <FromUserName><![CDATA[fromUser]]></FromUserName>
 *     <CreateTime>12345678</CreateTime>
 *     <MsgType><![CDATA[music]]></MsgType>
 *     <Music>
 *         <Title><![CDATA[TITLE]]></Title>
 *         <Description><![CDATA[DESCRIPTION]]></Description>
 *         <MusicUrl><![CDATA[MUSIC_Url]]></MusicUrl>
 *         <HQMusicUrl><![CDATA[HQ_MUSIC_Url]]></HQMusicUrl>
 *     </Music>
 *     <FuncFlag>0</FuncFlag>
 * </xml>
 * 
 * 3. Based on news mode with image and text.
 * <xml>
 *     <ToUserName><![CDATA[toUser]]></ToUserName>
 *     <FromUserName><![CDATA[fromUser]]></FromUserName>
 *     <CreateTime>12345678</CreateTime>
 *     <MsgType><![CDATA[news]]></MsgType>
 *     <ArticleCount>2</ArticleCount>
 *     <Articles>
 *         <item>
 *             <Title><![CDATA[title1]]></Title> 
 *             <Description><![CDATA[description1]]></Description>
 *             <PicUrl><![CDATA[picurl]]></PicUrl>
 *             <Url><![CDATA[url]]></Url>
 *         </item>
 *         <item>
 *             <Title><![CDATA[title]]></Title>
 *             <Description><![CDATA[description]]></Description>
 *             <PicUrl><![CDATA[picurl]]></PicUrl>
 *             <Url><![CDATA[url]]></Url>
 *         </item>
 *     </Articles>
 *     <FuncFlag>1</FuncFlag>
 * </xml> 
 * 
 * If you want to know more about API of Weixin, you could see this 
 * URL: http://mp.weixin.qq.com/wiki/index.php
 * 
 * Before we can talk to Weixin server, have to make sure you are certified.
 * Weixin provides special method to validate your ID.
 * @see WeixinApiModel::valid()
 * 
 * @package		app.Controller.Component
 * @link		http://mp.weixin.qq.com/wiki/index.php
 * 
 */
class WeixinApiComponent extends Component {
    
    public $components = array(
        'Response'  
    );
    
    const TYPE_REV_TEXT = 'text';
    const TYPE_REV_IMAGE = 'image';
    const TYPE_REV_LOCATION = 'location';
    const TYPE_REV_LINK = 'link';
    const TYPE_REV_EVENT = 'event';
    
/**
 * Validate ID before talk to weixin server.
 * 
 * Encrypt/Checking progress:
 * 1. Sort token, timestamp and nonce in alphabetic.
 * 2. Connect three parameters to string and encrypt it in sha1.
 * 3. If the string encrypted is same with signature parameter, 
 * then echo echostr parameter.
 * 
 * @param $data Array data posts from weixin server.
 *     It contains 4 fields:
 *     echostr: It's used to output as response text 
 *     to tell Weixin server the validate information is right.
 *     timestamp: It's timestamp of this message get from Weixin server.
 *     nonce: It's random number generated by Weixin server.
 *     signature: It's an encrypted string which is generated by Weixin server.
 * @param string $token The token for current business.
 * @return boolean If valid is ok, return echostr.
 */
    public function valid($data, $token) {
        if(isset($data['echostr']) && isset($data['timestamp']) && isset($data['nonce']) && isset($data['signature'])) {
        	$encrypted = $this->encryptByValid($data, $token);
        	if($encrypted == $data['signature']) {
        	    return $data['echostr'];
        	}
        }
        return false;
    }
    
/**
 * Encrypt validate parameters and return encrypted string.
 * 
 * @param array $data The validate parameters get from Weixin server.
 * @param string $token The token for current business.
 * @return string The encrypted code.
 */
    public function encryptByValid(array $data, $token) {
        $queue = array($token, $data['timestamp'], $data['nonce']);
        sort($queue);
        return sha1(implode('', $queue));
    }
    
/**
 * Try to retrieve data posted from weixin server.
 * The exception will be thrown if received xml is invalid 
 * 
 * @throws CakeException
 * @return WeixinReceivePackage
 *     If no post data found, return NULL.
 */
    public function getReceivedMessage($config = array()) {
        $postObj = NULL;
        $postStr = isset($GLOBALS["HTTP_RAW_POST_DATA"]) ? $GLOBALS["HTTP_RAW_POST_DATA"] : '';
        if($postStr) {
            $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
        }
        if(!$postObj) return $postObj;
        
        $api = NULL;
        if(!isset($postObj->MsgType) || empty($postObj->MsgType)) {
            throw new WeixinApiReceiveException(
                    "There is no found 'MsgType' in data package received.", 
                    $postObj);
        }
        
        switch ($postObj->MsgType) {
            case self::TYPE_REV_TEXT:
                $api = new WeixinTextReceivePackage($postObj, $config, $this->Response);
                break;
            case self::TYPE_REV_IMAGE:
                $api = new WeixinImageReceivePackage($postObj, $config, $this->Response);
                break;
            case self::TYPE_REV_LOCATION:
                $api = new WeixinLocationReceivePackage($postObj, $config, $this->Response);
                break;
            case self::TYPE_REV_LINK:
                $api = new WeixinLinkReceivePackage($postObj, $config, $this->Response);
                break;
            case self::TYPE_REV_EVENT:
                $api = new WeixinEventReceivePackage($postObj, $config, $this->Response);
                break;
            default:
                throw new WeixinApiReceiveException(
                    "The api named '{$postObj->MsgType}' looks like not be supported yet.", 
                    $postObj);
        }
        return $api;        
    }
}

/**
 * The class is used to package data and send to Weixin.
 * This is base class for all subclass of send package.
 * 
 * @author baohanddd@gmail.com
 */
abstract class WeixinSendPackage {
    
/**
 * It is a received package posted from Weixin server.
 * 
 * @var WeixinReceivePackage
 */
    protected $received;
    
/**
 * It is a from user name in received pacakge,
 * but in here as to user name.
 * 
 * @var string
 */
    protected $toUserName;
    
/**
 * It is a to user name in received package, 
 * but in here as from user name.
 * 
 * @var string
 */
    protected $fromUserName;
    
/**
 * It is a created time for this message.
 * 
 * @var int
 */
    protected $createTime;
    
/**
 * It is a msg type.
 * 
 * @var string
 */
    protected $msgType;
    
/**
 * If funcFlag is 1, that means this message is the latest one.
 * 
 * @var int
 */
    protected $funcFlag;
    
/**
 * It will returning a template object of send package.
 * 
 * @param WeixinReceivePackage $received
 */
    public function __construct(WeixinReceivePackage $received) {
        $this->received = $received;
        $this->toUserName = $received->getFromUserName();
        $this->fromUserName = $received->getToUserName();
        $this->createTime = time();
        $this->msgType = $this->getMsgType();
        $this->funcFlag = $this->getFuncFlag();    // It's default value is not promotion.
    }
    
/**
 * Set the message is latest one or not.
 * 
 * @param boolean $flag
 * @return void
 */
    public function setFuncFlag($flag) {
    	$this->funcFlag = $flag;
    }
    
/**
 * Get type value of current message.
 * 
 * @return string
 */
    public abstract function getMsgType();
    
/**
 * Get funcFlag value of current message.
 * 
 * @return int
 */
    public abstract function getFuncFlag();    
    
/**
 * Format to XML
 * 
 * @string string
 */
    public abstract function toXML();
}

/**
 * It is a template wrapper object of text.
 *
 * <xml>
 *     <ToUserName><![CDATA[toUser]]></ToUserName>
 *     <FromUserName><![CDATA[fromUser]]></FromUserName>
 *     <CreateTime>12345678</CreateTime>
 *     <MsgType><![CDATA[text]]></MsgType>
 *     <Content><![CDATA[content]]></Content>
 *     <FuncFlag>0</FuncFlag>
 * </xml>
 */
class WeixinTextSendPackage extends WeixinSendPackage {
    
    /**
     * The body of message.
     * 
     * @var string
     */
    private $content;
    
/**
 * @see WeixinSendPackage::__consruct()
 */
    public function __construct(WeixinReceivePackage $received, $message) {
        parent::__construct($received);
        if(is_array($message) && isset($message['AutoReplyMessage'])) {
            $this->setContent($message['AutoReplyMessage']['description']);
        } else {
            $this->setContent($message);
        }
    }
    
/**
 * Get type value of current message.
 *
 * @return string
 */
    public function getMsgType() {
        return 'text';
    }
    
/**
 * Get funcFlag value of current message.
 *
 * @return int
 */
    public function getFuncFlag() {
        return 0;
    }
    
/**
 * Format to XML
 *
 * @string string
 */
    public function toXML() {
        $tpl = "<xml>
               <ToUserName><![CDATA[%s]]></ToUserName>
               <FromUserName><![CDATA[%s]]></FromUserName>
               <CreateTime>%s</CreateTime>
               <MsgType><![CDATA[%s]]></MsgType>
               <Content><![CDATA[%s]]></Content>
               <FuncFlag>%s</FuncFlag>
               </xml>";
        return sprintf($tpl, 
                $this->toUserName, 
                $this->fromUserName, 
                $this->createTime, 
                $this->msgType, 
                $this->content,
                $this->funcFlag);
    }
    
    public function setContent($content) {
        $this->content = $content;
    }
    
    
}

/**
 * It is a template wrapper object of music.
 * 
 * <xml>
 *     <ToUserName><![CDATA[toUser]]></ToUserName>
 *     <FromUserName><![CDATA[fromUser]]></FromUserName>
 *     <CreateTime>12345678</CreateTime>
 *     <MsgType><![CDATA[music]]></MsgType>
 *     <Music>
 *         <Title><![CDATA[TITLE]]></Title>
 *         <Description><![CDATA[DESCRIPTION]]></Description>
 *         <MusicUrl><![CDATA[MUSIC_Url]]></MusicUrl>
 *         <HQMusicUrl><![CDATA[HQ_MUSIC_Url]]></HQMusicUrl>
 *     </Music>
 *     <FuncFlag>0</FuncFlag>
 * </xml>
 *
 */
class WeixinMusicSendPackage extends WeixinSendPackage {
    
/**
 * The title of music.
 * 
 * @var string
 */
    private $title;
    
/**
 * The description of music.
 * 
 * @var string
 */
    private $desc;
    
/**
 * The average quality URL of music.
 * 
 * @var string
 */
    private $url;
    
/**
 * The high quality URL of music.
 * 
 * @var string
 */
    private $hqUrl;
    
/**
 * @see WeixinSendPackage::__consruct()
 */
    public function __construct(WeixinReceivePackage $received, $data) {
        parent::__construct($received);
        $this->title = $data['AutoReplyMessageMusic']['title'];
        $this->desc = $data['AutoReplyMessage']['description'];
        $this->url = $data['AutoReplyMessageMusic']['music_url'];
        $this->hqUrl = $data['AutoReplyMessageMusic']['music_url'];
    }
    
/**
 * Get type value of current message.
 *
 * @return string
 */
    public function getMsgType() {
        return 'music';
    }
    
/**
 * Get funcFlag value of current message.
 *
 * @return int
 */
    public function getFuncFlag() {
        return 0;
    }
    
/**
 * Format to XML
 *
 * @string string
 */
    public function toXML() {
        $tpl = "<xml>
                <ToUserName><![CDATA[%s]]></ToUserName>
                <FromUserName><![CDATA[%s]]></FromUserName>
                <CreateTime>%s</CreateTime>
                <MsgType><![CDATA[%s]]></MsgType>
                <Music>
                    <Title><![CDATA[%s]]></Title>
                    <Description><![CDATA[%s]]></Description>
                    <MusicUrl><![CDATA[%s]]></MusicUrl>
                    <HQMusicUrl><![CDATA[%s]]></HQMusicUrl>
               </Music>
               <FuncFlag>%s</FuncFlag>
               </xml>";
        return sprintf($tpl, 
                $this->toUserName, 
                $this->fromUserName, 
                $this->createTime, 
                $this->msgType, 
                $this->title,
                $this->desc,
                $this->url,
                $this->hqUrl,
                $this->funcFlag);
    }
    
    public function setMusicTitle($title) {
        $this->title = $title;
    }
    
    public function setMusicDescription($desc) {
        $this->desc = $desc;
    }
    
    public function setMusicUrl($url) {
        $this->url = $url;
    }
    
    public function setMusicHQUrl($hqUrl) {
        $this->hqUrl = $hqUrl;
    }
    
    public function setFuncFlag($flag) {
        $this->funcFlag = (boolean) $flag;
    }
}

/**
 * It is a template wrapper object of news (hybird with image and text).
 * 
 * <xml>
 *     <ToUserName><![CDATA[toUser]]></ToUserName>
 *     <FromUserName><![CDATA[fromUser]]></FromUserName>
 *     <CreateTime>12345678</CreateTime>
 *     <MsgType><![CDATA[news]]></MsgType>
 *     <ArticleCount>2</ArticleCount>
 *     <Articles>
 *         <item>
 *             <Title><![CDATA[title1]]></Title> 
 *             <Description><![CDATA[description1]]></Description>
 *             <PicUrl><![CDATA[picurl]]></PicUrl>
 *             <Url><![CDATA[url]]></Url>
 *         </item>
 *         <item>
 *             <Title><![CDATA[title]]></Title>
 *             <Description><![CDATA[description]]></Description>
 *             <PicUrl><![CDATA[picurl]]></PicUrl>
 *             <Url><![CDATA[url]]></Url>
 *         </item>
 *     </Articles>
 *     <FuncFlag>1</FuncFlag>
 * </xml> 
 *
 */
class WeixinNewsSendPackage extends WeixinSendPackage {
    
/**
 * The container of articles.
 * 
 * @var array
 */
    protected $articles = array();
    
    /**
     * The item of article's template. 
     * @var string
     */
    protected $itemTpl = "<item>
        <Title><![CDATA[%s]]></Title> 
        <Description><![CDATA[%s]]></Description>
        <PicUrl><![CDATA[%s]]></PicUrl>
        <Url><![CDATA[%s]]></Url>
    </item>";
    
/**
 * @see WeixinSendPackage::__consruct()
 */
    public function __construct(WeixinReceivePackage $received, $data) {
        parent::__construct($received);
        $this->articles = $data;
    }
    
/**
 * Get type value of current message.
 *
 * @return string
 */
    public function getMsgType() {
        return 'news';
    }
    
/**
 * Get funcFlag value of current message.
 *
 * @return int
 */
    public function getFuncFlag() {
        return 0;
    }
    
/**
 * Format to XML
 *
 * @string string
 */
    public function toXML() {
        $tpl = "<xml>
                <ToUserName><![CDATA[%s]]></ToUserName>
                <FromUserName><![CDATA[%s]]></FromUserName>
                <CreateTime>%s</CreateTime>
                <MsgType><![CDATA[%s]]></MsgType>
                <ArticleCount>%s</ArticleCount>
                <Articles>%s</Articles>
                <FuncFlag>%s</FuncFlag>
                </xml>";
        
        return sprintf($tpl, 
                $this->toUserName, 
                $this->fromUserName, 
                $this->createTime, 
                $this->msgType, 
                $this->getArticleCount(),
                $this->getArticles(),
                $this->funcFlag);
    }
    
/**
 * Get total of articles.
 * 
 * @return number
 */
    protected function getArticleCount() {
        return count($this->articles);
    }
    
/**
 * Get formatted articles.
 * 
 * @return string
 */
    protected function getArticles() {
        $articles = '';
        foreach($this->articles as $item) {
        	$articles .= sprintf($this->itemTpl,
    			$item['AutoReplyMessageNews']['title'],
    			$item['AutoReplyMessage']['description'],
    			$this->getImageUrl($item),
    			$this->getURL($item));
        }
        return $articles;
    }
    
    /**
     * Get Image url, it will return default image url 
     * if found nothing.
     * 
     * @param array $item
     * @return string
     */
    protected function getImageUrl(&$item) {
        if(isset($item['AutoReplyMessageNews']['ImageAttachment']['original_url'])) {
            return Configure::read('Domain.image').$item['AutoReplyMessageNews']['ImageAttachment']['original_url'];
        }
        return Configure::read('Domain.image').Configure::read('AutoReplyMessage.default_message_original');
    }
    
/**
 * Get redirect url correctly
 * 
 * @param array $item
 * @return string
 */
    protected function getUrl($item) {
        switch($item['AutoReplyMessage']['type']) {
            case AutoReplyMessageNews::CUSTOM:
                return sprintf(Configure::read('Exlink.news.detail'), $item['AutoReplyMessage']['id']);
                break;
            case AutoReplyMessageNews::LINK:
                return sprintf(Configure::read('Exlink.news.detail'), $item['AutoReplyMessage']['id']);
                break;
            case AutoReplyMessageNews::MAP:
                return $item['AutoReplyMessageLocation']['AutoReplyLocation']['map_url'];
                break;
            case AutoReplyMessageNews::NEXT:
                return '';
                break;
            default:
                return Configure::read('Domain.main');
        }
    }
}

class WeixinDistanceSendPackage extends WeixinNewsSendPackage {
    
    const EARTH_RADIUS = 6378.137;
    const PI = 3.1415;
    
    protected function rad($d){
    	return $d * self::PI / 180.0;
    }
    
    public function getDistance($lat1, $lng1, $lat2, $lng2)
    {
    	$radLat1 = $this->rad($lat1);
    	$radLat2 = $this->rad($lat2);
    	$a = $radLat1 - $radLat2;
    	$b = $this->rad($lng1) - $this->rad($lng2);
    	$s = 2 * asin(sqrt(pow(sin($a/2),2) +
    	    cos($radLat1)*cos($radLat2)*pow(sin($b/2),2)));
    	$s = $s * self::EARTH_RADIUS;
    	$s = round($s * 10000) / 10000;
    	return round($s, 1);
    }
}

/**
 * It is a template wrapper object of locations based on news
 * The mode called multiply locations too.
 *
 */
class WeixinLocationsSendPackage extends WeixinDistanceSendPackage {
/**
 * @var Array of location
 */
    protected $locations = array();
/**
 * @see WeixinSendPackage::__consruct()
 */
    public function __construct(WeixinReceivePackage $received, $data) {
    	parent::__construct($received, $data);
    	$this->locations = $data;
    }
    
/**
 * (non-PHPdoc)
 * @see WeixinNewsSendPackage::getArticles()
 */
    protected function getArticles() {
    	$articles = '';
    	
    	if(is_array($this->locations) && !empty($this->locations)) {
    	    $main = sprintf($this->itemTpl,
	    		$this->received->config['WeixinLocationConfig']['title'],
	    		'',
	    		$this->getLocationCover(),
    	        '');    // without page.
    	    
    	    $point = $this->received->getLocation();
    	    foreach($this->locations as $item) {
    	        $distance = $this->getDistance(
	        		(double)$item['AutoReplyLocation']['latitude'], 
    	            (double)$item['AutoReplyLocation']['longitude'],
	        		(double)$point['x'], (double)$point['y']);
    	        $titles = array(
    	            $item['AutoReplyLocation']['title'],
    	            $distance."公里",
    	        );
    	        if(!empty($item['AutoReplyLocation']['description'])) {
    	            $titles[] = $item['AutoReplyLocation']['description'];
    	        }
    	    	$articles .= sprintf($this->itemTpl,
	    			implode("\n", $titles),
    	    	    '',
    	    	    $this->getLocationImage($item),
	    			$this->getURL($item));
    	    }
    	    
    	    $articles = $main.$articles;
    	}
    	
    	return $articles;
    }
    
/**
 * (non-PHPdoc)
 * @see WeixinNewsSendPackage::getArticleCount()
 */
    protected function getArticleCount() {
        return count($this->locations) + 1;    // plus a config item...
    }
    
/**
 * Get redirect url correctly
 *
 * @param array $item
 * @return string
 */
    protected function getUrl($item) {
    	return sprintf(Configure::read('Exlink.location.extend'), $item['AutoReplyLocation']['id']);
    }
    
/**
 * Get location cover, if there is found nothing return default.
 * @return string
 */
    protected function getLocationCover() {
    	if(isset($this->received->config['WeixinLocationConfig']['ImageAttachment']['original_url']) 
    	    && !empty($this->received->config['WeixinLocationConfig']['ImageAttachment']['original_url'])) {
    		return Configure::read('Domain.main').$this->received->config['WeixinLocationConfig']['ImageAttachment']['original_url'];
    	} else {
    		return Configure::read('Domain.main').Configure::read('AutoReplyMessage.default_location_original');
    	}
    }
    
/**
 * Get news image, if there is found nothing, return default image.
 * @param array $item
 * @return string
 */
    protected function getLocationImage(&$item) {
    	if(isset($item['ImageAttachment']['original_url']) &&
    	!empty($item['ImageAttachment']['original_url'])) {
    		return Configure::read('Domain.main').$item['ImageAttachment']['original_url'];
    	} else {
    		return Configure::read('Domain.main').Configure::read('AutoReplyMessage.default_location_thumbnail');
    	}
    }
}

/**
 * For nothing response.
 *
 */
class WeixinNullSendPackage extends WeixinSendPackage {
    
/**
 * (non-PHPdoc)
 * @see WeixinSendPackage::toXML()
 */
    public function toXML() {
        return false;
    }
    
/**
 * (non-PHPdoc)
 * @see WeixinSendPackage::getMsgType()
 */
    public function getMsgType() {
        return 'null';
    }
    
/**
 * (non-PHPdoc)
 * @see WeixinSendPackage::getFuncFlag()
 */
    public function getFuncFlag() {
        return 0;
    }
}

/**
 * Just only return xml which got from the third-party server.
 *
 */
class WeixinXmlSendPackage extends WeixinNullSendPackage {
    
    private $xml;
    
    public function __construct(WeixinReceivePackage $received, $xml) {
        parent::__construct($received);
        $this->xml = $xml;
    }
    
    public function toXML() {
        return $this->xml;
    }
    
    /**
     * (non-PHPdoc)
     * @see WeixinSendPackage::getMsgType()
     */
    public function getMsgType() {
    	return 'xml';
    }
}

/**
 * It is a template wrapper object of location extended based on news. 
 * The mode called single location too.
 * 
 */
class WeixinLocationExtendsSendPackage extends WeixinDistanceSendPackage {
    
/**
 * 
 * @var Array of location
 */
    protected $location;
/**
 * 
 * @var Array of ImageAttachment as cover
 */
    protected $cover;
/**
 * @see WeixinSendPackage::__consruct()
 */
    public function __construct(WeixinReceivePackage $received, $data) {
    	parent::__construct($received, $data);
    	$this->articles = $data['AutoReplyMessage'];
	    $this->location = $data['AutoReplyLocation'];
	    $this->cover = $data['ImageAttachment'];
    }
    
/**
 * Get formatted articles.
 *
 * @return string
 */
    protected function getArticles() {
    	$articles = '';
    	$point = $this->received->getLocation();
    	$distance = $this->getDistance(
    	        (double)$this->location['latitude'], (double)$this->location['longitude'], 
    	        (double)$point['x'], (double)$point['y']);
    	$articles = sprintf($this->itemTpl, 
    	        $this->location['title']."({$distance}公里)",
    	        $this->location['description'],
    	        $this->getLocationCover(),
    	        $this->getUrl($this->location));
    	
    	foreach($this->articles as $item) {
    		$articles .= sprintf($this->itemTpl,
				$item['AutoReplyMessageNews']['title'],
				$item['description'],
				$this->getNewsImage($item),
    		    parent::getURL(array('AutoReplyMessage' => $item)));
    	}
    	return $articles;
    }
    
/**
 * (non-PHPdoc)
 * @see WeixinNewsSendPackage::getArticleCount()
 */
    protected function getArticleCount() {
        $count = count($this->articles) + 1;    // location item plus extend items.
        return $count;
    }
    
/**
 * Get redirect url correctly
 *
 * @param array $item
 * @return string
 */
    protected function getUrl($item) {
    	return sprintf(Configure::read('Exlink.location.extend'), $item['id']);
    }
    
/**
 * Get location cover, if there is found nothing return default.
 * @return string
 */
    protected function getLocationCover() {
        if(isset($this->cover['original_url']) && !empty($this->cover['original_url'])) {
            return Configure::read('Domain.main').$this->cover['original_url'];
        } else {
            return Configure::read('Domain.main').Configure::read('AutoReplyMessage.default_location_original');
        }
    }
    
/**
 * Get news image, if there is found nothing, return default image.
 * @param array $item
 * @return string
 */
    protected function getNewsImage(&$item) {
        if(isset($item['AutoReplyMessageNews']['ImageAttachment']['original_url']) && 
            !empty($item['AutoReplyMessageNews']['ImageAttachment']['original_url'])) {
            return Configure::read('Domain.main').$item['AutoReplyMessageNews']['ImageAttachment']['original_url'];
        } else {
            return Configure::read('Domain.main').Configure::read('AutoReplyMessage.default_message_thumbnail');
        }
    }
} 

/**
 * The class is used to retrieve field data from received pacakge.
 * 
 * @author baohanddd@gmail.com
 *
 */
abstract class WeixinReceivePackage {
    
    protected $post = NULL;
/**
 * @var int
 */
    protected $userId;
    
/**
 * User configure information
 * @var array
 */
    public $config;
    
/**
 * The instance of ResponseComponent
 * @var ResponseComponent
 */
    public $resp;
    
/**
 * The constructor
 * @param SimpleXMLElement $post
 */
    public function __construct(SimpleXMLElement $post, $config = array(), ResponseComponent $resp) {
        $this->post = $post;
        $this->config = $config;
        $this->resp = $resp;
        $this->resp->setReceivePackage($this);
        $this->userId = $config['WeixinConfig']['user_id'];
    }
    
/**
 * Get the name who received this message.
 * 
 * @return string
 */
    public function getToUserName() {
        return $this->post->ToUserName;
    }
    
/**
 * Get the name who sent this message.
 * 
 * @return string
 */
    public function getFromUserName() {
        return $this->post->FromUserName;
    }
    
/**
 * Get the time which is created for this message.
 * 
 * @return int
 *     A ten digitals number, unix timestamp format.
 */
    public function getCreateTime() {
        return $this->post->CreateTime;
    }
    
/**
 * Get the message's type.
 * 
 * @return string
 */
    public function getMsgType() {
        return $this->post->MsgType;
    }
    
/**
 * Get the message ID.
 * 
 * The ID is auto generated by Weixin.
 * It's an 64-bit integer.
 */
    public function getMsgId() {
        return $this->post->MsgId;
    }
    
/**
 * Get array of HTTP_RAW_POST_DATA
 * 
 * @return string raw
 */
    public function getRaw() {
        return $GLOBALS['HTTP_RAW_POST_DATA'];
    }
    
/**
 * According rules return instance of WeixinSendPackage
 * 
 * @return WeixinSendPackage
 */
    abstract function getMessage();
}

/**
 * The class is weixin post data based on text.
 *
 * <xml>
 *   <ToUserName><![CDATA[toUser]]></ToUserName>
 *   <FromUserName><![CDATA[fromUser]]></FromUserName>
 *   <CreateTime>1348831860</CreateTime>
 *   <MsgType><![CDATA[text]]></MsgType>
 *   <Content><![CDATA[this is a test]]></Content>
 *   <MsgId>1234567890123456</MsgId>
 * </xml>
 *
 * @author baohanddd@gmail.com
 *
 */
class WeixinTextReceivePackage extends WeixinReceivePackage {
    /**
     * @var AutoReplyMessage
     */
    protected $AutoReplyMessage;
    
    public function __construct(SimpleXMLElement $post, $config = array(), ResponseComponent $resp) {
        parent::__construct($post, $config, $resp);
        $this->AutoReplyMessage = ClassRegistry::init('AutoReplyMessage');
    }
    
/**
 * The rule of how to response client:
 * 
 * 1. At the first, It will find any news messages can matched tag name, 
 * maximum up to 3 records will be responsed.
 * 2. Then it will find text and music message if there is nothing tag matached,
 * but only one record will be responsed.
 * 3. Finally, there is no found by tag name, 
 * it will response a random message which is pre-defined by customer.
 * 
 * (non-PHPdoc)
 * @see WeixinReceivePackage::getMessage()
 */
    public function getMessage() {
        // Try to match regular rules for third-party interface.
        $remote = $this->resp->getRemoteByRegexp($this->getContent(), $this->userId);
        if($remote) {
            $xml = $remote->post();
            $sp = new WeixinXmlSendPackage($this, $xml);
            return $sp;
        } else {
            // Try to get next resultset which is wrapped
            // into an instance of WeixinSendPackage
            $sp = $this->getNext();
            if($sp) return $sp;
            $sp = $this->resp->getFixcode($this->getContent(), $this->userId);
            if($sp) return $sp;
            $sp = $this->resp->getNews($this->getContent(), $this->userId);
            if($sp) return $sp;
            return $this->resp->getDefault($this->userId, AutoReplyConfig::EVT_NOANSWER);
        }
    }
    
/**
 * Get next resultset as instance of WeixinSendPackage
 * If no found any resultset or resultset, 
 * the false would be retuned.
 * @return WeixinSendPackage
 */
    private function getNext() {
        if($this->getContent() == 'n') {
            $pager = ResponsePager::getFromCache($this->userId);
            $messageIds = $pager->getNext();
            $reducer = $pager->getReducer($this);
            return $this->resp->getAutoReplyMessages($messageIds, $reducer);
        }
    }
    
    /**
     * Retrieve user input from received message.
     * 
     * @return string the received message
     */
    public function getContent() {
        return $this->post->Content;
    }
}

/**
 * The class is weixin post data based on image.
 *
 * <xml>
 *   <ToUserName><![CDATA[toUser]]></ToUserName>
 *   <FromUserName><![CDATA[fromUser]]></FromUserName>
 *   <CreateTime>1348831860</CreateTime>
 *   <MsgType><![CDATA[image]]></MsgType>
 *   <PicUrl><![CDATA[this is a url]]></PicUrl>
 *   <MsgId>1234567890123456</MsgId>
 * </xml>
 *
 * @author baohanddd@gmail.com
 *
 */
class WeixinImageReceivePackage extends WeixinReceivePackage {
    
    public function __construct(SimpleXMLElement $post, $config = array(), ResponseComponent $resp) {
        parent::__construct($post, $config, $resp);
        $this->AutoReplyMessage = ClassRegistry::init('AutoReplyMessage');
    }
    
/**
 * Get a pic url.
 * 
 * @return string
 */
    public function getPicUrl() {
        return $this->post->PicUrl;
    }
    
/**
 * (non-PHPdoc)
 * @see WeixinReceivePackage::getMessage()
 */
    public function getMessage() {
        return $this->resp->getDefault($this->userId);
    }
}

/**
 * The class is weixin post data based on location.
 *
 * <xml>
 *     <ToUserName><![CDATA[toUser]]></ToUserName>
 *     <FromUserName><![CDATA[fromUser]]></FromUserName>
 *     <CreateTime>1351776360</CreateTime>
 *     <MsgType><![CDATA[location]]></MsgType>
 *     <Location_X>23.134521</Location_X>
 *     <Location_Y>113.358803</Location_Y>
 *     <Scale>20</Scale>
 *     <Label><![CDATA[位置信息]]></Label>
 *     <MsgId>1234567890123456</MsgId>
 * </xml>
 *
 * @author baohanddd@gmail.com
 *
 */
class WeixinLocationReceivePackage extends WeixinReceivePackage {
    
    /**
     * @var AutoReplyLocation
     */
    private $AutoReplyLocation;
    
    public function __construct(SimpleXMLElement $post, $config = array(), ResponseComponent $resp) {
        parent::__construct($post, $config, $resp);
        $this->AutoReplyLocation = ClassRegistry::init('AutoReplyLocation');
    }
    
/**
 * Get a point coordinary as array.
 * 
 * The x is latitude
 * The y is longitude
 * 
 * @return array
 *     It contains x and y coordinary.
 */
    public function getLocation() {
        return array(
            'x' => $this->post->Location_X,
            'y' => $this->post->Location_Y 
        );
    }
    
/**
 * Get a scale times about map.
 * 
 * @return int
 */
    public function getScale() {
        return $this->post->Scale;
    }
    
/**
 * Get the location description as text.
 * 
 * @return string
 */
    public function getLabel() {
        return $this->post->Label;
    }
    
/**
 * (non-PHPdoc)
 * @see WeixinReceivePackage::getMessage()
 */
    public function getMessage() {
        $remote = $this->resp->getRemoteByLocation($this->userId);
        if($remote) {
        	$xml = $remote->post();
        	$sp = new WeixinXmlSendPackage($this, $xml);
        	return $sp;
        } else {
            $loc = $this->getLocation();
            if($this->config['WeixinLocationConfig']['type'] == WeixinLocationConfig::TYPE_SINGLE) {
                // single + extends
                $data = $this->AutoReplyLocation->getLocationExtends($loc['x'], $loc['y'], $this->userId, Configure::read('Limit.location.extends'));
                $this->AutoReplyLocation->increaseRequestTotal($data['AutoReplyLocation']['id']);
                return new WeixinLocationExtendsSendPackage($this, $data);
            } elseif($this->config['WeixinLocationConfig']['type'] == WeixinLocationConfig::TYPE_MULTIPLY) {
                // multi-locations
                $data = $this->AutoReplyLocation->getLocations($loc['x'], $loc['y'], $this->userId, 9);
                $ids = Hash::extract($data, '{n}.AutoReplyLocation.id');
                $this->AutoReplyLocation->increaseRequestTotal($ids);
                return new WeixinLocationsSendPackage($this, $data);
            } else {
                throw new NotFoundException('Unknown weixin type: '.$this->config['WeixinLocationConfig']['type']);
            }
        }
    }
}

/**
 * The class is weixin post data based on link.
 *
 * <xml>
 *     <ToUserName><![CDATA[toUser]]></ToUserName>
 *     <FromUserName><![CDATA[fromUser]]></FromUserName>
 *     <CreateTime>1351776360</CreateTime>
 *     <MsgType><![CDATA[link]]></MsgType>
 *     <Title><![CDATA[公众平台官网链接]]></Title>
 *     <Description><![CDATA[公众平台官网链接]]></Description>
 *     <Url><![CDATA[url]]></Url>
 *     <MsgId>1234567890123456</MsgId>
 * </xml> 
 *
 * @author baohanddd@gmail.com
 *
 */
class WeixinLinkReceivePackage extends WeixinReceivePackage {
    
/**
 * @var AutoReplyMessage
 */
    private $AutoReplyMessage;
    
    public function __construct(SimpleXMLElement $post, $config = array(), ResponseComponent $resp) {
        parent::__construct($post, $config, $resp);
        $this->AutoReplyMessage = ClassRegistry::init('AutoReplyMessage');
    }
    
/**
 * Get the title of message.
 * 
 * @return string
 */
    public function getTitle() {
        return $this->post->Title;
    }
    
/**
 * Get the description of message.
 * 
 * @return string
 */
    public function getDescription() {
        return $this->post->Description;
    }
    
/**
 * Get the Url.
 * 
 * @return string
 */
    public function getUrl() {
        return $this->post->Url;
    }
    
/**
 * (non-PHPdoc)
 * @see WeixinReceivePackage::getMessage()
 */
    public function getMessage() {
    	return $this->resp->getDefault($this->userId);
    }
}

/**
 * The class is weixin post data based on event.
 *
 * <xml>
 *     <ToUserName><![CDATA[toUser]]></ToUserName>
 *     <FromUserName><![CDATA[FromUser]]></FromUserName>
 *     <CreateTime>123456789</CreateTime>
 *     <MsgType><![CDATA[event]]></MsgType>
 *     <Event><![CDATA[EVENT]]></Event>
 *     <EventKey><![CDATA[EVENTKEY]]></EventKey>
 * </xml>
 *
 * @author baohanddd@gmail.com
 *
 */
class WeixinEventReceivePackage extends WeixinReceivePackage {
    
    const EVT_SUBSCRIBE = 'subscribe';
    const EVT_UNSUBSCRIBE = 'unsubscribe';
    
    /**
     * @var AutoReplyMessage
     */
    private $AutoReplyMessage;
    /**
     * @var WeixinConfig
     */
    private $WeixinConfig;
    
    public function __construct(SimpleXMLElement $post, $config = array(), ResponseComponent $resp) {
        parent::__construct($post, $config, $resp);
        $this->AutoReplyMessage = ClassRegistry::init('AutoReplyMessage');
        $this->WeixinConfig = ClassRegistry::init('WeixinConfig');
    }
    
/**
 * Get event type such as 'Subscribe', 'Unsubscribe' and 'CLICK'.
 * 
 * @return string
 *     The name of event type.
 */
    public function getEvent() {
        return $this->post->Event;
    }
    
/**
 * Get Key value of event.
 * 
 * @return string
 */
    public function getEventKey() {
        return $this->post->EventKey;
    }
    
/**
 * (non-PHPdoc)
 * @see WeixinReceivePackage::getMessage()
 */
    public function getMessage() {
        if($this->getEvent() == self::EVT_SUBSCRIBE) {
            $this->WeixinConfig->increaseUserNum($this->userId);
            // The final try to get default reply message if no found any matched.
            return $this->resp->getDefault($this->userId, AutoReplyConfig::EVT_SUBSCRIBE);
        } elseif($this->getEvent() == self::EVT_UNSUBSCRIBE) {
            $this->WeixinConfig->decreaseUserNum($this->userId);
        }
    }
}

/**
 * The exception for Weixin API.
 *
 * @see CakeException
 */
class WeixinApiException extends CakeException {
    
/**
 * To save current post data object.
 * 
 * @param string $message
 * @param number $code
 */
    public function __construct($message, $code = 500) {
        $code = 4001;
        parent::__construct($message, $code);
    }
}

/**
 * The exception for Weixin API while parsing/reading 
 * of received package data happens error.
 *
 * @see WeixinApiException
 * @see CakeException
 */
class WeixinApiReceiveException extends WeixinApiException {
    
/**
 * The received data posted from Weixin server.
 * 
 * @var SimpleXMLElement
 */
    private $logData;
    
/**
 * To save current post data object.
 * 
 * @param string $message
 * @param SimpleXMLElement $logData
 * @param number $code
 */
    public function __construct($message, $logData = NULL, $code = 500) {
        $code = 4001;
        $this->logData = $logData;
        parent::__construct($message, $code);
    }
    
/**
 * Get current post data object
 * 
 * @return SimpleXMLElement
 */
    public function getLogData() {
        return $this->logData;
    }
}