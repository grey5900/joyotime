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

$route['default_controller'] = "channel";
$route['404_override'] = '';

//$route['(:num)/(:num)'] = 'channel/index/$1/news/$2';
$route['new/(:num)/(:num)/(:num)/(:num)'] = 'channel/index/$1/$2/new/$3/$4';
$route['hot/(:num)/(:num)/(:num)/(:num)'] = 'channel/index/$1/$2/hot/$3/$4';
$route['new/(:num)/(:num)/(:num)'] = 'channel/index/$1/$2/new/1/$3';
$route['hot/(:num)/(:num)/(:num)'] = 'channel/index/$1/$2/hot/1/$3';

// 通行证登录
$route['signin'] = 'passport/signin';
$route['taboo'] = 'passport/check_taboo';
$route['signup'] = 'passport/signup';
$route['signout'] = 'passport/signout';
$route['sso'] = 'passport/sso';
$route['sso_logout'] = 'passport/sso_logout';
//第三方登录
//$route['login/(:any)'] = 'password/oauth_login/$1';
//完善资料
$route['complete/(:num)'] = 'profile/complete/$1';
//找回密码
$route['reset_pwd'] = 'user/reset_pwd';
$route['reset_pwd_(:num)'] = 'user/reset_pwd/$1';
$route['reset_pwd_(:num)/(:any)'] = 'user/reset_pwd/$1/$2';

//客户端下载页面
$route['download'] = 'web/download';
$route['download/(:any)'] = 'web/download/$1';

// 频道页面
$route['category/(:num)'] = 'channel/category/$1/0/new';
$route['category/(:num)/(:num)'] = 'channel/category/$1/$2/new';
$route['category/(:num)/(:num)/(:any)/(:num)'] = 'channel/category/$1/$2/$3/$4'; // 栏目页面
$route['category/(:num)/(:num)/(:any)/(:num)/(:num)'] = 'channel/category/$1/$2/$3/$4/$5'; // 栏目页面
$route['nlist/(:any)'] = 'channel/news_list/$1/$2'; // 新闻列表
$route['article/(:any)'] = 'channel/article/$1/$2'; // 内容
$route['plist/(:any)'] = 'channel/place/$1/$2/$3'; // 地点列表

// 用户主页
$route['user'] = 'user/index';//个人主页
$route['user/(:any)'] = 'user/index/$1';//个人主页
$route['user_(:any)'] = 'user/$1';//其他展示页

// 地点
$route['place_(:any)'] = 'place/$1';
$route['place/(:any)'] = 'place/index/$1';
$route['place/list'] = 'place/search_place';
$route['place/list/(:any)'] = 'place/search_place/$1';
$route['add_place'] = 'place/add_place';

# 活动
$route['event/(:num)'] = 'event/index/$1';
$route['event/detail/(:any)'] = 'event/detail/$1';

# 活动
$route['event_new/(:num)'] = 'event_new/index/$1';
$route['event_new/(:num)/(:num)'] = 'event_new/index/$1/$2';

//POST
$route['review/(:any)'] = 'review/index/$1';//POST详情页

// API配置，兼容1.0接口
$route['rtf/feed/find'] = 'api/rtf_feed';
$route["ciis/(:any)"] = 'api/ciis/$1';
$route["ciis2/(:any)"] = 'api/ciis2/$1';
$route['rtf/white/getWhiteTipFeed'] = 'api/rtf_white';

$route['toPlacePicWall(:any)'] = 'api/place_wall';
$route['toPlacePicDetail(:any)'] = 'api/place_pic_detail';
$route['toUserPicWall(:any)'] = 'api/user_wall';

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


// 绑定
$route['bind/(:num)/(:num)'] = 'oauth/bind/$1/$2';
$route['unbind/(:num)/(:num)'] = 'oauth/unbind/$1/$2';
$route['bind_error/(:any)'] = 'oauth/bind_error/$1';
$route['login/(:any)'] = 'oauth/login/$1';
$route['login_error/(:any)'] = 'oauth/login_error/$1';

//宝贝时钟
$route['babyclock/(:num)'] = 'babyclock/index/$1';
$route['babyclock/(:num)/(:num)'] = 'babyclock/index/$1/$2';

$route['qr/(:any)'] = 'web/qr/$1';//二维码桥接

//地点册
$route['placecoll'] = 'collection_place/index/hot/1/';
$route['placecoll/(:any)/(:num)'] = 'collection_place/index/$1/$2/';

$route['placecoll/(:num)'] = 'collection_place/detail/$1/1/';
$route['placecoll/(:num)/(:num)'] = 'collection_place/detail/$1/$2/';



/* End of file routes.php */
/* Location: ./application/config/routes.php */