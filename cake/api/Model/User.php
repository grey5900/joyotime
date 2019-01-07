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
APP::uses('Security', 'Utility');
APP::uses('Validation', 'Utility');
/**
 * @package		app.Model
 */
class User extends AppModel {
	
	public $primaryKey = '_id';
	
	public $name = 'User';
	
	public $mongoSchema = array(
		'username' => array('type'=>'string'),
		'email' => array('type'=>'string'),
		'password' => array('type'=>'string'),
		'avatar' => array(
		    'source' => array('type' => 'string'),    
		    'x80' => array('type' => 'string'),    
		    'x180' => array('type' => 'string'),    
		),
	    'money' => array('type' => 'integer'),                 // 账户总额
	    'earn' => array('type' => 'integer'),                  // voice销售所得
	    'cost' => array('type' => 'integer'),                  // 支出总计
	    'income' => array('type' => 'integer'),                // 收入总计，包括转账，赠送，销售
		'latest_voice_posts' => array('type'=>'datetime'),     // 最新一条语音发布的时间
		'voice_total' => array('type'=>'integer'),             // 发布的语音总数
	    'favorite_size' => array('type' => 'integer'),
	    'purchase_total' => array('type' => 'integer'),        // 总购买voice的数量
	    'voice_income_total' => array('type' => 'integer'),    // 总卖出voice的数量
	    'voice_length_total' => array('type' => 'integer'),    // 总voice的时长统计数量
	    'locale' => array('type' => 'string'),                 // client本地化所属地区
	    /*
	     * Records of gift sent
	     */
	    'gift' => array(
	        'register' => array(
	            /*
	             * Record sent gift to who
	             */
	            'device_code' => array('type' => 'string'),
	        )
	    ),
		'role' => array('type' => 'string'),
	    /*
	     * The list of device code which is used by user...
	     */
		'device_code' => array(),
		'reg_source' => array('type' => 'string'),
		/**
		 * Credential data...
		 */
		'certified' => array(
			'sina_weibo' => array(
				'open_id' => array('type' => 'string')
			),
			'qzone' => array(
				'open_id' => array('type' => 'string')
			),
			'twitter' => array(
				'open_id' => array('type' => 'string')
			),
			'facebook' => array(
				'open_id' => array('type' => 'string')
			)
		),
	    'is_contributor' => array('type' => 'boolean'),
	    /**
	     * Whether verified or not.
	     */
	    'is_verified' => array('type' => 'boolean'),
	    /**
	     * Verify information
	     */
	    'verified_description' => array('type' => 'string'),
	    /**
	     * Personalized signature
	     */
	    'description' => array('type' => 'string'),
	    /**
	     * VIP user background
	     *
	     * It includes:
	     * 1. the path of source.
	     * 2. the path of scaled 80 * 80
	     * 3. the path of scaled 160 * 160
	     * 4. the path of scaled 640 * 640
	     */
	    'cover' => array(
    		'source' => array('type'=>'string'),
    		'x80'  => array('type'=>'string'),
    		'x160' => array('type'=>'string'),
    		'x640' => array('type'=>'string')
	    ),
	    'recommend'        => array('type' => 'integer'),
	    'recommend_reason' => array('type' => 'string'),
	    'belong_partner'   => array('type' => 'string'),
		'created'  => array('type' => 'datetime'),
		'modified' => array('type' => 'datetime')
	);
	
	const ROLE_ADMIN = 'admin';
	const ROLE_USER = 'user';
	
	const USERNAME_MIN_LENGTH = 1;
	const USERNAME_MAX_LENGTH = 30;
	
	const PASSWORD_MIN_LENGTH = 6;
	const PASSWORD_MAX_LENGTH = 12;
	
	const NAME_SINA_WEIBO = "sina_weibo";
	const NAME_QZONE = "qzone";
	const NAME_FACEBOOK = "facebook";
	const NAME_TWITTER = "twitter";
	
/**
 * User info of updating
 * 
 * @var array
 */
	private $user;
	
/**
 * @var DeviceCode
 */
	private $deviceCode;
	
