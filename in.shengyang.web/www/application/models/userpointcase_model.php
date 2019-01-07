<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * UserPointCase表操作
 * Author: piglet chenglin.zhu@gmail.com
 * Created on 2012-12-6
 */

class Userpointcase_Model extends MY_Model {
	
	/**
	 * 查询积分规则列表
	 * Create by 2012-12-12
	 * @author liuweijava
	 * @return array
	 */
	public function get_list(){
		$list = $this->db->where('actionBegin <= CURRENT_TIMESTAMP AND actionEnd > CURRENT_TIMESTAMP')->order_by('createDate', 'asc')->get($this->_tables['userpointcase'])->result_array();
		foreach($list as &$row){
			$row['description'] = str_replace('n', '几', $row['description']);
			unset($row);
		}
		return $list;
	}
	
}