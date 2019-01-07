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
class Withdrawal extends AppModel {
    
	public $primaryKey = '_id';
	public $useTable = 'checkouts';
	
	public $name = 'Withdrawal';
	
	const TYPE = Checkout::TYPE_DRAW;
	
	const NOT_PROCESSED_YET = 1000;
	const PROCESSED = 1001;
	const REVERTED = 1002;
	
	public $mongoSchema = array(
	    'user_id' => array('type' => 'string'), 
	    /**
	     * The type of checkout, detail definitions are in Checkout.php
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
	        /**
	         * The name of payment account
	         */
	        'account' => array('type' => 'string'),
	        /**
	         * The name of payment realname
	         */
	        'realname' => array('type' => 'string'),
	    ),    
	    /**
	     * The status of whether the withdrawal checkout is processed or not
	     * If not, processed = NOT_PROCESSED_YET,
	     * otherwise, processed = PROCESSED,
	     * the last status is checkout is revert already, so processed = REVERTED
	     */
	    'processed' => array('type' => 'integer'),
	    'created' => array('type' => 'datetime'),
	    'modified' => array('type' => 'datetime'),
	);
	
/**
 * Create a checkout for user to withdraw
 * 
 * @param string $user
 * @param int $second
 * @param Cash $cash
 * @param Fee $fee
 * @param string $account
 * @param string $realname
 * @return array|boolean
 */
	public function add($userId, $second, Cash $cash, Fee $fee, $account, $realname = '') {
	    $money = $cash->calc($second);
	    $cost = $fee->draw($money);
		if($this->create(array(
			'user_id' => $userId,
			'type' => self::TYPE,
			'amount' => array(
				'time' => $second,
			    'currency' => $cash->toString(),
			    'money' => $money - $cost,
			    'fee' => $cost,
			    'gateway' => $fee->gateway(),
			    'account' => $account,
			    'realname' => $realname,
			),
		    'processed' => self::NOT_PROCESSED_YET
		))) {
			return $this->save();
		}
		return false;
	}
	
/**
 * Just change status checkout of withdrawal to processed
 * 
 * @param string $coId
 * @param integer $processed
 * @return boolean
 */
	public function update($coId, $processed) {
	    switch($processed) {
	        case self::NOT_PROCESSED_YET:
	        case self::PROCESSED:
	        case self::REVERTED:
	            break;
	        default:
	            return false;
	    }
	    $result = $this->updateAll(
	            array('processed' => $processed), 
	            array('_id' => new MongoId($coId)));
	    
	    if($result) {
	    	$this->read(null, $coId);
	    	// Send notification...
	    	if($processed == self::PROCESSED) {
    	    	CakeResque::enqueue('notification', 'NotificationShell',
        	    	array('withdrawal', $this->data[$this->name])
    	    	);
	    	}
// 	    	$this->getEventManager()->dispatch(new CakeEvent(
// 	    			'Model.Withdrawal.afterUpdate.accept', $this));
	    }
	    return $result;
	}
}