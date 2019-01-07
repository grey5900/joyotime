<?php
/**
 * Create by 2012-12-12
 * @author liuweijava
 * @copyright Copyright(c) 2012-
 */
  
 // Define and include
if ( ! defined('BASEPATH')) exit('No direct script access allowed');   
   
 // Code

//头像缓存配置
$config['image_cfg'] = array(
	'upload_dir'=>'images',
	'upload_view'=>'/images/',
	'upload_path'=>FRAMEWORK_PATH.'./www/images/',
	'large_image_prefix'=>'resize_',
	'thumb_image_prefix'=>'thumbnail_',
	'max_file'=>2,//最大文件大小，1mb
	'thumb_width'=>640,//缩略图宽度
	'thumb_height'=>160,//缩略图高度
	'max_width'=>900,
	'max_height'=>2000,
	'allowed_image_types'=>array('image/pjpeg'=>"jpg",'image/jpeg'=>"jpg",'image/jpg'=>"jpg",'image/png'=>"png",'image/x-png'=>"png",'image/gif'=>"gif"),
	'allowed_image_ext'=>array('jpg','png','gif','jpeg')
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
 // File end