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
 * @package       app.Model
 * @since         FenPay(tm) v 0.0.1
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
APP::uses('AppController', 'Controller');
/**
 * The CRUD controller of auto replay messages. 
 *
 * @package       app.Controller
 */
class AutoReplyLocationsController extends AppController {
    public $name = 'AutoReplyLocations';
    public $uses = array('AutoReplyLocation');
    
    public $layout = 'fenpay';
    
    public function beforeFilter() {
    	parent::beforeFilter();
    	$this->Auth->allow('extend');
    }

/**
 * The hybird page means maybe including image
 * and text at the same time.
 *
 * @param array $filter            
 */
    public function index($filter = array()) {
        // In paginate() have to follow below usage to order...
        $this->set('criteria', '');
        $criteria = array();
        if(isset($this->request->query['criteria']) && !empty($this->request->query['criteria'])) {
            $criteria = array(
                'AutoReplyLocation.title LIKE' => '%' . trim($this->request->query('criteria')) . '%' 
            );
            $this->set('criteria', $this->request->query('criteria'));
        }
        
        $conditions = array(
            'AutoReplyLocation.user_id' => $this->Auth->user('id')
        );
        
        $this->paginate = array(
            'limit' => 20,
            'paramType' => "querystring",
            'conditions' => array_merge($conditions, $criteria),
            'order' => array(
            	'AutoReplyLocation.id' => 'DESC'   
            ),
            'contain' => array(
                'AutoReplyLocationMessage',
                'AutoReplyMessage'
            )
        );
        $messages = $this->paginate('AutoReplyLocation');
        $this->set('messages', $messages);
        return $this->render('index');
    }

    public function add() {
        if($this->request->is('post') || $this->request->is('put')) {
            if($this->AutoReplyLocation->saveAssociated($this->data, array(
                'deep' => true 
            ))) {
                $this->Session->setFlash('创建地理位置回复成功。', 'flash');
                return $this->redirect('/' . Inflector::underscore($this->name));
            } else {
                $this->Session->setFlash($this->errorMsg($this->AutoReplyLocation), 'flash', array(
                    'class' => 'alert-error' 
                ));
            }
        }
        return $this->render('add');
    }

    public function edit($id = 0) {
        if(!$id) {
            $this->Session->setFlash('无法编辑此条自动回复', 'flash', array(
                'class' => 'alert-error' 
            ));
            return $this->redirect('/' . Inflector::underscore($this->name));
        }
        $this->request->data = $this->AutoReplyLocation->find('first', array(
            'conditions' => array(
                'AutoReplyLocation.user_id' => $this->Auth->user('id'),
                'AutoReplyLocation.id' => $id
            ),
            'recursive' => 2 
        ));
        if(!$this->request->data) {
            $this->Session->setFlash('你没有权限编辑此条自动回复', 'flash', array(
                'class' => 'alert-error'
            ));
        }
        return $this->render('add');
    }

    public function delete($id = 0) {
        $response = array();
        
        $this->autoRender = false;
        $this->autoLayout = false;
        
        if(!$id) {
            $response['result'] = false;
            $response['message'] = '无法删除此条地理位置回复';
        } else {
            $exist = $this->AutoReplyLocation->find('first', array(
                'conditions' => array(
                    'AutoReplyLocation.id' => $id,
                    'AutoReplyLocation.user_id' => $this->Auth->user('id') 
                ),
                'recursive' => -1 
            ));
            if(!$exist) {
                $response['result'] = false;
                $response['message'] = '你没有权限删除此条地理位置回复';
            } else {
                if($this->AutoReplyLocation->delete($id, TRUE)) {
                    $response['result'] = false;
                    $response['message'] = '删除地理位置回复成功。';
                } else {
                    $response['result'] = false;
                    $response['message'] = '删除地理位置回复失败';
                }
            }
        }
        return json_encode($response);
    }
    
/**
 * The page will display on mobile side of weixin.
 * 
 * @param number $id The $id is primary of AutoReplyLocation
 */
    public function extend($id = 0) {
        $this->helpers[] = 'Link';
        $location = $this->AutoReplyLocation->find('first', array(
            'conditions' => array(
                'AutoReplyLocation.id' => $id
            ),
            'contain' => array(
                'ImageAttachment',
                'AutoReplyMessage' => array(
                    'order' => array(
                        'AutoReplyMessage.id' => 'desc'
                    )
                ),
                'AutoReplyMessage.AutoReplyMessageNews',
                'AutoReplyMessage.AutoReplyMessageNews.ImageAttachment',
            )
        ));
        $this->set(compact('location'));
		$this->layout = 'mobile';
    }
}