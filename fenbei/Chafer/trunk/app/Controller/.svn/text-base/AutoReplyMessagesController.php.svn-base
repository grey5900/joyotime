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
APP::uses('AutoReplyMessageNews', 'Model');
/**
 * The CRUD controller of auto replay messages. 
 *
 * @package       app.Controller
 */
class AutoReplyMessagesController extends AppController {
    
	public $name = 'AutoReplyMessages';
	
/**
 * The specified models need to be loaded automatically.
 * @var array
 */
	public $uses = array('AutoReplyCategory', 'AutoReplyMessage');
	
/**
 * Specify layout name
 * @var string
 */
	public $layout = 'fenpay';
	
/**
 * (non-PHPdoc)
 * @see Controller::beforeFilter()
 */
	public function beforeFilter() {
		parent::beforeFilter();
		$this->Auth->allow('news');
	}
	
/**
 * (non-PHPdoc)
 * @see AutoReplyCRUDController::getMessageType()
 */
	public function getMessageType() {
        return array(
            AutoReplyMessageNews::CUSTOM,
            AutoReplyMessageNews::LINK,
            AutoReplyMessageNews::MAP 
        );
	}
	
/**
 * The news page means maybe including image
 * and text at the same time.
 *
 * @param array $filter
 */
	public function index($filter = array()) {
		$this->set('cates', $this->AutoReplyCategory->getList($this->Auth->user('id')));
		$this->set('filter', $filter);
		
		$conditions = array(
			"AutoReplyMessage.user_id" => $this->Auth->user('id'),
			"AutoReplyMessage.type" => $this->getMessageType()
		);
		
		$criteria = array();
		if($filter == -1) { 
		    $criteria = array('AutoReplyMessageNews.auto_reply_category_id' => NULL);
		} else if(!empty($filter)) {
		    $criteria = array('AutoReplyMessageNews.auto_reply_category_id' => intval($filter));
		}
		
		$this->set('criteria', '');
		if(isset($this->request->query['criteria']) && !empty($this->request->query['criteria'])) {
		    $tag = $this->AutoReplyMessage->AutoReplyTag->findByName(
		            trim($this->request->query('criteria')));
		    if($tag) {
		        // If tag has existed, try to search on tag and title.
		    	$ids = Hash::extract($tag, 'AutoReplyMessage.{n}.id');
		    	$criteria = array(
		    	    'OR' => array(
		    	        'AutoReplyMessage.id' => $ids,
        		        'AutoReplyMessageNews.title LIKE' => '%'.trim($this->request->query('criteria')).'%'
        		    )
		    	);
		    } else {
		    	$criteria = array(
		    	    'OR' => array(
	    	    		'AutoReplyMessage.id' => array(),
	    	    		'AutoReplyMessageNews.title LIKE' => '%'.trim($this->request->query('criteria')).'%'
		    	    )
		    	);
		    }
		    $this->set('criteria', $this->request->query('criteria'));
		}
		
		$conditions = array_merge($conditions, $criteria);
		
        $this->paginate = array(
            'limit' => 20,
            'order' => array(
                "AutoReplyMessage.id" => "desc" 
            ),
            'paramType' => "querystring",
            'conditions' => $conditions,
            'contain' => array(
                'AutoReplyMessageNews',    
                'AutoReplyMessageNews.AutoReplyCategory',
                'AutoReplyTag',
            ),
        );
        $messages = $this->paginate('AutoReplyMessage');
		$this->set('messages', $messages);
		$this->render('index');
	}
	
	public function add() {
	    $nonCate = false;
        if($this->request->is('post') || $this->request->is('put')) {
            if($this->AutoReplyMessage->saveAssociated($this->data, array('deep' => true))) {
                $this->Session->setFlash('保存成功。', 'flash');
                return $this->redirect('/' . Inflector::underscore($this->name));
            } else {
                $this->Session->setFlash($this->errorMsg($this->AutoReplyMessage), 'flash', array(
                    'class' => 'alert-error' 
                ));
            }
        }
        $this->set('cates', $this->AutoReplyCategory->getList($this->Auth->user('id'), $nonCate));
	}

    public function edit($id = 0) {
        if(!$id) {
            $this->Session->setFlash('无法编辑此条自动回复', 'flash', array(
                'class' => 'alert-error' 
            ));
            return $this->redirect('/' . Inflector::underscore($this->name));
        }
        $this->request->data = $this->AutoReplyMessage->find('first', array(
            'conditions' => array(
                "AutoReplyMessage.user_id" => $this->Auth->user('id'),
                "AutoReplyMessage.id" => $id 
            ),
            'recursive' => 2 
        ));
        if(!$this->request->data) {
            $this->Session->setFlash('你没有权限编辑此条自动回复', 'flash', array(
                'class' => 'alert-error' 
            ));
        }
        $nonCate = false;
        $this->set('cates', $this->AutoReplyCategory->getList($this->Auth->user('id'), $nonCate));
        if(isset($this->request->data['AutoReplyMessageTag'])) {
            $this->set('tags', Hash::extract($this->request->data, 'AutoReplyMessageTag.{n}.AutoReplyTag.name'));
        }
        return $this->render('add');
    }
	
/**
 * Display custom content as a page.
 * 
 * @param number $id
 * @throws NotFoundException
 */
	public function news($id = 0) {
	    if(!$id) {
	        throw new NotFoundException();
	    }
	    $message = $this->AutoReplyMessage->find('first', array(
            'conditions' => array(
                'AutoReplyMessage.id' => $id 
            ) 
        ));
	    if($message) {
 	        $this->AutoReplyMessage->AutoReplyMessageNews->increaseViewTotal($id);
	        if($message['AutoReplyMessage']['type'] == AutoReplyMessageNews::LINK) {
	            $this->Link = $this->Components->load('Link');
	            $this->redirect($this->Link->exlink($message['AutoReplyMessageExlink']['exlink']));
	            return ;
	        } else {
	            $this->set('news', $message);
	        }
	    }
	    $this->layout = 'mobile';
	} 
	
	public function delete($id = 0) {
	    $this->autoLayout = false;
	    $this->autoRender = false;
	    $response = array();
	    
	    if(!$id) {
            $response['result'] = false;
            $response['message'] = '无法删除此条自动回复';
        } else {
        	$exist = $this->AutoReplyMessage->find('first', array(
        		'conditions' => array(
        			"AutoReplyMessage.id" => $id,
        			"AutoReplyMessage.user_id" => $this->Auth->user('id')
        		),
        		'contain' => array(
        		    'AutoReplyMessageNews'    
        		),
        		'recursive' => -1
        	));
        	if(!$exist) {
        		$response['result'] = false;
        		$response['message'] = '你没有权限删除此条自动回复';
        	} else {
        	    if(isset($exist['AutoReplyMessageNews']['auto_reply_category_id'])) {
        	        $this->AutoReplyCategory->decrease(
        	                $exist['AutoReplyMessageNews']['auto_reply_category_id']);
        	    }
	            if($this->AutoReplyMessage->delete($id, TRUE)) {
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
}