<?php
/**
 * api接口配置
 * Create by 2012-5-7
 * @author liuw
 * @copyright Copyright(c) 2012-2014 joyotime
 */
  
 // Define and include
if ( ! defined('BASEPATH')) exit('No direct script access allowed');   
   
 // Code
$lang['api_signin'] = 'user/signin';
$lang['api_signup'] = 'user/signup';
$lang['api_user_edit_email'] = 'user/update_email';
$lang['api_user_edit_base'] = 'user/update_basic';
$lang['api_user_edit_avatar'] = 'user/update_avatar';
$lang['api_oauth_bind'] = 'oauth/bind';
$lang['api_oauth_unbind'] = 'oauth/unbind';
$lang['api_oauth_signin'] = 'oauth/login';  
$lang['api_post_tip'] = 'post/save_tip'; 
$lang['api_post_photo'] = 'post/save_photo';
$lang['api_share'] = 'user/share';
$lang['api_push_sys'] = 'push/push_system_message';
$lang['api_get_img'] = 'image/get_image';


// 被赞 type=1(点评),2(照片)
// someone觉得你在somewhere的点评/照片很赞
// 
// 被回复 type=3(点评),4(照片)
// 你在somewhere的点评/照片有了新回复
// 赞
$lang['sys_msg_praise'] = '%s觉得你在%s的%s很赞';
// 回复
$lang['sys_msg_reply'] = '你在%s的%s有了新回复';


