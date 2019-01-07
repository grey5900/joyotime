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
 * The model of Checkout
 *
 * @package		app.Model
 */
class Broadcast extends AppModel {
	
	public $primaryKey = '_id';
	
	public $name = 'Broadcast';
	
/**
 * It's limitation number for broadcasting
 *
 * @var int
 */
	const BROADCAST_COUNTER = 30;
	
	const TYPE_GIFT = 'type_gift_broadcast';
	const TYPE_MESSAGE = 'type_message_broadcast';
	
	public $mongoSchema = array(
		'user_id' => array('type' => 'string'),
		'type' => array('type' => 'string'),
		/**
		 * A list of user who has read it already
         */
		'readers' => array('type' => 'array'),
		'read_total' => array('type' => 'integer'),
		'amount' => array(
			'time' => array('type' => 'integer')
		),
	    'link' => array('type' => 'string'),
		'message' => array('type' => 'string'),
		'created' => array('type' => 'datetime'),
		'modified' => array('type' => 'datetime')
	);
	
	public function __construct($id = false, $table = null, $ds = null) {
		parent::__construct($id, $table, $ds);
		 
		$this->validate = array(
			'amount' => array(
				'required' => array(
					'rule' => array('chkAmount'),
					'allowEmpty' => false,
					'message' => __('Invalid amount')
				),
			),
			'message' => array(
				'required' => array(
					'rule' => array('notEmpty'),
					'required' => 'create',
					'allowEmpty' => false,
					'message' => __('Invalid message')
				),
			),
			'type' => array(
				'required' => array(
					'rule' => 'notEmpty',
					'required' => 'create',
					'allowEmpty' => false,
					'message' => __('Invalid type')
				)
			)
		);
	}
	
/**
 * (non-PHPdoc)
 * @see Model::beforeValidate()
 */
	public function beforeValidate($options = array()) {
	    if(!$this->isUpdate()) {
    		if(isset($this->data[$this->name]['seconds'])) {
    			$this->data[$this->name]['amount']['time'] = $this->data[$this->name]['seconds'];
    			$this->data[$this->name]['type'] = self::TYPE_GIFT;
    			$this->validate['amount']['required']['required'] = true;
    		} else {
    		    $this->data[$this->name]['type'] = self::TYPE_MESSAGE;
    		}
    		
    		// Init readers list while creating...
    		if(!isset($this->data[$this->name]['readers'])) {
    			$this->data[$this->name]['readers'] = array();
    		}
    	
    		// Initial when create new one...
    	    $this->data[$this->name]['read_total'] = 0;
	    }
	}
	
/**
 * (non-PHPdoc)
 * @see Model::afterSave()
 */
	public function afterSave($created, $options = array()) {
		if($created) {
		    if(isset($this->data[$this->name]['user_id'])) {
		    	if($this->data[$this->name]['type'] == self::TYPE_GIFT) {
		    	    // Send notification...
		    	    CakeResque::enqueue('notification', 'NotificationShell',
		    	        array('gift', $this->data[$this->name])
		    	    );
		    	} else {
		    	    // Send notification...
		    	    CakeResque::enqueue('notification', 'NotificationShell',
		    	        array('broadcast', $this->data[$this->name])
		    	    );
		    	}
		    } else {
		        if($this->data[$this->name]['type'] == self::TYPE_GIFT) {
		            // Send broadcasting...
		            CakeResque::enqueue('broadcasting', 'BroadcastShell',
		                array('gift', $this->data[$this->name])
		            );
		        } else {
		            // Send broadcasting...
		            CakeResque::enqueue('broadcasting', 'BroadcastShell',
		                array('send', $this->data[$this->name])
		            );
		        }
		    }
			
		}
	}
}