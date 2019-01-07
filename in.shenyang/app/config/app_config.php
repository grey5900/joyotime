<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/*
 * 应用配置
 *
 * Author: piglet chenglin.zhu@gmail.com
 * Created on 2012-2-8
 */

// 验证session名称
$config['sess_auth'] = 'auth';

// 验证码session名称
$config['sess_captcha'] = 'valicode';

// 模板的配置
$config['template'] = array(
        'template_dir' => FCPATH . 'static/template/',
        'compiled_dir' => FCPATH . 'data/compiled/',
        'pre_str' => "<?php if (!defined('BASEPATH')) exit('No direct script access allowed');?>"
);

// 超级管理员roleid号
$config['superadmin'] = 1;

// 包含缓存类型的保存路径
$config['inc_conf'] = array('dir' => FCPATH . 'data/inc/');


// 缓存配置
$config['cache'] = array(
        'role' => array(
                'name' => '角色缓存',
                'type' => 'inc'
        ),
        'rights_uri' => array(
                'name' => '权限URI',
                'type' => 'inc'
        ),
        'rights_id' => array(
                'name' => '权限ID',
                'type' => 'inc'
        ),
        'menu' => array(
                'name' => '菜单缓存',
                'type' => 'inc'
        ),
        'act_log' => array(
                'name' => '日志操作',
                'type' => 'inc'
        ),
        'common_setting' => array(
                'name' => '基础设置',
                'type' => 'inc'
        ),
        'image_setting' => array(
                'name' => '图片设置',
                'type' => 'inc'
        ),
        'taboo' => array(
                'name' => '敏感词缓存',
                'type' => 'inc'
        ),
        'taboo_user' => array(
                'name' => '用户名敏感词',
                'type' => 'memcached'
        ),
        'taboo_post' => array(
                'name' => '用户post敏感词',
                'type' => 'memcached'
        ),
        'fragment' => array(
                'name' => '碎片缓存',
                'type' => 'inc'
        ),
        'template' => array(
                'name' => '编译模板',
                'type' => 'template'
        ),
);

// 模型模板的路径
$config['mod_temp_path'] = FCPATH . 'static/template/module_template';

//默认的分页条件
$config['page'] = 1;
//默认从第一页开始
$config['size'] = 20;
//默认每页显示20条数据

//Add by Liuw:ugc相关配置
$config['post_comment'] = 2;
//点评的type
$config['post_pic'] = 3;
//图片数据的type
//post数据状态标志
$config['post_status'] = array(
        'examine' => 1, //已审
        'taboo' => 2, //敏感
        'status' => 3, //屏蔽
        'default' => 0//未审
);
//私信内容自定义标签
$config['tag_message'] = '@{tag}';

//系统消息附件类型
$config['msg_pm'] = 0;
//私信
$config['msg_place'] = 1;
//地点
$config['msg_comment'] = 2;
//点评
$config['msg_pic'] = 3;
//图片
$config['msg_user'] = 4;
//用户
$config['msg_event'] = 5;
//活动
$config['msg_prefer'] = 6;
//优惠

//默认头像配置
$config['default_avatar'] = '';

//敏感词类型
$config['taboo_types'] = array(
        'user' => '用户名/昵称',
        'post' => 'UGC内容/个性签名',
        'user,post' => '用户名/昵称&UGC内容/个性签名',
);
//敏感词批量导入文件属性
$config['taboo_file'] = array(
        'upload_path' => './data/upload/',
        'allowed_types' => 'txt',
        'max_size' => 2048,
);
//批量添加敏感词的分隔符
$config['taboo_split'] = "\n";

// POI状态
$config['poi_status'] = array(
        '0' => '正常',
        '1' => '隐藏',
        '2' => '删除'
);

// 地点处理状态
$config['poi_handle'] = array(
        '0' => '未处理',
        '1' => '已处理'
);

// 地点报错状态
$config['poi_report'] = array(
        '0' => '其他',
        '1' => '地点不存在',
        '2' => '地点重复',
        '3' => '地点信息有错',
        '4' => '地点位置有错',
);
//客户端类型
$config['client_type'] = array(
        0 => 'iphone',
        1 => 'android',
        2 => 'windows phone',
        3 => 'android 内测版'
);
//背景图规格列表
$config['background_image'] = array(
        0 => array(
                'name' => 'ios',
                'sizes' => array(
                        'udp' => '920*920',
                        'mdp' => '420*460'
                )
        ), //iphone
        1 => array(
                'name' => 'android',
                'sizes' => array(
                        'udp' => '640*1016',
                        'hdp' => '480*762',
                        'mdp' => '320*508'
                )
        ), //android
        2 => array(
                'name' => 'windows_phone',
                'sizes' => array()
        ), //windows phone
);
//banner图片规格列表
$config['banner_image'] = array(
        'udp' => '640*550', //大图
//         'hdp' => '480*75', //中图
//         'mdp' => '320*50', //小图
);
//banner图片排序值增幅
$config['banner_top_avegare'] = 10;

