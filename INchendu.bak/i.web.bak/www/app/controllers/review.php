<?php
/**
 * post内容页
 * Create by 2012-5-23
 * @author liuw
 * @copyright Copyright(c) 2012-2014 joyotime
 */
  
 // Define and include
if ( ! defined('BASEPATH')) exit('No direct script access allowed');   
   
 // Code
class Review extends MY_Controller{
	
	/**
	 * 内容页显示 
	 * Create by 2012-5-23
	 * @author liuw
	 * @param int $post_id
	 * @param int $page
	 */
	public function index($post_id, $page=1){
		//post信息
		$sql = 'SELECT p.*, pl.level AS p_level, pl.isBusiness,pl.placename, pl.icon,pl.description,pl.latitude,pl.longitude,pl.address, pl.tel, pl.isVerify, if(u.nickname is not null and u.nickname != \'\',u.nickname,u.username) AS uname, u.avatar, u.exp, u.isVerify FROM Post p, User u, Place pl WHERE pl.id=p.placeId AND u.id=p.uid AND p.id=? AND p.type<>1';
		$post = $this->db->query($sql, array($post_id))->first_row('array');
		if($post['status'] > 1) {
		    $this->showmessage('对不起，您访问的内容不存在');
		}
		if($post['type'] == $this->config->item('post_photo'))
			$post['original_photo'] = !empty($post['photoName']) ? image_url($post['photoName'], 'user', 'odp') : '';
		$post['photo'] = !empty($post['photoName']) ? image_url($post['photoName'], 'user', 'mdp') : '';
		$post['uname'] = get_my_desc($this->auth['id'], array('uid'=>$post['uid'], 'name'=>$post['uname']));
		//头像 
		$post['avatar'] = image_url($post['avatar'], 'head', 'hmdp');
		//评分星级
		$post['star'] = ceil($post['level']);
		//地点星级
		$post['p_star'] = ceil($post['p_level']);
		//地点图标
		// if(empty($post['icon'])){
			// //地点没有图标，查询地点分类的图标
			// $sql = 'SELECT pc.icon FROM PlaceCategory pc, PlaceOwnCategory poc WHERE poc.placeCategoryId=pc.id AND poc.placeId=? LIMIT 1';
			// $rs = $this->db->query($sql, array($post['placeId']))->first_row('array');
			// if(!empty($rs) && !empty($rs['icon']))
				// $post['icon'] = $rs['icon'];
		// }
		// //图标有问题，没有取地点自己的图标！！
		// $post['icon'] = !empty($post['icon']) ? image_url($post['icon'], 'common', 'odp') : '';
        //地点图标
        $sql = 'SELECT c.icon FROM PlaceCategory c, PlaceOwnCategory oc WHERE oc.placeCategoryId=c.id AND oc.placeId=? ORDER BY c.isBrand DESC';
        $q = $this->db->query($sql, array($post['placeId']))->first_row('array');
        $post['icon'] = image_url($q['icon'], 'common', 'odp');
        if(empty($post['icon'])){
            //地点没有图标，查询地点分类的图标
            $sql = 'SELECT pc.icon FROM PlaceCategory pc, PlaceOwnCategory poc WHERE poc.placeCategoryId=pc.id AND poc.placeId=? LIMIT 1';
            $rs = $this->db->query($sql, array($post['placeId']))->first_row('array');
            // if(!empty($rs) && !empty($rs['icon']))
                // $post['icon'] = $rs['icon'];
            $post['icon'] = empty($rs['icon'])?(image_url($rs['categoryIcon'], 'common', 'odp')):image_url($rs['icon'], 'common', 'odp');
        }
		//统计收藏数量
		$f_count = $this->db->where(array('itemId'=>$post_id, 'itemType'=>$post['type']))->count_all_results('UserFavorite');
    	$post['content'] = nl2br(htmlspecialchars($post['content']));
		$post['favoriteCount'] = $f_count;		
		$this->assign('info', $post);
		//发布者等级
		$sql = 'SELECT level FROM UserLevelConstans WHERE minExp <= ? AND maxExp > ? LIMIT 1';
		$arr = array($post['exp'], $post['exp']);
		$lv = $this->db->query($sql, $arr)->first_row('array');
		$this->assign('user_lv', $lv['level']);
		//赞过的人
		$sql = 'SELECT u.id, u.avatar, IF(u.nickname IS NOT NULL AND u.nickname != \'\', u.nickname, u.username) AS uname, u.isVerify FROM User u, UserPraise up WHERE up.uid=u.id AND up.itemType=? AND up.itemId=? ORDER BY up.createDate DESC';
		$arr = array($post['type'], $post_id);
		$praiser = array();
		$praise_query = $this->db->query($sql, $arr)->result_array();
		foreach($praise_query as $row){
			$row['uname'] = get_my_desc($this->auth['id'],array('uid'=>$row['id'],'name'=>$row['uname']));
			//头像
			$row['avatar'] = image_url($row['avatar'], 'head', 'hmdp');
			$praiser[$row['id']] = $row;
		}
		$this->assign('praisers', $praiser);
		
		//检查是否已赞
		$has_praise = $this->db->where(array('uid'=>$this->auth['id'], 'itemId'=>$post_id, 'itemType'=>$post['type']))->count_all_results('UserPraise');
		$this->assign('has_praise', $has_praise > 0);
		//检查是否已收藏
		$has_favorite = $this->db->where(array('uid'=>$this->auth['id'], 'itemId'=>$post_id, 'itemType'=>$post['type']))->count_all_results('UserFavorite');
		$this->assign('has_favorite', $has_favorite > 0);
		
    	$this->assign('site_keywords', $post['uname'].','.$post['placename'].','.$this->lang->line('site_keywords'));
    	$this->assign('site_description', $post[content].'；'.$this->lang->line('site_description'));
		//根据post类型加载模板
		if($post['type'] == $this->config->item('post_tip')){
			$post['str_type'] = '点评';
			$arr = array($post_id, $post['type']);
			//前一条
			$sql = 'SELECT id FROM Post WHERE id < ? AND type=? AND status <= 1 ORDER BY id DESC LIMIT 1';
			$rs = $this->db->query($sql, $arr)->first_row('array');
			$this->assign('prev_post', !empty($rs['id']) ? $rs['id'] : -1);
			//后一条
			$sql = 'SELECT id FROM Post WHERE id > ? AND type=? AND status <= 1 ORDER BY id ASC LIMIT 1';
			$rs = $this->db->query($sql, $arr)->first_row('array');
			$this->assign('next_post', !empty($rs['id']) ? $rs['id'] : -1);

	    	$this->assign('site_title', $post['uname'].'在'.$post['placename'].'的点评 - '.$this->lang->line('site_title'));
			$this->display('tip');
		}elseif($post['type'] == $this->config->item('post_photo')){
			//分页条
			$this->img_paginate($post);

	    	$this->assign('site_title', $post['uname'].'在'.$post['placename'].'的照片 - '.$this->lang->line('site_title'));
			$this->display('image');
		}


	}
	
