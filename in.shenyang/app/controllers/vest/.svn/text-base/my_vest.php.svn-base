<?php
/**
 * 我的马甲管理
 * Create by 2012-4-1
 * @author liuw
 * @copyright Copyright(c) 2012-2014 joyotime
 */
  
 // Define and include
if ( ! defined('BASEPATH')) exit('No direct script access allowed');   
   
 // Code
class My_vest extends MY_controller{
	var $reply_url;
	var $aid;
	
	public function __construct(){
		parent::__construct();
		$this->reply_url = site_url(array('vest','my_vest','reply'));
		$this->assign('reply_url', $this->reply_url);
		$this->aid = $this->auth['id'];
	}
	
	/**
	 * 列表显示我的马甲
	 * Create by 2012-4-1
	 * @author liuw
	 */
	public function index(){
		$keyword = $this->post('keyword');
		$keyword = isset($keyword) && !empty($keyword) ? $keyword:'';
		$this->assign('keyword', $keyword);
		
		//统计所有马甲的未读消息数
		$arr = array($this->aid);
		$sql = $this->lang->line('sum_vest_all_unread_msg');
		if(!empty($keyword)){
			$sql .= ' AND u.nickname LIKE \'%'.$keyword.'%\'';
		}
		$rs = $this->db->query($sql, $arr)->first_row('array');
		$all_count = $rs['all_count'];
		$this->assign('all_count', $all_count);
		
		//查询马甲总数
		$this->db->where('MorrisVest.aid', $this->aid);
		if(isset($keyword)&&!empty($keyword)){
			$this->db->join('User','User.id=MorrisVest.uid');
			$this->db->like('User.nickname', $keyword);
		}
		$count = $this->db->count_all_results('MorrisVest');
		if($count){
			//分页
			$pdata = $this->paginate($count);
			//查询数据
			$this->db->select('MorrisVest.*,User.nickname,User.username,User.avatar,((SELECT COUNT(*) FROM ReplyMessage WHERE uid=User.id AND isRead=0) + (SELECT COUNT(*) FROM UserPrivateMessage WHERE receiver=User.id AND isRead=0)) AS mc');
			$this->db->where('MorrisVest.aid', $this->aid);
			if(!empty($keyword))
				$this->db->like('User.nickname', $keyword);
			$this->db->join('User','User.id=MorrisVest.uid');
			$this->db->order_by('MorrisVest.isFirst','desc');
			$this->db->order_by('MorrisVest.dateline','desc');
			$this->db->limit($pdata['per_page_num'], $pdata['offset']);
			$query = $this->db->get('MorrisVest');
			$list = array();
			foreach($query->result_array() as $row){
				if(empty($row['nickname']))
					$row['nickname'] = $row['username'];
				$list[$row['uid']] = $row;
			}
			$this->assign('list',$list);
			$this->assign(array('vl_total_num'=>$count, 'vl_per_page_num'=>$pdata['per_page_num'], 'vl_cur_page'=>$pdata['cur_page']));
		}
		
		//消息列表
		$this->assign('mlist', $this->get_msg_list('all'));
		
		$this->display('my_vest', 'vest');
	}
	
	/**
	 * 设置首选马甲
	 * Create by 2012-4-1
	 * @author liuw
	 */
	public function set_first(){
		if($this->is_post()){
			$uid = intval($this->get('uid'));
			//取消原来的首选马甲
			$this->db->where(array('aid'=>$this->aid,'isFirst'=>1))->update('MorrisVest', array('isFirst'=>0));
			//设置新的首选马甲
			$this->db->where(array('aid'=>$this->aid, 'uid'=>$uid))->update('MorrisVest', array('isFirst'=>1));
			$this->success($this->lang->line('do_success'), $this->_index_rel, $this->_index_uri, 'forward');
		}
	}
	
	/**
	 * 设置所有未读消息为已读
	 * Create by 2012-4-1
	 * @author liuw
	 */
	public function read_all(){
		if($this->is_post()){
			$uid = $this->get('uid');
			$uid = isset($uid)&&!empty($uid)?intval($uid):FALSE;
			$ids = array();
			if(!$uid){
			//获得马甲id
				$query = $this->db->select('uid')->where('aid', $this->aid)->get('MorrisVest');
				foreach($query->result_array() as $row){
					$ids[] = $row['uid'];
				}
			}else{
				$ids[] = $uid;
			}
			//设置系统消息
			$this->db->where_in('uid', $ids)->where('isRead',0,FALSE)->update('ReplyMessage', array('isRead'=>1));
			//设置私信
			$this->db->where_in('receiver', $ids)->where('isRead', 0, FALSE)->update('UserPrivateMessage', array('isRead'=>1));
			$this->success($this->lang->line('do_success'), $this->_index_rel, $this->_index_uri, 'forward');
		}
	}
	
