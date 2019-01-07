<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * 具体cache操作的函数
 * 
 * @author chenglin.zhu@gmail.com
 * @date 2012-11-19
 */


/**
 * 获取一组敏感词
 * Create by 2012-12-14
 * @author liuweijava
 * @param string $type
 * @return array
 */
function cache_func_taboo($type=''){ 
	static $db = null;
	if(null == $db){
		$CI = &get_instance();
		$CI->load->model('taboo_model', 'm_taboo');
		$db = $CI->m_taboo;
	}
	$taboos = $db->get_taboos($type);
	return $taboos;
}

