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
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
App::uses('AppError', 'Lib');
App::uses('AppExceptionHandler', 'Lib');
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
CakePlugin::load('Mongodb');
CakePlugin::load(array(
    'CakeResque' => array('bootstrap' => array(
        'bootstrap_config',
        '../../../api/Config/cakeresque',
        'bootstrap'
    )),
    'QiNiu'
));

spl_autoload_register(function($class) {
	$parts = explode('\\', $class);
	if(in_array(strtolower($parts[0]), array('model', 'controller', 'utility'))) {
		$cate = array_shift($parts);
		$path = ROOT.DS.APP_DIR.DS.$cate.DS.str_replace('\\', DS, implode('\\', $parts)) . '.php';
		if (file_exists($path)) {
			return include $path;
		}
	}
});

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
 * Configures cache settings
 */
Cache::config('QiNiu', array(
	'engine' => 'Redis',
	'duration' => 85400,    // 1 day
	'prefix' => Inflector::slug(APP_DIR) . '_' . 'qiniutoken_new', 
	'server' => '127.0.0.1',
	'port' => '6377',                      
	'persistent' => true,        
	'compress' => false,
));

/**
 * Configure secrect code is used to generate access token
 */
Configure::write('Secrect', 'DYhG93b0qyJfIxfs2guVoUubbwvniR2G0FgaC9mi');

/**
 * Configure payment
 */
Configure::write('Pay.Fee.Alipay', 0.005);

/**
 * Configure products info stored in AppStore
 */
Configure::write('AppStore.Product', array(
    'com.fishsaying.product.20' => 1200,
    'com.fishsaying.product.60' => 3600,
    'com.fishsaying.product.150' => 9000,
    'com.fishsaying.product.400' => 24000,
    'com.fishsaying.product.1000' => 60000,
    'com.fishsaying.product.5000' => 300000,
    'com.fishsaying.product.20000' => 1200000
));
/**
 * Configure products info of Alipay
 */
Configure::write('Alipay.Product', array(
    array(
        'id' => 'com.fishsaying.product.20',
        'seconds' => 1200,
        'price' => 6.0
    ),
    array(
        'id' => 'com.fishsaying.product.60',
        'seconds' => 3600,
        'price' => 18.0
    ),
    array(
        'id' => 'com.fishsaying.product.150',
        'seconds' => 9000,
        'price' => 45.0
    ),
    array(
        'id' => 'com.fishsaying.product.400',
        'seconds' => 24000,
        'price' => 118.0
    ),
    array(
        'id' => 'com.fishsaying.product.1000',
        'seconds' => 60000,
        'price' => 288.0
    ),
    array(
        'id' => 'com.fishsaying.product.5000',
        'seconds' => 300000,
        'price' => 1398.0
    ),
    array(
        'id' => 'com.fishsaying.product.20000',
        'seconds' => 1200000,
        'price' => 4998.0
    ),
));

/**
 * Configure auth token expire time
 */
Configure::write('Expire.AuthToken', 2592000); // 30 days

/**
 * Configure gifts information
 */
Configure::write('Gift.Register', 12000);     // gift for register

/**
 * Configure sandbox mode for iap
 * 
 * If Iap.Sandbox is false, that means uses apple live server to check receiption.
 */
Configure::write('Iap.Sandbox', true);

/**
 * Configure TTL of finished in broadcasting...
 */
Configure::write('Broadcast.Finished.TTL', MONTH * 3);    // 3 months


/**
 * Configure whether enable accessToken of debug or not. 
 *
 * Whether it sets to false, don't allow use debug accessToken anymore.
 */
Configure::write('Enable.Debug.Access.Token', true);
Configure::write('Debug.Access.Token', '362edb126af7cacbae0d20051cbb2e76');

/**
 * Configure Payment access/auth token
 * 
 * It's specified accessToken using by payment server.
 */
Configure::write('Payment.Access.Token', 'a6b2c7f23322fbf168d6472cf785ff11');
Configure::write('Payment.Auth.Token',   'a48e24851770d11b642b5e232d00b88f');

/**
 * Define URL
 */
Configure::write('URL.Website.Domain', 'staging.www.fishsaying.com');
/**
 * Configure domain use to sharing voice to third-party platform
 */
Configure::write('Domain.Share', 'http://'.Configure::read('URL.Website.Domain').'/r/voice');
/**
 * Configure url of terms of service hosted on fishsaying website
 */
Configure::write('URL.Terms.Of.Service', 'http://'.Configure::read('URL.Website.Domain').'/terms');
/**
 * Configure verson of iap payment information 
 */
Configure::write('Payment.Version', '1.0');

//Configure::write('APP.IOS.Version', '1.2.0');
//Configure::write('APP.IOS.Description', '');
Configure::write('APP.IOS.Download.Link', 'https://itunes.apple.com/app/fishsaying/id747780955?ls=1&mt=8');
//Configure::write('APP.Android.Version', '0.3.0');
Configure::write('APP.Android.Download.Link', 'http://fsapp.joyotime.com/FishSaying.apk');
//Configure::write('APP.Android.Description', '');
/**
 * Configure minimum length of voice for upload
 */
Configure::write('Min.Upload.Length', 60);      // Unit: second
Configure::write('Max.Upload.Length', 300);     // Unit: second
Configure::write('Min.Withdrwal.Second', 1200);    // Unit: second

/**
 * Configure information about QiNiu.com
 */
Configure::write('QINIU_UP_HOST', 'http://up.qiniu.com');

/**
 * Configure URL of callback for alipay
 */
Configure::write('Alipay.Callback', 'http://staging.payment.fishsaying.com/alipays/callback');

/**
 * Configure contact information
 */
Configure::write('Contact.Phone', '+862883272116');

/**
 * Configure log filename
 */
Configure::write('Log.Ios', 'ios_iap');

/**
 * Configure locale and language supported
 */
Configure::write('Locale.Supported', array(
	'zh_CN',
	'zh_TW',
	'en_US'
));

/**
 * Configure Access/Write Key
 */
Configure::write('QINIU_ACCESS_KEY', 'p2DofS0KOhrEU1OXKk2X3fv2MapiWWjbbeAvnNtX');
Configure::write('QINIU_SECRET_KEY', 'KfPGoMQ12n1bxDe4ua8Y6suKK7PUd1ebBL1VibWQ');
Configure::write('QINIU_UP_TOKEN_EXPIRE', 604800);
Configure::write('QINIU_PRIVATE_URL_EXPIRE', 604800);
Configure::write('QINIU_TRIAL_URL_EXPIRE', 1576800000);    // 50 years

/**
 * Configure for award of daily sign in
 */
Configure::write('Register.Award', 900);

/**
 * Configure for subsidy
 */
Configure::write('Sale.Subsidy.Percent', 1.2);  // 120%