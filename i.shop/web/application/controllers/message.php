<?php
/**
 * 消息中心
 * Create by 2012-9-28
 * @author liuw
 * @copyright Copyright(c) 2012-
 */
  
 // Define and include
if ( ! defined('BASEPATH')) exit('No direct script access allowed');   
   
 // Code
class Message extends MY_Controller{
	
	function __construct(){
		parent::__construct();
		$this->assign('active', 'message');
	}
	
	/**
	 * 管理会员欢迎消息
	 * Create by 2012-9-28
	 * @author liuw
	 * @return code:1=消息内容为空,2=编辑失败,0=编辑成功
	 */
	function index(){
		$brandId = $this->auth['brand_id'];
	//	$this->assign('active', 'message');
		//商家消息详情
		$msg = $this->db->where('brandId', $brandId)->order_by('createDate', 'desc')->get('ApplyBrand')->first_row('array');//正式表没数据，从申请获取
		if(!empty($msg)){
			if(!empty($msg['welcomeLink'])){
				//解析获得优惠获得关联的优惠ID
				$msg['prefer_id'] = intval(str_replace('inprefer://', '', $msg['welcomeLink']));
				$p = $this->db->where('id', $msg['prefer_id'])->limit(1)->get('Preference')->first_row('array');
				$msg['prefer'] = $p['title'];
			}else{
				$msg['prefer'] = '无链接';
			}
		}
		$this->assign('msg', $msg);
		//商家的优惠，只能取已通过审核的
		$query = $this->db->where(array('brandId'=>$brandId, 'status'=>0, 'endDate > '=>gmdate('Y-m-d', time()+8*3600), 'isSend'=>1))->get('Preference')->result_array();
		$preferes = array();
		foreach($query as $row){
			$prefers[] = array('id'=>$row['id'], 'title'=>$row['title']);
		}
		$this->assign('prefers', $prefers);
		if($this->is_post()){
			$welcomeMsg = $this->post('msg');
			$prefer_id = $this->post('prefer_id');
			
			//检查是否有修改
			$welcomeMsg === $msg['welcomeMsg'] && $prefer_id == $msg['prefer_id'] && $this->echo_json(array('code'=>0, $this->lang->line('message_msg_edit_nothing')));
			//数据检查
			$msg_l = cstrlen($welcomeMsg);
			$msg_l > 140 && $this->echo_json(array('code'=>1, 'msg'=>'欢迎消息的长度不能超过140个汉字'));
			empty($welcomeMsg) && $this->echo_json(array('code'=>1, $this->lang->line('message_msg_empty')));
			$welcomeLink = '';
			!empty($prefer_id) && $welcomeLink = 'inprefer://'.$prefer_id;
			//链接到基础会员卡
		/*	$card = $this->db->where(array('brandId'=>$this->auth['brand_id'], 'isBasic'=>1))->order_by('createDate', 'desc')->limit(1)->get('BrandMemberCard')->first_row('array');
			$welcomeLink = 'inmcard://' . $card['id']; */
			//增加申请
		//	$apply = $this->brand;
		//	unset($apply['id'], $apply['lordId'], $apply['placeCount'], $apply['memberCount'], $apply['placeCategoryId']);
			$apply['status'] = 0;
			$apply['brandId'] = $this->auth['brand_id'];
			$apply['welcomeMsg'] = $welcomeMsg;
			$apply['welcomeLink'] = $welcomeLink;
			$apply['name'] = $this->brand['name'];
			//检查最新的一条申请是否已审核，未审核的直接修改，已审核过的则新加
			$old_new = $this->db->where('brandId', $this->auth['brand_id'])->order_by('createDate', 'desc')->limit(1)->get('ApplyBrand')->first_row('array');
			if(!empty($old_new) && !$old_new['status']){
				//更新最后一条
				$this->db->where('id', $old_new['id'])->update('ApplyBrand', $apply);
				$this->echo_json(array('code'=>0, 'msg'=>$this->lang->line('message_msg_edit_success'), 'refer'=>''));
			}else{
				$this->db->insert('ApplyBrand', $apply);
				$id = $this->db->insert_id();
				if($id <= 0)
					$this->echo_json(array('code'=>2, 'msg'=>$this->lang->line('message_msg_edit_faild')));
				else 
					$this->echo_json(array('code'=>0, 'msg'=>$this->lang->line('message_msg_edit_success'), 'refer'=>''));
			}
		}else{
			$this->assign('menu', 'index');
			$this->display('index');
		}
	}
	
