<?php
/**
 * POST管理，包括点评和图片
 * Create by 2012-3-28
 * @author liuw
 * @copyright Copyright(c) 2012-2014 joyotime
 */
  
 // Define and include
if ( ! defined('BASEPATH')) exit('No direct script access allowed');   
   
 // Code
class Post extends MY_Controller{
	
	function __construct(){
		parent::__construct();
		
		
		$role_keys = $this->auth['role'];
		$roles = get_data("role");
		$newsRights = array();
		foreach($role_keys as $k=>$row){
			$newsRights = array_merge($newsRights,$roles[$row]['newsRights']);
		}
		$this->newsRight = array_unique(array_filter($newsRights));
		empty($this->newsRight) && ($this->newsRight = array(0));
		unset($newRights);
	}
	/**
	 * 获取频道TAG列表
	 * Create by 2012-12-10
	 * @author liuweijava
	 */
	private function _get_cat_tags(){
		$this->db->select('WebNewsCategoryOwnTag.channelId, Tag.content, WebNewsCategory.catName');
		$this->db->join('Tag', 'Tag.id = WebNewsCategoryOwnTag.tagId', 'inner');
		$this->db->join('WebNewsCategory', 'WebNewsCategory.id = WebNewsCategoryOwnTag.channelId', 'inner');
		$list = array();
		if($this->auth['role'][0]==1){
		$query = $this->db->where(array('WebNewsCategoryOwnTag.tagType'=>0))->order_by('WebNewsCategory.orderValue', 'desc')->order_by('Tag.id', 'desc')->get('WebNewsCategoryOwnTag')->result_array();
		}else{
		$query = $this->db->where('WebNewsCategoryOwnTag.tagType=0 and WebNewsCategory.id in ('.implode(",",$this->newsRight).')',null,false)->order_by('WebNewsCategory.orderValue', 'desc')->order_by('Tag.id', 'desc')->get('WebNewsCategoryOwnTag')->result_array();
		}
		foreach($query as &$row){
			$list[$row['channelId']]['channel'] = $row['catName'];
			$list[$row['channelId']]['tags'][] = $row;
			unset($row);
		}
		unset($query);
		return $list;
	}
	
