<?php
/**
 * The project of FenPay is a CRM platform based on Weixin MP API.
 *
 * Use it to communicates with Weixin MP.
 *
 * PHP 5
 *
 * FenPay(tm) : FenPay (http://fenpay.com)
 * Copyright (c) in.chengdu.cn. (http://in.chengdu.cn)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) in.chengdu.cn. (http://in.chengdu.cn)
 * @link          http://fenpay.com FenPay(tm) Project
 * @since         FenPay(tm) v 0.0.1
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

/**
 * The class is used to build specified headers for 
 * requesting API of InChengdu.
 * 
 * @package		app.Controller.Component
 */
class ApiHeaderComponent extends Component {
    
/**
 * Headers needs to send
 * @var array
 */
    private $headers = array();
    
/**
 * Set security information to header
 * @param string $url The request address.
 * @throws CakeException if the request address is invalid.
 */
    private function security($url) {
        $urls = parse_url($url);
        if(!isset($urls['path'])) {
            throw new CakeException("The URL is invalid: " . $url);
        }
        
        $unique = uniqid();
        $timestamp = number_format(microtime(true) * 1000, 0, '.', '');
        $keys = Configure::read('Api.key');
        $idx = array_rand($keys);
        
        $this->headers['X-Ogc'] = $unique;
        $this->headers['X-Timestamp'] = $timestamp;
        $this->headers['X-Orz'] = md5($urls['path'] . $unique . $keys[$idx] . $timestamp);
        $this->headers['X-Real-Url'] = $urls['path'];
    }
    
/**
 * Set header to send
 * 
 * @param string $name
 * @param string $value
 */
    public function setHeader($name, $value) {
    	$this->headers[$name] = $value;
    }
    
/**
 * Format pairs of key and value for each item of header.
 * @param string $url The request address.
 * @return array
 */
    public function toArray($url) {
    	$this->setToken();
    	$this->security($url);

    	return $this->headers;
    }
    
/**
 * Set auth information to header. 
 */
    private function setToken() {
        $uid = 0;
        if(isset($this->headers['uid'])) {
            $uid = $this->headers['uid'];
            if($uid) {
                $tokens = Configure::read('Api.token');
                $idx = array_rand($tokens);
                $this->headers['X-ATX'] = $idx;
                $this->headers['X-INID'] = $uid;
                $this->headers['X-Incd20-Auth'] = md5($tokens[$idx] . $uid);
                unset($this->headers['uid']);
            }
        }
    }
}