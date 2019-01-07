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
 * @copyright Copyright (c) fishsaying.com. (http://www.fishsaying.com)
 * @link http://fishsaying.com FishSaying(tm) Project
 * @since FishSaying(tm) v 0.0.1
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
App::uses('AppController', 'Controller');
App::uses('Voice', 'Model');
/**
 * FishSaying Controller
 *
 * Handle events posted from fishsaying server.
 *
 * @package app.Controller
 */
class VoicesController extends AppController {
	public $name = 'Voices';
	public $layout = 'fishsaying';
	public $components = array(
		'VoiceApi'=>array(
			'className'=>'FishSayingApi.Voice' 
		), 
		'UserApi'=>array(
			'className'=>'FishSayingApi.User' 
		), 
		'TagApi'=>array(
			'className'=>'FishSayingApi.Tag' 
		), 
		'ConnectApi'=>array(
			'className'=>'FishSayingApi.Connect' 
		) 
	);
	public $uses = array(
		'Voice' 
	);
	
	/**
	 * Voice list
	 */
	public function index($status = 1, $userId = false) {
		$limit = 20;
		$items = array();
		$total = 0;
		$query = array();
		$re_status = false;
		// //
		if ($status == Voice::RECOMMENDED) {
			$query['recommend'] = 1;
			$re_status = true;
		} elseif ($status == Voice::RECOMMEND) {
			$query['recommend'] = 0;
			$re_status = true;
		}
		
		if ($status != - 1) {
			$query['status'] = (string)$status;
		}
		if ($re_status == true) {
			$query['status'] = 1;
		}
		
		$query['page'] = $this->request->query('page');
		$query['limit'] = $limit;
		
		$title = $this->request->query('title');
		if ($title) {
			$query['title'] = $title;
		}
		if ($userId) {
			$query['user_id'] = $userId;
			$user = array();
			$resp = $this->UserApi->view($userId);
			if (! $resp->isFail()) {
				$user = $resp->getData();
			}
			$this->set('user', $user);
		}
		$query['sort'] = 'status_modified';
		$this->paginate = array(
			'limit'=>$limit, 
			'paramType'=>"querystring" 
		);
		
		$responser = $this->VoiceApi->index($query);
		if (! $responser->isFail()) {
			$voices = $responser->getData();
			$this->redirectPage($voices, $limit);
			
			$this->Voice->results = $voices['items'];
			$this->Voice->count = $total = $voices['total'];
			$items = $this->paginate('Voice');
		}
		$this->ConnectApi->token();
		$this->set('items', $items);
		$this->set('total', $total);
		$this->set('kw', $title);
		return $this->render($this->getListTemp($status));
	}
	/**
	 * 取得各种解说列表模版
	 *
	 * @param integer $status        	
	 * @return string
	 */
	private function getListTemp($status) {
		$temp = '';
		switch ($status) {
			case 0 :
				$temp = 'pending';
				break;
			case 1 :
				$temp = 'index';
				break;
			case 2 :
				$temp = 'invalid';
				break;
			case 3 :
				$temp = 'unavailable';
				break;
			case 4 :
				$temp = 'recommended';
				break;
			case 5 :
				$temp = 'recommend';
				break;
			case 6 :
				$temp = 'adVoice';
				break;
			case 7 :
				$temp = 'ad_voice_add';
				break;
		}
		return $temp;
	}
	public function recommend($voiceId) {
		$this->autoLayout = false;
		$this->autoRender = false;
		$responser = $this->VoiceApi->edit($voiceId, array(
			'recommend'=>(int)Voice::RECOMMENDED_STATUS 
		));
		if ($responser->isFail()) {
			return json_encode(array(
				'result'=>false, 
				'message'=>__('The operation is to fail') . $responser->getMessage() 
			));
		} else {
			return json_encode(array(
				'result'=>true, 
				'message'=>__('The operation has finished in successfully') 
			));
		}
	}
	public function add() {
		if ($this->request->is('post')) {
			$responser = $this->VoiceApi->add($this->request->data('voices'));
			if ($responser->isFail()) {
				$this->Session->setFlash(__('The upload process is to fail') . $responser->getMessage(), 'flash', array(
					'class'=>'alert-danger' 
				));
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
		if ($this->request->is('post')) {
			$responser = $this->VoiceApi->edit($id, $this->request->data('voices'));
			if ($responser->isFail()) {
				$this->Session->setFlash(__('The upload process is to fail') . $responser->getMessage(), 'flash', array(
					'class'=>'alert-danger' 
				));
			} else {
				$this->Session->setFlash(__('The upload process has finished in successfully'));
				$this->redirect('/voices');
			}
		}
		$responser = $this->VoiceApi->view($id);
		if (! $responser->isFail()) {
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
		if ($id) {
			$responser = $this->VoiceApi->delete($id);
			if (! $responser->isFail()) {
				$this->Session->setFlash(__('The voice has been deleted'));
			} else {
				$this->Session->setFlash(__('Try to delete the voice but to fail') . $responser->getMessage());
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
		if (! $reason) {
			return $this->resp(false, __('请填写驳回理由'));
		}
		$data = array(
			'status'=>(int)Voice::STATUS_INVALID, 
			'comment'=>$reason 
		);
		$responser = $this->VoiceApi->edit($id, $data);
		if ($responser->isFail()) {
			return json_encode(array(
				'result'=>false, 
				'message'=>__('The operation is to fail') . $responser->getMessage() 
			));
		} else {
			return json_encode(array(
				'result'=>true, 
				'message'=>__('The operation has finished in successfully') 
			));
		}
	}
	public function unavailable($id = '') {
		$this->autoLayout = false;
		$this->autoRender = false;
		$reason = $this->request->query('comment');
		if (! $reason) {
			return $this->resp(false, __('请填写下架理由'));
		}
		$data = array(
			'status'=>(int)Voice::STATUS_UNAVAILABLE, 
			'comment'=>$reason 
		);
		$responser = $this->VoiceApi->edit($id, $data);
		if ($responser->isFail()) {
			return json_encode(array(
				'result'=>false, 
				'message'=>__('The operation is to fail') . $responser->getMessage() 
			));
		} else {
			return json_encode(array(
				'result'=>true, 
				'message'=>__('The operation has finished in successfully') 
			));
		}
	}
	public function approved($id = '') {
		$this->autoLayout = false;
		$this->autoRender = false;
		$score = (int)$this->request->query('score');
		$data = array(
			'status'=>(int)Voice::STATUS_APPROVED 
		);
		if ($score > 0) {
			$data['score'] = $score;
		}
		$responser = $this->VoiceApi->edit($id, $data);
		if ($responser->isFail()) {
			return json_encode(array(
				'result'=>false, 
				'message'=>__('The operation is to fail') . $responser->getMessage() 
			));
		} else {
			return json_encode(array(
				'result'=>true, 
				'message'=>__('The operation has finished in successfully') 
			));
		}
	}
	/**
	 * cancel recommend
	 */
	public function recommendCancel($voiceId) {
		$this->autoLayout = false;
		$this->autoRender = false;
		$responser = $this->VoiceApi->edit($voiceId, array(
			'recommend'=>(int)Voice::RECOMMEND_STATUS 
		));
		if ($responser->isFail()) {
			return json_encode(array(
				'result'=>false, 
				'message'=>__('The operation is to fail') . $responser->getMessage() 
			));
		} else {
			return json_encode(array(
				'result'=>true, 
				'message'=>__('The operation has finished in successfully') 
			));
		}
	}
	public function tag($id = '', $index) {
		$query['page'] = 1;
		$query['limit'] = 100;
		$voice_responser = $this->VoiceApi->view($id);
		if (! $voice_responser->isFail()) {
			$voice = $voice_responser->getData();
			$this->set('voice', $voice);
		}
		$responser = $this->TagApi->index($query);
		if (! $responser->isFail()) {
			$tags = $responser->getData();
			$datas = $tags['items'];
			$data = $this->_uniqueByKey($datas, 'category');
			foreach ($data as $k=>$v) {
				foreach ($datas as $key=>$val) {
					if (isset($val['category']) && isset($v['category'])) {
						if ($v['category'] == $val['category']) {
							$data[$k]['tag'][] = $val;
						}
					}
				}
			}
		}
		
		$this->request->data['voices']['id'] = $id;
		$this->set('index', $index);
		$this->set('data', $data);
	}
	/**
	 * edit voice tags
	 */
	public function adminEdit() {
		$this->autoLayout = false;
		$this->autoRender = false;
		if ($this->request->is('post')) {
			$request = $this->request->data('voices');
			$responser = $this->VoiceApi->view($request['id']);
			
			if (! $responser->isFail()) {
				$voice = $responser->getData();
				
				$responser = $this->VoiceApi->edit($request['id'], array(
					'user_id'=>$voice['user_id'], 
					'tags'=>$request['tags'] 
				));
			}
			$response = new \Controller\Response\Response();
			return $responser->isFail() ? $response->message(FALSE, $responser->getMessage()) : $response->message(TRUE, __('保存成功'));
		}
	}
	/**
	 * Remove duplicate values ​​in a two-dimensional array
	 *
	 * @param array $arr        	
	 * @param string $key        	
	 * @return multitype:
	 */
	private function _uniqueByKey($arr, $key) {
		$tmp_arr = array();
		foreach ($arr as $k=>$v) {
			if (! isset($v[$key])) {
				unset($arr[$k]);
			}
			if (isset($v[$key])) {
				if (in_array($v[$key], $tmp_arr)) {
					unset($arr[$k]);
				} else {
					$tmp_arr[] = $v[$key];
				}
			}
		}
		$arr = array_values($arr);
		
		return $arr;
	}
}