	/**
	 * 回复列表
	 * Create by 2012-5-25
	 * @author liuw
	 * @param int $post_id
	 * @param int $page
	 */
	public function replies($post_id, $page=1){
		$list = array();
		//检查回复总长度
		$count = $this->db->where(array('postId'=>$post_id, 'status <= '=>1))->count_all_results('PostReply');
		if($count){
			//分页
			$parr = js_paginate('/review_replies', $count, $page, 20, 7, 'load_reply(\'@{url}\', \''.$post_id.'\', \'@{page}\');');
			//查询数据
			$sql = 'SELECT pr.*, IF(s.nickname IS NOT NULL AND s.nickname != \'\', s.nickname, s.username) AS s_name, s.avatar AS avatar, s.isVerify AS s_is_v, IF(r.nickname IS NOT NULL AND r.nickname != \'\',r.nickname, r.username) AS r_name, r.isVerify AS r_is_v FROM PostReply pr INNER JOIN User s ON s.id=pr.uid LEFT JOIN User r ON pr.replyUid IS NOT NULL AND r.id=pr.replyUid WHERE pr.postId=? AND pr.status<=1 ORDER BY pr.createDate DESC LIMIT ?, ?';
			$arr = array($post_id, $parr['offset'], $parr['per_page_num']);
			$query = $this->db->query($sql, $arr)->result_array();
			foreach($query as $row){
				
				//头像
				$row['avatar'] = image_url($row['avatar'], 'head', 'hmdp');
				if($row['uid'] == $this->auth[id])
					$row['s_name'] = '您';
				else 
					$row['s_name'] = get_my_desc($this->auth['id'],array('uid'=>$row['uid'],'name'=>$row['s_name']));
				
				if($row['replyUid'] == $this->auth[id])
					$row['r_name'] = '您';
				else 
					$row['r_name'] = get_my_desc($this->auth['id'],array('uid'=>$row['replyUid'],'name'=>$row['r_name']));				
				
				$row['content'] = htmlspecialchars($row['content']);
				
				//标题
				//if(empty($row['replyUid']))
				//	$row['title'] = $row['s_name'].':';
				//elseif($row['uid'] == $row['replyUid'])
				//	$row['title'] = $row['s_name'].(!empty($row['replyId'])?'回复了自己':'').':';
				//else 	
				//	$row['title'] = $row['s_name'].'回复了'.$row['r_name'].':';
				//时间
				//$row['createDate'] = substr($row['createDate'], 0, -3)/*gmdate('Y-m-d', strtotime($row['createDate']))*/;
				$list[$row['id']] = $row;
			}
		}
		
		$this->assign('list', $list);
		$this->display('replies');
	}
	
