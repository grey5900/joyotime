<?php
if (!defined('BASEPATH'))
	exit('No direct script access allowed');

/**
 * 应用的一些助手
 */

/**
 * 发送短信息
 */
function send_sms($cellphone_no, $msg) {
	$CI = &get_instance();
	$config_sms = $CI->config->item('sms');
	$soap = new SoapClient($config_sms['url']);
	return $soap->__soapCall('SendSmS', array(
			'UserName' => $config_sms['user'],
			'UserPwd' => $config_sms['pass'],
			'TimeStamp' => now(0, 'YmdHis'),
			'SendMobile' => $cellphone_no,
			'SendMsg' => $msg
	));
}

// 生成一个verify_code
function generator_verify_code($cellphone_no) {
	$CI = &get_instance();
	$code = random_string('numeric', $CI->config->item('verify_code_lenth'));

	$encode = md5($code . strrev($cellphone_no));

	return array(
			'code' => $code,
			'encode' => $encode
	);
}

// 验证verify_code
function verify_code($encode, $code, $cellphone_no) {
	return $encode == md5($code . strrev($cellphone_no));
}

// 生成TOKEN
function generator_token($cellphone_no) {
	return md5(strrev(substr($cellphone_no, 3)));
}

/**
 * 获取图片地址
 * @param string $img
 * @param string $type common head
 * @param string $size odp hdp等
 */
function image_url($img, $type, $size = '') {
	if (strpos ( $img, 'http://') === 0) {
		// 如果是完整的http地址，直接返回
		return $img;
	}
	$CI = &get_instance();
	if (empty($img) || $img == "null") {
		return $CI->config->item('default_house_pic');
	}
	

	$size || $size = 'odp';
	$p = explode( '_', $img);
	
	return count($p)<2?$CI->config->item('default_house_pic'):implode( '/', array($CI->config->item('pic_domain'), $type, $size, $p[0], $p[1], $img));
}

/**
 * 返回封面图片地址
 * @param string $url
 * @param string $dp
 */
function cover_url($url, $dp = 'odp') {
	return str_replace(array('/odp/', '/udp/', '/hdp/', '/mdp/'), '/'.$dp.'/', $url);
}
