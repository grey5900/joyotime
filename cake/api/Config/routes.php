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
define('RULE_MONGO_ID', '[0-9a-fA-F]{24}');
/**
 * Here, we are connecting '/' (base path) to controller called 'Pages',
 * its action called 'display', and we pass a param to select the view file
 * to use (in this case, /app/View/Pages/home.ctp)...
 */
	Router::connect('/', array('controller' => 'pages', 'action' => 'display', 'home'));
/**
 * ...and connect the rest of 'Pages' controller's urls.
 */
	Router::connect('/pages/*', array('controller' => 'pages', 'action' => 'display'));
	
	$rules = array(
    	/**
    	 * Parse mongoID
         */
//     	'id' => '(.*?)+'    // very serious performance problem...
    	'id' => '[0-9a-fA-F]{24}'
	);
	
/**
 * The routes for resources of follows
 */
	Router::connect('/users/:uid/follows',
		array('controller' => 'follows', 'action' => 'index', '[method]' => 'GET'),
		array(
			'uid' => RULE_MONGO_ID,
			'pass' => array('uid'),
		)
	);
	Router::connect('/users/:uid/follows', 
	    array('controller' => 'follows', 'action' => 'edit', '[method]' => 'PUT'),
	    array(
	    	'uid' => RULE_MONGO_ID,
	    	'pass' => array('uid')
	    )
	);
	Router::connect('/users/:uid/follows/:fid', 
	    array('controller' => 'follows', 'action' => 'view', '[method]' => 'GET'), 
	    array(
	        'uid' => RULE_MONGO_ID, 
	        'fid' => RULE_MONGO_ID, 
	        'pass' => array('uid', 'fid')
	    )
	);
	Router::connect('/users/:uid/follows/:fid', 
	    array('controller' => 'follows', 'action' => 'delete', '[method]' => 'DELETE'), 
	    array(
	        'uid' => RULE_MONGO_ID, 
	        'fid' => RULE_MONGO_ID, 
	        'pass' => array('uid', 'fid')
	    )
	);
	Router::connect('/users/:uid/follows/new_posts', 
	    array('controller' => 'follows', 'action' => 'pull', '[method]' => 'GET'), 
	    array(
	        'uid' => RULE_MONGO_ID, 
	        'pass' => array('uid')
	    )
	);
/**
 * The routes for resources of favorites
 */
	Router::connect('/users/:uid/favorites',
		array('controller' => 'favorites', 'action' => 'index', '[method]' => 'GET'),
		array(
			'uid' => RULE_MONGO_ID,
			'pass' => array('uid')
		)
	);
	/*
	 * Old path for add
	 */
	Router::connect('/users/:uid/favorites',
		array('controller' => 'favorites', 'action' => 'add', '[method]' => 'POST'),
		array(
			'uid' => RULE_MONGO_ID,
			'pass' => array('uid')
		)
	);
	/*
	 * New path for add
	 */
	Router::connect('/favorites',
		array('controller' => 'favorites', 'action' => 'add', '[method]' => 'POST')
	);
	Router::connect('/users/:uid/favorites/:fid', 
	    array('controller' => 'favorites', 'action' => 'edit', '[method]' => 'PUT'),
	    array(
	    	'uid' => RULE_MONGO_ID,
	    	'fid' => RULE_MONGO_ID,
	    	'pass' => array('uid', 'fid')
	    )
	);
	Router::connect('/users/:uid/favorites/:fid/voices', 
	    array('controller' => 'favorites', 'action' => 'push', '[method]' => 'PUT'),
	    array(
	    	'uid' => RULE_MONGO_ID,
	    	'fid' => RULE_MONGO_ID,
	    	'pass' => array('uid', 'fid')
	    )
	);
	Router::connect('/users/:uid/favorites/:fid', 
	    array('controller' => 'favorites', 'action' => 'view', '[method]' => 'GET'), 
	    array(
	        'uid' => RULE_MONGO_ID, 
	        'fid' => RULE_MONGO_ID, 
	        'pass' => array('uid', 'fid')
	    )
	);
	Router::connect('/users/:uid/favorites/:fid', 
	    array('controller' => 'favorites', 'action' => 'delete', '[method]' => 'DELETE'), 
	    array(
	        'uid' => RULE_MONGO_ID, 
	        'fid' => RULE_MONGO_ID, 
	        'pass' => array('uid', 'fid')
	    )
	);
	Router::connect('/users/:uid/favorites/:fid/voices', 
	    array('controller' => 'favorites', 'action' => 'pull', '[method]' => 'DELETE'), 
	    array(
	        'uid' => RULE_MONGO_ID, 
	        'fid' => RULE_MONGO_ID, 
	        'pass' => array('uid', 'fid')
	    )
	);
