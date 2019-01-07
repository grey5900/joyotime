<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * WebEvent表操作
 * Author: piglet chenglin.zhu@gmail.com
 * Created on 2012-12-6
 */

class Webevent_Model extends MY_Model {
    /**
     * 点击
     * @param int $id
     */
	function hits($id) {
	    // 点击
	    $this->db->set('hits', 'hits+1', false)->where(array('id' => $id))->update($this->_tables['webevent']);
	}
	
	/**
	 * 获取关联的活动
	 * @param 活动Id $id
	 * @param 获取条数 $size
	 */
	function relation_event($id = 0, $size = 10) {
	    $where_sql = $id?array('a.id !=' => $id):array();
	    $where_sql['a.status'] = '0';
        $list = $this->db->select('a.*, count(b.uid) as applyCount', false)
                            ->order_by('', 'random')
                            ->limit($size)
                            ->where($where_sql, null, false)
                            ->from('WebEvent a')
                            ->join('WebEventApply b', 'a.id = b.eventId', 'left')
                            ->group_by('a.id')
                            ->where_in("type",array(0,2))
                            ->get()->result_array();
        return $list;
    }
    
}