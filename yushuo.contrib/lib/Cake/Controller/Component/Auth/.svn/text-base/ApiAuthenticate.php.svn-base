<?php
/**
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
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

App::uses('BaseAuthenticate', 'Controller/Component/Auth');
App::uses('AuthenticateComponent', 'Plugin/FishSayingApi/Controller/Component');

/**
 * An authentication adapter for AuthComponent. Provides the ability to authenticate using POST
 * data. Can be used by configuring AuthComponent to use it via the AuthComponent::$authenticate setting.
 *
 * {{{
 *	$this->Auth->authenticate = array(
 *		'Remote' => array(
 *			'scope' => array('User.active' => 1)
 *		)
 *	)
 * }}}
 *
 * When configuring FormAuthenticate you can pass in settings to which fields, model and additional conditions
 * are used. See FormAuthenticate::$settings for more information.
 *
 * @package       Cake.Controller.Component.Auth
 * @since 2.0
 * @see AuthComponent::$authenticate
 */
class ApiAuthenticate extends BaseAuthenticate {

/**
 * Authenticates the identity contained in a request. Will use the `settings.userModel`, and `settings.fields`
 * to find POST data that is used to find a matching record in the `settings.userModel`. Will return false if
 * there is no post data, either username or password is missing, or if the scope conditions have not been met.
 *
 * @param CakeRequest $request The request that contains login information.
 * @param CakeResponse $response Unused response object.
 * @return mixed False on login failure. An array of User data on success.
 */
	public function authenticate(CakeRequest $request, CakeResponse $response) {
	    try {
    	    $api = new AuthenticateComponent($this->_Collection);
    	    $fields = $this->settings['fields'];
    	    return $api->login(
    	            $request->data[$fields['username']], 
    	            $request->data[$fields['password']]);
	    } catch(Exception $e) {
	        CakeLog::write('API_LOG', $e->getMessage());
	        return false;
	    }
	}
}
