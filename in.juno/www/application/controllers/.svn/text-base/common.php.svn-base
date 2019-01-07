<?php
/**
 * 通用的功能
 * Create by 2012-12-13
 * @author liuweijava
 * @copyright Copyright(c) 2012-
 */

// Define and include
if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );

	// Code
class Common extends AuthController
{
	
	var $conf;
	var $pconf;
	
	public function __construct()
	{
		parent::__construct ();
		//预设参数
		$this->config->load ( 'config_common' );
		$this->conf = $this->config->item ( 'assert_type' );
		$this->pconf = $this->config->item ( 'post_type' );
		$this->tnconf = $this->config->item ( 'itemtype_name' );
		//数据模型
		//$this->load->model('userpraise_model', 'm_upraise');
		$this->load->model ( 'userfavorite_model', 'm_ufavorite' );
		$this->load->model ( 'userfaverplacecollection_model', 'm_ufavoritepc' );
		$this->load->model ( 'userfeed_model', 'm_ufeed' );
		$this->load->model ( 'post_model', 'm_post' );
		$this->load->model ( 'postreply_model', 'm_preply' );
		$this->load->model ( 'postreplymessage_model', 'm_prm' );
		$this->load->model ( 'user_model', 'm_user' );
		$this->load->model ( 'place_model', 'm_place' );
		$this->load->model ( 'usermnemonic_model', 'm_mnemonic' );
		$this->load->model ( 'share_model', 'm_share' );
		$this->load->model ( 'webeventapply_model', 'm_webeventapply' );
		
		$this->load->model ( 'postappraise_model', 'm_postappraise' );
		
		//辅助函数
		$this->load->helper ( 'api' );
		$this->load->helper ( 'cache' );
	}
	
	/**
	 * 分享
	 * Create by 2012-12-13
	 * @author liuweijava
	 * @param int $item_id
	 * @param int $item_type
	 * @param int $share_type 分类到哪里，0=到in成都；1=到新浪微博；2=到腾讯微博
	 * @param string $content 分享时说的话
	 */
	public function share()
	{
		$uid = $this->auth ['uid'];
		if ($this->is_post ())
		{
			$item_id = $this->post ( 'id' );
			$item_type = $this->post ( 'type' );
			$share_type = $this->post ( 's' );
			$content = $this->post ( 'content' );
			
			$api_url = "/user/share";
			
			$shared = $this->m_share->is_shared ( $uid, $item_id, $item_type, $share_type );
			
			if ($shared)
			{
				$this->echo_json ( array ('code' => 2, 'msg' => '您已经分享过这个' . $this->tnconf [$item_type] . '啦！' ) );
			} else
			{
				$attrs = array ('type' => $share_type, 'content' => $content, 'item_type' => $item_type, 'item_id' => $item_id );
				
				$result = request_api ( $api_url, "POST", $attrs, array ('uid' => $this->auth ['uid'] ) );
				$json = decode_json ( $result );
				
				if ($json ['result_code'] == 0)
				{
					get_data ( 'user', $this->auth ['uid'], true );
					
					$this->echo_json ( array ('code' => 0 ) );
				} else
				{
					$this->echo_json ( array ('code' => $json ['result_code'], 'msg' => '分享失败了' ) );
				}
			}
			/*//POST参数
			$item_id = $this->post('id');
			$type_id = $this->post('type');
			$share_type = $this->post('s');
			$content = $this->post('content');
			empty($content) && $content = '';
			//转换内容类型
			$item_type = $this->conf[$type_id];
			
			//只能同一类型分享一次
			$shared = $this->m_share->is_shared($uid,$item_id,$item_type,$share_type);
			if($shared){
				$this->echo_json(array('code'=>2, 'msg'=>'您已经分享过这个'.$this->tnconf[$item_type].'啦！'));
			}
			//API参数
			$attr = array(
				'uid'=>$uid,
				'item_id'=>$item_id,
				'item_type'=>$item_type,
				'type'=>$share_type,
				'content'=>$content
			);
			$result = decode_json(call_api('share', $attr));
			if(empty($result) || $result['result_code']){
				empty($result) && $result = array('result_code'=>1, 'result_msg'=>'通信失败');
				$this->echo_json(array('code'=>$result['result_code'], 'msg'=>$result['result_msg']));
			}else{
				//更新缓存
				get_data('user', $this->auth['uid'], true);
				$this->echo_json(array('code'=>0));
			}*/
		}
	}
	
