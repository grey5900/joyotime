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
 * @package       app.Controller
 * @link http://book.cakephp.org/2.0/en/controllers/pages-controller.html
 */
class ErrorsController extends AppController {

/**
 * Controller name
 *
 * @var string
 */
	public $name = 'Errors';

/**
 * This controller does not use a model
 *
 * @var array
 */
	public $uses = array();
	
	public function beforeFilter() {
		parent::beforeFilter();
		$this->OAuth->allow($this->name, 'error');
	}
	
/**
 * Default error route
 * 
 * @param number $code
 * @param string $message
 * @return boolean
 */
	public function error($code = 500, $message = '') {
		return $this->fail($code, $message);
	}
	
/**
 * Render as 404 page...
 */
	public function error404() {
		$this->autoLayout = false;
	}
	
/**
 * Render as 500 page...
 */
	public function error500() {
		$this->autoLayout = false;
	}
}