/**
 * The routes for resources of notifications
 */
	Router::connect('/users/:uid/notifications', 
	    array('controller' => 'notifications', 'action' => 'index', '[method]' => 'GET'), 
	    array(
	        'uid' => RULE_MONGO_ID, 
	        'pass' => array('uid')
	    )
	);
	Router::connect('/users/:uid/notifications/new_messages_total', 
	    array('controller' => 'notifications', 'action' => 'pull', '[method]' => 'GET'), 
	    array(
	        'uid' => RULE_MONGO_ID, 
	        'pass' => array('uid')
	    )
	);
	Router::connect('/admin/notifications', 
	    array('controller' => 'notifications', 'action' => 'admin_add', '[method]' => 'POST')
	);
/**
 * The routes for resources of comments
 */
	Router::connect('/users/:uid/comments', 
	    array('controller' => 'comments', 'action' => 'add', '[method]' => 'POST'), 
	    array(
	        'uid' => RULE_MONGO_ID, 
	        'pass' => array('uid')
	    )
	);
	/**
	 * New delete path...
	 */
	Router::connect('/comments/:cid', 
	    array('controller' => 'comments', 'action' => 'delete', '[method]' => 'DELETE'), 
	    array(
	        'cid' => RULE_MONGO_ID, 
	        'pass' => array('cid')
	    )
	);
	/**
	 * Old path, deprecated...
	 */
	Router::connect('/voices/:vid/comments', 
	    array('controller' => 'comments', 'action' => 'index', '[method]' => 'GET'), 
	    array(
	        'vid' => RULE_MONGO_ID, 
	        'pass' => array('vid')
	    )
	);
	/**
	 * New index path...
	 */
	Router::connect('/comments', 
	    array('controller' => 'comments', 'action' => 'index', '[method]' => 'GET')
	);
	Router::connect('/comments/:cid', 
	    array('controller' => 'comments', 'action' => 'view', '[method]' => 'GET'), 
	    array(
	        'cid' => RULE_MONGO_ID, 
	        'pass' => array('cid')
	    )
	);
/**
 * The routes for resources of authenticate
 */
	Router::connect('/authenticates', 
	    array('controller' => 'authenticates', 'action' => 'authorize', '[method]' => 'PUT')
	);
/**
 * The routes for resources of purchases
 */
	Router::connect('/users/:uid/purchases', 
	    array('controller' => 'purchases', 'action' => 'add', '[method]' => 'POST'),
	    array(
    		'uid' => RULE_MONGO_ID,
    		'pass' => array('uid'),
	    )
	);
	Router::connect('/users/:uid/purchases', 
	    array('controller' => 'purchases', 'action' => 'index', '[method]' => 'GET'),
	    array(
    		'uid' => RULE_MONGO_ID,
    		'pass' => array('uid'),
	    )
	);
	Router::connect('/purchases', 
	    array('controller' => 'purchases', 'action' => 'view', '[method]' => 'GET')
	);