	/**
	 * 显示数据列表，根据get参数op确定是点评还是图片
	 * Create by 2012-3-28
	 * @author liuw
	 */
	public function index(){
		$op = $this->get('op');
		$op = isset($op) && !empty($op) ? $op : 'comment';//默认显示点评列表 
		if(!in_array($op, array('comment','pic')))
			$this->error($this->lang->line('post_type_error'));
		$type = $this->config->item('post_'.$op);
		$keyword = $this->post('keyword');
		$keyword = isset($keyword) && !empty($keyword) ? $keyword : '';
		$status = $this->post('status');
		$status = !empty($status) && $status !== '-1' ? intval($status) : -1;
		$orderField = $this->post('orderField');
		$orderField = isset($orderField) && !empty($orderField) ? $orderField : 'createDate';
		$orderDirection = $this->post('orderDirection');
		$orderDirection = isset($orderDirection) && !empty($orderDirection) ? $orderDirection : 'desc';
		if(!empty($keyword))
			$this->assign('keyword',$keyword);
		$this->assign('status',$status);
		$this->assign('orderField', $orderField);
		$this->assign('orderDirection', $orderDirection);
		//高级搜索
		$begin = $this->post('begin');
		$begin = isset($begin) && !empty($begin) ? $begin : FALSE;
		$end = $this->post('end');
		$end = isset($end) && !empty($end) ? $end : FALSE;
		$placeId = $this->post('poi_id');
		$placeId = isset($placeId) && !empty($placeId) ? intval($placeId) : FALSE;
		$uid = $this->post('user_id');
		$uid = isset($uid) && !empty($uid)?intval($uid):FALSE;
		$this->assign(compact('begin','end','placeId','uid'));
		//pic搜索专有
		if($op === 'pic'){
			$isdesc = $this->post('isdesc');
			$isdesc = isset($isdesc)&&!empty($isdesc)?$isdesc:FALSE;
			$iswall = $this->post('iswall');
			$iswall = isset($iswall)&&!empty($iswall)?$iswall:'';
			$this->assign(compact('isdesc','iswall'));
		}
		
		//设置查询总数的查询条件
		$this->db->from('Post');
		$this->db->join('User','User.id=Post.uid','left');
		$this->db->join('Place','Place.id=Post.placeId','left');
		$this->db->where('Post.type',$type,FALSE);
		if(!empty($keyword))
			$this->db->like('Post.content',$keyword);
		if($status>0)
			$this->db->where('Post.status', $status==9?0:$status, FALSE);
		if($placeId)
			$this->db->where('Post.placeId',$placeId, FALSE);
		if($uid)
			$this->db->where('User.id', $uid, FALSE);
		if($begin || $end){
			if($begin && $end){
				$this->db->where("Post.createDate BETWEEN '{$begin}' AND '{$end}'", null , FALSE);
			}elseif($begin && !$end){
				$this->db->where('Post.createDate >= ', $begin);
			}elseif(!$begin && $end){
				$this->db->where('Post.createDate <= ', $end);
			}
		}	
		if($isdesc){
			if($isdesc === '1')
				$this->db->where('Post.content <>','');
			elseif($isdesc === '2')
				$this->db->where('Post.content','');
		}	
	//	if($iswall !== ''){
	//		$iswall = $iswall === '1' ? TRUE : FALSE;
			//判断是否上墙
	//	}	
		$count = $this->db->count_all_results();
		$paginate = $this->paginate($count);
		$size = $paginate['per_page_num'];
		$offset = $paginate['offset'];
		if($count){
			//查询数据
			$list = array();
			$this->db->select('Post.*,User.username,User.avatar,Place.placename');
			$this->db->from('Post');
			$this->db->join('User','User.id=Post.uid','left');
			$this->db->join('Place','Place.id=Post.placeId','left');
			$this->db->where('Post.type',$type,FALSE);
			if(!empty($keyword))
				$this->db->like('Post.content',$keyword);
			if($status>0)
				$this->db->where('Post.status', $status==9?0:$status, FALSE);
			if($placeId)
				$this->db->where('Post.placeId',$placeId, FALSE);
			if($uid)
				$this->db->where('User.id', $uid, FALSE);
			if($begin || $end){
				if($begin && $end){
					$this->db->where("Post.createDate BETWEEN '{$begin}' AND '{$end}'", null , FALSE);
				}elseif($begin && !$end){
					$this->db->where('Post.createDate >= ', $begin);
				}elseif(!$begin && $end){
					$this->db->where('Post.createDate <= ', $end);
				}
			}		
			if($isdesc){
				if($isdesc === '1')
					$this->db->where('Post.content <>','');
				elseif($isdesc === '2')
					$this->db->where('Post.content','');
			}		
			$this->db->order_by($orderField, $orderDirection);
			$this->db->limit($size, $offset);
			$query = $this->db->get();
			foreach($query->result_array() as $row){
				$row = inspaction_taboo($row, 'post');
				switch($row['status']){
					case $this->config->item('ugc_success'):
						$row['content'] = '<span class="f-blue">已审-</span><div style="width:560px;margin:0;padding:4px 6px;">'.$row['content'].'</div>';
						break;
					case $this->config->item('ugc_taboo'):
						$row = inspaction_taboo($row, 'post');
						$row['content'] = '<span class="taboo">敏感-</span><div style="width:560px;margin:0;padding:4px 6px;">'.$row['content'].'</div>';
						break;
					case $this->config->item('ugc_close'):
						$row['content'] = '<span class="f-close">屏蔽-</span><div style="width:560px;margin:0;padding:4px 6px;">'.$row['content'].'</div>';
						break;
					default:
						$row['content'] = '<div style="width:560px;margin:0;padding:4px 6px;">'.$row['content'].'</div>';
						break;
				}
				//查询关联的TAG
				$this->db->select('Tag.id, Tag.content');
				$this->db->join('Tag', 'Tag.id=PostOwnTag.tagId', 'inner');
				$tlist = $this->db->where('PostOwnTag.postId', $row['id'])->get('PostOwnTag')->result_array();
				$tags = array();
				foreach($tlist as $t){
					$tags[] = $t['content'];
				}
				$row['tags'] = implode(' ', $tags);
				unset($tlist, $tags);
				$list[$row['id']] = $row;
			}
		}
		$this->assign('aid',$this->auth['id']);
		$this->assign('list',$list);
		$this->display($op==='pic'?'picture':$op, 'ugc');
	}
	