	/**
	 * 发送新的推送
	 * Create by 2012-11-27
	 * @author liuweijava
	 */
	function add_new_push(){
		$brandId = $this->auth['brand_id'];
		//获得商家最后一次推送消息的时间
		$push_max = $this->config->item('push_max');
		$now = gmdate('Y-m-d', time()+8*3600);
		$begin = $now.' 00:00:00';
		$end = $now.' 23:59:59';
		//查询当天已推荐的次数
		$pushed_size = $this->db->where('brandId', $brandId)->where('pushDate BETWEEN \''.$begin.'\' AND \''.$end.'\'')->count_all_results('BrandPushLog');
		$can_push = $pushed_size < $push_max ? 1 : 0;
		$this->assign('push_max', $push_max);
		$this->assign('can_push', $can_push);
	//$this->assign('can_push', 0);
		
		//商家的优惠，只能取已通过审核的
		$query = $this->db->where(array('brandId'=>$brandId, 'status'=>0, 'endDate > '=>gmdate('Y-m-d', time()+8*3600), 'isSend'=>1))->get('Preference')->result_array();
		$preferes = array();
		foreach($query as $row){
			$prefers[] = array('id'=>$row['id'], 'title'=>$row['title']);
		}
		$this->assign('prefers', $prefers);
		
		if($this->is_post()){
			//接收消息的用户ID
			$uids = $this->post('recieverIds');
			$recIds = $data = array();
			if($uids === 'all'){//全体会员
				$query = $this->db->distinct()->select('uid')->where(array('brandId'=>$brandId, 'isBasic'=>1))->get('UserOwnMemberCard')->result_array();
				foreach($query as $row){
					$recIds[] = $row['uid'];
				}
				unset($query);
			}
			//消息主体
			$content = $this->post('content');
			empty($content) && $this->echo_json(array('code'=>2, 'msg'=>$this->lang->line('message_push_content_empty')));//消息内容为空
			$msg_l = cstrlen($content);
			$msg_l > 140 && $this->echo_json(array('code'=>3, 'msg'=>'欢迎消息的长度不能超过140个汉字'));//内容超长
			//检查敏感词
			$used = $taboo = array();
			$taboo = $this->db->get('Taboo')->result_array();
			foreach($taboo as $t){
				if(strpos($content, $t['word']) !== false){
					$used[] = $t['word'];
				}
			}
			unset($taboo);
			if(!empty($used)){
				$error = sprintf($this->lang->line('message_push_content_has_taboo'), implode(',', $used));
				$this->echo_json(array('code'=>4, 'msg'=>$error));//内容包含敏感词
			}else{			
				$preferId = $this->post('prefer_id');
				//写入表
				$dateline = time()+8*3600;
				$data = compact('content', 'preferId', 'brandId', 'dateline');
				$this->db->insert('BrandPushMessage', $data);
				$id = $this->db->insert_id();
				!$id && $this->echo_json(array('code'=>1, 'msg'=>$this->lang->line('message_push_content_failed')));
			//记录消息实体
				if($id){
					$datas = array();
					$data = array(
						'content'=>$content,
						'type'=>17,
						'itemId'=>$id,
						'sendType'=>2,
					);
					!empty($preferId) && $data['relatedHyperLink'] = 'inprefer://'.$preferId;
					foreach($recIds as $k=>$recId){
						$data['recieverId'] = $recId;
						$datas[] = $data;
					}
					unset($data);
					//保存数据
					$this->db->insert_batch('SystemMessage', $datas);
					unset($datas);
					//获得消息实体的ID列表
					$ids = array();
					$query = $this->db->select('id')->where(array('type'=>17, 'itemId'=>$id))->get('SystemMessage')->result_array();
					foreach($query as $row){
						$ids[] = $row['id'];
					}
					//调用接口发送消息
					$param = array(
						'api'		 => $this->lang->line('api_push_sys_msgs'),
						'uid'		 => $this->auth['id'],
						'has_return' =>	true,
						'attr'		 => array('sys_msg_ids'=>implode(',', $ids))
					);
					$result = json_decode($this->call_api($param), true);
					$msg = $result['result_msg'];
					$code = intval($result['result_code']);
					$code > 0 && $this->echo_json(array('code'=>$code, 'msg'=>$this->lang->line('message_push_push_failed')));
					//推送成功，记录商家推送日志
					$data = array('brandId'=>$brandId, 'msgId'=>$id);
					$this->db->insert('BrandPushLog', $data);
				//	$this->db->where('id', $brandId)->set('lastPushTime', gmdate('Y-m-d H:i:s', time()+8*3600))->update('Brand');
					$this->echo_json(array('code'=>0, 'msg'=>$this->lang->line('message_push_push_success'), 'refer'=>'/message/pushed'));
				}
			}
		}else{
			$this->assign('menu', 'newpush');
			$this->display('new_push');
		}
	}
	
