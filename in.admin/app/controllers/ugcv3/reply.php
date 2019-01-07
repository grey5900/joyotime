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
	
	function __construct(){
		parent::__construct();
		$this->load->model("reply_model","m_reply");
		$this->load->model("post_model","m_post");
		$this->load->model("morrisvest_model","m_vest");
	}
	
	public function index(){
		$pageNum 	= $this->input->post("pageNum") ? $this->input->post("pageNum") : 1;
    	$numPerPage = $this->input->post("numPerPage") ? $this->input->post("numPerPage") : 20;
    	$keywords 	= $this->input->post("keywords");
    	$status 	= intval($this->input->post("status"));
    	$itemid 	= $this->get("itemid") ;
    	$itemtype 	= $this->get("itemtype") ;
    	
    	 
    	
    	$isread = $this->input->post("isread");
    	$isTaboo = $this->input->post("isTaboo");
    	
    	$begin = $this->input->post("begin");
    	$end = $this->input->post("end");
    	
    	$user = $this->input->post("user_id");
    	
    	$where = " 1=1 "; 
    	$where .= $keywords ? " and  content like '%".$keywords."%' " : "";
    	$where .= $status === '0' || $status>=1 ? " and  status = ".$status  : "";
    	//$where .= $type ? " and  r.type = ".$type  : "  ";
    	$where .= $isTaboo ? " and isTaboo = 1" : "";
    	$where .= $isread ?  " and m.read is null" : "";//取未读的
    	$where .= $itemid && !$itemtype ? " and r.itemId = ".$itemid." and r.itemType=19" : ""; //因为。。post的回复嘛。只能是19了..猪，还有地点册的回复
    	$where .= $itemid && $itemtype ? " and r.itemId = ".$itemid." and r.itemType=20" : "";
    	
    	$where .= $begin ? " and r.createDate >= '$begin' " : "";
    	$where .= $end ? " and r.createDate <= '$end' " : "";
    	
    	$where .= $user ? " and uid = $user " : "";
    	
    	$list = $this->m_reply->get_reply_list($where,$numPerPage,($pageNum-1)*$numPerPage);
    	$total = $this->m_reply->count_reply($where,$having);
    	
    	if($total){
    		$parr = $this->paginate($total);
    	}
    	
    	
    	$this->assign(compact('parr','list','keywords','status','type','isread','isTaboo','begin','end','user'));
		$this->display('reply','ugcv3');
	}
	
	public function advsearch(){
		$this->assign('do','advsearch');
		$this->display('reply','ugcv3');
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
				$this->db->query('UPDATE UserFeed SET replyCount=if(replyCount-1 <= 0, 0, replyCount-1) WHERE itemId=? AND itemType=?', array($row['postId'], $row['ptype']));
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
		$reply_id = $this->get('reply_id');
		$item_type = $this->get("item_type");
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
				//$rs = $this->m_vest->get_vest($this->auth['id'] , '',' uid rand() ');
				//$v_id = $rs['uid'];
			}else{
				$v_id = $this->post('v_id');
			}
			if(isset($v_id) && !empty($v_id))
				$sender = intval($v_id);
			//执行操作
			//$this->load->helper('ugcv3');
			//$rt = reply($sender, $rlist, $content, $reply_type, $item_id);
			//request_api('/post/list_following', 'GET', array(), array('uid' => 1));
			$attrs = array(
				'item_type' => $item_type,
				'item_id' => $item_id,
				'content' => $content,
				'reply_uid' => $receiver//,
				//'reply_rid' => $item_id
			);
			$reply_id && $attrs['reply_rid'] = $reply_id;
				
			//var_dump($reply_id,$attrs);exit;
			$result = request_api('/reply/reply', 'POST', $attrs, array('uid' => $v_id));
			if(!$result) $this->error("请求API失败");
			$rt = json_decode($result,true);
			
			if($rt['result_code'] == 0){
				
				$this->success($rt['result_msg'],$from_rel?$from_rel:null,null,'closeCurrent');
			}
			else{
				
				$this->error($rt['result_msg']);
			}
		}else{		
			//检查当前后台帐号有没有马甲
			$count = $this->m_vest->count_vest($this->auth['id']);//$this->db->where('aid', $this->auth['id'])->count_all_results('MorrisVest');
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
        $item_type = intval($this->get('item_type'));
        
        $reply_id = intval($this->get("reply_id"));
        
        if($do == 'reply') {
            $row = $this->m_reply->get_one_reply($reply_id);//$this->db->get_where('PostReply', array('id'=>$item_id))->row_array();
        } else if($do == 'post') {
            $row = $this->m_post->get_one_post($item_id);//$this->db->get_where('Post', array('id'=>$item_id))->row_array();
        }
        else if($do == 'placecollection'){
        	$row = $this->db->where("id",$item_id)->get("PlaceCollection")->row_array(0);
        }
        redirect(site_url(array(
                'ugcv3',
                'reply',
                'send',
                'receiver',
                $row['uid'],
                'reply_type',
                $do=='reply'?1:0,
                'item_id',
                $item_id,
                'item_type',
                $item_type,
                'reply_id',
                $do=='reply'?$reply_id:0,
                'rel',
                $this->get('rel'),
                'in_sign',
                in_sign()
        )));
    }
}   
   
 // File end