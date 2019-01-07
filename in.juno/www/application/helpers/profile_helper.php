<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * profile的一些helper
 */

/**
 * 更新cookie中的auth
 * @param mixed $auth
 */
function update_auth($auth) {
	global $CI;
	
	// 获取本来保存的COOKIE，主要是获取是否自动登陆标志
	$auth_cookie = $CI->cookie('auth');
	list(, , , $auto_login) = explode("\t", authcode($auth_cookie));
	// 生成新的cookie保存值
	$cookie = $auth['uid'] . "\t" . $auth['nickname'] . "\t" . $auth['avatar_uri'] . "\t" . $auto_login;
	
	set_cookie('auth', authcode($cookie, 'ENCODE'), $auto_login?(TIMESTAMP+365*86400):0);
}