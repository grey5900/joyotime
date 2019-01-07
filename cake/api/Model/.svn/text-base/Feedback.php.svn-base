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

/**
 * @package		app.Model
 */
class Feedback extends AppModel {
	
	public $useDbConfig = 'redis'; // Defined at app/Config/database.php
	
	public $name = 'Feedback';
	
	private $keyQueue = 'feedbacks';
	
/**
 * @var Redis
 */
	protected $redis;
	
	const STATUS_PENDING = 1000;
	const STATUS_DONE = 1001;
	
	public $mongoSchema = array(
		'user_id' => array('type'=>'string'),
		'username' => array('type'=>'string'),
		'email' => array('type'=>'string'),
		'content' => array('type'=>'string'),
		'contact' => array('type'=>'string'),
		'user_agent' => array('type'=>'string')
	);
	
	public function __construct($id = false, $table = null, $ds = null) {
	    parent::__construct($id, $table, $ds);
	    $this->redis = $this->getDataSource()->getInstance();
	    $this->initValidates();
	}
	
/**
 * (non-PHPdoc)
 * @see Model::beforeValidate()
 */
	public function beforeValidate($options = array()) {
	    if(isset($this->data[$this->name]['status'])) {
	    	$this->data[$this->name]['status'] = (int) $this->data[$this->name]['status'];
	    }
	    // It's creating...
	    if(!$this->isUpdate()) {
	        if(!isset($this->data[$this->name]['user_id'])) {
	        	$this->data[$this->name]['user_id'] = '';
	        }
	        if(!isset($this->data[$this->name]['email'])) {
	        	$this->data[$this->name]['email'] = '';
	        }
	        if(!isset($this->data[$this->name]['user_agent'])) {
	        	$this->data[$this->name]['user_agent'] = 'Unknown';
	        }
	    }
	}
	
/**
 * (non-PHPdoc)
 * @see AppModel::initValidates()
 */
	public function initValidates() {
		$this->validate = array(
			'user_id' => array(
        		'required' => array(
    				'rule' => array('isMongoId'),
        		    'allowEmpty' => true,
    				'message' => __('Invalid user id')
        		),
	        ),
			'email' => array(
        		'required' => array(
    				'rule' => array('email'),
        		    'allowEmpty' => true,
    				'message' => __('Invalid email')
        		),
	        ),
			'user_agent' => array(
        		'required' => array(
    				'rule' => array('notEmpty'),
        		    'allowEmpty' => true,
    				'message' => __('Invalid email')
        		),
	        ),
			'content' => array(
        		'required' => array(
    				'rule' => 'notEmpty',
    				'required' => 'create',
    				'allowEmpty' => false,
    				'message' => __('Invalid content')
        		),
	        )
		);
	}
	
/**
 * Push an item into queue
 *
 * @param Array $save
 * @return boolean
 */
	public function enqueue($save) {
		return $this->create($save) 
		    && $this->validates() 
		    && $this->redis->lPush($this->keyQueue, json_encode($save));
	}
	
/**
 * Pop an Array from queue
 * 
 * @return mixed|boolean
 */
	public function dequeue() {
	    $jsonStr = $this->redis->rPop($this->keyQueue);
	    if($jsonStr) {
	        return json_decode($jsonStr, true);
	    }
	    return false;
	}
}