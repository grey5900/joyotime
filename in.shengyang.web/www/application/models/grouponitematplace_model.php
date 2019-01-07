<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * GrouponItemAtPlace表操作
 * Author: piglet chenglin.zhu@gmail.com
 * Created on 2012-12-6
 */

class Grouponitematplace_Model extends MY_Model {
	
	/**
	 * 查询地点的团购信息
	 * Create by 2012-12-30
	 * @author liuweijava
	 * @param int $id
	 * @param int $size
	 * @param int $offset
	 */
	public function get_groupones($id, $size=20, $offset=0){
		$this->db->select($this->_tables['grouponitem'].'.*');
		$this->db->join($this->_tables['grouponitem'], $this->_tables['grouponitem'].'.id='.$this->_tables['grouponitematplace'].'.grouponId', 'inner');
		$this->db->from($this->_tables['grouponitematplace']);
		$this->db->where(array(
			$this->_tables['grouponitematplace'].'.placeId'=>$id,
			$this->_tables['grouponitem'].'.status'=>0
		));
		$this->db->order_by($this->_tables['grouponitem'].'.rankOrder', 'desc');
		$query = $this->db->limit($size, $offset)->get()->result_array();
		return $query;
	}
	
}