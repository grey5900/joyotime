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
App::uses('Controller', 'Controller');
App::uses('ApiAuthenticate', 'Controller/Component/Auth');

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @package		app.Controller
 * @link		http://book.cakephp.org/2.0/en/controllers.html#the-app-controller
 */
class AppController extends Controller {

    public $components = array(
        'Session',
        'Auth' => array(
            'loginRedirect' => array(
                'controller' => 'voices',
                'action' => 'index' 
            ),
            'logoutRedirect' => array(
                'controller' => 'pages',
                'action' => 'login' 
            )
        )
    );
    
/**
 * (non-PHPdoc)
 * @see Controller::beforeFilter()
 */
	public function beforeFilter() {
	    $this->Auth->allow('login');
        $this->Auth->authenticate = array('Api');
        $this->set('title_for_layout', '鱼说');
        
        // set language...        
        if($this->Session->check('Config.language')) {
        	$lang = $this->Session->read('Config.language');
        	Configure::write('Config.language', $lang);
        } else {
        	Configure::write('Config.language', 'chn');
        }
	}

/**
 * Defined helpers which want to use in whole app.
 * 
 * If you want to re-define in any sub controllers, please 
 * re-define whole helpers array. otherwise load helper on-the-fly 
 * like below,
 * 
 * $this->helpers[] = 'Custom';    // the help name you want to use 
 * 
 * @var array
 */
    public $helpers = array(
        'Session',
        'Html' => array(
            'className' => 'TwitterBootstrap.BootstrapHtml' 
        ),
        'Form' => array(
            'className' => 'TwitterBootstrap.BootstrapForm' 
        ),
        'Paginator' => array(
            'className' => 'TwitterBootstrap.BootstrapPaginator' 
        ),
        'Js' => array('Jquery')
    );

}

