<?php
if (!defined('BASEPATH'))
	exit('No direct script access allowed');

/**
 * 获取当前时间
 * @param 时间戳 $t
 * @param 格式化 $format
 */
function now($t = 0, $format = '') {
	static $timestamp = 0;
	
	if ($timestamp === 0) {
		$timestamp = TIMESTAMP;
	}
	
	empty($t) && ($t = $timestamp);
	$time = $format?gmdate($format, $t + 3600 * 8):$t;
	
	return $time;
}

/**
 * 获取一个redis对象
 */
function redis_instance() {
	// 静态变量  同一个CGI进程中多次调用返回一个
	static $redis = null;
	$CI = & get_instance ();
	if (null == $redis)
	{
		$CI->load->driver ( 'cache', array ('adapter' => 'redis' ) );
		$redis = $CI->cache->redis;
	}

	return $redis;
}

/**
 * JSON加密
 * @param mixed $string
 * @param int $option
 * @return string
 */
function encode_json($string, $option = JSON_UNESCAPED_UNICODE) {
	return json_encode ( $string, $option );
}

/**
 * JSON解密
 * @param string $string
 * @param bool $assoc
 * @return mixed
 */
function decode_json($string, $assoc = true) {
	return json_decode ( $string, $assoc );
}

/**
 * 加入反斜线
 * @param mixed $string
 * @param number $force
 * @return mixed
 */
function daddslashes($string, $force = 1)
{
	if (is_array ( $string ))
	{
		$keys = array_keys ( $string );
		foreach ( $keys as $key )
		{
			$val = $string [$key];
			unset ( $string [$key] );
			$string [addslashes ( $key )] = daddslashes ( $val, $force );
		}
	} else
	{
		$string = addslashes ( $string );
	}
	return $string;
}

/**
 * 去掉反斜线
 * @param maxied $string
 * @return mixed
 */
function dstripslashes($string)
{
	if (empty ( $string ))
		return $string;
	if (is_array ( $string ))
	{
		foreach ( $string as $key => $val )
		{
			$string [$key] = dstripslashes ( $val );
		}
	} else
	{
		$string = stripslashes ( $string );
	}
	return $string;
}

/**
 * 计算翻页的一些参数
 * @param 总条数 $total
 * @param 当前页码 $page
 * @param 每页条数 $page_size
 */
function paginate($total_num, $page = 1, $page_size = 0) {
	$CI = &get_instance();
	($page < 1) && ($page = 1);
	($page_size <= 0) && ($page_size = $CI->config->item('default_page_size'));
	
	$total_page = ceil($total_num/$page_size);
	$offset = $page_size * ($page - 1);
	 
	return compact('total_num', 'total_page', 'offset', 'page', 'page_size');
}

/**
 * 截取字符串
 * @param string $string
 * @param int $strlen
 * @return string
 */
function cut_string($string, $length = 60, $dot = '...') {
	if (strlen ( $string ) <= $length)
	{
		return $string;
	}

	$pre = chr ( 1 );
	$end = chr ( 1 );
	$string = str_replace ( array ('&amp;', '&quot;', '&lt;', '&gt;' ), array ($pre . '&' . $end, $pre . '"' . $end, $pre . '<' . $end, $pre . '>' . $end ), $string );

	$strcut = '';
	if (strtolower ( CHARSET ) == 'utf-8')
	{

		$n = $tn = $noc = 0;
		while ( $n < strlen ( $string ) )
		{

			$t = ord ( $string [$n] );
			if ($t == 9 || $t == 10 || (32 <= $t && $t <= 126))
			{
				$tn = 1;
				$n ++;
				$noc ++;
			} elseif (194 <= $t && $t <= 223)
			{
				$tn = 2;
				$n += 2;
				$noc += 2;
			} elseif (224 <= $t && $t <= 239)
			{
				$tn = 3;
				$n += 3;
				$noc += 2;
			} elseif (240 <= $t && $t <= 247)
			{
				$tn = 4;
				$n += 4;
				$noc += 2;
			} elseif (248 <= $t && $t <= 251)
			{
				$tn = 5;
				$n += 5;
				$noc += 2;
			} elseif ($t == 252 || $t == 253)
			{
				$tn = 6;
				$n += 6;
				$noc += 2;
			} else
			{
				$n ++;
			}

			if ($noc >= $length)
			{
				break;
			}

		}
		if ($noc > $length)
		{
			$n -= $tn;
		}

		$strcut = substr ( $string, 0, $n );

	} else
	{
		for($i = 0; $i < $length; $i ++)
		{
			$strcut .= ord ( $string [$i] ) > 127 ? $string [$i] . $string [++ $i] : $string [$i];
		}
	}

	$strcut = str_replace ( array ($pre . '&' . $end, $pre . '"' . $end, $pre . '<' . $end, $pre . '>' . $end ), array ('&amp;', '&quot;', '&lt;', '&gt;' ), $strcut );

	$pos = strrpos ( $strcut, chr ( 1 ) );
	if ($pos !== false)
	{
		$strcut = substr ( $strcut, 0, $pos );
	}
	return $strcut . $dot;
}

/**
 * 统计，一个中文就是一个字符，英文字符就是一个字符
 */
function cstrlen($str)
{
	$count = 0;
	for($i = 0; $i < strlen ( $str ); $i ++)
	{
		$value = ord ( $str [$i] );
		if ($value > 127)
		{
			if ($value >= 192 && $value <= 223)
				$i ++;
			elseif ($value >= 224 && $value <= 239)
			$i = $i + 2;
			elseif ($value >= 240 && $value <= 247)
			$i = $i + 3;
		}
		$count ++;
	}
	return $count;
}

/**
 * 统计中文算2个字符，英文就是一个字符
 */
function dstrlen($str)
{
	$count = 0;
	for($i = 0; $i < strlen ( $str ); $i ++)
	{
		$value = ord ( $str [$i] );
		if ($value > 127)
		{
			$count ++;
			if ($value >= 192 && $value <= 223)
				$i ++;
			elseif ($value >= 224 && $value <= 239)
			$i = $i + 2;
			elseif ($value >= 240 && $value <= 247)
			$i = $i + 3;
		}
		$count ++;
	}
	return $count;
}

// 验证手机号
function check_cellphone($cellphone) {
	return preg_match("/^(13|15|18)[0-9]{9}$/", $cellphone);
}

