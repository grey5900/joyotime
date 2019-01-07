<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 系统的配置
 */

// 是否配置为fastcgi
$config['fastcgi'] = true;

// 需要返回给客户端的配置信息
$config['setting_keys'] = array('select_option', 'withdraw_tel', 'banner', 'web_link');

// 生成PHP文件的时候的头
define('ALLOWED_HEADER', "<?php if (!defined('BASEPATH')) exit('No direct script access allowed');?>");

define('CHARSET', 'utf-8');

$config['default_house_pic'] = '';

// 默认的翻页条数
$config['default_page_size'] = 20;

// 首页动态信息条数
$config['home_trends_num'] = 5;

// 名字验证的长度
$config['name_check_num'] = 20;

// 验证码长度
$config['verify_code_lenth'] = 6;

// 验证失败次数限制
$config['verify_num_limit'] = 5;

// 0的时候的价格排序
$config['order_price'] = 999999999;

// 获取header中的值
$config['header_keys'] = array(
		'av', 'ov', 't', 'dt', 'dc', 'k', 'token', 'cel', 'dp'
		);

// 报名 团购、优惠、推荐
$config['apply_types'] = array(0, 1, 2);

// 佣金
$config['commision_actions'] = array('获得佣金', '提现');

// 推荐限制
$config['recommend_limit'] = 10;

// 需要去刷新楼盘团购推荐状态的接口
$config['flush_house_status'] = array(
		'/api/house_list',
		'/api/house_same_price',
		'/api/house_detail',
		'/api/apply'
		);

// 权限数据
$config['rights'] = array(
		'/api/apply' => array(
				'method' => 'POST',
				'params' => array(
						'type' => 2
						)
				),
		'/api/my_commision' => array(
				'method' => 'GET'
				),
		'/api/my_recommend' => array(
				'method' => 'GET'
				),
		'/api/update_user' => array(
				'method' => 'POST'
				)
		);

$config['area'] = array (
		1 => '锦江区',
		2 => '青羊区',
		3 => '成华区',
		4 => '金牛区',
		5 => '武侯区',
		6 => '高新区',
		7 => '高新西区',
		8 => '温江',
		9 => '新都',
		10 => '龙泉驿区',
		11 => '双流',
		12 => '郫县',
		13 => '青白江',
		14 => '都江堰',
		15 => '邛崃',
		16 => '崇州',
		17 => '彭州',
		18 => '大邑',
		19 => '新津',
		20 => '蒲江',
		21 => '金堂',
);

$config['price'] = array(
		1 => array(0, 6000),
		2 => array(6001, 8000),
		3 => array(8001, 10000),
		4 => array(10001, 15000),
		5 => array(15000, 999999999)
);

$config['direction'] = array (
		1 => '东',
		2 => '南',
		3 => '西',
		4 => '北',
		5 => '中',
);

$config['loopline'] = array (
		1 => array('一环内', '一环-二环'),
		2 => '二环-三环',
		3 => '三环-绕城',
		4 => '绕城外',
);

// 0：待审核  1：有效推荐  2：失败推荐 3：推荐过期 4：成功成交的推荐
$config['recommend_status'] = array(
		0 => '待审核',
		1 => '有效推荐', 
		2 => '失败推荐',
		3 => '推荐过期',
		4 => '成功成交的推荐'
		);

$config['distance_order'] = 1;

$config['order'] = array (
		0 => 'a.has_group DESC, group_valid DESC, distance ASC, order_price ASC',
		1 => 'distance ASC, a.has_group DESC, group_valid DESC, order_price ASC',
		2 => 'order_price ASC, a.has_group DESC, group_valid DESC, distance ASC',
		3 => 'a.price DESC, a.has_group DESC, group_valid DESC, distance ASC'
);

$config['detail'] = array(
		'v-avgprice', 'v-ownarea', 'v-biz', 'v-towards', 'v-saletele', 'v-dongtai'
);

$config['album'] = array(
		'v-effectpic', 'v-homepic', 'v-realpic'
);

$config['detail_more'] = array(
		'v-develop', 'v-housetype', 'v-wuye', 'v-managefee', 'v-decorate', 'v-huxingmianji',
		'v-xiangmutese', 'v-buyneed', 'v-indoor', 'v-capacity', 'v-green', 'v-starttime',
		'v-staytime', 'v-area', 'v-buildarea', 'v-hushu', 'v-nianxian', 'v-diantihushu',
		'v-louchen', 'v-ditie', 'v-chewei', 'v-licence', 'v-introduce', 'v-around'
);

