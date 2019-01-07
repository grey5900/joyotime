<?php
/**
 * 机甲后台管理
 * Create by 2012-3-8
 * @author liuw
 * @copyright Copyright(c) 2012-2014 joyotime
 */
  
 // Define and include
if (!defined('BASEPATH'))
    exit('No direct script access allowed');   
   
 // Code
class Account extends MY_Controller{
	
	/**
	 * 列表显示管理帐户
	 * Create by 2012-3-8
	 * @author liuw
	 */
	public function index(){
		
		$type = $this->post('type');
		$keyword = $this->post('keyword');
		$orderfield = $this->post('orderField');
		$orderDirection = $this->post('orderDirection');
		$type = isset($type) && !empty($type) ? intval($type) : 1;
		$keyword = isset($keyword) && !empty($keyword) ? $keyword : FALSE;
		$orderfield = isset($orderfield) && !empty($orderfield) ? $orderfield : 'lasttime';
		$orderDirection = isset($orderDirection) && !empty($orderDirection) ? $orderDirection : 'desc';
		
		$rid = $this->get('rid');
		$rid = isset($rid)&&!empty($rid)?intval($rid):false;
		
		//统计数据长度
		if($keyword){
			switch($type){
				case 1:$this->db->from('MorrisAdmin');$this->db->like('truename', $keyword);if($rid)$this->db->where("FIND_IN_SET({$rid}, role)");break;
				case 2:$this->db->from('MorrisAdmin');$this->db->like('name', $keyword);if($rid)$this->db->where("FIND_IN_SET({$rid}, role)");break;
				case 3:$this->db->select('MorrisAdmin.*');
					$this->db->from('MorrisAdmin,MorrisRole');
					if($rid)
						$this->db->where('MorrisRole.id', $rid);
					$this->db->where("FIND_IN_SET(MorrisRole.id, MorrisAdmin.role)");
					$this->db->like('MorrisRole.name', $keyword);
					break;
			}
		}elseif($rid){
			$this->db->from('MorrisAdmin');
			$this->db->where("FIND_IN_SET({$rid}, role)");
		}else
			$this->db->from('MorrisAdmin');
		$count = $this->db->count_all_results();
		if($count){
			$parr = $this->paginate($count);
			//统计数据长度
			if($keyword){
				switch($type){
					case 1:$this->db->from('MorrisAdmin');$this->db->like('truename', $keyword);$this->db->order_by($orderfield, $orderDirection);if($rid)$this->db->where("FIND_IN_SET({$rid}, role)");break;
					case 2:$this->db->from('MorrisAdmin');$this->db->like('name', $keyword);$this->db->order_by($orderfield, $orderDirection);if($rid)$this->db->where("FIND_IN_SET({$rid}, role)");break;
					case 3:$this->db->select('MorrisAdmin.*');
						$this->db->from('MorrisAdmin,MorrisRole');
						if($rid)
							$this->db->where('MorrisRole.id', $rid);
						$this->db->where("FIND_IN_SET(MorrisRole.id, MorrisAdmin.role)");
						$this->db->like('MorrisRole.name', $keyword);
						$this->db->order_by('MorrisAdmin.'.$orderfield, $orderDirection);
						break;
				}
			}else{
				$this->db->from('MorrisAdmin');
				if($rid)
					$this->db->where("FIND_IN_SET({$rid}, role)");
				$this->db->order_by($orderfield, $orderDirection);
			}
			$this->db->limit($parr['per_page_num'], $parr['offset']);
			$query = $this->db->get();
			$list = array();
			//获取角色
			$roles = get_data('role', FALSE);
			foreach($query->result_array() as $row){
				$row['lasttime'] = mdate('%Y-%m-%d %H:%i', $row['lasttime']);
				$row['state'] = $row['state'] ? '<font color="blue">已启用</font>':'<font color="red">未启用</font>';
				$hasRoles = explode(',', $row['role']);
				$role = array();
				foreach($hasRoles as $rid){
					$role[] = $roles[$rid]['name'];
				}
				$row['role'] = implode(',',$role);
				$list[$row['id']] = $row;
			}
		}
		$this->assign(compact('list','type','keyword','orderfield','orderDirection'));
		$this->display('account');
	}
	
