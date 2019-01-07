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
App::uses('Tag', 'Model');
/**
 *
 * @package app.Controller
 */
class TagsController extends AppController {
	public $name = 'Tags';
	public $layout = 'fishsaying';
	public $uses = array(
			'Tag' 
	);
	public $components = array(
			'TagApi'=>array(
					'className'=>'FishSayingApi.Tag' 
			),
			'ConnectApi'=>array(
					'className'=>'FishSayingApi.Connect' 
			) 
	);
	/**
	 * (non-PHPdoc)
	 *
	 * @see Controller::beforeFilter()
	 */
	public function beforeFilter() {
		parent::beforeFilter();
	}
	public function index($category = '') {
		$limit = 20;
		$items = array();
		$total = 0;
		$query = array();
		$query['page'] = $this->request->query('page');
		$query['limit'] = $limit;
		// $category = $this->request->query('category');
		if ($category) {
			$query['category'] = $category;
		}
		$this->paginate = array(
				'limit'=>$limit,
				'paramType'=>"querystring" 
		);
		$responser = $this->TagApi->index($query);
		if (! $responser->isFail()) {
			$tags = $responser->getData();
			$this->redirectPage($tags,$limit);
			$this->Tag->results = $tags['items'];
			$this->Tag->count = $total = $tags['total'];
			$items = $this->paginate('Tag');
		}
		$this->set("category",$category);
		$this->set('items', $items);
		$this->set('total', $total);
		$this->set('kw', $category);
	}
	public function add($category='') {
		if ($this->request->is('post')) {
			$this->autoLayout = false;
			$this->autoRender = false;
			$result = $this->TagApi->add($this->request->data['tag']);
			$token = $this->ConnectApi->token();
			$response = new \Controller\Response\Response();
			return $result->isFail() ? $response->message(FALSE, $result->getMessage()) : $response->message(TRUE, __('保存成功'));
		}
		if($category){
			$this->request->data['tag']['category'] = $category;
		}
		
		$this->set("category",$category);
		$this->set('required', true);
		$this->set('token', $this->ConnectApi->token());
	}
	public function edit($id) {
		if ($this->request->is('post')) {
			$this->autoLayout = false;
			$this->autoRender = false;
			$result = $this->TagApi->edit($id, $this->request->data['tag']);
			$token = $this->ConnectApi->token();
			$response = new \Controller\Response\Response();
			return $result->isFail() ? $response->message(FALSE, $result->getMessage()) : $response->message(TRUE, __('保存成功'));
		}
		$this->set('required', true);
		$this->set('token', $this->ConnectApi->token());
		$responser = $this->TagApi->view($id);
		
		if (! $responser->isFail()) {
			$tag = $responser->getData();
			
			$this->request->data['tag'] = $tag;
			$this->set("category",$tag['category']);
			
		}
		$this->set("category",'');
		$this->set("type","edit");
		$this->render('add');
	}
	public function delete($tag_id) {
		$this->autoLayout = false;
		$this->autoRender = false;
		$responser = $this->TagApi->delete($tag_id);
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
}