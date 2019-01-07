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
class FeedbacksController extends AppController {
	
	/**
	 * Controller name
	 *
	 * @var string
	 */
	public $name = 'Feedbacks';
	
	/**
	 * This controller does not use a model
	 *
	 * @var array
	 */
	public $uses = array();
	public $components = array(
			'FeedbackApi'=>array(
					'className'=>'FishSayingApi.Feedback' 
			) 
	);
	
	/**
	 * 用户提交联系我们
	 *
	 * @param string $content  not null
	 * @param string $contact       	
	 */
	public function post() {
		$this->autoLayout = false;
		$this->autoRender = false;
		if ($this->request->is('post')){
			$content = $this->request->data['content'];
			$contact = $this->request->data['contact'];
			$responser = $this->FeedbackApi->add(array(
					'content'=>$content,
					'contact'=>$contact 
			));
			$response = new \Controller\Response\Response();
			return $responser->isFail() ? $response->message(FALSE, $responser->getMessage()) : $response->message(TRUE, __('操作成功'));
		}
	}
}