	/**
	 * 赞
	 * Create by 2012-12-13
	 * @author liuweijava
	 * @param int $item_id 被赞的内容ID
	 * @param int $item_type 实例TYPE
	 * @param int $sort  赞 1 踩 -1
	 */
	public function praise()
	{
		if ($this->is_post ())
		{
			//调用接口来赞！
			$item_id = $this->post ( 'id' );
			$item_type = $this->post ( 'type' );
			$sort = $this->post ( 'sort' ) ? $this->post ( 'sort' ) : 1;
			
			$attrs = array ('id' => $item_id, 'type' => $sort );
			
			switch ($item_type)
			{
				case 19 : //post	
					$api_url = "/post/appraise";
					$msg = "点评";
					break;
				case 20 : //地点册	
					$msg = "地点册";
					$api_url = "/place_collection/appraise";
					break;
			}
			//var_dump()
			//$json = decode_json($result);
			if ($this->m_postappraise->check_praise ( $this->auth ['uid'], $item_type , $item_id, $sort ))
			{
				$this->echo_json ( array ('code' => 1, 'msg' => lang_message ( 'common_has_praised' . $sort, array ($msg ) ) ) );
			} else
			{
				$result = request_api ( $api_url, "POST", $attrs, array ('uid' => $this->auth ['uid'] ) );
				
				$json = decode_json ( $result );
				$p_type = $sort == 1 ? '赞' : '踩';
				if ($json ['result_code'] == 0)
				{
					
					get_data ( 'user', $this->auth ['uid'], true );
					$item_type==19 && get_data ( 'post_praised', sprintf ( '%s-%s-%s', $this->auth ['uid'], $item_id, 1 ), true );
					$this->echo_json ( array ('code' => $json ['result_code'], 'msg' => '成功'.$p_type.'了这个' . $msg ) );
				} else
				{
					$this->echo_json ( array ('code' => $json ['result_code'], 'msg' => $p_type.'失败了' ) );
				}
			}
		
		//var_dump($json);exit;
		

		//$result = request_api("place_collection/list","GET",$attrs);
		//var_dump($result);exit;
		/*
			//POST参数
			$item_id = $this->post('id');
			$type = $this->post('type'); // 1 或者 -1
			//获得TYPE ID
			$item_type = 19;//$this->conf[$type];
			switch($type){
					case 'tip':$msg = '点评';break;
					case 'image':$msg = '图片';break;
					case 'reply':$msg = '回复';break;
					case 'place':$msg = '地点';break;
			}
			$msg = "点评";
			//检查是否赞过了
			if(!$this->m_upraise->check_praise($this->auth['uid'], $item_id, $item_type)){
				
				$this->echo_json(array('code'=>1, 'msg'=>lang_message('common_has_praised', array($msg))));
			}else{
				//已经不是使用userpraise 用postappraise
				$code = $this->m_upraise->praise_item($this->auth['uid'], $item_id, $item_type);
				switch($code){
					case 1://插入赞的LOG失败
					case 2://更新POST的被赞次数失败
					case 3://更新FEED的被赞次数失败
					case 4://发送系统消息失败
						$this->echo_json(array('code'=>$code, 'msg'=>'操作失败了'));break;
					default://成功
						//更新缓存
						get_data('user', $this->auth['uid'], true);
						get_data('post_praised', sprintf('%s-%s-%s', $this->auth['uid'], $item_id, $item_type), true);
						$this->echo_json(array('code'=>$code, 'msg'=>'已经成功赞了这个'.$msg));break;
				}
			}*/
		}
	}
	
	/**
	 * 收藏
	 * Create by 2012-12-13
	 * @author liuweijava
	 * @param int $item_id
	 * @param int $item_type
	 */
	public function favorite()
	{
		if ($this->is_post ())
		{
			//POST参数 
			$item_id = $this->post ( 'id' );
			$type_id = $this->post ( 'type' );
			//获得item_type
			$item_type = $this->conf [$type_id];
			//检查是否可以收藏
			if (! $this->m_ufavorite->check_favorite ( $this->auth ['uid'], $item_id, $item_type ))
			{
				switch ($item_type)
				{
					case 1 :
						$str_rep = '个地点';
						break;
					case 2 :
						$str_rep = '个点评';
						break;
					case 3 :
						$str_rep = '张图片';
						break;
					default :
						$str_rep = '个签到';
						break;
				}
				$this->echo_json ( array ('code' => 1, 'msg' => lang_message ( 'common_has_favorited', array ($str_rep ) ) ) );
			} else
			{
				//保存收藏
				$code = $this->m_ufavorite->favorite ( $this->auth ['uid'], $item_id, $item_type );
				if ($code)
					$this->echo_json ( array ('code' => $code, 'msg' => '收藏失败了' ) );
				else
				{
					//更新缓存
					get_data ( 'user', $this->auth ['uid'], true );
					get_data ( 'post_favorited', sprintf ( '%s-%s-%s', $this->auth ['uid'], $item_id, $item_type ), true );
					$this->echo_json ( array ('code' => $code ) );
				}
			}
		}
	}
	
