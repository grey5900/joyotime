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

/**
 * The CRUD controller of auto replay messages. 
 *
 * @package       app.Controller
 */
class AutoReplyEchosController extends AppController {
    public $name = 'AutoReplyEchos';
    
    public $uses = array('AutoReplyEcho');
    
    public $layout = 'fenpay';
    
/**
 * (non-PHPdoc)
 * @see Controller::beforeFilter()
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
	public function index() {
        $this->paginate = array(
            'limit' => 20,
            'order' => array(
                "AutoReplyEcho.id" => "desc" 
            ),
            'paramType' => "querystring",
            'conditions' => array(
                'user_id' => $this->Auth->user('id'),
            ),
            'contain' => array(
                'AutoReplyEchoRegexp',
            ) 
        );
        $messages = $this->paginate('AutoReplyEcho');
	    $this->set('messages', $messages);
        return $this->render('index');
	}
	
/**
 * @return CakeResponse
 */
	public function add() {
	    if($this->request->is('post')) {
	        if($this->AutoReplyEcho->saveAssociated($this->data, array('deep' => true))) {
	            $this->Session->setFlash('保存成功。', 'flash');
                return $this->redirect('/' . Inflector::underscore($this->name));
	        } else {
	            $this->Session->setFlash($this->errorMsg($this->AutoReplyEcho), 'flash', array(
	            	'class' => 'alert-error'
	            ));
	        }
        }
        $this->set('initInputNumber', 1);
        $this->set('locationCheckboxAvailable', 
                $this->AutoReplyEcho->isLocationEnabled($this->Auth->user('id')));
        return $this->render('add');
	}
	
	public function edit($id = 0) {
	    $this->request->data = $this->AutoReplyEcho->findById($id);
	    if(isset($this->request->data['AutoReplyEchoRegexp']) && 
	        is_array($this->request->data['AutoReplyEchoRegexp'])) {
	        $this->set('initInputNumber', count($this->request->data['AutoReplyEchoRegexp']));
	    } else {
	        $this->set('initInputNumber', 1);
	    }
	    $this->set('locationCheckboxAvailable',
	    		$this->AutoReplyEcho->isLocationEnabled($this->Auth->user('id'), $id));
	    return $this->render('add');
	}
		
	public function delete($id = 0) {
        $this->autoLayout = false;
        $this->autoRender = false;
        $response = array();
        
        if(!$id) {
            $response['result'] = false;
            $response['message'] = '无法删除';
        } else {
            $exist = $this->AutoReplyEcho->find('first', array(
                'conditions' => array(
                    "AutoReplyEcho.id" => $id,
                    "AutoReplyEcho.user_id" => $this->Auth->user('id') 
                ) 
            ));
            if(!$exist) {
                $response['result'] = false;
                $response['message'] = '你没有权限删除';
            } else {
                if($this->AutoReplyEcho->delete($id)) {
                    $response['result'] = true;
                    $response['message'] = '删除成功。';
                } else {
                    $response['result'] = false;
                    $response['message'] = '删除失败';
                }
            }
        }
        return json_encode($response);
    }
}