	/**
	 * 高级搜索
	 * Create by 2012-3-27
	 * @author liuw
	 */
	public function advsearch(){
		$op = $this->get('op');
		$op = isset($op) && !empty($op) ? $op : 'comment';//默认显示点评列表 
		if(!in_array($op, array('comment','pic')))
			$this->error($this->lang->line('post_type_error'), $this->_index_rel, $this->_index_uri);
		$search_url = site_url(array('ugc','post','index','op',$op));
		$this->assign('search_url', $search_url);
		$this->assign('do','advsearch');
		$this->display($op==='pic'?'picture':$op,'ugc');
	}
	
	/**
	 * 点评专用
	 * Create by 2012-4-28
	 * @author liuw
	 */
	public function c_edit(){
		$this->edit();
	}
	/**
	 * 编辑点评，包括审核和屏蔽。通过post参数$do来区分审核和屏蔽操作
	 * Create by 2012-3-22
	 * @author liuw
	 */
	public function edit(){
		$op = $this->get('op');
		$op = isset($op) && !empty($op) ? $op : 'comment';//默认显示点评列表 
		if(!in_array($op, array('comment','pic')))
			$this->error($this->lang->line('post_type_error'));
		$do = $this->get('do');
		if(!isset($do) || empty($do) || !in_array($do, array('examine','status')))
			$this->error($this->lang->line('post_type_error'));
			
		$ids = $this->post('ids');
		$status = $this->config->item('post_status');
		//检查有没有敏感词类型
		$this->db->where_in('id', $ids);
		$query = $this->db->get('Post')->result_array();
		$taboos = $list = array();
		foreach($query as $row){
			$list[] = $row;
			if($row['status'] == $status['taboo'])
				$taboos[] = $row;
		}
		
		$this->db->where_in('id', $ids)->where('status != ',$status['status']);
		$edit = array('status'=>$status[$do]);
		$this->db->update('Post', $edit);
		
		$this->load->helper('ugc');
		$edits = $uedits = array();
		if($do == 'status'){
			//屏蔽回复，需要向用户发一条私信并执行积分操作
			$msg_key = 'ugc_post_kill';
			$tos = $items = $types = $replace = $t_pids = $t_uids = $p_pids = $p_uids = array();
			$update_u_t_sql = 'UPDATE User SET tipCount=if(tipCount-1 <= 0,0,tipCount-1) WHERE id IN ';
			$update_p_t_sql = 'UPDATE Place SET tipCount=if(tipCount-1 <= 0,0,tipCount-1) WHERE id IN ';
			$update_u_p_sql = 'UPDATE User SET photoCount=if(photoCount-1 <= 0,0,photoCount-1) WHERE id IN ';
			$update_p_p_sql = 'UPDATE Place SET photoCount=if(photoCount-1 <= 0,0,photoCount-1) WHERE id IN ';
			foreach($list as $row){
			    if($row['status'] == $status[$do]) {
			        continue;
			    }
				$rp = array();
				$tos[] = $row['uid'];
				$items[] = -1;
				$types[] = $row['type'] == $this->config->item('post_comment') ? $this->config->item('msg_comment') : ($row['type'] == $this->config->item('post_pic')?$this->config->item('msg_pic'):$this->config->item('msg_pm'));
				if($row['type'] == $this->config->item('post_comment')){
					$t_pids[] = $row['placeId'];
					$t_uids[] = $row['uid'];
					$rp['post_type'] = '点评';
				}elseif($row['type'] == $this->config->item('post_pic')){
					$p_pids[] = $row['placeId'];
					$p_uids[] = $row['uid'];
					$rp['post_type'] = '图片';
				}
				//地点名称
				$rs = $this->db->select('Place.placename')->join('Post','Post.placeId=Place.id','left')->where('Post.id', $row['id'])->get('Place')->first_row('array');
				$rp['place'] = isset($rs)&&!empty($rs)&&!empty($rs['placename']) ? $rs['placename'] : '未知地点';
				$replace[] = $rp;
				//删除动态
// 				$sql = 'SELECT id FROM UserFeed WHERE itemType=? AND itemId=? LIMIT 1';
// 				$rs = $this->db->query($sql, array($row['type'], $row['id']))->first_row('array');
				//删除主feed
// 				$this->db->where(array('itemType'=>$row['type'],'itemId'=>$row['id']))->delete('UserFeed');
				//删除引用的feed
// 				$this->db->where('feedId', $rs['id'])->delete('UserFeed');
			}
			send_message($msg_key, $tos, $items, $types, TRUE, $replace);
			//-1
			if(!empty($t_pids))
				$this->db->query($update_p_t_sql . "('".implode("','",$t_pids)."')");
			if(!empty($t_uids))
				$this->db->query($update_u_t_sql . "('".implode("','",$t_uids)."')");
			if(!empty($p_pids))
				$this->db->query($update_p_p_sql . "('".implode("','",$p_pids)."')");
			if(!empty($p_uids))
				$this->db->query($update_u_p_sql . "('".implode("','",$p_uids)."')");
			//积分操作
			$this->success($this->lang->line('do_success'));
		}
		if($do == 'examine'){
			//给敏感词内容发布者发私信
			// if(!empty($taboos)){
				// $msg_key = 'ugc_taboo_examine';
				// $tos = $items = $types = $replace = array();
				// foreach($list as $row){
				    // if($row['status'] == 3) {
				        // // 为屏蔽状态那么不处理
				        // continue;
				    // }
					// $tos[] = $row['uid'];
					// $items[] = $row['id'];
					// $types[] = $row['type'] == $this->config->item('post_comment') ? $this->config->item('msg_comment') : ($row['type'] == $this->config->item('post_pic')?$this->config->item('msg_pic'):$this->config->item('msg_pm'));
					// if($row['type'] == $this->config->item('post_comment')){
						// $edits[] = array(
							// 'id'=>$row['placeId'],
							// 'tipCount'=>'tipCount+1'
						// );
						// $rp['post_type'] = '点评';
                        // // 地点点评+1
                        // $this->db->query("UPDATE Place SET tipCount=tipCount+1 WHERE id='{$row['placeId']}'");
					    // // 用户点评+1
					    // $this->db->query("UPDATE User SET tipCount=tipCount+1 WHERE id='{$row['uid']}'");
                    // }elseif($row['type'] == $this->config->item('post_pic')){
						// $edits[] = array(
							// 'id'=>$row['placeId'],
							// 'photoCount'=>'photoCount+1'
						// );
						// $rp['post_type'] = '图片';
                        // // 地点图片+1
                        // $this->db->query("UPDATE Place SET photoCount=photoCount+1 WHERE id='{$row['placeId']}'");
                        // // 用户图片+1
                        // $this->db->query("UPDATE User SET photoCount=photoCount+1 WHERE id='{$row['uid']}'");
					// }
					// //地点名称
					// $rs = $this->db->select('Place.placename')->join('Post','Post.placeId=Place.id','left')->where('Post.id', $row['id'])->get('Place')->first_row('array');
					// $rp['place'] = isset($rs)&&!empty($rs)&&!empty($rs['placename']) ? $rs['placename'] : '未知地点';
					// $replace[] =$rp;
					// //发一条动态
					// if($row['status'] == 2){//只发敏感状态的POST的FEED
					// //查出post详情
						// $sql = 'SELECT p.*, pl.placename,pl.latitude,pl.longitude, u.avatar, IF(u.nickname IS NOT NULL AND u.nickname != \'\',u.nickname, u.username) AS nickname FROM Post p, Place pl, User u WHERE pl.id=p.placeId AND u.id=p.uid AND p.id=?';
						// $rs = $this->db->query($sql, array($row['id']))->first_row('array');
						// //封装动态数据
						// $feed = array(
							// 'uid' => $rs['uid'],
							// 'replyCount' => $rs['replyCount'],
							// 'praiseCount' => $rs['praiseCount'],
							// 'itemType' => $rs['type'],
							// 'itemId' => $rs['id'],
							// 'nickname' => $rs['nickname'],
							// 'avatar' => $rs['avatar'],
							// 'latitude' => $rs['latitude'],
							// 'longitude' => $rs['longitude'],
							// 'type' => $rs['type'],
							// 'placename' => $rs['placename'],
						// );
						// //detail字段
						// $detail = array(
							// 'item_id' => $rs['id'],
							// 'content' => $rs['content'],
							// 'placename' => $rs['placename'],
							// 'place_id' => $rs['placeId']
						// );
						// if($rs['type'] == $this->config->item('post_comment')){
							// if(!empty($rs['level']) && $rs['level'] > 0)
								// $detail['post_level'] = $rs['level'];
							// if(!empty($rs['pcc']) && $rs['pcc'] > 0)
								// $detail['post_pcc'] = $rs['pcc'];
						// }else if($rs['type'] == $this->config->item('post_pic')){
							// $detail['photo_uri'] = $detail['photo_thumb_uristring'] = $rs['photoName'];
						// }
				// /*		$json_str = '{';
						// $json_arr = array();
						// foreach($detail as $key=>$val){
							// if(in_array($key, array('level','pcc')))
								// $json_arr[] = '"'.$key.'":'.$val;
							// else
								// $json_arr[] = '"'.$key.'":"'.$val.'"';
						// }
						// $json_str .= implode(',',$json_arr);
						// $json_str .= '}'; */
						// $feed['detail'] = json_encode($detail);
						// //保存数据
						// $this->db->insert('UserFeed', $feed);
						// //
						// send_message($msg_key, $tos, $items, $types, TRUE, $replace);
					// }
					// //end
				// }
			// }
			$this->success($this->lang->line('do_success'));
		}
		
	}
	
