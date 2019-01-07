<?php
/**
 * The project of FishSaying is a SNS platform which is
 * based on voice sharing for each other with journey.
 *
 * The contributor platform is used to CP create/publish costomize content.
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
 * @since         FishSaying(tm) v 1.0.0
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
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
		'ConnectApi'=>array(
			'className'=>'FishSayingApi.Connect' 
		), 
		'TagApi'=>array(
			'className'=>'FishSayingApi.Tag' 
		) 
	);
	public $uses = array(
		'Voice' 
	);
	
	/**
	 * (non-PHPdoc)
	 *
	 * @see Controller::beforeFilter()
	 */
	public function beforeFilter() {
		parent::beforeFilter();
	}
	public function index() {
		$limit = 20;
		$items = array();
		$total = 0;
		
		$query = array(
			'user_id'=>$this->Auth->user('_id'), 
			'page'=>$this->request->query('page'), 
			'limit'=>$limit 
		);
		$this->paginate = array(
			'limit'=>$limit, 
			'paramType'=>"querystring" 
		);
		$responser = $this->VoiceApi->index($query);
		//print_r($query);
		if (! $responser->isFail()) {
			$voices = $responser->getData();
		//	print_r($voices);
			$this->Voice->results = $voices['items'];
			$this->Voice->count = $total = $voices['total'];
			$items = $this->paginate('Voice');
		} else {
			$this->Session->setFlash(__('无法获取数据，') . $responser->getMessage());
		}
		$this->set('items', $items);
		$this->set('total', $total);
		
		return $this->render('index');
	}
	public function add() {
		if ($this->request->is('post')) {
			$this->autoLayout = false;
			$this->autoRender = false;
			$data = $this->request->data;
			$token = $this->ConnectApi->token();
			$data['created_from'] = Voice::CREATED_FROM;
			if (! isset($data['address']))
				$data['address'] = '未知地区';
			//echo '<pre>';
			//print_r($data);;exit;
			$result = $this->VoiceApi->add($data);
			$response = new \Controller\Response\Response();
			return $result->isFail() ? $response->message(FALSE, $result->getMessage()) : $response->message(TRUE, __('保存成功'));
		}
		
		$this->set('required', true);
		// $this->set('coverDownload', $this->QiNiu->getDomain(QiNiuComponent::BUCKET_COVER));
		// $this->set('voiceDownload', $this->QiNiu->getDomain(QiNiuComponent::BUCKET_VOICE));
		$tags = $this->getTags();
		
		$this->set('tags', $tags['tags']);
		// print_r($_SESSION);
		
		$this->set('token', $this->ConnectApi->token());
		$this->set('data_input_status', 'add');
		return $this->render('add');
	}
	private function getTags($id = 0) {
		$query['page'] = 1;
		$query['limit'] = 100;
		$voice = array();
		if ($id != 0) {
			$voice_responser = $this->VoiceApi->view($id);
			if (! $voice_responser->isFail()) {
				$voice = $voice_responser->getData();
			}
		}
		//print_r($query);
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
		
		return array(
			'tags'=>$data, 
			'voice_tag'=>$voice 
		);
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
	/**
	 * Check field of `address`
	 *
	 * @param
	 *        	data
	 */
	private function chkAddress(&$data) {
		if (! isset($data['address'])) {
			$data['address'] = '未知地区';
		}
	}
	public function edit($id = '') {
		if ($this->request->is('post')) {
			$this->autoLayout = false;
			$this->autoRender = false;
			
			$token = $this->ConnectApi->token();
			$voice = $this->request->data;
			$voice['_id'] = $id;
			
			// Set voice status to pending...
			$voice['status'] = Voice::STATUS_PENDING;
			if (empty($voice['cover']))
				unset($voice['cover']);
			if (empty($voice['voice']))
				unset($voice['voice']);
			if (empty($voice['address_components']))
				unset($voice['address_components']);
			if (empty($voice['address']))
				$voice['address'] = '未知地区';
			if(isset($voice['voice'])){
				$voice['created_from'] = Voice::CREATED_FROM;
			}
		//	print_r($voice);exit;
			$result = $this->VoiceApi->edit($id, $voice);
			$response = new \Controller\Response\Response();
			return $result->isFail() ? $response->message(FALSE, $result->getMessage()) : $response->message(TRUE, __('保存成功'));
		}
		$tags = $this->getTags($id);
		$this->set('tags', $tags['tags']);
		
		if ($tags['voice_tag']) {
			$this->set('voice', $tags['voice_tag']);
		}
		$responser = $this->VoiceApi->view($id);
		if (! $responser->isFail()) {
			$voice = $responser->getData();
			$this->request->data['voices'] = $voice;
			
			if (isset($voice['tags']) && $voice['tags']) {
				$tags = implode(',', $voice['tags']);
				$this->request->data['voices']['tags'] = $tags;
			}
			$this->request->data['voices']['latitude'] = $voice['location']['lat'];
			$this->request->data['voices']['longitude'] = $voice['location']['lng'];
			$this->request->data['voices']['address_components'] = '';
		}
		// $this->set('coverDownload', $this->QiNiu->getDomain(QiNiuComponent::BUCKET_COVER));
		// $this->set('voiceDownload', $this->QiNiu->getDomain(QiNiuComponent::BUCKET_VOICE));
		$this->set('required', false);
		$this->set('data_input_status', 'edit');
		$this->set('token', $this->ConnectApi->token());
		return $this->render('add');
	}
	public function remove($id = '') {
		$this->autoLayout = false;
		$this->autoRender = false;
		$responser = $this->VoiceApi->delete($id);
		if ($responser->isFail()) {
			return json_encode(array(
				'message'=>__('解说删除失败'), 
				'result'=>true 
			));
		} else {
			return json_encode(array(
				'message'=>__('解说删除成功'), 
				'result'=>false 
			));
		}
	}
}