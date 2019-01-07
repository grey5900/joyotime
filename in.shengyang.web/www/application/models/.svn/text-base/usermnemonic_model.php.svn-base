<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * UserMnemonic表操作
 * Author: piglet chenglin.zhu@gmail.com
 * Created on 2012-12-6
 */

class Usermnemonic_Model extends MY_Model {
	
	public function __construct(){
		parent::__construct();
	}
	
	/**
	 * 设置好友备注
	 * Create by 2012-12-25
	 * @author liuweijava
	 * @param int $uid
	 * @param int $mUid
	 * @param string $mnemonic
	 * @return int 0=设置成功；1=设置失败
	 */
	public function set_mnemonic($uid, $mUid, $mnemonic){
		//检查是否有记录
		$r = $this->db->where(array('uid'=>$uid, 'mUid'=>$mUid))->count_all_results($this->_tables['usermnemonic']);
		if($r){
			//更新
			$this->db->where(array('uid'=>$uid, 'mUid'=>$mUid))->set('mnemonic', $mnemonic)->update($this->_tables['usermnemonic']);
			return 0;
		}else{
			$data = compact('uid', 'mUid', 'mnemonic');
			$this->db->insert($this->_tables['usermnemonic'], $data);
			//检查
			$r = $this->db->where(array('uid'=>$uid, 'mUid'=>$mUid))->count_all_results($this->_tables['usermnemonic']);
			return $r ? 0 : 1;
		}
	}
	
}