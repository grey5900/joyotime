<?php
/**
 * This file is loaded automatically by the app/webroot/index.php file after core.php
 *
 * This file should load/create any application wide configuration settings, such as
 * Caching, Logging, loading additional configuration files.
 *
 * You should also use this file to include any files that provide global functions/constants
 * that your application uses.
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
 * @since         CakePHP(tm) v 0.10.8.2117
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

// Setup a 'default' cache configuration for use in the application.
Cache::config('default', array('engine' => 'File'));

/**
 * The settings below can be used to set additional paths to models, views and controllers.
 *
 * App::build(array(
 *     'Model'                     => array('/path/to/models', '/next/path/to/models'),
 *     'Model/Behavior'            => array('/path/to/behaviors', '/next/path/to/behaviors'),
 *     'Model/Datasource'          => array('/path/to/datasources', '/next/path/to/datasources'),
 *     'Model/Datasource/Database' => array('/path/to/databases', '/next/path/to/database'),
 *     'Model/Datasource/Session'  => array('/path/to/sessions', '/next/path/to/sessions'),
 *     'Controller'                => array('/path/to/controllers', '/next/path/to/controllers'),
 *     'Controller/Component'      => array('/path/to/components', '/next/path/to/components'),
 *     'Controller/Component/Auth' => array('/path/to/auths', '/next/path/to/auths'),
 *     'Controller/Component/Acl'  => array('/path/to/acls', '/next/path/to/acls'),
 *     'View'                      => array('/path/to/views', '/next/path/to/views'),
 *     'View/Helper'               => array('/path/to/helpers', '/next/path/to/helpers'),
 *     'Console'                   => array('/path/to/consoles', '/next/path/to/consoles'),
 *     'Console/Command'           => array('/path/to/commands', '/next/path/to/commands'),
 *     'Console/Command/Task'      => array('/path/to/tasks', '/next/path/to/tasks'),
 *     'Lib'                       => array('/path/to/libs', '/next/path/to/libs'),
 *     'Locale'                    => array('/path/to/locales', '/next/path/to/locales'),
 *     'Vendor'                    => array('/path/to/vendors', '/next/path/to/vendors'),
 *     'Plugin'                    => array('/path/to/plugins', '/next/path/to/plugins'),
 * ));
 *
 */

/**
 * Custom Inflector rules, can be set to correctly pluralize or singularize table, model, controller names or whatever other
 * string is passed to the inflection functions
 *
 * Inflector::rules('singular', array('rules' => array(), 'irregular' => array(), 'uninflected' => array()));
 * Inflector::rules('plural', array('rules' => array(), 'irregular' => array(), 'uninflected' => array()));
 *
 */

/**
 * Plugins need to be loaded manually, you can either load them one by one or all of them in a single call
 * Uncomment one of the lines below, as you need. make sure you read the documentation on CakePlugin to use more
 * advanced ways of loading plugins
 *
 * CakePlugin::loadAll(); // Loads all plugins at once
 * CakePlugin::load('DebugKit'); //Loads a single plugin named DebugKit
 *
 */
CakePlugin::load('TwitterBootstrap'); //Loads a single plugin named DebugKit
/**
 * You can attach event listeners to the request lifecycle as Dispatcher Filter . By Default CakePHP bundles two filters:
 *
 * - AssetDispatcher filter will serve your asset files (css, images, js, etc) from your themes and plugins
 * - CacheDispatcher filter will read the Cache.check configure variable and try to serve cached content generated from controllers
 *
 * Feel free to remove or add filters as you see fit for your application. A few examples:
 *
 * Configure::write('Dispatcher.filters', array(
 *		'MyCacheFilter', //  will use MyCacheFilter class from the Routing/Filter package in your app.
 *		'MyPlugin.MyFilter', // will use MyFilter class from the Routing/Filter package in MyPlugin plugin.
 * 		array('callable' => $aFunction, 'on' => 'before', 'priority' => 9), // A valid PHP callback type to be called on beforeDispatch
 *		array('callable' => $anotherMethod, 'on' => 'after'), // A valid PHP callback type to be called on afterDispatch
 *
 * ));
 */
Configure::write('Dispatcher.filters', array(
	'AssetDispatcher',
	'CacheDispatcher'
));

/**
 * Configures default file logging options
 */
