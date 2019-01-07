<?php
if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
/**
 * website的一些
 * @author chenglin.zhu@gmail.com
 * @date 2013-08-13
 */


/**
 * ID加密
 * @param string $id
 */
function id_encode($id, $len) {
	$str = base64_encode($id);
	$p = $str{$len};
	if ($p !== false) {
		if(strtolower($p) == $p) {
			// 小写
			$p = strtoupper($p);
		} else {
			// 大写
			$p = strtolower($p);
		}

		$str = substr($str, 0, $len) . $p . substr($str, $len+1);
	}

	return $str;
}

/**
 * ID解密
 * @param string $str
 */
function id_decode($str, $len) {
	$p = $str{$len};
	if ($p !== false) {
		if(strtolower($p) == $p) {
			// 小写
			$p = strtoupper($p);
		} else {
			// 大写
			$p = strtolower($p);
		}

		$str = substr($str, 0, $len) . $p . substr($str, $len+1);
	}

	return base64_decode($str);
}

function rand_array($array) {
	$index = array_rand($array);
	return $array[$index];
}

function wine_gift() {
	$CI = &get_instance();
	return rand(0, 999) < $CI->config->item('gift_rate');
}