	/**
	 * 
	 * 需要登录
	 * 只需要一个地点册ID
	 */
	public function favorite_placecoll()
	{
		if($this->is_post()){
			$pcid = intval($this->post('id'));
			if($pcid){
				//调用接口去收藏 /place_collection/add_collection_to_favorite
				
				if(!$this->m_ufavoritepc->check_favorite($this->auth['uid'],$pcid)){
					$this->echo_json ( array ('code' => 1, 'msg' =>  '您已经收藏了这个地点册') );
				}else{
					$api_url = '/place_collection/add_collection_to_favorite';
					$attrs = array('id'=>$pcid);
					$result = request_api ( $api_url, "POST", $attrs, array ('uid' => $this->auth ['uid'] ) );
					
					$json = decode_json ( $result );
					
					if ($json ['result_code'] == 0)
					{
						$this->echo_json ( array ('code' => $json ['result_code'], 'msg' => '收藏成功' ) );
					} else
					{
						$this->echo_json ( array ('code' => $json ['result_code'], 'msg' => '收藏失败了' ) );
					}
				}
			}
			else{
				$this->echo_json ( array ('code' => -1,'msg' => '缺少参数' ) );
			}
		}
			
	}
	/**
	 * 回复
	 * Create by 2012-12-13
	 * @author liuweijava
	 * @param int $reply_id 回复的POST的ID
	 * @param string $content 回复的内容
	 * @param int $to_uid 回复谁
	 * @param int $post_type POST类型 tip=评论，image=图片，reply=回复
	 * @return array code:0=回复成功；1=非法的回复；2=保存回复内容失败；3=回复内容为空
	 */
	public function reply()
	{
		$uid = $this->auth ['uid'];
		if ($this->is_post ())
		{
			//POST参数
			//$reply_id = $this->post('id');
			//$to_uid = $this->post('uid');
			//$content = $this->post('content');
			

			//$post_type = $this->post('type');
			

			$item_type = 19;
			$item_id = $this->post ( 'pid' );
			$content = $this->post ( 'content' );
			$reply_uid = $this->post ( 'uid' );
			$reply_rid = $this->post ( 'id' ); //这个有值的时候就是回复的回复
			

			//不管是回复回复还是回复点评 。ITEMTYPE 和ITEMID 都是 源POST 的id 和itemtype 用reply_uid 和reply_rid来区分是不是回复的回复
			$attrs = array ('item_type' => $item_type, //暂时只能回复点评
'item_id' => $item_id, 'content' => $content );
			
			if ($reply_rid)
			{
				$attrs ['reply_uid'] = $reply_uid;
				$attrs ['reply_rid'] = $reply_rid;
			}
			
			$api_url = "/reply/reply";
			
			$result = request_api ( $api_url, "POST", $attrs, array ('uid' => $this->auth ['uid'] ) );
			$json = decode_json ( $result );
			
			if ($json ['result_code'] == 0)
			{
				get_data ( 'post_replies', $item_id, true );
				$this->echo_json ( array ('code' => $json ['result_code'] ) );
			} else
			{
				$this->echo_json ( array ('code' => $json ['result_code'], 'msg' => "回复失败了,请稍后再试" ) );
			}
			
		/*$uid = $this->post('authid');		
			if(empty($this->auth))
				$this->echo_json(array('code'=>-1, 'msg'=>'没有登录，请先登录'));
			elseif(empty($this->conf[$post_type]))//非法的回复
				$this->echo_json(array('code'=>1, 'msg'=>'这里不能回复'));
			elseif(empty($content))
				$this->echo_json(array('code'=>3, 'msg'=>'回复内容不能为空哦'));
			else{
				$post_id = $reply_id;//默认$reply_id指向Post
				$reply_type = $this->conf[$post_type];
				if($reply_type == 4){//回复回复，$reply_id指向PostReply
					//从PostReply获取postId
					$pr = $this->m_preply->select_by_id($reply_id);
					$post_id = $pr['postId'];
				}
				//POST
				$post = $this->m_post->select_by_id($post_id);
				$post_id = $post['id'];
				//写PostReply
				$data = array('uid'=>$uid, 'content'=>$content, 'postId'=>$post_id);
				//敏感词检查
				$data['status'] = check_taboo($content, 'post') ? 0 : 2;
				if($reply_type == 4){
					$data['replyId'] = $reply_id;
					$data['replyUid'] = $to_uid;
				}
				$this->m_preply->insert($data);
				$new_reply_id = $this->db->insert_id();
				if(!$new_reply_id)
					$this->echo_json(array('code'=>2, 'msg'=>'回复失败了'));
				else{
					//更新我的回复数
					$this->m_user->update_stat_count($uid, 'replyCount');
					//更新POST的回复数
					$this->m_post->update_stat_count($post_id, 'replyCount');
					//更新用户动态的回复数
					$this->m_ufeed->update_stat_count($post_id, $post['type'], 'replyCount');
					//发私信
					if($to_uid != $uid){//不是回复的自己
						$this->m_prm->insert(array('uid'=>$to_uid, 'replyId'=>$new_reply_id));
						$new_id = $this->db->insert_id();
					}
					if($post['uid'] != $uid && $post['uid'] != $to_uid){//不是楼主回复
						$this->m_prm->insert(array('uid'=>$post['uid'], 'replyId'=>$new_reply_id));
						$new_id = $this->db->insert_id();
					}
					//发新回复提醒 
					send_reply_msg(array('reply_msg_id'=>$new_id));
					if($data['status'] <= 0){
						//被回复的人
						$to = $this->m_user->select_by_id($to_uid);
						$replace = !empty($to['nickname']) ? $to['nickname'] : $to['username'];
						if(empty($replace)){
							$host_user = get_data('user',$post['uid']);
							$replace = $host_user['nickname'] ? $host_user['nickname'] : $host_user['username'] ;
						}
						$msg = lang_message('reply_success', array($replace));
						//提示消息
						$rep_d = array(
							'createDate' => gmdate('Y-m-d H:i', time()+8*3600),
							'avatar' => image_url($to['avatar'], 'head', 'hmdp')
						);
						// 更新POST的回复缓存
						get_data('post_replies', $post_id, true);
					}else{
						$msg = '你发布的回复可能包含不适宜的内容，正在等待管理员审核。由此带来的不便敬请谅解。';
						//地点
						$this->load->model('place_model', 'm_place');
						$place = $this->m_place->select_by_id($post['placeId']);
						//系统通知
						$s_msg = array('type'=>23,'recieverId'=>$uid,'content'=>lang_message('sm_has_taboo', array($place['placename'])));
						$this->load->model('systemmessage_model', 'm_sm');
						$this->m_sm->insert($s_msg);
						$msg_id = $this->db->insert_id();
						if($msg_id){//推系统消息
							call_api('msg_push_sys', array('sys_msg_id'=>$msg_id));
						}
					}
					$this->echo_json(array('code'=>0, 'msg'=>$msg, 'id'=>$new_reply_id, 'data'=>$rep_d));
				}
			}*/
		}
	}
	
