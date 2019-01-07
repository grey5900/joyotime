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
App::uses('Coupon', 'Model');
/**
 *
 * @package app.Controller
 */
class CouponsController extends AppController {
	public $name = 'Coupons';
	public $layout = 'fishsaying';
	public $uses = array(
			'Coupon' 
	);
	public $components = array(
			'CouponApi'=>array(
					'className'=>'FishSayingApi.Coupon' 
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
	public function index() {
		$limit = 20;
		$items = array();
		$total = 0;
		$query = array();
		$query['page'] = $this->request->query('page');
		$query['limit'] = $limit;
		
		$this->paginate = array(
				'limit'=>$limit,
				'paramType'=>"querystring" 
		);
		$responser = $this->CouponApi->index($query);
		if (! $responser->isFail()) {
			$coupons = $responser->getData();
			$this->Coupon->results = $coupons['items'];
			$this->Coupon->count = $total = $coupons['total'];
			$items = $this->paginate('Coupon');
		}
		$this->set('role',$this->Session->read('Auth.User.role'));
		$this->set('items', $items);
		$this->set('total', $total);
		$this->set('kw', '');
	}
	public function add() {
		if ($this->request->is('post')) {
			$this->autoLayout = false;
			$this->autoRender = false;
			$this->request->data['coupon']['length'] = $this->request->data['coupon']['length']*60;
			$this->request->data['coupon']['expire'] = strtotime($this->request->data['coupon']['expire']);
			$result = $this->CouponApi->add($this->request->data['coupon']);
			$token = $this->ConnectApi->token();
			$response = new \Controller\Response\Response();
			return $result->isFail() ? $response->message(FALSE, $result->getMessage()) : $response->message(TRUE, __('保存成功'));
		}
		
		$this->set("type", "add");
		$this->set('required', true);
		$this->set('token', $this->ConnectApi->token());
	}
	public function edit($id) {
		if ($this->request->is('post')) {
			$this->autoLayout = false;
			$this->autoRender = false;
			unset($this->request->data['coupon']['number']);
			unset($this->request->data['coupon']['length']);
			$this->request->data['coupon']['expire'] = strtotime($this->request->data['coupon']['expire']);
			$result = $this->CouponApi->edit($id, $this->request->data['coupon']);
			$token = $this->ConnectApi->token();
			$response = new \Controller\Response\Response();
			return $result->isFail() ? $response->message(FALSE, $result->getMessage()) : $response->message(TRUE, __('保存成功'));
		}
		$this->set('required', true);
		$this->set('token', $this->ConnectApi->token());
		$responser = $this->CouponApi->view($id);
		if (! $responser->isFail()) {
			$coupon = $responser->getData();
			// echo "<pre>";
			//print_r($coupon);exit;
			$this->request->data['coupon'] = $coupon;
			$this->request->data['coupon']['length'] = $coupon['length']/60;
			
		}
		
		$this->set("type", "edit");
		$this->render('add');
	}
	/**
	 * coupon export csv
	 */
	public function export($id) {
		$this->ConnectApi->token();
		$this->autoLayout = false;
		$this->autoRender = false;
		ob_clean();
		$responser = $this->CouponApi->view($id);
		if (! $responser->isFail()) {
			$coupon = $responser->getData();
			foreach ($coupon['codes'] as $key=>$val) {
				$data[$key]['code'] = $val['code'];
				$data[$key]['link'] = $this->Session->read('Api.Token.web_host').'coupon'.DS. $val['code'];
			}
		} else {
			// @todo
			$this->Session->setFlash(__('导出失败'));
			$this->redirect(array('action' => 'index'));
			return;
		}
		$filename = date('YmdHis') . '.csv';
		header('Content-Type: text/csv');
		header('Content-Disposition: attachment;filename=' . $filename);
		$fp = fopen('php://output', 'w');
		fwrite($fp, "\xEF\xBB\xBF");
		foreach ($data as $fields) {
			fputcsv($fp, $fields);
		}
		fclose($fp);
	}
	/**
	 * charge detail
	 */
	public function detail($id) {
		$responser = $this->CouponApi->view($id);
		if (! $responser->isFail()) {
			$coupon = $responser->getData();
		}
		$this->set('items', $coupon);
	}
}