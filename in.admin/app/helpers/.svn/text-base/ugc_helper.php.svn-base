<?php
/**
 * UGC管理会用到的通用函数
 * Create by 2012-3-29
 * @author liuw
 * @copyright Copyright(c) 2012-2014 joyotime
 */
  
 // Define and include
if ( ! defined('BASEPATH')) exit('No direct script access allowed');   
   
 // Code

/**
 * 发送系统私信
 * Create by 2012-3-29
 * @author liuw
 * @param array $to,接受者id
 * @param mixed $message,私信内容。这个参数类型根据$is_preinstall确定，当为真（默认为真）时，表示一个预设私信内容的key，是一个整型数；为假时表示自定义的私信内容，是一个字符串
 * @param array $item_id,资源id
 * @param array $item_type,资源类型
 * @param boolean $is_preinstall,预设私信内容标志，默认为预设私信内容(TRUE)，为假时表示非预设私信内容(FALSE)
 * @param array $replace,用于替换私信内容中的自定义标签，key要求必须是私信内容中的自定义标签(省略标签界限符号)，value表示要替换成什么内容
 */ 
function send_message($msg_keys, $to, $item_id, $item_type, $is_preinstall=TRUE, $replace=array()){
	global $CI;
	$CI->lang->load('premessage','chinese');
	$datas = array();
	if(!is_array($msg_keys)){//内容相同的情况 
		$message = $is_preinstall ? $CI->lang->line($msg_keys) : $msg_keys;
	    for($i=0; $i< count($to); $i++) {
			//替换自定义标签
			if(!empty($replace)&&!empty($replace[$i])){
				$tag = $CI->config->item('tag_message');
				$regex = $contents = array();
				foreach($replace[$i] as $key=>$value){
					$regex[] = "/".str_replace('tag', $key, $tag)."/";
					$contents[] = $value;
				}
				$msg = preg_replace($regex, $contents, $message);
			}else{
				$msg = $message;
			}
			$data = array(
				'recieverId' => is_array($to)?$to[$i]:$to,
				'content' => $msg,
				'type' => is_array($item_type)?$item_type[$i]:$item_type,
				'itemId' => is_array($item_id)?$item_id[$i]:$item_id
			);
			$datas[] = $data;
		}
	}else{//内容不同的情况
		for($i=0;$i<count($msg_keys);$i++){
			$msg = $is_preinstall ? $CI->lang->line($msg_keys[$i]):$msg_keys[$i];
			//替换自定义标签
			if(!empty($replace)&&!empty($replace[$i])){
				$tag = $CI->config->item('tag_message');
				$regex = $contents = array();
				foreach($replace[$i] as $key=>$value){
					$regex[] = "/".str_replace('tag', $key, $tag)."/";
					$contents[] = $value;
				}
				$msg = preg_replace($regex, $contents, $msg);
			}
			$datas[] = array(
				'recieverId' => is_array($to)?$to[$i]:$to,
				'content' => $msg,
				'type' => is_array($item_type) ? $item_type[$i] : $item_type,
				'itemId' => is_array($item_id) ? $item_id[$i] : $item_id
			);
		}
	}

	// //保存数据
	// $CI->db->insert_batch('SystemMessage', $datas);
	// //推送消息
	// $CI->lang->load('api');
	// $api_interface = $CI->lang->line('api_msg_system');
	// $attr = array('content'=>$message);
	// if(is_array($to)){
		// foreach($to as $receiverId){
			// $attr['reciever'] = $receiverId;
			// send_api_interface($api_interface, 'POST', $attr);
		// }
	// }else{
		// $attr['reciever'] = $to;
		// send_api_interface($api_interface, 'POST', $attr);
	// }
	static $item_types = null;
    if($item_types == null) {
        $item_types = $CI->config->item('item_type');
    }
	$system_ids = array();
	foreach($datas as $row) {
	    $item_type = $item_types[$row['type']];
        if($item_type) {
            $row['itemId'] > 0 && $row['relatedHyperLink'] = $item_type['key'] . '://' . $row['itemId'];
        }
	    // 一条一条的加入
	    $CI->db->insert('SystemMessage', $row);
        $system_ids[] = $CI->db->insert_id();
	}
    $CI->lang->load('api');
    $api_interface = $CI->lang->line('api_msg_system');
    // 根据ID推送消息
    foreach($system_ids as $sys_id) {
        send_api_interface($api_interface, 'POST', array('sys_msg_id' => $sys_id));
    }
}

