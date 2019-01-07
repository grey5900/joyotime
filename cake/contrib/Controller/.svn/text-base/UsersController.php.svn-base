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
APP::uses('AppController', 'Controller');

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
 * (non-PHPdoc)
 * @see Controller::beforeFilter()
 */
    public function beforeFilter() {
    	parent::beforeFilter();
    	$this->Auth->allow('login');
    }

    public function login() {
    	
        // if user input login info, check it first...
        if($this->request->is('post')) {
            if($this->Auth->login()) {
                if(!empty($this->data)) {
                    $user = $this->Auth->user();
                }
                return $this->redirect($this->Auth->redirect());
            } else {
                $this->Session->setFlash('用户名不存在/密码错误');
                return $this->redirect('/pages/login');
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
    	$this->Session->destroy();
    	return $this->redirect($this->Auth->logout());
    }
}
