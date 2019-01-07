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
    
    const TYPE_REV_TEXT = 'text';
    
/**
 * Try to retrieve data posted from weixin server.
 * The exception will be thrown if received xml is invalid 
 * 
 * @throws CakeException
 * @return WeixinReceivePackage
 */
    public function getReceivedMessage() {
        $postObj = NULL;
        $postStr = isset($GLOBALS["HTTP_RAW_POST_DATA"]) ? $GLOBALS["HTTP_RAW_POST_DATA"] : '';
        if($postStr) {
            $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
        }
        if(!$postObj) return new WeixinNullReceivedPackage(
                simplexml_load_string('<xml></xml>', 'SimpleXMLElement', LIBXML_NOCDATA));
        
        $api = NULL;
        if(!isset($postObj->MsgType) || empty($postObj->MsgType)) {
            return new WeixinNullReceivedPackage(
            		simplexml_load_string('<xml></xml>', 'SimpleXMLElement', LIBXML_NOCDATA));
        }
        
        switch($postObj->MsgType) {
            case self::TYPE_REV_TEXT:
                $api = new WeixinTextReceivePackage($postObj);
                break;
            default:
                $api = new WeixinNullReceivedPackage($postObj);
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
        $this->setContent($message);
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
 * For nothing response.
 *
 */
class WeixinNullSendPackage extends WeixinSendPackage {
    
/**
 * (non-PHPdoc)
 * @see WeixinSendPackage::toXML()
 */
    public function toXML() {
        return '<xml></xml>';
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
    
    public function __construct(WeixinReceivePackage $received, string $xml) {
        parent::__construct($received);
        $this->xml = $xml;
    }
    
    public function toXML() {
        return $this->xml;
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
 * The constructor
 * @param SimpleXMLElement $post
 */
    public function __construct(SimpleXMLElement $post) {
        $this->post = $post;
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
 */
class WeixinTextReceivePackage extends WeixinReceivePackage {
    
    public function __construct(SimpleXMLElement $post) {
        parent::__construct($post);
        $this->Saler = ClassRegistry::init('Saler');
        $this->CouponCode = ClassRegistry::init('CouponCode');
        $this->Shop = ClassRegistry::init('Shop');
    }
    
/**
 * The rule of how to response client:
 * 
 * 1. At the first, it will try to get saler by 
 * openid which is FromUserName in received xml.
 * If found anything then go to process of coupon code.
 * If not then go to process of validate saler by token.
 * 
 * (non-PHPdoc)
 * @see WeixinReceivePackage::getMessage()
 * @return WeixinSendPackage
 */
    public function getMessage() {
        $msg = $this->getContent();
        $openId = $this->getFromUserName();
        
        // validate token
        if(preg_match('/^[0-9a-z]{8,8}$/i', $msg)) {
            $saler = $this->Saler->findByOpenId($openId);
            if($saler && !empty($saler['Saler']['open_id'])) {
            	return new WeixinTextSendPackage($this, '您已经认证过了，请勿重复认证');
            }
            
        	$saler = $this->Saler->findByToken($msg);
        	if($saler && !empty($saler['Saler']['open_id'])) {
        		return new WeixinTextSendPackage($this, '验证码已经使用过了，身份认证失败');
        	} elseif ($saler && empty($saler['Saler']['open_id'])) {
        		$saler['Saler']['open_id'] = $openId;
        		if($this->Saler->save($saler)) {
        			return new WeixinTextSendPackage($this, "收银员[{$saler['Saler']['name']}]身份认证成功！");
        		}
        	} 
        	return new WeixinTextSendPackage($this, '验证码错误，身份认证失败');
        // validate coupon code.
        } elseif (preg_match('/^[0-9]{12,12}$/', $msg)) {
            $saler = $this->Saler->findByOpenId($openId);
            if($saler && !empty($saler['Saler']['open_id'])) {
        		$data = array(
    				'saler_id' => $saler['Saler']['id'],
    				'shop_id' => $saler['Shop']['id'],
    				'coupon' => $msg
        		);
        		$this->CouponCode->create($data);
        		if($this->CouponCode->save()) {
        			$this->Shop->increaseCouponTotal($saler['Shop']['id']);
        			return new WeixinTextSendPackage($this, '兑换码有效。使用成功！');
        		}
        		return new WeixinTextSendPackage($this, '兑换码已经使用过了, 请勿重复使用');
            } 
        }
        
        return new WeixinNullSendPackage($this);
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