/**
 * 发送系统私信
 * Create by 2012-3-29
 * @author liuw
 * @param array $to,接受者id
 * @param mixed $message,私信内容。这个参数类型根据$is_preinstall确定，当为真（默认为真）时，表示一个预设私信内容的key，是一个整型数；为假时表示自定义的私信内容，是一个字符串
 * @param array $item_id,资源id
 * @param array $item_type,资源类型
 * @param boolean $is_preinstall,预设私信内容标志，默认为预设私信内容(TRUE)，为假时表示非预设私信内容(FALSE)
 * @param array $replace,用于替换私信内容中的自定义标签，key要求必须是私信内容中的自定义标签(省略标签界限符号)，value表示要替换成什么内容
 */
/*
function send_message2($message, $to, $item_id, $item_type, $is_preinstall=TRUE, $replace=array()){
    global $CI;
	// $CI = $GLOBALS['CI'];
	$CI->lang->load('premessage','chinese');
	//预设内容
	$message = $is_preinstall ? $CI->lang->line($message) : $message;
	//替换自定义标签
	if(!empty($replace)){
		$tag = $CI->config->item('tag_message');
		$regex = $contents = array();
		foreach($replace as $key=>$value){
			$regex[] = "/".str_replace('tag', $key, $tag)."/";
			$contents[] = $value;
		}
		$message = preg_replace($regex, $contents, $message);
	}
	//构造数据
	$datas = array();
	// for($i=0;$i<count($to);$i++){
	$len = count($to);
    for($i=0; $i< $len; $i++) {
		$data = array(
			'recieverId' => $to[$i],
			'content' => $message,
			'type' => is_array($item_type)?$item_type[$i]:$item_type,
			'itemId' => is_array($item_id)?$item_id[$i]:$item_id
		);
		$datas[] = $data;
	}
	// //保存数据
	// $CI->db->insert_batch('SystemMessage', $datas);
	// //推送消息
	// $CI->lang->load('api');
	// $api_interface = $CI->lang->line('api_msg_system');
	// $attr = array('content'=>$message);
	// if(is_array($to)){
		// foreach($to as $receiverId){
			// $attr['reciever'] = $receiverId;
			// send_api_interface($api_interface, 'POST', $attr);
		// }
	// }else{
		// $attr['reciever'] = $to;
		// send_api_interface($api_interface, 'POST', $attr);
	// }
	
    $system_ids = array();
    foreach($datas as $row) {
        // 一条一条的加入
        $CI->db->insert('SystemMessage', $row);
        $system_ids[] = $CI->db->insert_id();
    }
    $CI->lang->load('api');
    $api_interface = $CI->lang->line('api_msg_system');
    // 根据ID推送消息
    foreach($system_ids as $sys_id) {
        send_api_interface($api_interface, 'POST', array('sys_msg_id' => $sys_id));
    }
}
*/
/**
 * 对指定用户执行积分操作并纪录积分日志
 * Create by 2012-3-29
 * @author liuw
 * @param array $uid,指定用户的user_id
 * @param int $key,积分规则id
 * @param $score 直接修改的积分
 * @param $item_id 操作对象的ID号
 * @param $remark 备注
 * @param $item_type 操作的类型，主要为了区分推荐那里
 */
