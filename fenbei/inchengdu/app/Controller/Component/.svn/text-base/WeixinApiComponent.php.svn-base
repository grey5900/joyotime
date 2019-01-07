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
 * @since         FenPay(tm) v 0.0.1
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
App::uses('HttpSocket', 'Network/Http');
App::uses('InchengduApiComponent', 'Controller/Component');
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
        'InchengduApi'
    );
    
    const TYPE_REV_TEXT = 'text';
    const TYPE_REV_LOCATION = 'location';
    
/**
 * Try to retrieve data posted from weixin server.
 * The exception will be thrown if received xml is invalid 
 * 
 * @throws CakeException
 * @return WeixinReceivePackage
 *     If no post data found, return NULL.
 */
    public function getReceivedMessage() {
        $postObj = NULL;
        $postStr = isset($GLOBALS["HTTP_RAW_POST_DATA"]) ? $GLOBALS["HTTP_RAW_POST_DATA"] : '';
        if($postStr) {
            $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
        }
        if(!$postObj) return NULL;
        
        $api = NULL;
        if(!isset($postObj->MsgType) || empty($postObj->MsgType)) {
            return new WeixinNullReceivedPackage($postObj);
        }
        
        switch($postObj->MsgType) {
            case self::TYPE_REV_TEXT:
                $api = new WeixinTextReceivePackage($postObj, $this->InchengduApi);
                break;
            case self::TYPE_REV_LOCATION:
                $api = new WeixinLocationReceivePackage($postObj, $this->InchengduApi);
                break;
            default:
                return new WeixinNullReceivedPackage($postObj);
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
 * It would return a template object of send package.
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
 * @var WeixinNewsResponse
 */
    protected $resp;
    
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
    public function __construct(WeixinReceivePackage $received, WeixinNewsResponse $resp) {
        parent::__construct($received);
        $this->resp = $resp;
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
                $this->resp->getArticleCount(),
                $this->resp->getArticles(),
                $this->funcFlag);
    }
}

/**
 * The class is used to provide interfaces to format 
 * and wrap any results array whatever got from db or 
 * api server.
 *
 */
abstract class WeixinNewsResponse {

    protected $messages = array();
    
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
    
	public function __construct($messages = array()) {
        $this->messages = $messages;
	}
	
/**
 * Get total of articles.
 * @return number
 */
	abstract function getArticleCount();
	
/**
 * Get formatted articles.
 * @return string
 */
	abstract function getArticles();
}

/**
 * It uses to format/wrap result return from api interface into weixin XML.
 */
abstract class PlacesResponse extends WeixinNewsResponse {
    
    public function setMessages($messages = array()) {
    	$this->messages = $messages;
    }

/**
 * Get total of articles.
 * @return number
 */
    public function getArticleCount() {
        if(is_array($this->messages)) {
            return count($this->messages);
        }
    }

/**
 * Get formatted articles.
 * @return string
 */
    public function getArticles() {
        $articles = '';
        foreach($this->messages as $idx => $item) {
            $articles .= sprintf($this->itemTpl, 
                $this->getTitle($item, (boolean)($idx == 0)), 
                isset($item['address']) ? $item['address'] : '', 
                $this->getImageUrl($item, ($idx == 0) ? 'original' : 'thumbnail'), 
                $this->getURL($item)
            );
        }
        return $articles;
    }

/**
 * Get Image url, it will return default image url
 * if found nothing.
 *
 * @param array $item            
 * @param string $dimension thumbnail | original
 * @return string
 */
    private function getImageUrl(&$item, $dimension = 'thumbnail') {
        if($dimension == 'original') {
            if(isset($item['background_uri']) && !empty($item['background_uri'])) {
                return $item['background_uri'];
            } else {
                return Configure::read('DefaultImage.Cover.location');
            }
        } elseif($dimension == 'thumbnail') {
            if(isset($item['icon_uri'])) {
            	return $item['icon_uri'];
            } else {
                return Configure::read('DefaultImage.Icon.location');
            }
        }
    }

/**
 * Get redirect url
 *
 * @param array $item            
 * @return string
 */
    protected function getUrl($item) {
        if(isset($item['id']) && !empty($item['id'])) {
            return Configure::read('Domain.main') . '/locations/index/' . intval($item['id']);
        }
    }
    
/**
 * Get title formatted.
 * @param array $item
 * @param boolean $first Is first item?
 * @return string
 */
    abstract public function getTitle(&$item, $first = false);
}

