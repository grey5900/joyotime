<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/*
 * 一些应用配置
 *
 * Author: piglet chenglin.zhu@gmail.com
 * Created on 2012-5-2
 */

// 模板的配置
$config['template'] = array(
        'template_dir' => FCPATH . 'template/',
        'compiled_dir' => FCPATH . 'data/compiled/',
        'pre_str' => "<?php if (!defined('BASEPATH')) exit('No direct script access allowed');?>"
);
    
//用户帐户cookie名称
$config['auth_cookie'] = 'auth';
// 验证码session名称
$config['sess_captcha'] = 'verify_code';
//默认的cookie生命周期
// $config['cookie_expire'] = 0;
//post已审核状态标志
$config['post_exmine'] = 1;
$config['post_close'] = 2;
//POST类型
$config['post_checkin'] = 1;//签到
$config['post_tip'] = 2;//点评
$config['post_photo'] = 3;//照片
//需要检查登录状态的函数
$config['check_signins'] = array(
	'common/reply',//回复
	'common/favorite',//收藏
	'common/praise',//赞
	'common/share',//分享
	'user/do_follow',//关注or取消关注
);
//允许回复的type
$config['assert_type'] = array(
    'place' => 1,//地点
    'tip' => 2,//点评
    'image' => 3,//照片
    'reply' => 4,//回复
);
$config['type_name'] = array(
    1 => '地点',//地点
    2 => '点评',//点评
    3 => '照片',//照片
    4 => '回复',//回复
);
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

//poi星级评分参数 
$config['poi_level_top'] = 50;
$config['poi_level_C'] = 7;
//poi分类缓存文件
// $config['poi_category_cache'] = FCPATH . './data/inc/poi_cat.inc.php';
//RSA密钥文件地址
$config['rsa_private_key_path'] = FCPATH . './forbid/rsa_private_key.pem';
$config['rsa_public_key_path'] = FCPATH.'./forbid/rsa_public_key.pem';
// $config['rsa_key_path'] = FCPATH.'./forbid/rsa_private_key_pkcs8.txt';
//可同步的第三方平台
$config['sync_platforms'] = array(
	array(
		'name'=>'新浪微博',
		'icon'=>'img/lg_sinaweibo.gif',
		'table'=>'UserSinaBindInfo'
	),
	array(
		'name'=>'腾讯微博',
		'icon'=>'img/lg_qqweibo.gif',
		'table'=>'UserTencentBindInfo',
	),
);
//头像缓存配置
$config['image_cfg'] = array(
	'upload_dir'=>'images',
	'upload_view'=>'/data/images/',
	'upload_path'=>FCPATH.'./data/images/',
	'large_image_prefix'=>'resize_',
	'thumb_image_prefix'=>'thumbnail_',
	'max_file'=>1,//最大文件大小，1mb
	'thumb_width'=>640,//缩略图宽度
	'thumb_height'=>160,//缩略图高度
	'allowed_image_types'=>array('image/pjpeg'=>"jpg",'image/jpeg'=>"jpg",'image/jpg'=>"jpg",'image/png'=>"png",'image/x-png'=>"png",'image/gif'=>"gif"),
	'allowed_image_ext'=>array('jpg','png','gif')
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

// 包含缓存类型的保存路径
$config['inc_conf'] = array('dir' => FCPATH . 'data/inc/');

$config['recommend_conf'] = array('type' => 'file', 'expire' => 1800);

$config['user_desc_conf'] = array('cache_type' => 'file', 'cache_expire' => 3000);
// 推荐数据的保存路径
$config['file_conf'] = array(
                          'cacheDir' => FCPATH . 'data/cache/'
                        );

// 缓存配置
$config['cache'] = array(
        'category' => array(
                'name' => '分类缓存',
                'type' => 'inc'
        ),
);

// 和机甲通信的一个加密key
$config['sign_key'] = '4fdf1ad4403d2';

// auth加密的KEY
$config['authcode_key'] = '%#!!#!@inchengdu';

// 头像
$config['image_head'] = array('odp', 'hudp', 'hhdp', 'hmdp', 'udpl', 'hdpl', 'mdpl');
// 用户发布
$config['image_user'] = array('odp', 'udp', 'hdp', 'mdp', 'thudp', 'thhdp', 'thmdp', 'thweb');

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