	public function __construct($id = false, $table = null, $ds = null) {
        parent::__construct($id, $table, $ds);
        
        $this->validate = array_merge($this->validate, array(
            'username' => array(
                'required' => array(
                    'rule' => array('chkUsername'),
                    'required' => 'create',
                    'allowEmpty' => false,
                    'message' => __('Invalid username supplied') 
                ),
            	'unique' => array(
            		'rule' => array('isUserNameUnique'),
            		'allowEmpty' => false,
            		'message' => __('Username has existed already')
            	)
            ),
            'email' => array(
        		'required' => array(
    				'rule' => array('email'),
    				'required' => 'create',
    				'allowEmpty' => false,
    				'message' => __('Invalid email supplied')
        		),
        		'unique' => array(
    				'rule' => array('isUnique'),
    				'message' => __('Email has existed already')
        		),
        		'update' => array(
    				'rule' => array('chkEmailUpdate'),
    				'message' => __('The original password is invalid')
        		),
            ),
            'password' => array(
                'required' => array(
                    'rule' => array('chkPassword'),
                    'required' => 'create',
                    'allowEmpty' => false,
                    'message' => __('Invalid password') 
                )
            ),
            'old_password' => array(
        		'same' => array(
    				'rule' => array('chkSamePwd'),
    				'message' => __('The original password is invalid')
        		)
            ),
            'avatar' => array(
                'required' => array(
                    'rule' => array('chkAvatar'),
                    'required' => false,
                    'message' => __('Invalid avatar supplied') 
                ) 
            ),
            'money' => array(
                'required' => array(
                    'rule' => 'notEmpty',
                    'required' => 'create',
                    'allowEmpty' => false,
                    'message' => __('Invalid money supplied') 
                ) 
            ),
            'earn' => array(
                'required' => array(
                    'rule' => 'notEmpty',
                    'required' => 'create',
                    'allowEmpty' => false,
                    'message' => __('Invalid earn supplied') 
                ) 
            ),
            'cost' => array(
                'required' => array(
                    'rule' => 'notEmpty',
                    'required' => 'create',
                    'allowEmpty' => false,
                    'message' => __('Invalid cost supplied') 
                ) 
            ),
            'role' => array(
                'required' => array(
                    'rule' => array(
                        'chkRole' 
                    ),
                    'required' => 'create',
                    'allowEmpty' => false,
                    'message' => __('Invalid role supplied') 
                ) 
            ),
            'device_code' => array(
                'required' => array(
                    'rule' => 'chkDeviceCode',
                    'required' => 'create',
                    'allowEmpty' => false,
                    'message' => __('Invalid device code supplied') 
                ) 
            ),
			'locale' => array(
				'required' => array(
					'rule' => array('chkLocale'),
					'required' => 'create',
					'allowEmpty' => false,
					'message' => __("Invalid locale")
				)
			),
			'reg_source' => array(
				'required' => array(
					'rule' => 'notEmpty',
					'required' => 'create',
					'allowEmpty' => false,
					'message' => __("Invalid reg source")
				)
			),
			'certified' => array(
				'required' => array(
					'rule' => array('chkCertified'),
					'message' => __("Invalid certified")
				),
				'unique' => array(
					'rule' => array('chkUniqueCertified'),
					'message' => __("Your account has been bound to another fishsaying account")
				),
				'format' => array(
					'rule' => array('chkCertifiedFormat')	// It always be TRUE...
				)
			),
            'is_contributor'   => 'chkIsContributor',
            'is_verified'      => 'chkIsVerified',
            'cover'            => 'chkCover',
            'recommend' => array(
                'required' => array(
            		'rule' => array('chkRecommend'),
            		'message' => __("Just user role as `contributor` can be recommended")
                )
            )
        ));
    }
	
/**
 * (non-PHPdoc)
 * @see Model::beforeValidate()
 */
	public function beforeValidate($options = array()) {
        if(!$this->isMainModel()) return;
        
        if(FALSE == ($id = $this->isUpdate())) {
            $this->deviceCode = ClassRegistry::init('DeviceCode');
            $this->getEventManager()->attach($this->deviceCode);
            
            // It's creating...
            $award = $this->getRegisterAward();
            $this->data[$this->name]['voice_length_total'] = 0;
            $this->data[$this->name]['purchase_total'] = 0;
            $this->data[$this->name]['voice_income_total'] = 0;
            $this->data[$this->name]['voice_total'] = 0;
            $this->data[$this->name]['favorite_size'] = 0;
            $this->data[$this->name]['money']  = $award;
            $this->data[$this->name]['earn'] = 0;
            $this->data[$this->name]['cost'] = 0;
            $this->data[$this->name]['is_verified'] = 0;
            $this->data[$this->name]['is_contributor'] = 0;
            $this->data[$this->name]['income'] = $award;
            
            if(!isset($this->data[$this->name]['role'])) {
            	$this->data[$this->name]['role'] = \Controller\Setting\Permission::ROLE_USER;
            }
            
            // If request is third-party ssologin, then modify validation rules.
            if($this->isCreateByCertified()) {
            	$this->validate['email']['required']['required'] = false;
            	$this->validate['email']['required']['allowEmpty'] = true;
            	unset($this->validate['password']);
            	if(!isset($this->data[$this->name]['email'])) {
            	    $this->data[$this->name]['email'] = '';
            	}
            	$this->data[$this->name]['password'] = '';
            	// Initial ceritified
            	$certified = $this->gets('certified', $this->data[$this->name]);
            	$this->data[$this->name]['reg_source'] = $certified;
            } else {
                $this->data[$this->name]['reg_source'] = 'email';
            }
        } else {
            $this->user = $this->findById($id);
        }
	}
	
/**
 * (non-PHPdoc)
 * @see Model::afterValidate()
 */
	public function afterValidate() {
	    if(!$this->isMainModel()) return;
		// Whether it's creating...
	    if(isset($this->data[$this->name]['password']) && $this->data[$this->name]['password']) {
            $this->data[$this->name]['password'] = $this->encrypt($this->data[$this->name]['password']);
	    }
	}
	
/**
 * (non-PHPdoc)
 * @see Model::afterSave()
 */
	public function afterSave($created, $options = array()) {
	    if(!$this->isMainModel()) return;
	    if($this->id) {
    	    if($created) {
    	        if($this->data[$this->name]['money'] > 0) {
        	        CakeResque::enqueue('notification', 'NotificationShell',
        	            array('giftRegister', $this->data[$this->name])
        	        );
    	        }
    	        $this->getEventManager()->dispatch(new CakeEvent('Model.User.afterRegister', $this));
    	    } else {
    	        // It is going to hash username...
    	        if(isset($this->data[$this->name]['username'])) {
        	        $this->getEventManager()->dispatch(new CakeEvent('Model.User.afterUpdated', $this));
    	        }
    	        if(isset($this->data[$this->name]['is_verified'])) {
    	            $this->getEventManager()->dispatch(new CakeEvent('Model.User.afterUpdate.Verified', $this));
    	        }
    	        if(isset($this->data[$this->name]['recommend'])) {
    	            $this->getEventManager()->dispatch(new CakeEvent('Model.User.afterUpdate.Recommend', $this));
    	        }
    	        if(isset($this->data[$this->name]['recommend_offset'])) {
    	            $this->getEventManager()->dispatch(new CakeEvent('Model.User.afterUpdate.RecommendOffset', $this));
    	        }
    	    }
	    }
	}
	
/**
 * (non-PHPdoc)
 * @see Model::implementedEvents()
 */
	public function implementedEvents() {
		$callbacks = parent::implementedEvents();
		return array_merge($callbacks, array(
			'Model.Voice.afterUpdated'                 => 'onVoiceUpdate',
			'Model.Voice.afterCreated'                 => 'onVoiceUpdate',
			'Model.Voice.afterDeleted.approve'         => 'onVoiceUpdate',
		    'Model.Receipt.afterPaid'                  => 'payment',
		    'Model.Receipt.afterCreated.coupon'        => 'payment',
		    'Model.Checkout.afterCreated.dailySignIn'  => 'payment',
		    'Model.Checkout.afterCreated.voice_cost'   => 'purchaseTotal',
		    'Model.Checkout.afterCreated.voice_income' => 'voiceIncomeTotal'
		));
	}
	
/**
 * Handle event when user's voice has been updated or created.
 * 
 * @param CakeEvent $event
 */
	public function onVoiceUpdate(CakeEvent $event) {
	    $this->voiceTotal($event);
	    $this->voiceLengthTotal($event);
	}
	
/**
 * Update purchase total when user bought voice.
 *
 * @param CakeEvent $event
 * @return boolean
 */
	public function voiceLengthTotal(CakeEvent $event) {
	    APP::uses('Voice', 'Model');
	    $model = $event->subject();
	    $data = $model->data[$model->name];
	    
		$result = $this->updateAll(array(
		    '$set' => array('voice_length_total' => 
		        $model->getLengthCount($data['user_id'], Voice::STATUS_APPROVED))
		), array(
			'_id' => new MongoId($data['user_id'])
		));
		if(!$result) $this->failEvent($event);
	}
	
/**
 * Update purchase total when user bought voice.
 *
 * @param CakeEvent $event
 * @return boolean
 */
	public function purchaseTotal(CakeEvent $event) {
	    $model = $event->subject();
	    $data = $model->data[$model->name];
	    
		$result = $this->updateAll(array(
		    '$set' => array('purchase_total' => $model->getPurchaseCount($data['user_id']))
		), array(
			'_id' => new MongoId($data['user_id'])
		));
		if(!$result) $this->failEvent($event);
	}
	
/**
 * Update voice income total when user bought voice.
 *
 * @param CakeEvent $event
 * @return boolean
 */
	public function voiceIncomeTotal(CakeEvent $event) {
	    $model = $event->subject();
	    $data = $model->data[$model->name];
	    
		$result = $this->updateAll(array(
		    '$inc' => array('voice_income_total' => 1)
		), array(
			'_id' => new MongoId($data['user_id'])
		));
		if(!$result) $this->failEvent($event);
	}
	
/**
 * The voice total plus 1 when new voice is approved by admin
 *
 * @param CakeEvent $event
 * @return boolean
 */
	public function voiceTotal(CakeEvent $event) {
	    APP::uses('Voice', 'Model');
	    $model = $event->subject();
	    $data = $model->data[$model->name];
	    
		$result = $this->updateAll(array(
			'voice_total' => $model->count($data['user_id'], Voice::STATUS_APPROVED)
		), array(
			'_id' => new MongoId($data['user_id'])
		));
		if(!$result) $this->failEvent($event);
	}
	
/**
 * Login
 *
 * @param string $authorize
 * @param string $password
 * @return Array|boolean
 */
	public function login($authorize, $password) {
	    $password = $this->encrypt($password);
		if (Validation::email($authorize)) {
			$conditions = array(
				'email' => $authorize,
				'password' => $password
			);
		} else {
			$conditions = array(
				'username' => $authorize,
				'password' => $password
			);
		}
	
		return $this->find('first', array(
			'conditions' => $conditions
		));
	}
	
/**
 * Login by certified
 * 
 * @param string $certified The name of certified
 * @param string $openId The unique id got from third-party
 * @return Ambigous <multitype:, NULL, mixed>
 */
	public function loginByCertified($certified, $openId) {
	    return $this->find('first', array(
	        'conditions' => array(
	            "certified.$certified.open_id" => (string)$openId
	        )
	    ));
	}
	
/**
 * Validate whether `password` is right by specified user
 * 
 * @param string $userId
 * @param string $password The flat password word
 * @return Ambigous <multitype:, NULL, mixed>
 */
	public function validPassword($userId, $password) {
	    return $this->find('count', array(
	        'conditions' => array(
	            '_id' => new MongoId($userId),
	            'password' => $this->encrypt($password)
	        )
	    ));
	}
	
/**
 * Create an account for user.
 * 
 * @param array $data
 * @return mixed It returns saved data if success or returns false.
 */
	public function register(array $data) {
        if($this->create($data) && TRUE == ($saved = $this->save())) {
            unset($saved[$this->name]['password']);
            return $saved;
        }
	    return false;
	}
	
/**
 * Check username whether has existed or not.
 * 
 * @param array $fields
 * @param string $or
 * @return boolean
 */
	public function isUserNameUnique($fields, $or = true) {
		$conditions = array();
		foreach($fields as $field => $value) {
			$conditions[$field] = $value;
		}
		if(TRUE == ($id = $this->isUpdate())) {
		    $conditions = array_merge($conditions, 
		            array('_id' => array('$ne' => new MongoId($id))));
		}
		$exist = $this->find('count', array(
			'conditions' => $conditions,
		));
		return $exist > 0 ? false : true;
	}
	
/**
 * Get user by id.
 * 
 * @param string $userId
 * @return mixed return user data array or empty array if found nothing.
 */
	public function getById($userId) {
	    $user = $this->find('first', array(
	        'fields' => array('password' => 0),
	    	'conditions' => array(
	    	    '_id' => $userId
	    	)
	    ));
	    return $user;
	}
	
/**
 * Buy something
 * 
 * @param string $userId
 * @param int $price
 * @return boolean
 */
	public function cost($userId, $price) {
	    if(!$userId) return false;
	    $price = (int) $price;
	    if($price <= 0) return false;
// 	    $price = abs(intval($price));
	    // The $user variable is snapshot before modify...
	    $user = $this->find('first', array(
    		'modify' => array('$inc' => array(
				'money' => -$price,
				'cost' => $price
    		)),
    		'conditions' => array(
    		    '_id' => new MongoId($userId),
				'money' => array('$gte' => $price),
    		)
	    ));
	    if(isset($user['User']) && isset($user['User']['money']) && isset($user['User']['earn'])) {
	        // Do `money` modify again, because the $user varaible is snapshot before modified.
	        $user['User']['money'] -= $price;
	        /*
	         * If `money` is less than `earn`, the `earn` has to equals `money`
	         * Because `money` = `earn` + other(e.g. `payment`)
	         */
	        if($user['User']['money'] < $user['User']['earn']) {
	            return $this->updateAll(array(
	                'earn' => $user['User']['money']
	            ), array(
	                '_id' => new MongoId($userId)
	            ));
	        }
	    }
	    return isset($user['User']) && $user['User'] != null;
	}
	
/**
 * Buy something
 * 
 * @param string $userId
 * @param int $price
 * @return boolean
 */
	public function withdraw($userId, $price) {
	    if(!$userId) return false;
	    $price = abs(intval($price));
	    // The $user variable is snapshot before modify...
	    $user = $this->find('first', array(
    		'modify' => array('$inc' => array(
				'money' => -$price,
				'earn' => -$price,
				'cost' => $price
    		)),
    		'conditions' => array(
    		    '_id' => new MongoId($userId),
				'earn' => array('$gte' => $price),
    		)
	    ));
	    return isset($user['User']) && $user['User'] != null;
	}
	
/**
 * Earn money!
 * 
 * @param string $userId
 * @param int $price
 * @return boolean
 */
	public function earn($userId, $price) {
	    if(!$userId) return false;
	    $price = abs(intval($price));
	    return $this->updateAll(array(
    		'$inc' => array(
    			'money' => $price,
    			'earn' => $price,
    		    'income' => $price
    		)), array(
    			'_id' => new MongoId($userId)
    		)
	    );
	}
	
/**
 * Reverse withdraw!
 * 
 * @param string $userId
 * @param int $price
 * @return boolean
 */
	public function withdrawRevert($userId, $price) {
	    if(!$userId) return false;
	    $price = abs(intval($price));
	    return $this->updateAll(array(
    		'$inc' => array(
    			'money' => $price,
    			'earn' => $price,
    		    'income' => $price,
    		)), array(
    			'_id' => new MongoId($userId)
    		)
	    );
	}
	
/**
 * Payment!
 * 
 * @param string $userId
 * @param int $seconds
 * @return boolean
 */
	public function payment(CakeEvent $event) {
	    $model = $event->subject();
	    $data = $model->data[$model->name];
	    
	    $userId  = $data['user_id'];
	    $seconds = $data['amount']['time'];
	    
	    if(!$userId) return false;
	    $seconds = abs(intval($seconds));
	    
	    $result = $this->updateAll(array(
    		'$inc' => array(
    			'money' => $seconds,
    		    'income' => $seconds
    		)), array(
    			'_id' => new MongoId($userId)
    		)
	    );
	    if(!$result) $this->failEvent($event);
	}
	
/**
 * Transfer money!
 * 
 * @param string $userId
 * @param int $price
 * @return boolean
 */
	public function transfer($userId, $price) {
	    if(!$userId) return false;
	    $price = abs(intval($price));
	    return $this->updateAll(array(
    		'$inc' => array(
    			'money' => $price,
    		    'income' => $price
    		)), array(
    			'_id' => new MongoId($userId)
    		)
	    );
	}
	
/**
 * Gift!
 * 
 * @param string $userId
 * @param int $seconds
 * @param string $deviceCode
 * @return boolean
 */
	public function gift($userId, $seconds, $deviceCode = '') {
	    if(!$userId) return false;
	    $seconds = abs(intval($seconds));
	    $result = false;
	    if($deviceCode) {
    	    $result = $this->updateAll(array(
    	        '$set' => array('gift' => array('register' => array('device_code' => $deviceCode))),
        		'$inc' => array(
        			'money' => $seconds,
        		    'income' => $seconds
        		)), array(
        			'_id' => new MongoId($userId)
        		)
    	    );
	    } else {
	        $result = $this->updateAll(array(
        		'$inc' => array(
    				'money' => $seconds,
    				'income' => $seconds
        		)), array(
        			'_id' => new MongoId($userId)
        		)
	        );
	    }

	    return $result;
	}
	
/**
 * Check whether there is uploaded file as avatar image or not.
 *
 * @param array $check
 * @return boolean
 */
	public function chkAvatar($check) {
	    foreach($check as $key => $val) 
			$this->data[$this->name]['avatar'] = array(
			    'source' => $val,
			    'x80'    => $val,
			    'x180'   => $val
			);
	    return true;
	}
	
/**
 * Reformat value of `role`.
 *
 * @param array|boolean|int $check
 * @return boolean
 */
	public function chkRole($check) {
		$roles = explode('|', $this->getCheck('role', $check));
		foreach($roles as &$role) {
			$role = strtolower($role);
			if(!\Controller\Setting\Permission::validate($role)) return false;
		}
		$this->data[$this->name]['role'] = implode('|', $roles);
		return true;
	}
	
/**
 * Reformat value of `device_code`.
 *
 * @param array|boolean|int $check
 * @return boolean
 */
	public function chkDeviceCode($check) {
		$code = $this->getCheck('device_code', $check);
		if(!$code) {
			return false;
		}
		
		return true;
	}
	
/**
 * Check whether `locale` is valid or not
 *  
 * @param array|string $check
 * @return boolean
 */
	public function chkLocale($check) {
		return in_array($this->getCheck('locale', $check), Configure::read('Locale.Supported'));
	}
	
/**
 * Check whether `username` is valid or not
 *
 * @param array|string $check
 * @return boolean
 */
	public function chkUsername($check) {
		$item = $this->getCheck('username', $check);
		
		if(!$item) {
			return false;
		}
		
		$len = mb_strlen($item, 'UTF-8');
		if($len < self::USERNAME_MIN_LENGTH || $len > self::USERNAME_MAX_LENGTH) {
			return false;
		}
		
		return true;
	}
	
/**
 * Check whether `password` is valid or not
 *
 * @param array|string $check
 * @return boolean
 */
	public function chkPassword($check) {
		$item = $this->getCheck('password', $check);
		
		if(!$item) {
			return false;
		}
		
		$len = strlen($item);
		if($len < self::PASSWORD_MIN_LENGTH || $len > self::PASSWORD_MAX_LENGTH) {
			return false;
		}
		
// 		if($this->isUpdate()) {
// 		    if(!isset($this->data[$this->name]['old_password']))
// 		        $this->data[$this->name]['old_password'] = '';
// 		}
		
		return true;
	}
	
/**
 * Check whether certified data is valid or not
 * 
 * @param array|string $check
 * @return boolean
 */
	public function chkCertified($check) {
		$name = $this->getCheck('certified', $check);
		if($name && is_string($name)) {
			$name = strtolower($name);
			switch($name) {
				case self::NAME_SINA_WEIBO:
				case self::NAME_QZONE:
				case self::NAME_FACEBOOK:
				case self::NAME_TWITTER:
					return true;
			}
		}
		return false;
	}

/**
 * Check whether the certified has existed or not
 * 
 * @param string $name The name of certification
 * @param string $openId
 * @return boolean
 */
	public function chkUniqueCertified($check) {
		$name = $this->getCheck('certified', $check);
		$openId = $this->gets('open_id', $this->data[$this->name]);
		
		if($name && $openId) {
			$conditions = array(
				"certified.$name.open_id" => $openId
			);
			if(TRUE == ($id = $this->isUpdate())) {
				$conditions['_id'] = array('$ne' => new MongoId($id));
			}
			return $this->find('count', array(
				'conditions' => $conditions
			)) == 0;
		}
		
		return true;
	}

/**
 * Format certified data
 * 
 * @return boolean It always be TRUE.
 */
	public function chkCertifiedFormat($check) {
		$name = $this->getCheck('certified', $check);
		$openId = $this->gets('open_id', $this->data[$this->name]);
		
		if($name) {
			if(TRUE == ($id = $this->isUpdate())) {
				$user = $this->findById($id)[$this->name];
				if(isset($user['certified'])) {
					$this->data[$this->name]['certified'] =
						am($user['certified'], $this->certifiedItem($name, $openId));
				} else {
					$this->data[$this->name]['certified'] = $this->certifiedItem($name, $openId);
				}
			} else {
				$this->data[$this->name]['certified'] = $this->certifiedItem($name, $openId);
			}
		}
		// unset `open_id`, no need to save it...
		if(isset($this->data[$this->name]['open_id'])) {
			unset($this->data[$this->name]['open_id']);
		}
		
		return true;
	}
	
/**
 * Reformat value of `is_contributor`.
 *
 * @param array|double $check
 * @return boolean
 */
	public function chkIsContributor($check) {
		foreach($check as $key => $val) 
		    $this->data[$this->name]['is_contributor'] = (int)(bool) $val;
		return true;
	}
	
/**
 * Reformat value of `is_verified`.
 *
 * @param array|double $check
 * @return boolean
 */
	public function chkIsVerified($check) {
		foreach($check as $key => $val) 
		    $this->data[$this->name]['is_verified'] = (int)(bool) $val;
		return true;
	}
	
/**
 * Reformat value of `recommend`.
 *
 * @param array|double $check
 * @return boolean
 */
	public function chkRecommend($check) {
	    if(!isset($this->user[$this->name]['is_contributor'])) return false;
	    if(!$this->user[$this->name]['is_contributor']) return false;
		foreach($check as $key => $val) 
		    $this->data[$this->name]['recommend'] = (int)(bool) $val;
		return true;
	}
		
/**
 * Check whether there is uploaded file as cover image or not.
 *
 * @param array $check
 * @return boolean
 */
	public function chkCover($check) {
        foreach($check as $key => $val) 
		    $this->data[$this->name]['cover'] = array(
		        'source' => $val,
		        'x80'    => $val,
		        'x160'   => $val,
		        'x640'   => $val
		    );
		return true;
	}
	
