<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/*
 * 拦截器
 *
 * Author: piglet chenglin.zhu@gmail.com
 * Created on 2013-08-15
 */

class Interceptor {
    /**
     * 构造函数
     */
    function __construct() {
    	$this->ci = &get_instance();
    }
	
    /**
     * 用于更新楼盘的团购和推荐状态
     */
    function flush_house_status() {
    	if(in_array($this->ci->cur_uri, $this->ci->config->item('flush_house_status'))) {
    		// 那么去更新状态吧，这里暂时不需要去缓存什么的，因为现在量不大，去更新吧，不影响的
    		$house_id = $this->ci->get_post('id');
    		$where_arr = array();
    		if($house_id) {
    			// 只更新一个楼盘
    			$where_arr[] = "house_id = '{$house_id}'";
    		}
    		
    		$today_date = now(TIMESTAMP, 'Y-m-d');
    		
    		foreach(array('group', 'recommend') as $type) {
    			foreach(array(0, 1) as $status) {
    				$arr = $where_arr;
    				$arr[] = "has_{$type} = 1";
    				$arr[] = "{$type}_status = {$status}";
    				$arr[] = "{$type}_expire_date ".($status?'>=':'<')." '{$today_date}'";
    				
    				$this->ci->db->where(join(" and ", $arr))
    					->update('house', array("{$type}_status" => $status?0:1));
    			}
    		}
    	}
    }
}
