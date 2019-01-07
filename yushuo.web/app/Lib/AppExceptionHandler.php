<?php
APP::uses('CakeResponse', 'Network');
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
class AppExceptionHandler 
{
	public static function handle($error) {
	    $code = $error->getCode();
	    $message = $error->getMessage();
	    
	    switch ($code) {
	    	case 404:
	    		break;
	    }
	    
		
// 		try {
// 		    $resp->statusCode($code);
// 		} catch(Exception $e) {
// 		    $code = 500;
// 		    $resp->statusCode($code);
// 		}
		CakeLog::debug("[$code] $message");
		pr($error);
// 		$resp->body(json_encode(array('code' => $code, 'message' => $message)));
// 		$resp->send();
		return false;
	}
}