	/**
	 * 私信
	 * Create by 2012-12-13
	 * @author liuweijava
	 */
	public function im()
	{
	
	}
	
	/**
	 * 地点报错
	 * Create by 2012-12-21
	 * @author liuweijava
	 * @param int $place_id
	 */
	public function report($place_id)
	{
		if ($this->is_post ())
		{
			//参数
			$err_conf = $this->config->item ( 'place_err' );
			$error = array ('placeId' => $place_id, 'uid' => $this->auth ['uid'] );
			//报错类型
			$error ['type'] = $this->post ( 'type' );
			//组装报错的content
			$content = $err_conf [$error ['type']];
			switch ($error ['type'])
			{
				case 0 : //其他错误
					$content .= ':' . $this->post ( 'content' );
					break;
				case 3 : //地点信息错误
					$place_name = $this->post ( 'place_name' );
					! empty ( $place_name ) && $error ['placename'] = $placename;
					$addr = $this->post ( 'place_address' );
					! empty ( $addr ) && $error ['address'] = $addr;
					break;
				case 4 : //地点位置错误
					$coods = explode ( ',', $this->post ( 'coordinate' ) );
					$error ['latitude'] = floatval ( trim ( $coods [0] ) );
					$error ['longitude'] = floatval ( trim ( $coods [1] ) );
					break;
				default : //其他类型的不处理
					break;
			}
			$error ['content'] = $content;
			//保存报错信息
			$this->load->model ( 'placeerrorreport_model', 'm_report' );
			$flag = $this->m_report->add_report ( $error );
			$code = $flag ? 0 : 1;
			$msg = $flag ? '管理员已收到您的报错信息，感谢您对IN成都的支持！' : '真抱歉！因为网络的原因，管理员没能收到您的报错信息。请稍后再尝试一次吧';
			$this->echo_json ( compact ( 'code', 'msg' ) );
		} else
		{
			die ( '非法请求' );
		}
	}
	