$config['gender_state'] = array(
        '0' => '女',
        '1' => '男'
);

// 字段前缀
$config['field_prefix'] = 'v-';
//ugc的状态
$config['ugc_success'] = 1;
$config['ugc_default'] = 0;
$config['ugc_close'] = 3;
$config['ugc_taboo'] = 2;

// 配置碎片的字段
$config['fragment_field'] = array(
    'title' => '标题',
    'title_link' => '标题连接',
    'category' => '分类',
    'category_link' => '分类连接',
    'image' => '图片',
    'image_link' => '图片链接',
    'author' => '用户',
    'author_link' => '用户连接',
    'author_avatar' => '用户头像',
    'intro' => '简介'
);

// 配置碎片默认选中的字段
$config['fragment_default_field'] = array(
    'user' => array('author'=>'用户', 'author_avatar'=>'用户头像', 'author_link'=>'用户连接'),
    'clientUser' => array('author'=>'用户', 'author_avatar'=>'用户头像', 'author_link'=>'用户连接'),
    'poi' => array('title'=>'地点名', 'title_link'=>'地点连接', 'level'=>'评分'),
    'tip' => array('intro'=>'点评内容', 'title_link'=>'点评连接', 'category'=>'地点名', 'category_link'=>'地点连接', 'author'=>'用户', 'author_avatar'=>'用户头像', 'author_link'=>'用户连接')
);

$config['fragment_category'] = array(
    'user' => '用户',
    'clientUser' => '客户端用户',
    'poi' => 'POI',
    'tip' => '点评',
);


// 付款方式
$config['pay_way'] = array(
    '0' => '支付宝',
    '1' => '银联'
);
//0=正常，1=已作废,2=未发货
$config['order_status'] = array(
    '0' => '正常',
    '1' => '作废',
    '2' => '失败'
);

// 存放生成的HTML的路径
$config['html_path'] = FCPATH . 'html/';

// 和网站通信的一个加密key
$config['sign_key'] = '4fdf1ad4403d2';

//RSA密钥文件地址
$config['rsa_private_key_path'] = FCPATH . './forbid/rsa_private_key.pem';
$config['rsa_public_key_path'] = FCPATH.'./forbid/rsa_public_key.pem';

// 1              地点 ITEM_TYPE_PLACE                                 inplace://10001
// 2              点评 ITEM_TYPE_TIP                                      intip://10001
// 3              照片 ITEM_TYPE_PHOTO                                inphoto://10001
// 4              用户 ITEM_TYPE_USER                                   inuser://10001
// 5              活动 ITEM_TYPE_EVENT                                 inevent://10001
// 6              优惠 ITEM_TYPE_PREFER                                inprefer://10001                                   
// 7              勋章 ITEM_TYPE_BADGE                                 inbadge://10001                 
// 8              签到 ITEM_TYPE_CHECKIN                              incheckin://10001                   
// 9              私信 ITEM_TYPE_PRIVATE_MESSAGE                inpm://10001                                  
// 10             系统消息 ITEM_TYPE_SYSTEM_MESSAGE         insm://10001                                       
// 11             回复 ITEM_TYPE_REPLY                                  inreply://10001                
// 12             团购 ITEM_TYPE_GROUPON                            ingroupon://10001                      
// 13             电影票 ITEM_TYPE_FILMTICKET                       infilm://10001                           
// 14             网页 ITEM_TYPE_WEB                                      http://in.jin95.com/user/10001// 连接类型
// 15             会员卡 ITEM_TYPE_MCARD                                      inmcard://in.jin95.com/10001// 连接类型
// 16             订单 ITEM_TYPE_ORDER                                inorder://10001
// 17             商家平台发送的推送消息
// 18             YY
// 19             POST(包含itemType 2,3,8,18)                      inpost://10001
// 20             地点册                                          inpc://10001
// 21             道具                                             inprops://10001
// 22             道具消息                                         inpropsmsg://10001
// 23             商品(Product)                                    inproduct://10001
// 24             积分票                                         inpt://10001
// 网址、团购详情页、电影票详情页、POI档案页、用户档案页、点评、图片、签到

