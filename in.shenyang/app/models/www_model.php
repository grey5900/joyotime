<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * www controller里面会用到的一些数据库操作
 * @author chenglin.zhu@gmail.com
 * @date 2012-11-28
 */

class Www_Model extends MY_Model {
    /**
     * 得到最新的一条手机报信息
     * @param int $id
     */
    function find_enewspaper($id = 0) {
        $where = array('status' => 0);
        
        // 如果出入了ID，直接获取，不用管状态
        $id && ($where = array('id' => $id));
        
        $paper = $this->db->limit(1)->order_by('publishDate', 'DESC')
                 ->get_where('ENewsPaper', $where)->row_array();
        return $paper;
    }

    /**
     * 得到一条手机报的前一条信息
     * @param $publish_date
     */
    function find_prev_enewspaper($publish_date) {
        $paper = $this->db->limit(1)->order_by('publishDate', 'DESC')
                    ->where(array('publishDate < ' => $publish_date, 'status' => 0))
                    ->get('ENewsPaper')->row_array();
        return $paper;
    }
    
    /**
     * 得到一条手机报的后一条信息
     * @param $publish_date
     */
    function find_next_enewspaper($publish_date) {
        $paper = $this->db->limit(1)->order_by('publishDate', 'ASC')
                    ->where(array('publishDate > ' => $publish_date, 'status' => 0))
                    ->get('ENewsPaper')->row_array();
        return $paper;
    }
}