<?php
if (!defined('BASEPATH'))
	exit('No direct script access allowed');

/**
 * 商圈的helper
 */

/**
 * 获取用户的礼物
 * @param array 配置 $conf
 */
function get_user_gift($uid, $conf) {
	$func = $conf['func'];
	unset($conf['func']);
	return $func($uid, $conf);
}

/**
 * 获取华侨城的用户礼物
 * @param  $conf
 */
function hqc_gift($uid, $conf) {
	$CI = &get_instance();
	
	$itemid = $conf['itemid'];
	// 检查今天的时间
	$week = idate_format(0, 'w');
	$device_code = $CI->device_code;
	if (!in_array($week, $conf['award']) || empty($device_code)) {
		// 返回空
		return '';
	}
	
	$date = idate_format(0, 'Y-m-d');
	
	// 去查询今天用户是否已经领取过
	// 查询道具消息表 看今天是否发过消息给用户
// 	$gift = $CI->db->query(sprintf("SELECT * FROM ItemMessage 
// 		WHERE actionId = '%s'
// 		AND createDate BETWEEN '%s' AND '%s'
// 		AND itemId in ('%s') AND reciever = '%s' ORDER BY rand() LIMIT 1",
// 			$conf['actionid'], 
// 			$date . ' 00:00:00', $date . ' 23:59:59',
// 			join('\',\'', $itemid), $uid))->row_array();
	// 检查设备限制表 LimitDevice
	$row = $CI->db->query(sprintf("SELECT * FROM LimitDevice
		WHERE itemType = '1'
		AND createDate BETWEEN '%s' AND '%s'
		AND (uid = '%s' OR deviceCode = '%s') LIMIT 1",
			$date . ' 00:00:00', $date . ' 23:59:59', $uid, $device_code))->row_array();
	
	$b = true;
	if (empty($row)) {
		// 没有那么生成一个
		$index = array_rand($itemid);
		$gift = array(
				'itemId' => $itemid[$index],
				'actionId' => $conf['actionid'],
				'reciever' => $uid,
				'message' => $conf['message'],
				'sender' => $conf['sender']
				);
		$b = $CI->db->insert('ItemMessage', $gift);
		$mid = $CI->db->insert_id();
		$gift['id'] = $mid;
		if ($mid) {
			// 写入限制设备表
			$CI->db->insert('LimitDevice', array(
					'itemType' => 1,
					'itemId' => $mid,
					'uid' => $uid,
					'deviceCode' => $device_code
					));
			// 写入日志
			// ItemAwardLog
			$CI->db->insert('ItemAwardLog', array(
					'actionId' => $conf['actionid'],
					'itemId' => $itemid[$index],
					'uid' => $uid,
					'deviceCode' => $device_code
					));
		}
	} else {
		// 去获取消息
		$gift = $CI->db->query(sprintf("SELECT * FROM ItemMessage WHERE id='%s' 
				LIMIT 1", $row['itemId']))->row_array();
		// 检查状态
		if ($gift['status']) {
			// 如果已经放入包包
			unset($gift);
		}
	}
	
	if ($b && $gift) {
// 		return 'inchengdu://inpropsmessage/' . $gift['id'];
		// 获取ITEM内容
		$gift['item'] = $CI->db->query(sprintf("SELECT name, description, notice, image
			    FROM Item WHERE id='%s'", 
				$gift['itemId']))->row_array();
		$image_conf = get_data('imagesetting');
		$gift['item']['image'] = sprintf('%s/common/odp/%s', 
				$image_conf['image_base_uri'], $gift['item']['image']); 
		return $gift;
	}
	
	return '';
}