// $config['link_type'] = array(
//     '14' => array('key'=>'http','value'=>'网址'),
//     '12' => array('key'=>'ingroupon','value'=>'团购'),
//     '13' => array('key'=>'infilmticket','value'=>'电影票'),
//     '6' => array('key'=>'inprefer','value'=>'优惠'),
//     '1' => array('key'=>'inplace','value'=>'POI'),
//     '4' => array('key'=>'inuser','value'=>'用户'),
//     '2' => array('key'=>'intip','value'=>'点评'),
//     '3' => array('key'=>'inphoto','value'=>'图片'),
//     '8' => array('key'=>'incheckin','value'=>'签到'),
//     '15' => array('key'=>'inmcard','value'=>'会员卡')
// );

// 无链接   网址    商品   道具   地点册     POI     用户    POST  其他      话题
$config['link_type'] = array(
        '14' => array('key'=>'http','value'=>'网址'),
        '23' => array('key'=>'inproduct','value'=>'商品'),
        '21' => array('key'=>'inprops','value'=>'道具'),
        '20' => array('key'=>'inpc','value'=>'地点册'),
        '1' => array('key'=>'inplace','value'=>'POI'),
        '4' => array('key'=>'inuser','value'=>'用户'),
        '19' => array('key'=>'inpost','value'=>'POST'),
        '666' => array('key' => '', 'value' => '其他'),
        '26' => array('key' => 'intopic', 'value' => '话题'),
);

// $config['link_type'] = array(
    // '1' => array('key'=>'inplace','value'=>'POI档案页'),
    // '2' => array('key'=>'intip','value'=>'点评'),
    // '3' => array('key'=>'inphoto','value'=>'图片'),
    // '4' => array('key'=>'inuser','value'=>'用户页面'),
    // '5' => array('key'=>'inevent','value'=>'活动'),
    // '6' => array('key'=>'inprefer','value'=>'优惠'),
    // '7' => array('key'=>'inbadge','value'=>'勋章'),
    // '8' => array('key'=>'incheckin','value'=>'签到'),
    // '9' => array('key'=>'inpm','value'=>'私信'),
    // '10' => array('key'=>'insm','value'=>'系统消息'),
    // '11' => array('key'=>'inreply','value'=>'回复'),
    // '12' => array('key'=>'ingroupon','value'=>'团购详情'),
    // '13' => array('key'=>'infilm','value'=>'电影票详情'),
    // '14' => array('key'=>'http','value'=>'网址')
// );

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
    '24' => array('key'=>'inpt','value'=>'积分票'),
    '25' => array('key'=>'ingm','value'=>'全局消息'),
    '26' => array('key'=>'intopic','value'=>'话题'),
);

$config['groupon_source'] = array(
    '章鱼团' => 'tg_chengdu_cn',
    '商报买购网' => 'mygo_chengdu_cn'
);

// 地点创建的类型
$config['poi_creator_type'] = array(
    '0' => '后台',
    '1' => '普通用户',
    '2' => '商户'
);

// 积分操作的对应ID号
$config['point_case'] = array(
    'banned_tip' => 17,
    'banned_photo' => 18,
    'banned_reply' => 19,
    'banned_user' => 20,
    'poi_report' => 21,
    'poi_create' => 22,
    'user_feedback' => 23,
    'manual_point' => 24,
    'digest' => 48,
    'order_refund' => 55,
    'manual_point_minus' => 58
);

$config['ipdatafile_tiny'] = FCPATH . './forbid/ipdata/tinyipdata.dat';
$config['ipdatafile_full'] = FCPATH . './forbid/ipdata/qqwry.dat';

$config['font_path'] = FCPATH . './forbid/fonts/';
$config['font_name'] = 'YAHEI_MONO.TTF';

$config['deal_analysis_item'] = array(
    'orderCount' => '订单总数',
    'paidCount' => '付款订单数',
    'paidRate' => '付款率',
    'saleCount' => '售出份数',
    'dealAmount' => '交易金额'    
);

$config['user_analysis_item'] = array(
    'connectCount' => '启动次数',
    'newCount' => '新增终端',
    'newUserCount' => '新增注册用户',
    'activeCount7' => '7日活跃终端',
    'activeRate7' => '7日活跃率',
    'clientCount' => '累计终端',
    'userCount' => '累计注册用户'
);

// 不用权限判断的页面
$config['uncheck_rights'] = array('/main/login', '/main/captcha', '/main/place', '/main/goods', '/api/private_api');

// 连接团购接口配置
$config['source_type'] = array('tg_chengdu_cn' => 1, 'mygo_chengdu_cn' => 2);

$config['source_type_name'] = array(
    '1' => '章鱼团',
    '2' => '商报买购网'
);