	/**
	 * 发布POST
	 * Create by 2012-12-20
	 * @author liuweijava
	 * @param int $place_id
	 */
	public function send($place_id)
	{
		$uid = $this->auth ['uid'];
		if ($this->is_post ())
		{
			$data = array ();
			$place_id = $this->post ( 'place_id' );
			if ($place_id)
			{
				$data ['place_id'] = $place_id;
			}
			
			$user = get_data ( 'user', $uid );
			//同步标识
			$do_sync = $this->post ( 'synchronous' );
			//封装
			$data ['content'] = trim ( $this->post ( 'content' ) );
			$data ['content'] = str_replace ( '＃', '#', $data ['content'] );
			
			//检查是否有图片
			$photo = $this->post ( 'photo' );
			//内容长度检查
			if (empty ( $photo ))
			{
				// 如果没有发布照片，那么去验证字符长度
				$len = cstrlen ( $data ['content'] );
				if ($len < 10 || $len > 500)
				{
					$this->echo_json ( array ('code' => 1, 'msg' => '内容不能少于10个字或超过500个字哦' ) );
				}
			}
			
			$data ['uid'] = $uid;
			
			$is_sync_sina = $is_sync_tencent = 0;
			if (! empty ( $do_sync ))
			{
				$is_sync_sina = $user ['is_sync_sina'];
				$is_sync_tencent = $user ['is_sync_tencent'];
			}
			
			$data ['is_sync_to_sina'] = $is_sync_sina;
			$data ['is_sync_to_tencent'] = $is_sync_tencent;
			//POST类型，默认为点评
			$post_type = $this->pconf ['tip'];
			//POST状态
			$data ['status'] = check_taboo ( $data ['content'], 'post' ) ? 0 : 2;
			
			//接口名称
			$api_name = '/post/save_tip';
			if (empty ( $place_id ))
			{
				$api_name = '/post/save_yy';
			}
			if ($photo)
			{ //发图片
				$post_type = $this->pconf ['photo'];
				//保存图片到本地
				$this->config->load ( 'config_image' );
				$img_c = $this->config->item ( 'image_cfg' );
				$locale = $img_c ['upload_path'] . str_replace ( './', '', basename ( $photo ) );
				
				//封装数据
				$data ['@uploaded_file'] = '@' . $locale;
			
			} else
			{
				if ($api_name == '/post/save_tip')
				{
					$level = $this->post ( 'score' );
					$pcc = $this->post ( 'pcc' );
					! empty ( $level ) && $data ['level'] = floatval ( $level ) * 2;
					! empty ( $pcc ) && $data ['pcc'] = intval ( $pcc );
				}
			
			}
			
			//调用接口发布POST
			$result = decode_json ( request_api( $api_name,'POST',$data,array('uid'=>$uid)) );
			$code = ! is_int ( $result ['result_code'] ) ? 1 : $result ['result_code'];
			
			if ($code == - 1 || in_array ( $code, $this->config->item ( 'bind_err' ) ))
			{
				$msg = $result ['result_msg'];
				$stage_code = 0;
				//重新绑定
				// 这个写法是为何呀？？？
				// 				switch($code){
				// 					case 4507:$stage_code = 1;break;//重新绑定腾讯微博
				// 					default:break;//重新绑定新浪微博
				// 				}
				if ($code == 4507)
				{
					$stage_code = 1;
				}
				$aconf = $this->config->item ( 'api' );
				$bind_url = $aconf ['url'] . '/' . $aconf ['path'] ['oauth_bind'] . '?' . http_build_query ( array ('uid' => $uid, 'stage_code' => $stage_code, 'is_redirect' => 1 ) );
				//$this->echo_json ( array ('code' => $code, 'msg' => $msg, 'bind_url' => $bind_url ) );
				$this->echo_json ( compact ( 'code', 'msg' ,'bind_url') );
			} elseif ($code > 0)
			{
				$s_type = $post_type == $this->pconf ['photo'] ? '图片' : '点评';
				$msg = lang_message ( 'send_post_fail', array ($s_type ) );
				$this->echo_json ( compact ( 'code', 'msg' ) );
			} elseif (! empty ( $data ['content'] ) && $data ['status'] == 2)
			{ //敏感词检查
				//获取地点名称
				$this->load->model ( 'place_model', 'm_place' );
				$p = $this->m_place->select ( array ('id' => $place_id ) );
				$s_type = $post_type == $this->pconf ['photo'] ? '图片' : '点评';
				$msg = lang_message ( 'send_post_has_taboo', array ($p ['placename'], $s_type ) );
				
				//更新活动参与表
				$this->_update_event_apply ();
				
				$this->echo_json ( compact ( 'code', 'msg' ) );
			} else
			{
				
				//发布成功
				$locale && @unlink ( $locale );
				//$s_type = $post_type == $this->pconf ['photo'] ? '图片' : '点评';
				$s_type = $place_id ? '点评' : 'YY' ;
				$msg = lang_message ( 'send_post_success', array ($s_type ) );
				//更新缓存
				get_data ( 'user', $uid, true );
				
				//更新活动参与表
				$this->_update_event_apply ();
				
				$this->echo_json ( compact ( 'code', 'msg' ) );
			}
		} else
		{
			die ( '非法请求' );
		}
	}
	
