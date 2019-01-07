<?php
/**
 * 角色管理
 * Create by 2012-3-6
 * @author liuw
 * @copyright Copyright(c) 2012-2014 joyotime
 */
  
 // Define and include   
if (!defined('BASEPATH'))
    exit('No direct script access allowed');   
   
 // Code
class Role extends MY_Controller{
	
	/**
	 * 角色列表
	 * Create by 2012-3-6
	 * @author liuw
	 * @param string $keyword
	 * @param int $page
	 * @return array
	 */
	public function index(){
		$keyword = $this->post('keyword');
		$keyword = isset($keyword) && !empty($keyword) ? $keyword : '';
		
		//查询数据长度
		$this->db->where('id!=',$this->config->item('superadmin'), FALSE);
		if(!empty($keyword)){
			$this->db->like('name',$keyword);
		}
		$count = $this->db->count_all_results('MorrisRole');
		if($count){
			$parr = $this->paginate($count);
			$list = array();
			//查询数据
			$arr = array($this->config->item('superadmin'));
			$sql = 'SELECT mr.*, GROUP_CONCAT(c.catName) AS catName FROM MorrisRole mr LEFT JOIN WebNewsCategory c ON FIND_IN_SET(c.id, mr.newsRights) WHERE mr.id != ? ';
			if(!empty($keyword)){
				$sql .= 'AND mr.name LIKE \'%'.$keyword.'%\' ';
			}
			$sql .= 'GROUP BY mr.id ORDER BY mr.id DESC LIMIT '.$parr['offset'].', '.$parr['per_page_num'];
			$this->db->select('MorrisRole.*, GROUP_CONCAT(WebNewsCategory.catName) AS catName');
		/*	$this->db->join('WebNewsCategory')
			$this->db->where('id!=',$this->config->item('superadmin'), FALSE);
			if(!empty($keyword)){
				$this->db->like('name',$keyword);
			}
			$this->db->order_by('id','desc');
			$this->db->limit($parr['per_page_num'], $parr['offset']);
			$query = $this->db->get('MorrisRole');*/
			$query = $this->db->query($sql, $arr)->result_array();
			foreach($query as $row){
				$list[$row['id']] = $row;
			}
		}
		$data = array(
			'list' => $list,
		);
		if(!empty($keyword)){
			$data['keyword'] = $keyword;
		}
		$this->assign($data);		
		$this->display('role');
	}
	
	/**
	 * 添加角色
	 * Create by 2012-3-6
	 * @author liuw
	 */
	public function add(){
		if($this->is_post()){
			$statusCode = 200;
			$message = $this->lang->line('role_add_success');
			$navTabId = $this->_uri;
			$name = $this->post('name');
			//长度超过20,报错
			if(strlen($name) > 20)
				$this->error($this->lang->line('role_name_to_lang'));
			else{
				$desc = $this->post('description');
				$desc = isset($desc) && !empty($desc) ? $desc : '';
				//检查角色名是否重复
				$this->db->where('name',$name);
				$result = $this->db->get('MorrisRole')->first_row();
				if(isset($result) && !empty($result) && $result->id){
					$statusCode = 300;
					$message = $this->lang->line('role_name_to_repeat');
					$navTabId = '';
					$this->message($statusCode, $message, $navTabId, $this->_uri);
				}else{
					$data = array(
						'name'=>$name,
						'rights'=>'',
						'description'=>$desc
					);
					$cids = $this->post('cids');
					!empty($cids) && $data['newsRights'] = implode(',', $cids);
					$this->db->set($data);
					$this->db->insert('MorrisRole');
					//更新缓存
					get_data('role',TRUE);
					$this->message($statusCode, $message, $this->_index_rel, $this->_index_uri, 'closeCurrent');
				}
			}
		}else{
			$name = $description = '';
			$uri = site_url(array('role','add'));
			$cats = $this->_get_cates();
			$this->assign(compact('name','description','uri', 'cats'));
			$this->display('role_add');
		}
	}
	
