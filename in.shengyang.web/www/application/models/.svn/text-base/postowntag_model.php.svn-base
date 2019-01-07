<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * PostOwnTag表操作
 * Author: piglet chenglin.zhu@gmail.com
 * Created on 2012-12-6
 */

class Postowntag_Model extends MY_Model {
    /**
     * 获取一个POST的TAG
     * @param POST的ID号 $id
     */
	function get_postowntag($id) {
	    $list = $this->db->from($this->_tables['postowntag'] . ' a, ' . $this->_tables['tag'] . ' b')
	                 ->where("a.postId = '{$id}' AND a.tagId = b.id", null, false)->get()->result_array();
	    
	    $data = array();
	    foreach($list as $row) {
	        $data[] = $row['content'];
	    }
	    unset($list);
	    return $data;
	}
}