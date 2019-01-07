<?php
/**
 * The project of FenPay is a CRM platform based on Weixin MP API.
 *
 * Use it to communicates with Weixin MP.
 *
 * PHP 5
 *
 * FenPay(tm) : FenPay (http://fenpay.com)
 * Copyright (c) in.chengdu.cn. (http://in.chengdu.cn)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) in.chengdu.cn. (http://in.chengdu.cn)
 * @link          http://fenpay.com FenPay(tm) Project
 * @since         FenPay(tm) v 0.0.1
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
APP::uses('AppController', 'Controller');
/**
 * Define expire time of cookie.
 * 
 * @var int
 */
define('COOKIE_EXPIRE', 365 * 24 * 3600);    // unit is second.

/**
 * User Controller
 *
 * Handle login/logout and permission control of user.
 *
 * @package		app.Controller
 */
class UsersController extends AppController {
    
    public $name = 'UsersController';
    
    public $autoRender = false;
    public $autoLayout = false;
    
    public $uses = array(
        'User'
    );
    
    public $components = array(
        'Cookie'
    );
    
    public function beforeFilter() {
    	parent::beforeFilter();
    	$this->Auth->allow('login', 'test');
//     	$this->Cookie = $this->Components->load('Cookie');
    }
    
/**
 * Test account is bob/pppp
 */
    public function login() {
        // if user input login info, check it first...
        if($this->request->is('post')) {
            if($this->Auth->login()) {
                if(!empty($this->data)) {
                	if(empty($this->data['User']['remember_me'])) {
                	    // If user don't selected checkbox named remember me, 
                	    // Clean all stored auth data in Cookie.
                		$this->Cookie->destroy('Auth.User');
                	} else {
                	    // If user login success and selected checkbox named remember me,
                	    // Save login info into Cookie.
                	    // The Cookie's expire time is defined in core.php
                	    // See Session.cookieTimeout
                		$user = $this->Auth->user();
                		
                	    $cookie = array();
                		$cookie = $user;
                		$this->Cookie->write('Auth.User', $cookie, true, COOKIE_EXPIRE);
//                 		unset($this->data['User']['remember_me']);
                	}
                }
                return $this->redirect($this->Auth->redirect());
            } else {
                $this->Session->setFlash('用户名不存在/密码错误');
                return $this->redirect('/pages/login');
            }
        }
        // If there is without input data, 
        // then will check data from Cookie.
        $cookie = $this->Cookie->read('Auth.User');
        if (!is_null($cookie) && isset($cookie['username']) && isset($cookie['id'])) {
        	if ($this->Auth->login($cookie)) {
        		return $this->redirect($this->Auth->redirect());
        	} else {
        		$this->Session->setFlash('用户名或密码错误，或长时间未登录，请重新登录');
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
}