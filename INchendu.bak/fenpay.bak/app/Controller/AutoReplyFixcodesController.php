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
APP::uses('AutoReplyMessageNews', 'Model');
/**
 * The controller of auto reply fixcode.
 *
 * @package       app.Controller
 */
class AutoReplyFixcodesController extends AppController {
    
    public $name = 'AutoReplyFixcodes';
    
    public $layout = 'fenpay';
    
    public $uses = array('AutoReplyFixcode', 'AutoReplyMessage', 'AutoReplyKeyword');
    
/**
 * @return CakeResponse
 */
    public function index() {
        $criteria = array();
        $this->set('criteria', '');
        if(isset($this->request->query['criteria']) && !empty($this->request->query['criteria'])) {
        	$keyword = $this->AutoReplyKeyword->findByName(trim($this->request->query('criteria')));
            if($keyword) {
                $fixcodeIds = Hash::extract($keyword, 'AutoReplyFixcode.{n}.id');
                $criteria = array('AutoReplyFixcode.id' => $fixcodeIds);
            } else {
                $criteria = array('AutoReplyFixcode.id' => array());
            }
            $this->set('criteria', $this->request->query('criteria'));
        }
        
        $conditions = array(
            "AutoReplyFixcode.user_id" => $this->Auth->user('id'),
        );
        
        $this->paginate = array(
            'limit' => 20,
            'order' => array(
                "AutoReplyFixcode.created" =>  "desc" 
            ),
            'paramType' => "querystring",
            'conditions' => array_merge($conditions, $criteria),
        );
        $messages = $this->paginate('AutoReplyFixcode');
        $this->set('messages', $messages);
        return $this->render('index');
    }
    
/**
 * Create a AutoReplyFixcode
 */
    public function add() {
        if($this->request->is('post')) {
            if($this->AutoReplyFixcode->saveAssociated($this->data, array('deep' => true))) {
                $this->Session->setFlash('保存成功', 'flash');
                return $this->redirect('/'.Inflector::underscore($this->name));
            }
            $this->Session->setFlash('保存失败', 'flash', array('class' => 'alert-error'));
        }
        $this->set('selected_messages', array());
        $this->getNewsList();

        return $this->render('add');
    }
    
/**
 * Get new list and set it as template vars.
 * @return void
 */
	 private function getNewsList() {
		$this->set('messages', $this->AutoReplyMessage->find('list', array(
            'fields' => array(
                'AutoReplyMessage.id',
                'AutoReplyMessageNews.title',
            ),
            'conditions' => array(
                'AutoReplyMessage.user_id' => $this->Auth->user('id'),
                'AutoReplyMessage.type' => array('custom'),
            ),
            'contain' => array(
                'AutoReplyMessageNews' => array(
                    'order' => array('id' => 'desc')
                )
            )
        )));
	}

/**
 * Edit fixcode message.
 * 
 * @param number $id
 * @return void|CakeResponse
 */
    public function edit($id = 0) {
        if(!$id) {
            $this->Session->setFlash('无法编辑此条自动回复', 'flash', array(
                'class' => 'alert-error' 
            ));
            return $this->redirect('/' . Inflector::underscore($this->name));
        }
        $this->request->data = $this->AutoReplyFixcode->find('first', array(
            'conditions' => array(
                "AutoReplyFixcode.user_id" => $this->Auth->user('id'),
                "AutoReplyFixcode.id" => $id 
            ),
            'recursive' => 2
        ));
        $this->request->data['AutoReplyFixcode']['type'] = $this->checkType($this->request->data);
        // Get selected messages ID. 
        $selected_messages = array();
        foreach($this->request->data['AutoReplyMessage'] as $item) {
            $selected_messages[] = $item['id'];
        }
        $this->set('selected_messages', $selected_messages);
        if(!$this->request->data) {
            $this->Session->setFlash('你没有权限编辑此条自动回复', 'flash', array(
                'class' => 'alert-error' 
            ));
        }
        $this->getNewsList();
        
        // Get tags of fixcode.
        if(isset($this->request->data['AutoReplyFixcodeKeyword'])) {
        	$this->set('tags', 
        	        Hash::extract($this->request->data, 
        	                'AutoReplyFixcodeKeyword.{n}.AutoReplyKeyword.name')
        	);
        }
        
        $this->set('selected_messages', Hash::extract($this->request->data, 'AutoReplyMessage.{n}.id'));
        return $this->render('add');
    }
    
/**
 * Delete a fixcode message and all related records if existed.
 *  
 * @param number $id
 * @return string json
 *     it contains below structures,
 *     1. if successful, 
 *     {result: true}
 *     2. if failure,
 *     {result: false, message: "message for failure."}
 */
    public function delete($id = 0) {
        $this->autoLayout = false;
	    $this->autoRender = false;
	    $response = array();
	    
	    if(!$id) {
            $response['result'] = false;
            $response['message'] = '无法删除此条自动回复';
        } else {
        	$exist = $this->AutoReplyFixcode->find('first', array(
        		'conditions' => array(
        			"AutoReplyFixcode.id" => $id,
        			"AutoReplyFixcode.user_id" => $this->Auth->user('id')
        		),
        	));
        	$kwIds = Hash::extract($exist, 'AutoReplyKeyword.{n}.id');
        	if(!$exist) {
        		$response['result'] = false;
        		$response['message'] = '你没有权限删除此条自动回复';
        	} else {
	            if($this->AutoReplyFixcode->delete($id)) {
	                // delete related kw...
	                if($kwIds && is_array($kwIds)) {
	                    foreach($kwIds as $id) {
	                        $this->AutoReplyKeyword->delete($id);
	                    }
	                }
	                $response['result'] = true;
	                $response['message'] = '删除自动回复成功。';
	            } else {
	                $response['result'] = false;
	                $response['message'] = '删除回复失败';
	            }
        	}
        }
        return json_encode($response);
    }
    
/**
 * Check whether type is text or news.
 * @param array $item
 * @return string return empty string if not matched text nor news.
 */
    private function checkType(&$item) {
    	$total = count($item['AutoReplyMessage']);
    	if($total > 1) {
    		return 'news';
    	} else {
    		if($total == 1 && $item['AutoReplyMessage'][0]['type'] == 'text') {
    			return 'text';
    		} else {
    			return 'news';
    		}
    	}
    	return '';
    }
}