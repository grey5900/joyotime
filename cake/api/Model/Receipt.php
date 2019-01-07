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
/**
 * @package		app.Model
 */
class Receipt extends AppModel {
	
	public $primaryKey = '_id';
	
	public $name = 'Receipt';
	
	const TYPE_ALIPAY = 1000;
	const TYPE_IOS    = 1001;
	const TYPE_COUPON = 1002;
	
	const STATUS_PENDING = 2000;
	const STATUS_PAID = 2001;
	const STATUS_PRICE_EXCEPTION = 2002;
	
	public $mongoSchema = array(
	    'user_id' => array('type' => 'string'), 
	    /**
	     * the available values are 
	     * 'earn' and 'cost' and 'send' and 'receive'
	     */
	    'type' => array('type' => 'integer'),
	    /**
	     * Price user should pay
	     */      
	    'price' => array('type' => 'double'),      
	    'trade_no' => array('type' => 'string'),      
	    'coupon' => array('type' => 'string'),      
	    /**
	     * Transaction detail...
	     */
	    'amount' => array(
	        /**
	         * The unit is second.
	         */
	        'time' => array('type' => 'integer')
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
	     * The status of receipt
	     */
	    'status' => array('type' => 'int'),
	    'created' => array('type' => 'datetime'),
	    'modified' => array('type' => 'datetime')
	);
	
	public function __construct($id = false, $table = null, $ds = null) {
	    parent::__construct($id, $table, $ds);
	    
	    $this->validate = array(
    		'user_id' => array(
				'required' => array(
					'rule' => array('isMongoId'),
					'required' => 'create',
				    'message' => __('Invalid user id')
				),
    		),
    		'type' => array(
				'required' => array(
					'rule' => array('notEmpty'),
					'required' => 'create',
				    'message' => __('Invalid type')
				),
    		),
    		'amount' => array(
				'required' => array(
					'rule' => array('chkAmount'),
					'required' => 'create',
				    'message' => __('Invalid amount')
				),
    		),
	        /*
	         * It would be modified in chkType() 
	         */
    		'receipt' => array(
				'required' => array(
					'rule' => array('chkReceipt'),
				    'message' => __('Invalid receipt')
				),
    		),
    		'status' => array(
				'required' => array(
					'rule' => array('chkStatus'),
					'required' => true,
				    'message' => __('Invalid status')
				)
    		)
	    );
	}
	
	/**
	 * (non-PHPdoc)
	 * @see Model::afterSave()
	 */
	public function afterSave($created, $options = array()) {
		if($created && isset($this->data[$this->name]['type'])) {
			switch ($this->data[$this->name]['type']) {
				case self::TYPE_COUPON:
					$this->getEventManager()->dispatch(
					   new CakeEvent('Model.Receipt.afterCreated.coupon', $this));
					break;
			}
		}
	}
	
/**
 * Create a receipt for coupon
 *
 * @param string $userId
 * @param string $code
 * @param int    $second
 * @return array|boolean
 */
	public function coupon($userId, $code, $second) {
	    $data = array(
			'user_id' => $userId,
			'type'    => self::TYPE_COUPON,
		    'status'  => self::STATUS_PAID,
		    'coupon'  => $code,
			'amount'  => array(
				'time' => $second
			)
		);
		return $this->create($data) && $this->save();
	}
	
/**
 * Create a receipt for user who readies for pay by alipay
 *
 * @param string $userId
 * @param number $price
 * @param int $seconds
 * @return array|boolean
 */
	public function alipay($userId, $price, $seconds) {
		if($this->create(array(
			'user_id' => $userId,
			'type' => self::TYPE_ALIPAY,
		    'status' => self::STATUS_PENDING,
		    'price' => (float) $price,
			'amount' => array(
				'time' => $seconds
			)
		))) {
			return $this->save();
		}
		return false;
	}
	
/**
 * Create a receipt for user who readies for pay by alipay
 *
 * @param string $userId
 * @param int $seconds
 * @param string $receipt
 * @param array $info
 * @return array|boolean
 */
	public function ios($userId, $seconds, $receipt, $info) {
		if($this->create(array(
			'user_id' => $userId,
			'type' => self::TYPE_IOS,
		    'status' => self::STATUS_PAID,
		    'receipt' => array(
	    		'data' => $info,
	    		'raw' => $receipt,
		        'identify' => md5($receipt)
		    ),
			'amount' => array(
				'time' => $seconds
			)
		))) {
    	    $saved = $this->save();
			if($saved) {
			    $this->data = $saved;
			    $this->getEventManager()->dispatch(new CakeEvent('Model.Receipt.afterPaid', $this));
			    return $saved;
			}
		}
		return false;
	}
	
/**
 * Change receipt status from pendding to paid
 * 
 * @param string $receiptId
 * @return boolean
 */
	public function paid($receiptId, $data = array()) {
	    if(!$receiptId) {
	        return false;
	    }
	    
	    $receipt = $this->findById($receiptId);
	    if(!$receipt) {
	        return false;
	    }
	    
	    $price = isset($data['price']) ? (float)$data['price'] : false;
	    $trade = isset($data['trade_no']) ? $data['trade_no'] : false;
	    
	    if(!$price || !$trade) {
	        return false;
	    }
	    
	    // Handle price exception...
	    if($receipt[$this->name]['price'] != $price) {
	        $result = $this->updateAll(
        		array('$set' => array(
    				'status' => self::STATUS_PRICE_EXCEPTION,
    				'trade_no' => $trade
        		)),
        		array(
    				'_id' => new MongoId($receiptId)
        		)
	        );
	        if($result) {
	        	CakeResque::enqueue('notification', 'NotificationShell',
                    array('payfail', $receipt[$this->name])
                );
	        }
	        return false;
	    }
	    
	    // Handle normal payment...
	    $result = $this->updateAll(
            array('$set' => array(
                'status' => self::STATUS_PAID,
                'trade_no' => $trade
            )), 
            array(
                '_id' => new MongoId($receiptId),
                'status' => self::STATUS_PENDING
            )
	    );
	    if($result) {
	        $this->read(null, $receiptId);
	        $this->getEventManager()->dispatch(new CakeEvent('Model.Receipt.afterPaid', $this));
	    }
	    return $result;
	}
	
/**
 * Check whether type is valid
 * 
 * @param array|string $check
 * @return boolean
 */
	public function chkType($check) {
	    if(isset($check['type'])) {
	        $type = $check['type'];
	    } else {
	        $type = $check;
	    }

	    if($type == self::TYPE_ALIPAY
	        || $type == self::TYPE_IOS) {
	        
	        if($type == self::TYPE_IOS) {
	            // Whether type is ios, needs to check receipt data...
	            $this->validate['receipt']['required']['required'] = 'create';
	        }
	        return true;
	    }
	    return false;
	}
	
/**
 * Check whether status is valid
 * 
 * @param array|string $check
 * @return boolean
 */
	public function chkStatus($check) {
	    if(isset($check['status'])) {
	        $status = $check['status'];
	    } else {
	        $status = $check;
	    }

	    if($status == self::STATUS_PAID
	        || $status == self::STATUS_PENDING) {
	        return true;
	    }
	    return false;
	}
	
/**
 * $data = array(
 * 		"quantity" => "1",
 * 		"product_id" => "com.joyotime.fs.tier5",
 * 		"transaction_id" => "1000000087424240",
 * 		"purchase_date" => "2013-09-17 04:02:47 Etc\/GMT",
 * 		"item_id" => "702763541",
 * 		"bid" => "com.joyotime.fishsaying",
 * 		"bvrs" => "1.0.0"
 * );
 *
 * @param array $check
 * @return boolean
 */
	public function chkReceipt($check) {
	    // Only for ios...
		if(!isset($check['receipt']['raw'])) {
			$this->log('no found receipt raw', 'debug');
			return false;
		}
		if(!isset($check['receipt']['identify'])) {
			$this->log('no found receipt identify', 'debug');
			return false;
		}
		if(!isset($check['receipt']['data'])) {
			$this->log('no found receipt info data', 'debug');
			return false;
		}
		if(!isset($check['receipt']['data']['quantity'])
    		|| !isset($check['receipt']['data']['product_id'])
    		|| !isset($check['receipt']['data']['transaction_id'])
    		|| !isset($check['receipt']['data']['purchase_date'])
    		|| !isset($check['receipt']['data']['item_id'])
    		|| !isset($check['receipt']['data']['bid'])
    		|| !isset($check['receipt']['data']['bvrs'])
		) {
			$this->log('Incompleted receipt info data', 'debug');
			$this->log($check, 'debug');
			return false;
		}
		if(!array_key_exists($check['receipt']['data']['product_id'], Configure::read('AppStore.Product'))) {
			$this->log('Invalid product_id in receipt info data', 'debug');
			$this->log($check, 'debug');
			return false;
		}
		if($this->exist($check['receipt']['raw'])) {
		    $this->log('Existed for this receipt', 'debug');
		    return false;
		}
		return true;
	}
	
/**
 * Check whether the receipt is repeat
 *
 * @param string $receipt The binary json string got from AppStore
 * @return boolean
 */
	public function exist($receipt) {
		return $this->find('count', array(
			'conditions' => array(
				'receipt.identify' => md5($receipt)
			)
		)) > 0;
	}
}