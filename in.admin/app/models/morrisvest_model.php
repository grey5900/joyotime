<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * MorrisVest表操作
 * Author: piglet chenglin.zhu@gmail.com
 * Created on 2012-12-6
 */

class Morrisvest_Model extends MY_Model {
	
	
	//按照AID查找当前用户的马甲
	function count_vest($aid,$where = ''){
		$count = $this->db->where('aid', $this->auth['id'])->count_all_results('MorrisVest');
		return $count;
	}
	
	function get_vest($aid,$where = '',$order_by = '',$limit = 1){
		
		$this->db->where('aid', $aid);
		$where && $this->db->where($where,null,false);
		$order_by && $this->db->order_by($order_by,null,false);
		$limit && $this->db->limit($limit);
		
		//$rs = $this->db->where('aid', $this->auth['id'])->select('uid')->order_by('uid','random')->limit(1)->get('MorrisVest')->first_row('array');
		$rs = $this->db->get("MorrisVest")->first_row('array');
		//$v_id = $rs['uid'];
		return $rs;
	}
}