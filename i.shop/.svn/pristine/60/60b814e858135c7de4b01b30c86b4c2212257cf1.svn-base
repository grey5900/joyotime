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

$route['login'] = 'web/login';
//$route['index/(:any)/(:any)'] = 'web/index/$1/$2';
//$route['index/(:any)/(:any)/(:num)'] = 'web/index/$1/$2/$3';
$route['modify_pass'] = 'web/modify_pass';
$route['logout'] = 'web/logout';
$route['export_data/(:any)/(:any)'] = 'web/export_data/$1/$2';

//优惠
$route['prefer'] = 'prefer/add';
$route['prefer/(:any)'] = 'prefer/$1';
//会员
$route['member'] = 'member/index';
$route['member/index/(:any)'] = 'member/index/$1';
$route['member/(:any)'] = 'member/index/$1';
//消息
$route['message'] = 'message/index/';
$route['message/(:any)'] = 'message/$1';
//图片上传
$route['upload'] = 'web/upload';
$route['upload/(:num)'] = 'web/upload/$1';
$route['upload_profile/(:any)'] = 'web/upload_profile/$1';
$route['upload_to_pic'] = 'web/upload_to_pic';
//扩展信息
$route['profile'] = 'profile/index';
$route['profile/(:any)'] = 'profile/$1';
//统计
$route['report'] = 'report/index';
$route['report/(:any)'] = 'report/$1';
/* End of file routes.php */
/* Location: ./application/config/routes.php */