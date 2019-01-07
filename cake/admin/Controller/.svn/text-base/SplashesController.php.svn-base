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
// App::uses('Withdrawal', 'Model');
/**
 * FishSaying Controller
 *
 * Handle events posted from fishsaying server.
 *
 * @package		app.Controller
 */
class SplashesController extends AppController
{
    public $name = 'Splashes';
    
    public $layout = 'fishsaying';
    
    public $components = array(
        'QiNiu.Upload', 'QiNiu.Path', 
    	'ConfigureApi' => array(
    		'className' => 'FishSayingApi.Configure'
    	),
    );
    
    public $uses = array('Splash');
    
/**
 * (non-PHPdoc)
 * @see Controller::beforeFilter()
 */
    public function beforeFilter() {
    	parent::beforeFilter();
    }
    
    public function add() {
    	if($this->request->is('POST')) {
    		$file = $this->request->data('Splash.image.tmp_name');
    		try {
	    		$key  = $this->Upload->splash($file);
	    		$url  = $this->Path->splash($key);
	    		$resp = $this->ConfigureApi->edit(array(
	    			'splash' => $url
	    		));
	    		if($resp->isFail()) {
	    			$this->Session->setFlash(__('提交失败： ').$resp->getMessage());
	    		} else {
	    			$this->Session->setFlash(__('提交成功'));
	    		}
    		} catch (Exception $e) {
    			$this->Session->setFlash(__('提交失败： ').$resp->getMessage());
    		}
    	}
    	$resp = $this->ConfigureApi->index();
    	if($resp->isFail()) $this->Session->setFlash(__('访问API失败，').$resp->getMessage());
    	$this->set('splash', Hash::get($resp->getData(), 'splash'));
    }
}