	/**
	 * 回复点评
	 * Create by 2012-3-22
	 * @author liuw
	 */
	public function reply(){
		$id = intval($this->get('id'));
		if($this->is_post()){
			$content = $this->post('content');
			$s_uid = $this->post('uid');
			if($s_uid === 'random'){
				//随机马甲
				$row = $this->db->query("SELECT u.id FROM User u, MorrisVest v WHERE v.uid=u.id AND v.aid='{$this->auth['id']}' ORDER BY random() LIMIT 1")->first_row();
				$uid = $row->id;
			}else{
				//指定马甲
				$uid = $this->post('user_id');
			}
			//封装回复内容 
			$data = array(
				'uid' => $uid,
				'postId' => $id,
				'content' => $content,
			);
			//保存回复
			$this->db->insert('PostReply', $data);
			$rid = $this->db->insert_id();
			if(!isset($rid) || empty($rid) || !$rid)
				$this->error($this->lang->line('post_reply_error'));
			else{
				//更新post的回复数量
	//			$this->db->query('UPDATE Post SET replyCount=replyCount+1 WHERE id='.$id);
				$this->success($this->lang->line('post_reply_success'), $this->_index_rel, $this->_index_uri, 'closeCurrent');
			}
		}else{
			$op = $this->get('op');
			$op = isset($op) && !empty($op) ? $op : 'comment';//默认显示点评列表 
			if(!in_array($op, array('comment','pic')))
				$this->error($this->lang->line('post_type_error'), $this->_index_rel, $this->_index_uri);
			$this->assign('post_url', site_url(array('ugc','post','reply','id',$id)));
			$this->assign('aid',$this->auth['id']);
			$this->assign('now',gmdate('Y-m-d',time()));
			$this->assign('do','reply');
			$this->display($op==='pic'?'picture':$op,'ugc');
		}
	}
	
