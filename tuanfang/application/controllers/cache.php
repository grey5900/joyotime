<?php
if (!defined('BASEPATH'))
	exit('No direct script access allowed');
/*
 * 缓存的一些操作
*
* Author: piglet chenglin.zhu@gmail.com
* Created on 2013-08-16
*/

class Cache extends MY_Controller {
	/**
	 * 更新缓存
	 * @param 时间戳 $timestamp
	 * @param 验证签名 $sign
	 */
	function clear_cache($timestamp = 0, $sign = '', $do = '', $name ='') {
		$timestamp = intval($timestamp);
		$sign = trim($sign);
		(empty($timestamp) || empty($sign)) && die();

		$_sign = md5($timestamp . $timestamp{$timestamp{9}});
		($_sign == $sign) or die();
		
		if($do == 'inc') {
			// 清空inc某个文件的单独缓存
			@unlink(FRAMEWORK_PATH . 'data/inc/cache_' . trim($name) . '.inc.php');
		} else {
			// 清空data下的所有缓存
			$cache_path = FRAMEWORK_PATH . 'data/';
			$cache_dir = dir($cache_path);
			while(false != ($f = $cache_dir->read())) {
				if('.' != $f && '..' != $f && !is_dir($f)) {
					$d = dir($cache_path . $f);
					while(false != ($sf = $d->read())) {
						if('.' != $sf && '..' != $sf && !is_dir($sf)) {
							unlink($cache_path . $f . '/' . $sf);
						}
					}
					$d->close();
				}
			}
			$cache_dir->close();
		}

		$this->echo_json(array('statusCode' => 200, 'message' => '团房缓存清空成功'));
	}
}
