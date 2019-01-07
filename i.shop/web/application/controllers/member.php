<?php
/**
 * 会员管理
 * Create by 2012-9-26
 * @author liuw
 * @copyright Copyright(c) 2012-
 */
  
 // Define and include
if ( ! defined('BASEPATH')) exit('No direct script access allowed');   
   
 // Code
class Member extends MY_Controller{
	
	/**
	 * 会员列表
	 * Create by 2012-9-27
	 * @author liuw
	 * @param int $page
	 */
	function index($keyword='all', $page=1){
		!empty($keyword) && $keyword !== 'all' && $keyword=urldecode($keyword);
		$list = array();
		$brand_id = $this->auth['brand_id'];
		$arr = array($keyword);
		//查询会员总数
		$this->db->where(array('UserOwnMemberCard.brandId'=>$brand_id, 'UserOwnMemberCard.isBasic'=>1));
		if(!empty($keyword) && $keyword !== 'all'){
			$this->db->join('User', 'User.id=UserOwnMemberCard.uid', 'inner');
			$this->db->where("(User.username LIKE '%{$keyword}%' OR User.nickname LIKE '%{$keyword}%' OR User.description LIKE '%{$keyword}%')");
		}
		$count = $this->db->count_all_results('UserOwnMemberCard');
		if($count){
			//分页
			$parr = paginate('/member/index', $count, $page, $arr);
			$no_start = $count-$parr['offset'];
			//列表
			$this->db->select('User.id, User.username, User.nickname, User.description, User.avatar, UserOwnMemberCard.createDate');
			$this->db->where(array('UserOwnMemberCard.brandId'=>$brand_id, 'UserOwnMemberCard.isBasic'=>1));
			if(!empty($keyword) && $keyword !== 'all')
				$this->db->where("(User.username LIKE '%{$keyword}%' OR User.nickname LIKE '%{$keyword}%' OR User.description LIKE '%{$keyword}%')");
			$this->db->join('User', 'User.id=UserOwnMemberCard.uid','left');
			$query = $this->db->order_by('UserOwnMemberCard.createDate', 'desc')->limit($parr['size'], $parr['offset'])->get('UserOwnMemberCard')->result_array();
			foreach($query as $row){
				empty($row['username']) && $row['username'] = '游客or第三方账号';
				$row['nickname'] = !empty($row['nickname']) ? $row['nickname'] : $row['username'];
				$row['avatar'] = image_url($row['avatar'], 'head', 'hhdp');
				$row['createDate'] = gmdate('Y/m-d H:i', strtotime($row['createDate'])+8*3600);
				$row['number'] = $no_start--;//Add by 2012-11-20: 给会员编号，根据页码和会员总数确定，倒序
				$list[] = $row;
			}		
		}
		$this->assign('list', $list);
		$this->assign('active', 'member');
		$keyword === 'all' && $keyword = '';
		$this->assign(compact('keyword', 'page'));
		$this->display('index');
	}
	
}   
   
 // File end