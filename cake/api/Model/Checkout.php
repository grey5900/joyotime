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
APP::uses('AppModel', 'Model');
APP::uses('PriceComponent', 'Controller/Component');
/**
 * The model of Checkout
 *
 * @package		app.Model
 */
class Checkout extends AppModel {
	
	public $primaryKey = '_id';
	
	public $name = 'Checkout';
	
	const TYPE_VOICE_INCOME = 1;
	const TYPE_VOICE_COST = 2;
	const TYPE_PAYMENT = 3;
	const TYPE_DRAW = 4;
	const TYPE_TRANSFER = 5;
	const TYPE_RECEIVED = 6;
	const TYPE_DRAW_REVERSE = 7;
	const TYPE_OFFICIAL_GIFT = 8;
	const TYPE_IOS_IAP = 9;
	const TYPE_DAILY_SIGNIN = 10;
	const TYPE_GIVE_TIP = 11;
	const TYPE_RECE_TIP = 12;
	const TYPE_SUBSIDY = 13;
	
	public $mongoSchema = array(
	    'user_id' => array('type' => 'string'), 
	    /**
	     * the available values are 
	     * 'earn' and 'cost' and 'send' and 'receive'
	     */
	    'type' => array('type' => 'integer'),      
	    'cover' => array('type' => 'string'),
	    'title' => array('type' => 'string'),
	    /**
	     * Buy voice mode only.
	     */
	    'voice_id' => array('type' => 'string'),
	    /**
	     * Transaction detail...
	     */
	    'amount' => array(
	        /**
	         * The unit is second.
	         */
	        'time' => array('type' => 'integer'),
	        /**
	         * It looks like `CNY` or `USD`
	         */
	        'currency' => array('type' => 'string'),
	        /**
	         * The final amount that means it has been costed all fees already
	         */
	        'money' => array('type' => 'float'),
	        /**
	         * The gateway fee
	         */
	        'fee' => array('type' => 'float'),
	        /**
	         * The name of payment gateway
	         */
	        'gateway' => array('type' => 'string'),
	    ),    
        /**
         * The user who transfered money to you (transfer mode only)
         */
	    'from' => array(
	        'user_id' => array('type' => 'string'),
	        'username' => array('type' => 'string')
	    ), 
	    /**
	     * The user who was transfered money (transfer mode only)
	     */
	    'to' => array(
	        'user_id' => array('type' => 'string'),
	        'username' => array('type' => 'string')
	    ),
	    'receipt' => array(
	        /**
	         * The byte array get from client side.
	         */
	        'data' => array('type' => 'string'),
	        /**
	         * The raw array confirmed from app stroe
	         */
	        'raw' => array('type' => 'string'),
	        /**
	         * The identify is used to identicate raw data.
	         * Because raw data is too long to search
	         * So generated identify by md5(raw) get a short string could be queried 
	         */
	        'identify' => array('type' => 'string'),
	    ),
	    /**
	     * The id of checkout is reverted
	     */
	    'reverted' => array('type' => 'string'),
	    /**
	     * The status of whether the withdrawal checkout is processed or not
	     * If not, processed = self::WITHDRAWAL_DONT_PROCESS_YET,
	     * otherwise, processed = self::WITHDRAWAL_PROCESSED,
	     * the last status is checkout is revert already, so processed = self::WITHDRAWAL_REVERTED
	     */
	    'processed' => array('type' => 'integer'),
	    /**
	     * That's why admin want to revert withdrawal
	     * Just only for self::TYPE_DRAW_REVERSE
	     */
	    'reason' => array('type' => 'string'),
	    'created' => array('type' => 'datetime'),
	    'modified' => array('type' => 'datetime'),
	);
	
	
	public function __construct($id = false, $table = null, $ds = null) {
		parent::__construct($id, $table, $ds);
		$this->initValidates();
	}

/**
 * Initialize validation rules
 */
	public function initValidates() {
		$this->validate = array(
			'user_id' => array(
				'required' => array(
					'rule' => array('isMongoId'),
					'required' => 'create'
				)
			),
			'type' => array(
				'required' => array(
					'rule' => 'notEmpty',
					'required' => 'create'
				)
			),
			'amount' => array(
				'required' => array(
					'rule' => array('chkAmount'),
					'message' => __('Invalid amount')
				)
			)
		);
	}
	
/**
 * (non-PHPdoc)
 * @see Model::implementedEvents()
 */
	public function implementedEvents() {
		$callbacks = parent::implementedEvents();
		return array_merge($callbacks, array(
			'Model.Receipt.afterPaid'  => 'receipt',
			'Model.Receipt.afterCreated.coupon' => 'receipt',
		    'Model.User.afterRegister'   => 'register'
		));
	}
	
/**
 * (non-PHPdoc)
 * @see Model::beforeValidate()
 */
	public function beforeValidate($options = array()) {
	    if(isset($this->data[$this->name]['type'])) {
    		switch($this->data[$this->name]['type']) {
    			case self::TYPE_TRANSFER:
    				$this->validate['amount']['required']['required'] = true;
    				$this->validate['to']['required']['required'] = true;
    				break;
    			case self::TYPE_RECEIVED:
    				$this->validate['amount']['required']['required'] = true;
    				$this->validate['from']['required']['required'] = true;
    				break;
    			case self::TYPE_OFFICIAL_GIFT:
    				$this->validate['amount']['required']['required'] = true;
    				break;
		    }
	    }
	}
	
/**
 * (non-PHPdoc)
 * @see Model::afterValidate()
 */
	public function afterValidate() {
		$this->initValidates();
	}
	
/**
 * (non-PHPdoc)
 * @see Model::afterSave()
 */
	public function afterSave($created, $options = array()) {
		if($created && isset($this->data[$this->name]['type'])) {
		    switch ($this->data[$this->name]['type']) {
		        case self::TYPE_RECEIVED:
		            // Send notification
		            CakeResque::enqueue('notification', 'NotificationShell',
		                array('transfer', $this->data[$this->name])
		            );
		            break;
		        case self::TYPE_RECE_TIP:
		            // Send notification
		            CakeResque::enqueue('notification', 'NotificationShell',
		                array('receiveTip', $this->data[$this->name])
		            );
		            break;
		        case self::TYPE_VOICE_INCOME:
		            // Model.Checkout.afterCreated.voice_income
		            $this->getEventManager()->dispatch(
		              new CakeEvent('Model.Checkout.afterCreated.voice_income', $this));
		            break;
		        case self::TYPE_VOICE_COST:
		            $this->getEventManager()->dispatch(
		              new CakeEvent('Model.Checkout.afterCreated.voice_cost', $this));
		            break;
		        case self::TYPE_DAILY_SIGNIN:
		            $this->getEventManager()->dispatch(
		              new CakeEvent('Model.Checkout.afterCreated.dailySignIn', $this));
		            break;
		    }
		}
	}
	
/**
 * Get purchase total by user
 * 
 * @param string $userId
 * @return int
 */
	public function getPurchaseCount($userId) {
	    return $this->find('count', array(
	        'conditions' => array(
	            'user_id' => $userId,
	            'type' => self::TYPE_VOICE_COST
	        )
	    ));
	}
	
/**
 * Get voice income total by user
 * 
 * @param string $userId
 * @return int
 */
	public function getVoiceIncomeCount($userId) {
	    return $this->find('count', array(
	        'conditions' => array(
	            'user_id' => $userId,
	            'type' => self::TYPE_VOICE_INCOME
	        )
	    ));
	}
	
/**
 * Daily sign in
 * 
 * @param string $userId
 * @return boolean | array
 */
	public function dailySignIn($userId, $award) {
	    $data = array(
	        'user_id' => $userId,
	        'type' => self::TYPE_DAILY_SIGNIN,
	        'amount' => array(
	            'time' => $award
	        )
	    );
	    return $this->create($data) && $this->save();
	}
	
