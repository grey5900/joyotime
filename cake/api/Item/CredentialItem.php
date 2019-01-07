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
APP::uses('AppItem', 'Item');
APP::uses('User', 'Model');

use \Controller\Setting\Permission;

class CredentialItem extends AppItem {

    private $userId = '';
    
    private $role = array();
    
/**
 * @param array $user No wrapper
 */
    public function __construct(array $user = array()) {
    	parent::__construct();
    	$this->setUserId($user['_id']);
    	$this->setRole($user['role']);
    }
    
/**
 * @return the $role
 */
	public function getRole() {
		if(is_string($this->role)) return explode('|', $this->role);
		return $this->role;
	}

/**
 * @param string $role
 */
	public function setRole($role) {
		$this->role = explode('|', $role);
	}

/**
 * @return the $userId
 */
	public function getUserId() {
		return $this->userId;
	}

/**
 * @param string $userId
 */
	public function setUserId($userId) {
		$this->userId = $userId;
	}

/**
 * (non-PHPdoc)
 * @see AppItem::toArray()
 */
    public function toArray() {
    	return array(
    		'user_id' => $this->userId,
    		'role' => $this->role
    	);
    }
    
/**
 * Is admin?
 * 
 * @return boolean
 */
    public function isAdmin() {
    	return in_array(Permission::ROLE_ADMIN, $this->getRole()) 
    	    || in_array(Permission::ROLE_CHECKER, $this->getRole());
    }
    
/**
 * Is same person?
 * 
 * @param string $userId
 * @return boolean true
 */
    public function isSame($userId) {
        return $this->userId == $userId;
    }
}