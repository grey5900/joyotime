<?php
if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
/**
 * 应用的一些公用函数
 * @author chenglin.zhu@gmail.com
 * @date 2012-11-12
 */

/**
 * 时间格式化
 * @param int $t
 * @param string $format
 * @return string
 */
function idate_format($t = 0, $format = '')
{
	static $timestamp = 0;

	if ($timestamp === 0)
	{
		$timestamp = TIMESTAMP;
	}

	if ($t && 'u' == $format)
	{
		// 只有传入了时间，才会去处理这个样子的显示
		// 显示什么前这样子的时间
		$offset = $timestamp - $t;
		if ($offset > 3600 && $offset <= 86400)
		{
			$time = intval ( $offset / 3600 ) . '小时前';
		} elseif ($offset > 1800 && $offset <= 3600)
		{
			$time = '半小时前';
		} elseif ($offset > 60 && $offset <= 1800)
		{
			$time = intval ( $offset / 60 ) . '分钟前';
		} elseif ($offset > 0 && $offset <= 60)
		{
			$time = $offset . '秒前';
		} elseif ($offset == 0)
		{
			$time = '刚刚';
		} else
		{
			$time = gmdate ( 'Y-m-d H:i', $t + 3600 * 8 );
		}
	} else
	{
		// 如果为空 那么默认为当前时间
		empty ( $t ) && $t = $timestamp;
		$time = $format ? gmdate ( $format, $t + 3600 * 8 ) : $t;
	}

	return $time;
}

/**
 * JSON加密
 * @param mixed $string
 * @param int $option
 * @return string
 */
function encode_json($string, $option = JSON_UNESCAPED_UNICODE)
{
	return json_encode ( $string, $option );
}

/**
 * JSON解密
 * @param string $string
 * @param bool $assoc
 * @return mixed
 */
function decode_json($string, $assoc = true)
{
	$string = str_replace(array("\n","\r"),"", $string);
	return json_decode ( $string, $assoc );
}

/**
 *
 * 字符串标签转成数组
 * @param string $str
 * @param string $spilt 分隔符(默认以空格分隔)
 * @return array $tags
 * @author fc_lamp 2013320
 */
