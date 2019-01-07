<?php
/**
 * The project of FishSaying is a SNS platform which is
 * based on voice sharing for each other with journey.
 *
 * The RESTful style API is used to communicate with each client-side.
 *
 * PHP 5
 *
 * FishSaying(tm) : FishSaying (http://www.fishsaying.com)
 * Copyright (c) fishsaying.com. (http://fishsaying.com)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) fishsaying.com. (http://www.fishsaying.com)
 * @link          http://fishsaying.com FishSaying(tm) Project
 * @since         FishSaying(tm) v 0.0.1
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
App::uses('AppController', 'Controller');
App::uses('Voice', 'Model');
/**
 * FishSaying Controller
 *
 * Handle events posted from fishsaying server.
 *
 * @package		app.Controller
 */
class VoicesController extends AppController
{
    public $name = 'Voices';
    
    public $layout = 'fishsaying';
    
    public $components = array(
        'VoiceApi' => array(
            'className' => 'FishSayingApi.Voice'
        ), 
        'UserApi' => array(
            'className' => 'FishSayingApi.User'
        ), 
        'ConnectApi' => array(
            'className' => 'FishSayingApi.Connect'
        )
    );
    
    public $uses = array('Voice');
    
/**
 * Voice list
 */
    public function index($status = 1, $userId = false) {
        $limit = 20;
        $items = array();
        $total = 0;
        $query = array();
        
        if($status != -1) {
            $query['status'] = (string) $status;
        }
        
        $query['page'] = $this->request->query('page');
        $query['limit'] = $limit;
        
        $title = $this->request->query('title');
        if($title) {
            $query['title'] = $title;
        }
        
        if($userId) {
            $query['user_id'] = $userId;
            $user = array();
            $resp = $this->UserApi->view($userId);
            if(!$resp->isFail()) {
                $user = $resp->getData();
            }
//             $this->set('userId', $userId);
            $this->set('user', $user);
        }
        $this->paginate = array(
    		'limit' => $limit,
    		'paramType' => "querystring",
        );
        $responser = $this->VoiceApi->index($query);
        if(!$responser->isFail()) {
            $voices = $responser->getData();
            $this->Voice->results = $voices['items'];
            $this->Voice->count = $total = $voices['total'];
            $items = $this->paginate('Voice');
        }
        $this->set('items', $items);
        $this->set('total', $total);
        $this->set('kw', $title);
        
        if($status == 0) {
            return $this->render('pending');
        } else if ($status == 2) {
            return $this->render('invalid');
        } else if ($status == 3) {
            return $this->render('unavailable');
        }
    }
    
    public function add() {
        if($this->request->is('post')) {
            $responser = $this->VoiceApi->add($this->request->data('voices'));
            if($responser->isFail()) {
                $this->Session->setFlash(
                        __('The upload process is to fail').$responser->getMessage(), 
                        'flash',
                        array('class' => 'alert-danger'));
            } else {
                $this->Session->setFlash(__('The upload process has finished in successfully'));
                $this->redirect('/voices');
            }
        }
        $this->set('required', true);
        $this->set('token', $this->ConnectApi->token());
        return $this->render('add');
    }
    
    public function edit($id = '') {
        if($this->request->is('post')) {
            $responser = $this->VoiceApi->edit($id, $this->request->data('voices'));
            if($responser->isFail()) {
                $this->Session->setFlash(
                        __('The upload process is to fail').$responser->getMessage(), 
                        'flash', 
                        array('class' => 'alert-danger'));
            } else {
            	$this->Session->setFlash(__('The upload process has finished in successfully'));
            	$this->redirect('/voices');
            }
        }
        $responser = $this->VoiceApi->view($id);
        if(!$responser->isFail()) {
            $voice = $responser->getData();
            $this->request->data['voices'] = $voice;
            $this->request->data['voices']['latitude'] = $voice['location']['lat'];
            $this->request->data['voices']['longitude'] = $voice['location']['lng'];
        }
        $this->set('required', false);
        $this->set('token', $this->ConnectApi->token());
        return $this->render('add');
    }

    public function remove($id = '') {
        if($id) {
            $responser = $this->VoiceApi->delete($id);
            if(!$responser->isFail()) {
                $this->Session->setFlash(__('The voice has been deleted'));
            } else {
                $this->Session->setFlash(
                        __('Try to delete the voice but to fail').$responser->getMessage());
            }
        } else {
            $this->Session->setFlash(__('No valid voice ID supplied'));
        }
        return $this->redirect('/voices');
    }
    
    public function invalid($id = '') {
        $this->autoLayout = false;
        $this->autoRender = false;
        $reason = $this->request->query('comment');
        if(!$reason) {
        	return $this->resp(false, __('请填写驳回理由'));
        }
	    $data = array(
	        'status' => (int) Voice::STATUS_INVALID,
	        'comment' => $reason,
	    );
		$responser = $this->VoiceApi->edit($id, $data);
		if($responser->isFail()) {
			return json_encode(array(
			    'result' => false,
			    'message' => __('The operation is to fail').$responser->getMessage()));
		} else {
			return json_encode(array(
			    'result' => true, 
			    'message' => __('The operation has finished in successfully')));
		}
    }
    
    public function unavailable($id = '') {
        $this->autoLayout = false;
        $this->autoRender = false;
        $reason = $this->request->query('comment');
        if(!$reason) {
        	return $this->resp(false, __('请填写下架理由'));
        }
	    $data = array(
	        'status' => (int) Voice::STATUS_UNAVAILABLE,
	        'comment' => $reason,
	    );
		$responser = $this->VoiceApi->edit($id, $data);
		if($responser->isFail()) {
			return json_encode(array(
			    'result' => false, 
			    'message' => __('The operation is to fail').$responser->getMessage()));
		} else {
			return json_encode(array(
			    'result' => true, 
			    'message' => __('The operation has finished in successfully')));
		}
    }
    
    public function approved($id = '') {
        $this->autoLayout = false;
        $this->autoRender = false;
	    $data = array(
	        'status' => (int) Voice::STATUS_APPROVED,
	    );
		$responser = $this->VoiceApi->edit($id, $data);
		if($responser->isFail()) {
			return json_encode(array(
			    'result' => false, 
			    'message' => __('The operation is to fail').$responser->getMessage()));
		} else {
			return json_encode(array(
			    'result' => true, 
			    'message' => __('The operation has finished in successfully')));
		}
    }
}