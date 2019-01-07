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
 * Redirect controller
 *
 * @package       app.Controller
 */
class RController extends AppController {

/**
 * Controller name
 *
 * @var string
 */
	public $name = 'R';

/**
 * @var array
 */
	public $uses = array();
	public $components = array(
			'VoiceApi'=>array(
					'className'=>'FishSayingApi.Voice'
			)
	);
	public $autoRender = false;
	public $autoLayout = false;
	
	/**
	 * Download routor
	 */
	public function dl() {
		$userAgent = $this->request->header('User-Agent');
		
		if(stristr($userAgent, 'Android')) {
			if(stristr($userAgent, 'MicroMessenger')) {
				$this->redirect('/');
			} else {
				$this->redirect(Configure::read('Download.android'));
			}
		} else if(stristr($userAgent, 'iPhone')) {
			$this->redirect(Configure::read('Download.ios'));
		} else if(stristr($userAgent, 'iPad')) {
			$this->redirect(Configure::read('Download.ios'));
		} else if(stristr($userAgent, 'iPod')) {
			$this->redirect(Configure::read('Download.ios'));
		} else {
			$this->redirect('/');
		}
	}
	
/**
 * Router for play link
 * 
 * @param string $voice The name of voice
 */
	public function play($voice) {
		$url = urldecode($this->request->query('url'));
		if(!$url) return false;
		$counter = new \Model\Counter\Voice();
		$counter->increase($voice);
		$this->redirect($url);
		exit;
	}
	/**
	 * 二维码跳转
	 *
	 * @param string $code
	 */
	public function coupon($code) {
		$this->autoRender = true;
		$this->log($this->request->header('User-Agent'), 'user_agent');
		$this->set('code', $code);
	}
	/**
	 * 解说跳转
	 *
	 * @param string $shortId
	 */
	public function voice($shortId) {
		$this->autoRender = true;
		$this->log($this->request->header('User-Agent'), 'user_agent');
		$this->set('shortId', $shortId);
		$this->set('hash', $this->request->query('hash'));
	}
	/**
	 * 解说包跳转
	 *
	 * @param string $packageId
	 */
	public function package($packageId) {
		$this->autoRender = true;
		$this->log($this->request->header('User-Agent'), 'user_agent');
		$this->set('packageId', $packageId);
	}
	/* 用户跳转
	*
	* @param string $userId
	*/
	public function user($userId) {
		$this->autoRender = true;
		$this->log($this->request->header('User-Agent'), 'user_agent');
		$this->set('userId', $userId);
	}
}