	/**
	 * 编辑角色
	 * Create by 2012-3-6
	 * @author liuw
	 * @param int $id
	 */
	public function edit(){
		$id = $this->get('id');
		$type = $this->get('type');
		$type = !empty($type) ? $type : 'edit';
		//查询数据
		$this->db->where('id',$id);
		$role = $this->db->get('MorrisRole')->first_row('array');
		if('edit' == $type){
			if('POST' == $this->server('REQUEST_METHOD')){
				$statusCode = 200;
				$message = $this->lang->line('do_success');
				$navTabId = $this->_uri;
				$name = $this->post('name');
				$desc = $this->post('description');
				$this->db->where('id', $id);
				$old = $this->db->get('MorrisRole')->first_row('array');
				//检查新的角色名是否被占用
				$this->db->where('name',$name);
				$r = $this->db->count_all_results('MorrisRole');
				if(isset($r) && ($r && $old['name'] != $name)){
					$statusCode = 300;
					$message = $this->lang->line('role_name_to_repeat');
					$navTabId = '';
				}else{
					//修改
					$edit = array('name'=>$name,'description'=>$desc);
					$cids = $this->post('cids');
					if(empty($cids))
						$edit['newsRights'] = '';
					else 
						$edit['newsRights'] = implode(',', $cids);
					$this->db->where('id',$id);
					$this->db->update('MorrisRole',$edit);
					//更新缓存
					get_data('role',TRUE);
				}
				$this->message($statusCode, $message);
			}
			$uri = site_url(array('role','edit')).'/id/'.$id.'/type/'.$type;
			$cats = $this->_get_cates();
			if(!empty($role['newsRights'])){
				$checked = explode(',', $role['newsRights']);
				foreach($cats as &$cat){
					in_array($cat['id'], $checked) && $cat['checked'] = 1;
					unset($cat);
				}
			}
			$this->assign(compact('role','uri','cats'));
			$this->assign('act','edit');
			$this->display('role_add');
		}elseif('setrights' == $type){
			if('POST' == $this->server('REQUEST_METHOD')){
				$rids = $this->post('rids');
				$statusCode = 200;
				$message = $this->lang->line('do_success');
				$navTabId = $this->_uri;
				//查询所有的rid,从path字段获取
				if(isset($rids) && !empty($rids)){
					$this->db->where_in('id',$rids);
					$query = $this->db->get("MorrisRights");
					$rlist = array();
					foreach($query->result_array() as $row){
						$rlist = array_merge($rlist,explode(',',substr($row['path'],2)));
					}
					$rlist = array_unique($rlist);
					$edit = array('rights'=>implode(',',$rlist));
				}else{
					$edit = array('rights'=>'');
				}
				//设置权限
				$this->db->where('id', $id);
				$this->db->update('MorrisRole', $edit);
				//更新缓存
				get_data('role',TRUE);
				$this->message($statusCode, $message, $this->_index_rel, $this->_index_uri, 'closeCurrent');
			}
			$oldR = isset($role['rights']) && !empty($role['rights']) ? explode(',',$role['rights']) : FALSE;
			//获得全部权限
			$this->db->order_by('depth','asc');
			$query = $this->db->get('MorrisRights');
			$list = array();
			foreach($query->result_array() as $row){
				$path = '['.str_replace(',','][', substr($row['path'],2)).']';
				$hasChecked = isset($oldR) && $oldR ? (is_array($oldR) && in_array($row['id'], $oldR) ? TRUE : ($row['id'] == $oldR ? TRUE : FALSE)) : FALSE;
				eval("\$list{$path} = array('id'=>'{$row['id']}','name'=>'{$row['name']}','checked'=>'{$hasChecked}');");
			}
			$name = $role['name'];
        	$this->load->helper('menu');
			$rHtml = build_check_tree($list);
			$this->assign(compact('id','name','rHtml'));
			$this->display('role_setrights');
		}elseif('setnewsRights' == $type){
			if('POST' == $this->server('REQUEST_METHOD')){
				$rids = $this->post('rids');
				//var_dump($rids);exit;
				$statusCode = 200;
				$message = $this->lang->line('do_success');
				$navTabId = $this->_uri;
				//查询所有的rid,从path字段获取
				if(isset($rids) && !empty($rids)){
					$this->db->where_in('id',$rids);
					$query = $this->db->get("WebNewsCategory");
					$rlist = array();
					foreach($query->result_array() as $row){
						$rlist = array_merge($rlist,array(0=>$row['id']),explode(',',substr($row['catPath'],2)));
					}
					$rlist = array_filter(array_unique($rlist));
					$edit = array('newsRights'=>implode(',',$rlist));
				}else{
					$edit = array('newsRights'=>'');
				}
				//设置权限
				$this->db->where('id', $id);
				$this->db->update('MorrisRole', $edit);
				//更新缓存
				get_data('role',TRUE);
				$this->message($statusCode, $message, $this->_index_rel, $this->_index_uri, 'closeCurrent');
			}
			$oldR = isset($role['newsRights']) && !empty($role['newsRights']) ? explode(',',$role['newsRights']) : FALSE;
			//获得全部权限
			$this->db->order_by('depth','asc');
			$query = $this->db->get('WebNewsCategory');
			$list = array();
			foreach($query->result_array() as $row){
				//$path = '['.str_replace(',','][', substr($row['catPath'],2)).']';
				$arr = array_filter(explode(",",$row['catPath']));//array_reverse(array_filter(explode(",",$v['catPath'])));
				$path = '';
				if(!empty($arr)){
					foreach($arr as $value){
						$path.= "[$value]";
					}
				}
				$path .= "[{$row[id]}]";
				$hasChecked = isset($oldR) && $oldR ? (is_array($oldR) && in_array($row['id'], $oldR) ? TRUE : ($row['id'] == $oldR ? TRUE : FALSE)) : FALSE;
				
				
				eval("\$list{$path} = array('id'=>'{$row['id']}','name'=>'{$row['catName']}','checked'=>'{$hasChecked}');");
				
			}//var_dump($list);
			$name = $role['name'];
        	$this->load->helper('menu');
			$rHtml = build_check_tree($list);
			$this->assign(compact('id','name','rHtml'));
			$this->display('role_setnewsrights');
		}
	}
	
	/**
	 * 删除角色
	 * Create by 2012-3-6
	 * @author liuw
	 */
	public function delete(){
		$id = $this->get('id');
		if('POST' == $this->server('REQUEST_METHOD')){
			$statusCode=200;
			$message = $this->lang->line('role_del_success');
			$navTabId = $this->_uri;
			//删除
			$this->db->where('id',$id);
			$this->db->delete('MorrisRole');
			//更新
			$sql = "UPDATE MorrisAdmin SET role=REPLACE(role,',{$id},',',') WHERE FIND_IN_SET('{$id}',role)";
			$this->db->query($sql);
			//更新缓存
			get_data('role',TRUE);
			$this->message($statusCode, $message);
		}else{
			$this->message(300, '非法操作!');
		}
	}
	
	/**
	 * 获取频道列表 
	 * Create by 2012-12-10
	 * @author liuweijava
	 */
	private function _get_cates(){
		$list = $this->db->where(array('status < '=>2, 'parentId'=>0))->order_by('parentId', 'asc')->order_by('orderValue', 'desc')->get('WebNewsCategory')->result_array();
		return $list;
	}
	
}
   
 // File end