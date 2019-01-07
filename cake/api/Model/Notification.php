<?php
/**
 * The project of FishSaying is a SNS platform which is
 * based on voice sharing for each other with journey.
 *
 * The RESTful style API is used to communicate with each client-side.
 *
 * PHP 5
 *
 * FishSaying(tm) : FishSaying (http://www.fishsaying.com)
 * Copyright (c) fishsaying.com. (http://fishsaying.com)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) fishsaying.com. (http://www.fishsaying.com)
 * @link          http://fishsaying.com FishSaying(tm) Project
 * @since         FishSaying(tm) v 0.0.1
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
APP::uses('CakeTime', 'Utility');
/**
 * @package		app.Model
 */
class Notification extends AppModel {
    
    public $primaryKey = '_id';
    
    public $name = 'Notification';
	
	public $mongoSchema = array(
		'user_id' => array('type'=>'string'),
	    'voice_id' => array('type' => 'string'),
	    'type' => array('type' => 'integer'),
        'message' => array('type' => 'string'),
	    'template' => array('type' => 'string'),    // The template for merge.
        'merged' => array('type' => 'integer'),     // The number of merged already.
        'link' => array('type' => 'string'),
	    'isread' => array('type' => 'integer'),     // Whether notification is read.
	    /**
	     * Is sent by admin?
	     */
	    'official' => array('type' => 'integer'),    
        'created' => array('type' => 'datetime'),
        'modified' => array('type' => 'datetime'),
	);
	
	const TYPE_NEW_COMMENT = 1000;
	const TYPE_VOICE_APPROVED = 1003;
	const TYPE_VOICE_INVALID = 1004;
	const TYPE_VOICE_UNAVAILABLE = 1005;
	const TYPE_VOICE_TRANSFER = 1006;
	const TYPE_HIDE_COMMENT = 1007;
	const TYPE_DRAW_SUCCESS = 1008;
	const TYPE_DRAW_REVERSE = 1009;
	const TYPE_GIFT = 1010;
	const TYPE_BROADCAST = 1012;
	const TYPE_PAY_FAIL = 1013;
	const TYPE_RECE_TIP = 1014;
	
	public function __construct($id = false, $table = null, $ds = null) {
		parent::__construct($id, $table, $ds);
		
		$this->validate = array(
		    'user_id' => array(
		        'required' => array(
		            'rule' => 'notEmpty',
    				'required' => true,
    				'allowEmpty' => false,
    				'message' => __('Invalid userid supplied')
		        )
		    ),
		    'message' => array(
		        'required' => array(
		            'rule' => 'notEmpty',
    				'required' => true,
    				'allowEmpty' => false,
    				'message' => __('Invalid message supplied')
		        )
		    ),
		    'link' => array(
		        'required' => array(
		            'rule' => 'notEmpty',
    				'allowEmpty' => false,
    				'message' => __('Invalid link supplied')
		        )
		    ),
		    'type' => array(
		        'required' => array(
		            'rule' => 'notEmpty',
		            'required' => true,
    				'allowEmpty' => false,
    				'message' => __('Invalid type supplied')
		        )
		    )
		);
    	
    	$this->User = ClassRegistry::init('User');
	}
	