/**
 * The class is used to format results get from interface of place/search.
 * It searches by keyword.
 */
class PlaceSearchResponse extends PlacesResponse {
    
    public function getTitle(&$item, $first = false) {
        $title = '';
        $title .= $item['placename'];
        if(!$first) {
            $title .= isset($item['address']) ? "\n".$item['address'] : '';
        }
        return $title;
    }
}

/**
 * The class is used to format results get from interface of place/category.
 * It searches by location.
 */
class PlaceCategoryResponse extends PlacesResponse {
    
    public function getTitle(&$item, $first = false) {
        $title = '';
        
        if(!$first) {
            $title .= $item['placename']."\n";
            $title .= $this->distance($item)."\n";
        	$title .= isset($item['address']) ? $item['address'] : '';
        } else {
            $distance = $this->distance($item);
            $title .= $item['placename'].'('.$distance.')';
        }
        return $title;
    }
    
/**
 * Formatted distance
 * @param array $item
 * @return string
 */
    private function distance(&$item) {
        return isset($item['distance']) ? round($item['distance'] / 1000, 2) . "公里" : '';
    } 
}

/**
 * The class is used to parse and wrap results return from solr.
 *
 */
class PlaceSolrResponse extends PlacesResponse {
    
    public function getTitle(&$item, $first = false) {
        $title = '';
        
        if(!$first) {
            $title .= $item['placename']."\n";
            $title .= $this->distance($item)."\n";
        	$title .= isset($item['address']) ? $item['address'] : '';
        } else {
            $distance = $this->distance($item);
            $title .= $item['placename'].'('.$distance.')';
        }
        return $title;
    }
    
/**
 * Formatted distance
 * @param array $item
 * @return string
 */
    private function distance(&$item) {
        return isset($item['distance']) ? round($item['distance'] / 1000, 2) . "公里" : '';
    } 
}

/**
 * For default response, display something default message to client.
 *
 */
class WeixinNullSendPackage extends WeixinSendPackage {
    
/**
 * (non-PHPdoc)
 * @see WeixinSendPackage::toXML()
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
                $this->getContent(),
                $this->funcFlag);
    }
    
/**
 * (non-PHPdoc)
 * @see WeixinSendPackage::getMsgType()
 */
    public function getMsgType() {
        return 'text';
    }
    
/**
 * (non-PHPdoc)
 * @see WeixinSendPackage::getFuncFlag()
 */
    public function getFuncFlag() {
        return 0;
    }
    
    public function getContent() {
        return '小in找不到相关信息';
    }
}

/**
 * The class is used to retrieve field data from received pacakge.
 * 
 * @author baohanddd@gmail.com
 *
 */
