<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * Product表操作
 * Author: piglet chenglin.zhu@gmail.com
 * Created on 2012-12-6
 */

class Product_Model extends MY_Model {
    /**
     * 获取商品信息
     * @param int $id
     */
	function get($id) {
	    $sql = 'SELECT * FROM ' . $this->_tables['product'] . ' WHERE id=?';
	    $query = $this->db2->query($sql, $id);
	    
	    return $query->row_array();
	}
	
	/**
	 * 获取商品的code信息
	 * @param 电子券code $code
	 */
	function get_code_info($code) {
	    $sql = 'SELECT * FROM ' . $this->_tables['productowntradecode'] . ' WHERE code=?';
	    $query = $this->db2->query($sql, $code);
	    
	    return $query->row_array();
	}
	
	/**
	 * 添加产品
	 * @param array $info
	 */
	function add($info) {
	    $this->db2->insert($this->_tables['product'], $info);
	    
	    return $this->db2->insert_id();
	}
	
	/**
	 * 修改产品
	 * @param int $id
	 * @param array $info
	 */
	function update($id, $info) {
	    return $this->db2->where('id', $id)->update($this->_tables['product'], $info);
	}
	
	/**
	 * 删除商品
	 * @param int $id
	 */
	function delete($id) {
	    return $this->db2->delete($this->table, array('id' => $id));
	}
}