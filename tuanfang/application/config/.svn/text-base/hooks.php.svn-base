<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------------
| Hooks
| -------------------------------------------------------------------------
| This file lets you define "hooks" to extend CI without hacking the core
| files.  Please see the user guide for info:
|
|	http://codeigniter.com/user_guide/general/hooks.html
|
*/

// 构造函数之后的拦截器，用于更新团购和推荐状态
$hook['post_controller_constructor'][] = array(
		'class' => 'Interceptor',
		'function' => 'flush_house_status',
		'filename' => 'interceptor.php',
		'filepath' => 'hooks',
		'params' => array()
);

/* End of file hooks.php */
/* Location: ./application/config/hooks.php */