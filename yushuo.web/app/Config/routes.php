<?php
/**
 * Routes configuration
 *
 * In this file, you set up routes to your controllers and their actions.
 * Routes are very important mechanism that allows you to freely connect
 * different urls to chosen controllers and their actions (functions).
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
 * @package       app.Config
 * @since         CakePHP(tm) v 0.2.9
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
define('RULE_SHORT_ID', '[a-zA-Z0-9]{5,7}+');
define('RULE_LANG', '[a-zA-Z]{3}+');
/**
 * Here, we are connecting '/' (base path) to controller called 'Pages',
 * its action called 'display', and we pass a param to select the view file
 * to use (in this case, /app/View/Pages/home.ctp)...
 */
	Router::connect('/', array('controller' => 'pages', 'action' => 'display', 'index'));
    
	Router::connect('/terms', array('controller' => 'pages', 'action' => 'terms'));
	Router::connect('/agree', array('controller' => 'pages', 'action' => 'agree'));
    
	Router::connect('/support', array('controller' => 'pages', 'action' => 'support'));
	Router::connect('/:short_id',
		array('controller' => 'pages', 'action' => 'redirectTo', '[method]' => 'GET'),
		array(
			'short_id' => RULE_SHORT_ID,
			'pass' => array('short_id')
		)
	);
	Router::connect('/voice', array('controller' => 'pages', 'action' => 'voice'));
	Router::connect('/voice/:short_id',
		array('controller' => 'pages', 'action' => 'voice', '[method]' => 'GET'),
		array(
			'short_id' => RULE_SHORT_ID,
			'pass' => array('short_id')
		)
	);
	Router::connect('/contact', array('controller' => 'pages', 'action' => 'contact'));
	Router::connect('/langs/:lang_name',
		array('controller' => 'langs', 'action' => 'setLang', '[method]' => 'GET'),
		array(
			'lang_name' => RULE_LANG,
			'pass' => array('lang_name')
		)
	);
    
/**
 * ...and connect the rest of 'Pages' controller's urls.
 */
// 	Router::connect('/pages/*', array('controller' => 'pages', 'action' => 'display'));

/**
 * Load all plugin routes. See the CakePlugin documentation on
 * how to customize the loading of plugin routes.
 */
	CakePlugin::routes();

/**
 * Load the CakePHP default routes. Only remove this if you do not want to use
 * the built-in default routes.
 */
	require CAKE . 'Config' . DS . 'routes.php';