	/**
	 * 添加新帐户
	 * Create by 2012-3-8
	 * @author liuw
	 */
	public function add(){
		if('POST' == $this->server('REQUEST_METHOD')){
			$addnew = array(
				'name' => $this->post('name'),
				'truename' => $this->post('truename'),
				'password' => md5($this->post('password')),
				'state' => intval($this->post('state')),
				'description' => $this->post('description'),
				'dateline' => now(),
				'lasttime' => now(),
				//'vest' => ''
			);
			$rids = $this->post('role');
			if(isset($rids) && !empty($rids))
				$addnew['role'] = implode(',',$rids);
			//检查登录名是否重复
			$count = $this->db->where('name',$addnew['name'])->count_all_results('MorrisAdmin');
			//检查真实名字是否重复
			$tcount = $this->db->where('truename', $addnew['truename'])->count_all_results('MorrisAdmin');
			if(isset($count) && $count > 0){
				$this->error($this->lang->line('account_name_to_repeat'));
			}elseif(isset($tcount) && $tcount > 0){
				$this->error($this->lang->line('account_tname_to_repeat'));
			}else{
				//创建帐号
				$this->db->insert('MorrisAdmin', $addnew);
				$id = $this->db->insert_id();
				if(!isset($id) || !$id){
					$this->error($this->lang->line('do_error'));
				}else{
					//更新角色的帐号数统计
					$sql = 'UPDATE MorrisRole SET accounts = accounts+1 WHERE FIND_IN_SET(id,\''.$addnew['role'].'\')';
					$this->db->query($sql);
					$this->success($this->lang->line('account_add_success'), $this->_index_rel, $this->_index_uri, 'forward');
				}
			}
		}else{
			$act = 'add';
			
			//查询角色列表
			$this->db->order_by('id','asc');
			$query = $this->db->get('MorrisRole');
			$roles = array();
			foreach($query->result_array() as $row){
				$roles[$row['id']] = $row;
			}
			$posturi = site_url(array('account','add'));
			$this->assign(compact('act', 'roles','posturi'));
			$this->display('account');
		}
	}
	
	/**
	 * 删除帐户
	 * Create by 2012-3-8
	 * @author liuw
	 */
	public function delete(){
		$id = $this->get('id');
		if('POST' === $this->server('REQUEST_METHOD')){
			//查询帐号
			$acc = $this->db->where('id', $id)->get('MorrisAdmin')->first_row('array');
			if(isset($acc) && !empty($acc)){
				//删除
				$this->db->delete('MorrisAdmin', array('id'=>$id));
				//更新角色的帐号统计
				$this->db->query('UPDATE MorrisRole SET accounts=accounts-1 WHERE FIND_IN_SET(id,\''.$acc['role'].'\')');
				//更新角色缓存
				get_data('role', TRUE);
				$this->success($this->lang->line('account_del_success'));
			}
		}
	}
	
	/**
	 * 编辑帐户
	 * Create by 2012-3-8
	 * @author liuw
	 */
	public function edit(){
		$id = $this->get('id');
		if('POST' === $this->server('REQUEST_METHOD')){
			$edit = array(
				'truename' => $this->post('truename'),
				'state' => $this->post('state'),
				'description' => $this->post('description'),
			);
			$role_ids = $this->post('role');
			if(isset($role_ids) && !empty($role_ids)){
				$edit['role'] = implode(',', $role_ids);
			}
			$password = $this->post('password');
			if(isset($password) && !empty($password)){
				$edit['password'] = md5($password);
			}
			//查询指定的帐号
			$old = $this->db->where('id',$id)->get('MorrisAdmin')->first_row('array');
			//检查实名
			if(isset($edit['truename'])&&!empty($edit['truename'])&&$edit['truename']!=$old['truename']){
				//检查真实名字是否重复
				$tcount = $this->db->where('truename', $edit['truename'])->count_all_results('MorrisAdmin');
				if(isset($tcount) && $tcount > 0)
					$this->error($this->lang->line('account_tname_to_repeat'));
			}
			//更新原来的角色的帐号统计
			$this->db->query('UPDATE MorrisRole SET accounts=accounts-1 WHERE FIND_IN_SET(id, \''.$old['role'].'\')');
			//更新本次提交的角色的帐号统计
			$this->db->query('UPDATE MorrisRole SET accounts=accounts+1 WHERE FIND_IN_SET(id, \''.$edit['role'].'\')');
			//更新帐号信息
			$this->db->where('id',$id)->update('MorrisAdmin', $edit);
			get_data('role',TRUE);
			$this->success($this->lang->line('account_edit_success'));
		}else{
			$act = 'edit';
			//查询指定的帐号
			$this->db->where('id', $id);
			$acc = $this->db->get('MorrisAdmin')->first_row('array');
			
			$rids = explode(',',$acc['role']);
			
			//查询角色列表
			$this->db->order_by('id','asc');
			$query = $this->db->get('MorrisRole');
			$roles = array();
			foreach($query->result_array() as $row){
				$roles[$row['id']] = $row;
			}
			$posturi = site_url(array('account','edit', 'id', $id));
			$this->assign(compact('act', 'roles','posturi','id','acc', 'rids'));
			$this->display('account');
		}
	}
	
}
   
 // File end