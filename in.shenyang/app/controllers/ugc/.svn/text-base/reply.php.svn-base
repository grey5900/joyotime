<?php
/**
 * 回复管理
 * Create by 2012-3-22
 * @author liuw
 * @copyright Copyright(c) 2012-2014 joyotime
 */
  
 // Define and include
if ( ! defined('BASEPATH')) exit('No direct script access allowed');   
   
 // Code
class Reply extends MY_Controller{
	
	/**
	 * 列表显示回复
	 * Create by 2012-3-22
	 * @author liuw
	 */
	public function index(){
		$keyword = $this->post('keyword');
		$status = $this->post('status');
		$status = isset($status)&&!empty($status)?$status:'-1';
		$orderField = $this->post('orderField');
		$orderField = isset($orderField)&&!empty($orderField)?$orderField:'createDate';
		$orderDirection = $this->post('orderDirection');
		$orderDirection = isset($orderDirection)&&!empty($orderDirection)?$orderDirection:'desc';
		
		//高级搜索
		$begin = $this->post('begin');
		$begin = isset($begin) && !empty($begin) ? $begin : FALSE;
		$end = $this->post('end');
		$end = isset($end) && !empty($end) ? $end : FALSE;
		$placeId = $this->post('poi_id');
		$placeId = isset($placeId) && !empty($placeId) ? intval($placeId) : FALSE;
		$uid = $this->post('user_id');
		$uid = isset($uid) && !empty($uid)?intval($uid):FALSE;
		
		//设置查询总数的查询条件
		$this->db->from('PostReply');
		$this->db->join('User','User.id=PostReply.uid','inner');
		$this->db->join('Post','Post.id=PostReply.postId','inner');
		if(!empty($keyword))
			$this->db->like('PostReply.content',$keyword);
		if($status != '-1')
			$this->db->where('PostReply.status', $status=='9'?0:$status);
		if($placeId)
			$this->db->where('Post.placeId',$placeId, FALSE);
		if($uid)
			$this->db->where('User.id', $uid, FALSE);
		if($begin || $end){
			if($begin && $end){
				$this->db->where("PostReply.createDate BETWEEN '{$begin}' AND '{$end}'", null , FALSE);
			}elseif($begin && !$end){
				$this->db->where('PostReply.createDate >= ', $begin);
			}elseif(!$begin && $end){
				$this->db->where('PostReply.createDate <= ', $end);
			}
		}			
			
		$count = $this->db->count_all_results();
		$paginate = $this->paginate($count);
		$size = $paginate['per_page_num'];
		$offset = $paginate['offset'];
		if($count){
			//查询数据
			$list = array();
			$sql = 'select pr.*,u.username,u.avatar,p.content as post_content,p.type,p.photoName,'.
				'(select ifnull(nickname,username) as name from User where id=pr.replyUid) as r_name '.
				'from PostReply pr,User u,Post p where u.id=pr.uid and p.id=pr.postId ';
			if(!empty($keyword))
				$sql  .= 'and pr.content like \'%'.$keyword.'%\' ';
			if($status !== '-1')
				$sql .= 'and pr.status='.($status==9 ? 0 : $status).' ';
			if($placeId)
				$sql .= 'and p.placeId='.$placeId.' ';
			if($uid)
				$sql .= "and u.id={$uid} ";
			if($begin||$end){
				if($begin && $end){
					$sql .= "and pr.createDate between '{$begin}' and '{$end}' ";
				}elseif($begin && !$end){
					$sql .= "and pr.createDate >= '{$begin}' ";
				}elseif(!$begin && $end){
					$sql .= "and pr.createDate <= '{$end}' ";
				}
			}
			$sql .= 'order by pr.'.$orderField.' '.$orderDirection.' limit '.$offset.','.$size;
			$query = $this->db->query($sql)->result_array();
			foreach($query as $row){
				$row['content'] = !empty($row['replyId']) ? '[回复 '.$row['r_name'].']:'.$row['content'] : $row['content'];
				$row['avatar'] = image_url($row['avatar'], 'head', 'hmdp');//!empty($row['avatar'])?image_url($row['avatar'], 'head', 'hmdp'):'';
				switch($row['status']){
					case 1:$row['content'] = '<span class="f-blue">已审-</span><div>'.$row['content'].'</div>';break;
					case 2:
						$row['content'] = '<span class="f-red">敏感-</span><div>'.$row['content'].'</div>';
						//高亮敏感词
						$this->load->helper('ugc');
						$row['content'] = inspaction_taboo($row['content'],'post');
						break;
					case 3:$row['content'] = '<span class="f-close">屏蔽-</span><div>'.$row['content'].'</div>';break;
					default:break;
				}
				$list[$row['id']] = $row;
			}
		}
		$this->assign('aid',$this->auth['id']);
		$this->assign('list',$list);
		$this->assign(compact('keyword','status','orderField','orderDirection','begin','end','placeId','uid'));		
		//批量审核url
		$this->assign('examine_url', site_url(array('ugc','reply','edit','do','examine')));
		//批量屏蔽url
		$this->assign('status_url', site_url(array('ugc','reply','edit','do','status')));
		$this->display('reply','ugc');
	}
	
