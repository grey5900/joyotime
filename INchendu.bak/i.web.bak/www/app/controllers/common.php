<?php
/**
 * 通用的交互功能
 * Create by 2012-5-16
 * @author liuw
 * @copyright Copyright(c) 2012-2014 joyotime
 */
  
 // Define and include
if ( ! defined('BASEPATH')) exit('No direct script access allowed');   
   
 // Code
class Common extends MY_Controller{
	
	/**
	 * 检查是否已登陆 
	 * Create by 2012-6-14
	 * @author liuw
	 */
	public function check_online(){
		$has_online = isset($this->auth)&&!empty($this->auth)?true:false;
		$this->echo_json((array('result'=>$has_online?1:0)));
	}
	
	/**
	 * 显示交互窗口 
	 * Create by 2012-5-16
	 * @author liuw
	 * @param unknown_type $form
	 */
	public function show_form($form=''){
		$this->assign('form', empty($form)?'signin':$form);
		$this->display('index');
	}
	
	/**
	 * 回复
	 * Create by 2012-5-16
	 * @author liuw
	 */
	public function reply(){
	    $this->is_login();
		if($this->is_post()){
			//表单数据
			$item_id = $this->post('reply_id');//回复哪个post or reply
			$content = $this->post('message');//回复的内容
			$reply_uid = $this->post('reply_uid');//回复谁
			$type = $this->post('reply_to');//回复的是什么，tip=评论，image＝照片,reply=回复
			//前头没得回复：谁谁谁了
			//if($type === 'reply'){
			//	$cs = explode('：', $content);
			//	$content = $cs[1];
			//}
			$uid = $this->auth['id'];//回复所有者id
			//非法的回复
			$suc_types = $this->config->item('assert_type');
			if(empty($suc_types[$type]))
				$this->echo_json((array('code'=>0,'msg'=>$this->lang->line('reply_faild_type'))));
			//获得post id
			$post_id = null;
			if($suc_types[$type] == 4){//回复回复,$item_id指向PostReply
				$query = $this->db->where('id', $item_id)->get('PostReply')->first_row('array');
				$post_id = $query['postId'];
			}else{//其他，包括回复评论、照片
				$post_id = $item_id;
			}
			//获得post uid
			$query = $this->db->where('id', $post_id)->get('Post')->first_row('array');
			$post_uid = $query['uid'];
			//写PostReply表
			$data = array(
				'uid' => $uid,
				'content' => $content,
		//		'replyUid' => $reply_uid,
			);
			if($suc_types[$type] == 4){
				$data['replyId'] = $item_id;
				$data['replyUid'] = $reply_uid;
			}
			$data['postId'] = $post_id;
			//关键词检查
			if(check_taboo($content, 'post')){
				//包含敏感词
				$data['status'] = 2;
			}else{
				$data['status'] = 0;
			}
			$this->db->insert('PostReply', $data);
			$new_id = $this->db->insert_id();
			if($new_id <= 0){
				$result = array('code'=>0, 'msg'=>$this->lang->line('do_error'));
			}else{
				//＋1
				$this->db->query('UPDATE User SET replyCount=replyCount+1 WHERE id=?', array($uid));
				$this->db->query('UPDATE Post SET replyCount=replyCount+1 WHERE id=?', array($post_id));
				$this->db->query('UPDATE UserFeed SET replyCount=replyCount+1 WHERE itemType=? AND itemId=?', array($query['type'], $post_id));
				//写PostReplyMessage
				$api_url = $this->config->item('api_serv') . $this->config->item('api_folder');
				if($reply_uid != $uid){//不是回复自己
					$this->db->insert('PostReplyMessage', array('uid'=>$reply_uid, 'replyId'=>$new_id));
					$prm_id = $this->db->insert_id();
					if($prm_id)
                   	 send_api_interface($api_url . 'push/push_reply_message', 'POST', array('reply_msg_id' => $prm_id));
				}
				if($post_uid != $uid && $post_uid != $reply_uid){//不是楼主回复
					$this->db->insert('PostReplyMessage', array('uid'=>$post_uid, 'replyId'=>$new_id));
					$prm_id = $this->db->insert_id();
					if($prm_id)
                   	 send_api_interface($api_url . 'push/push_reply_message', 'POST', array('reply_msg_id' => $prm_id));
				}
				if($data['status'] <= 0){
					//回复谁
					$query = $this->db->where('id', $reply_uid)->get('User')->first_row('array');
					$reply_user = !empty($query['nickname']) ? $query['nickname'] : $query['username'];
					$message = format_msg($this->lang->line('reply_success'), array('reply_user'=>$reply_user));
					//查出回复内容
					$rep_data = $this->db->query('SELECT r.*,IF(u.nickname IS NOT NULL AND u.nickname != \'\',u.nickname,u.username) AS uname, u.avatar FROM PostReply r INNER JOIN User u ON u.id=r.uid WHERE r.id=?', array($new_id))->first_row('array');
					//$rep_data['createDate'] = gmdate('Y-m-d H:i', strtotime($rep_data['createDate']));
					$rep_data['createDate'] = $rep_data['createDate'];
					$rep_data['avatar'] = image_url($rep_data['avatar'], 'head', 'hmdp');
				}else{
					$message = $this->lang->line('reply_has_taboo');
					//给用户发敏感词的系统通知
					$place = $this->db->select('Place.placename')->join('Place', 'Place.id=Post.placeId')->where('Post.id', $post_id)->get('Post')->first_row('array');
					$data = array(
						'type'=>23,//回复包含敏感词而被屏蔽
						'recieverId'=>$uid,
						'content'=>format_msg($this->lang->line('sm_has_taboo'), array('place'=>$place['placename'])),
					);
					$this->db->insert('SystemMessage', $data);
					$msg_id = $this->db->insert_id();
					if($msg_id){
						$params = array(
							'api'=>$this->lang->line('api_push_sys'),
							'attr'=>array('sys_msg_id'=>$msg_id),
							'has_return'=>false
						);
						$this->call_api($params);
					}
				}
				$result = array('code'=>1, 'msg'=>$message, 'id'=>$item_id, 'pid'=>$post_id, 'data'=>$rep_data);
                
                // 发送推送
                // send_system_message(3, $suc_types[$type], $item_id);
			}
			$this->echo_json(($result));
		}else{
			$this->echo_json((array('code'=>1)));
		}
	}
	/**
	 * 分享
	 * Create by 2012-5-16
	 * @author liuw
	 */
	public function share(){
		if($this->is_post()){
			$item_type = $this->post('itype');//分享资源类型
			$item_id = $this->post('id');//分享资源id
			$share_type = $this->post('type');//分享类型
			$content = $this->post('content');//分享时说的话
			$uid = $this->auth['id'];
			//转换type
			$types = $this->config->item('assert_type');
			$item_type = $types[$type];
			$params = array(
				'api'=>$this->lang->line('api_share'),
				'uid'=>$uid,
				'has_return'=>true
			);
			$attrs = array(
				'item_id'=>$item_id,
				'item_type'=>$item_type,
				'content'=>$content,
				'type'=>$share_type,
			);
			$params['attr'] = $attrs;
			//调用api分享
			$result = json_decode($this->call_api($params), true);
			if($result['code'] != 0)
				$this->echo_json(array('code'=>0, 'msg'=>$this->lang->line('share_faild')));
			else 
				$this->echo_json(array('code'=>1, 'msg'=>$this->lang->line('share_success')));
		}else{
			$this->echo_json((array('code'=>1)));
		}
	}
	