/**
 * The routes for resources of withdrawals
 */
	Router::connect('/users/:uid/withdrawals', 
	    array('controller' => 'withdrawals', 'action' => 'add', '[method]' => 'POST'),
	    array(
    		'uid' => RULE_MONGO_ID,
    		'pass' => array('uid'),
	    )
	);
	Router::connect('/users/:uid/withdrawals/:cid', 
	    array('controller' => 'withdrawals', 'action' => 'view', '[method]' => 'GET'),
	    array(
    		'uid' => RULE_MONGO_ID,
    		'cid' => RULE_MONGO_ID,
    		'pass' => array('cid'),
	    )
	);
	Router::connect('/users/:uid/withdrawals', 
	    array('controller' => 'withdrawals', 'action' => 'index', '[method]' => 'GET'),
	    array(
    		'uid' => RULE_MONGO_ID,
    		'pass' => array('uid'),
	    )
	);
	Router::connect('/admin/withdrawals/:cid', 
	    array('controller' => 'withdrawals', 'action' => 'admin_edit', '[method]' => 'PUT'),
	    array(
    		'cid' => RULE_MONGO_ID,
    		'pass' => array('cid'),
	    )
	);
	Router::connect('/admin/withdrawals', 
	    array('controller' => 'withdrawals', 'action' => 'admin_index', '[method]' => 'GET')
	);
/**
 * The routes for resources of reverse withdrawals
 */
	Router::connect('/admin/reverse_withdrawals', 
	    array('controller' => 'reverse_withdrawals', 'action' => 'admin_add', '[method]' => 'POST')
	);
/**
 * The routes for resources of charges
 */
	Router::connect('/charges/:gateway/:currency/:seconds', 
	    array('controller' => 'charges', 'action' => 'view', '[method]' => 'GET'),
	    array(
    		'gateway' => '[a-zA-Z]+',
    		'currency' => '[a-zA-Z]+',
    		'seconds' => '[.0-9]+',
    		'pass' => array('gateway', 'currency', 'seconds'),
	    )
	);
/**
 * The routes for resources of transfers
 * Old path [[Deprecated!!!]]
 */
	Router::connect('/users/:uid/transfers',
	    array('controller' => 'checkouts', 'action' => 'transfer', '[method]' => 'POST'),
	    array(
	        'uid' => RULE_MONGO_ID,
	        'pass' => array('uid'),
	    )
	);
/**
 * The routes for resources of checkouts
 */
	Router::connect('/users/:uid/checkouts', 
	    array('controller' => 'checkouts', 'action' => 'index', '[method]' => 'GET'),
	    array(
    		'uid' => RULE_MONGO_ID,
    		'pass' => array('uid'),
	    )
	);
	Router::connect('/admin/checkouts', 
	    array('controller' => 'checkouts', 'action' => 'admin_index', '[method]' => 'GET')
	);
	Router::connect('/checkouts/transfer',
	    array('controller' => 'checkouts', 'action' => 'transfer', '[method]' => 'POST')
	);
/**
 * The routes for resources of user
 * @deprecated
 */
	Router::connect('/users/:uid/profile',
	    array('controller' => 'users', 'action' => 'edit', '[method]' => 'PUT'),
	    array(
	        'uid' => RULE_MONGO_ID, 
	        'pass' => array('uid')
	    )
	);
/**
 * New edit URI
 */
	Router::connect('/users/:userid',
	    array('controller' => 'users', 'action' => 'edit', '[method]' => 'PUT'),
	    array(
    		'userid' => RULE_MONGO_ID,
    		'pass' => array('userid')
	    )
	);
/**
 * New edit URI
 */
	Router::connect('/admin/users/:userid',
	    array('controller' => 'users', 'action' => 'admin_edit', '[method]' => 'PUT'),
	    array(
    		'userid' => RULE_MONGO_ID,
    		'pass' => array('userid')
	    )
	);
