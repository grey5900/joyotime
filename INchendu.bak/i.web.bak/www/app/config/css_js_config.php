<?php
/**
 * 页面css和js配置
 * Create by 2012-6-15
 * @author liuw
 * @copyright Copyright(c) 2012-2014 joyotime
 */
  
 // Define and include
if ( ! defined('BASEPATH')) exit('No direct script access allowed');   
   
 // Code
 
$config['css_version'] = 'v23';
// 页面CSS加载和JS加载的映射
$config['loader_map'] = array(
    // 账号页面
    'account' => array(
            'css' => array(
		        '/css/account.css' => 0,
				'/css/in_responsive.css' => 0,
        	),
            'js' => array(
                '/js/jquery.validate.min.js' => 0,
                '/js/in.account.min.js' => 0
            )
    ),
    // web
    'web' => array(
        'index' => array(
            'css' => array(
                '/css/index.css' => 0
            ),
            'js' => array(
                '/js/jquery.slider.min.js' => 0,
                '/js/in.index.min.js' => 0
            )
         ),
    	'about'=>array(
    		'css' => array('/css/footer_content.css'=>0),
    	),
    	'contact'=>array(
    		'css' => array('/css/footer_content.css'=>0),
    	),
    	'help'=>array(
    		'css' => array('/css/footer_content.css'=>0),
    	),
    	'privacy'=>array(
    		'css' => array('/css/footer_content.css'=>0),
    	),
    	'jobs'=>array(
    		'css' => array('/css/footer_content.css'=>0),
    	),
    	'photo'=>array(
    		'css' => array('/css/photo.css'=>0),
    		'js' => array(
    			'/js/jquery.infinitescroll.min.js'=>0,
    			'/js/jquery.masonry.min.js'=>0,
    			'/js/in.photo.min.js' => 0
    		),
    	),
    ),
    // web
    'download' => array(
        'android' => array(
            'css' => array(
                '/css/download.css' => 0
            ),
            'js' => array(
                '/js/in.download.min.js' => 0
            )
         ),
    	'iphone'=>array(
            'css' => array(
                '/css/download.css' => 0
            ),
            'js' => array(
                '/js/in.download.min.js' => 0
            )
    	),
    	'wp'=>array(
            'css' => array(
                '/css/download.css' => 0
            ),
            'js' => array(
                '/js/in.download.min.js' => 0
            )
    	),
    ),
    //place
    'place' => array(
    	'add_place' => array(
    		'css' => array('/css/business.css' => 0),
    		'js' => array(
    			'/js/jquery.validate.min.js' => 0,
    			'http://api.map.soso.com/v1.0/main.js' => 0,//搜搜地图 
    			'/js/in.map.min.js' => 0,
    			'/js/in.place_add.min.js' => 0,
    		)
    	),
    	'listes' => array(
    		'css' => array('/css/place_list.css' => 0),
    		'js' => array(
    			'/js/jquery.raty.min.js' => 0,
    			'/js/in.place_list.min.js'=>0
    		),
    	),
    	'favorite' => array(
    		'css' => array('/css/pagelet.css' => 0),
    		'js' => array(
    			'/js/in.pagelet.min.js' => 0
    		),
    	),
    	'photo' => array(
    		'css' => array('/css/pagelet.css' => 0),
    		'js' => array(
    			'/js/jquery.infinitescroll.min.js' => 0,
    			'/js/jquery.masonry.min.js' => 0,
    			'/js/in.photo.min.js' => 0,
    			'/js/in.pagelet.min.js' => 0
    		),
    	),
    	'visitor' => array(
    		'css' => array('/css/pagelet.css' => 0),
    		'js' => array(
    			'/js/in.pagelet.min.js' => 0
    		),
    	),
    	'info' => array(
    		'css' => array('/css/pagelet.css' => 0),
    		'js' => array(
    			'/js/in.pagelet.min.js' => 0
    		),
    	),
    	'index' => array(
    		'css' => array('/css/place_timeline.css'=>0),
    		'js' => array(
    			'/js/jquery.viewport.min.js' => 0,
    			'/js/jquery.masonry.min.js' => 0,
    			'/js/jquery.raty.min.js' => 0,
    			'http://api.map.soso.com/v1.0/main.js' => 0,//搜搜地图 
    			'/js/in.map.min.js' => 0,
    			'/js/in.place_timeline.min.js' => 0,
    			'/js/in.timeline.min.js' => 0
    		),
    	),
    ),
    'user' => array(
    	'friend' => array(
    		'css' => array('/css/pagelet.css' => 0),
    		'js' => array(
    			'/js/jquery.validate.min.js' => 0,
    			'/js/in.pagelet.min.js' => 0
    		),
    	),
    	'photo' => array(
    		'css' => array('/css/pagelet.css' => 0),
    		'js' => array(
    			'/js/jquery.infinitescroll.min.js' => 0,
    			'/js/jquery.masonry.min.js'=> 0,
    			'/js/in.photo.min.js' => 0,
    			'/js/in.pagelet.min.js' => 0
    		),
    	),
    	'score' => array(
    		'css' => array('/css/user_settings.css' => 0),
    		'js' => array(
    			'/js/jquery.validate.min.js' => 0,
    			'/js/in.account.min.js' => 0
    		),
    	),
    	'email' => array(
    		'css' => array('/css/user_settings.css' => 0),
    		'js' => array(
    			'/js/jquery.validate.min.js' => 0,
    			'/js/in.account.min.js' => 0
    		),
    	),
    	'revisepassword' => array(
    		'css' => array('/css/user_settings.css' => 0),
    		'js' => array(
    			'/js/jquery.validate.min.js' => 0,
    			'/js/in.account.min.js' => 0
    		),
    	),
    	'settings' => array(
    		'css' => array('/css/user_settings.css' => 0),
    		'js' => array(
    			'/js/jquery.validate.min.js' => 0,
    			'/js/in.account.min.js' => 0
    		),
    	),
    	'info' => array(
    		'css' => array('/css/pagelet.css' => 0),
    		'js' => array(
    			'/js/in.pagelet.min.js' => 0
    		),
    	),
    	'mayor' => array(
    		'css' => array('/css/pagelet.css' => 0),
    		'js' => array(
    			'http://api.map.soso.com/v1.0/main.js' => 0,//搜搜地图 
    			'/js/in.pagelet.min.js' => 0
    		),
    	),
    	'sync' => array(
    		'css' => array('/css/user_settings.css'=>0),
    		'js' => array(
    		),
    	),
    	'avatar' => array(
    		'css' => array(
		    	'/css/user_settings.css'=>0,
		    	'/css/imgareaselect-default.css'=>0,
		    	'/css/uploadify.css'=>0,
	    	),
    		'js' => array(
    			'/js/jquery.uploadify.min.js'=>0,
    			'/js/jquery.imgareaselect.min.js'=>0,
    			'/js/in.avatar.min.js'=>0,
    		),
    	),
    	'index' => array(
    		'css' => array('/css/user_timeline.css'=>0),
    		'js' => array(
    			'/js/jquery.viewport.min.js'=>0,
    			'/js/jquery.masonry.min.js'=>0,
    			'/js/in.user_timeline.min.js'=>0,
    			'/js/in.timeline.min.js'=>0
    		),
    	),
    ),
    'review' => array(
    	'css' => array('/css/review.css' => 0),
    	'js' => array(
    		'/js/in.review.min.js' => 0,
    	),
    ),
    'cms' => array(
    	'list_news'=>array(
    		'css' => array('/css/business.css' => 0),
    		'js' => array(
    			
    		),
    	),
    	'search'=>array(
    		'css' => array('/css/business.css' => 0),
    		'js' => array(
    			
    		),
    	),
    	'detail'=>array(
    		'css' => array('/css/business.css' => 0),
    		'js' => array(
    			'/js/in.review.min.js' => 0
    		),
    	),
    ),
    
);   
   
 // File end