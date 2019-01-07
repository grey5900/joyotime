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
App::uses('CakeTime', 'Utility');
App::uses('CredentialItem', 'Item');
App::uses('User', 'Model');

use \Controller\Setting\Permission;

/**
 * The class is used to authorize whether the request is illegal 
 * or not by informations in request header.
 * 
 * @package		app.Controller.Component
 */
class OAuthComponent extends Component {
    
/**
 * Allow methods without auth
 * 
 * @var array
 */
    private $allows = array();
    
    private $domains = array();
    
/**
 * @var Controller
 */
    private $controller;
    
/**
 * @var AuthorizeToken
 */
    private $AuthorizeToken;
    
/**
 * @var CredentialItem
 */
    private $credential;
    
    public function __construct(ComponentCollection $collection, $settings = array()) {
        parent::__construct($collection, $settings);
        $this->controller = $collection->getController();
        $this->AuthorizeToken = ClassRegistry::init('AuthorizeToken');
    }
    
/**
 * The method name which is not require to authorize.
 * 
 * @param string $method
 */
    public function allow($controller, $method) {
        $this->allows[$controller][$method] = 1;
    }
    
/**
 * Authorize whether the current request is illegal or not.
 * The auth string is consisted with pk/email/pwd in md5.
 * 
 * @return mixed return true or void redirected to 401 page.
 */
    public function startup(Controller $controller) {
        // Check whether the access token (akas api_key) is valid or not. 
        if(!$this->accessToken()) { 
            return $this->response(403, '', true);
        }
        
        // Skip check by specified actions... 
        if(isset($this->allows[$controller->name][$controller->action])) {
            if($this->allows[$controller->name][$controller->action] == 1) {
            	return true;
            }    
        }
        
        // Check whether current user has permission to access/write resource requested.
        $this->credential = $this->AuthorizeToken->getCredential($this->getToken());
        // token is invalid...
        if(!$this->credential || !$this->credential->getUserId()) {
            return $this->response(401, '', true);
        }
        
        if(isset($controller->request->params['uid'])) {
            // The token is valid, but the credential is not for the userId...
            if($this->credential->getUserId() != $controller->request->params['uid']) {
                return $this->response(403, '', true); 
            }
        }
        
        $perm = new Permission();
        
        if(!$perm->allow($this->credential->getRole(), $controller)) {
        	return $this->response(403, __('Permission denied'), true);
        }
        if($perm->forbid($this->credential->getRole(), $controller)) {
        	return $this->response(403, __('Permission denied'), true);
        }
        
        // Check admin permission by auth token....
        if(stristr($controller->request->params['action'], 'admin_')) {
        	// The token is valid, but the credential is not for admin...
        	if(!$this->credential->isAdmin()) {
        		return $this->response(403, '', true); 
        	}
        }
    }
    
/**
 * Get credential for current user when passed validation
 * 
 * @return CredentialItem
 */
    public function getCredential() {
        if($this->credential) {
            return $this->credential;
        }
        return $this->AuthorizeToken->getCredential($this->getToken());
    }
    

/**
 * Get authorize token from request
 *
 * @return Ambigous <mixed, boolean, unknown, NULL, array>
 */
    public function getToken() {
    	$token = '';
    	$token = $this->controller->request->header('Authorization');
    	if(!$token) {
    		$token = $this->controller->request->query('authorization');
    	}
    	return $token;
    }
    
/**
 * Clean credential
 * 
 * @param $userId
 * @return boolean
 */
    public function clean($userId) {
    	return $this->AuthorizeToken->remove($userId);
    }
    
/**
 * Send response to client side
 * 
 * @return boolean Always is false
 */
	private function response($code, $message = '', $exit = true) {
		$this->controller->response->statusCode($code);
		if(!$message) {
    		$codes = $this->controller->response->httpCodes($code);
    		if(is_array($codes)) $message = $codes[$code];
		}
		$this->controller->response->statusCode($code);
		$result = json_encode(array('code' => $code, 'message' => $message));
		$this->controller->response->body($result);
		$this->controller->response->send();
		if($exit) exit;
		return false;
	}

/**
 * Check whether the access token is valid or not.
 * 
 * @return boolean
 */
    private function accessToken() {
        $token = $this->controller->request->query('api_key');
        if(Configure::read('Enable.Debug.Access.Token')) {
        	if($token == Configure::read('Debug.Access.Token')) {
        	    return true;
        	}
        }
        
        // Whether the token come from payment server...
        if($token == Configure::read('Payment.Access.Token')) {
            return true;
        }
        
        $timestamp = $this->controller->request->query('timestamp');
        if($timestamp && $token && $timestamp + 300 > time()) {
            return md5(Configure::read('Secrect').$timestamp) == $token;
        }
    }

}