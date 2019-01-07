<?php
if (!defined('BASEPATH'))
	exit('No direct script access allowed');

/**
 * 商圈的配置
 */

$config['district'] = array(
		'1' => array(
				'name' => '华侨城',
				'brandname' => '侨城会',
				'fids' => array(30, 31), // 推荐数据对应的ID
				'pcid' => array(56538), // 商家关联的地点册 商家关联
				'pcid_rob' => array(55940,55939,55896,55941,55937,55938), // 商家管理地点册 抢地主无距离限制 55940,55939,55896,55941,55937,55938
				'brandid' => 888, // 品牌ID号
				'point_case_id' => 62, // 积分的case id
				'game_point_id' => 65, // 华侨城游戏 case id
				'sm_lang' => '恭喜您成为侨城会会员，获得%s积分哦',
				'random_point' => array(1, 5), // 随机分数
				'game_count' => 3, // 可以游戏点击的次数
				'game_card' => range(0, 38), // 卡片图片的下标
				'encrypt_key' => 'hqc_520@', // 权限检查的加密key
				'gift_conf' => array(
					'point' => array(
							'499' => 5000,
							'999' => 2000,
							'2999' => 1000
							), // 奖励分数
					'actionid' => 8, // 动作ID
					'itemid' => array(67), // 道具ID
					'award' => array(-1), // 中奖的配置
					'func' => 'hqc_gift', // 获取的函数
					'sender' => 56, // 礼物发送者
					'message' => '使用后，可在IN沈阳领取原价190元的欢乐谷日场门票一张，一期二期通玩，让你清爽欢乐一夏!' // 发送礼物的时候的消息
				),
				'event_fids' => array(33)
		)
);