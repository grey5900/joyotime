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
/**
 * The model of Checkout
 *
 * @package		app.Model
 */
class Transfer extends AppModel {
	
	public $primaryKey = '_id';
	public $useTable = 'checkouts';
	
	const TYPE = Checkout::TYPE_TRANSFER;
	
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
	     * The user who was transfered money (transfer mode only)
	     */
	    'to' => array(
	        'user_id' => array('type' => 'string'),
	        'username' => array('type' => 'string')
	    ),
	    'created' => array('type' => 'datetime'),
	    'modified' => array('type' => 'datetime'),
	);
	
	public function beforeValidate($options = array()) {
	    
	}
	
/**
 * Create checkout for payee
 * 
 * @param array $payer
 * @param array $payee
 * @param int $seconds
 * @return array|boolean
 */
	public function add(array $payer, array $payee, $seconds) {
	    if($this->create(array(
	        'user_id' => (string) $payer['User']['_id'],
	        'type' => self::TYPE,
	        'amount' => array(
	            'time' => $seconds
	        ),
	        'to' => array(
	            'user_id' => (string) $payee['User']['_id'],
	            'username' => $payee['User']['username'],
	        )
	    ))) {
	        return $this->save();
	    }
	    return false;
	}
}