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
APP::uses('WeixinApi', 'Controller/Component');
APP::uses('Remote', 'Controller/Component');
APP::uses('AutoReplyConfig', 'Model');
APP::uses('AutoReplyMessageNews', 'Model');
/**
 * The class decides which type message should returns.
 *
 * @package		app.Controller.Component
 */
class ResponseComponent extends Component {
    
/**
 * @var AutoReplyMessageTag
 */
    public $AutoReplyMessageTag;
/**
 * @var AutoReplyMessage
 */
    public $AutoReplyMessage;
/**
 * @var AutoReplyConfigTag
 */
    public $AutoReplyConfigTag;
/**
 * @var AutoReplyFixcode
 */
    public $AutoReplyFixcode;
/**
 * @var AutoReplyFixcodeKeyword
 */
    public $AutoReplyFixcodeKeyword;
/**
 * @var AutoReplyFixcodeMessage
 */
    public $AutoReplyFixcodeMessage;
/**
 * @var WeixinReceivePackage
 */
    public $recv;
    
    public $components = array(
    	'Remote'
    );
    
    public function __construct(ComponentCollection $collection, $settings = array()) {
        parent::__construct($collection, $settings);
        $this->AutoReplyMessageTag = ClassRegistry::init('AutoReplyMessageTag');
        $this->AutoReplyMessage = ClassRegistry::init('AutoReplyMessage');
        $this->AutoReplyConfigTag = ClassRegistry::init('AutoReplyConfigTag');
        $this->AutoReplyFixcode = ClassRegistry::init('AutoReplyFixcode');
        $this->AutoReplyFixcodeKeyword = ClassRegistry::init('AutoReplyFixcodeKeyword');
        $this->AutoReplyFixcodeMessage = ClassRegistry::init('AutoReplyFixcodeMessage');
    }
    
/**
 * @param WeixinReceivePackage $recv
 */
    public function setReceivePackage(WeixinReceivePackage $recv) {
        $this->recv = $recv;
    }
    
/**
 * @return WeixinReceivePackage
 */
    public function getReceivePackage() {
        return $this->recv;
    }
    
/**
 * Match fixcode(the highest priority).
 * 
 * @param string $tag
 * @param int $userId
 * @return WeixinSendPackage|null if no matched.
 */
    public function getFixcode($tag, $userId) {
        $fixcode = $this->AutoReplyFixcodeKeyword->find('first', array(
            'fields' => array(
                'AutoReplyFixcodeKeyword.auto_reply_fixcode_id' 
            ),
            'conditions' => array(
                'AutoReplyKeyword.name' => $tag,
                'AutoReplyKeyword.user_id' => $userId
            ),
            'contain' => array(
                'AutoReplyKeyword',
            ),
        ));
        
        $fixcodeIds = Hash::extract($fixcode, 'AutoReplyFixcodeKeyword.auto_reply_fixcode_id');
        $this->AutoReplyFixcode->increaseRequestTotal($fixcodeIds);
        
        $message = $this->AutoReplyFixcodeMessage->find('all', array(
            'conditions' => array(
                'AutoReplyFixcodeMessage.auto_reply_fixcode_id' => $fixcodeIds
            ),
            'fields' => array(
                'AutoReplyFixcodeMessage.auto_reply_message_id'
            ),
            'contain' => array(
                'AutoReplyMessage' => array(
                    'order' => array(
                        'AutoReplyMessage.id' => 'desc'
                    )
                )
            ),
            'order' => array(
                'id' => 'desc'
            ),
            'recursive' => -1,
            'limit' => 100,
        ));
        
        $messageIds = Hash::extract($message, '{n}.AutoReplyFixcodeMessage.auto_reply_message_id');
        $reducer = new FixcodeSendPackageReducer($this->getReceivePackage());
        if($messageIds) {
            $pager = ResponsePager::createNew($userId, $reducer, $messageIds, Configure::read('Limit.fixcode'));
            $reducer->setPager($pager);
            $messageIds = $pager->getNext();
        }
        
        return $this->getAutoReplyMessages($messageIds, $reducer);
    }
    
/**
 * To get the latest news messages, 
 * 
 * @param string $tag
 * @param int $userId
 * @return WeixinSendPackage
 */
    public function getNews($tag, $userId) {
        $messages = $this->AutoReplyMessageTag->find('all', array(
            'fields' => array(
                'AutoReplyMessage.id' 
            ),
            'conditions' => array(
                'AutoReplyTag.name' => $tag,
                'AutoReplyMessage.type' => array('custom', 'link', 'map'),
                'AutoReplyMessage.user_id' => $userId,
            ),
            'contain' => array(
                'AutoReplyMessage',
                'AutoReplyTag' 
            ),
            'order' => array(
                'AutoReplyMessage.created desc' 
            ),
            'limit' => 100
        ));
        
        $messageIds = Hash::extract($messages, '{n}.AutoReplyMessage.id');
        $reducer = new NewsSendPackageReducer($this->getReceivePackage());
        
        if($messageIds) {
            $pager = ResponsePager::createNew($userId, $reducer, $messageIds, Configure::read('Limit.news'));
            $reducer->setPager($pager);
            $messageIds = $pager->getNext();
        } else {
            // if there is nothing matched with tag...
            $news = $this->AutoReplyMessage->AutoReplyMessageNews->find('all', array(
                'conditions' => array(
                    'title LIKE' => '%'.trim($tag).'%'
                ),
                'fields' => array(
                    'auto_reply_message_id'
                ),
                'contain' => array(
                    'AutoReplyMessage' => array(
                        'conditions' => array(
                            'AutoReplyMessage.user_id' => $userId
                        )
                    )
                ),
                'recursive' => -1,
                'limit' => 100,
            ));
            if($news) {
                $messageIds = Hash::extract($news, '{n}.AutoReplyMessageNews.auto_reply_message_id');
                $pager = ResponsePager::createNew($userId, $reducer, $messageIds, Configure::read('Limit.news'));
                $reducer->setPager($pager);
                $messageIds = $pager->getNext();
            } 
        }
        
        return $this->getAutoReplyMessages($messageIds, $reducer);
    }
    
/**
 * Get default reply message when no matached keyword.
 * 
 * @param int $userId
 * @param int $limit 
 * @return WeixinSendPackage
 */
    public function getDefault($userId, $situation = AutoReplyConfig::EVT_NOANSWER) {
        $fixcode = $this->AutoReplyFixcode->find('first', array(
            'fields' => array(
            	'id'
            ),
            'conditions' => array(
                'user_id' => $userId,
                $situation => 1
            ),
            'recursive' => -1
        ));
        
        $fixcodeIds = Hash::extract($fixcode, 'AutoReplyFixcode.id');
        $this->AutoReplyFixcode->increaseRequestTotal($fixcodeIds);
        
        $message = $this->AutoReplyFixcodeMessage->find('all', array(
            'conditions' => array(
                'AutoReplyFixcodeMessage.auto_reply_fixcode_id' => $fixcodeIds
            ),
            'fields' => array(
                'AutoReplyFixcodeMessage.auto_reply_message_id' 
            ),
            'contain' => array(
                'AutoReplyMessage' => array(
                    'order' => array(
                        'id' => 'desc'
                    )
                )
            ),
            'recursive' => -1 ,
            'limit' => 100,
        ));
        
        $messageIds = Hash::extract($message, '{n}.AutoReplyFixcodeMessage.auto_reply_message_id');
        $reducer = new DefaultSendPackageReducer($this->getReceivePackage());
        if($messageIds) {
            $pager = ResponsePager::createNew($userId, $reducer, $messageIds, Configure::read('Limit.fixcode'));
            $reducer->setPager($pager);
            $messageIds = $pager->getNext();
        }
        
        return $this->getAutoReplyMessages($messageIds, $reducer);
    } 
    
/**
 * Get remote server object if pre-define matched.
 * @param string $content
 * @param int $userId
 * @return RemoteComponent|false
 */
    public function getRemoteByRegexp($content, $userId) {
        $echos = ClassRegistry::init('AutoReplyEcho')->findAllByUserId($userId);
        $raw = $this->getReceivePackage()->getRaw();
        return $this->Remote->setConfig($echos)->regexp($content, $raw);
    }
    
/**
 * Get remote server object if pre-define matched.
 * @param string $content
 * @param int $userId
 * @return RemoteComponent|false
 */
    public function getRemoteByLocation($userId) {
        $echos = ClassRegistry::init('AutoReplyEcho')->findAllByUserId($userId);
        $raw = $this->getReceivePackage()->getRaw();
        return $this->Remote->setConfig($echos)->location($raw);
    }
    
/**
 * 
 * @param array $messageIds
 * @param SendPackageReducer $reducer
 * @return WeixinSendPackage
 */
    public function getAutoReplyMessages($messageIds = array(), SendPackageReducer $reducer) {
        $data = $this->AutoReplyMessage->find('all', array(
            'conditions' => array(
                'AutoReplyMessage.id' => $messageIds 
            ),
            'contain' => array(
                'AutoReplyMessageNews',
                'AutoReplyMessageNews.ImageAttachment',
                'AutoReplyMessageCustom',
                'AutoReplyMessageExlink',
                'AutoReplyMessageLocation',
                'AutoReplyMessageLocation.AutoReplyLocation' 
            ),
            'order' => array(
                'AutoReplyMessage.id' => 'desc' 
            ),
            'recursive' => 2 
        ));
        return $reducer->getSendPackage($data, $messageIds);
    }
}

