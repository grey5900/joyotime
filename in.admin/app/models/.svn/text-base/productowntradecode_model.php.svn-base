<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * ProductOwnTradeCode表操作
 * Author: piglet chenglin.zhu@gmail.com
 * Created on 2012-12-6
 */

class Productowntradecode_Model extends MY_Model {
    /**
     * 批量添加
     * @param array $datas
     */
	function add_batch($datas) {
	    return $this->db2->insert_batch($this->_tables['productowntradecode'], $datas);
	}
	
	/**
	 * 修改
	 * @param int $id
	 * @param array $data
	 */
	function update($id, $data) {
	    return $this->db2->where('id', $id)->update($this->table, $data);
	}
	
	/**
	 * 根据产品ID删除电子码
	 * @param int $product_id
	 */
	function detete_by_productid($product_id) {
	    return $this->db2->delete($this->table, array('productId' => $product_id));
	}
}