	/**
	 * 回复
	 * Create by 2012-4-1
	 * @author liuw
	 */
	public function reply(){
		$no_dlg = $this->get('nodialog');
		$no_dlg = isset($no_dlg)&&!empty($no_dlg)?TRUE:FALSE;
		$sender = $this->get('sender');
		$receiver = $this->get('receiver');
		$id = $this->get('id');
		$this->assign(array('to'=>$receiver,'from'=>$sender));
		if($this->is_post()){	
			$content = $this->post('content');
			$data = array(
				'sender'=>intval($sender),
				'receiver'=> intval($receiver),
				'content'=>$content
			);
			$this->db->insert('UserPrivateMessage',$data);
            $pm_id = $this->db->insert_id();
            push_message('api_msg_private', array('pm_id' => $pm_id));
			$this->success($this->lang->line('do_success'), $this->_index_rel, $this->_index_uri, 'forward');
		}
		else{
			if(!$no_dlg){
				//查内容
				$rs = $this->db->where('id',$receiver)->limit(1)->get('User')->first_row('array');
				$nickname = !empty($rs['nickname'])?$rs['nickname']:$rs['username'];
				$this->lang->load('premessage','chinese');
				$insert = preg_replace("/@\{nickname\}/",$nickname, $this->lang->line('pm_reply_prefix'));
				$this->assign('insert', $insert);
				$this->assign('do','reply');
				$this->display('my_vest','vest');
			}
		}
	}
	
	/**
	 * 显示消息列表
	 * Create by 2012-4-1
	 * @author liuw
	 */
	public function list_msg(){
		$uid = $this->get('uid');
		$list = $this->get_msg_list($uid);
		$this->assign('page_rel','jbsxBox2');
		$this->assign('list', $list);
		$this->assign('do','list_msg');
		$this->display('my_vest', 'vest');
	}
	
	/**
	 * 显示系统消息详情
	 * Create by 2012-4-1
	 * @author liuw
	 */
	public function view_pm(){
		$uid = intval($this->get('ruid'));
		$this->assign('from',$uid);
		$this->assign('search_url', site_url(array('vest','my_vest','view_pm','ruid',$uid)));
		
		//查询总数
		$count = $this->db->where('ReplyMessage.uid', $uid)->where('Reply.uid != ', $uid)->join('Reply', 'Reply.id=ReplyMessage.replyId', 'inner')->count_all_results('ReplyMessage');
		if($count){
			//分页
			$parr = $this->paginate($count);
			$this->assign('page_rel','jbsxBox3');
			//数据列表
			$param = array($uid,$parr['offset'], intval($parr['per_page_num']));
			$this->lang->load('sql','chinese');
			$sql = $this->lang->line('vest_reply_list_from');
			$list = array();
			$query = $this->db->query($sql,$param)->result_array();
			foreach($query as $row){
				$s_name = $row['s_name'];
				$item = array(
					'sender'=>$uid,
					'receiver'=>$row['uid'],
					'avatar'=>!empty($row['avatar']) ? image_url($row['avatar'],'head','odp') : '',
					'createDate'=>$row['createDate'],
					'content'=>$row['content'],
					'id'=>$row['id'],
					's_name'=>$s_name
				);
				//echo $row['itemId'].",".$row['itemType']."<br/>";
				$source_item = array();
				//owner
				switch($row['itemType']){
					case 19:
						$typename = "POST";
						$source_item = $this->db->where("id",$row['itemId'])->get($this->_tables['post'])->row_array(0);
						break;
					case 20:
						$typename = "地点册";
						$source_item = $this->db->where("id",$row['itemId'])->get($this->_tables['placecollection'])->row_array(0);
						break;
				}
				$row['owner'] = $source_item['uid'];
				//查询r_name
				$ruid = empty($row['replyTo']) ? $row['owner'] : $row['replyTo'];
				$rs = $this->db->query('SELECT IF(nickname IS NOT NULL AND nickname !=\'\',nickname, username) AS uname FROM User WHERE id=?', array($ruid))->first_row('array');
				$row['r_name'] = $rs['uname'];
				//echo $row['r_name'];
				//拼接title
				if($row['owner'] == $uid && (!in_array($row['owner'], array($row['replyTo'], $row['uid'])) && !empty($row['replyId']))){
					$title = $s_name.'在你的'.$typename.'中回复了'.$row['r_name'];
				}else{
					$title = $s_name.'回复了你';
					if(empty($row['replyId'])){
						/*switch($row['ptype']){
							case $this->config->item('post_comment'):$title .= '在['.$row['placename'].']的点评';break;
							case $this->config->item('post_pic'):$title .= '在['.$row['placename'].']的图片';break;
						}*/
						$title .= '的'.$typename;
					}
				}
				$item['title'] = $title;
				//回复地址
				$item['reply_url'] = site_url(array('ugcv3','reply','send','receiver', $item['receiver'], 'sender', $item['sender'],'item_id', $row['itemId'],'item_type',$row['itemType'],'reply_id',$row['id'], 'reply_type', 1, 'rel', $this->_index_rel));
				$list[$row['id']] = $item;
			}
			$this->assign('list', $list);
		}
		//更新回复消息的状态
		$this->db->where('isRead',0,FALSE)->where('uid',$uid,FALSE)->update('ReplyMessage', array('isRead'=>1));
		
		$this->assign('do','view_msg');
		$this->display('my_vest', 'vest');
	}
	