	/**
	 * 
	 * 更新活动参与表
	 * 
	 * 
	 */
	private function _update_event_apply()
	{
		$uid = $this->auth ['uid'];
		$event_id = $this->post ( 'event_id' );
		if (is_numeric ( $event_id ) and is_numeric ( $uid ))
		{
			$data = array ('uid' => $uid, 'eventId' => $event_id );
			$is_apply = $this->m_webeventapply->select ( $data );
			if (empty ( $is_apply ))
			{
				$this->m_webeventapply->insert ( $data );
			}
		}
	}
	
	/**
	 * 备注好友
	 * Create by 2012-12-25
	 * @author liuweijava
	 */
	public function mnemonic()
	{
		$uid = $this->auth ['uid'];
		if ($this->is_post ())
		{
			$m_uid = $this->post ( 'muid' );
			$mnemonic = $this->post ( 'mnemonic' );
			//设置备注
			$code = $this->m_mnemonic->set_mnemonic ( $uid, $m_uid, $mnemonic );
			if ($code)
			{
				$msg = '备注好友失败了';
			} else
			{
				$msg = '已更新好友备注';
				get_data ( 'mnemonic', sprintf ( '%s-%s', $uid, $m_uid ), true );
			}
			
			$this->echo_json ( compact ( 'code', 'msg' ) );
		}
	}
	
	/**
	 * 敏感词检查
	 * Create by 2012-12-25
	 * @author liuweijava
	 */
	public function check_taboo()
	{
		if ($this->is_post ())
		{
			$content = $this->post ( 'content' );
			$type = $this->post ( 'type' );
			if (check_taboo ( $content, $type ))
				$this->echo_json ( array ('code' => 0, 'msg' => 'Success' ) );
			else
				$this->echo_json ( array ('code' => 1, 'msg' => 'Has taboo' ) );
		}
	}
	
	/**
	 * 得到图片的url地址
	 */
	function get_image_url()
	{
		$image_name = $this->get ( 'file_name' );
		
		if (strpos ( $image_name, 'http://' ) === 0)
		{
			die ( json_encode ( array ('type' => 'url', 'image_name' => $image_name, 'image' => $image_name, 'source_image' => $image_name ) ) );
		}
		
		$file_type = $this->get ( 'file_type' );
		$resolution = $this->get ( 'resolution' );
		
		$image = image_url ( $image_name, $file_type, $resolution );
		$source_image = $image;
		
		echo json_encode ( compact ( 'image_name', 'image', 'source_image' ) );
	}

}  
   
 // File end