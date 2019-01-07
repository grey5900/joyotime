<?php
/**
 * 用户资料管理
 * Create by 2012-3-22
 * @author liuw
 * @copyright Copyright(c) 2012-2014 joyotime
 */
  
 // Define and include
if ( ! defined('BASEPATH')) exit('No direct script access allowed');   
   
 // Code
class Profile extends MY_Controller{
	
	/**
	 * 列表显示用户资料
	 * Create by 2012-3-22
	 * @author liuw
	 */
	public function index(){
		//post参数
		$keyword = $this->post('keyword');
		$keyword = isset($keyword)&&!empty($keyword)?$keyword:'';
		$is_sensored = $this->post('is_sensored');
		$is_sensored = isset($is_sensored)&&!empty($is_sensored)?intval($is_sensored):0;
		if($is_sensored)
			$status = $is_sensored==2?0:$is_sensored;
		$orderField = $this->post('orderField');
		$orderField = isset($orderField)&&!empty($orderField)?$orderField:'lastUpdate';
		$orderDirection = $this->post('orderDirection');
		$orderDirection = isset($orderDirection)&&!empty($orderDirection)?$orderDirection:'desc';
		
		//高级检索
		$begin = $this->post('begin');
		$begin = isset($begin)&&!empty($begin)?$begin:'';
		$end = $this->post('end');
		$end = isset($end)&&!empty($end)?$end:'';
		$user_id = $this->post('user_id');
		$user_id = isset($user_id)&&!empty($user_id)?intval($user_id):'';
		
		//查询总数
		if($user_id)
			$this->db->where('id',$user_id,FALSE);
		else{//高级检索指定了uid的，忽略关键词模糊搜索
			if($keyword && $keyword !== ''){
				$this->db->or_where("(nickname LIKE '%{$keyword}' OR description LIKE '%{$keyword}%')", null, FALSE);
			}
		}
		if(isset($status)){
			if($status == 3){
				$this->db->where('isSensored',2);
				$this->db->where("(avatar='".$this->config->item('default_avatar')."' OR nickname = '' OR description='')",null,FALSE);
			}else
				$this->db->where('isSensored',$status,FALSE);
		}
	
		if($begin || $end){
			if($begin && $end)
				$this->db->where("lastUpdate BETWEEN '{$begin}' AND '{$end}'");
			else if($begin && !$end)
				$this->db->where('lastUpdate >= ',$begin);
			else 
				$this->db->where('lastUpdate <= ',$end);
		}
		$count = $this->db->from('User')->count_all_results();
		if($count){
			//分页属性
			$page_attr = $this->paginate($count);
			//查询数据
			if($user_id)
				$this->db->where('id',$user_id,FALSE);
			elseif($keyword && $keyword !== ''){
				$this->db->or_where("(nickname LIKE '%{$keyword}' OR username LIKE '%{$keyword}%' OR description LIKE '%{$keyword}%')", null, FALSE);
			}
			if(isset($status)){
				if($status == 3){
					$this->db->where('isSensored',2);
					$this->db->where("(avatar='".$this->config->item('default_avatar')."' OR nickname = '' OR description='')",null,FALSE);
				}else
					$this->db->where('isSensored',$status,FALSE);
			}
			if($begin || $end){
				if($begin && $end)
					$this->db->where("lastUpdate BETWEEN '{$begin}' AND '{$end}'");
				else if($begin && !$end)
					$this->db->where('lastUpdate >= ',$begin);
				else 
					$this->db->where('lastUpdate <= ',$end);
			}
			$this->db->order_by($orderField,$orderDirection);
			$this->db->limit($page_attr['per_page_num'], $page_attr['offset']);
			$query = $this->db->get('User');
			$list = array();
			foreach($query->result_array() as $row){
				if($row['isSensored']==2){
					$off = array();
					if($row['avatar'] === $this->config->item('default_avatar') || empty($row['nickname']) || empty($row['description'])){
						//屏蔽了什么
						if($row['avatar'] === $this->config->item('default_avatar'))
							$off[] = '头像';
						if(empty($row['nickname']))
							$off[] = '昵称';
						if(empty($row['description']))
							$off[] = '签名';
						$row['off'] = implode(',', $off);
					}
				}
				//高亮显示敏感词
				$row = inspaction_taboo($row, 'user');
				$list[$row['id']] = $row;
			}
			$this->assign('list',$list);
		}
		$this->assign(compact('keyword','is_sensored','orderDirection','begin','end','user_id'));
		
		$this->display('profile','ugc');
	}
	
	/**
	 * 高级搜索
	 * Create by 2012-3-30
	 * @author liuw
	 */
	public function advsearch(){
		$search_url = site_url(array('ugc','profile','index'));
		$this->assign('do','advsearch');
		$this->assign('search_url',$search_url);
		$this->display('profile','ugc');
	}
	
	/**
	 * 批量审核
	 * Create by 2012-3-30
	 * @author liuw
	 */
	public function examine(){
		if($this->is_post()){
			$ids = $this->post('ids');
			$this->db->where_in('id',$ids)->where('isSensored!=', 2, FALSE)->update('User',array('isSensored'=>1));
			$this->success($this->lang->line('do_success'));
		}else{
			$this->error($this->lang->line('faild_request'));
		}
	}
	
	/**
	 * 审核或屏蔽用户资料
	 * Create by 2012-3-22
	 * @author liuw
	 */
	public function edit(){
		$id = $this->get('id');
		$do = $this->get('do');
		$do = isset($do)&&!empty($do)?$do:FALSE;
		if(!$do || !in_array($do, array('avatar','nickname','description')))
			$this->error($this->lang->line('faild_request'));
		switch($do){
			case 'avatar':
				//设置指定用户的头像为默认头像
				$this->db->where('id',$id)->update('User',array('avatar'=>$this->config->item('default_avatar'),'isSensored'=>2));
				// 修改feed表
// 				$this->db->where('uid',$id)->update('UserFeed',array('avatar'=>$this->config->item('default_avatar')));
				break;
			case 'nickname':
                $user = $this->db->get_where('User', array('id'=>$id))->row_array();
                $nickname = $user['username']?$user['username']:($user['stageCode']==='0'?'新浪微博用户':($user['stageCode']==='1'?'腾讯微博用户':'IN沈阳用户'));
				$this->db->where('id',$id)->update('User',array('nickname'=>$nickname,'isSensored'=>2));
                // 修改feed表
//                 $this->db->where('uid',$id)->update('UserFeed',array('nickname'=>$nickname));
				break;
			case 'description':
				//清空相应的字段值
				$this->db->where('id',$id)->update('User',array('description'=>'','isSensored'=>2));
				break;
		}
		//发系统消息
		$msg_key = 'ugc_profile_'.$do.'_kill';
		$tos = array($id);
		$item_id = array($id);
		$item_type = array($this->config->item('msg_user'));
		$this->load->helper('ugc');
		send_message($msg_key,$tos,$item_id,$item_type);
		
		$this->success($this->lang->line('do_success'));
	}
	
	public function output(){
		echo strpos('user,post','user')===false?'0':'1';
	}
	
}   
   
 // File end