	/**
	 * 赞
	 * Create by 2012-5-25
	 * @author liuw
	 */
	public function praise(){
		if($this->is_post()){
			$result = array();
			$post_id = $this->post('id');
			$uid = $this->auth['id'];
			$type = $this->post('type');
			//检查是否已赞过
			$has_praise = $this->db->where(array('uid'=>$uid, 'itemId'=>$post_id, 'itemType'=>$type))->count_all_results('UserPraise');
			if($has_praise){
				$result = array('code'=>0, 'msg'=>preg_replace("/@{post}/",($type == $this->config->item('post_tip') ? '个点评' : '张照片'),$this->lang->line('praise_has_done')));
			}else{
				//新数据
				$data = array('uid'=>$uid, 'itemId'=>$post_id, 'itemType'=>$type);
				$this->db->insert('UserPraise', $data);
				$id = $this->db->insert_id();
				if(!$id) {
					$result = array('code'=>0,'msg'=>$this->lang->line('praise_faild'));
				}else{
					//更新post的赞数
					$this->db->query('UPDATE Post SET praiseCount=praiseCount+1 WHERE id=?', array($post_id));
					//检查并更新feed的赞数
					$this->db->query('UPDATE UserFeed SET praiseCount=praiseCount+1 WHERE itemId=? AND itemType=?', array($post_id, $type));
					//新的赞
					$data = array('uid'=>$uid, 'avatar'=>image_url($this->auth['avatar'], 'head', 'hmdp'), 'name'=>!empty($this->auth['nickname'])?$this->auth['nickname']:$this->auth['username']);
					$result = array('code'=>1, 'msg'=>preg_replace("/@{post}/",($type == $this->config->item('post_tip') ? '个点评' : '张照片'),$this->lang->line('praise_success')), 'praiser'=>$data);
				}
			}
			$this->echo_json(($result));
		}else{
			$this->echo_json((array('code'=>1, 'msg'=>'赞')));
		}
	}
	
