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
 * @package     app.Controller
 */
class UsersController extends AppController {
    
    public $name = 'Users';
    
    public $autoRender = false;
    public $autoLayout = false;
    
/**
 * @var CookieComponent
 */
    public $Cookie;
    
    public $components = array(
        'UserApi' => array(
            'className' => 'FishSayingApi.User'
        ), 
        'BroadcastApi' => array(
            'className' => 'FishSayingApi.Broadcast'
    ));
    
    public $uses = array('User');
    
/**
 * (non-PHPdoc)
 * @see Controller::beforeFilter()
 */
    public function beforeFilter() {
    	parent::beforeFilter();
    	$this->Auth->allow('login');
    	$this->Cookie = $this->Components->load('Cookie');
    }

    public function login() {
        // if user input login info, check it first...
        if($this->request->is('post')) {
            if($this->Auth->login()) {
                if(!empty($this->data)) {
                    if(empty($this->data['remember_me'])) {
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
//                         unset($this->data['remember_me']);
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
        if(!is_null($cookie) && isset($cookie['username'])) {
            if($this->Auth->login($cookie)) {
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
    	switch(strtolower($language)) {
    		case 'zh_cn':
    			$this->Session->write('Config.language', 'chn');
    			break;
    		default:
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
        
        $limit = 20;
        $items = array();
        $total = 0;
        
        $query = array(
            'page' => $this->request->query('page'),
            'limit' => $limit,
        );
        if($username) {
        	$query = am($query, array('username' => $username));
        }
        $this->paginate = array(
    		'limit' => $limit,
    		'paramType' => "querystring",
        );
        $responser = $this->UserApi->index($query);
        if(!$responser->isFail()) {
        	$cos = $responser->getData();
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
    
    public function send_message($userId) {
        $responser = $this->BroadcastApi->add(array(
    		'message' => $this->request->query('message'),
    		'user_id' => $userId
        ));
        if(!$responser->isFail()) {
        	return $this->resp(TRUE, __('消息发送成功'));
        }
        return $this->resp(FALSE, __('消息发送失败').' '.$responser->getMessage());
    }
    
    public function send_gift($userId) {
        $responser = $this->BroadcastApi->add(array(
            'message' => $this->request->query('message'),
            'seconds' => $this->request->query('minutes') * 60,
            'user_id' => $userId
        ));
        
        if(!$responser->isFail()) {
            return $this->resp(TRUE, __('赠送成功'));
        }
        return $this->resp(FALSE, __('赠送失败').' '.$responser->getMessage());
    }
}
