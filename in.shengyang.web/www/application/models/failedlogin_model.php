<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * 对表FailedLogin的操作
 * Author: piglet chenglin.zhu@gmail.com
 * Created on 2012-11-30
 */

class Failedlogin_Model extends MY_Model {
	/**
	 * 删除过期的banned
	 * @param datetime $banned_time
	 * @reutrn bool
	 */
	function delete_by_time($banned_time) {
		// 删除了已经超过设定时间的banned这样子，才能登录啦
		return $this->db->delete($this->_tables['failedlogin'], array('lastDate < ' => $banned_time));
	}
	
}
