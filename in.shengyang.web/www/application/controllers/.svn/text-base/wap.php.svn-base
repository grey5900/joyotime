<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');   
/**
  * 提供客户端的一些访问
  * @Author: chenglin.zhu@gmail.com
  * @Date: 2013-3-11
  */

class Wap extends Controller {
	function __construct() {
		parent::__construct();
		
		$uid = intval($this->get('uid'));
		if ($uid > 0 && empty($this->auth)) {
			$this->auth['uid'] = $uid;
		}
	}
	function app_list(){
		$this->display('app_list');
	}
	function app_detail(){
		$this->display('app_detail');
	}
	function contact() {
        $this->display('contact');
    }
    
}