	/**
	 * 返回马甲列表
	 * Create by 2012-3-27
	 * @author liuw
	 * @param mixed $aid,机甲帐户id，如果为假，则查询全部马甲
	 * @param mixed $template,模板名称，如果为假，则输出json字符串。此类模板必须放在sys目录下面
	 */
	public function listvest(){
		$aid = $this->get('aid');
		$aid = isset($aid)&&!empty($aid)?intval($aid):$this->auth['id'];
		$template = $this->get('tmp');
		$template = isset($template)&&!empty($template)?$template:FALSE;
		// $this->db->select('MorrisVest.*,User.username,User.nickname,User.realname,User.avatar');
		// $this->db->from('MorrisVest');
		// $this->db->join('User','User.id=MorrisVest.uid','inner');
		// if($aid)
			// $this->db->where('MorrisVest.aid', $aid, FALSE);
		// $this->db->order_by('MorrisVest.dateline', 'desc');
		// $query = $this->db->get();
		// $list = array();
		// foreach($query->result_array() as $row){
			// $list[$row['uid']] = $row;
		// }
		// if($template){
			// $this->assign('list',$list);
			// $this->display($template,'sys');
		// }else{
			// $list[] = array(
				// 'id'=>1,
				// 'realname'=>'test'
			// );
			// exit(json_encode($list));
		// }
		redirect(site_url(array('lookup', 'listvest', 'aid', $aid, 'tmp', $template?$template:'')));
	}
	
