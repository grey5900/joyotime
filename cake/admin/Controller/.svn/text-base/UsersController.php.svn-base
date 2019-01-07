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
App::uses('User', 'Model');
/**
 * Define expire time of cookie.
 *
 * @var int
 */
define('COOKIE_EXPIRE', 365 * 24 * 3600); // unit is second.

/**
 * User Controller
 *
 * Handle login/logout and permission control of user.
 *
 * @package app.Controller
 */
class UsersController extends AppController {
	public $name = 'Users';
	public $autoRender = false;
	public $autoLayout = false;
	
	/**
	 *
	 * @var CookieComponent
	 */
	public $Cookie;
	public $components = array(
			'UserApi'=>array(
					'className'=>'FishSayingApi.User' 
			),
			'BroadcastApi'=>array(
					'className'=>'FishSayingApi.Broadcast' 
			),
			'NotificationApi'=>array(
					'className'=>'FishSayingApi.Notification' 
			),
			'RoleApi'=>array(
					'className'=>'FishSayingApi.Role' 
			),
			'UserRecommendApi'=>array(
					'className'=>'FishSayingApi.UserRecommend' 
			) 
	);
	public $uses = array(
			'User' 
	);
	
	/**
	 * (non-PHPdoc)
	 *
	 * @see Controller::beforeFilter()
	 */
	public function beforeFilter() {
		parent::beforeFilter();
		$this->Auth->allow('login');
		$this->Cookie = $this->Components->load('Cookie');
	}
	public function login() {
		// if user input login info, check it first...
		if ($this->request->is('post')) {
			if ($this->Auth->login()) {
				if (! empty($this->data)) {
					if (empty($this->data['remember_me'])) {
						// If user don't selected checkbox named remember me,
						// Clean all stored auth data in Cookie.
						$this->Cookie->destroy('Auth.User');
					} else {
						// If user login success and selected checkbox named remember me,
						// Save login info into Cookie.
						// The Cookie's expire time is defined in core.php
						// See Session.cookieTimeout
						$user = $this->Auth->user();
						$this->Cookie->write('Auth.User', $user, true, COOKIE_EXPIRE);
						// unset($this->data['remember_me']);
					}
				}
				return $this->redirect($this->Auth->redirect());
			} else {
				$this->Session->setFlash(__('Username is inexist or password wrong'));
				return $this->redirect('/pages/login');
			}
		}
		// If there is without input data,
		// then will check data from Cookie.
		$cookie = $this->Cookie->read('Auth.User');
		if (! is_null($cookie) && isset($cookie['username'])) {
			if ($this->Auth->login($cookie)) {
				return $this->redirect($this->Auth->redirect());
			} else {
				$this->Session->setFlash(__('Username/password wrong or session expired'));
			}
		}
		return $this->redirect('/pages/login');
	}
	
	/**
	 * User logout
	 *
	 * The auth info will be destoryed whatever
	 * saved in SESSION or Cookie
	 *
	 * @return void
	 */
	public function logout() {
		$this->Cookie->destroy('Auth.User');
		return $this->redirect($this->Auth->logout());
	}
	
	/**
	 * Switch language to show
	 *
	 * @param string $language        	
	 */
	public function lang($language = '') {
		switch (strtolower($language)) {
			case 'zh_cn' :
				$this->Session->write('Config.language', 'chn');
				break;
			default :
				$this->Session->write('Config.language', 'eng');
		}
		$this->redirect($this->request->referer(true));
	}
	
