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
App::uses('History', 'Model');
/**
 * FishSaying Controller
 *
 * Handle events posted from fishsaying server.
 *
 * @package		app.Controller
 */
class HistoriesController extends AppController
{
    public $name = 'Histories';
    
    public $layout = 'fishsaying';
    
    public $uses = array('History');
    
    public function index() {
        $limit = 20;
        $controller = $action = '';
        $conditions = array();
        $param = '';
        
        $param = $this->request->query('action');
        if($param) {
            $pair = split('/', strtolower($param));
            $controller = $pair[0];
            $action = isset($pair[1]) ? $pair[1] : '';
            
            if($controller) {
                $conditions['controller'] = $controller;
            }
            if($action) {
                $conditions['action'] = $action;
            }
        }
        
        $this->paginate = array(
            'conditions' => $conditions,
            'order' => array('created' => 'desc'),
            'page' => $this->request->query('page'),
    		'limit' => $limit,
    		'paramType' => "querystring"
        );
        
        $this->set('items', $this->paginate('History'));
        $this->set('param', $param);
    }
}