	/**
	 * 赞
	 * Create by 2012-5-17
	 * @author liuw
	 */
	public function praise(){
	    $this->is_login();
		if($this->is_post()){
			//表单数据
			$item_id = $this->post('id');
			$type = $this->post('type');
			$uid = $this->auth['id'];
			//转换type
			$types = $this->config->item('assert_type');
			$item_type = $types[$type];
			//检查是否赞过了
			$rs = $this->db->where(array('uid'=>$uid, 'itemType'=>$item_type, 'itemId'=>$item_id))->count_all_results('UserPraise');
			if($rs > 0){
				switch($item_type){
					case 3:$str = '张照片';break;
					case 2:$str = '个点评';break;
					default:$str = '个签到';break;
				}
				$message = format_msg($this->lang->line('praise_has_done'), array('post'=>$str));
				$result = array('code'=>2, 'msg'=>$message);
			}else{
				//保存数据
				$data = array('uid'=>$uid, 'itemType'=>$item_type, 'itemId'=>$item_id);
				$this->db->insert('UserPraise', $data);
				$id = $this->db->insert_id();
				if($id <= 0)
					$result = array('code'=>2, 'msg'=>$this->lang->line('do_error'));
				else{
					//＋1
					$this->db->query('UPDATE Post SET praiseCount=praiseCount+1 WHERE id=?', array($item_id));
					$this->db->query('UPDATE UserFeed SET praiseCount=praiseCount+1 WHERE itemId=? AND itemType=?', array($item_id, $item_type));
					$reptxt = '';
					switch($item_type){
						case $this->config->item('post_checkin'):$reptxt='个签到';break;
						case $this->config->item('post_tip'):$reptxt='个点评';break;
						case $this->config->item('post_photo'):$reptxt='张照片';break;
					}
					$msg = preg_replace("/@{post}/",$reptxt, $this->lang->line('praise_success'));
					$result = array('code'=>1, 'msg'=>$msg);
                    // 发送推送
                    send_system_message(2, $item_type, $item_id);
				}
			}
			$this->echo_json(($result));
		}
	}
	
