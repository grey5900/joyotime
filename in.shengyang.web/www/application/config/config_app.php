<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
 * 应用的一些设定，可以修改
 * 
 * Author: piglet chenglin.zhu@gmail.com
 * Created on 2012-11-7
 */

// 默认自动登录
$config['auto_login'] = true;

// 频道默认样式
$config['in_style'] = 'default';

// IN CITY 默认样式
$config['style'] = 'default';

// IN CITY domain
$config['in_domain'] = 'joyotime.com';

// IN CITY 站点地址
$config['in_host'] = 'in.' . $config['in_domain'];

// 活动频道的ID号
// $config['event_cid'] = 47;

// 版本号 暂时先用时间戳，上线以后改成版本号
$config['version'] = TIMESTAMP;

// 这里是旧活动的配置信息，一大坨
$config['event_config'] = FRAMEWORK_PATH . 'www/static/event/inc_event_config.php';

// 上传图片接口地址
$config['upload_image_api'] = array(
        'transfer_image' => '/image/transfer_image',
        'sava_image' => '/image/save_image'
);

// 客户端用的token数组
// token_password=fuck_fuck_fuck_gfw_must_die,fuck_key2,fuck_key3
$config['token_password'] = array(
        'fuck_fuck_fuck_gfw_must_die', 
        'fuck_key2', 
        'fuck_key3'
        );

// 需要跳转到IN CITY域名的controller
$config['redirect_c'] = array(
            'api' => $config['in_host'],
            'cache' => $config['in_host'],
            'common' => $config['in_host'],
            'place' => $config['in_host'],
            'profile' => 'p.' . $config['in_host'],
            'review' => $config['in_host'],
            'user' => $config['in_host'],
            'wall' => $config['in_host']
        );
