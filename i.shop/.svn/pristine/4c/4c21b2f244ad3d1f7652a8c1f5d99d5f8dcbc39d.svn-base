<?php
/**
 * 商家统计配置
 * Create by 2012-11-5
 * @author liuweijava
 * @copyright Copyright(c) 2012- joyotime
 */
  
 // Define and include
if ( ! defined('BASEPATH')) exit('No direct script access allowed');   
   
 // Code

$config['charts'] = array(
	
/*
 * [统计函数]=>([显示标题],[统计项([显示名称],[图标样式名称])])
 */
	'stat_brand' => array(//品牌统计
		'open'=>false,
		'title'=>'品牌统计',
		'items'=>array(//统计项
			1=>array('item'=>'店铺浏览次数','chart'=>'pie'),
			2=>array('item'=>'店铺访问人数','chart'=>'pie'),
		),	
	),
	'stat_member' => array(//会员统计
		'open'=>true,
		'title'=>'会员统计',
		'items'=>array(
			1=>array('item'=>'新会员统计','chart'=>'column'/*纵向到柱图*/),
			2=>array('item'=>'会员增长趋势','chart'=>'line'),
		),
		'active'=>1,
	),

);
   
 // File end