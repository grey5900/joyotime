<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/*
 * 
 * 
 * Author: piglet chenglin.zhu@gmail.com
 * Created on 2012-6-18
 */

//api接口domain
$config['api_serv'] = 'http://api-a.out.chengdu.cn';
$config['api_folder'] = '/true/private_api/';
//$config['api_serv'] = 'http://192.168.1.40';
//$config['api_folder'] = '/private_api/';

// 配置memcached
$config['memcached_conf'] = array( array(
            '192.168.1.7',
            11211
    ));

//邮件服务器配置
$config['email_serv'] = array(
	'post_email' => 'liuweijava@gmail.com',
	'post_pwd' => 'lqw3344cd',
	'from_name' => 'in成都',
	'charset' => 'utf-8',
	'encoding' => 'base64',
	'default_subject' => '这是来自【in成都】的邮件，用于找回密码',
	'default_body' => '这是来自【in成都】的邮件，用于找回密码',
	'boty_type' => 'html',
	'word_wrap' => 75,
	//邮件发送服务器配置
	'smtp_server' => array(
		'host' => 'smtp.gmail.com',
		'port' => 465,
		'auth' => true,
		'secure' => 'ssl',
	),
);

//获取poi模型html的url
$config['poi_url'] = 'http://223.4.93.206/main/place/@{place_id}/@{module_id}';
//CMS文章发布标志,取值0或1,0表示忽略发布标志
$config['cms_post_tag'] = 1;
//图片服务器地址
$config['pic_host'] = 'http://pic-a.out.chengdu.cn/';