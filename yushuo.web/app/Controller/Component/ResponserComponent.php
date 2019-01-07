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
App::uses('UsersController', 'Controller');
/**
 * The wrapper class for handle all fish saying api service.
 *
 * @package		app.Controller.Component
 */
class ResponserComponent extends Component {
    
    private $resp;
/**
 * The http status code >= 300
 * 
 * @var boolean
 */
    private $isfail = false;
    
    /**
     * Parse response
     *
     * @param string $resp The JSON string as HTTP response body
     * @return boolean
     */
    public function parse($resp = '') {
        if(!$resp) {
            $this->isfail = true;
            return false;
        }
    	$this->resp = json_decode($resp, true);
    	if(isset($this->resp['code']) 
    	    && $this->resp['code'] != 200 
    	    && $this->resp['code'] != 201) {
    		switch($this->resp['code']) {
    			case 401:
    			    /*
    			     * For unauthenticate...
    			     */
    			    $this->_Collection->getController()->Session->setFlash(__('登录超时，请重新登录'));
    				return $this->getController()->logout();
    				break;
    		}
    		$this->isfail = true;
    		return false;
    	}
    	return true;
    }
    
    public function getData() {
        if(isset($this->resp['result'])) {
        	return $this->resp['result'];
        } 
    	return array();
    }
    
    public function getMessage() {
        if(isset($this->resp['message'])) {
        	return $this->resp['message'];
        } 
        return __('未知错误');
    }
    
    public function isFail() {
        return $this->isfail;
    }
    
    private function getController() {
    	$uc = new UsersController(new CakeRequest(), new CakeResponse());
    	$uc->constructClasses();
    	$uc->beforeFilter();
    	$uc->Cookie->startup($uc);
    	return $uc;
    }
}