	/**
	 * 取消收藏
	 * Create by 2012-7-13
	 * @author liuw
	 */
	public function un_favorite(){
		//检查登录状态
		$this->is_login();
		if($this->is_post()){
			//表单数据
			$item_id = $this->post('id');
			$type = $this->post('type');
			$uid = $this->auth['id'];
			//转换type
			$types = $this->config->item('assert_type');
			$item_type = $types[$type];
			switch($item_type){
				case 1:$str = '个地点';break;
				case 3:$str = '张照片';break;
				case 2:$str = '个点评';break;
				default:$str = '个签到';break;
			}
			//检查是否收藏过了
			$rs = $this->db->select('id')->where(array('uid'=>$uid, 'itemType'=>$item_type, 'itemId'=>$item_id))->get('UserFavorite')->first_row('array');
			//没收藏
			if(!isset($rs) || empty($rs)){
				$result = array('code'=>0, 'msg'=>format_msg($this->lang->line('ufavorite_has_not_fav'), array('post'=>$str)));
			}else{
				//清理UserFavorite
				$id = $rs['id'];
				$this->db->where('id', $id)->delete('UserFavorite');
				$result = array('code'=>1, 'msg'=>format_msg($this->lang->line('ufavorite_success'), array('post'=>$str)));
			}
			$this->echo_json(($result));
		}
	}
	
	/**
	 * 收藏
	 * Create by 2012-5-16
	 * @author liuw
	 */
	public function favorite(){
	    $this->is_login();
		if($this->is_post()){
			//表单数据
			$item_id = $this->post('id');
			$type = $this->post('type');
			$uid = $this->auth['id'];
			//转换type
			$types = $this->config->item('assert_type');
			$item_type = $types[$type];
			//检查是否收藏过了
			$rs = $this->db->where(array('uid'=>$uid, 'itemType'=>$item_type, 'itemId'=>$item_id))->count_all_results('UserFavorite');
			if($rs > 0){
				switch($item_type){
					case 1:$str = '个地点';break;
					case 3:$str = '张照片';break;
					case 2:$str = '个点评';break;
					default:$str = '个签到';break;
				}
				$message = format_msg($this->lang->line('favorite_has_faved'), array('post'=>$str));
				$result = array('code'=>2, 'msg'=>$message);
			}else{
				//保存数据
				$data = array('uid'=>$uid, 'itemType'=>$item_type, 'itemId'=>$item_id);
				$this->db->insert('UserFavorite', $data);
				$id = $this->db->insert_id();
				if($id <= 0) {
					$result = array('code'=>2, 'msg'=>$this->lang->line('do_error'));
				}else{
					switch($item_type){
						case 1:$str = '个地点';break;
						case 3:$str = '张照片';break;
						case 2:$str = '个点评';break;
						default:$str = '个签到';break;
					}
					$message = format_msg($this->lang->line('favorite_success'), array('post'=>$str));
					$result = array('code'=>1, 'msg'=>$message);
                    // 发送推送(不发送)
                    // send_system_message(1, $item_type, $item_id);
				}
			}
			$this->echo_json(($result));
		}
	}
	
}   
   
 // File end