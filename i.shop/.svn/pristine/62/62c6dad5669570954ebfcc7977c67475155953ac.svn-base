<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/*
 * 应用的一些需要改动的配置 存放需要改动的东西，放上服务器
 * 
 * Author: piglet chenglin.zhu@gmail.com
 * Created on 2012-9-12
 */

// 用于加密的KEY
$config['authcode_key'] = 'www.jiuwo8.com';
 
// cookie的domain
$config['cookie_domain'] = '';
 
// cookie的path
$config['cookie_path'] = '/';
 
//cookie名字的前缀
$config['cookie_pre'] = 'ICD_';
 
// 登录cookie的名字
$config['cookie_name'] = 'auth';

// 登录多少次禁止登录
$config['login_error_num'] = 5;

// 禁止登录时间 秒
$config['login_error_time'] = 900;

// 分页的配置
$config['page_config'] = array(
    'per_page' => 20,
    'first_link' => '首页',
    'last_link' => '尾页',
    'prev_link' => '上一页',
    'next_link' => '下一页',
    'full_tag_open' => '<ul>',
    'full_tag_close' => '</ul>',
    'first_tag_open' => '<li>',
    'first_tag_close' => '</li>',
    'num_tag_open' => '<li>',
    'num_tag_close' => '</li>',
    'cur_tag_open' => '<li><span>',
    'cur_tag_close' => '</span></li>',
    'next_tag_open' => '<li>',
    'next_tag_close' => '</li>',
    'prev_tag_open' => '<li>',
    'prev_tag_close' => '</li>',
    'use_page_numbers' => TRUE,
    'num_links' => 5
);

// 远程操作的加密key
$config['sign_key'] = '4fdf1ad4403d2';

//RSA密钥文件地址
$config['rsa_private_key_path'] = FRAMEWORK_PATH . '/fordid/rsa_private_key.pem';
$config['rsa_public_key_path'] = FRAMEWORK_PATH.'/fordid/rsa_public_key.pem';

// 包含缓存类型的保存路径
$config['inc_conf'] = array('dir' => FRAMEWORK_PATH . '/www/data/inc/');

//图片缓存配置
$config['image_cfg'] = array(
	'upload_dir'=>'img',
	'upload_view'=>'/data/img/',
	'upload_path'=>FRAMEWORK_PATH . '/www/data/img/',
	'large_image_prefix'=>'resize_',
	'thumb_image_prefix'=>'thumbnail_',
	'max_file'=>1,//最大文件大小，1mb
	//'thumb_width'=>640,//缩略图宽度
	//'thumb_height'=>160,//缩略图高度
	'allowed_image_types'=>array('image/pjpeg'=>"jpg",'image/jpeg'=>"jpg",'image/jpg'=>"jpg",'image/png'=>"png",'image/x-png'=>"png",'image/gif'=>"gif"),
	'allowed_image_ext'=>array('jpg','png','gif'),
	'image_size' => 2048000
);
//优惠图片尺寸
$config['prefer_img'] = array('w'=>640, 'h'=>400);
//优惠图标尺寸
$config['prefer_icon'] = array('w'=>190, 'h'=>190);
//会员卡大图尺寸
$config['card_bimg'] = array('w'=>640, 'h'=>420);
//会员卡小图尺寸
$config['card_img'] = array('w'=>260, 'h'=>170);
//扩展信息封面图片尺寸
$config['cover_img'] = array('w'=>120, 'h'=>120);
//会员卡样式3封面图尺寸
$config['cover_img_2'] = array('w'=>520, 'h'=>120);


//控制JS和CSS刷新的SN
$config['css_version'] = '201211271438';