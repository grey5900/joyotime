<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 抢地主
 */

class Lord extends Controller {
	
	function index($order = 'robCount'){
		
		if(!$order || !in_array($order,array('robCount','point'))){ $order = 'robCount' ;}
		$battleField = $this->db->where('status',0)->limit(20)->order_by($order,'desc')->get($this->_tables['place'])->result_array();
		$rent = get_data('rent',0);
		$this->assign(compact('battleField','rent','order'));
		$this->display('index');
	}
	
	function flush_rent_list(){
		$d = get_data_ttl('rent',0,3600*8);
		echo 'var status = '.count($d).';';
		exit;
	}
}