/**
 * Reduce array of messages and return an instance of WeixinSendPackage
 * which wrapped those messages.
 *
 */
abstract class SendPackageReducer {
    
/**
 * @var AutoReplyMessage
 */
    protected $AutoReplyMessage;
/**
 * @var WeixinReceivePackage
 */
    protected $recv;
/**
 * @var ResponsePager
 */
    protected $pager;
    
    public function __construct(WeixinReceivePackage $recv) {
        $this->AutoReplyMessage = ClassRegistry::init('AutoReplyMessage');
        $this->recv = $recv;
    }
    
/**
 * @param ResponsePager $pager
 */
    public function setPager(ResponsePager $pager) {
        $this->pager = $pager;
    }
    
/**
 * Append a new help item to end of data container
 * if there is next page.
 *
 * @param ResponsePager $pager
 * @param array $data
 * @return void
 */
    protected function appendNext(&$data = array()) {
    	if($this->pager->hasNext()) {
    		$item = array();
    		$item['AutoReplyMessageNews']['title'] = '>>>回复“n”查看更多';
    		$item['AutoReplyMessage']['description'] = '';
    		$item['AutoReplyMessage']['type'] = AutoReplyMessageNews::NEXT;
    		array_push($data, $item);
    	}
    }
    
/**
 * Return the instance of class name.
 * @return string
 */
    abstract function getName();
    
/**
 * Try to get an instance of WeixinSendPackage according with $data
 * @param array $data
 * @param array $messageIds
 * @return WeixinSendPackage
 */
    abstract function getSendPackage(array $data = array(), array $messageIds = array());
}

