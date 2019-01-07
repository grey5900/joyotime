<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * UserFeed表操作
 * Author: piglet chenglin.zhu@gmail.com
 * Created on 2012-12-6
 */

class Userfeed_Model extends MY_Model {
	
	/**
	 * 更新统计数
	 * Create by 2012-12-14
	 * @author liuweijava
	 * @param int $item_id
	 * @param int $item_type
	 * @param string $stat_col
	 * @param boolean $is_minus true=减少，false=增加
	 */
	public function update_stat_count($item_id, $item_type, $stat_col, $is_minus=false){
		$set = $stat_col.($is_minus?'-1':'+1');
		$this->db->where(array('itemId'=>$item_id, 'itemType'=>$item_type))->set($stat_col, $set, false)->update($this->_tables['userfeed']);
	}
	
}