/**
 * The routes for resources of voices
 */
	Router::connect('/users/:uid/voices',
	    array('controller' => 'voices', 'action' => 'add', '[method]' => 'POST'),
	    array(
	        'uid' => RULE_MONGO_ID, 
	        'pass' => array('uid')
	    )
	);
	Router::connect('/users/:uid/voices/:vid',
	    array('controller' => 'voices', 'action' => 'edit', '[method]' => 'PUT'),
	    array(
	        'uid' => RULE_MONGO_ID, 
	        'vid' => RULE_MONGO_ID, 
	        'pass' => array('uid', 'vid'), 
	    )
	);
	Router::connect('/users/:uid/voices/:vid',
	    array('controller' => 'voices', 'action' => 'delete', '[method]' => 'DELETE'),
	    array(
	        'uid' => RULE_MONGO_ID, 
	        'vid' => RULE_MONGO_ID, 
	        'pass' => array('uid', 'vid'), 
	    )
	);
	Router::connect('/voices/:vid',
	    array('controller' => 'voices', 'action' => 'view', '[method]' => 'GET'),
	    array(
	        'vid' => RULE_MONGO_ID, 
	        'pass' => array('vid'), 
	    )
	);
	Router::connect('/voices/:vid',
	    array('controller' => 'voices', 'action' => 'view', '[method]' => 'GET'),
	    array(
	        'vid' => RULE_SHORT_ID, 
	        'pass' => array('vid')
	    )
	);
	Router::connect('/voices',
	    array('controller' => 'voices', 'action' => 'index', '[method]' => 'GET')
	);
/**
 * The routes for resources of feedbacks
 */
	Router::connect('/feedbacks',
	    array('controller' => 'feedbacks', 'action' => 'add', '[method]' => 'POST')
	);
	Router::connect('/admin/feedbacks/:fid',
	    array('controller' => 'feedbacks', 'action' => 'admin_edit', '[method]' => 'PUT'),
	    array(
	        'fid' => RULE_MONGO_ID, 
	        'pass' => array('fid'), 
	    )
	);
	Router::connect('/admin/feedbacks/:fid',
	    array('controller' => 'feedbacks', 'action' => 'admin_delete', '[method]' => 'DELETE'),
	    array(
	        'fid' => RULE_MONGO_ID, 
	        'pass' => array('fid'), 
	    )
	);
	Router::connect('/admin/feedbacks/:fid',
	    array('controller' => 'feedbacks', 'action' => 'admin_view', '[method]' => 'GET'),
	    array(
	        'fid' => RULE_MONGO_ID, 
	        'pass' => array('fid'), 
	    )
	);
	Router::connect('/admin/feedbacks',
	    array('controller' => 'feedbacks', 'action' => 'admin_index', '[method]' => 'GET')
	);

/**
 * The routes for resources of tags
 */
	Router::connect('/tags',
	    array('controller' => 'tags', 'action' => 'index', '[method]' => 'GET')
	);
	Router::connect('/tags/:tid',
	    array('controller' => 'tags', 'action' => 'view', '[method]' => 'GET'),
	    array(
    		'tid' => RULE_MONGO_ID,
    		'pass' => array('tid')
	    )
	);
	Router::connect('/admin/tags',
	    array('controller' => 'tags', 'action' => 'admin_add', '[method]' => 'POST')
	);
	Router::connect('/admin/tags/:tid',
	    array('controller' => 'tags', 'action' => 'admin_edit', '[method]' => 'PUT'),
	    array(
    		'tid' => RULE_MONGO_ID,
    		'pass' => array('tid'),
	    )
	);
	Router::connect('/admin/tags/:tid',
	    array('controller' => 'tags', 'action' => 'admin_delete', '[method]' => 'DELETE'),
	    array(
    		'tid' => RULE_MONGO_ID,
    		'pass' => array('tid')
	    )
	);

