<?php
/**
 * Application level Controller
 *
 * This file is application-wide controller file. You can put all
 * application-wide controller-related methods here.
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Controller
 * @since         CakePHP(tm) v 0.2.9
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
App::uses('Controller', 'Controller');

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @package		app.Controller
 * @link		http://book.cakephp.org/2.0/en/controllers.html#the-app-controller
 */
class AppController extends Controller {
    
    public $components = array(
    	/**
    	 * The handler specify to handle views for RESTful API
    	 */
    	'RequestHandler',
        /**
         * To authorize/generate token.
         */
        'OAuth',
        /**
         * The calculator of price
         */
        'Price',
        /**
         * The helper class to patch/extend info according to field
         */
        'Patch',
        'QiNiu'
    );
    
/**
 * (non-PHPdoc)
 * @see Controller::beforeFilter()
 */
    public function beforeFilter() {
        // Try to parse raw data that content type is multipart/form-data...
        if(stristr($this->request->header('Content-Type'), 'multipart/form-data') 
            && $this->request->is('PUT')) {
            $this->request->data = $this->request->input('parseRawHttpRequest');
        }
        
        // set language...
        $language = $this->request->query('language');
        Configure::write('Config.language', $language);
    }
    
/**
 * A response message returns to client while failing.
 * 
 * @param integer $code
 * @param string $message
 * @return boolean
 */
    public function fail($code, $message = array()) {
        if(!$message) {
            $codes = $this->response->httpCodes($code);
            if(is_array($codes)) {
                $message = $codes[$code];
            }
        }
        $this->response->statusCode($code);
        $result = array('code' => $code, 'message' => $message);
        $this->set(array(
    		'result' => $result,
    		'_serialize' => 'result'
        ));
        return false;
    }
    
/**
 * A response message returns to client while successing.
 * 
 * @param number $code The http status code
 * @param array $headers
 * @param array $data
 * @return boolean Always return true
 */
    public function success($code = 200, $headers = array(), $data = array()) {
        $this->response->statusCode($code);
        $this->response->header($headers);
        $this->set(array(
    		'root' => array('result' => $data),
    		'_serialize' => 'root'
        ));
        return true;
    }
    
/**
 * Return formatted results.
 * 
 * @param array $items
 * @param number $total
 * @return boolean
 */
    public function results(array $items = array(), $total = 0) {
        $data = array(
            'total' => $total,
            'items' => $items
        );
        $this->set(array(
    		'root' => array('result' => $data),
    		'_serialize' => 'root'
        ));
        return true;
    }
    
/**
 * Return an one-dimession array as result
 * 
 * @param mixed $result supported array or string
 * @return boolean
 */
    public function result($data = array()) {
        $this->set(array(
            'root' => array('result' => $data),
    		'_serialize' => 'root'
        ));
        return true;
    }
    
/**
 * Generate http header for all responses
 * 
 * @param boolean $isPublic
 * @param int $expire
 * @return void
 */
    protected function httpCache($isPublic, $expire) {
    	if($isPublic) {
    		$since = time();
    		$this->response->cache($since, $expire);
    	} else {
    		//It's private cache
    		$since = time();
    		if (!is_int($expire)) {
    			$expire = strtotime($expire);
    		}
    		$this->response->header(array(
    			'Date' => gmdate("D, j M Y G:i:s ", $since) . 'GMT'
    		));
    		$this->response->modified($since);
    		$this->response->expires($expire);
    		$this->response->sharable(false);
    	}
    }
    
    public function getUserAgent() {
		$userAgent = $this->request->query('user_agent');
		if(!$userAgent) {
			$userAgent = $this->request->header('User-Agent');
		}
    	return $userAgent;
    }
    
/**
 * Is valid mongo id?
 * 
 * @todo needs move it to model as one of validation rule.
 * @deprecated
 * 
 * @param string $id
 * @return boolean
 */
    public function isMongoId($id = '') {
        if(!$id) return false;
        return (bool) preg_match('/[0-9a-f]{24}/i', $id);
    }
    
/**
 * Add slashes if there is no found slashe at the first character.
 * 
 * @param string $variable
 * @return string
 */
    protected function addSlashes($variable) {
        if(substr($variable, 0, 1) == '/') {
            return $variable;
        }
        return '/'.$variable;
    }
       
/**
 * Get first error message after validated.
 *
 * @param Model $model
 * @return array Message content list
 */
    protected function errorMsg(Model $model) {
    	$errors = isset($model->validationErrors) ? $model->validationErrors : array();
    	$messages = array();
    	if($errors) {
    		foreach($errors as $type => $error) {
    			if(is_array($error) && !isset($error[0])) {
    				foreach($error as $field => $err) {
    					if(!isset($err[0])) continue;
    					$messages[] = $err[0];
    				}
    			} else {
    				$messages[] = $error[0];
    			}
    		}
    	}
    	return empty($messages) ? '' : $messages[0];
    }
}

if(!function_exists('parseRawHttpRequest')) {
    
    /**
     * Parse raw http request
     *
     * @param string $input The http raw content by PUT
     * @return array
     */
    function parseRawHttpRequest($input) {
        $data = array();
        
        // grab multipart boundary from content type header
        preg_match('/boundary=(.*)$/', $_SERVER['CONTENT_TYPE'], $matches);
        $boundary = $matches[1];
        
        // split content by boundary and get rid of last -- element
        $a_blocks = preg_split("/-+$boundary/", $input);
        array_pop($a_blocks);
        
        // loop data blocks
        foreach($a_blocks as $id => $block) {
            if(empty($block))
                continue;
                
            // you'll have to var_dump $block to understand this and maybe replace \n or \r with a visibile char
            // parse uploaded files
            if(strpos($block, 'application/octet-stream') !== FALSE) {
                // match "name", then everything after "stream" (optional) except for prepending newlines
                preg_match("/name=\"([^\"]*)\".*stream[\n|\r]+([^\n\r].*)?$/s", $block, $matches);
            }             // parse all other fields
            else {
                // match "name" and optional value in between newline sequences
                preg_match('/name=\"([^\"]*)\"[\n|\r]+([^\n\r].*)?\r$/s', $block, $matches);
            }
            $data[$matches[1]] = $matches[2];
        }
        return $data;
    }
}