	/**
	 * Index page for management
	 */
	public function index($username = '') {
		$this->autoLayout = true;
		$this->autoRender = true;
		
		$this->layout = 'fishsaying';
		
		$data = $this->_getUserList($username);
		
		$this->set('items', $data['items']);
		$this->set('total', $data['total']);
		$this->set('active', 'users');
		$this->set('kw', $username);
	}
	/**
	 * user Authenticate
	 */
	function authAddList($username = '') {
		$this->autoLayout = true;
		$this->autoRender = true;
		$this->layout = 'fishsaying';
		$data = $this->_getUserList($username, array(
				'is_verified'=>User::UNVERIFIED 
		));
		$this->set('items', $data['items']);
		$this->set('total', $data['total']);
		$this->set('active', 'users');
		$this->set('kw', $username);
	}
	/**
	 * user Authenticated
	 */
	function authList($username = '') {
		$this->autoLayout = true;
		$this->autoRender = true;
		$this->layout = 'fishsaying';
		$data = $this->_getUserList($username, array(
				'is_verified'=>User::VERIFIED 
		));
		$this->set('items', $data['items']);
		$this->set('total', $data['total']);
		$this->set('active', 'users');
		$this->set('kw', $username);
	}
	private function _getUserList($username, $query_data = array()) {
		$limit = 20;
		$items = array();
		$total = 0;
		$query = array(
				'page'=>$this->request->query('page'),
				'limit'=>$limit 
		);
		if ($username) {
			$query = am($query, array(
					'username'=>$username 
			));
		}
		if ($query_data) {
			$query = array_merge($query, $query_data);
		}
		
		$this->paginate = array(
				'limit'=>$limit,
				'paramType'=>"querystring" 
		);
		//print_r($query);
		$responser = $this->UserApi->index($query);
		if (! $responser->isFail()) {
			$cos = $responser->getData();
			//dump($cos);
			$this->redirectPage($cos,$limit);
			$this->User->results = $cos['items'];
			$this->User->count = $cos['total'];
			
			$items = $this->paginate('User');
			$items = is_array($items) ? $items : array();
			$total = $cos['total'];
		} else {
			$this->Session->setFlash($responser->getMessage());
		}
		return array(
				'items'=>$items,
				'total'=>$total 
		);
	}
	/**
	 * ajax do auth
	 */
	public function authDo($userId) {
		$responser = $this->UserApi->edit($userId, array(
				'is_verified'=>User::VERIFIED,
				'verified_description'=>$this->request->query('verified_description') 
		));
		if (! $responser->isFail()) {
			return json_encode(array(
					'result'=>true,
					'message'=>__('The operation has finished in successfully'),
					'verified_description'=>$this->request->query('verified_description') 
			));
		}
		return $this->resp(FALSE, __('认证失败') . ' ' . $responser->getMessage());
	}
	/**
	 * ajax do auth
	 */
	public function authCancel($userId) {
		$responser = $this->UserApi->edit($userId, array(
				'is_verified'=>User::UNVERIFIED,
				'verified_description'=>'' 
		));
		
		if (! $responser->isFail()) {
			return json_encode(array(
					'result'=>true,
					'message'=>__('The operation has finished in successfully'),
					'verified_description'=>$this->request->query('verified_description') 
			));
		}
		return $this->resp(FALSE, __('The operation is to fail') . ' ' . $responser->getMessage());
	}
	public function send_message($userId) {
		$responser = $this->NotificationApi->add(array(
				'message'=>$this->request->query('message'),
				'user_id'=>$userId 
		));
		if (! $responser->isFail()) {
			return $this->resp(TRUE, __('消息发送成功'));
		}
		return $this->resp(FALSE, __('消息发送失败') . ' ' . $responser->getMessage());
	}
	public function send_gift($userId) {
		$responser = $this->BroadcastApi->add(array(
				'message'=>$this->request->query('message'),
				'seconds'=>$this->request->query('minutes') * 60,
				'user_id'=>$userId 
		));
		
		if (! $responser->isFail()) {
			return $this->resp(TRUE, __('赠送成功'));
		}
		return $this->resp(FALSE, __('赠送失败') . ' ' . $responser->getMessage());
	}
	public function role($userId) {
		$responser = $this->RoleApi->edit(array(
				'role'=>$this->request->query('role'),
				'user_id'=>$userId 
		));
		
		if (! $responser->isFail()) {
			return $this->resp(TRUE, __('设置成功'));
		}
		return $this->resp(FALSE, __('设置失败') . ' ' . $responser->getMessage());
	}
	public function add() {
		$responser = $this->UserApi->view("525176b96f159a1e60ddd1f2");
		print_r($responser);
		if (! $responser->isFail()) {
			return $this->resp(TRUE, __('添加成功'));
		}
		return $this->resp(FALSE, __('添加失败') . ' ' . $responser->getMessage());
	}
	/**
	 * 用户推荐列表
	 */
	function recommendList($username = '') {
		$this->autoLayout = true;
		$this->autoRender = true;
		$this->layout = 'fishsaying';
		$limit = 9999;
		$items = array();
		$total = 0;
		$query = array(
				'page'=>$this->request->query('page'),
				'limit'=>$limit
		);
		$this->paginate = array(
				'limit'=>$limit,
				'paramType'=>"querystring"
		);
		$responser = $this->UserRecommendApi->index($query);
		if (! $responser->isFail()) {
			$cos = $responser->getData();
			$this->redirectPage($cos,$limit);
			$this->User->results = $cos['items'];
			$this->User->count = $cos['total'];
				
			$items = $this->paginate('User');
			$items = is_array($items) ? $items : array();
			$total = $cos['total'];
		} else {
			$this->Session->setFlash($responser->getMessage());
		}
	
		$this->set('items', $items);
		$this->set('total', $total);
		$this->set('active', 'users');
		$this->set('kw', $username);
	}
	/**
	 * 待增加推荐用户列表
	 */
	function recommendAddList($username = '') {
		$this->autoLayout = true;
		$this->autoRender = true;
		$this->layout = 'fishsaying';
		$data = $this->_getUserList($username, array(
				'recommend'=>User::RECOMMEND,
				'is_contributor'=>User::CONTRIBUTORED,
		));
		
		$this->set('items', $data['items']);
		$this->set('total', $data['total']);
		$this->set('active', 'users');
		$this->set('kw', $username);
	}
	/**
	 * 删除已推荐用户
	 * 
	 */
	public function recommendDelete($userId) {
		$responser = $this->UserRecommendApi->edit($userId, array(
				'recommend'=>User::RECOMMEND,
				'recommend_reason'=>''
		));
	
		if (! $responser->isFail()) {
			return json_encode(array(
					'result'=>true,
					'message'=>__('The operation has finished in successfully'),
					'verified_description'=>$this->request->query('verified_description')
			));
		}
		return $this->resp(FALSE, __('The operation is to fail') . ' ' . $responser->getMessage());
	}
	/**
	 * ajax 推荐操作
	 * @param string $userId
	 * @return json
	 */
	public function recommend($userId) {
		$responser = $this->UserRecommendApi->edit($userId, array(
				'recommend'=>User::RECOMMENDED,
				'recommend_reason'=>$this->request->query('recommend_reason') 
		));
		if (! $responser->isFail()) {
			return json_encode(array(
					'result'=>true,
					'message'=>__('The operation has finished in successfully'),
					'recommend_reason'=>$this->request->query('recommend_reason') 
			));
		}
		return $this->resp(FALSE, __('推荐失败') . ' ' . $responser->getMessage());
	}
	/**
	 * 用户推荐排序
	 */
	public function recommendOrder() {
		$user_id = $this->request->query('user_id');
		$recommend_offset = $this->request->query('recommend_offset');
		$this->autoLayout = false;
		$this->autoRender = false;
		$responser = $this->UserRecommendApi->edit($user_id, array(
				'recommend_offset'=>$recommend_offset
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
	/**
	 * 
	 * 机构认证 已增加的机构列表
	 * @param string $username
	 */
	public function agencyAuth($username=''){
		$this->autoLayout = true;
		$this->autoRender = true;
		$this->layout = 'fishsaying';
		$data = $this->_getUserList($username, array(
				'is_verified'=>User::VERIFIED
		));
		$this->set('items', $data['items']);
		$this->set('total', $data['total']);
		$this->set('active', 'users');
		$this->set('kw', $username);
	}
	/**
	 * 
	 * 机构认证 待增加的机构列表
	 * @param string $username
	 */
	public function agencyAuthAdd($username=''){
		$this->autoLayout = true;
		$this->autoRender = true;
		$this->layout = 'fishsaying';
		$data = $this->_getUserList($username, array(
				'is_verified'=>User::VERIFIED
		));
		$this->set('items', $data['items']);
		$this->set('total', $data['total']);
		$this->set('active', 'users');
		$this->set('kw', $username);
	}
	/**
	 * 机构认证 认证
	 */
	public function doAgencyAuth(){
		
	}
	
	/**
	 * 机构认证 取消认证
	 */
	public function AgencyAuthCancel(){
		
	}
	/**
	 * 机构认证 编辑认证信息
	 */
	public function agencyAuthInfoEdit(){
		$this->autoLayout = true;
		$this->autoRender = true;
		$this->layout = 'fishsaying';
	
	}
}