/**
 * The routes for resources of receipts
 */
	Router::connect('/receipts/alipay',
	    array('controller' => 'receipts', 'action' => 'alipay', '[method]' => 'POST')
	);
	Router::connect('/receipts/ios',
	    array('controller' => 'receipts', 'action' => 'ios', '[method]' => 'POST')
	);
	Router::connect('/receipts/:rid',
	    array('controller' => 'receipts', 'action' => 'edit', '[method]' => 'PUT'),
	    array(
    		'rid' => RULE_MONGO_ID,
    		'pass' => array('rid'),
	    )
	);
	Router::connect('/receipts/:rid',
	    array('controller' => 'receipts', 'action' => 'view', '[method]' => 'GET'),
	    array(
    		'rid' => RULE_MONGO_ID,
    		'pass' => array('rid'),
	    )
	);

/**
 * The routes for resources of broadcasts
 */
	Router::connect('/admin/broadcasts',
	    array('controller' => 'broadcasts', 'action' => 'admin_add', '[method]' => 'POST')
	);
	Router::connect('/admin/broadcasts',
	    array('controller' => 'broadcasts', 'action' => 'admin_index', '[method]' => 'GET')
	);
	Router::connect('/broadcasts',
	    array('controller' => 'broadcasts', 'action' => 'add', '[method]' => 'POST')
	);

/**
 * The routes for resources of roles
 */
	Router::connect('/admin/roles',
	    array('controller' => 'roles', 'action' => 'admin_edit', '[method]' => 'PUT')
	);

/**
 * The routes for resources of passwords
 */
	Router::connect('/passwords/reset',
	    array('controller' => 'passwords', 'action' => 'reset_add',  '[method]' => 'POST')
	);
	Router::connect('/passwords/reset',
	    array('controller' => 'passwords', 'action' => 'reset_edit', '[method]' => 'PUT')
	);

/**
 * The routes for resources of versions
 */
	Router::connect('/admin/versions',
	    array('controller' => 'versions', 'action' => 'admin_add',  '[method]' => 'POST')
	);
	Router::connect('/admin/versions',
	    array('controller' => 'versions', 'action' => 'admin_index', '[method]' => 'GET')
	);

/**
 * The routes for resources of configures
 */
	Router::connect('/admin/configures',
	    array('controller' => 'configures', 'action' => 'admin_edit',  '[method]' => 'PUT')
	);
	Router::connect('/admin/configures',
	    array('controller' => 'configures', 'action' => 'admin_index',  '[method]' => 'GET')
	);

/**
 * The routes for resources of packages
 */
	Router::connect('/admin/packages',
	    array('controller' => 'packages', 'action' => 'admin_add',  '[method]' => 'POST')
	);
	Router::connect('/admin/packages/:id',
	    array('controller' => 'packages', 'action' => 'admin_edit',  '[method]' => 'PUT'),
	    array(
    		'id' => RULE_MONGO_ID,
    		'pass' => array('id'),
	    )
	);
	Router::connect('/admin/packages/:id',
	    array('controller' => 'packages', 'action' => 'admin_delete',  '[method]' => 'DELETE'),
	    array(
    		'id' => RULE_MONGO_ID,
    		'pass' => array('id'),
	    )
	);
	Router::connect('/admin/packages/:pid/voice/:vid',
	    array('controller' => 'packages', 'action' => 'admin_voice_add',  '[method]' => 'PUT'),
	    array(
    		'pid' => RULE_MONGO_ID,
    		'vid' => RULE_MONGO_ID,
    		'pass' => array('pid', 'vid')
	    )
	);
	Router::connect('/admin/packages/:pid/voice/:vid',
	    array('controller' => 'packages', 'action' => 'admin_voice_delete',  '[method]' => 'DELETE'),
	    array(
    		'pid' => RULE_MONGO_ID,
    		'vid' => RULE_MONGO_ID,
    		'pass' => array('pid', 'vid'),
	    )
	);
	Router::connect('/packages',
	    array('controller' => 'packages', 'action' => 'index',  '[method]' => 'GET')
	);
	Router::connect('/packages/:pid',
	    array('controller' => 'packages', 'action' => 'view',  '[method]' => 'GET'),
	    array(
    		'pid' => RULE_MONGO_ID,
    		'pass' => array('pid')
	    )
	);

