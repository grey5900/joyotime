<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * UserOwnMemberCard表操作
 * Author: piglet chenglin.zhu@gmail.com
 * Created on 2012-12-6
 */

class Userownmembercard_Model extends MY_Model {
	
	public function __construct(){
		parent::__construct();
	}
	
	/**
	 * 统计会员卡数量
	 * Create by 2012-12-27
	 * @author liuweijava
	 * @param int $uid
	 * @param boolean $is_basic 是否只统计基础会员卡数量
	 * @return int
	 */
	public function count_cards($uid, $is_basic = true){
		$this->db->join($this->_tables['brandmembercard'], $this->_tables['brandmembercard'].'.id='.$this->_tables['userownmembercard'].'.memberCardId', 'inner');
		$this->db->where(array($this->_tables['userownmembercard'].'.uid'=>$uid, $this->_tables['brandmembercard'].'.status'=>0));
		if($is_basic)
			$this->db->where($this->_tables['userownmembercard'].'.isBasic', 1);
		return $this->db->count_all_results($this->_tables['userownmembercard']);
	}
	
	/**
	 * 查询会员卡列表
	 * Create by 2012-12-27
	 * @author liuweijava
	 * @param int $uid
	 * @param boolean $is_basic 是否只统计基础会员卡数量
	 * @param int $size
	 * @param int $offset
	 * @return array
	 */
	public function list_cards($uid, $is_basic=true, $size=20, $offset=0){
		$this->db->select($this->_tables['brandmembercard'].'.*, '.$this->_tables['userownmembercard'].'.createDdate AS gotDate');
		$this->db->distinct();
		$this->db->join($this->_tables['brandmembercard'], $this->_tables['brandmembercard'].'.id='.$this->_tables['userownmembercard'].'.memberCardId', 'inner');
		$this->db->where(array($this->_tables['userownmembercard'].'.uid'=>$uid, $this->_tables['brandmembercard'].'.status'=>0));
		if($is_basic)
			$this->db->where($this->_tables['userownmembercard'].'.isBasic', 1);
		$list = $this->db->order_by($this->_tables['userownmembercard'].'.createDate', 'desc')->limit($size, $offset)->get($this->_tables['userownmembercard'])->result_array();
		foreach($list as &$row){
			//图片
			$row['image'] = image_url($row['image'], 'common');
			$row['gotDate'] = get_date($row['gotDate']);
		}
		unset($row);
		return $list;
	}
	
}