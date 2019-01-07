<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * Share表操作
 * Author: piglet chenglin.zhu@gmail.com
 * Created on 2012-12-6
 */

class Share_Model extends MY_Model {
	function is_shared($uid = 0,$item_id,$item_type,$share_type){
		if(!$uid){
			$uid = $this->auth['uid'];
		}
		
		$where = " relateditemType={$item_type} and relateditemId={$item_id} and uid={$uid} and type=7 ";
		$res = $this->db->where($where,null,false)->get($this->_tables['post'])->row_array(0);
		return $res ? 1 : 0;
	}
}