/**
 * The routes for resources of themes
 */
	Router::connect('/admin/themes',
	    array('controller' => 'themes', 'action' => 'admin_add',  '[method]' => 'POST')
	);
	Router::connect('/admin/themes/:id',
	    array('controller' => 'themes', 'action' => 'admin_edit',  '[method]' => 'PUT'),
	    array(
    		'id' => RULE_MONGO_ID,
    		'pass' => array('id'),
	    )
	);
	Router::connect('/admin/themes/:id',
	    array('controller' => 'themes', 'action' => 'admin_delete',  '[method]' => 'DELETE'),
	    array(
    		'id' => RULE_MONGO_ID,
    		'pass' => array('id'),
	    )
	);
	Router::connect('/admin/themes/:pid/voice/:vid',
	    array('controller' => 'themes', 'action' => 'admin_voice_add',  '[method]' => 'PUT'),
	    array(
    		'pid' => RULE_MONGO_ID,
    		'vid' => RULE_MONGO_ID,
    		'pass' => array('pid', 'vid')
	    )
	);
	Router::connect('/admin/themes/:pid/voice/:vid',
	    array('controller' => 'themes', 'action' => 'admin_voice_delete',  '[method]' => 'DELETE'),
	    array(
    		'pid' => RULE_MONGO_ID,
    		'vid' => RULE_MONGO_ID,
    		'pass' => array('pid', 'vid'),
	    )
	);
	Router::connect('/themes',
	    array('controller' => 'themes', 'action' => 'index',  '[method]' => 'GET')
	);
	Router::connect('/themes/:pid',
	    array('controller' => 'themes', 'action' => 'view',  '[method]' => 'GET'),
	    array(
    		'pid' => RULE_MONGO_ID,
    		'pass' => array('pid')
	    )
	);
/**
 * The routes for resources of themes
 */
	Router::connect('/admin/coupons',
	    array('controller' => 'coupons', 'action' => 'admin_add',  '[method]' => 'POST')
	);
	Router::connect('/admin/coupons/:cid',
	    array('controller' => 'coupons', 'action' => 'admin_edit',  '[method]' => 'PUT'),
	    array(
    		'cid' => RULE_MONGO_ID,
    		'pass' => array('cid')
	    )
	);
	Router::connect('/admin/coupons/:cid',
	    array('controller' => 'coupons', 'action' => 'admin_delete',  '[method]' => 'DELETE'),
	    array(
    		'cid' => RULE_MONGO_ID,
    		'pass' => array('cid')
	    )
	);
	Router::connect('/admin/coupons',
	    array('controller' => 'coupons', 'action' => 'admin_index',  '[method]' => 'GET')
	);
	Router::connect('/admin/coupons/:cid',
	    array('controller' => 'coupons', 'action' => 'admin_view',  '[method]' => 'GET'),
	    array(
    		'cid' => RULE_MONGO_ID,
    		'pass' => array('cid')
	    )
	);
/**
 * The routes for resources of users/recommend
 */
	Router::connect('/admin/users/recommend/:userid',
	    array('controller' => 'RecommendUsers', 'action' => 'admin_edit',  '[method]' => 'PUT'),
	    array(
    		'userid' => RULE_MONGO_ID,
    		'pass' => array('userid')
	    )
	);
	Router::connect('/users/recommend',
	    array('controller' => 'RecommendUsers', 'action' => 'index',  '[method]' => 'GET')
	);
	
	Router::mapResources('users', $rules);
	Router::mapResources('reports', $rules);
	Router::parseExtensions();

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