abstract class WeixinReceivePackage {
    
/**
 * @var SimpleXMLElement
 */
    protected $post = NULL;
/**
 * The instance of InchengduApiComponent
 * @var InchengduApiComponent
 */
    protected $api;
    
/**
 * The constructor
 * @param SimpleXMLElement $post
 */
    public function __construct(SimpleXMLElement $post, InchengduApiComponent $api) {
        $this->post = $post;
        $this->api = $api;
    }
    
/**
 * Get the name who received this message.
 * 
 * @return string
 */
    public function getToUserName() {
        return (string) $this->post->ToUserName;
    }
    
/**
 * Get the name who sent this message.
 * 
 * @return string
 */
    public function getFromUserName() {
        return (string) $this->post->FromUserName;
    }
    
/**
 * Get the time which is created for this message.
 * 
 * @return int
 *     A ten digitals number, unix timestamp format.
 */
    public function getCreateTime() {
        return (string) $this->post->CreateTime;
    }
    
/**
 * Get the message's type.
 * 
 * @return string
 */
    public function getMsgType() {
        return (string) $this->post->MsgType;
    }
    
/**
 * Get the message ID.
 * 
 * The ID is auto generated by Weixin.
 * It's an 64-bit integer.
 */
    public function getMsgId() {
        return (string) $this->post->MsgId;
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
    
    public function __construct(SimpleXMLElement $post, InchengduApiComponent $api) {
        parent::__construct($post, $api);
    }
    
/**
 * (non-PHPdoc)
 * @see WeixinReceivePackage::getMessage()
 */
    public function getMessage() {
        $response = $this->getMessagesFromApi();
        if($response->getArticleCount()) {
            return new WeixinNewsSendPackage($this, $response);
        }
        return new WeixinNullSendPackage($this);
    }
    
/**
 * Try to get messages from Api server of Inchengdu.
 * @return PlacesResponse
 */
    private function getMessagesFromApi() {
        $place = $this->api->place();
        if($this->getContent() == 'n' 
                && TRUE == ($pager = ResponsePager::getFromCache($this->getFromUserName()))) {
            $pager = ResponsePager::getFromCache($this->getFromUserName());
            $finder = $pager->getNext();
            $wrapper = new PlacesWrapper($finder);
            $pager->appendNext($wrapper, array(
                'placename' => '>>>回复“n”查看更多',
                'address' => ''
            ));
            return $wrapper->getResponse();
        } else {
            $cache = new ResponsePoint();
            $point = $cache->getPoint($this->getFromUserName());
            if($point) {
                $finder = $place->searchByLocationAndKeyword($this, $point);
            } else {
                $finder = $place->searchByKeyword($this);
            }
            $wrapper = new PlacesWrapper($finder);
            if($wrapper->getTotal() > 0) {
                ResponsePager::getInstance($this, $finder, $wrapper)->appendNext($wrapper, array(
                    'placename' => '>>>回复“n”查看更多',
                    'address' => ''
                ))->store();
            }
            return $wrapper->getResponse();
        }
    }
    
/**
 * Retrieve user input from received message.
 * 
 * @return string the received message
 */
    public function getContent() {
        return (string) $this->post->Content;
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
    
    public function __construct(SimpleXMLElement $post, InchengduApiComponent $api) {
        parent::__construct($post, $api);
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
            'x' => (double) $this->post->Location_X,
            'y' => (double) $this->post->Location_Y 
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
        $response = $this->getMessagesFromApi();
        if($response->getArticleCount()) {
            return new WeixinNewsSendPackage($this, $response);
        }
        return new WeixinNullSendPackage($this);
    }

/**
 * Try to get messages from Api server of Inchengdu.
 * 
 * @return PlacesResponse
 */
    private function getMessagesFromApi() {
        $finder = $this->api->place()->searchByLocation($this);
        $wrapper = new PlacesWrapper($finder);
        ResponsePager::getInstance($this, $finder, $wrapper)->appendNext($wrapper, array(
            'placename' => '>>>回复“n”查看更多',
            'address' => ''
        ))->store();
        $cache = new ResponsePoint();
        $cache->savePoint($this);
        return $wrapper->getResponse();
    }
}

/**
 * The class is used to no found or something wrong was happened, it will return 
 * an instance of WeixinNullSendPackage too.
 *
 */
class WeixinNullReceivedPackage extends WeixinReceivePackage {

	public function __construct(SimpleXMLElement $post) {
		parent::__construct($post);
	}

/**
 * (non-PHPdoc)
 * @see WeixinReceivePackage::getMessage()
 */
	public function getMessage() {
		return new WeixinNullSendPackage($this);
	}
}