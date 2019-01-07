<?php
/**
 * Create by 2012-12-12
 * @author liuweijava
 * @copyright Copyright(c) 2012-
 */
  
 // Define and include
if ( ! defined('BASEPATH')) exit('No direct script access allowed');   
   
 // Code
//可同步的第三方平台
$config['sync_platforms'] = array(
	array(
		'name'=>'新浪微博',
		'icon'=>'static/img/lg_sinaweibo.gif'
	),
	array(
		'name'=>'腾讯微博',
		'icon'=>'static/img/lg_qqweibo.gif'
	),
);
   
 // File end