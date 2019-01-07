<?php
/**
 * 缩略图规格
 * Create by 2012-3-14
 * @author liuw
 * @copyright Copyright(c) 2012-2014 Liuw
 */
  
 // Define and include
if ( ! defined('BASEPATH')) exit('No direct script access allowed');   
   
 // Code
$config['thumb'] = array(
	'suffix' => '_thumb',//缩略图文件名标识
	'save_path' => '/data/image/',//图片存放路径
	'jpeg_quality' => 80,//JPEG图片质量，默认80%
	'jpeg_interlace' => TRUE,//JPEG图片是否启动隔行扫描
	'view_uri' => '',//图片访问路径，不带文件名的
	'web' => array(
		'w' => 90,//宽度
		'h' => 90,//高度
		'folder' => 'uimg/#uid#/web/',//缩略图存放目录，#uid#在使用时需要替换
	),//网站缩略图参数
);   
//默认背景色
$config['background_red'] = 0xFF;
$config['background_green'] = 0xFF;
$config['background_blue'] = 0xFF;
$config['png_alpha'] = 0;//png图片透明度，取值0～127，0表示完全不透明，127表示完全透明
   
 // File end