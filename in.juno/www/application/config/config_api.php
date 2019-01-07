<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * API的一些配置
 * Author: piglet chenglin.zhu@gmail.com
 * Created on 2012-12-6
 */

$config['api']['url'] = 'http://api-a.out.chengdu.cn/true/';
//$config['api']['url'] = 'http://api-a.out.chengdu.cn/true/private_api';
//$config['api']['url'] = 'http://joyotime.gicp.net:80/hl/private_api';

$config['api']['path'] = array(
    'signin' => '/user/signin',
    'signup' => '/user/signup',
	//Add by Liuw
	'setting' => '/user/update_basic',
	'up_email' => '/user/update_email',  
	'up_avatar' => '/user/update_avatar',
	'oauth_bind' => '/oauth/bind',
	'oauth_unbind' => '/oauth/unbind',
	'oauth_signin' => '/oauth/login',
	'msg_push_sys' => '/push/push_system_message',
	'msg_push_rep' => '/push/push_reply_message',
	'share' => '/user/share',
	'send_tip' => '/post/save_tip',
	'send_photo' => '/post/save_photo'
); 

// rsa证书地址
$config['rsa_private_key_path'] = FRAMEWORK_PATH . './forbid/rsa_private_key.pem';
$config['rsa_public_key_path'] = FRAMEWORK_PATH.'./forbid/rsa_public_key.pem';


// 头像
$config['image_head'] = array('odp', 'hudp', 'hhdp', 'hmdp', 'udpl', 'hdpl', 'mdpl');
// 用户发布
$config['image_user'] = array('odp', 'udp', 'hdp', 'mdp', 'thudp', 'thhdp', 'thmdp', 'thweb');

$config['v3_api_domain'] = $config['api']['url'];

// 客户端用的token数组
// token_password=fuck_fuck_fuck_gfw_must_die,fuck_key2,fuck_key3
$config['token_password'] = array(
        'fuck_fuck_fuck_gfw_must_die',
        'fuck_key2',
        'fuck_key3'
);

$config['api_request_key'] = array(
        'gfw_must_die_die_die_fuck2587',
		'你妹',
		'gfw_must_die',
		'f85af97a-fbe8-11e1-b5c3-109add6b666c',
		'f68e12bf-5bff-47dd-8f18-7c6be7310359'
);