	/**
	 * @param string $userId
	 */
	public function setLanguage($userId) {
	    $user = $this->User->findById($userId);
	    if(isset($user['User']['locale'])) {
	        Configure::write('Config.language', $user['User']['locale']);
	    }
	}
	
/**
 * (non-PHPdoc)
 * @see Model::beforeValidate()
 */
	public function beforeValidate($options = array()) {
	    if(isset($this->data[$this->name]['mergable'])
	        && $this->data[$this->name]['mergable'] == TRUE) {
	        $this->validate['merged'] = array(
	            'required' => array(
            		'rule' => 'notEmpty',
            		'required' => true,
            		'allowEmpty' => false,
            		'message' => __('Invalid merged number supplied')
	            )
	        );
	        $this->validate['template'] = array(
	            'required' => array(
            		'rule' => 'notEmpty',
            		'required' => true,
            		'allowEmpty' => false,
            		'message' => __('Invalid template supplied')
	            )
	        );
	    }
	}
	
/**
 * Send notification when voice is approved by admin
 * 
 * @param CakeEvent $event
 * @return array|boolean
 */
	public function payfail($data) {
	    $this->setLanguage($data['user_id']);
	    
	    $tpl = __('Something is wrong with your recharge (#%d# minutes, ¥#%s#), please contact us via feedback');
		if($this->create(array(
			'user_id' => $data['user_id'],
			'message' => sprintf($tpl, $data['amount']['time'] / 60, $data['price']),
			'type' => self::TYPE_PAY_FAIL,
			'link' => "account://0"
		))) {
		    return $this->save();
		}
		return false;
	}
	
/**
 * Send notification when voice is approved by admin
 * 
 * @param CakeEvent $event
 * @return array|boolean
 */
	public function approved($data) {
	    $this->setLanguage($data['user_id']);
	    
	    $tpl = __('Your voice #%s# has been verified.');
		if($this->create(array(
			'user_id' => $data['user_id'],
			'voice_id' => $data['_id'],
			'message' => sprintf($tpl, $data['title']),
			'type' => self::TYPE_VOICE_APPROVED,
			'link' => "myvoices://0"
		))) {
		    return $this->save();
		}
		return false;
	}
	
/**
 * Send notification when a unavailable voice is approved by admin
 * 
 * @param CakeEvent $event
 * @return array|boolean
 */
	public function approvedAgain($data) {
	    $this->setLanguage($data['user_id']);
	    
	    $tpl = __('Your voice #%s# has been put on shelf anew.');
		if($this->create(array(
			'user_id' => $data['user_id'],
			'voice_id' => $data['_id'],
			'message' => sprintf($tpl, $data['title']),
			'type' => self::TYPE_VOICE_APPROVED,
			'link' => "myvoices://0"
		))) {
		    return $this->save();
		}
		return false;
	}
	
/**
 * Send notification when voice is marked invalid by admin
 *
 * @param CakeEvent $event
 * @return array|boolean
 */
	public function invalid($data) {
	    $this->setLanguage($data['user_id']);
	    
		$template = __('Your voice #%s# has been rejected for "%s".');
		$comment = '';
		if(isset($data['comment'])) {
			$comment = $data['comment'];
		}
		if($this->create(array(
			'user_id' => $data['user_id'],
			'voice_id' => $data['_id'],
			'message' => sprintf($template, $data['title'], $comment),
			'type' => self::TYPE_VOICE_INVALID,
			'link' => "myvoices://0"
		))) {
		    return $this->save();
		}
		return false;
	}
	
/**
 * Send notification when voice is marked unavailable by admin
 *
 * @param CakeEvent $event
 * @return array|boolean
 */
	public function unavailable($data) {
	    $this->setLanguage($data['user_id']);
	    
		$tpl = __('Your voice #%s# has been pulled off shelf for "%s".');
		$comment = '';
		if(isset($data['comment'])) {
			$comment = $data['comment'];
		}
		if($this->create(array(
			'user_id' => $data['user_id'],
			'message' => sprintf($tpl, $data['title'], $comment),
			'type' => self::TYPE_VOICE_UNAVAILABLE,
		    'link' => "myvoices://0"
		))) {
		    return $this->save();
		}
		return false;
	}
	
/**
 * Send notification when new comment created.
 * 
 * @param CakeEvent $event
 * @return array|boolean
 */
	public function newComment($data) {
	    $voiceTitle = $data['voice_title'];
	    $userId = $data['voice_user_id'];
	    $voiceId = $data['voice_id'];
	    
	    $this->setLanguage($userId);
	    
	    $template = __('Your voice #%s# receives %s new comments.');
	    
	    $data = array();
	    
	    // Try to get same notification could be merged...
	    $row = $this->find('first', array(
	        'conditions' => array(
	            'user_id' => $userId,
	            'voice_id' => $voiceId,
	            'type' => self::TYPE_NEW_COMMENT,
	        ),
	        'order' => array(
	            'modified' => 'desc'
	        )
	    ));
	    
	    // initial merged number
	    $merged = 1;
	    
	    // If notification has existed and no read yet, preparing to merge...
	    if(isset($row[$this->name]['_id']) 
	        && (!isset($row[$this->name]['isread']) || $row[$this->name]['isread'] == 0)) {
	    	$data = array_merge($data, array('_id' => $row[$this->name]['_id']));
	    	
	    	if(isset($row[$this->name]['merged'])) {
	    		$merged = intval($row[$this->name]['merged']);
	    	}
	    	// Increament merged number...
	    	$merged++;
	    }
	    
	    $data = array_merge($data, array(
    		'user_id' => $userId,
    		'voice_id' => $voiceId,
    		'message' => sprintf($template, $voiceTitle, $merged),
    		'type' => self::TYPE_NEW_COMMENT,
    		'merged' => $merged,                 // The number of merged already.
    		'link' => "comment://$voiceId"     // The redirect url after clicked.
	    ));
	    
        return $this->save($data);
	}
	
/**
 * Send message to user because of received gift
 * 
 * @return boolean
 */
	public function gift($data) {
	    $save = array(
	        'user_id' => $data['user_id'],
	        'type' => self::TYPE_BROADCAST,
	        'message' => $data['message'],
	        'official' => 1,
	        'link' => 'account://0'
	    );
	    
	    if($this->create($save)) { 
	        return $this->save();
	    }
	    return false;
	}
	
/**
 * Notify user got gift for register
 *
 * @param string $userId
 * @param string $seconds
 * @return boolean
 */
	public function giftRegister($data) {
		Configure::write('Config.language', $data['locale']);
		$tpl = __('Welcome to join FishSaying! We are honored to give you #%d# minutes as a gift. Start your journey now!');
		$template = sprintf($tpl, ceil($data['money']/60));
		$data = array(
			'user_id' => $data['_id'],
			'message' => $template,
			'type' => self::TYPE_GIFT,
			'link' => 'account://0'
		);
		
		if($this->create($data)) {
			return $this->save();
		}
		return false;
	}
	
/**
 * Send broadcasting message to user
 * 
 * @param CakeEvent $event
 * @return boolean
 */
	public function broadcast($userId, $data) {
	    $save = array(
	        'user_id' => $userId,
	        'type' => self::TYPE_BROADCAST,
	        'message' => $data['message'],
	        'official' => 1
	    );
	    
	    if(isset($data['link']) && !empty($data['link'])) {
	        $save = array_merge($save, array('link' => $data['link']));
	    }
	    
	    if($this->create($save)) { 
	        return $this->save();
	    }
	    return false;
	}
	
/**
 * Send notification after transfer successful
 * 
 * @return array|boolean
 */
	public function transfer($data) {
	    $this->setLanguage($data['user_id']);
	    $template = __('Your friend #%s# just transferred #%d# minutes to you, please check your account.');
	    if($this->create(array(
	        'user_id' => $data['user_id'],
	        'message' => sprintf($template, $data['from']['username'], 
	                ceil($data['amount']['time']/60)),
	        'type' => self::TYPE_VOICE_TRANSFER,
	        'link' => 'account://0',
	    ))) {
	        return $this->save();
	    }
	    return false;
	}
	
/**
 * Send notification after transfer successful
 * 
 * @return array|boolean
 */
	public function receiveTip($data) {
	    $this->setLanguage($data['user_id']);
	    $template = __('#%s#, tipped you #%d# minutes, please check your account.');
	    if($this->create(array(
	        'user_id' => $data['user_id'],
	        'message' => sprintf($template, $data['from']['username'], 
	                ceil($data['amount']['time']/60)),
	        'type' => self::TYPE_RECE_TIP,
	        'link' => 'account://0',
	    ))) {
	        return $this->save();
	    }
	    return false;
	}

/**
 * Send notification when comment is removed by admin
 * 
 * @param CakeEvent $event
 * @return array|boolean
 */
	public function hideComment($data) {
	    $this->setLanguage($data['user_id']);
	    
	    $voiceTitle = $data['voice_title'];
	    $template = __('Sorry, your comment on #%s# has been hidden for inappropriate content.');
	    return $this->save(array(
	        'user_id' => $data['user_id'],
	        'message' => sprintf($template, $voiceTitle),
	        'type' => self::TYPE_HIDE_COMMENT
	    ));
	}

/**
 * The callback method for sending message that apply for withdraw is aceepted 
 * 
 * @param CakeEvent $event
 * @return array|boolean
 */
	public function withdrawal($data) {
		$this->setLanguage($data['user_id']);
		
		$tpl = __('Your request for withdrawal #%s# has been processed, please check your account.');
		
		// @todo needs refactoring here...
		APP::uses('PriceComponent', 'Controller/Component');
		$price = new PriceComponent(new ComponentCollection());
		$cash = $price->toCash($data['amount']['currency']);
		
	    if($this->create(array(
	        'user_id' => $data['user_id'],
	        'message' => sprintf($tpl, 
	        		$cash->currency().$data['amount']['money'], 
	        		CakeTime::format(time(), '%Y-%m-%d')),
	        'type' => self::TYPE_DRAW_SUCCESS
	    ))) {
	        return $this->save();
	    }
	    return false;
	}
	
/**
 * The callback method 
 * 
 * @param CakeEvent $event
 * @return array|boolean
 */
	public function reverseWithdawal($data) {
		$this->setLanguage($data['user_id']);
		
		$tpl = __('Sorry, your request for withdrawal on %s has been rejected for "%s". Your frozen #%d# minutes has been returned to your account.');
		
	    if($this->create(array(
	        'user_id' => $data['user_id'],
	        'message' => sprintf($tpl, 
	        		CakeTime::format(time(), '%Y-%m-%d'), 
	        		$data['reason'], 
	        		ceil($data['amount']['time']/60)),
	        'type' => self::TYPE_DRAW_REVERSE,
	    	'link' => 'account://0'
	    ))) {
	        return $this->save();
	    }
	    return false;
	}
	
/**
 * @param array $check
 * @return boolean
 */
	public function chkType($check) {
	    if($check && is_array($check) && isset($check['type'])) {
	        $type = $check['type'];
	        switch ($type) {
	            case self::TYPE_NEW_COMMENT:
    	        case self::TYPE_VOICE_APPROVED:
    	        case self::TYPE_VOICE_INVALID:
    	        case self::TYPE_VOICE_UNAVAILABLE:
    	        case self::TYPE_VOICE_TRANSFER:
    	        case self::TYPE_HIDE_COMMENT:
    	        case self::TYPE_DRAW_SUCCESS:
    	        case self::TYPE_DRAW_REVERSE:
    	        case self::TYPE_GIFT:
    	        case self::TYPE_BROADCAST:
    	        case self::TYPE_PAY_FAIL:
                    return true;
	        }
	    }
	    return false;
	}
}