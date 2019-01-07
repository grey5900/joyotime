<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * UserFavorite表操作
 * Author: piglet chenglin.zhu@gmail.com
 * Created on 2012-12-6
 */

class Userfavorite_Model extends MY_Model {
	
	/**
	 * 检查是否能收藏
	 * Create by 2012-12-14
	 * @author liuweijava
	 * @param int $uid
	 * @param int $item_id
	 * @param int $item_type
	 */
	public function check_favorite($uid, $item_id, $item_type){
		$c = $this->db->where(array('uid'=>$uid, 'itemType'=>$item_type, 'itemId'=>$item_id))->count_all_results($this->_tables['userfavorite']);
		return $c <= 0;
	}
	
	/**
	 * 统计资源的收藏次数
	 * Create by 2012-12-19
	 * @author liuweijava
	 * @param int $item_id
	 * @param int $item_type
	 */
	public function state_favorite($item_id, $item_type){
		return $this->db->where(array('itemType'=>$item_type, 'itemId'=>$item_id))->count_all_results($this->_tables['userfavorite']);
	}
	
	/**
	 * 收藏
	 * Create by 2012-12-14
	 * @author liuweijava
	 * @param int $uid
	 * @param int $item_id
	 * @param int $item_type
	 * @return int 0=收藏成功；1=保存收藏详情失败
	 */
	public function favorite($uid, $item_id, $item_type){
		//收藏的数据
		$data = array('uid'=>$uid, 'itemType'=>$item_type, 'itemId'=>$item_id);
		$this->db->insert($this->_tables['userfavorite'], $data);
		$insert_id = $this->db->insert_id();
		return $insert_id ? 0 : 1;
	}
	
}