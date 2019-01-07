<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');   
/**
 * 外部访问操作
 */

class Api extends MY_Controller {
	function __construct() {
		parent::__construct();
		
		// 验证
		$header = $this->input->request_headers(true);
		foreach($header as $key => $value) {
			$k = strtolower($key);
			$header[$k] = $value;
		}
		$t = intval(trim($header['t']));
		$s = trim($header['s']);
		
		if(empty($t) || empty($s)) {
			die('error timestamp or sign');
		}
		
		// 比较时间不能超过3分钟
		if(abs(TIMESTAMP - $t) > 180) {
			die('error timestamp');
		}
		
		$t = strval($t);
		$mt = md5($t);
		$k = md5(md5($mt{0}) . $mt . md5($mt{$t{9}}));
		if ($s != $k) {
			die('error sign');
		}
	}
	
	function index() {
		
	}
	
	/**
	 * 访问private api的操作
	 */
	function private_api() {
		$uri = trim($_POST['uri']);
		$p = trim($_POST['params']);
		$params = json_decode($p, true);

		$uri or die('error');
		
		echo send_api_interface($uri, 'POST', $params);
	}
}
