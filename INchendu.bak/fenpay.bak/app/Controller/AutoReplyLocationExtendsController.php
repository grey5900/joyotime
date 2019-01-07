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
 * The CRUD controller of auto replay messages. 
 *
 * @package       app.Controller
 */
class AutoReplyLocationExtendsController extends AppController {
    public $name = 'AutoReplyLocationExtends';
    
    public $uses = array(
        'AutoReplyLocation',
        'AutoReplyCategory',
        'AutoReplyMessage',
        'AutoReplyLocationMessage' ,
        'AutoReplyMessageNews',
    );
    
    public $layout = 'fenpay';
    
/**
 * (non-PHPdoc)
 * @see Controller::beforeFilter()
 * @deprecated
 */
	public function beforeFilter() {
		parent::beforeFilter();
	}
	
/**
 * The hybird page means maybe including image
 * and text at the same time.
 *
 * @param array $filter
 */
	public function index($filter = array()) {
	    $this->set('criteria', '');
	    $criteria = array();
	    if(isset($this->request->query['criteria']) && !empty($this->request->query['criteria'])) {
	        $news = $this->AutoReplyMessageNews->find('all', array(
	            'fields' => array('auto_reply_message_id'),
	            'conditions' => array(
	                'title LIKE' => '%'.trim($this->request->query['criteria']).'%', 
	            ),
	            'recursive' => -1
	        ));
	        $msgIds = Hash::extract($news, '{n}.AutoReplyMessageNews.auto_reply_message_id');
	    	$criteria = array(
	    	    'OR' => array(
	    		    'AutoReplyLocation.title LIKE' => '%' . trim($this->request->query('criteria')) . '%',
	    		    'AutoReplyMessage.id' => $msgIds,
	    	    )
	    	);
	    	$this->set('criteria', $this->request->query('criteria'));
	    }
	    
	    $conditions = array(
            'AutoReplyLocationMessage.user_id' => $this->Auth->user('id')
        );
	    
        $this->paginate = array(
            'limit' => 20,
            'paramType' => 'querystring',
            'fields' => array(
                'auto_reply_message_id'
            ),
            'order' => array(
                'id' => 'desc'
            ),
            'group' => array(
                'auto_reply_message_id'
            ),
            'contain' => array(
                'AutoReplyLocation',
                'AutoReplyMessage',
                'AutoReplyMessage.AutoReplyMessageNews',
            ),
            'conditions' => array_merge($conditions, $criteria),
            'recursive' => -1
        );
        
        $messageIds = Hash::extract($this->paginate('AutoReplyLocationMessage'), 
                '{n}.AutoReplyLocationMessage.auto_reply_message_id');
        $messages = array();
        if($messageIds) {
            $messages = $this->AutoReplyMessage->find('all', array(
                'conditions' => array(
                    'AutoReplyMessage.id' => $messageIds,
                ),
                'contain' => array(
                    'AutoReplyMessageNews',
                    'AutoReplyLocation',
                ),
                'joins' => array(
                    array(
                        'table' => 'auto_reply_location_messages',
                        'alias' => 'AutoReplyLocationMessage',
                        'conditions' => array(
                            'AutoReplyMessage.id = AutoReplyLocationMessage.auto_reply_message_id' 
                        ) 
                    ) 
                ),
                'group' => array(
                    'AutoReplyMessage.id'
                ),
                'order' => array(
                    'AutoReplyLocationMessage.id' => 'desc'
                )
            ));
        }
	    $this->set('messages', $messages);
        return $this->render('index');
	}
	
/**
 * Bind locations to a news.
 * 
 * @return CakeResponse
 */
	public function add() {
	    if($this->request->is('post')) {
	        if(!isset($this->data['AutoReplyLocationMessage']) || 
	            empty($this->data['AutoReplyLocationMessage'])) {
	            $this->Session->setFlash(
	            		'请选择关联地点',
	            		'flash', array('class' => 'alert-error'));
	            $this->request->data = array();
	        } else {
                $save = $this->data['AutoReplyLocationMessage'];
                $first = array_shift($save);
                
                if(!isset($first['auto_reply_message_id']) || empty($first['auto_reply_message_id'])) {
                    $this->Session->setFlash(
                		'请选择关联图文信息',
                		'flash', array('class' => 'alert-error'));
                    $this->request->data = array();
                } else {
                
                    $this->AutoReplyLocationMessage->deleteAll(array(
                        'auto_reply_message_id' => $first['auto_reply_message_id']
                    ), false);
                    
                    $this->AutoReplyMessageNews->updateAll(array(
                    	'selected_by_location_extend' => 1,
                    ), array(
                    	'auto_reply_message_id' => $first['auto_reply_message_id']
                    ));
                    
        	        if($this->AutoReplyLocationMessage->saveAll($this->data['AutoReplyLocationMessage'])) {
        	            
        	        	$this->Session->setFlash('保存扩展信息成功。', 'flash');
        	        	return $this->redirect('/'.Inflector::underscore($this->name));
        	        } else {
        	        	$this->Session->setFlash(
        	        		$this->errorMsg($this->AutoReplyLocationMessage),
        	        		'flash', array('class' => 'alert-error'));
        	        }
                }
	        }
        }
        
        // Load component and helpers on the fly...
        $this->Components->load('RequestHandler');
        $this->helpers[] = 'Js';
        
        $this->paginate = array (
    		'limit' => 10,
    		'order' => array (
    			'AutoReplyMessage.id' => 'desc'
    		),
    		'paramType' => "querystring",
    		'conditions' => array(
    		    'AutoReplyMessage.type' => array(
		    		AutoReplyMessageNews::CUSTOM,
		    		AutoReplyMessageNews::LINK,
		    		AutoReplyMessageNews::MAP
    		    ),
    		    'AutoReplyMessage.user_id' => $this->Auth->user('id'),
    		    'AutoReplyMessageNews.selected_by_location_extend' => 0,
    		),
            'recursive' => -1,
            'contain' => array(
                'AutoReplyMessageNews',
                'AutoReplyMessageNews.ImageAttachment',
                'AutoReplyMessageCustom',
            )
        );
        $messages = $this->paginate('AutoReplyMessage');
        $this->set('messages', $messages);
        
        if(!$this->request->query('page')) {
            $this->set('locations', $this->AutoReplyLocation->find('all', array(
                'order' => array('AutoReplyLocation.id desc'),
                'conditions' => array(
                    'AutoReplyLocation.user_id' => $this->Auth->user('id'),
                )
            )));
            return $this->render('add');
        } else {
            $this->autoLayout = false;
            $this->autoRender = false;
            $this->set('isAjax', true);
            return $this->render('paginate');
        }
	}
	