	public function advsearch(){
		$this->assign('do','advsearch');
		$this->display('reply','ugc');
	}
	
	/**
	 * 审核或屏蔽回复。通过post参数$do来区分审核和屏蔽操作
	 * Create by 2012-3-22
	 * @author liuw
	 */
	public function edit(){
		$do = $this->get('do');
		if(!isset($do) || empty($do) || !in_array($do, array('examine','status')))
			$this->error($this->lang->line('post_type_error'));
		$ids = $this->post('ids');
		$status = $this->config->item('post_status');
		
		$this->db->select('PostReply.uid,PostReply.status,PostReply.postId,Post.type AS ptype');
		$this->db->join('Post','Post.id=PostReply.postId');
		$this->db->where_in('PostReply.id',$ids);
		$query = $this->db->get('PostReply');
		$taboos = $list = array();
		foreach($query->result_array() as $row){
			$list[] = $row;
			if($row['status'] == $status['taboo'])
				$taboos[] = $row;
		}
		$this->db->query('UPDATE PostReply SET status='.$status[$do]." WHERE id IN ('".implode("','", $ids)."') AND status<>'{$status['status']}' AND status<>3");
		$this->load->helper('ugc');
		if($do == 'status'){
			//屏蔽回复，需要向用户发一条私信并执行积分操作
			$msg_key = 'ugc_post_kill';
			$pids = $uids = array();
			$tos = $items = $types = $replace = array();
			$update_u_sql = 'UPDATE User SET replyCount=if(replyCount-1 <= 0,0,replyCount-1) WHERE id IN ';
			$update_p_sql = 'UPDATE Post SET replyCount=if(replyCount-1 <= 0,0,replyCount-1) WHERE id IN ';
			foreach($list as $row){
				$rp['post_type'] = '回复';
				//获取地点名称
				$rs = $this->db->select('Place.placename')->join('Post','Post.placeId=Place.id','left')->where('Post.id', $row['postId'])->get('Place')->first_row('array');
				$rp['place'] = isset($rs)&&!empty($rs)&&!empty($rs['placename']) ? $rs['placename'] : '未知地点';
				$tos[] = $row['uid'];
				$items[] = $row['postId'];
				$types[] = $row['ptype'] == $this->config->item('post_comment') ? $this->config->item('msg_comment') : ($row['ptype'] == $this->config->item('post_pic')?$this->config->item('msg_pic'):$this->config->item('msg_pm'));
				
				$pids[] = $row['postId'];
				$uids[] = $row['uid'];
				$replace[] = $rp;
				//Feed replyCount -1
// 				$this->db->query('UPDATE UserFeed SET replyCount=if(replyCount-1 <= 0, 0, replyCount-1) WHERE itemId=? AND itemType=?', array($row['postId'], $row['ptype']));
			}
			send_message($msg_key, $tos, $items,  $types, TRUE, $replace);				
			//-1
			if(!empty($uids))
				$update_u_sql .= "('".implode("','",$uids)."')";
			$this->db->query($update_u_sql);
			if(!empty($pids))
				$update_p_sql .= "('".implode("','",$pids)."')";
			$this->db->query($update_p_sql);

			$this->success($this->lang->line('do_success'));
		}
		if($do == 'examine'){
			//给敏感词内容发布者发私信
			// $msg_key = 'ugc_taboo_examine';
			// $tos = $items = $types = $replace = array();
			// foreach($taboos as $row){
			    // if($row['status'] == 3) {
                    // // 为屏蔽状态那么不处理
                    // continue;
                // }
//                 
				// $rr['post_type'] = '回复';
				// //获取地点名称
				// $rs = $this->db->select('Place.placename')->join('Post','Post.placeId=Place.id','left')->where('Post.id', $row['postId'])->get('Place')->first_row('array');
				// $rr['place'] = isset($rs)&&!empty($rs)&&!empty($rs['placename']) ? $rs['placename'] : '未知地点';
				// $replace[] = $rr;
				// $tos[] = $row['uid'];
				// $items[] = $row['postId'];
				// $types[] = $row['ptype'] == $this->config->item('post_comment') ? $this->config->item('msg_comment') : ($row['ptype'] == $this->config->item('post_pic')?$this->config->item('msg_pic'):$this->config->item('msg_pm'));
				// if($row['status'] == 2){
	                // // POST数据+1
	                // $this->db->query("UPDATE Post SET replyCount=replyCount+1 WHERE id='{$row['postId']}'");
	                // // Feed数据+1
	                // $this->db->query("UPDATE UserFeed SET replyCount=replyCount+1 WHERE itemType='{$row['ptype']}' AND itemId='{$row['postId']}'");
	                // // USER数据+1
	                // $this->db->query("UPDATE User SET replyCount=replyCount+1 WHERE id='{$row['uid']}'");
					// send_message($msg_key, $tos, $items, $types, TRUE, $replace);
				// }
			// }
			$this->success($this->lang->line('do_success'), $this->_index_rel, $this->_index_uri);
		}
		
	}
	