class FixcodeSendPackageReducer extends SendPackageReducer {
    
    public $name = 'FixcodeSendPackageReducer';
    
/**
 * (non-PHPdoc)
 * @see SendPackageReducer::getName()
 */
    public function getName() {
        return $this->name;
    }
    
/**
 * (non-PHPdoc)
 * @see SendPackageReducer::getSendPackage()
 */
    public function getSendPackage(array $data = array(), array $messageIds = array()) {
        if($data) {
        	$this->AutoReplyMessage->increaseRequestTotal($messageIds);
        	$this->appendNext($data);
        	if(count($data) == 1) {
        		if(in_array($data[0]['AutoReplyMessage']['type'], array(
        				AutoReplyMessageNews::CUSTOM,
        				AutoReplyMessageNews::LINK,
        				AutoReplyMessageNews::MAP
        		))) {
        			return new WeixinNewsSendPackage($this->recv, $data);
        		}
        		return new WeixinTextSendPackage($this->recv, $data[0]);
        	} else {
        		return new WeixinNewsSendPackage($this->recv, $data);
        	}
        }
    }
}

class NewsSendPackageReducer extends SendPackageReducer {
    
    public $name = 'NewsSendPackageReducer';
    
/**
 * (non-PHPdoc)
 * @see SendPackageReducer::getName()
 */
    public function getName() {
    	return $this->name;
    }
    
/**
 * (non-PHPdoc)
 * @see SendPackageReducer::getSendPackage()
 */
    public function getSendPackage(array $data = array(), array $messageIds = array()) {
        if($data) {
            $this->AutoReplyMessage->increaseRequestTotal($messageIds);
            $this->appendNext($data);
        	return new WeixinNewsSendPackage($this->recv, $data);
        } 
    }
}

class DefaultSendPackageReducer extends SendPackageReducer {

    public $name = 'DefaultSendPackageReducer';
    
/**
 * (non-PHPdoc)
 * @see SendPackageReducer::getName()
 */
    public function getName() {
    	return $this->name;
    }
    
/**
 * (non-PHPdoc)
 * @see SendPackageReducer::getSendPackage()
 */
    public function getSendPackage(array $data = array(), array $messageIds = array()) {
        if($data) {
            $this->AutoReplyMessage->increaseRequestTotal($messageIds);
            $this->appendNext($data);
            if(count($data) == 1) {
                if(in_array($data[0]['AutoReplyMessage']['type'], array(
                    AutoReplyMessageNews::CUSTOM,
                    AutoReplyMessageNews::LINK,
                    AutoReplyMessageNews::MAP 
                ))) {
                    return new WeixinNewsSendPackage($this->recv, $data);
                }
                return new WeixinTextSendPackage($this->recv, $data[0]);
            } else {
                return new WeixinNewsSendPackage($this->recv, $data);
            }
        }
        
        return new WeixinNullSendPackage($this->recv);
    }
}

