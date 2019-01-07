<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * UserResetPwd表操作
 * Author: piglet chenglin.zhu@gmail.com
 * Created on 2012-12-6
 */

class Userresetpwd_Model extends MY_Model {
	
	public function insert($data){
		$this->db->insert($this->_tables['userresetpwd'], $data);
	}
	
	public function update($data){
		$this->db->where('email', $data['email'])->set($data)->update($this->_tables['userresetpwd']);
	}
	
	public function used_reset($id){
		$this->db->where('id', $id)->set('isUse', 1, false)->update($this->_tables['userresetpwd']);
	}
	
}