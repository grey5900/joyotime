<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
  * 3.0的配置
  * @Author: chenglin.zhu@gmail.com
  * @Date: 2013-1-29
  */

// 直接访问v3 api的地址
$config['v3_api'] = 'http://api.chengdu.cn/3.0';

$config['v3_api_domain'] = $config['v3_api'] . '/private_api';
// $config['v3_api_domain'] = 'http://joyotime.gicp.net/private_api';
// 上传图片接口地址v3
$config['v3_upload_image_api'] = array(
        'transfer_image' => 'http://jj.joyotime.com/image_v3/transfer_image',
        'save_image' => 'http://jj.joyotime.com/image_v3/save_image'
);

// $config['v3_api'] = 'http://192.168.2.178';

// 客户端用的token数组
// token_password=fuck_fuck_fuck_gfw_must_die,fuck_key2,fuck_key3
$config['token_password'] = array(
        'fuck_fuck_fuck_gfw_must_die',
        'fuck_key2',
        'fuck_key3'
);

$config['api_request_key'] = array(
        'f68e12bf-5bff-47dd-8f18-7c6be7310359'
        );

// 推荐配置
$config['digest'] = array(
            '18' => array(100, 200, 300, 'input'),
            '19' => array(100, 200, 300, 'input'),
            '20' => array(500, 1000, 'input')
        );

// 图片剪切配置
$config['image_select'] = array(
            'home' => array(320, 231),
			'place' => array(640, 640),
            'avatar' => array(230, 230)
        );

$config['digest_type'] = array(
		1 => 'POI',
		19 => 'POST',
		20 => '地点册',
		23 => 'IN沈阳商品',
		5 => '活动',
        4 => '用户',
		26 => '话题'
	);

// 图片类型配置
$config['image_types'] = array('-1' => 'gif', '-2' => 'jpeg', '-3' => 'png', '1' => 'gif', '2' => 'jpg', '3' => 'png');

// 订单状态  0:正常(未使用) 1:已使用,2:已过期
$config['trade_code_status'] = array('0' => '未使用', '1' => '已使用', '2' => '已过期');
