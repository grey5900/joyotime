<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 地点册
 */

class Mall extends Controller {
	
	function index(){
		$this->load->helper('api_helper');
		$buyable = $this->db->where('isInStore',1)->get($this->_tables['item'])->result_array();
		$rare = $this->db->where('level',3)->get($this->_tables['item'])->result_array();
		$this->assign(compact('buyable','rare'));
		$this->display('index');
	}
}