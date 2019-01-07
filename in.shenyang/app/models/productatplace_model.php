<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * ProductAtPlace表操作
 * Author: piglet chenglin.zhu@gmail.com
 * Created on 2012-12-6
 */

class Productatplace_Model extends MY_Model {
    /**
     * 选出关联的地点
     * @param int $product_id
     */
	function find_relation_place($product_id) {
	    return $this->db2->from($this->table . ' a')
	                ->join($this->_tables['place'] . ' b', 'a.placeId = b.id')
	                ->where('a.productId', $product_id)
	                ->get()->result_array();
	}
	
	/**
	 * 删除商品地点
	 * @param int $product_id
	 */
	function delete_by_productid($product_id) {
	    return $this->db2->delete($this->table, array('productId' => $product_id));
	}
}