function make_point($uid, $key, $score = 0, $item_id = 0, $remark = '', $item_type = '') {
	global $CI;
	//载入积分规则
	// $CI->lang->load('point');
	// $point = $CI->lang->line($key);
	// //查询用户当前积分并计算新的积分，如果为负，则置为0
	// if($uid) {
	    // if(!is_array($uid)) {
	        // $uid = array($uid);
	    // }
        // foreach($uid as $u) {
            // $r = $CI->db->select('point')->where("id = '{$u}'")->get('User')->row_array();
            // $new_point = intval($r['point']) + intval($point);
            // $new_point = $new_point < 0 ? 0 : $new_point;
            // $CI->db->where("id = '{$u}'")->update('User', array('point'=>$new_point));
        // }
	// }
	// $rs = $CI->db->where_in('id', $uid)->get('User')->first_row('array');
	// $new_point = intval($rs['point']) + $point;
	// $new_point = $new_point <= 0 ? 0 : $new_point;
	// //更新积分
	// $CI->db->where_in('id', $uid)->update('User', array('point'=>$new_point));

	// 2012.07.18最新的
	$b = true;
	if($uid) {
        $point_case_conf = $CI->config->item('point_case');
        $point_id = $point_case_conf[$key];
        $point_case = get_data('point_case');
        $case = $point_case[$point_id];
        $point = intval(($score || $score === 0)?$score:$case['point']); // 积分
        if($point) {
            // 有规则才去做处理
            if(!is_array($uid)) {
                $uid = array($uid);
            }
            
            // 判断管理员的分数是否够用
            if($point > 0) {
                $total_point = count($uid) * $point;
                $admin = $CI->db->get_where($CI->_tables['morrisadmin'], 
                        array('id' => $CI->auth['id']))->row_array();
                // 先看今天管理员是否领取过积分
                $today = dt(TIMESTAMP, 'Y-m-d');
                if(strtotime(dt(TIMESTAMP, 'Y-m-d')) > strtotime($admin['autoGiveDate'])) {
                    // 没有领取过
                    $admin['todayPoint'] = 0;
                    $admin['autoGiveDate'] = $today;
                }
                if(($admin['todayPoint'] + $total_point) > $admin['everydayPoint']) {
                    // 大于了管理员能使用的积分
                    $CI->error('您的每日积分不够用了，不能给用户发送积分了，请联系管理员');
                }
                // 计算这次用户的积分
                $admin['todayPoint'] += $total_point;
                $admin['totalPoint'] += $total_point;
                // 去更管理员的积分配发记录
                $admin_id = $admin['id'];
                unset($admin['id']);
                $CI->db->where('id', $admin_id)->update($CI->_tables['morrisadmin'], $admin);
            }
            
            $data = array();
            foreach($uid as $u) {
                // 更新用户的积分
                if($point > 0) {
                    // 正数
                    $b &= $CI->db->where("id = '{$u}'")
                                ->set('point', 'point+' . $point, false);
                    if('digest' == $key) {
                        $CI->db->set('exp', 'exp+' . $point, false);
                    }
                    $CI->db->update('User');
                } else {
                    // 负数，那么需要去判断是否用户积分够扣
                    $r = $CI->db->select('point')->where("id = '{$u}'")->get('User')->row_array();
                    $new_point = $r['point'] + $point;
                    $new_point = $new_point < 0 ? 0 : $new_point;
                    $d = array('point'=>$new_point);
                    
                    $b &= $CI->db->where("id = '{$u}'")->update('User', $d);
                }
                
                // 
                
                $data[] = array(
                    'uid' => $u,
                    'pointCaseId' => $point_id,
                    'point' => $point,
                    'operatorId' => $CI->auth['id'],
                    'remark' => encode_json(array(
                            'remark' => $remark,
                            'item_id' => $item_id,
                            'action' => $key,
                            'item_type' => $item_type
                            ))
                );
                
                if($key == 'digest') {
                    $exp_data[] = array(
                            'uid' => $u,
                            'expCaseId' => 48,
                            'exp' => abs($point)
                            );
                }
            }            
            // 更新用户的积分日志
            $b &= $CI->db->insert_batch('UserPointLog', $data);
            if($key == 'digest' && $exp_data) {
                // 需要去更新用户的经验
                $CI->db->insert_batch('UserExpLog', $exp_data);
            }
        }
	}
    return $b;
}

/**
 * 将指定的字符串中的自定义tag转换成实际值
 * Create by 2012-4-5
 * @author liuw
 * @param string $string
 * @param array $attrs
 * @return string
 */
function replaceAttr($string, $attrs){
	$keys = $values = array();
	foreach($attrs as $key=>$value){
		$keys[] = '/@{'.$key.'}/';
		$values[] = $value;
	}
	return preg_replace($keys, $values, $string);
}

/**
 * 发布回复和私信的通用方法
 * Create by 2012-4-13
 * @author liuw
 * @param int $sender，发送者id
 * @param array $receiver，接收者id或用户名
 * @param string $content，内容
 * @param int $reply_type，发布的是什么，0表示回复post，1表示回复的回复，2表示私信
 * @param mixed $item_id，可以为空，需要与$reply_type联合使用。$reply_type为0时，$item_id必须有值，且值必须是Post中一条纪录的id；$reply_type为1时，$item_id必须有值，且值必须是PostReply中一条纪录的id；$reply_type为2时，忽略$item_id。
 */
