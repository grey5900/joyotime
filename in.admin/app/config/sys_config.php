<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/*
 * 需要修改的
 * 
 * Author: piglet chenglin.zhu@gmail.com
 * Created on 2012-6-18
 */

// 配置memcached
$config['memcached_conf'] = array( array(
            '127.0.0.1',
            22222
    ));

//是否连接api，默认不连接，生产环境修改
$config['api_connect'] = TRUE;
//API访问地址，生产环境需要更换
$config['api_domain'] = 'http://api-a.out.chengdu.cn/true';
$config['tag_api_domain'] = 'http://118.26.202.215:89/';
$config['tag_for_post_head'] = 'indigo';

// 上传图片接口地址
$config['upload_image_api'] = array(
    'transfer_image' => 'http://xr87-a.out.chengdu.cn/image/transfer_image',
    'sava_image' => 'http://xr87-a.out.chengdu.cn/image/save_image'
);
// 网站地址
//$config['web_site'] = 'http://in-a.out.chengdu.cn';
$config['in_host'] = 'joyotime.com';
$config['web_site'] = 'http://in.' . $config['in_host'];
// 渠道商地址
$config['channel_site'] = 'http://qd-a.out.chengdu.cn';
// 商家后台地址
$config['merchant_site'] = 'http://pt-a.out.chengdu.cn';
// 团房
$config['house_site'] = 'http://tuanfang.joyotime.com';

// 网站几种地址配置
$config['web_url'] = array(
    'user' => $config['web_site'] . '/user/%s', // 用户
    'clientUser' => 'user://%s', // 客户端用户
    'poi' => $config['web_site'] . '/place/%s', // 地点
    'tip' => $config['web_site'] . '/review/%s', // 点评
	'cms_list' => $config['web_site'] . '/cms_list/%d', //CMS列表
	'cms' => $config['web_site'] . '/cms_detail/%d', //CMS内容
);
//CMS列表页模板目录
$config['cms_list_tmp_folder'] = '/var/www/html/web/template/cms';
$config['cms_content_def_status'] = 1;
// 活动配置的绝对地址，如果在不同服务器需要映射一个本地路径过来，
$config['web_event_config'] = 'static/event/inc_event_config.php';//活动配置文件路径

//图片缓存配置
$config['image_cfg'] = array(
	'upload_dir'=>'upload',
	'upload_view'=>'/data/upload/',
	'upload_path'=>FCPATH . 'data/upload/',
	'large_image_prefix'=>'resize_',
	'thumb_image_prefix'=>'thumbnail_',
	'max_file'=>1,//最大文件大小，1mb
	//'thumb_width'=>640,//缩略图宽度
	//'thumb_height'=>160,//缩略图高度
	'allowed_image_types'=>array('image/pjpeg'=>"jpg",'image/jpeg'=>"jpg",'image/jpg'=>"jpg",'image/png'=>"png",'image/x-png'=>"png",'image/gif'=>"gif"),
	'allowed_image_ext'=>array('jpg','png','gif')
);
//远程图片目录
$config['img_remote'] = array(
	'root' => '/data/image/',//根目录	
	'folder' => array('picture3', 'picture1', 'picture2'),//图片远程文件夹
	//访问域，本地测试用的.上ALPHA及公网时修改
	'domain' => array(
		1=>'http://a-image1.joyotime.com',
		2=>'http://a-image2.joyotime.com',
		3=>'http://a-image3.joyotime.com'
	),
);
//水印配置
$config['water_cfg'] = array(
	'root' => FCPATH . 'data/water/',//水印存放路径
	'dis_x' => 10,//与X轴的距离
	'dis_y' => 5,//与Y轴的距离
	//'alpha' => 80,//透明度
);

// 查询分类
// 类型 10：楼盘 20：地点 30：用户 40：POST数据 50：新闻
$config['search'] = array(
        'type' => array(
            '10' => '楼盘',
            '20' => '地点',
            '30' => '用户',
            '40' => '点评/图片',
            '50' => '新闻'
                ),
        'options' => array(
            'hostname' => '127.0.0.1',
            'port' => 8983,
            'path' => '/solr/collection1',
            'wt' => 'json'
                )
);

$config['ueditor'] = array(
	'attachment_save_path' => '/data/upload/',
	'attachment_domain' => 'http://download.joyotime.com/'
);
