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
APP::uses('AppController', 'Controller');
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
    
    public $uses = array('WeixinConfig', 'AutoReplyHistory');
    
    public $components = array('WeixinLog', 'WeixinApi');
    
    public $autoRender = false;
    public $autoLayout = false;
    
/**
 * (non-PHPdoc)
 * @see Controller::beforeFilter()
 */
    public function beforeFilter() {
    	parent::beforeFilter();
    	$this->Auth->allow('handle');
    }
    
/**
 * Handle all events about talking with Weixin.
 * Response wrapped string to Weixin server directly.
 * 
 * @return void
 */
    public function handle($who = '') {
        if(!$who) {
            $this->log('illegal access, without name.', 'api_log');
            return false;
        }
        
        $config = $this->WeixinConfig->find('first', array(
            'conditions' => array(
                'WeixinConfig.token' => $who
            ),
            'contain' => array(
                'WeixinLocationConfig',
                'WeixinLocationConfig.ImageAttachment'
            )
        ));
        if(empty($config)) {
            $this->log('Illegal access, no found any account related ['.$who.']', 'api_log');
            return false;
        }
        
        try {
            $received = $this->WeixinApi->getReceivedMessage($config);
        } catch (CakeException $e) {
            $this->WeixinLog->exception($e, $this, $config);
            return false;
        }
        
        if($received) {
            $this->wrapReceived($received);
            $resp = $received->getMessage();
            $xml = $resp->toXML();
            $this->wrapResponse($received, $resp);
            return $xml;
        } else {
            $echostr = $this->WeixinApi->valid($this->request->query, $config['WeixinConfig']['token']);
            if(!$echostr) return false;
            return $echostr;
        }
    }
    
/**
 * Archive received message in database.
 * 
 * @param WeixinReceivePackage $received
 * @return boolean
 */
    private function wrapReceived(WeixinReceivePackage $received) {
        $data = array();
        $data['user_id'] = $received->config['WeixinConfig']['user_id'];
        $data['method'] = 'receive';
        $data['message_type'] = $received->getMsgType();
        $data['client_user'] = $received->getFromUserName();
        $data['raw'] = $received->getRaw();
        $this->AutoReplyHistory->create($data);
        return $this->AutoReplyHistory->save();
    }
    
/**
 * Archive response message in database.
 * 
 * @param WeixinReceivePackage $received
 * @param WeixinSendPackage $resp
 * @return boolean
 */
    private function wrapResponse(WeixinReceivePackage $received, WeixinSendPackage $resp) {
        $data = array();
        $data['user_id'] = $received->config['WeixinConfig']['user_id'];
        $data['method'] = 'response';
        $data['message_type'] = $resp->getMsgType();
        $data['client_user'] = $received->getFromUserName();
        $data['raw'] = $resp->toXML();
        $this->AutoReplyHistory->create($data);
        return $this->AutoReplyHistory->save();
    }
}