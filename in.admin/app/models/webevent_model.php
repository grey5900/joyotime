<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * WebEvent表操作
 * Author: piglet chenglin.zhu@gmail.com
 * Created on 2012-12-6
 */

class Webevent_Model extends MY_Model {
	
	function get_max_active_client_event($id){
		$this->load->helper('cache');
		/*$e = $this->db->select('id')
				  ->where('status',0)
				  ->where_in('type',array(0,1))
				  ->where('endDate > CURRENT_TIMESTAMP',null,false)
				  ->order_by('id','desc')
				  ->limit(1)
				  ->get($this->_tables['webevent'])
				  ->row_array();*/
		$event = $this->db->select_by_id ( $id );
		if($event['status']==0){
		
		}
		//notify_key_event
		@set_cache('notify_key_event',$e['id']);
	}


}