	/**
	 * 收藏
	 * Create by 2012-5-25
	 * @author liuw
	 */
	public function favorite(){
		if($this->is_post()){
			$resultl = array();
			$uid = $this->auth['id'];
			$post_id = $this->post('id');
			$post_type = $this->post('type');
			//检查是否已收藏
			$has_favorite = $this->db->where(array('uid'=>$uid, 'itemId'=>$post_id, 'itemType'=>$post_type))->count_all_results('UserFavorite');
			if($has_favorite){
				$msg = $this->lang->line('favorite_has_faved');
				$msg = preg_replace("/@{post}/", $post_type == $this->config->item('post_tip') ? '个点评':'张照片', $msg);
				$result = array('code'=>0, 'msg'=>$msg);
			}else{
				//收藏
				$data = compact('uid', 'itemId', 'itemType');
				$this->db->insert('UserFavorite', $data);
				$id = $this->db->insert_id();
				if(!$id)
					$result = array('code'=>0, 'msg'=>$this->lang->line('favorite_faild'));
				else{
					$msg = preg_replace("/@{post}/", $post_type == $this->config->item('post_tip') ? '个点评':'张照片', $this->lang->line('favorite_success'));
					$result = array('code'=>1, 'msg'=>$msg);
				}
			}
			$this->echo_json(($result));
		}else{
			$this->echo_json((array('code'=>1, 'msg'=>'收藏')));
		}
	}
	
	/**
	 * 回复
	 * Create by 2012-5-28
	 * @author liuw
	 */
	public function reply(){
		if($this->is_post()){
			$uid = $this->auth['id'];
			$content = $this->post('content');
			$post_id = $this->post('pid');
			$reply_id = $this->post('rid');
			$reply_id = isset($reply_id) && !empty($reply_id) ? $reply_id : FALSE;
			//发回复
			$this->echo_json((reply($uid, $content, $post_id, $reply_id)));
		}else{
			exit('post详细页的回复功能');
		}
	}
	
	/**
	 * 照片post的分页
	 * Create by 2012-5-25
	 * @author liuw
	 * @param int $post_id，post ID
	 * @param int $place_id，地点id
	 * @param int $page，当前页码
	 * @param int $size，分页条显示数量
	 */
	private function img_paginate($post, $size=7){
		$page_arr = array();
		$post_id = $post['id'];
		$place_id = $post['placeId'];
		$paginate = '';
		//查询地点的照片post总数
		$count = $this->db->where(array('placeId'=>$place_id, 'type'=>3, 'status <= '=>1))->count_all_results('Post');
		if($count){			
			$page_arr = array();
			//计算左右各需要查询的数量
			$side = ($size - 1) / 2;
			$left = $right = $side;
			//左边
			$list = array();
			$sql = 'SELECT id, photoName FROM Post WHERE id < ? AND type=? AND status<=1 AND placeId=? ORDER BY id DESC LIMIT ?';
			$par = array($post_id, $this->config->item('post_photo'), $place_id, $left);
			$query = $this->db->query($sql, $par)->result_array();
			foreach($query as $row){
				$page = array(
					'id'=>$row['id'],
					'thumb'=>!empty($row['photoName'])?image_url($row['photoName'], 'user', 'thmdp'):'',
				);
				$list[] = $page;
			}
			//前一页
			$left = count($list)+1;
			$right = $size - $left;
			$page_arr['prev'] = $list[0]['id'];
			
			$page_arr['list'] = array_reverse($list);
			//当前页
			$page_arr['list'][] = array(
				'id'=>$post_id,
				'thumb'=>!empty($post['photoName'])?image_url($post['photoName'], 'user', 'thmdp'):'',
				'class'=>'active'
			);
			//右边
			$list = array();
			$par = array($post_id, $this->config->item('post_photo'), $place_id, $right);				
			$sql = 'SELECT id, photoName FROM Post WHERE id>? AND type=? AND status<=1 AND placeId=? ORDER BY id ASC LIMIT ?';
			$query = $this->db->query($sql, $par)->result_array();
			foreach($query as $row){
				$page = array(
					'id'=>$row['id'],
					'thumb'=>!empty($row['photoName'])?image_url($row['photoName'], 'user', 'thmdp'):'',
				);
				$list[] = $page;
			}
			//后一页
			$page_arr['next'] = $list[0]['id'];
			foreach($list as $k=>$v){
				$page_arr['list'][] = $v;
			}
		}
		$this->assign('page_arr', $page_arr);
	}
	
}   
   
 // File end