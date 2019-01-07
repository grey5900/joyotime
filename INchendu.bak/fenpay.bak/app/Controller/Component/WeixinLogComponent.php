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
 * The log component to help save weixin xml 
 * or object to log file.
 *
 * @package       app.Controller.Component
 */
class WeixinLogComponent extends Component {
    
/**
 * To log received xml to file
 */
    public function received() {
        if(isset($GLOBALS["HTTP_RAW_POST_DATA"])) {
            $this->log($GLOBALS["HTTP_RAW_POST_DATA"], 'weixin_receives');
        }
    }

/**
 * To log responsed xml to file
 * @param string $xml
 */
    public function response($xml = '') {
        if($xml) {
            $this->log($xml, 'weixin_responses');
        }
    }
    
/**
 * To log exception message and code to file
 * @param CakeException $e
 * @param AppController $c
 * @param array $config
 * @return void
 */
    public function exception(CakeException $e, AppController $c, array $config) {
        $snap = array(
            'code' => $e->getCode(),
            'message' => $e->getMessage(),
        );
        $snap = array_merge($snap, $c->request->query);
        if(isset($GLOBALS["HTTP_RAW_POST_DATA"])) {
        	$snap['post_data'] = $GLOBALS["HTTP_RAW_POST_DATA"];
        }
        $snap = array_merge($snap, $config);
        $this->log($snap, 'weixin_exceptions');
    }
}