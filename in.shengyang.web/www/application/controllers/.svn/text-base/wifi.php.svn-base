<?php
if (!defined('BASEPATH'))
	exit('No direct script access allowed');


class Wifi extends MY_Controller {
	function index() {
		echo 'success';
	}
	
	function login() {
		if ('ok' == $_GET['do']) {
			// 登陆
			// 返回一个token
 			// http://192.168.44.1:2060/wifidog/auth?token=a977ba5edb3e10b7f49f79694a741c480d08b511
			$this->assign('token', 'a977ba5edb3e10b7f49f79694a741c480d08b511');
			$this->display('success');
		}
		
		$this->display('login');
	}
	
	function ping() {
		echo 'Pong';
	}
	
	function portal() {
		echo 'portal';
	}
}