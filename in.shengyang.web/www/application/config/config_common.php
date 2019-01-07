<?php
/**
 * 通用功能的相关配置
 * Create by 2012-12-14
 * @author liuweijava
 * @copyright Copyright(c) 2012-
 */
  
 // Define and include
if ( ! defined('BASEPATH')) exit('No direct script access allowed');   
   
 // Code

//允许回复|赞|收藏的type
$config['assert_type'] = array(
    'place' => 1,//地点
    'tip' => 2,//点评
    'image' => 3,//照片
    'reply' => 4,//回复
);   
$config['itemtype_name'] = array(
	1 => '地点',
	19 => '点评',
);
$config['type_name'] = array(
    1 => '地点',//地点
    2 => '点评',//点评
    3 => '照片',//照片
    4 => '回复',//回复
);
$config['item_type'] = array(
    '1' => array('key'=>'inplace','value'=>'POI'),
    '2' => array('key'=>'intip','value'=>'点评'),
    '3' => array('key'=>'inphoto','value'=>'图片'),
    '4' => array('key'=>'inuser','value'=>'用户'),
    '5' => array('key'=>'inevent','value'=>'活动'),
    '6' => array('key'=>'inprefer','value'=>'优惠'),
    '7' => array('key'=>'inbadge','value'=>'勋章'),
    '8' => array('key'=>'incheckin','value'=>'签到'),
    '9' => array('key'=>'inpm','value'=>'私信'),
    '10' => array('key'=>'insm','value'=>'系统消息'),
    '11' => array('key'=>'inreply','value'=>'回复'),
    '12' => array('key'=>'ingroupon','value'=>'团购'),
    '13' => array('key'=>'infilmticket','value'=>'电影票'),
    '14' => array('key'=>'http','value'=>'网址'),
    '15' => array('key'=>'inmcard','value'=>'会员卡'),
    '16' => array('key'=>'inorder','value'=>'订单'),
    '17' => array('key'=>'','value'=>'商家发送推送消息'),
    '18' => array('key'=>'inpost','value'=>'YY'),
    '19' => array('key'=>'inpost','value'=>'POST'),
    '20' => array('key'=>'inpc','value'=>'地点册'),
    '21' => array('key'=>'inprops','value'=>'道具'),
    '22' => array('key'=>'inpropsmsg','value'=>'道具消息'),
    '23' => array('key'=>'inproduct','value'=>'商品'),
    '24' => array('key'=>'inpt','value'=>'积分票')
);
//地点的POST类型
$config['post_type'] = array(
	'checkin'=>1,//签到
	'tip'=>2,//点评
	'photo'=>3//图片
);
//POST热度算法偏移参数
$config['post_hot_params'] = array(
	//基础权重
	'P'=>array(
		'hit'=>0.2,//点击次数偏移
		'reply'=>1,//回复次数偏移
		'praise'=>1,//被赞次数偏移
		'share'=>2,//分享次数偏移
	),
	//时间权重
	'T'=>array(
		'poor'=>1,//发布时间到当前时间的小时差偏移
	),
	//热度计算权重
	'HOT'=>array(
		'gravity'=>1.5//热度重力偏移
	)
);

// FEED类型
$config['feed_types'] = array(
    '1' => '签到',
    '2' => '点评',
    '3' => '照片',
    '4' => '分享',
    '5' => '勋章',
    '6' => '团购',
    '7' => '电影票'
);
//第三方绑定过期的错误码
$config['bind_err'] = array(4506, 4507);
//用户申请类型
$config['apply_type'] = array(
	1=>'申请品牌商家入驻',
);
// 地点报错状态
$config['place_err'] = array(
        '0' => '其他',
        '1' => '地点不存在',
        '2' => '地点重复',
        '3' => '地点信息有错',
        '4' => '地点位置有错',
);
//FEED类型
$config['feed_type'] = array(
	1=>'feed_checkin',
	11=>'feed_mayor',
	4=>'feed_share_post',
	5=>'feed_badge',
	6=>'feed_share_group',
	7=>'feed_share_ticket'
);
//月份转换
$config['month_CN'] = array(
	1 => '一月',
	2 => '二月',
	3 => '三月',
	4 => '四月',
	5 => '五月',
	6 => '六月',
	7 => '七月',
	8 => '八月',
	9 => '九月',
	10 => '十月',
	11 => '十一月',
	12 => '十二月'
);
//星座转换
$config['star'] = array(
	1=>array('魔羯座'=>19, '水瓶座'=>20),
	2=>array('水瓶座'=>18, '双鱼座'=>19),
	3=>array('双鱼座'=>20, '白羊座'=>21),
	4=>array('白羊座'=>20, '金牛座'=>21),
	5=>array('金牛座'=>20, '双子座'=>21),
	6=>array('双子座'=>21, '巨蟹座'=>22),
	7=>array('巨蟹座'=>22, '狮子座'=>23),
	8=>array('狮子座'=>22, '处女座'=>23),
	9=>array('处女座'=>22, '天秤座'=>23),
	10=>array('天秤座'=>23, '天蝎座'=>24),
	11=>array('天蝎座'=>22, '射手座'=>23),
	12=>array('射手座'=>21, '魔羯座'=>22),	
);
//网站注册的渠道号
$config['web_channel_id'] = 10000;
//移动客户端ID
$config['client_id'] = array(
	'iphone' => 0,
	'android' => 1,
	'wp' => 2
);
 // File end