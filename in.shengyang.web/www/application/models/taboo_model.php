<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * Taboo表操作
 * Author: piglet chenglin.zhu@gmail.com
 * Created on 2012-12-6
 */

class Taboo_Model extends MY_Model {
	
	/**
	 * 获取敏感词列表
	 * Create by 2012-12-14
	 * @author liuweijava
	 * @param string $type
	 */
	public function get_taboos($type=''){
		!empty($type) && $this->db->like('types', $type);
		return $this->db->get($this->_tables['taboo'])->result_array();
	}
	
}