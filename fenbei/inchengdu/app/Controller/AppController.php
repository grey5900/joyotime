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
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
App::uses('Controller', 'Controller');

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
    );
    
/**
 * Wrap response information
 * @param boolean $result
 * @param array $message
 * @return string json
 */
    protected function resp($result, $message = array()) {
    	$this->response->type('json');
    	$message['result'] = $result;
    	return json_encode($message);
    }
    
/**
 * Get first error message after validated.
 * 
 * @param Model $model
 * @return array Message content list
 */
    protected function errorMsg(Model $model) {
        $errors = isset($model->validationErrors) ? $model->validationErrors : array();
        $messages = array();
        if($errors) {
            foreach($errors as $type => $error) {
                if(is_array($error) && !isset($error[0])) {
                    foreach($error as $field => $err) {
                        if(!isset($err[0])) continue;
                        $messages[] = $err[0];
                    }
                } else {
                    $messages[] = $error[0];
                }
            }
        }
        return empty($messages) ? '' : $messages[0];
    }
}
