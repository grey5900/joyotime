<?php
/**
 * 马甲管理
 * Create by 2012-4-1
 * @author liuw
 * @copyright Copyright(c) 2012-2014 joyotime
 */
  
 // Define and include
if ( ! defined('BASEPATH')) exit('No direct script access allowed');   
   
 // Code
class Vest extends MY_Controller{
	
	/**
	 * 列表显示马甲
	 * Create by 2012-4-1
	 * @author liuw
	 */
	public function index(){
		$type = $this->post('type');
		$type = isset($type)&&!empty($type)?$type:FALSE;
		$keyword = $this->post('keyword');
		$keyword = isset($keyword)&&!empty($keyword)?$keyword:FALSE;
		$is_first = $this->post('is_first');
		$is_first = isset($is_first)&&!empty($is_first)?1:0;
		$this->assign(compact('type','keyword','is_first'));
		
		//查询总数
		$this->db->from('MorrisVest');
		if($type && $keyword){
			switch($type){
				case 1://查询username
					$this->db->join('User','User.id=MorrisVest.uid');
					$this->db->like('User.username', $keyword);
					$this->db->or_like('User.email', $keyword);
					break;
				case 2://查询adminname
					$this->db->join('MorrisAdmin','MorrisAdmin.id=MorrisVest.aid');
					$this->db->like('MorrisAdmin.truename', $keyword);
					break;
			}
		}
		if($is_first)
			$this->db->where('MorrisVest.isFirst',$is_first, FALSE);
		$count = $this->db->count_all_results();
		//有数据才执行下面的逻辑
		if($count){
			//分页
			$paginate = $this->paginate($count);
			//查询数据列表
			$list = array();
			$this->db->select('MorrisVest.*,User.username,User.nickname,User.avatar,MorrisAdmin.name, MorrisAdmin.truename');
			$this->db->from('MorrisVest');
			$this->db->join('User','User.id=MorrisVest.uid', 'inner');
			$this->db->join('MorrisAdmin','MorrisAdmin.id=MorrisVest.aid', 'left');
			if($type && $keyword){
				switch($type){
					case 1:
						$this->db->like('User.username',$keyword);
						$this->db->or_like('User.email', $keyword);
						break;
					case 2:$this->db->like('MorrisAdmin.truename', $keyword);break;
				}
			}
			if($is_first)
				$this->db->where('MorrisVest.isFirst', $is_first, FALSE);
			$this->db->order_by('MorrisVest.aid', 'desc')->order_by('MorrisVest.dateline', 'desc');
			$this->db->limit($paginate['per_page_num'], $paginate['offset']);
			$query = $this->db->get();
			foreach($query->result_array() as $row){
				$list[] = $row;
			}
			$this->assign('list',$list);
		}
		//功能的url
		$add_url = site_url(array('vest','vest','add'));
		$del_url = site_url(array('vest','vest','delete'));
		$dis_url = site_url(array('vest','vest','distribute','do','view'));
		$this->assign(compact('add_url','del_url','dis_url'));
		$this->display('index','vest');
	}
	
	/**
	 * 添加马甲
	 * Create by 2012-4-1
	 * @author liuw
	 */
	public function add(){
		if($this->is_post()){
			$username = $this->post('username');
			$password = strtoupper(md5(trim($this->post('password'))));
			//查找用户并检查密码是否正确
			$row = $this->db->select('id,password')->where('username',$username)->get('User')->first_row('array');
			!$row && $row = $this->db->select('id,password')->where('email',$username)->get('User')->first_row('array');
			if(!isset($row)||empty($row))
				$this->error($this->lang->line('vest_user_has_not_here'), $this->_index_rel, $this->_index_uri, 'forward');
			elseif($row['password'] != $password)
				$this->error($this->lang->line('vest_user_password_error'), $this->_index_rel, $this->_index_uri, 'forward');
			else{
				//检查是否已添加
				$count = $this->db->where('uid',$row['id'])->count_all_results('MorrisVest');
				if(isset($count) && $count > 0)
					$this->error($this->lang->line('vest_user_has_added'), $this->_index_rel, $this->_index_uri, 'forward');
				else{
					//添加马甲
					$data = array('uid'=>$row['id'],'dateline'=>time());
					$this->db->insert('MorrisVest', $data);
					$this->success($this->lang->line('vest_add_success'), $this->_index_rel, $this->_index_uri, 'forward');
				}
			}
		}
		$this->assign('do','add');
		$this->display('index','vest');
	}
	
	/**
	 * 删除马甲
	 * Create by 2012-4-1
	 * @author liuw
	 */
	public function delete(){
		if($this->is_post()){
			$ids = $this->post('ids');
			//解析参数
			$uids = $aids = array();
			foreach($ids as $id){
				$idArr = explode('+', $id);
				$uids[] = intval($idArr[0]);
				$aids[] = intval($idArr[1]);
			}
			$this->db->where_in('uid',$uids)->where_in('aid',$aids);
			$this->db->delete('MorrisVest');
			$this->success($this->lang->line('vest_delete_success'), $this->_index_rel, $this->_index_uri, 'forward');
		}
	}
	
	/**
	 * 派发马甲
	 * Create by 2012-4-1
	 * @author liuw
	 */
	public function distribute(){
		$do = $this->get('do');
		$do = isset($do)&&!empty($do)?$do:'view';
		
		if($do === 'post'){
			$uids = explode(',',$this->post('uids'));
			$aid = $this->post('aid');
			$aid = isset($aid)&&!empty($aid)?intval($aid):0;
			$edit = array('aid'=>$aid);
			//派发
			$this->db->where_in('uid',$uids)->update('MorrisVest',array('aid'=>$aid, 'isFirst'=>0));
			$this->success($this->lang->line('vest_dis_success'), $this->_index_rel, $this->_index_uri, 'closeCurrent');
		}else{
			$uid = $this->get('sids');
			$post_url = site_url(array('vest','vest','distribute','sids',$uid,'do','post'));
			$this->assign('post_url', $post_url);
			if(!isset($uid)||empty($uid))
				$this->error('请选择一个马甲', $this->_index_rel, $this->_index_uri, 'closeCurrent');
			$uid = str_replace('|',',',urldecode($uid));
			$this->assign('uids', $uid);
			//获得管理员列表
			$this->assign('dlg_rel', build_rel(array('vest','vest','dis')));
			$truename = $this->get('str');
			$truename = isset($truename)&&!empty($truename)?urldecode($truename):FALSE;
			$list = array();
			if($truename != FALSE){
				$this->assign('truename', $truename);
				$this->db->like('truename', $truename);
			}
			$query = $this->db->where(array('state='=>1/*,'id<>'=>1*/),null,FALSE)->order_by('dateline', 'desc')->get('MorrisAdmin');
			foreach($query->result_array() as $row){
				$list[$row['id']] = $row;
			}	
			$this->assign('admins', $list);
			$this->assign('do','distribute');
			$this->display('index','vest');
		}
	}	
}   
   
 // File end