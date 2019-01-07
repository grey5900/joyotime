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
App::uses('AppController', 'Controller');
/**
 * WeixinApi Controller
 *
 * Handle events posted from Weixin server.
 *
 * @package		app.Controller
 */
class ApiController extends AppController
{
    public $name = 'Api';
    
    public $uses = array('WeixinConfig');
    
    public $components = array('WeixinApi');
    
    public $autoRender = false;
    public $autoLayout = false;
    
/**
 * Handle all events about talking with Weixin.
 * Response wrapped string to Weixin server directly.
 * 
 * @return void
 */
    public function handle() {
        return $this->WeixinApi->getReceivedMessage()->getMessage()->toXML();
    }
}