	/**
	 * 返回poi列表
	 * Create by 2012-3-27
	 * @author liuw
	 * @param mixed $template，模板名称，如果为假，则输出json字符串。此类模板必须放在sys目录下面
	 */
	public function listpoi(){
		$template = $this->get("tmp");
		$template = isset($template)&&!empty($template)?$template:FALSE;
		
		if($template){
			$this->display($template, 'sys');
		}else{
			$list[] = array(
				'id'=>1,
				'placename'=>'poitest'
			);
			exit(json_encode($list));
		}
	}
	
	/**
	 * 查找带回
	 * Create by 2012-5-2
	 * @author liuw
	 */
	public function list_lookup(){
		$type = $this->get('type');
		$keyword = $this->post('keyword');
		$keyword = isset($keyword)&&!empty($keyword)?$keyword:'';
		// //确定表和搜索字段
		// $table = $column = $order_col = $label = '';
		// switch($type){
			// case 'place':$table = 'Place';$column = 'placename';$label = '地点名称'; $order_col = 'createDate';break;
			// case 'user':$table = 'User';$column = 'username'; $label = '用户名'; $order_col = 'createDate';break;
			// case 'badge':$table = ''; $column = ''; $label = '勋章名称'; $order_col = '';break;
		// }
		// $this->assign(compact('type', 'label', 'keyword'));
		// //查询数据总数
		// if($type === 'place')
			// $this->db->where('status', 0);
		// if(!empty($keyword))
			// $this->db->like($column, $keyword);
		// $count = $this->db->count_all_results($table);
		// if($count){
			// //分页
			// $parr = $this->paginate($count);
			// //数据
			// $list = array();
			// if($type === 'place')
				// $this->db->where('status', 0);
			// if(!empty($keyword))
				// $this->db->like($column, $keyword);
			// $query = $this->db->order_by($order_col, 'desc')->limit($parr['per_page_num'], $parr['offset'])->get($table);
			// foreach($query->result_array() as $row){
				// $data = array();
				// //差异化封装数据
				// switch($type){
					// case 'place':
						// $data['name'] = $row['placename'];
						// $data['address'] = $row['address'];
						// $data['createDate'] = $row['createDate'];
						// break;
					// case 'user':
						// $data['name'] = $row['username'];
						// $data['realName'] = $row['realName'];
						// $data['nickname'] = $row['nickname'];
						// $data['description'] = $row['description'];
						// break;
				// }
				// $list[$row['id']] = $data;
			// }
			// $this->assign('list', $list);
		// }
		// $this->assign('is_dialog', true);
// 		
		// $this->display('lookup', 'sys');
		redirect(site_url(array('lookup','list_lookup', 'type', $type, 'keyword', $keyword)));
	}
	
