<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * PostAppraise表操作
 * Author: piglet chenglin.zhu@gmail.com
 * Created on 2012-12-6
 */

class Postappraise_Model extends MY_Model {

	function check_praise($uid ,$itemtype, $id ,$type){
		$where = array();
		switch($itemtype){
			case 19:
				$where['postId'] = $id;
				$table = $this->_tables['postappraise'];
				break;
			case 20:
				$where['pcId'] = $id;
				$table = $this->_tables['placecollectionappraiser'];
				break;
		}
		$where = array_merge($where,array(
			'uid' => $uid,
			'type' => $type
		));
		
		
		return $this->db->where($where)->get($table)->row_array(0) ? 1 : 0 ;
	}
}