	/**
	 * 已推送消息列表
	 * Create by 2012-11-27
	 * @author liuweijava
	 * @param unknown_type $page
	 */
	function pushed($page=1){
		$brandId = $this->auth['brand_id'];
		$list = array();
		//查询消息总数
		$count = $this->db->where('brandId', $brandId)->count_all_results('BrandPushMessage');
		if($count){
			//分页
			$parr = paginate('/message/pushed', $count, $page);
			//列表
			$now = time()+8*3600;
			$query = $this->db->where('brandId', $brandId)->order_by('dateline', 'desc')->limit($parr['size'], $parr['offset'])->get('BrandPushMessage')->result_array();
			foreach($query as $row){
				//发送时间
				$row['pushDate'] = gmdate('Y/m-d H:i', $row['dateline']);
				//已读人数
				$readCount = $this->db->where(array('type'=>17, 'itemId'=>$row['id'], 'isRead'=>1))->count_all_results('SystemMessage');
				$row['readCount'] = $readCount;
				//优惠名称
				if($row['preferId']){
					$q = $this->db->where('id', $row['preferId'])->get('Preference')->first_row('array');
					$end = strtotime(substr($row['endDate'], 0, -9).' 23:59:59')+8*3600;
					($q['status'] == 2 || $end <= $now) && $q['str_status']='<font color="red">已过期</font>';
					$row['prefer'] = $q;
					unset($q);
				}
				$list[$row['id']] = $row;
				unset($row);
			}
			unset($query);
		}
		$this->assign('list', $list);
		$this->assign('menu', 'pushed');
		$this->display('list');
	}
	
	/**
	 * 发放优惠，向用户发送一条消息
	 * Create by 2012-9-28
	 * @author liuw
	 * @param int $prefer_id
	 * @return json ,code:0=发送成功，1=优惠数据错误（无相关优惠或优惠已过期），2=已推送过了，3=发送失败(API反馈的信息)
	 */
	function grant_prefer(){
		//因为IE7的BUG，这里改成POST试试
		if($this->is_post()){
			$prefer_id = $this->post('id');
			//优惠属性
			$prefer = $this->db->where(array('id'=>$prefer_id, 'brandId'=>$this->auth['brand_id']))->get('Preference')->first_row('array');
			if(empty($prefer)){
				$this->echo_json(array('code'=>1, 'msg'=>$this->lang->line('do_faild').': 未查询到相关优惠'));
			}elseif($prefer['status'] > 0){
				$this->echo_json(array('code'=>1, 'msg'=>$this->lang->line('do_faild').': 优惠已过期'));
			}elseif($prefer['isSend'] == 1){
				$this->echo_json(array('code'=>2, 'msg'=>$this->lang->line('do_faild').': 该优惠已发送过了'));
			}else{
				//调用接口API给所有会员发送指定的优惠
				$param = array(
					'api'		 => $this->lang->line('api_push_prefer'),
					'uid'		 => $this->auth['id'],
					'has_return' =>	true,
					'attr'		 => array('prefer_id'=>$prefer_id)
				);
				$result = json_decode($this->call_api($param), true);
			//	file_put_contents('test.txt', print_r($result, true));
				$code = intval($result['result_code']);
				$msg = $result['result_msg'];
				if($code == 0){
					$msg = $this->lang->line('do_success');
					//修改isSend
					$this->db->where('id', $prefer_id)->set('isSend', 1, false)->update('Preference');
					$rs = $this->db->where('id', $prefer_id)->get('Preference')->first_row('array');
					if($rs['isSend'] != 1){
				//		$code = 1;
				//		$msg = '更改优惠发送状态失败了';
						//强制更新状态
						$this->db->query('UPDATE Preference SET isSend=1 WHERE id=?', array($prefer_id));
					}
				}
				$this->echo_json(compact('code', 'msg'));
			}
		}else{
			$this->echo_json(array('code'=>1, 'msg'=>'非法请求'));
		}
	}
	
	function check_length(){
		if($this->is_post()){
			$msg = $this->post('msg');
			$length = cstrlen($msg);
			$code = $length > 140 ? 1 : 0;
			$this->echo_json(array('code'=>$code, 'msg'=>$length));
		}
	}
	
}
   
 // File end