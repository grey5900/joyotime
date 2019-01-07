<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * HomePageData表操作
 * Author: piglet chenglin.zhu@gmail.com
 * Created on 2012-12-6
 */

class Homepagedata_Model extends MY_Model {
    
    function __construct() {
        parent::__construct();
        $this->protetype = config_item('digest_type');
    }
	
	
	
	function get_list($keywords,$itemtype,$pageNum,$numPerPage){
		if($keywords){
			$this->db->like("content",$keywords);
		}
		if($itemtype == 2){
// 			$this->db->where("itemtype",$itemtype);
			// 过期
			$this->db->where("expireDate < '" . now() . "'", null, false);
		} else if($itemtype == 1){
			// 展示中
			$this->db->where("expireDate > '" . now() . "'", null, false);
		}
		
		$this->db->order_by("createDate","desc");
		$this->db->order_by("rankOrder","desc");
		$this->db->order_by("lastUpdate","desc");
		$this->db->limit($numPerPage,($pageNum-1)*$numPerPage);
// 		$this->db->where('itemId <> ', 0, false);
		$this->db->where_in('itemType', array_keys($this->protetype));
		$list = $this->db->get("HomePageData")->result_array();
// 		echo $this->db->last_query();
		foreach($list as &$row){
			$row['typename'] = $this->protetype[$row['itemType']];
			//获取原型数据。。
			switch($row['itemType']){
				case 19:
					$item_table = $this->_tables['post'];
					break;
				case 20:
					$item_table = $this->_tables['placecollection'];
					break;
				case 5:
					$item_table = $this->_tables['webevent'];
					break;
				case 4:
					$item_table = $this->_tables['user'];
					break;
				case 1:
					$item_table = $this->_tables['place'];
					break;
				case 23:
					$item_table = $this->_tables['product'];
					break;
				case 26:
					$item_table = 'Topic';
					break;
			}
			$item = $this->db->where('id',$row['itemId'])->get($item_table)->row_array();
			$item['count'] = $item['replyCount'] + $item['postCount'];
			$row['item'] = $item;
		}
		return $list;
	}
	
	function count_all($keywords,$itemtype,$pageNum,$numPerPage){
		
		if($keywords){
			$this->db->like("content",$keywords);
		}
		if($itemtype == 2){
// 			$this->db->where("itemtype",$itemtype);
			// 过期
			$this->db->where("expireDate < '" . now() . "'", null, false);
		} else if($itemtype == 1){
			// 展示中
			$this->db->where("expireDate > '" . now() . "'", null, false);
		}
// 		$this->db->where('itemId <> ', 0, false);
		$this->db->where_in('itemType', array_keys($this->protetype));
		return $this->db->count_all_results("HomePageData");
	}
}