/**
 * The class is used to control limit items 
 * are displayed and pagination.
 *
 */
class ResponsePager {
    
/**
 * The prefix of pagination information in cache.
 * @var string
 */
    const CACHE_CONFIG = 'Response.pager';
    
/**
 * The identical for user.
 * @var string|int
 */
    public $openId = '';
    
/**
 * The current page num, the initial starts from 0.
 * @var int
 */
    public $curPage = 0;
    
/**
 * The max page number calcualted.
 * @var int
 */
    public $maxPage = 0;
    
/**
 * The container of message ids.
 * @var array
 */
    public $messageIds = array();
    
/**
 * The limitation number of items displayed for each page.
 * @var int
 */
    public $limit = 10;
    
/**
 * @var the class name of instance class of 
 * SendPackageReducer
 */
    public $reducer = null;
    
/**
 * DO NOT uses "new" keyword to initial object.
 * @see ResponsePager::createNew()
 * @see ResponsePager::getFromCache()
 */
    private function __construct() {
        
    }
    
/**
 * Create an instance of ResponsePager and initialize.
 * 
 * @param int|string $openId
 * @param array $messageIds
 * @param number $limit
 * @return ResponsePager
 */
    public static function createNew($openId, SendPackageReducer $reducer, 
            $messageIds = array(), $limit = 10) {
        $pager = new ResponsePager();
        $pager->openId = $openId;
        $pager->messageIds = $messageIds;
        $pager->reducer = $reducer->getName();
        $pager->limit = $limit;
        $pager->curPage = 0;
    	$pager->maxPage = ceil(count($pager->messageIds)/($pager->limit));
    	$pager->store();
    	return $pager;
    }
    
/**
 * Get instance of ResponsePager from cache
 * If no found in cache, return false.
 * 
 * @param int $openId
 * @return ResponsePager
 */
    public static function getFromCache($openId) {
        $pages = Cache::read($openId, self::CACHE_CONFIG);
        $pages = unserialize($pages);
        $pager = new ResponsePager();
        if($pager) {
            $pager->openId = $pages['openId'];
            $pager->curPage = $pages['curPage'];
            $pager->maxPage = $pages['maxPage'];
            $pager->reducer = $pages['reducer'];
            $pager->messageIds = $pages['messageIds'];
            $pager->limit = $pages['limit'];
        }
        return $pager;
    }
    
/**
 * Get next page of instance by from user name.
 *
 * @return array $messageIds
 */
    public function getNext() {
        $messageIds = array();
        if(++$this->curPage < $this->maxPage) {
            $chunks = array_chunk($this->messageIds, $this->limit - 1);
            $messageIds = $chunks[$this->curPage - 1];
        } else if($this->curPage == $this->maxPage) {
            if($this->curPage > 1) {
                $chunks = array_chunk($this->messageIds, $this->limit - 1);
                $messageIds = $chunks[$this->curPage - 1];
            } elseif($this->curPage == 1) {
                $chunks = array_chunk($this->messageIds, $this->limit);
                $messageIds = $chunks[$this->curPage - 1];
            }
        }
        $this->store();
        return $messageIds;
    }
    
/**
 * Is there next resultset?
 * @return boolean true means there is next result-set.
 */
    public function hasNext() {
        return ($this->curPage < $this->maxPage) ? true : false;
    }
    
/**
 * @param WeixinReceivePackage $recv
 * @return SendPackageReducer
 */
    public function getReducer(WeixinReceivePackage $recv) {
        $reducer = new $this->reducer($recv);
        $reducer->setPager($this);
        return $reducer;
    }
    
/**
 * Initial pager if no found
 */
    private function store() {
    	Cache::write($this->openId, $this->toArray(), self::CACHE_CONFIG);
    }
    
/**
 * To array of ResponsePager
 * @return array
 */
    private function toArray() {
        $pager = array(
    		'openId' => $this->openId,
    		'curPage' => $this->curPage,
    		'maxPage' => $this->maxPage,
    		'reducer' => $this->reducer,
    		'messageIds' => $this->messageIds,
    		'limit' => $this->limit,
        );
        return serialize($pager);
    }
}