function reply($sender, $receiver, $content, $reply_type=0, $item_id=null){
	global $CI;
	$reply_mine = true;
	$err_code = 0;
	$message = 0;
	$postId = FALSE;
	$postType = FALSE;
	if(isset($receiver) && !empty($receiver)){
		//检查并获得接收者id
		$rids = $unames = array();
		foreach($receiver as $key=>$val){
			if(is_numeric($val)){
				$rids[] = $val;
			}else{
				$unames[] = $val;
			}
		}
		//通过用户名查询uid
		if(!empty($unames)){
			$query = $CI->db->where_in('username', $unames)->get('User');
			foreach($query->result_array() as $row){
				$rids[] = $row['id'];
			}
		}
	}
	
	if($reply_type == 1){//回复的回复
		if(empty($item_id)||!$item_id){
			$err_code = 300;
			$message = $CI->lang->line('reply_item_not_empty');
		}else{
			//获取回复的内容和所有者昵称
			$rs = $CI->db->from('PostReply')
			             ->join('User','User.id=PostReply.uid')
			             ->join('Post', 'Post.id=PostReply.postId')
			             ->select('PostReply.uid,PostReply.content,PostReply.postId,
			                       Post.uid as puid,Post.type,User.nickname,User.username')
			             ->where('PostReply.id',$item_id,FALSE)
			             ->limit(1)->get()->first_row('array');
			//$content = '回复['.(!empty($rs['nickname'])?$rs['nickname']:$rs['username']).']'.$content;
			$postId = $rs['postId'];
			$postType = $rs['type'];
			$data = array('uid'=>$sender, 'content'=>$content, 'postId'=>$rs['postId'], 'replyUid'=>$rs['uid'], 'replyId'=>$item_id);
			$CI->db->insert('PostReply', $data);
			$insert_id = $CI->db->insert_id();
			$err_code = isset($insert_id)&&!empty($insert_id)&&$insert_id?200:300;//数据保存成功，返回200。否则返回300
			//发系统消息
			if($err_code == 200){
				$r = $CI->db->select('Post.type, Place.placename')->from('Post')->join('Place','Place.id=Post.placeId', 'inner')->where('Post.id',$rs['postId'])->get()->first_row('array');
				$msg_key = 'sm_reply_prefix';
				$item_id = $rs['postId'];
				$item_type = $r['type'];
				$replace = array('place', $r['placename']);
				$to = $rs['uid'];
				//发送
				//send_message($msg_key, $to, $item_id, $item_type, TRUE, $replace);
				//纪录PostReplyMessage
				$insert = array();
				if($sender != $rs['uid']){
					$insert[] = array('uid'=>$rs['uid'], 'replyId'=>$insert_id);
				}
				// if($sender != $rs['puid']){
					// $insert[] = array('uid'=>$rs['puid'],'replyId'=>$insert_id);
					// $reply_mine = false;
				// }
				// if(!empty($insert))
					// $CI->db->insert_batch('PostReplyMessage', $insert);	
			    if($insert) {
			        $reply_ids = array();
                    foreach($insert as $row) {
                        // 一条一条的加入
                        $CI->db->insert('PostReplyMessage', $row);
                        $reply_ids[] = $CI->db->insert_id();
                    }
                    $CI->lang->load('api');
                    $api_interface = $CI->lang->line('api_msg_reply');
                    // 根据ID推送消息
                    foreach($reply_ids as $reply_msg_id) {
                        send_api_interface($api_interface, 'POST', array('reply_msg_id' => $reply_msg_id));
                    }
			    }
			}
			$message = $err_code == 200 ? $CI->lang->line('do_success') : $CI->lang->line('do_error');
		}
	}elseif($reply_type == 2){//发私信
		$datas = array();
		$data = array(
			'sender'=>$sender,
			'content'=>$content
		);
		$sms = array();
		foreach($receiver as $rid){
			$data['receiver'] = $rid;
			$datas[] = $data;
			//统计对话数
			$pm_size = $CI->db->where('sender', $sender)->where('receiver', $rid)->count_all_results('UserPrivateMessage');
			$sms[$rid] = $pm_size;
		}
		// $CI->db->insert_batch('UserPrivateMessage', $datas);
		// $err_code = 200;
		// $message = $CI->lang->line('do_success');
		// //系统消息
		// $CI->lang->load('premessage','chinese');
		// $message = $CI->lang->line('sm_pm_prefix');
		// $to = $rids;
		// $item_type=0;
		// //内容替换
		// $sender_inf = $CI->db->where('id', $sender)->get('User')->first_row('array');
		// $replace = array('sender'=>!empty($sender_inf['nickname'])?$sneder_inf['nickname']:$sender_inf['username']);
// 		
		// $attr = array('sender'=>$sender);
		// foreach($sms as $receiverId=>$pm_size){
			// if(!$pm_size)
				// $pm_size = "新的";
			// else 
				// $pm_size = $pm_size .'条';
			// $replace['pm_size'] = $pm_size;
			// //替换自定义标签
			// if(!empty($replace)){
				// $tag = $CI->config->item('tag_message');
				// $regex = $contents = array();
				// foreach($replace as $key=>$value){
					// $regex[] = "/".str_replace('tag', $key, $tag)."/";
					// $contents[] = $value;
				// }
				// $message = preg_replace($regex, $contents, $message);
			// }
			// $attr['content'] = $message;
			// $attr['reciever'] = $receiverId;
			// //推送
			// $CI->lang->load('api');
			// $api_int = $CI->lang->line('api_msg_private');
			// send_api_interface($api_int, 'POST', $attr);
		// //	send_message($msg_key, $receiverId, 0, $item_type, true, $replace);私信不发系统消息
		// }
		
        $private_ids = array();
        foreach($datas as $row) {
            // 一条一条的加入
            $CI->db->insert('UserPrivateMessage', $row);
            $private_ids[] = $CI->db->insert_id();
        }
        $CI->lang->load('api');
        $api_interface = $CI->lang->line('api_msg_private');
        // 根据ID推送消息
        foreach($private_ids as $pm_id) {
            send_api_interface($api_interface, 'POST', array('pm_id' => $pm_id));
        }
    }else{//普通回复
		if(empty($item_id)||!$item_id){
			$err_code = 300;
			$message = $CI->lang->line('reply_item_not_empty');
		}else{
			$data = array('uid'=>$sender,'content'=>$content, 'postId'=>$item_id);
			//查询replyUid
			$rs = $CI->db->select('id,uid,type')->where('id', $item_id, FALSE)->get('Post')->first_row('array');
			$postId = $rs['id'];
			$postType = $rs['type'];
			$data['replyUid']=$rs['uid'];
			$CI->db->insert('PostReply', $data);
			$insert_id = $CI->db->insert_id();
			$err_code = isset($insert_id)&&!empty($insert_id)&&$insert_id?200:300;//数据保存成功，返回200。否则返回300
			$message = $err_code == 200 ? $CI->lang->line('do_success') : $CI->lang->line('do_error');
			//纪录PostReplyMessage
			
			if($rs['uid'] != $sender){
				$insert = array('uid'=>$data['replyUid'], 'replyId'=>$insert_id);
				$CI->db->insert('PostReplyMessage', $insert);
                $reply_pm_id = $CI->db->insert_id();
                // 发送消息
                $CI->lang->load('api');
                send_api_interface($CI->lang->line('api_msg_reply'), 'POST', array('reply_msg_id' => $reply_pm_id));
			}
			$reply_mine = $rs['uid'] == $sender;
		}
	}
	//回复+1
	if($err_code == 200 && $reply_type != 2){
		$CI->db->query('UPDATE User SET replyCount=replyCount+1 WHERE id=\'?\'', array($sender));
		if($postId){
		    // 存在postId
			$CI->db->query('UPDATE Post SET replyCount=replyCount+1 WHERE id=?', array($postId));
            
            // 更新feed表
            $CI->db->query('UPDATE UserFeed SET replyCount=replyCount+1 WHERE itemId=? AND itemType=?', array($postId, $postType));
			// if($postType !== FALSE)
				// $CI->db->query('UPDATE UserFeed SET replyCount=replyCount+1 WHERE itemId=? AND itemType=?', array($postId, $postsType));
		}
	}
	
	return compact('err_code','message');
}
   
 // File end