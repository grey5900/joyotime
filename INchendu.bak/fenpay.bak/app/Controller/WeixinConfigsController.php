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
 * Weixin Configs Controller
 *
 * Save all inforamtion about how to uses weixin api.
 *
 * @package		app.Controller
 */
class WeixinConfigsController extends AppController
{
    public $name = 'WeixinConfigs';
    
    public $uses = array('WeixinConfig', 'AutoReplySign');
    
    public $components = array('WeixinLog');
    
    public $layout = 'fenpay';
    
/**
 * (non-PHPdoc)
 * @see Controller::beforeFilter()
 */
    public function beforeFilter() {
    	parent::beforeFilter();
    }

/**
 * Save configure info of weixin.
 *
 * @param string $configTab
 *            There are two values could be used to $configTab,
 *            'basic_info' and 'basic_config' in respectively.
 *            The value used to control which tab should be actived
 *            when page reload.
 *            
 * @return void
 */
    public function add() {
        if($this->request->is('post')) {
            if($this->WeixinConfig->save($this->data)) {
                $this->Session->setFlash('保存配置成功。', 'flash');
            } else {
                $this->Session->setFlash('保存配置失败。'.$this->errorMsg($this->WeixinConfig), 'flash', array(
                    'class' => 'alert-error' 
                ));
            }
            return $this->redirect('/weixin_configs/add');
        }
        $config = $this->WeixinConfig->find('first', array(
            'conditions' => array(
                'user_id' => $this->Auth->user('id') 
            ) 
        ));
        $this->set('config', $config);
        $this->set('signs', $this->AutoReplySign->find('all', array(
            'conditions' => array(
                'user_id' => $this->Auth->user('id') 
            ),
            'orders' => array(
                'id desc' 
            ) 
        )));
        return $this->render('add');
    }

    /**
     * Save settings of weixin
     * 
     * @return void|CakeResponse
     */
    public function setting() {
        if($this->request->is('post') || $this->request->is('put')) {
            if($this->WeixinConfig->saveAssociated($this->data, array('deep' => true))) {
                $this->Session->setFlash('保存配置成功。', 'flash');
            } else {
                $this->Session->setFlash('保存配置失败。', 'flash', array(
                    'class' => 'alert-error' 
                ));
            }
            return $this->redirect('/weixin_configs/setting');
        }
        $config = $this->WeixinConfig->find('first', array(
            'conditions' => array(
                'user_id' => $this->Auth->user('id') 
            ),
            'contain' => array(
                'WeixinLocationConfig',
                'WeixinLocationConfig.ImageAttachment'
            )
        ));
        $this->request->data = $config;
//         debug($this->request->data); // exit;
        return $this->render('setting');
    }
}