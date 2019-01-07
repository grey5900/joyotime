<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');


class Vote_Model extends MY_Model {
    
	function get_vote($item_type,$item_id,$option_limit = 0,$path=''){
		$vote = $this->db->
					   where(array('itemType'=>$item_type,'itemId'=>$item_id))->
					   get($this->_tables['vote'])->
					   row_array(0);
		if(empty($vote)){
			return array();
		}
		else{
			$this->config->load('config_vote');
			$web2wap = $this->config->item('web2wap');
			$option_limit ? $this->db->limit($option_limit) : "";
			//vote rankOrder  0 票数排序 1添加时间排序 2随即排序
			switch($vote['rankOrder']){
				case 0 :
					$order = 'votes desc';
					break;
				case 1 :	
				case 2 :
					$order = 'createDate asc';
					//$order = 'rand()';
					break;
			}
			$options = $this->db->where(array('voteId'=>$vote['id']))->order_by($order)->get($this->_tables['voteoptions'])->result_array();
			if($vote['rankOrder'] == 2){
				shuffle($options);
			}
			if(is_mobile() || $path){
				//切换link
				foreach($options as &$row){
					$c = explode("/",$row['link'])[3];
					$id = explode("/",$row['link'])[4];
					if(array_key_exists($c,$web2wap) ){
						$row['link'] = $web2wap[$c].$id;
					}
				}
			}
			$vote['options'] = $options;
			return $vote;
		}
	}
	
}