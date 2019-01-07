<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/*
 * 系统的配置 存放不需要配置的东西
 * 
 * Author: piglet chenglin.zhu@gmail.com
 * Created on 2012-9-12
 */

// 模板的配置
$config['template'] = array(
        'template_dir' => FCPATH . 'static/template/',
        'compiled_dir' => FCPATH . 'data/compiled/',
        'pre_str' => "<?php if (!defined('BASEPATH')) exit('No direct script access allowed');?>"
);

$config['api_serv'] = 'http://api-a.out.chengdu.cn';
$config['api_folder'] = '/true/private_api/';
//$config['api_serv'] = 'http://192.168.1.40';
//$config['api_folder'] = '/private_api/';

//商家每日最大推送次数，2.4以后版本可能会改成动态确定，2.4在这里配置
$config['push_max'] = 1;