	public function register(CakeEvent $event) {
	    $model = $event->subject();
	    $data = $model->data[$model->name];
	    
	    if($data['money'] == 0) return ;
	    
	    $data = array(
    		'user_id' => $data['_id'],
    		'type' => self::TYPE_OFFICIAL_GIFT,
    		'amount' => array(
    			'time' => Configure::read('Register.Award')
    		)
	    );
	    $result = (bool) $this->create($data) && $this->save();
	    if(!$result) $this->failEvent($event);
	}
	
/**
 * Create checkout for payee
 * 
 * @param array $user
 * @param array $voice
 * @param int $price
 * @return array|boolean
 */
	public function voiceIncome(array $user, array $voice, $price) {
	    if($this->create(array(
	        'user_id' => $voice['Voice']['user_id'],
	        'type' => self::TYPE_VOICE_INCOME,
	        'cover' => $voice['Voice']['cover'],
	        'title' => $voice['Voice']['title'],
	        'voice_id' => (string) $voice['Voice']['_id'],
	        'amount' => array(
	            'time' => $price
	        ),
	        'from' => array(
	            'user_id' => $user['User']['_id'],
	            'username' => $user['User']['username'],
	        )
	    ))) {
	        return $this->save();
	    }
	    return false;
	}
	
/**
 * Create a checkout for payer
 * 
 * @param array $user
 * @param array $voice
 * @param int $price
 * @return array|boolean
 */
	public function voiceCost(array $user, array $voice, $price) {
	    if($this->create(array(
    		'user_id' => (string) $user['User']['_id'],
    		'type' => self::TYPE_VOICE_COST,
    		'cover' => $voice['Voice']['cover'],
    		'title' => $voice['Voice']['title'],
	        'voice_id' => (string) $voice['Voice']['_id'],
    		'amount' => array(
    			'time' => $price
    		),
	    ))) {
			return $this->save();
		}
	    return false;
	}
		
/**
 * Create transfer checkout for payee
 * 
 * @param array $user
 * @param array $voice
 * @param int $price
 * @return array|boolean
 */
	public function received(array $payer, array $payee, $price) {
	    if($this->create(array(
    		'user_id' => $payee['_id'],
    		'type' => self::TYPE_RECEIVED,
    		'amount' => array(
    			'time' => $price
    		),
	        'from' => array(
        		'user_id' => $payer['_id'],
        		'username' => $payer['username'],
	        )
	    ))) {
			return $this->save();
		}
	    return false;
	}
	
/**
 * Create a checkout for user who ready for pay by alipay
 *
 * @param CakeEvent $event
 * @return array|boolean
 */
	public function receipt(CakeEvent $event) {
	    $model = $event->subject();
	    $data = $model->data[$model->name];
	    $save = array(
			'user_id' => $data['user_id'],
			'type' => self::TYPE_PAYMENT,
			'amount' => array(
				'time' => $data['amount']['time'],
				'gateway' => $data['type']
			)
		);
		$result = (bool)$this->create($save) && $this->save();
		if(!$result) $this->failEvent($event);
	}
	
/**
 * Create transfer checkout for payer
 *
 * @param array $payer        	
 * @param array $payee        	
 * @param int $seconds        	
 * @return array boolean
 */
	public function transfer(array $payer, array $payee, $seconds) {
		if ($this->create(array(
			'user_id' => $payer['_id'],
			'type' => self::TYPE_TRANSFER,
			'amount' => array(
				'time' => $seconds
			),
			'to' => array(
				'user_id' => $payee['_id'],
				'username' => $payee['username']
			)
		))) {
			return $this->save();
		}
		return false;
	}
	
/**
 * Create tip checkout for each users
 *
 * @param array $payer        	
 * @param array $payee        	
 * @param int $seconds        	
 * @return array boolean
 */
	public function tip(array $payer, array $payee, $seconds) {
	    $seconds = abs(intval($seconds));
	    $data = array(
			'user_id' => $payer['_id'],
			'type' => self::TYPE_GIVE_TIP,
			'amount' => array(
				'time' => $seconds
			),
			'to' => array(
				'user_id'  => $payee['_id'],
				'username' => $payee['username']
			)
		);
		if (!$this->create($data) || !$this->save()) return false;
		
	    $data = array(
			'user_id' => $payee['_id'],
			'type' => self::TYPE_RECE_TIP,
			'amount' => array(
				'time' => $seconds
			),
			'from' => array(
				'user_id'  => $payer['_id'],
				'username' => $payer['username']
			)
		);
		return $this->create($data) && $this->save();
	}
	
/**
 * Create gift checkout
 * 
 * @param string $userId
 * @param number $seconds
 * @param string $message
 * @return array|boolean
 */
	public function gift($userId, $seconds, $message = '') {
		$data = array(
			'user_id' => $userId,
			'type' => self::TYPE_OFFICIAL_GIFT,
			'amount' => array(
				'time' => $seconds
			),
			'message' => $message
		);
		 
		if($this->create($data)){
		    return $this->save();
		}
		return false;
	}
	
/**
 * Create sale subsidy checkout
 * 
 * @param string $userId
 * @param number $seconds
 * @param string $message
 * @return array|boolean
 */
	public function subsidy($userId, $voiceId, $seconds) {
		$data = array(
			'user_id' => $userId,
		    'voice_id' => $voiceId,
			'type' => self::TYPE_SUBSIDY,
			'amount' => array(
				'time' => $seconds
			)
		);
		return $this->create($data) && $this->save();
	}
	
/**
 * Check whether amount is valid or not
 *
 * @param array $check
 * @return boolean
 */
	public function chkAmount($check) {
		$amount = array();
		$time = 0;
		if(isset($check['amount'])) {
			$amount = $check['amount'];
		} else {
			$amount = $check;
		}
		if(!isset($amount['time'])) {
			return false;
		}
		$time = $amount['time'];
		if(!is_numeric($time) || $time < 1) {
			return false;
		}
		
		if($this->data[$this->name]['type'] == self::TYPE_PAYMENT) {
			// do somthing...
		}
		
		return true;
	}
}