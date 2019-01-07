<?php
APP::uses('CakeResponse', 'Network');
APP::uses('ClassRegistry', 'Utility');
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
class AppError 
{
	public static function handleError($code, $description, $file = null, $line = null, $context = null) {
		$errorConfig = Configure::read('Error');
		list($error, $log) = ErrorHandler::mapErrorCode($code);
		$message = $error . ' (' . $code . '): ' . $description . ' in [' . $file . ', line ' . $line . ']';
		if (!empty($errorConfig['trace'])) {
			$trace = Debugger::trace(array('start' => 1, 'format' => 'log'));
			$message .= "\nTrace:\n" . $trace . "\n";
		}
		ClassRegistry::init('Error')->enqueue(
		    array('message' => $message, 'context' => self::collectData()));
		return ErrorHandler::handleError($code, $description, $file, $line, $context);
	}
	
	private static function collectData() {
	    if(strtoupper($_SERVER['REQUEST_METHOD']) == 'PUT' || 
	        strtoupper($_SERVER['REQUEST_METHOD']) == 'POST') {
	        return print_r($_SERVER, TRUE).print_r($_POST, TRUE);
	    } else {
	        return print_r($_SERVER, TRUE);
	    }
	}
}