<?php
/**
 * Application level Controller
 *
 * This file is application-wide controller file. You can put all
 * application-wide controller-related methods here.
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
App::uses('ExceptionRenderer', 'Error');

class AppExceptionRenderer extends ExceptionRenderer 
{
	public function __construct(Exception $exception) {
		parent::__construct($exception);
		$code = $exception->getCode();
		switch($code) {
			case 404:
				$this->method = 'notFound';
				break;
		}
	}
	
	public function notFound($error) {
		$this->controller->redirect(array('controller' => 'errors', 'action' => 'error404'));
	}
}