	/**
	 * 回复指定的回复
	 * Create by 2012-3-22
	 * @author liuw
	 */
	public function recomment(){
		$id = $this->get('id');
		if($this->is_post()){
			$item_id = $this->post('item_id');
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
			//封装数据
			$data = array(
				'uid'=>$uid,
				'content'=>$content,
				'postId'=>$item_id,
			);
			//保存数据
			$this->db->insert('PostReply',$data);
			$new_id = $this->db->insert_id();
			if(!$new_id)
				$this->error($this->lang->line('post_reply_error'));
			else{
				//更新post的回复数
				$this->db->query('UPDATE Post SET replyCount=replyCount+1 WHERE id='.$item_id);
				$this->success($this->lang->line('post_reply_success'));
			}
		}else{
			//查询填充内容
			$this->db->select('PostReply.postId,PostReply.content,User.username,User.nickname');
			$this->db->from('PostReply');
			$this->db->join('User','User.id=PostReply.uid');
			$this->db->where('PostReply.id',$id);
			$row = $this->db->get()->first_row('array');
			$insert = '回复 '.(isset($row['nickname'])&&!empty($row['nickname'])?$row['nickname']:$row['username']).': '.$row['content'];
			$this->assign('insert',$insert);
			$this->assign('item_id',$row['postId']);
			$this->assign('post_url', site_url(array('ugc','reply','recomment','id',$id)));
			$this->assign('do','reply');
			$this->assign('aid',$this->auth['id']);
			$this->display('reply','ugc');
		}
	}
	