    /**
     * 屏蔽内容
     * 点评 2  图片 3 回复 11
     */
    function banned() {
        $type = $this->get('type');
        $id = $this->get('id');
        
        // 取出信息
        $table = $type=='11'?'PostReply':'Post';
        $row = $this->db->get_where($table, array('id' => $id))->row_array();
        if($row) {
            if($row['status'] == 3) {
                // 本身状态已经为屏蔽状态了
                $this->error('该记录已经屏蔽过了，不能重复屏蔽哦，亲。');
            }
            
            $this->load->helper('ugc');
            // 更新数量
            $field = '';
            $type_name = '';
            switch($type) {
                case 11:
                    $field = 'replyCount';
                    $type_name = '回复';
                    make_point($row['uid'], 'banned_reply', "0", $id);
                    break;
                case 2:
                    $field = 'tipCount';
                    $type_name = '点评';
                    make_point($row['uid'], 'banned_tip', "0", $id);
                    break;
                case 3:
                    $field = 'photoCount';
                    $type_name = '图片';
                    make_point($row['uid'], 'banned_photo', "0", $id);
                    break;
            }
            
            // 处理屏蔽信息
            $b = $this->db->where(array('id' => $id))->update($table, array('status' => 3));
            // 在频道关联POST中删除这条POST
            $this->db->where(array('postId' => $id))->delete($this->_tables['webnewscategorydata']);
            // 删除用户动态 回复不进动态
//             if($type < 11) {
//                 // 先查询出所有的动态数据
//                 $list = $this->db->get_where('UserFeed', array('type'=>$type, 'itemId'=>$id))->result_array();
//                 // 删除所有的分享
//                 $feed_ids = array();
//                 $sql = 'UPDATE User SET feedCount=feedCount-1 WHERE id=? AND feedCount > 0';
//                 foreach($list as $r) {
//                     $feed_ids[] = $r['id'];
//                     // 更新用户的feedCount
//                     $this->db->query($sql, array($r['uid']));
//                     // 去查询分享的用户
//                     $share_feed = $this->db->get_where('UserFeed', array('feedId' => $r['id']))->result_array();
//                     foreach($share_feed as $f) {
//                         $this->db->query($sql, array($f['uid']));
//                     }
//                 }
//                 // 删除分享过的FEED
//                 $feed_ids && ($b &= $this->db->where_in('feedId', $feed_ids)->delete('UserFeed'));
//                 // 删除所有的动态数据
//                 $b &= $this->db->delete('UserFeed', array('type'=>$type, 'itemId'=>$id));
//             }
			
                        
            $place_id = $row['placeId'];
            //if($field && $row['status'] != 2) {
                // 如果记录不是敏感词状态
            // 2012.09.11修改为即使在敏感状态也需要去处理数量
            if($field) {
                // 处理用户的 点评 图片 回复数
                $sql = "UPDATE User SET {$field}={$field}-1 WHERE id=? AND {$field} > 0";
                $b &= $this->db->query($sql, array($row['uid']));
                if($type < 11) {
                    // 处理地点的 点评 图片数
                    $sql = "UPDATE Place SET {$field}={$field}-1 WHERE id=? AND {$field} > 0";
                    $b &= $this->db->query($sql, array($place_id));
                } else {
                    // 获取用户回复的信息的POST是什么数据
                    $post_item = $this->db->get_where('Post', array('id'=>$row['postId']))->row_array();
                    // 回复屏蔽 ，需要去处理它的feed的replyCount
//                     $sql = "UPDATE UserFeed SET replyCount=replyCount-1 WHERE itemId=? AND itemType=? AND replyCount > 0";
//                     $b &= $this->db->query($sql, array($row['postId'], $post_item['type']));
                    // POST count-1
                    $sql = "UPDATE Post SET replyCount=replyCount-1 WHERE id=? AND replyCount > 0";
                    $b &= $this->db->query($sql, array($row['postId']));
                    // 回复的话需要获取POST的placeId
                    $place_id = $post_item['placeId'];
                }
            }
            
            // 获取地点名称
            $place = $this->db->get_where('Place', array('id'=>$place_id))->row_array();

            // 发送系统信息
            send_message('ugc_post_kill', array($row['uid']), array(null), array($type), TRUE, array(array('place'=>$place?$place['placename']:'未知地点', 'post_type'=>$type_name)));
            //积分操作
            
            // 去更新索引
            if($type < 11) {
                $this->load->helper('search');
                @update_index(40, $id, 'delete');
            }
            
            $b?$this->success('屏蔽成功'):$this->error('屏蔽出错，请检查');
        } else {
            $this->error('错误');
        }
    }
    
