<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*客户端 - 网站域名切换*/
$config['web2wap'] = array(
	'review' => 'inpost://',
	'place'=> 'inplace://',
	'user' => 'inuser://',
	'pc' => 'inpc://'
);

$config['wap2web'] = array(
	'inpost://' => '/review',
	'inplace://' => '/place',
	'inuser://' => '/user',
	'inpc://' => '/placecoll',
	'inevents://' => '/event_new',
);