	public function edit($auto_reply_message_id = 0) {
	    if(!$auto_reply_message_id) {
	        throw new NotFoundException();
	    }
            
        // load component and helpers on the fly...
        $this->Components->load('RequestHandler');
        $this->helpers[] = 'Js';
        
        $this->paginate = array(
            'limit' => 1,
            'order' => array(
                'AutoReplyMessage.id desc' 
            ),
            'paramType' => "querystring",
            'conditions' => array(
                'AutoReplyMessage.type' => array(
                    AutoReplyMessageNews::CUSTOM,
                    AutoReplyMessageNews::LINK,
                    AutoReplyMessageNews::MAP 
                ),
                'AutoReplyMessage.user_id' => $this->Auth->user('id') 
            ) 
        );
        $this->set('messages', $this->paginate('AutoReplyMessage'));
	    
        if(!$this->request->query('page')) {
            $this->request->data = $this->AutoReplyMessage->find('first', array(
                'conditions' => array(
                    'AutoReplyMessage.id' => $auto_reply_message_id 
                ),
                'contain' => array(
                    'AutoReplyLocation',
                    'AutoReplyMessageNews',
                    'AutoReplyMessageNews.ImageAttachment',
                    'AutoReplyMessageCustom',
                ) 
            ));
            
            $this->set('selected_locations', Hash::extract($this->request->data, 'AutoReplyLocation.{n}.id'));
            $this->set('locations', $this->AutoReplyLocation->find('all', array(
                'order' => array(
                    'AutoReplyLocation.id desc' 
                ),
                'conditions' => array(
                    'AutoReplyLocation.user_id' => $this->Auth->user('id') 
                ) 
            )));
            return $this->render('add');
        } else {
            $this->autoLayout = false;
            $this->autoRender = false;
            return $this->render('get_messages');
        }
	}
	
	public function delete($auto_reply_message_id = 0) {
	    $this->autoLayout = false;
	    $this->autoRender = false;
	    $response = array();
	    
	    if(!$auto_reply_message_id) {
	        $response['result'] = false;
            $response['message'] = '无法删除此条自动回复';
            return json_encode($response);
	    }
	    
	    $this->AutoReplyMessageNews->updateAll(array(
        	'selected_by_location_extend' => 0,
        ), array(
        	'auto_reply_message_id' => $auto_reply_message_id
        ));
	    
	    if($this->AutoReplyLocationMessage->deleteAll(
	            array('AutoReplyLocationMessage.auto_reply_message_id' => $auto_reply_message_id), false)) {
	        $response['result'] = true;
	        $response['message'] = '删除成功。';
	    } else {
	        $response['result'] = false;
	        $response['message'] = '无法删除回复';
	    }
	    return json_encode($response);
	}
}