	/**
	 * 通用回复&发私信
	 * Create by 2012-4-13
	 * @author liuw
	 */
	public function send(){
	    $id = $this->get('id');
        if($id) {
            // 特殊处理下
            list($receiver, $item_id) = explode('_', $id);
        } else {
            $receiver = $this->get('receiver');
            $item_id = $this->get('item_id');
        }
		// $receiver = $this->get('receiver');
		$sender = $this->get('sender');
		// $item_id = $this->get('item_id');
		$reply_type = $this->get('reply_type');
		$from_rel = $this->get('rel');
		$from_rel = isset($from_rel)&&!empty($from_rel)?$from_rel:FALSE;
		
		if($this->is_post()){
			$rlist = array();
			if(isset($receiver) && !empty($receiver)){
                $rlist = explode(':', $receiver);
				// if(is_array($receiver))
					// $rlist = $receiver;
				// else
					// $rlist[] = $receiver;
			}
			$content = $this->post('content');
			$vest = $this->post('vest');
			if($vest === 'random'){
				//随机
				$rs = $this->db->where('aid', $this->auth['id'])->select('uid')->order_by('uid','random')->limit(1)->get('MorrisVest')->first_row('array');
				$v_id = $rs['uid'];
			}else{
				$v_id = $this->post('v_id');
			}
			if(isset($v_id) && !empty($v_id))
				$sender = intval($v_id);
			//执行操作
			$this->load->helper('ugc');
			$rt = reply($sender, $rlist, $content, $reply_type, $item_id);
			if($rt['err_code'] == 300)
				$this->error($rt['message']);
			else
				$this->success($rt['message'],$from_rel?$from_rel:null,null,'closeCurrent');
		}else{		
			//检查当前后台帐号有没有马甲
			$count = $this->db->where('aid', $this->auth['id'])->count_all_results('MorrisVest');
			if(!isset($count) || $count <= 0){
				echo execjs("alertMsg.warn('".$this->lang->line('reply_vest_has_empty')."');$.pdialog.closeCurrent();", false);
			}else{
				if(!empty($sender)){
					//指定了马甲的
					$rs = $this->db->where('id', $sender)->limit(1)->get('User')->first_row('array');
					$vest = array(
						'id' => intval($sender),
						'name' => isset($rs['nickname'])&&!empty($rs['nickname'])?$rs['nickname']:$rs['username']
					);
					$this->assign('vest',$vest);
				}
				$this->display('reply_panel','ugc');
			}
		}
	}
	
	/**
	 * 获取当前用户的首选马甲
	 * Create by 2012-4-13
	 * @author liuw
	 */
	public function get_frist_vest(){
		$aid = $this->auth['id'];
		//查询
		$rs = $this->db->select('User.id, User.nickname, User.username')->from('MorrisVest')->join('User','User.id=MorrisVest.uid')->where('MorrisVest.aid', $aid)->where('MorrisVest.isFirst',1)->limit(1)->get()->first_row('array');
		if(!isset($rs) || empty($rs)){
			//没有首选马甲，则随机一个马甲
			$rs = $this->db->select('User.id, User.nickname, User.username')->from('MorrisVest')->join('User','User.id=MorrisVest.uid')->where('MorrisVest.aid', $aid)->order_by('MorrisVest.uid','random')->limit(1)->get()->first_row('array');
		}
		exit(json_encode(array('id'=>$rs['id'],'nickname'=>isset($rs['nickname'])&&!empty($rs['nickname'])?$rs['nickname']:$rs['username'])));
	}
    
    /**
     * 回复的跳转
     */
    function redirect() {
        $do = $this->get('do');
        // 去获取接受用户ID
        $item_id = $this->get('item_id');
        if($do == 'reply') {
            $row = $this->db->get_where('PostReply', array('id'=>$item_id))->row_array();
        } else {
            $row = $this->db->get_where('Post', array('id'=>$item_id))->row_array();
        }
        redirect(site_url(array(
                'ugc',
                'reply',
                'send',
                'receiver',
                $row['uid'],
                'reply_type',
                $do=='reply'?1:0,
                'item_id',
                $item_id,
                'rel',
                $this->get('rel'),
                'in_sign',
                in_sign()
        )));
    }
}   
   
 // File end