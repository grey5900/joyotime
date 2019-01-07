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
APP::uses('AppController', 'Controller');
App::uses('Package', 'Model');
App::uses('Voice', 'Model');
/**
 *
 * @package app.Controller
 */
class PackagesController extends AppController {
	public $name = 'Packages';
	public $layout = 'fishsaying';
	public $components = array(
			'QiNiu.Upload',
			'QiNiu.Path',
			'PackagesApi'=>array(
					'className'=>'FishSayingApi.Package' 
			),
			'ConnectApi'=>array(
					'className'=>'FishSayingApi.Connect' 
			),
			'VoiceApi'=>array(
					'className'=>'FishSayingApi.Voice' 
			),
			'UserApi'=>array(
					'className'=>'FishSayingApi.User' 
			) 
	);
	public $uses = array(
			'Package',
			'Voice' 
	);
	/**
	 * 增加
	 *
	 * @return string
	 */
	public function add() {
		if ($this->request->is('post')) {
			$this->autoLayout = false;
			$this->autoRender = false;
			$data = $this->request->data['packages'];
			$token = $this->ConnectApi->token();
			$result = $this->PackagesApi->add($data);
			$response = new \Controller\Response\Response();
			return $result->isFail() ? $response->message(FALSE, $result->getMessage()) : $response->message(TRUE, __('保存成功'));
		}
		
		$this->set('required', true);
		$this->set('token', $this->ConnectApi->token());
		$this->set('data_input_status', 'add');
		return $this->render('add');
	}
	/**
	 * Push a voice into package
	 *
	 * @param unknown $voice_id        	
	 * @param unknown $package_id        	
	 * @return string
	 */
	public function pushVoice($voice_id, $package_id) {
		$this->autoLayout = false;
		$this->autoRender = false;
		$responser = $this->PackagesApi->pushVoice($voice_id, $package_id);
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
	 * pull a voice out of package
	 *
	 * @param unknown $voice_id        	
	 * @param unknown $package_id        	
	 * @return string
	 */
	public function pullVoice($voice_id, $package_id) {
		$this->autoLayout = false;
		$this->autoRender = false;
		$responser = $this->PackagesApi->pullVoice($voice_id, $package_id);
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
	 * delete packages
	 */
	public function delete($package_id) {
		$this->autoLayout = false;
		$this->autoRender = false;
		$responser = $this->PackagesApi->delete($package_id);
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
	 * edit
	 */
	public function edit($id = '') {
		if ($this->request->is('post')) {
			$this->autoLayout = false;
			$this->autoRender = false;
			
			$token = $this->ConnectApi->token();
			$package = $this->request->data['packages'];
			$package['_id'] = $id;
			
			// Set voice status to pending...
			$package['status'] = Package::PENDING;
			if (empty($package['cover']))
				unset($package['cover']);
			
			$result = $this->PackagesApi->edit($id, $package);
			$response = new \Controller\Response\Response();
			
			return $result->isFail() ? $response->message(FALSE, $result->getMessage()) : $response->message(TRUE, __('保存成功'));
		}
		
		$responser = $this->PackagesApi->view($id);
		if (! $responser->isFail()) {
			$package = $responser->getData();
			$this->request->data['packages'] = $package;
			$this->request->data['packages']['latitude'] = $package['location']['lat'];
			$this->request->data['packages']['longitude'] = $package['location']['lng'];
			$this->request->data['packages']['address_components'] = '';
			// print_r($this->request->data['packages']);
		}
		// $this->set('coverDownload', $this->QiNiu->getDomain(QiNiuComponent::BUCKET_COVER));
		// $this->set('voiceDownload', $this->QiNiu->getDomain(QiNiuComponent::BUCKET_VOICE));
		$this->set('required', false);
		$this->set('data_input_status', 'edit');
		// print_r($package);
		$this->set('token', $this->ConnectApi->token());
		return $this->render('add');
	}
	/**
	 * relation voice
	 */
	function relationVoice($id, $type) {
		$limit = 20;
		$items = array();
		$total = 0;
		$query = array();
		$voice_ids = array();
		$responser = $this->PackagesApi->view($id);
		if (! $responser->isFail()) {
			$package = $responser->getData();
			$this->set('package', $package);
		}
		
		if ($type == 1) {
			$this->render('relation_voice');
			return;
		}
		
		// $status = 3;
		$query['status'] = Voice::STATUS_APPROVED;
		$query['page'] = $this->request->query('page');
		$query['limit'] = $limit;
		
		$title = $this->request->query('title');
		if ($title) {
			$query['title'] = $title;
		}
		
		$this->paginate = array(
				'limit'=>$limit,
				'paramType'=>"querystring" 
		);
		
		
		$responser = $this->VoiceApi->index($query);
		if (! $responser->isFail()) {
			$voices = $responser->getData();
			
			$this->Voice->results = $voices['items'];
			$this->Voice->count = $total = $voices['total'];
			$items = $this->paginate('Voice');
		}
		foreach ($package['voices'] as $key=>$val) {
			$voice_ids[] = $val['_id'];
		}
		$this->set('voice_ids', $voice_ids);
		$this->set('items', $items);
		$this->set('total', $total);
		$this->set('kw', $title);
		$responser = $this->PackagesApi->view($id);
		if (! $responser->isFail()) {
			$package = $responser->getData();
			$this->set('package', $package);
		}
		$this->render('list_voice');
	}
	/**
	 * 处理上下架
	 */
	function status($id) {
		$this->autoLayout = false;
		$this->autoRender = false;
		$responser = $this->PackagesApi->view($id);
		if (! $responser->isFail()) {
			$package = $responser->getData();
			$data = array(
					'status'=>$package['status'] == Package::PENDING ? Package::AVALIABLE : Package::PENDING 
			);
			$responser = $this->PackagesApi->edit($id, $data);
		}
		if ($responser->isFail()) {
			return json_encode(array(
					'result'=>false,
					'status'=>$package['status'],
					'id'=>$id,
					'message'=>__('The operation is to fail') . $responser->getMessage() 
			));
		} else {
			return json_encode(array(
					'result'=>true,
					'status'=>$package['status'],
					'id'=>$id,
					// 'a'=>$package ['status'] == Package::PENDING ? Package::AVALIABLE : Package::PENDING ,
					'message'=>__('The operation has finished in successfully') 
			));
		}
	}
	public function index() {
		$limit = 20;
		$items = array();
		$total = 0;
		$title = $this->request->query('title');
		
		$query = array(
				'limit'=>$limit,
				'page'=>$this->request->query('page') 
		);
		$this->paginate = array(
				'limit'=>$limit,
				'paramType'=>"querystring" 
		);
		if ($title) {
			$query['keyword'] = $title;
		}
		
		$responser = $this->PackagesApi->index($query);
		if (! $responser->isFail()) {
			$result = $responser->getData();
			$this->redirectPage($result,$limit);
			$this->Package->results = $result['items'];
			$this->Package->count = $result['total'];
			$items = $this->paginate('Package');
			$items = is_array($items) ? $items : array();
			$total = $result['total'];
		} else {
			$this->Session->setFlash($responser->getMessage());
		}
		$this->ConnectApi->token();
		
		$this->set('webHostUrl',$this->Session->read('Api.Token.web_host'));
		$this->set('items', $items);
		$this->set('total', $total);
		$this->set('kw', $title);
	}
	
	/**
	 * upload package cover
	 */
	public function upload() {
		$this->autoLayout = false;
		$this->autoRender = false;
		if ($this->request->is('POST')) {
			$file = $this->request->data('Package.image.tmp_name');
			try {
				$key = $this->Upload->cover($file);
				echo json_encode(array(
						'result'=>TRUE,
						'file'=>$key,
						'url'=>'http://cover.fishsaying.com/' . $key . '?imageView/1/w/160/h/160/quality/90' 
				));
			} catch (Exception $e) {
				echo json_encode(array(
						'result'=>false,
						'message'=>$e->getMessage() 
				));
			}
		}
	}
	/**
	 * order voice
	 */
	public function offset() {
		$package_id = $this->request->query('package_id');
		$voice_id = $this->request->query('voice_id');
		$offset = $this->request->query('offset');
		$this->autoLayout = false;
		$this->autoRender = false;
		$responser = $this->PackagesApi->offsetVoice($voice_id, $package_id, array(
				'offset'=>$offset 
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

	function test(){
		$this->autoLayout = false;
		$this->autoRender = false;
		/**
		try{
			$this->Package->save(array('package'=>array('title'=>122)));
		}catch (ErrorException $e){
			$e->getMessage();
		}
		
		**/
		$this->autoLayout = false;
		$this->autoRender = false;
		
		$cond = array();
		
		$rows = $this->Package->find('all', array(
				'conditions'=>$cond,
				'order'=>array(
						'_id'=>'desc'
				),
				'page'=>$this->request->query('page') ?  : 1,
				'limit'=>$this->request->query('limit') ?  : 20
		));
		dump($rows);
		
		dump($this->Package->find());exit;
		$package = $this->Package->find('all', array(
				'page' => $this->request->query('page'),
				'limit' => $this->request->query('limit')?:20
		));
		$p = $this->Package->findById('53390b4967033352438b4577');
		dump($p);
	}
	
}