// 配送方式
$config['ship_type'] = array(
    '0' => '电子券',
    '1' => '自提',
    '2' => '配送'
);

// 0电子券，这里使用的status来判断的状态，本身shipType和shipStatus对于电子券来说没有用
$config['ship_status'] = array(
    '0' => array(
        '0' => '完成(已发货)',
        '2' => '未发货'
    ),
    '1' => array(
        '0' => '备货中',
        '1' => '等待自提',
        '2' => '完成(已提货)'
    ),
    '2' => array(
        '0' => '备货中',
        '1' => '完成(已发货)'
    )
);

// 多图控件的配置
$config['rich_image'] = array(
    array(
        'key' => 'image',
        'name' => '图片',
        'type' => 'file'
    ),
    array(
        'key' => 'title',
        'name' => '标题',
        'type' => 'input'
    ),
    array(
        'key' => 'detail',
        'name' => '详情',
        'type' => 'textarea'
    )
);
$config['fragment_tmp_folder'] = 'static/template/fragment/temp/';
//FTP配置
/*$config['ftp_serv'] = array(
	1=>array(
		'server' => 'http://pic1.in.jin95.com',//FTP地址
		'user' => 'user', //FTP账号
		'password' => 'password',//FTP登录密码
		'port' => 23,//FTP上传端口
		'debug' => true//上线时改成FALSE
	),
	2=>array(
		'server' => 'http://pic1.in.jin95.com',
		'user' => 'user',
		'password' => 'password',
		'port' => 23,//FTP上传端口
		'debug' => true//上线时改成FALSE
	),
	3=>array(
		'server' => 'http://pic1.in.jin95.com',
		'user' => 'user',
		'password' => 'password',
		'port' => 23,//FTP上传端口
		'debug' => true//上线时改成FALSE
	),
);*/

// 0：点击报名\r\n            1：填写报名表\r\n            2：点评报名\r\n            3：传图报名
$config['event_apply_type'] = array(
            '0' => '点击报名',
            '1' => '填写报名表',
            '2' => '点评报名',
            '3' => '传图报名'
        );

$config['sqlite_file'] = FCPATH . './forbid/task.sqlite';


// 数据枚举\r\n0:首页上部banner\r\n1:首页中部banner\r\n2:首页底部banner\r\n
// 3:第一部分展示数据\r\n4:首页话题\r\n5:首页活动\r\n6:首页商品\r\n7:首页\r\n\r\n地点\r\n
// 8:首页推荐达人\r\n9:首页推荐新人
$config['home_digest'] = array(
	'home_banner_header' => array('id'=> 0, 'size' => '640x320', 'label' => '标题', 'type' => 'all'),
	'home_banner_middle' => array('id'=> 1, 'size' => '640x320', 'label' => '标题', 'type' => 'all'),
	'home_banner_footer' => array('id'=> 2, 'size' => '640x120', 'label' => '标题', 'type' => 'all'),
	'home_post' => array('id'=> 3, 'size' => '302x218', 'label' => '标题', 'type' => 'all'),
	'home_topic' => array('id'=> 4, 'size' => '200x150', 'label' => '话题', 'type' => array('topic')),
	'home_event' => array('id'=> 5, 'size' => '616x246', 'label' => '活动', 'type' => array('event')),
	'home_product' => array('id'=> 6, 'size' => '302x246', 'label' => '商品', 'type' => array('product')),
	'home_place' => array('id'=> 7, 'size' => '200x140', 'label' => '地点名', 'type' => array('place')),
	'home_user' => array('id'=> 8, 'size' => '80x80', 'label' => '用户名', 'type' => array('user')),
	'home_new_user' => array('id'=> 9, 'size' => '80x80', 'label' => '用户名', 'type' => array('user')),
	'home_banner_dynamic' => array('id'=> 10, 'size' => '640x120', 'label' => '标题', 'type' => 'all')
);

$config['home_link_type'] = array(
	'post' => array('name' => 'POST', 'schema' => 'inpost', 'type' => 19),
	'topic' => array('name' => '话题', 'schema' => 'intopic', 'type' => 26),
	'event' => array('name' => '活动', 'schema' => 'inevent', 'type' => 5),
	'place' => array('name' => '地点', 'schema' => 'inplace', 'type' => 1),
	'product' => array('name' => '商品', 'schema' => 'inproduct', 'type' => 23),
	'user' => array('name' => '用户', 'schema' => 'inuser', 'type' => 4),
	'http' => array('name' => 'HTTP', 'schema' => 'http', 'type' => 14),
	'any' => array('name' => '任意', 'schema' => '', 'type' => 0)
);