function tags_to_array($str, $spilt = '')
{
	$tags = array ();
	if (empty ( $spilt ))
	{
		//笨方法
		$str = str_replace('　',' ',$str);
		$tpl = preg_split ( "/[\s]+/", $str );
	} else
	{
		$tpl = explode ( $spilt, $str );
	}

	foreach ( $tpl as $tag )
	{
		$tag = trim ( $tag );
		if (empty ( $tag ))
		{
			continue;
		}
		$tags [$tag] = $tag;
	}
	return $tags;
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
 * 获取一个redis对象
 */
function redis_instance()
{
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
 * 获得一个salt
 */
function get_salt($len = 8)
{
	$salt = $GLOBALS ['CI']->cookie ( 'salt' );

	if (empty ( $salt ))
	{
		$salt = substr ( random_string ( 'alpha' ), 0 - $len );
		set_cookie ( 'salt', $salt );
	}

	return $salt;
}

/**
 * 访问接口
 * @param 接口uri $api_uri
 * @param 调用方法 $request_type
 * @param 参数 $attrs
 * @param header内容 $header key=>value 数组
 * @param 配置key $conf_key
 */
function request_api($api_uri, $request_type = 'POST', $attrs = array(), $header = array() , $custogc = false)
{
	$CI = & get_instance ();
	
	if (strpos($api_uri, 'http://') === 0) {
		$http_api_uri = $api_uri;
	} else {
		$http_api_uri = $CI->config->item("api")['url'] . $api_uri;
	}

	// 需要用户验证的会在header里面放入uid
	$headers = array ();
	if ($header)
	{
		$uid = $header ['uid'];
		if ($uid)
		{
			unset ( $header ['uid'] );
				
			$token_password = $CI->config->item ( 'token_password' );
			$tx = array_rand ( $token_password );
			$header ['X-ATX'] = $tx;
			$header ['X-INID'] = $uid;
			$header ['X-Incd20-Auth'] = md5 ( $token_password [$tx] . $uid );
		}
	}
	$urls = parse_url ( $http_api_uri );
	$api_uri = $urls ['path'];
	$ogc = uniqid ();
	if($custogc && $uid){
		$user = get_data('user',$uid);
		$ogc = $user['deviceCode'];
	}
	$timestamp = number_format ( microtime ( true ) * 1000, 0, '.', '' );
	$api_request_key = $CI->config->item ( 'api_request_key' );
	$i = array_rand ( $api_request_key );
	$orz = md5 ( $api_uri . $ogc . $api_request_key [$i] . $timestamp );

	$header ['X-Ogc'] = $ogc;
	$header ['X-Timestamp'] = $timestamp;
	$header ['X-Orz'] = $orz;
	$header ['X-Real-Url'] = $api_uri;

	foreach ( $header as $k => $v )
	{
		$headers [] = "{$k}: {$v}";
	}

	return http_request ( $http_api_uri, $attrs, $headers, $request_type, true );
}

/**
 * http请求
 * @param $url 请求地址
 * @param $req_params 请求参数
 * @param $headers 请求头 例：array('X-INID: 1', 'uid: 1');
 * @param $method 请求方法 默认GET
 * @param $return 是否输出
 */
function http_request($url, $req_params = array(), $headers = array(), $method = 'GET', $return = false)
{
	$method = strtoupper ( $method );
	$ch = curl_init ();
	if ($method == 'GET')
	{
		// GET请求
		$url_parsed = parse_url ( $url );
		curl_setopt ( $ch, CURLOPT_URL, $url . ($url_parsed ['query'] ? '&' : '?') . http_build_query ( $req_params ) );
	} else
	{
		// POST请求
		curl_setopt ( $ch, CURLOPT_URL, $url );
		curl_setopt ( $ch, CURLOPT_POST, count ( $req_params ) );
		// 判断参数是否有@开头的，有的话为传文件
		$is_file = false;
		foreach ( $req_params as $key => $value )
		{
			if (strpos ( $key, '@' ) === 0)
			{
				$req_params [substr ( $key, 1 )] = $value;
				unset ( $req_params [$key] );
				$is_file = true;
			} elseif (strpos ( $value, '@' ) === 0)
			{
				$req_params [$key] = " @" . substr ( $value, 1 );
			}
		}

		if ($is_file)
		{
			// 传文件直接用数组
			curl_setopt ( $ch, CURLOPT_POSTFIELDS, $req_params );
		} else
		{
			curl_setopt ( $ch, CURLOPT_POSTFIELDS, http_build_query ( $req_params ) );
		}
	}
	curl_setopt ( $ch, CURLOPT_HEADER, false );
	// 设定头
	if ($is_file)
	{
		$headers [] = "content-type: multipart/form-data; charset=UTF-8";
	} else
	{
		$headers [] = "content-type: application/x-www-form-urlencoded; charset=UTF-8";
	}
	curl_setopt ( $ch, CURLOPT_HTTPHEADER, $headers );

	if ($return)
	{
		curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt ( $ch, CURLOPT_BINARYTRANSFER, true );
		$data = curl_exec ( $ch );
	} else
	{
		curl_exec ( $ch );
	}
	curl_close ( $ch );

	if ($return)
	{
		return $data;
	}
}

/**
 * 获取语言文件信息
 *
 * @param string $line
 * @param mixed $args
 * @return string
 */
function lang_message($line, $args = array())
{
	$line = $GLOBALS ['CI']->lang->line ( $line );
	array_unshift ( $args, $line );
	return call_user_func_array ( 'sprintf', $args );
}

/**
 * DISCUZ的加解密函数
 * @param string $string
 * @param string $operation
 * @param  $key
 * @param  $expiry
 */
function authcode($string, $operation = 'DECODE', $key = '', $expiry = 0)
{
	// 动态密钥长度
	$ckey_length = 4;
	// 密钥
	$key = md5 ( $key ? ($key === 'nil'?'':$key) : 'abcdefghijklmnopqrstuvwxyz13550009575' );
	// 密钥A用于加密
	$keya = md5 ( substr ( $key, 0, 16 ) );
	// 密钥B用于验证
	$keyb = md5 ( substr ( $key, 16, 16 ) );
	// 密钥C，生成动态密码部分
	// 解密的时候获取需要解密的字符串前面的$ckey_length长度字符串
	// 加密的时候，用当前时间戳的微妙数md5加密的最后$ckey_length长度字符串
	$keyc = $ckey_length ? ($operation == 'DECODE' ? substr ( $string, 0, $ckey_length ) : substr ( md5 ( microtime () ), - $ckey_length )) : '';
	// 用于运算的密钥
	$cryptkey = $keya . md5 ( $keya . $keyc );
	$key_length = strlen ( $cryptkey );

	$string = $operation == 'DECODE' ? base64_decode ( substr ( $string, $ckey_length ) ) : sprintf ( '%010d', $expiry ? $expiry + time () : 0 ) . substr ( md5 ( $string . $keyb ), 0, 16 ) . $string;
	$string_length = strlen ( $string );

	$result = '';
	$box = range ( 0, 255 );

	$rndkey = array ();
	for($i = 0; $i <= 255; $i ++)
	{
		$rndkey [$i] = ord ( $cryptkey [$i % $key_length] );
	}

	for($j = $i = 0; $i < 256; $i ++)
	{
		$j = ($j + $box [$i] + $rndkey [$i]) % 256;
		$tmp = $box [$i];
		$box [$i] = $box [$j];
		$box [$j] = $tmp;
	}

	for($a = $j = $i = 0; $i < $string_length; $i ++)
	{
		$a = ($a + 1) % 256;
		$j = ($j + $box [$a]) % 256;
		$tmp = $box [$a];
		$box [$a] = $box [$j];
		$box [$j] = $tmp;
		$result .= chr ( ord ( $string [$i] ) ^ ($box [($box [$a] + $box [$j]) % 256]) );
	}

	if ($operation == 'DECODE')
	{
		if ((substr ( $result, 0, 10 ) == 0 || substr ( $result, 0, 10 ) - time () > 0) && substr ( $result, 10, 16 ) == substr ( md5 ( substr ( $result, 26 ) . $keyb ), 0, 16 ))
		{
			return substr ( $result, 26 );
		} else
		{
			return '';
		}
	} else
	{
		return $keyc . str_replace ( '=', '', base64_encode ( $result ) );
	}
}

/**
 * 获取图片地址
 * @param string $img
 * @param string $type common head
 * @param string $size odp hdp等
 */
function image_url($img, $type, $size = '')
{
	if (strpos ( $img, 'http://' ) === 0)
	{
		// 如果是完整的http地址，直接返回
		return $img;
	}
	global $CI;
	if ((empty ( $img ) || $img == 'default.png') || $img == "null")
	{
		return base_url () . 'static/img/' . $type . '_default.png';
	}

	$image_setting = get_inc ( 'imagesetting' );
	if ($type == 'head')
	{
		// 如果是取头像，需要去判断下是否为1.X的默认头像
		$default_image = get_inc ( 'defaulthead' );
		if ($default_image [$img])
		{
			return $image_setting ['image_base_uri'] . '/head/odp/' . $img;
		}
	}

	if ($image_setting)
	{
		$url = $image_setting ['image_base_uri'];
		$cat = $image_setting ['image_cat_arr'] [$type];
		$size || $size = 'odp';
		$p = explode ( '_', $img );

		if ($cat)
		{
			return implode ( '/', array ($url, $cat ['path'], $size, $p [0], $p [1], $img ) );
		}
	}

	return base_url () . 'static/img/' . $type . '_default.png';
}

/**
 * 敏感词检查
 * Create by 2012-12-14
 * @author liuweijava
 * @param string $content
 * @param string $taboo_type 目前有post和user两种，因此taboo_type有'post','user','post,user'三种情况，根据Taboo表的types字段变化
 * @return boolean true=不含敏感词；false=包含敏感词
 */
function check_taboo($content, $taboo_type = '')
{
	global $CI;
	$CI->load->helper ( 'cache' );
	//获得敏感词列表
	$taboos = get_data_ttl ( 'taboos', $taboo_type, 300 );
	if (empty ( $taboos ))
		return true;
	else
	{
		$check_count = 0;
		foreach ( $taboos as $k => $t )
		{
			strpos ( $content, $t ['word'] ) !== false && $check_count += 1;
		}
		return ! $check_count ? true : false;
	}
}

/**
 * 转换时间，如：X分钟前，X秒前，X天前。超过7天返回yyyy年mm月dd日 HH:ii
 * Create by 2012-12-19
 * @author liuweijava
 * @param string $str_date
 */
function get_date($str_date)
{
	$now = time () + 8 * 3600;
	$date = strtotime ( $str_date ) + 8 * 3600;
	//计算时间差
	$diff = abs ( $date - $now );
	//差几天
	$diff_day = (( float ) $diff) / (24 * 3600);
	//差几小时
	$diff_hour = (( float ) $diff) / 3600;
	//差几分钟
	$diff_minute = (( float ) $diff) / 60;
	if ($diff_day > 7) //超过7天
		return substr ( $str_date, 0, - 3 );
	elseif ($diff_day >= 1) //超过1天
	return ceil ( $diff_day ) . '天前';
	elseif ($diff_day < 1 && $diff_hour >= 1) //超过或等于1小时
	return floor ( $diff_hour ) . '小时前';
	elseif ($diff_hour < 1 && $diff_minute >= 1) //超过或等于1分钟
	return floor ( $diff_minute ) . '分钟前';
	elseif ($diff > 5) //超过5秒
	return floor ( $diff ) . '秒前';
	else
		return '刚刚';
}
/**
 * curl方式请求数据
 * @param $durl
 * @return unknown_type
 */

function curl_get_contents($durl)
{
	$ch = curl_init ();
	curl_setopt ( $ch, CURLOPT_URL, $durl );
	curl_setopt ( $ch, CURLOPT_TIMEOUT, 30 );
	curl_setopt ( $ch, CURLOPT_USERAGENT, _USERAGENT_ );
	curl_setopt ( $ch, CURLOPT_REFERER, _REFERER_ );
	curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
	$r = curl_exec ( $ch );
	curl_close ( $ch );
	return $r;
}

/**
 * 截取字符串，单位字节
 * Create by 2012-6-19
 * @author liuw
 * @param string $string
 * @param int $strlen
 * @return string
 */
define ( 'CHARSET', 'utf-8' );
function cut_string($string, $length = 60, $dot = '...')
{
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

/**
 * 对ID号格式化
 * @param int $id
 */
function formatid($id)
{
	return intval ( $id );
}

/**
 * 获取显示的频道
 */
function get_display_channels()
{
	$categories = get_inc ( 'newscategory' );
	$channels = array ();
	foreach ( $categories as $key => $value )
	{
		($value ['parentId'] == 0 && $value ['status'] == 1) && $channels [$key] = $value;
	}
	return $channels;
}

/**
 * 转换html
 * @param string $str
 */
function format_html($str)
{
	return nl2br ( htmlentities ( $str ) );
}

/**
 * 获取IP地址
 */
function GetClintIp()
{
	if (isset ( $_SERVER ))
	{
		if (isset ( $_SERVER [HTTP_X_FORWARDED_FOR] ))
		{
			$realip = $_SERVER [HTTP_X_FORWARDED_FOR];
		} elseif (isset ( $_SERVER [HTTP_CLIENT_IP] ))
		{
			$realip = $_SERVER [HTTP_CLIENT_IP];
		} else
		{
			$realip = $_SERVER [REMOTE_ADDR];
		}
	} else
	{
		if (getenv ( "HTTP_X_FORWARDED_FOR" ))
		{
			$realip = getenv ( "HTTP_X_FORWARDED_FOR" );
		} elseif (getenv ( "HTTP_CLIENT_IP" ))
		{
			$realip = getenv ( "HTTP_CLIENT_IP" );
		} else
		{
			$realip = getenv ( "REMOTE_ADDR" );
		}
	}
	return $realip;
}

/**
 * 判断是否为手机访问
 */
function is_mobile()
{
	$useragent = $_SERVER ['HTTP_USER_AGENT'];
	return (preg_match ( '/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|ipad|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i', $useragent ) || preg_match ( '/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i', substr ( $useragent, 0, 4 ) ));
}

function check_idnumber($idnumber){
	if(empty($idnumber)){
		return false;
	}
	$idcard = $idnumber;
	$City = array(11=>"北京",12=>"天津",13=>"河北",14=>"山西",15=>"内蒙古",21=>"辽宁",22=>"吉林",23=>"黑龙江",31=>"上海",32=>"江苏",33=>"浙江",34=>"安徽",35=>"福建",36=>"江西",37=>"山东",41=>"河南",42=>"湖北",43=>"湖南",44=>"广东",45=>"广西",46=>"海南",50=>"重庆",51=>"四川",52=>"贵州",53=>"云南",54=>"西藏",61=>"陕西",62=>"甘肃",63=>"青海",64=>"宁夏",65=>"新疆",71=>"台湾",81=>"香港",82=>"澳门",91=>"国外");
	$iSum = 0;
	$idCardLength = strlen($idcard);
	//长度验证
	if(!preg_match('/^\d{17}(\d|x)$/i',$idcard) and !preg_match('/^\d{15}$/i',$idcard))
	{
		return false;
	}
	//地区验证
	if(!array_key_exists(intval(substr($idcard,0,2)),$City))
	{
		return false;
	}
	// 15位身份证验证生日，转换为18位
	if ($idCardLength == 15)
	{
		$sBirthday = '19'.substr($idcard,6,2).'-'.substr($idcard,8,2).'-'.substr($idcard,10,2);
		$d = new DateTime($sBirthday);
		$dd = $d->format('Y-m-d');
		if($sBirthday != $dd)
		{
			return false;
		}
		$idcard = substr($idcard,0,6)."19".substr($idcard,6,9);//15to18
		$Bit18 = getVerifyBit($idcard);//算出第18位校验码
		$idcard = $idcard.$Bit18;
	}
	// 判断是否大于2078年，小于1900年
	$year = substr($idcard,6,4);
	if ($year<1900 || $year>2078 )
	{
		return false;
	}

	//18位身份证处理
	$sBirthday = substr($idcard,6,4).'-'.substr($idcard,10,2).'-'.substr($idcard,12,2);
	$d = new DateTime($sBirthday);
	$dd = $d->format('Y-m-d');
	if($sBirthday != $dd)
	{
		return false;
	}
	//身份证编码规范验证
	$idcard_base = substr($idcard,0,17);
	if(strtoupper(substr($idcard,17,1)) != get_verify_bit($idcard_base))
	{
		return false;
	}
	return $idnumber;
}

// 计算身份证校验码，根据国家标准GB 11643-1999
function get_verify_bit($idcard_base)
{
	if(strlen($idcard_base) != 17)
	{
		return false;
	}
	//加权因子
	$factor = array(7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2);
	//校验码对应值
	$verify_number_list = array('1', '0', 'X', '9', '8', '7', '6', '5', '4', '3', '2');
	$checksum = 0;
	for ($i = 0; $i < strlen($idcard_base); $i++)
	{
		$checksum += substr($idcard_base, $i, 1) * $factor[$i];
	}
	$mod = $checksum % 11;
	$verify_number = $verify_number_list[$mod];
	return $verify_number;
}

// 验证手机号
function check_cellphone($cellphone) {
	return preg_match("/^(13|15|18)[0-9]{9}$/", $cellphone);
}

function nl2p($text) {
  return "<p>" . str_replace("\n", "</p><p>", $text) . "</p>";
}

function p2nl ($str) {
   return preg_replace(array("/<p[^>]*>/iU","/<\/p[^>]*>/iU"),
                       array("","\n"),
                       $str);
}

function google2baidu($lat, $lng) {
	$str = http_request('http://api.map.baidu.com/ag/coord/convert?from=0&to=4&x='.$lng.'&y='.$lat, array(), array(), 'GET', true);
	$data = decode_json($str);

	if (empty($data['error'])) {
		return array('lat' => base64_decode($data['y']), 'lng' => base64_decode($data['x']));
	}

	return array('lat' => '30.659462', 'lng' => '104.065735');
}

