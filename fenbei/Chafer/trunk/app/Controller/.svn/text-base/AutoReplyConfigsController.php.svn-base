<?php
APP::uses('AutoReplyConfig', 'Model');

class AutoReplyConfigsController extends AppController {
    
    public $name = 'AutoReplyConfigs';
    
    public $uses = array('AutoReplyTag', 'AutoReplyConfig');
    
    public $layout = 'fenpay';
    
    public function add() {
    	if($this->request->is('post') || $this->request->is('put')) {
    		if($this->AutoReplyConfig->saveAssociated($this->data, array('deep' => true))) {
    			$this->Session->setFlash('保存成功。', 'flash');
    			return $this->redirect('/'.Inflector::underscore($this->name).'/add');
    		} else {
    			$this->Session->setFlash(
    					$this->errorMsg($this->AutoReplyConfig),
    					'flash', array('class' => 'alert-error'));
    		}
    	}
        $available = $this->AutoReplyTag->find('all', array(
            'fields' => 'name',
            'conditions' => array(
                'user_id' => $this->Auth->user('id')
            ),
            'limit' => 100
        ));
        $available = Hash::extract($available, '{n}.AutoReplyTag.name');
        $this->set(compact('available'));
        
        $subscribe = $this->AutoReplyConfig->find('first', array(
        	'conditions' => array(
        		'AutoReplyConfig.user_id' => $this->Auth->user('id'),
        		'AutoReplyConfig.situation' => AutoReplyConfig::EVT_SUBSCRIBE
        	),
        ));
        $noanswer = $this->AutoReplyConfig->find('first', array(
        	'conditions' => array(
        		'AutoReplyConfig.user_id' => $this->Auth->user('id'),
        		'AutoReplyConfig.situation' => AutoReplyConfig::EVT_NOANSWER
        	),
        ));
        $this->set(compact('subscribe', 'noanswer'));
    }
    
}