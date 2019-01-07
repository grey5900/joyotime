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
/**
 * @package		app.Controller
 */
class TagsController extends AppController
{
    public $name = 'Tags';
    
    public $layout = 'fishsaying';
    
    public $components = array();
    
    public $uses = array('Tag');
    
/**
 * (non-PHPdoc)
 * @see Controller::beforeFilter()
 */
    public function beforeFilter() {
    	parent::beforeFilter();
    }
    
    public function index() {
        
    }

    public function add() {
        
    }
    
    public function visble() {
        
    }
}