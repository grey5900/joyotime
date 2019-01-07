<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * WebEventApply表操作
 * Author: piglet chenglin.zhu@gmail.com
 * Created on 2012-12-6
 */

class Webeventapply_Model extends MY_Model {


	
	/**
	 * 获取参与活动的用户列表
	 * Create by 2012-12-30
	 * @author liuweijava
	 * @param int $id
	 * @param int $size
	 * @param int $offset
	 */
	function list_applies($id, $size=20, $offset=0){
		$select = array(
			$this->_tables['user'].'.id',
			$this->_tables['user'].'.username',
			$this->_tables['user'].'.nickname',
			$this->_tables['user'].'.description',
			$this->_tables['user'].'.avatar',
			$this->_tables['webeventapply'].'.createDate'
		);
		$this->db->select(implode(',', $select));
		$this->db->join($this->_tables['user'], $this->_tables['user'].'.id = '.$this->_tables['webeventapply'].'.uid', 'inner');
		$this->db->from($this->_tables['webeventapply']);
		$query = $this->db->where($this->_tables['webeventapply'].'.eventId', $id)->order_by($this->_tables['webeventapply'].'.createDate', 'desc')->limit($size, $offset)->get()->result_array();
		foreach($query as &$row){
			//友好的时间格式
			$row['createDate'] = get_date($row['createDate']);
			//名称
			$row['name'] = $row['nickname'] ? $row['nickname'] : $row['username'];
			//头像
			$row['avatar_uri'] = image_url($row['avatar'], 'head', 'hmdp');
			unset($row);
		}
		return $query;
	}
    
}