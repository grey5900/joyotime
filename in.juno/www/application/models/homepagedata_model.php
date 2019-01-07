<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * HomePageData表操作
 * Author: piglet chenglin.zhu@gmail.com
 * Created on 2012-12-6
 */

class Homepagedata_Model extends MY_Model {
	
	public $home_itemtypes = array(  19 , 4 , 20 , 5 );//array( 1 , 19 , 4 , 20 , 5 ); POI淘汰
	function getHomePageData($item_type = 0 , $limit = 10){
		// TIMESTAMP;
		/* 19 4 20 5 */
		$curr_time = date("Y-m-d H:i:s",TIMESTAMP);
		if(!$item_type){
			$item_type = $this->home_itemtypes;
		}
		
		if(is_string($item_type)){
			$item_type = explode(",",$item_type);
		}
		
		$this->db->where_in("itemType",(array)$item_type);
		$this->db->limit($limit,0);
		$this->db->where("image!=''",null,false);
		$this->db->order_by("expireDate desc ,rankOrder desc");
		
		return $this->db->get($this->_tables['homepagedata'])->result_array();
	}
}