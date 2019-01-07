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
APP::uses('CredentialItem', 'Item');
APP::uses('NullCredentialItem', 'Item');

class AuthorizeToken extends AppModel {
    
    public $useDbConfig = 'redis'; // Defined at app/Config/database.php
    
    public $name = 'AuthorizeToken';
    
/**
 * @var Redis
 */
    protected $redis;
    
/**
 * Get credential by the key
 * 
 * @var string
 */
    private $credentialKey = 'token:%s';
    
/**
 * Get token of specified user
 * 
 * @var string
 */
    private $tokenKey = 'user:%s:token';
    
    public function __construct($id = false, $table = null, $ds = null) {
        parent::__construct($id, $table, $ds);
        $this->redis = $this->getDataSource()->getInstance();
    }
    
/**
 * Add token to list
 * 
 * @param string $userId
 * @return string Token is generated for current user
 */
    public function add(CredentialItem $item) {
    	$token = $this->getExistToken($item->getUserId());
    	if(!$token) {
    		// Generate an new one if no found exist token for the user...
        	$token = $this->generateToken($item->getUserId());
    	}
    	
    	$this->saveCredential($token, $item);
    	
    	// Store relationship with userid and token
    	// Make sure only one token is assigned to each user
    	$this->saveToken($token, $item);
    	return $token;
    }
    
/**
 * Get credential
 *
 * @param string $token
 * @return CredentialItem
 */
    public function getCredential($token) {
    	$item = $this->redis->get(sprintf($this->credentialKey, $token));
    	if($item) {
    		return @unserialize($item);
    	}
    	return new NullCredentialItem();
    }
    
/**
 * Remove credential
 *
 * @param string $token
 * @return void
 */
    public function remove($userId) {
    	$token = $this->getExistToken($userId);
    	if($token) {
    		$this->redis->delete(sprintf($this->credentialKey, $token));
    		$this->redis->delete(sprintf($this->tokenKey, $userId));
    	}
    }
    
/**
 * Try to check whether credential has existed or not
 * 
 * @param CredentialItem $item
 */
    private function getExistToken($userId) {
    	return $this->redis->get(sprintf($this->tokenKey, $userId));
    }
    
/**
 * Save relationship with token and credential
 * 
 * @param string $token
 * @param CredentialItem $item
 */
    private function saveCredential($token, CredentialItem $item) {
    	$key = sprintf($this->credentialKey, $token);
    	$this->redis->set($key, serialize($item));
    	$this->redis->setTimeout($key, Configure::read('Expire.AuthToken'));
    }
    
/**
 * Save relationship with token and userId
 * 
 * @param string $token
 * @param CredentialItem $item
 */
    private function saveToken($token, CredentialItem $item) {
    	$this->redis->set(sprintf($this->tokenKey, $item->getUserId()), $token);
    }
    
/**
 * Generate a auth token string via md5.
 *
 * @param string $userId
 * @return string token
 */
    private function generateToken($userId) {
    	$seed = Configure::read('Security.cipherSeed');
    	mt_srand($seed);
    	$timestamp = time();
    	return md5(mt_rand(10000000, 99999999).uniqid('auth').$timestamp);
    }
}