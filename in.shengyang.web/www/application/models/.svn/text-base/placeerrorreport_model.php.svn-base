<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * PlaceErrorReport表操作
 * Author: piglet chenglin.zhu@gmail.com
 * Created on 2012-12-6
 */

class Placeerrorreport_Model extends MY_Model {
	
	/**
	 * 增加报错
	 * Create by 2012-12-21
	 * @author liuweijava
	 * @param array $error
	 * @return boolean
	 */
	public function add_report($error){
		$this->db->insert($this->_tables['placeerrorreport'], $error);
		$err_id = $this->db->insert_id();
		if(!$err_id)//保存报错失败了
			return false;
		else{
			//更新地点状态
			$this->db->where('id', $error['placeId'])->set('isConfirm', 0)->update($this->_tables['place']);
			return true;
		}
	}
	
}