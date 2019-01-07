<?php
if (!defined('BASEPATH'))
	exit('No direct script access allowed');
/**
 * 团房的配置
 */

// 应用类型
$config['app_type'] = array(
        0 => 'ios',
        1 => 'android'
);

// 连接
$config['house_link_type'] = array(
		'detail' => '楼盘详情',
		'http' => '网址'
		);

$config['intf_type'] = array(
		'detail' => 'intf://house/detail?id=%s'
		);

$config['house_module_id'] = array(
		28,
		29
		);

// 短信的SOAP配置
$config['sms'] = array(
		'url' => 'http://sm.mms4g.net/uapni/services/SMSEngine?wsdl',
		'user' => 'cdqss01',
		'pass' => 'zytzqm'
);

$config['house_order_status'] = array(
	'0' => '待审核',
	'1' => '推荐成功',
	'2' => '推荐失败',
	'3' => '已失效',
	'4' => '已成交'
);
