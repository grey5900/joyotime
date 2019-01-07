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
App::uses('HttpSocket', 'Network/Http');
App::uses('UsersController', 'Controller');
App::uses('Withdrawal', 'Model');
/**
 * The wrapper class for handle all fish saying api service.
 *
 * @package		app.Controller.Component
 */
class ApiComponent extends Component {
    
/**
 * @var HttpSocket
 */
    private $client;
    
    public $domain;
    
    public $components = array('Responser');
    
    const AUTH = '/authenticates.json';
    const CONNECT = '/startups/connect.json';
    
    public function __construct(ComponentCollection $collection, $settings = array()) {
        parent::__construct($collection, $settings);
        $this->domain = Configure::read('Api.Domain');
    }
    
/**
 * Get api token
 * 
 * @throws CakeException
 * @throws SocketException
 * @return string The token string.
 */
    public function token() {
        $session = CakeSession::read('Api.Token');
        if(!$session) {
            $resp = $this->send(self::CONNECT, array(), 'get');
            
            if(!$resp->isFail()) {
            	$data = $resp->getData();
            	if(isset($data['result']) && isset($data['result']['uptoken'])) {
            		CakeSession::write('Api.Token', $data['result']);
            		$session = $data['result'];
            	} else {
            		throw new CakeException('Fails to get token from API server.');
            	}
            }
        }
        return $session;
    }
    
/**
 * Execute login action
 * 
 * @param string $username
 * @param string $password
 * @throws SocketException
 * @return boolean|array The user profile will be returned if logged in.
 */
    public function login($username, $password) {
        $data = array(
    		'authorize' => $username,
    		'password' => $password,
        );
        $resp = $this->send(self::AUTH, $data, 'put');
        if(!$resp->isFail()) {
        	$user = $resp->getData();
        	if(!isset($user['user']['role']) || $user['user']['role'] != 'admin') {
        	    return false;
        	}
        	CakeSession::write('auth_token', $user['auth_token']);
        	return $user['user'];
        }
        return false;
    }
    
/**
 * Get a secrect to retrieve token from server.
 *
 * @param int $now
 * @return string
 */
    public function geneSecrect($now) {
    	return md5(Configure::read('Api.PK').$now);
    }
    
/**
 * Get http client instance
 * 
 * @return HttpSocket
 */
    public function client() {
        if(!$this->client) {
            $this->client = new HttpSocket();
        }
        return $this->client;
    }
    
    public function request() {
        $token = CakeSession::read('auth_token');
        $request = array();
        
        if($token) {
        	$request['header'] = array(
        		'Authorization' => $token,
        	);
        }
    	return $request;
    }
    
/**
 * Generates and formats URL
 * 
 * @param string $url
 * @return string
 */
    public function url($url) {
        $now = time();
        $params = sprintf('api_key=%s&timestamp=%s', $this->geneSecrect($now), $now);
        if(stristr($url, '?')) {
            return $this->domain.$url.'&'.$params;
        } else {
            return $this->domain.$url.'?'.$params;
        }
    }
    
/**
 * Send request to api server
 * 
 * @param string $url
 * @param array $data
 * @param string $method
 * @return ResponserComponent
 */
    public function send($url, $data, $method) {
        $resp = $this->client()->$method(
        		$this->url($url),
        		$data,
        		$this->request());
        $this->Responser->parse($resp);
        return $this->Responser;
    }
}