<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');   
/**
 * 手机报
 * @author chenglin.zhu@gmail.com
 * @date 2012-11-27
 */

class Enewspaper_Model extends MY_Model {
    /**
     * 得到列表
     * @param int $status 
     * @param string $keywords
     */
    function get_list($status = 0, $keywords = '', $size = 0, $offset = 0) {
        $this->db->where(array('status' => $status));
        if($keywords !== '') {
            $this->db->like('subject', $keywords);
        }
        $this->db->limit($size, $offset);
        
        $this->db->order_by($status?'id':'publishDate', 'DESC');
        return $this->db->get('ENewsPaper')->result_array();
    }
    
    /**
     * 得到条数
     * @param int $status
     * @param string $keywords
     * @return int 
     */
    function get_counts($status = 0, $keywords = '') {
        $this->db->where(array('status' => $status));
        if($keywords !== '') {
            $this->db->like('subject', $keywords);
        }
        
        return $this->db->from('ENewsPaper')->count_all_results();
    }
    
    /**
     * 获取一个
     * @param id $id
     */
    function get_data($id) {
        return $this->db->get_where('ENewsPaper', array('id' => $id))->row_array();
    }
	
	/**
	 * 添加数据
	 * @param array $data
	 * @return int 
	 */
	function insert_data($data) {
		$this->db->insert('ENewsPaper', $data);
		return $this->db->insert_id();
	}
	
	/**
	 * 修改数据
	 * @param int id
	 * @param array $data
	 * @return bool
	 */
	function update_data($id, $data) {
		return $this->db->where('id', $id)->update('ENewsPaper', $data);
	}
}