App::uses('CakeLog', 'Log');
CakeLog::config('debug', array(
	'engine' => 'FileLog',
	'types' => array('notice', 'info', 'debug'),
	'file' => 'debug',
));
CakeLog::config('error', array(
	'engine' => 'FileLog',
	'types' => array('warning', 'error', 'critical', 'alert', 'emergency'),
	'file' => 'error',
));

/**
 * The config for upload path
 */
Configure::write('Upload.Cover.upload_url', '/files/auto_replies/covers');
Configure::write('Upload.Cover.upload_dir', WWW_ROOT.'files'.DS.'auto_replies'.DS.'covers');
/**
 * Upload from WYSIWYG editor
 */
Configure::write('Upload.Figure.upload_url', '/files/auto_replies/figures');
Configure::write('Upload.Figure.upload_dir', WWW_ROOT.'files'.DS.'auto_replies'.DS.'figures');

/**
 * Configures default auto reply messages
 */
Configure::write('AutoReplyMessage.default_message', 'Test default reply message.');
Configure::write('AutoReplyMessage.default_message_original', '/files/auto_replies/covers/default/message/original.jpg');
Configure::write('AutoReplyMessage.default_message_thumbnail', '/files/auto_replies/covers/default/message/thumbnail.jpg');
Configure::write('AutoReplyMessage.default_location_original', '/files/auto_replies/covers/default/location/original.jpg');
Configure::write('AutoReplyMessage.default_location_thumbnail', '/files/auto_replies/covers/default/location/thumbnail.jpg');

/**
 * Configures information of domain
 */
Configure::write('Domain.main', 'http://wx.joyotime.com');
/**
 * Image domain name
 */
Configure::write('Domain.image', 'http://wx.joyotime.com');

/**
 * Configures external links
 */
/**
 * The link used to redirectly go to news detail 
 * page from weixin client side.
 */
Configure::write('Exlink.news.detail', Configure::read('Domain.main').'/auto_reply_messages/news/%d');
/**
 * The link used to redirectly go to news detail 
 * page from weixin client side.
 */
Configure::write('Exlink.location.extend', Configure::read('Domain.main').'/auto_reply_locations/extend/%d');

/**
 * Configures the number of limit for each of list.
 * Regarding to the limitation of weixin api, the 
 * max is 10 for single message.
 */
/**
 * The limit of fixcode messages.
 */
Configure::write('Limit.fixcode', 10);
/**
 * The limit of news messages.
 */
Configure::write('Limit.news', 10);
/**
 * The limit of locations.
 */
Configure::write('Limit.location', 10);
/**
 * The limit of extends message of location.
 * The max limitation is 9, because there is location 
 * message.
 */
Configure::write('Limit.location.extends', 9);

/**
 * The version of static files.
 * Change the value can avoid cache on the client side.
 */
Configure::write('Css.version', '1000');
Configure::write('Js.version', '1000');

// Cache::config('memcache', array(
// 	'engine' => 'Memcache',     //[required]
// 	'duration' => 3600,         //[optional]
// 	'probability' => 100,       //[optional]
// 	'prefix' => Inflector::slug(APP_DIR) . '_', //[optional]  prefix every cache file with this string
// 	'servers' => array(
// 		'127.0.0.1:22222'         // localhost, default port 11211
// 	),                            // [optional]
// 	'persistent' => true,         // [optional] set this to false for non-persistent connections
// 	'compress' => false,          // [optional] compress data in Memcache (slower, but uses less memory)
// ));

Cache::config('default', array(
	'engine' => 'Xcache', //[required]
	'duration' => 3600, //[optional]
	'probability' => 100, //[optional]
	'prefix' => Inflector::slug(APP_DIR) . '_', //[optional] prefix every cache file with this string
	'user' => 'xcache', //user from xcache.admin.user settings
	'password' => 'xcache', //plaintext password (xcache.admin.pass)
));

Cache::config('Response.pager', array(
	'engine' => 'Xcache', //[required]
	'duration' => 3600, //[optional]
	'probability' => 100, //[optional]
	'prefix' => Inflector::slug(APP_DIR) . '_', //[optional] prefix every cache file with this string
	'user' => 'xcache', //user from xcache.admin.user settings
	'password' => 'xcache', //plaintext password (xcache.admin.pass)
));