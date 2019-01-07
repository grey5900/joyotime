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
APP::uses('Checkout', 'Model');
APP::uses('PriceComponent', 'Controller/Component');
/**
 * The model of Checkout
 *
 * @package		app.Model
 */
class ReverseWithdrawal extends AppModel {
	
	public $primaryKey = '_id';
	
	public $useTable = 'checkouts';
	
	public $name = 'ReverseWithdrawal';
	
	const TYPE = Checkout::TYPE_DRAW_REVERSE;
	
	public $mongoSchema = array(
	    'user_id' => array('type' => 'string'), 
	    /**
	     * the available values are 
	     * 'earn' and 'cost' and 'send' and 'receive'
	     */
	    'type' => array('type' => 'integer'),      
	    /**
	     * Transaction detail...
	     */
	    'amount' => array(
	        /**
	         * The unit is second.
	         */
	        'time' => array('type' => 'integer'),
	    ),    
	    /**
	     * The id of checkout is reverted
	     */
	    'reverted' => array('type' => 'string'),
	    /**
	     * That's why admin want to revert withdrawal
	     * Just only for self::TYPE_DRAW_REVERSE
	     */
	    'reason' => array('type' => 'string'),
	    'created' => array('type' => 'datetime'),
	    'modified' => array('type' => 'datetime'),
	);
	
/**
 * (non-PHPdoc)
 * @see Model::afterSave()
 */
	public function afterSave($created, $options = array()) {
		if($created && isset($this->data[$this->name]['_id'])) {
		    CakeResque::enqueue('notification', 'NotificationShell',
		        array('reverseWithdawal', $this->data[$this->name])
		    );
		}
	}
	
/**
 * Create checkout for revert withdrawal
 * 
 * @param string $userId
 * @param string $checkoutId
 * @param integer $second
 * @param string $reason
 * @return boolean
 */
	public function add($userId, $checkoutId, $second, $reason) {
	    if($this->create(array(
    		'user_id' => $userId,
    		'type' => self::TYPE,
    		'reverted' => $checkoutId,
    		'amount' => array(
				'time' => $second,
    		),
	        'reason' => $reason
	    ))) {
			return $this->save();
		}
		return false;
	}
	
/**
 * Did revert?
 * 
 * @param string $coId
 * @return boolean|string It will return id of checkout if found, otherwise false will be returned.
 */
	public function exist($coId) {
	    $row = $this->find('first', array(
	        'conditions' => array(
	            'reverted' => $coId
	        )
	    ));
	    if($row && isset($row['ReverseWithdrawal']['_id'])) {
	        return $row['ReverseWithdrawal']['_id'];
	    }
	    return false;
	}
}