	/**
	 * 显示用户私信
	 * Create by 2012-4-1
	 * @author liuw
	 */
	public function view_topic(){
		$sender = intval($this->get('from'));
		$uid = intval($this->get('to'));
		$this->assign('to',$sender);
		$this->assign('from', $uid);
		$this->assign('search_url', site_url(array('vest','my_vest','view_topic','from',$sender,'to',$uid)));
		
		//查询总长度
		$count = $this->db->where(array('sender'=>$sender,'receiver'=>$uid),null,FALSE)->or_where(array('sender'=>$uid,'receiver'=>$sender), null, FALSE)->count_all_results('UserPrivateMessage');
		if($count){
			//分页
			$pagearr = $this->paginate($count);
			$this->assign('page_rel','jbsxBox3');
			//数据
			$list = array();
			$this->db->select('UserPrivateMessage.content,UserPrivateMessage.createDate,UserPrivateMessage.isRead,UserPrivateMessage.sender,User.avatar,User.nickname,User.username');
			$this->db->join('User','User.id=UserPrivateMessage.sender','inner');
			$this->db->where("(UserPrivateMessage.sender='{$sender}' AND UserPrivateMessage.receiver='{$uid}') OR (UserPrivateMessage.sender='{$uid}' AND UserPrivateMessage.receiver='{$sender}')");
			$this->db->order_by('UserPrivateMessage.createDate','desc');
			$this->db->limit($pagearr['per_page_num'], $pagearr['offset']);
			$query = $this->db->get('UserPrivateMessage');
			foreach($query->result_array() as $row){
				$row['is_my_send'] = $row['sender'] == $uid ? 1 : 0;
				$row['nickname'] = !empty($row['nickname']) ? $row['nickname'] : $row['username'];
				$list[] = $row;
			}
			$this->assign('list',$list);
		}
		
		//更新消息状态
		$this->db->where(array('sender'=>$sender,'receiver'=>$uid),null,FALSE)->update('UserPrivateMessage', array('isRead'=>1));
		$this->assign('do','view_topic');
		$this->display('my_vest', 'vest');
	}
	
	/**
	 * 查询消息列表，包含私信对话和回复提醒
	 * Create by 2012-5-9
	 * @author liuw
	 */
	private function get_msg_list($uid){
		$uid = $uid !== 'all' ? intval($uid) : $uid;
		$arr = array($this->auth['id'],$this->auth['id']);
		//搜索条件
		$mtype = $this->post('mtype');
		$mtype = isset($mtype)&&!empty($mtype) ? intval($mtype) : FALSE;
		$is_read = $this->post('is_read');
		$is_read = isset($is_read)&&!empty($is_read) ? 1 : 0;
		$this->assign(compact('mtype','is_read'));
		$sql = $this->lang->line('vest_msg_list_from');
		$sql_counter = $this->lang->line('vest_msg_list_from_count');
		if($uid !=='all'){
			$split = empty($split) ? 'WHERE' : 'AND';
			$sql .= ' '.$split.' ruid=?';
			$arr[] = $uid;
			$this->assign('uid', $uid);
		}
		//高级搜索
		if($mtype){
			$split = empty($split) ? 'WHERE' : 'AND';
			$sql .= ' '.$split.' type=?';
			$arr[] = $mtype;
		}
		if($is_read){
			$split = empty($split) ? 'WHERE' : 'AND';
			$from_c .= ' '.$split.' if( tmp.type = 1 , m.isRead=0 , n.isRead=0 )';
			$sql .= ' '.$split.' if( tmp.type = 1 , m.isRead=0 , n.isRead=0 )';
		}
		$sql_c = 'SELECT COUNT(*) AS numrows FROM '.$sql_counter;
		//数据总长度
		$rs = $this->db->query($sql_c, $arr)->first_row('array');
		$count = $rs['numrows'];
		if($count){
			//分页
			$pagearr = $this->paginate($count);
			//数据
			$list = array();
			$sql = /*'SELECT * FROM '.*/$sql.' ORDER BY createDate DESC LIMIT '.$pagearr['offset'].','.$pagearr['per_page_num'];
			$rs = $this->db->query($sql, $arr)->result_array();
			foreach($rs as $key=>$row){
				$row['jump_url'] = $row['type'] == 1 ? site_url(array('vest','my_vest','view_topic','from',$row['suid'],'to',$row['ruid'])) : site_url(array('vest','my_vest','view_pm','ruid',$row['ruid']));
				$list[] = $row;
			}
			$this->assign(array('ml_total_num'=>$count,'ml_per_page_num'=>$pagearr['per_page_num'], 'ml_cur_page'=>$pagearr['cur_page']));
		}
		return $list;
	}
	
}   
   
 // File end