<?php
/**
 * 碎片模板
 * Create by 2012-12-5
 * @author liuweijava
 * @copyright Copyright(c) 2012-
 */
  
 // Define and include
if ( ! defined('BASEPATH')) exit('No direct script access allowed');   
   
 // Code
$config['frag_tmp'] = array(
	//'默认样式' => array('is_def'=>1, 'tmp'=>'default'),
	'头条推荐' => array('is_def'=>0, 'tmp'=>'headline'),
	'幻灯' => array('is_def'=>0, 'tmp'=>'focus'),
	'全屏广告' => array('is_def'=>0, 'tmp'=>'fullscreen'),
	'活动列表' => array('is_def'=>0, 'tmp'=>'event_list','exp'=>'key=count|view=参与人数|type=string|required=1'),
	'图片列表' => array('is_def'=>0, 'tmp'=>'photo_list'),
	'商家列表' => array('is_def'=>0, 'tmp'=>'brand_list'),
	'团购列表' => array('is_def'=>0, 'tmp'=>'groupon_list', 'exp'=>'key=price|view=原价￥|type=string|required=1
key=sale_price|view=现价￥|type=string|required=1'),
	'链接列表' => array('is_def'=>0, 'tmp'=>'link_list'),
	'新闻列表' => array('is_def'=>0, 'tmp'=>'news_list','exp'=>'key=catname|view=分类|type=tring
key=catlink|view=分类链接|type=string'),
	'达人列表' => array('is_def'=>0, 'tmp'=>'mvp_list','fregType'=>1),
	'达人点评列表' => array('is_def'=>0, 'tmp'=>'mvp_post_list','fregType'=>1),
	'签到动态' => array('is_def'=>0, 'tmp'=>'feed-list','fregType'=>1),
	'宝宝报时' => array('is_def'=>0, 'tmp'=>'babyclock'),
	'房产微博' => array('is_def'=>0, 'tmp'=>'f_weibo'),
	
	'房产关注楼盘' => array('is_def'=>0, 'tmp'=>'f_focus_list','exp'=>'key=price|view=单价|type=string|required=1'),
	'成都团房' => array('is_def'=>0, 'tmp'=>'f_tf','exp'=>'key=market_price|view=市场均价|type=string|required=1
key=end_time|view=结束时间|type=string|required=1
key=youhui|view=团购优惠|type=string|required=1
key=fanli|view=返利|type=string|required=1
key=max_youhui|view=最高优惠|type=string|required=1
key=now_number|view=当前团购人数|type=string|required=1
key=tenper|view=成交10套优惠|type=string|required=1'),
	'观影头条' => array('is_def'=>0, 'tmp'=>'headline_movie'),
	'教育头条' => array('is_def'=>0, 'tmp'=>'headline_edu'),
    //'活动幻灯' => array('is_def'=>0, 'tmp'=>'event_slider'),
    '半截通栏(旗帜)' => array('is_def'=>0, 'tmp'=>'halfing_ad_left'),
    //'半截通栏右' => array('is_def'=>0, 'tmp'=>'halfing_ad_right'),
	'顶部通栏广告' => array('is_def'=>0, 'tmp'=>'banner_ad'),
	'中部通栏广告1' => array('is_def'=>0, 'tmp'=>'banner_middle_ad'),
	'中部通栏广告2' => array('is_def'=>0, 'tmp'=>'banner_middle_ad2'),
	'中部通栏广告3' => array('is_def'=>0, 'tmp'=>'banner_middle_ad3'),
	'中部通栏广告4' => array('is_def'=>0, 'tmp'=>'banner_middle_ad4'),
	'中部通栏广告5' => array('is_def'=>0, 'tmp'=>'banner_middle_ad5'),
	'中部通栏广告6' => array('is_def'=>0, 'tmp'=>'banner_middle_ad6'),
	'中部通栏广告7' => array('is_def'=>0, 'tmp'=>'banner_middle_ad7'),
	'中部通栏广告8' => array('is_def'=>0, 'tmp'=>'banner_middle_ad8'),
	'中部通栏广告9' => array('is_def'=>0, 'tmp'=>'banner_middle_ad9'),	
	'中部大图广告' => array('is_def'=>0, 'tmp'=>'banner_large_ad'), //作废
	'对联广告_左' => array('is_def'=>0, 'tmp'=>'couplet_left_ad'),
	'对联广告_右' => array('is_def'=>0, 'tmp'=>'couplet_right_ad'),
	'自定义微博秀' => array('is_def'=>0, 'tmp'=>'weibo'),
	'频道大新闻列表' => array('is_def'=>0, 'tmp'=>'big_newslist'),
	//'样式1' => array('is_def'=>0, 'tmp'=>'tmp1'),
	//'样式2' => array('is_def'=>0, 'tmp'=>'tmp2'),
	'教育搜索' => array('is_def'=>0, 'tmp'=>'search_school'),
    '教育频道登录条' => array('is_def'=>0, 'tmp'=>'edu'),
	'背投广告' => array('is_def'=>0,'tmp'=>'back_ad'),
	'页面顶部广告' => array('is_def'=>0,'tmp'=>'back_ad_top'),
	'右下角弹出广告' => array('is_def'=>0,'tmp'=>'pop_ad','exp'=>'key=type|view=类型|type=radio|def_value=img,flash'),
	'图片列表210*95' => array('is_def'=>0,'tmp'=>'image_list'),
	'首页活动推荐' => array('is_def'=>0,'tmp'=>'index3_event','exp'=>'key=itemid|view=活动ID|type=string|required=1'),
	'首页地点册110*110' => array('is_def'=>0,'tmp'=>'index3_placecoll','exp'=>'key=itemid|view=地点册ID|type=string|required=1'),
	'首页积分合作商户_幻灯' => array('is_def'=>0,'tmp'=>'index3_brand_ppt'),
	'首页积分合作商户_新闻' => array('is_def'=>0,'tmp'=>'index3_brand_news','exp'=>'key=catname|view=分类|type=tring
key=catlink|view=分类链接|type=string
key=brandname|view=商家名称|type=string
key=brandlink|view=商家链接|type=string'),
	'微博关注按钮' => array('is_def'=>0,'tmp'=>'weibo_attention'),
	'首页积分合作商户_新增商户' => array('is_def'=>0,'tmp'=>'index3_brand_newbe'),
	'(车)车市立场' => array('is_def'=>0,'tmp'=>'v3_auto_lichang'),
	'(车)车市立场内右侧列表' => array('is_def'=>0,'tmp'=>'v3_auto_lichang_news'),
	'(车)车市立场2' => array('is_def'=>0,'tmp'=>'v3_auto_lichang2'), //为什么有2？因为一个页面只能有同一个碎片，如果要2个，只能再加一个
	'(车)车市立场内右侧列表2' => array('is_def'=>0,'tmp'=>'v3_auto_lichang_new2'),
	'(车)优惠行情' => array('is_def'=>0,'tmp'=>'v3_auto_youhui_hangq','exp'=>'key=time|view=新闻时间|type=string'),
	'(车)章鱼说车' => array('is_def'=>0,'tmp'=>'v3_auto_zhangyu_shuo','exp'=>'key=subtitle|view=图上文字|type=string|required=1'),
	'团购' => array('is_def'=>0,'tmp'=>'v3_tuangou'),/*动态碎片*/
	'活动' => array('is_def'=>0,'tmp'=>'v3_huodong','exp'=>'key=itemid|view=活动ID|type=string|required=1'),
	'频道搜索框配置' => array('is_def'=>0,'tmp'=>'v3_search'),
	'频道首页-大家都在说' => array('is_def'=>0,'tmp'=>'v3_posts'),
	'(车)汽车频道联系我们' => array('is_def'=>0,'tmp'=>'v3_auto_contact_us'),
	'(房)房产频道联系我们' => array('is_def'=>0,'tmp'=>'v3_f_contact_us'),
	'(房)独家章鱼评' => array('is_def'=>0,'tmp'=>'v3_f_zhangyu_ping'),
	'(房)独家章鱼评右侧' => array('is_def'=>0,'tmp'=>'v3_f_zhangyu_right'),
	'(房)大咖鉴盘' => array('is_def'=>0,'tmp'=>'v3_f_daka_rating'),
	'(房)置业宝典' => array('is_def'=>0,'tmp'=>'v3_f_book'),
	//'(房)车市+章鱼右' => array('is_def'=>0,'tmp'=>'v3_auto_lichang'),
	'(房)独家章鱼评右侧-其他搭配' => array('is_def'=>0,'tmp'=>'v3_f_zhangyu_right2'),
	/*'(房)置业大咖' =>	array('is_def'=>0,'tmp'=>'v3_f_zhiye_daka','exp'=>'key=price|view=均价|type=string
key=address|view=地址|type=string'),*/
	'(房)看报告' => array('is_def'=>0,'tmp'=>'v3_f_kanbaogao'),
	'(房)看报告2' => array('is_def'=>0,'tmp'=>'v3_f_kanbaogao2'), //为什么有2？因为一个页面只能有同一个碎片，如果要2个，只能再加一个
	'(房)成都团房'=> array('is_def'=>0,'tmp'=>'v3_f_tf4.0'),
	'(房)楼盘搜索'=> array('is_def'=>0,'tmp'=>'v3_f_loupan_search','exp'=>'key=price|view=均价|type=string
key=address|view=地址|type=string'),
	'(房)新楼盘盘点' => array('is_def'=>0,'tmp'=>'v3_f_loupan_rec','exp'=>'key=price|view=价格详情|type=string
key=youhui|view=优惠信息|type=string
key=kaipan|view=开盘时间|type=string
key=ruzhu|view=入住时间|type=string
key=comp|view=开发商|type=string
key=address|view=物业地址|type=string'),
	'(房)楼市快讯' => array('is_def'=>0,'tmp'=>'v3_f_speed_news'),
	'(房)新鲜楼盘抢先看' => array('is_def'=>0,'tmp'=>'v3_f_fresh_estate'),
	'(房)美容美装' => array('is_def'=>0,'tmp'=>'v3_f_makeup'),
	'房产中部广告1' => array('is_def'=>0,'tmp'=>'v3_f_m_ad1'),
	'房产中部广告2' => array('is_def'=>0,'tmp'=>'v3_f_m_ad2'),
);   
   
 // File end