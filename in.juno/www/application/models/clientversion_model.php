<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * ClientVersion表操作
 * Author: piglet chenglin.zhu@gmail.com
 * Created on 2012-12-6
 */

class Clientversion_Model extends MY_Model {
	
	/**
	 * 获取最新的客户端版本
	 * Create by 2012-12-29
	 * @author liuweijava
	 * @param int $platform_type
	 */
	public function get_last_version($platform_type){
		$this->db->where('type', $platform_type);
		return $this->db->order_by('createDate', 'desc')->limit(1)->get($this->_tables['clientversion'])->first_row('array');
	}
	
}