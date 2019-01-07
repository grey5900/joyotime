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
 * It uses to post raw data got from weixin to another place which is pre-defined.
 *
 * @package       app.Controller.Component
 */
class RemoteComponent extends Component {
/**
 * @var RemoteHttp
 */
    private $http;
/**
 * @var array
 */
    private $cfg = array();
/**
 * @var AutoReplyEcho
 */
    private $AutoReplyEcho;
    
    public function __construct(ComponentCollection $collection, $settings = array()) {
        parent::__construct($collection, $settings);
        $this->http = new RemoteHttp();
        $this->AutoReplyEcho = ClassRegistry::init('AutoReplyEcho');
    }
    
/**
 * The config items are pre-defined.
 * @param array $configs
 * @return RemoteComponent
 */
    public function setConfig(array $configs = array()) {
        $this->cfg = $configs;
        return $this;
    }

/**
 * Try to find matched in regexps pre-defined.
 * @param string $content
 * @param string $raw
 * @return RemoteHttp it returns false if nothing matched.
 */
    public function regexp($content, $raw) {
        foreach($this->cfg as $echo) {
            if($echo['AutoReplyEcho']['enabled_regexp'] && !empty($echo['AutoReplyEchoRegexp'])) {
                foreach($echo['AutoReplyEchoRegexp'] as $regexp) {
                    if(preg_match('/' . $regexp['regexp'] . '/', $content)) {
                        $this->http->setRaw($raw);
                        $this->http->setUrl($echo['AutoReplyEcho']['url']);
                        $this->AutoReplyEcho->increaseSentNum($echo['AutoReplyEcho']['id']);
                        return $this->http;
                    }
                }
            }
        }
        return false;
    }
    
/**
 * Try to find matched in locations pre-defined.
 * @param string $content
 * @param string $raw
 * @return RemoteHttp it returns false if nothing matched.
 */
    public function location($raw) {
        foreach($this->cfg as $echo) {
            if($echo['AutoReplyEcho']['enabled_location']) {
                $this->http->setRaw($raw);
                $this->http->setUrl($echo['AutoReplyEcho']['url']);
                $this->AutoReplyEcho->increaseSentNum($echo['AutoReplyEcho']['id']);
                return $this->http;
            }
        }
        return false;
    }
}

/**
 * The proxy class for communicate with remote server in HTTP.
 *
 */
class RemoteHttp {
/**
 * Request URL
 * @var string
 */
    private $url;
    
/**
 * Raw xml data posted from weixin.
 * @var string
 */
    private $raw;
    
/**
 * Raw xml data posted from weixin.
 * @param string $raw
 */
    public function setRaw($raw) {
    	$this->raw = $raw;
    }
    
/**
 * Request URL
 * @param string $url
 */
    public function setUrl($url) {
    	$this->url = $url;
    }
    
/**
 * Post data to remote server.
 * @return mixed It returns results returned from remote server, 
 *     or error message if any error happened.
 */
    public function post() {
    	$ch = curl_init();
    	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: text/xml'));
    	curl_setopt($ch, CURLOPT_URL, $this->url);
    	curl_setopt($ch, CURLOPT_HEADER, 0);
    	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    	curl_setopt($ch, CURLOPT_POST, 1);
    	curl_setopt($ch, CURLOPT_POSTFIELDS, $this->raw);
    	$output = curl_exec($ch);
    	if($output === FALSE) {
    		return "cURL Error: " . curl_error($ch);
    	}
    	curl_close($ch);
    	return $output;
    }
}