    /**
     * 设置关联TAG
     * Create by 2012-12-6
     * @author liuweijava
     */
   function set_tag(){
    	$objType = $this->get('type');
    	$objId = intval($this->get('ids'));//$this->get('postId');
    	$ids = explode("-",$this->get('ids'));
    	//var_dump($_REQUEST);
    	if($this->is_post()){
    		$tags = $this->post('tags');
    		if(!empty($tags)){
    			$tags = explode(' ', $tags);
    			foreach($tags as &$tag){
    				$tag = trim($tag);
    				unset($tag);
    			}
    		}else{
    			$tags = false;
    		}
    		$head = $this->config->item('tag_for_post_head');
    		
            // 访问接口数据
            foreach($ids as $k=>$row){
    			$datas[$k] = array('task'=>encode_json(array('head'=>$head, 'data'=>array('id'=>$row, 'tags'=>($tags?$tags:array())))));
            }
    		
    		//接口
    		$this->lang->load('api');
    		$api_uri = $this->lang->line('tag_api_post_join_tag');
    		//调用接口
    		//var_dump($api_uri,$data);exit;
    		foreach($datas as $data){
    			$result = send_api_interface($api_uri, 'POST', $data, array(), 'tag_api_domain');
    			if($result<0) break;
    		}
    		
    		if($result < 0) {
    		    $this->error('设置TAG的操作执行失败了！');
    		} else {
    		    // 更新网站的post_tags缓存
    		    update_cache('web', 'data', 'post_tags', $ids);
    		    /*foreach($ids as $row){
    		    	$this->m_post->do_read($row,19);
    		    }*/
    		    $this->success('操作已完成', $this->_index_rel, $this->_index_uri, 'closeCurrent');
    		}
    	}else{
    		$this->db->select('Post.*, Place.placename, User.username, User.nickname, User.avatar');
    		$this->db->join('User', 'User.id = Post.uid','inner');
    		$this->db->join('Place', 'Place.id = Post.placeId', 'left');
    		$post = $this->db->where('Post.id', $objId)->limit(1)->get('Post')->first_row('array');
    		//var_dump($objId);
    		$post['type'] == 3 && $post['image'] = image_url($post['photoName'], 'user', 'hdp');
    		//已关联的TAG
    		$this->db->select('Tag.*');
    		$this->db->join('Tag', 'Tag.id = PostOwnTag.tagId', 'inner');
    		$tags = $this->db->where(array('PostOwnTag.postId'=>$objId))->order_by('PostOwnTag.tagId', 'asc')->get('PostOwnTag')->result_array();
    		$ts = array();
    		foreach($tags as $row){
    			$ts[] = $row['content'];
    		}
    		$post['tags'] = implode(' ', $ts);
    		//频道的TAG
    		$tags = $this->_get_cat_tags();
    		//设置TAG的选中状态
    		foreach($tags as &$tag){
    			if(strpos($post['tags'], $tag['content']) !== FALSE){
    				$tag['checked'] = 1;
    			}else{
    				$tag['checked'] = 0;
    			}
    			unset($tag);
    		}
    		//$item_types = $this->config->item("item_type");
    		
    		$typename = $this->post_type[$post['type']];//$item_types[$post['type']]['value'];
    		//var_dump($typename,$post['type']);
    		$this->assign(compact('objType', 'objId', 'post', 'tags','typename'));
    		unset($tags, $ts, $ctags, $list);
    		$this->display('join_tag', 'ugc');
    	}
    }
}   
   
 // File end