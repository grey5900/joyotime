<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	http://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There area two reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router what URI segments to use if those provided
| in the URL cannot be matched to a valid route.
|
*/

$route['default_controller'] = "web";
$route['404_override'] = '';


// 自定义的路由规则
// 用于更新的一些路由
$route['index'] = 'web/index';
$route['index/(:num)/(:num)'] = 'web/index/$1/$2';


//帐户相关页面的路由规则
$route['signup'] = 'account/signup';
$route['signin'] = 'account/signin';
$route['is_signin'] = 'account/is_signin';
$route['check_username/(:any)'] = 'account/check_username/$1';
$route['check_email'] = 'account/check_email';
$route['check_taboo'] = 'account/check_taboo';
$route['signout'] = 'account/signout';
$route['complete/(:num)'] = 'account/complete/$1';
$route['reset_pwd'] = 'account/reset_pwd';
$route['reset_pwd_(:num)'] = 'account/reset_pwd/$1';
$route['reset_pwd_(:num)/(:any)'] = 'account/reset_pwd/$1/$2';
$route['upload'] = 'web/upload';
$route['bind/(:num)/(:num)'] = 'oauth/bind/$1/$2';
$route['unbind/(:num)/(:num)'] = 'oauth/unbind/$1/$2';
$route['login/(:any)'] = 'oauth/login/$1';
$route['login_error/(:any)'] = 'oauth/login_error/$1';
$route['bind_error/(:any)'] = 'oauth/bind_error/$1';
//通用交互功能的路由规则
$route['reply'] = 'common/reply';
$route['share'] = 'common/share';
$route['favorite'] = 'common/favorite';
$route['unfavorite'] = 'common/un_favorite';
$route['praise'] = 'common/praise';
$route['show_form/(:any)'] = 'common/show_form/$1';
$route['check_online'] = 'common/check_online';
$route['download'] = 'download/iphone';
$route['download/android'] = 'download/android';
$route['download/iphone'] = 'download/iphone';
$route['about'] = 'web/about';
$route['contact'] = 'web/contact';
$route['help'] = 'web/help';
$route['privacy'] = 'web/privacy';
$route['jobs'] = 'web/jobs';
$route['photo/(:any)'] = 'web/photo/$1';//图片墙
$route['photo'] = 'web/photo/1';//图片墙
$route['qr/(:any)'] = 'web/qr/$1';//二维码桥接
//用户相关页面的路由规则
$route['user/(:any)'] = 'user/index/$1';
$route['user_(:any)'] = 'user/$1';
//$route['user_(:any)/(:any)'] = 'user/$1/$2';
$route['do_follow'] = 'user/do_follow';
//地点相关页面的路由规则
$route['list_category/(:num)'] = 'place/list_category/$1';
$route['list_son/(:num)'] = 'place/list_soncategory/$1';
$route['get_parent/(:num)'] = 'place/get_parent/$1';
$route['captcha'] = 'place/captcha';
$route['check_captcha'] = 'place/check_captcha';
$route['add_place'] = 'place/add_place'; 
$route['place'] = 'place/0';
$route['place_(:any)'] = 'place/$1';
$route['place/(:num)'] = 'place/index/$1';
$route['place/list/(:any)'] = 'place/listes/$1';
$route['place/list'] = 'place/listes/1';
//review
$route['review/(:num)'] = 'review/index/$1';
$route['review_(:any)'] = 'review/$1';

// 在线
$route['online'] = 'web/online';

// 跳转
$route['redirect/(:any)'] = 'web/redirect/$1';


// API配置，兼容1.0接口
$route['rtf/feed/find'] = 'api/rtf_feed';
$route["ciis/(:any)"] = 'api/ciis/$1';
$route['rtf/white/getWhiteTipFeed'] = 'api/rtf_white';

$route['toPlacePicWall(:any)'] = 'api/place_wall';
$route['toPlacePicDetail(:any)'] = 'api/place_pic_detail';
$route['toUserPicWall(:any)'] = 'api/user_wall';

//CMS
$route['cms_list/(:any)'] = 'cms/list_news/$1';
$route['cms_detail/(:num)'] = 'cms/detail/$1';
$route['cms_search'] = 'cms/search';
$route['cms_search/(:any)'] = 'cms/search/$1';

//品牌商家相关
$route['brand_(:any)'] = 'brand/$1';
//活动
//$route['event/(:any)'] = 'event/index/$1';
//$route['event_(:any)'] = 'event/$1';


// 上墙
// 获取列表数据 1：活动名称  2：活动关联的地点ID，格式：1001-1002-1003 3：获取条数
$route['wall/post_list_place/(:any)/(:any)/(:num)'] = 'wall/post_list_place/$1/$2/$3';
// 审核数据 1：活动名称 2：POST的ID
$route['wall/checked/(:any)/(:num)'] = 'wall/checked/$1/$2';
// 清空 1：活动名称 
$route['wall/clean/(:any)'] = 'wall/clean/$1';
// 获取已经通过的列表  1：活动名称 2：之前最后一条的score
$route['wall/list/(:any)/(:any)'] = 'wall/list_post/$1/$2';
// 获取列表（品牌）1：活动名称  2：品牌ID，格式：1001:1002:1003 3：获取条数
$route['wall/post_list_brand/(:any)/(:any)/(:num)'] = 'wall/post_list_brand/$1/$2/$3';
////////////////////////////////////////////////////


/* End of file routes.php */
/* Location: ./application/config/routes.php */