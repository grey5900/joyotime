<?php
if (!defined('BASEPATH'))
	exit('No direct script access allowed');

function cache_func_setting() {
	$CI = &get_instance();
	
	$rows = $CI->db->select('k, v, dateline')->from('setting')->get()->result_array();
	$data = array();
	foreach($rows as $row) {
		$arr = decode_json($row['v']);
		$data[$row['k']] = array('v' => is_array($arr)?$arr:$row['v'], 't' => $row['dateline']);
	}
	
	return $data;
}