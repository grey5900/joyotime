<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * Reply表操作
 * Author: piglet chenglin.zhu@gmail.com
 * Created on 2012-12-6
 */

class Reply_Model extends MY_Model {
	/**
	 * 
	 * Create by 2012-12-19
	 * @author liuweijava
	 * @param int $id 查询用的ID
	 * @param string $id_type ID的类型，POST=postId，USER=uid，REUSER=replyUid，REPLY=replyId
	 * @param int $size
	 * @param int $offset
	 * @return array
	 */
	public function list_reply($id, $id_type='post', $size=20, $offset=0){
		$where_id = '';
		switch($id_type){
			case 'post':$where_id = 'itemId';break;
			case 'user':$where_id = 'uid';break;
			case 'reuser':$where_id = 'replyTo';break;
			case 'REPLY':$where_id = 'replyId';break;
		}
		$sql = array(
			'SELECT r.*, sender.avatar, sender.nickname AS s_nick, sender.username AS s_name, receiver.nickname AS r_nick, receiver.username AS r_name',
			'FROM '.$this->_tables['reply'].' r',
			'INNER JOIN '.$this->_tables['user'].' sender ON sender.id = r.uid and r.itemType=19',
			'LEFT JOIN '.$this->_tables['user'].' receiver ON r.replyTo IS NOT NULL AND receiver.id = r.replyTo',
			'WHERE r.status = 0 AND r.'.$where_id.' = \''.$id.'\'',
			'ORDER BY r.createDate DESC',
			'LIMIT '.$offset.', '.$size // status < 2
		);
		//数据
		$list = array();
		$query = $this->db->query(implode(' ', $sql))->result_array();
		foreach($query as $row){
			//SENDER名字
			$sender = $row['s_nick'] ? $row['s_nick'] : $row['s_name'];
			//RECEIVER名字
			$receiver = $row['replyTo'] ? ($row['r_nick'] ? $row['r_nick'] : $row['r_name']) : false;
			//格式化标题
			$title = $receiver ? lang_message('reply_reply_format', array($row['uid'], $sender, $row['replyTo'], $receiver, $row['content'])) : lang_message('reply_post_format', array($row['uid'], $sender, $row['content']));
			//友好的时间格式 
			$createDate = get_date($row['createDate']);
			//SENDER头像
			$s_avatar = image_url($row['avatar'], 'head', 'hmdp');
			$id = $row['id'];
			$reply_type = 4;
			$list[] = array(
				'id' => $row['id'],
				's_uid' => $row['uid'],
				's_name' => $sender,
				'reply_type' => 4,
				's_avatar' => $s_avatar,
				'createDate' => $createDate,
				'content' => $title,
				'itemId' => $row['itemId']
			);
			unset($sender, $receiver, $title, $createDate, $s_avatar, $id, $reply_type);
		}
		unset($query);
		return $list;
	}
	
}