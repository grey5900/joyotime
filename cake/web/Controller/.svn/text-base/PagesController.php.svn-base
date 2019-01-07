<?php
/**
 * Static content controller.
 *
 * This file will render views from views/pages/
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Controller
 * @since         CakePHP(tm) v 0.2.9
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
App::uses('AppController', 'Controller');

/**
 * Static content controller
 *
 * Override this controller by placing a copy in controllers directory of an application
 *
 * @package app.Controller
 * @link http://book.cakephp.org/2.0/en/controllers/pages-controller.html
 */
class PagesController extends AppController {
	
	/**
	 * Controller name
	 *
	 * @var string
	 */
	public $name = 'Pages';
	/**
	 * This controller does not use a model
	 *
	 * @var array
	 */
	public $uses = array();
	public $components = array(
			'VoiceApi'=>array(
					'className'=>'FishSayingApi.Voice' 
			),
			'PasswordApi'=>array(
					'className'=>'FishSayingApi.Password' 
			),
			'ConnectApi'=>array(
					'className'=>'FishSayingApi.Connect' 
			) 
	);
	
	/**
	 *
	 * @var string
	 */
	public $layout = 'fishsaying2_0';
	
	/**
	 * Displays a view
	 *
	 * @param
	 *        	mixed What page to display
	 * @return void
	 * @throws NotFoundException When the view file could not be found
	 *         or MissingViewException in debug mode.
	 */
	public function display() {
		$this->layout = 'fishsaying_v3.0';
		$path = func_get_args();
		
		$count = count($path);
		if (! $count){
			return $this->redirect('/');
		}
		$page = $subpage = $title_for_layout = null;
		
		if (! empty($path[0])){
			$page = $path[0];
		}
		if (! empty($path[1])){
			$subpage = $path[1];
		}
		if (! empty($path[$count - 1])){
			$title_for_layout = Inflector::humanize($path[$count - 1]);
		}
		$this->set(compact('page', 'subpage', 'title_for_layout'));
		
		try{
			$this->render(implode('/', $path));
		}catch ( MissingViewException $e ){
			if (Configure::read('debug')){
				throw $e;
			}
			throw new NotFoundException();
		}
	}
	public function contact() {
		$this->layout = 'fishsaying_contact';
	}
	
	/**
	 * It's terms of services page uses in APP client side.
	 */
	public function terms() {
		$this->autoLayout = false;
	}
	public function agree() {
		$this->autoLayout = false;
	}
	public function support() {
		$this->redirect('/');
	}
	
	/**
	 * Voice player page
	 *
	 * @param string $shortId        	
	 */
	public function voice($shortId) {
		$this->ConnectApi->token();
		$this->layout = 'fishsaying_voice';
		$resp = $this->VoiceApi->view($shortId);
		if ($resp->isFail()){
			throw new NotFoundException();
		}
		$this->set('hash', $this->request->query('hash'));
		$this->set('voice', $resp->getData());
	}
	
	// public function fishsaying2_0() {
	// $this->layout = 'fishsaying2_0';
	// }
	public function reset() {
		$this->layout = 'fishsaying';
		if ($this->request->is('post')){
			$this->autoLayout = false;
			$this->autoRender = false;
			
			$pwd1 = $this->request->data('pwd1');
			$pwd2 = $this->request->data('pwd2');
			$email = $this->request->data('email');
			$expire = $this->request->data('expire');
			$hash = $this->request->data('hash');
			
			if ($pwd1 == $pwd2){
				$resp = $this->PasswordApi->edit(array(
						'email'=>$email,
						'expire'=>$expire,
						'hash'=>$hash,
						'password'=>$pwd1 
				));
				if ($resp->isFail()){
					return $this->fail($resp->getMessage());
				}else{
					return $this->success(array(
							'message'=>__("密码重置完成！你现在可以使用新密码登录鱼说了！") 
					));
				}
			}else{
				return $this->fail(__('两次密码输入不一致'));
			}
		}
		$this->set('email', $this->request->query('email'));
		$this->set('expire', $this->request->query('expire'));
		$this->set("logo", __('logo_cn'));
		$this->set('hash', $this->request->query('hash'));
		$this->render('reset');
	}
}