	public function chkSamePwd($check) {
	    if(!$this->id) return false;
	    foreach($check as $key => $val) {
	        return $this->find('count', array(
        		'conditions' => array(
    				'_id' => new MongoId($this->id),
    				'password' => $this->encrypt($val)
        		)
	        )) > 0;
	    }
	    return false;
	}
	
	public function chkEmailUpdate($check) {
	    if($this->isUpdate()) {
	        if(isset($this->user[$this->name]['password']) && $this->user[$this->name]['password']) {
    	        return isset($this->data[$this->name]['password'])
    	        && ($this->user[$this->name]['password'] == $this->encrypt($this->data[$this->name]['password']));
	        }
	    }
	    return true;
	}
	
	public function isVerified($userId) {
	    return $this->find('count', array(
	        'conditions' => array(
	            '_id' => new MongoId($userId),
	            'is_verified' => 1
	        )
	    )) > 0;
	}
	
/**
 * Check whether or not creating an account of certified
 * 
 * @return boolean
 */
	protected function isCreateByCertified() {
		return isset($this->data[$this->name]['certified']) && isset($this->data[$this->name]['open_id']);
	}
	
/**
 * Common encryption method
 * 
 * @param string $plain
 * @return string
 */
	protected function encrypt($plain) {
	    return Security::hash($plain, 'md5');
	}
	
/**
 * Consist structure of certified with name and open id
 * 
 * @param string $name The name of certification
 * @param string $openId
 * @return multitype:multitype:unknown
 */
	protected function certifiedItem($name, $openId) {
		return array(
			$name => array(
				'open_id' => $openId
			)
		);
	}
	
	protected function getRegisterAward() {
	    return (!$this->deviceCode->isExist($this->data[$this->name]['device_code'])) ? 
	        Configure::read('Register.Award') : 0;
	}
}