<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/*
 * 一些公用方法
 *
 * Author: piglet chenglin.zhu@gmail.com
 * Created on 2012-2-14
 */

/**
 * 执行JS
 * @param $js 执行的JS代码
 * @param $isExit 是否执行完，结束输出
 */
function execjs($js, $isExit = true) {
    echo <<< EOD
    <script type="text/javascript">
    $js
        </script>
EOD;
    $isExit && exit ;
}

/**
 * 页面转向
 * @param $url 跳转地址
 * @param $target 默认本页面跳转，用PHP的header跳转方式，
 * 如果指定框架页面，则用JS跳转方式
 */
function forward($url, $target = 'self') {
    if ("self" == $target) {
        header("Location:" . $url);
    } else {
        execjs("{$target}.location.href=\"{$url}\";", false);
    }
    exit(0);
}

/**
 * 弹出窗口
 */
function open_window($url) {
    execjs("window.open(\"{$url}\");");
}

/**
 * 弹出JS的alert窗口
 * @param $msg 消息内容
 * @param $url 跳转地址
 */
function alert($msg, $url = '') {
    header('Content-type:text/html; charset=' . $GLOBALS['CI']->config->item('charset'));
    $js = "alert('$msg');";
    if ($url) {
        // 需要跳转
        $js .= "window.location.href=\"{$url}\";";
    }
    execjs($js);
}

/**
 * 清除一些特殊字符
 */
function dhtmlspecialchars($string) {
    if (is_array($string)) {
        foreach ($string as $key => $val) {
            $string[$key] = dhtmlspecialchars($val);
        }
    } else {
        $string = str_replace(array(
                '&',
                '"',
                '<',
                '>'
        ), array(
                '&amp;',
                '&quot;',
                '&lt;',
                '&gt;'
        ), $string);
        if (strpos($string, '&amp;#') !== false) {
            $string = preg_replace('/&amp;((#(\d{3,5}|x[a-fA-F0-9]{4}));)/', '&\\1', $string);
        }
    }
    return $string;
}

/**
 * 类似site_url只是不加上前面的网址
 */
function build_rel($uri) {
    if (is_array($uri)) {
        return implode('_', $uri);
    } else {
        return $uri;
    }
}

/**
 * 生成uri
 */
function build_uri($uri) {
    if (is_array($uri)) {
        return implode('/', $uri);
    } else {
        return $uri;
    }
}

/**
 * 格式化自定义标签信息
 * Create by 2012-3-31
 * @author liuw
 * @param string $message
 * @param array $replace
 * @return string
 */
function formart_tag_msg($message, $replace) {
    $CI = $GLOBALS['CI'];
    $tag = $CI->config->item('tag_message');
    $regex = $contents = array();
    foreach ($replace as $key => $value) {
        $regex[] = "/" . str_replace('tag', $key, $tag) . "/";
        $contents[] = $value;
    }
    $message = preg_replace($regex, $contents, $message);
    return $message;
}

/**
 * 返回图片路径
 * @param $img 图片名称
 * @param $type 类型 用户发的图片放到user下 head放用户头像 common放所有其他的
 * @param $size 分辨率 如:hdp ldp mdp 默认为odp原图
 */
function image_url($img, $type, $size = '') {
	// update 2013/1/9 默认地址前加 /static
    if (strpos($img, 'http://') === 0) {
        return $img;
    }
    $web_site = $GLOBALS['CI']->config->item('web_site');
    if((empty($img) || $img == 'default.png') && $type == 'head') {
        return $web_site . '/static/img/head_default.png';
    }
    global $image_setting;
    if(empty($image_setting)) {
        $image_setting = get_data('image_setting');
    }
    if($type == 'head') {
        foreach(range(0, 75) as $i) {
            $o_img = $i.'.jpg';
            if($img == $o_img) {
                return $image_setting['image_base_uri'] . '/head/odp/' . $o_img;
            }
        }
    }
    if(empty($img) && $type == 'common') {
        return $web_site . '/static/img/icon_default.png';
    }
    // $cache = include FCPATH . './data/inc/inc_image_setting.php';
    // $image_setting = $cache['data'];

    if($image_setting) {
        $url = $image_setting['image_base_uri'];
        $cat = $image_setting['image_cat_arr'][$type];
        $size || $size = 'odp';
        $p = explode('_', $img);
        
        if($cat) {
            return implode('/', array($url, $cat['path'], $size, $p[0], $p[1], $img));
        } else {
            return $web_site . '/static/img/head_default.png';
        }
    } else {
        return $web_site . '/static/img/head_default.png';
    }
    // global $image_setting;
    // empty($image_setting) && $image_setting = get_data('image_setting');
// 
    // if ($image_setting) {
        // $url = $image_setting['image_base_uri'];
        // $cat = $image_setting['image_cat_arr'][$type];
        // $size || $size = 'odp';
        // $p = explode('_', $img);
        // if ($cat) {
            // return implode('/', array(
                    // $url,
                    // $cat['path'],
                    // $size,
                    // $p[0],
                    // $p[1],
                    // $img
            // ));
        // } else {
            // return $url . '/' . $type . '/none.jpg';
        // }
    // } else {
        // return '';
    // }
}

/**
 * 返回
 */
function include_file($uri) {
    $in_sign = in_sign();
    // echo $uri . '/in_sign/' . $in_sign;die();
    return file_get_contents($uri . '/in_sign/' . $in_sign);
}

/**
 * 检查敏感词
 * Create by 2012-5-3
 * @author liuw
 * @param array $data
 * @param string $type,敏感词类型
 * @return $data
 */
function inspaction_taboo($data, $type = 'user') {
    $taboos = get_data('taboo', FALSE);
    $ins_taboo = array();
    foreach ($taboos as $key => $taboo) {
        if (strpos(strtolower($key), $type) !== FALSE) {
            $ins_taboo = array_merge($ins_taboo, $taboo);
        }
    }
    if (!empty($ins_taboo)) {
        //高亮标注敏感词
        $isSensored = FALSE;
        foreach ($ins_taboo as $key => $val) {
            switch($type) {
                case 'user' :
                //用户昵称&签名
                //			if(!empty($data['nickname']) && @strpos($data['nickname'],$val) !== FALSE){
                //				$data['nickname'] = str_replace($val, '<span class="taboo">'.$val.'</span>', $data['nickname']);
                //				$isSensored = TRUE;
                //			}
                //			if(!empty($data['description']) && @strpos($data['description'], $val) !== FALSE){
                //				$data['description'] = str_replace($val, '<span class="taboo">'.$val.'</span>', $data['description']);
                //				$isSensored = TRUE;
                //			}
                    break;
                case 'post' :
                    if (!empty($data['content']) && @strpos($data['content'], $val) !== FALSE) {
                        $data['content'] = str_replace($val, '<span class="taboo">' . $val . '</span>', $data['content']);
                        $isSensored = TRUE;
                    }
                    break;
            }
        }
        //	if($isSensored){
        //		switch($type){
        //			case 'user':$data['isSensored'] = 3;break;
        //			case 'post':break;
        //		}
        //	}
    }
    return $data;
}

/**
 * 远程请求api的接口
 * Create by 2012-4-28
 * @author liuw
 * @param string $api_uri，api接口的访问地址
 * @param string $request_type，http请求类型
 * @param array $attrs，需要传递到api的参数集
 */
function send_api_interface($api_uri, $request_type = 'POST', $attrs = array(), $headers = array(), $conf_key = 'api_domain') {
    global $CI;
    if ($CI->config->item('api_connect')) {
        $api_uri = $CI->config->item($conf_key) . $api_uri;
        
        //对参数值进行url加密
        $request_str = '';
        // $request_arr = array();
        $attrs['sign'] = rsa_sign();
        return http_request($api_uri, $attrs, $headers, $request_type, true);
        // $request_str = http_build_query($attrs);
        // //CURL
        // $curl = curl_init();
        // if ($request_type == 'GET') {
            // curl_setopt($curl, CURLOPT_URL, $api_uri . '?' . $request_str);
        // } else {//POST
            // curl_setopt($curl, CURLOPT_URL, $api_uri);
            // curl_setopt($curl, CURLOPT_POST, count($attrs));
            // if ($request_str) {
                // curl_setopt($curl, CURLOPT_POSTFIELDS, $request_str);
            // }
        // }
        // curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        // curl_setopt($curl, CURLOPT_BINARYTRANSFER, true);
        // curl_exec($curl);
        // //执行
        // curl_close($curl);
        //关闭句柄
    }
}

/**
 * 访问接口
 * @param 接口uri $api_uri
 * @param 调用方法 $request_type
 * @param 参数 $attrs
 * @param header内容 $header key=>value 数组
 * @param 配置key $conf_key
 */
function request_api($api_uri, $request_type = 'POST', $attrs = array(), $header = array(), $conf_key = 'v3_api') {
    $CI =& get_instance();
    $http_api_uri = $CI->config->item($conf_key) . $api_uri;
    
    // 需要用户验证的会在header里面放入uid
    $headers = array();
    if($header) {
        $uid = $header['uid'];
        if($uid) {
            unset($header['uid']);
            
            $token_password = $CI->config->item('token_password');
            $tx = array_rand($token_password);
            $header['X-ATX'] = $tx;
            $header['X-INID'] = $uid;
            $header['X-Incd20-Auth'] = md5($token_password[$tx] . $uid);
        }
    }
    $urls = parse_url($http_api_uri);
    $api_uri = $urls['path'];
    $ogc = uniqid();
    $timestamp = number_format(microtime(true) * 1000, 0, '.', '');
    $api_request_key = $CI->config->item('api_request_key');
    $i = array_rand($api_request_key);
    $orz = md5($api_uri . $ogc . $api_request_key[$i] . $timestamp);

    $header['X-Ogc'] = $ogc;
    $header['X-Timestamp'] = $timestamp;
    $header['X-Orz'] = $orz;
    $header['X-Real-Url'] = $api_uri;
    
    foreach($header as $k=>$v) {
        $headers[] = "{$k}: {$v}";
    }
    
    return http_request($http_api_uri, $attrs, $headers, $request_type, true);
}

/**
 * http请求
 * @param $url 请求地址
 * @param $req_params 请求参数 GET方法的时候可以传入拼好的字符串
 * @param $headers 请求头
 * @param $method 请求方法 默认GET 
 * @param $return 是否输出
 */
/*function http_request($url, $req_params = array(), $headers = array(), $method = 'GET', $return = false)
{
	$method = strtoupper ( $method );
	$param_isarr = is_array($req_params);
	$ch = curl_init ();
	if ($method == 'GET')
	{
		// GET请求
// 		$url_parsed = parse_url ( $url );
// 		curl_setopt ( $ch, CURLOPT_URL, $url . ($url_parsed ['query'] ? '&' : '?') . http_build_query ( $req_params ) );
		$url_parsed = parse_url($url);
        $url .= ($url_parsed['query']?'&':'?').($param_isarr?http_build_query($req_params):$req_params);
        curl_setopt($ch, CURLOPT_URL, $url);
	} else
	{
		// POST请求
		curl_setopt ( $ch, CURLOPT_URL, $url );
		$param_isarr && curl_setopt($ch, CURLOPT_POST, count($req_params));
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
			curl_setopt ( $ch, CURLOPT_POSTFIELDS, $param_isarr?http_build_query($req_params):$req_params);
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
}*/
function http_request($url, $req_params = array(), $headers = array(), $method = 'GET', $return = false) {
    $method = strtoupper($method);
    
    $param_isarr = is_array($req_params);
    
    $headers || ($headers = array());
    
    $ch = curl_init();
    if($method == 'GET') {
        // GET请求
        $url_parsed = parse_url($url);
        $url .= ($url_parsed['query']?'&':'?').($param_isarr?http_build_query($req_params):$req_params);
        curl_setopt($ch, CURLOPT_URL, $url);
    } else {
        // POST请求
        curl_setopt($ch, CURLOPT_URL, $url);
        $param_isarr && curl_setopt($ch, CURLOPT_POST, count($req_params));
        // 判断参数是否有@开头的，有的话为传文件
        $is_file = false;
        if($param_isarr) {
            foreach($req_params as $key=>$value) {
                if(strpos($value, '@') === 0) {
                    $is_file = true;
                    break;
                }
            }
        }
        if($is_file) {
            // 传文件直接用数组
            curl_setopt($ch, CURLOPT_POSTFIELDS, $req_params);
            $headers[] = 'Content-type: multipart/form-data;charset=UTF-8';
        } else {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $param_isarr?http_build_query($req_params):$req_params);
        }
    }
    // 设定头
    if($headers) {
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    }
    
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
    
    // 设置超时时间
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    
    $data = curl_exec($ch);
    curl_close($ch);
    
    if($return) {
        return $data;
    }else 
    	return true;
}

/**
 * PUSH消息
 */
function push_message($push_api, $post_data) {
    global $api_interface, $CI;
    $CI->lang->load('api');
    $api_interface || $api_interface = $CI->lang->line($push_api);
    // 根据ID推送消息
    send_api_interface($api_interface, 'POST', $post_data);
}

/**
 * 得到网站的连接地址
 * @param $type 连接类型
 * @param $params 参数
 */
function get_web_url($type, $params) {
    $url = $GLOBALS['CI']->config->item('web_url');
    return vsprintf($url[$type], $params);
}

/**
 * magic
 */
function daddslashes($string, $force = 1, $strip = true) {
    if(MAGIC_QUOTES_GPC) {
        return $string;
    } else {
        if (is_array($string)) {
            $keys = array_keys($string);
            foreach ($keys as $key) {
                $val = $string[$key];
                unset($string[$key]);
                $string[addslashes($key)] = daddslashes($val, $force);
            }
        } else {
            $string = addslashes($strip ? stripcslashes($string) : $string);
        }
        return $string;
    }
}

/**
 * 
 */
function dstripslashes($string) {
    if(empty($string)) return $string;
    if(is_array($string)) {
        foreach($string as $key => $val) {
            $string[$key] = dstripslashes($val);
        }
    } else {
        $string = stripslashes($string);
    }
    return $string;
}


/**
 * 生成文件夹
 */
function mkdirs($dir) {
    if (!is_dir($dir)) {
        if (!mkdirs(dirname($dir))) {
            return false;
        }
        if (!@mkdir($dir, 0777)) {
            return false;
        }
    }
    return true;
}

/**
 * 格式化一个时间戳
 */
function dt($timestamp, $format = 'Y-m-d H:i:s') {
    return date($format, intval($timestamp));
}

/**
 * 格式化一个时间
 * @param 时间字符串 $date_str
 * @param 格式化格式 $format
 */
function dt2($date_str, $format = 'Y-m-d H:i:s') {
    return date($format, strtotime($date_str));
}

/**
 * RSA签名
 */
function rsa_sign() {
    global $CI;
    $return = '';
    $second = number_format(microtime(true) * 1000, 0, '', '');
    $b = openssl_private_encrypt($second, $return, file_get_contents($CI->config->item('rsa_private_key_path')));
    return $b ? base64_encode($return) : false;
}

/**
 * 不转换中文的json_encode
 */
function php_json_encode($arr) {
    $json_str = "";
    if (is_array($arr)) {
        $pure_array = true;
        $array_length = count($arr);
        for ($i = 0; $i < $array_length; $i++) {
            if (!isset($arr[$i])) {
                $pure_array = false;
                break;
            }
        }
        if ($pure_array) {
            $json_str = "[";
            $temp = array();
            for ($i = 0; $i < $array_length; $i++) {
                $temp[] = sprintf("%s", php_json_encode($arr[$i]));
            }
            $json_str .= implode(",", $temp);
            $json_str .= "]";
        } else {
            $json_str = "{";
            $temp = array();
            foreach ($arr as $key => $value) {
                $temp[] = sprintf("\"%s\":%s", $key, php_json_encode($value));
            }
            $json_str .= implode(",", $temp);
            $json_str .= "}";
        }
    } else {
        if (is_string($arr)) {
            $json_str = "\"" . $arr . "\"";
        } else if (is_numeric($arr)) {
            $json_str = $arr;
        } else {
            $json_str = "\"" . $arr . "\"";
        }
    }
    return $json_str;
}

/**
 * JSON ENCODE 在5.2中，还没有options参数。所以要显示成中文需要处理下，要不然会被unicode
 * 方法两个 
 * 但是方法 1.里面会把 比如 测试11.2-11.30这样子的 前面的11.2变成.2，不知为何
 * 方法2.先把所有的做urlencode处理，最后再做urldecode处理
 */
// function encode_json($str){
// 	$code = json_encode($str);
// 	return preg_replace("#\\\u([0-9a-f]{4})#ie", "iconv('UCS-2', 'UTF-8', pack('H4', '\\1'))", $code);
// }

function encode_json($str) {
	return urldecode(json_encode(url_encode($str)));
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
 * 
 */
function url_encode($str) {
	if(is_array($str)) {
		foreach($str as $key=>$value) {
			$str[urlencode($key)] = url_encode($value);
		}
	} else {
		$str = urlencode(str_replace(array("\r", "\n", "\t", "\""), array("\\\\r", "\\\\n", "\\\\t", "\\\""), $str));
	}
	
	return $str;
}



/**
 * 检查文本的编码格式
 */
function check_text($str) {
    $array = array('ANSI', 'GBK','UTF-8');
    foreach ($array as $value) {
        if ($str === iconv("UTF-32", $value, iconv($value, "UTF-32", $str))) {
            return $value;
        }
    }
    return 'UTF-8';
}

/**
 * 通过API更新缓存
 * @param $table 需要更新的表明
 * @param $ids 需要更新的表的那些记录ID号
 */
function api_update_cache($table, $ids = array()) {
    global $CI;
   
    $api_domain = $CI->config->item('api_domain');
    $req_params = 'table_name=' . $table;
    
    if(empty($ids)) {
        $api_url = $api_domain . '/private_api/cache/update_table';
    } else {
        $api_url = $api_domain . '/private_api/cache/update';
      
        if(is_array($ids)) {
            // 数组的话
            foreach($ids as $id) {
                $req_params .= '&ids=' . $id;
            }
        } else {
            $req_params .= '&ids=' . $ids;
        }
    }
    $req_params .= '&sign=' . urlencode(rsa_sign());
    
    return http_request($api_url, $req_params, array(), 'GET', true);
}

/**
 * 获取得到内部跳转的验证字符串
 * 参数包括用户ID + 配置的sign_key
 */
function in_sign() {
    return substr(md5(time()), 0, 14) . substr(md5($GLOBALS['CI']->config->item('sign_key')), 0, 18);
}

/**
 * 转换IP
 * @from discuz.net
 */
function convertip($ip, $type = 'full') {
    $return = '';

    if(preg_match("/^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}$/", $ip)) {

        $iparray = explode('.', $ip);

        if($iparray[0] == 10 || $iparray[0] == 127 || ($iparray[0] == 192 && $iparray[1] == 168) || ($iparray[0] == 172 && ($iparray[1] >= 16 && $iparray[1] <= 31))) {
            $return = '- LAN';
        } elseif($iparray[0] > 255 || $iparray[1] > 255 || $iparray[2] > 255 || $iparray[3] > 255) {
            $return = '- Invalid IP Address';
        } else {
            $ipfile = $GLOBALS['CI']->config->item('ipdatafile_' . $type);
            $func = 'convertip_' . $type;
            $return = $func($ip, $ipfile);
        }
    }

    return $return;

}


/**
 * 获取IP地址信息
 * @from discuz.net
 */
function convertip_tiny($ip, $ipdatafile) {
    static $fp = NULL, $offset = array(), $index = NULL;

    $ipdot = explode('.', $ip);
    $ip    = pack('N', ip2long($ip));

    $ipdot[0] = (int)$ipdot[0];
    $ipdot[1] = (int)$ipdot[1];

    if($fp === NULL && $fp = @fopen($ipdatafile, 'rb')) {
        $offset = @unpack('Nlen', @fread($fp, 4));
        $index  = @fread($fp, $offset['len'] - 4);
    } elseif($fp == FALSE) {
        return  '- Invalid IP data file';
    }

    $length = $offset['len'] - 1028;
    $start  = @unpack('Vlen', $index[$ipdot[0] * 4] . $index[$ipdot[0] * 4 + 1] . $index[$ipdot[0] * 4 + 2] . $index[$ipdot[0] * 4 + 3]);

    for ($start = $start['len'] * 8 + 1024; $start < $length; $start += 8) {

        if ($index{$start} . $index{$start + 1} . $index{$start + 2} . $index{$start + 3} >= $ip) {
            $index_offset = @unpack('Vlen', $index{$start + 4} . $index{$start + 5} . $index{$start + 6} . "\x0");
            $index_length = @unpack('Clen', $index{$start + 7});
            break;
        }
    }

    @fseek($fp, $offset['len'] + $index_offset['len'] - 1024);
    if($index_length['len']) {
        return @fread($fp, $index_length['len']);
    } else {
        return '- Unknown';
    }

}

/**
 * 转换IP
 * @from discuz.net
 */
function convertip_full($ip, $ipdatafile) {

    if(!$fd = @fopen($ipdatafile, 'rb')) {
        return '- Invalid IP data file';
    }

    $ip = explode('.', $ip);
    $ipNum = $ip[0] * 16777216 + $ip[1] * 65536 + $ip[2] * 256 + $ip[3];

    if(!($DataBegin = fread($fd, 4)) || !($DataEnd = fread($fd, 4)) ) return;
    @$ipbegin = implode('', unpack('L', $DataBegin));
    if($ipbegin < 0) $ipbegin += pow(2, 32);
    @$ipend = implode('', unpack('L', $DataEnd));
    if($ipend < 0) $ipend += pow(2, 32);
    $ipAllNum = ($ipend - $ipbegin) / 7 + 1;

    $BeginNum = $ip2num = $ip1num = 0;
    $ipAddr1 = $ipAddr2 = '';
    $EndNum = $ipAllNum;

    while($ip1num > $ipNum || $ip2num < $ipNum) {
        $Middle= intval(($EndNum + $BeginNum) / 2);

        fseek($fd, $ipbegin + 7 * $Middle);
        $ipData1 = fread($fd, 4);
        if(strlen($ipData1) < 4) {
            fclose($fd);
            return '- System Error';
        }
        $ip1num = implode('', unpack('L', $ipData1));
        if($ip1num < 0) $ip1num += pow(2, 32);

        if($ip1num > $ipNum) {
            $EndNum = $Middle;
            continue;
        }

        $DataSeek = fread($fd, 3);
        if(strlen($DataSeek) < 3) {
            fclose($fd);
            return '- System Error';
        }
        $DataSeek = implode('', unpack('L', $DataSeek.chr(0)));
        fseek($fd, $DataSeek);
        $ipData2 = fread($fd, 4);
        if(strlen($ipData2) < 4) {
            fclose($fd);
            return '- System Error';
        }
        $ip2num = implode('', unpack('L', $ipData2));
        if($ip2num < 0) $ip2num += pow(2, 32);

        if($ip2num < $ipNum) {
            if($Middle == $BeginNum) {
                fclose($fd);
                return '- Unknown';
            }
            $BeginNum = $Middle;
        }
    }

    $ipFlag = fread($fd, 1);
    if($ipFlag == chr(1)) {
        $ipSeek = fread($fd, 3);
        if(strlen($ipSeek) < 3) {
            fclose($fd);
            return '- System Error';
        }
        $ipSeek = implode('', unpack('L', $ipSeek.chr(0)));
        fseek($fd, $ipSeek);
        $ipFlag = fread($fd, 1);
    }

    if($ipFlag == chr(2)) {
        $AddrSeek = fread($fd, 3);
        if(strlen($AddrSeek) < 3) {
            fclose($fd);
            return '- System Error';
        }
        $ipFlag = fread($fd, 1);
        if($ipFlag == chr(2)) {
            $AddrSeek2 = fread($fd, 3);
            if(strlen($AddrSeek2) < 3) {
                fclose($fd);
                return '- System Error';
            }
            $AddrSeek2 = implode('', unpack('L', $AddrSeek2.chr(0)));
            fseek($fd, $AddrSeek2);
        } else {
            fseek($fd, -1, SEEK_CUR);
        }

        while(($char = fread($fd, 1)) != chr(0))
        $ipAddr2 .= $char;

        $AddrSeek = implode('', unpack('L', $AddrSeek.chr(0)));
        fseek($fd, $AddrSeek);

        while(($char = fread($fd, 1)) != chr(0))
        $ipAddr1 .= $char;
    } else {
        fseek($fd, -1, SEEK_CUR);
        while(($char = fread($fd, 1)) != chr(0))
        $ipAddr1 .= $char;

        $ipFlag = fread($fd, 1);
        if($ipFlag == chr(2)) {
            $AddrSeek2 = fread($fd, 3);
            if(strlen($AddrSeek2) < 3) {
                fclose($fd);
                return '- System Error';
            }
            $AddrSeek2 = implode('', unpack('L', $AddrSeek2.chr(0)));
            fseek($fd, $AddrSeek2);
        } else {
            fseek($fd, -1, SEEK_CUR);
        }
        while(($char = fread($fd, 1)) != chr(0))
        $ipAddr2 .= $char;
    }
    fclose($fd);

    if(preg_match('/http/i', $ipAddr2)) {
        $ipAddr2 = '';
    }
    $ipaddr = "$ipAddr1 $ipAddr2";
    $ipaddr = preg_replace('/CZ88\.NET/is', '', $ipaddr);
    $ipaddr = preg_replace('/^\s*/is', '', $ipaddr);
    $ipaddr = preg_replace('/\s*$/is', '', $ipaddr);
    if(preg_match('/http/i', $ipaddr) || $ipaddr == '') {
        $ipaddr = '- Unknown';
    }

    return iconv('gbk', 'utf-8', $ipaddr);

}

/**
 * 得到一个PHPLOT实例
 */
function get_phplot($w = 600, $h = 400) {
    global $CI;
    
    $CI->load->library('phplot');
    // 创建图表大小
    $plot = new PHPlot($w, $h);
    $plot->SetTTFPath($CI->config->item('font_path'));
    $plot->SetDefaultTTFont($CI->config->item('font_name'));
    
    return $plot;
}

/**
 * 
 */
function plot_x_format($date) {
    return substr($date, -2);
}

/**
 * 截取字符串，默认长度40个汉字
 * Create by 2012-6-19
 * @author liuw
 * @param string $string
 * @param int $strlen
 * @return string
 */
define('CHARSET', 'utf-8');
function cut_string($string, $length = 60, $dot = '...') {
    if (strlen($string) <= $length) {
        return $string;
    }

    $pre = chr(1);
    $end = chr(1);
    $string = str_replace(array(
            '&amp;',
            '&quot;',
            '&lt;',
            '&gt;'
    ), array(
            $pre . '&' . $end,
            $pre . '"' . $end,
            $pre . '<' . $end,
            $pre . '>' . $end
    ), $string);

    $strcut = '';
    if (strtolower(CHARSET) == 'utf-8') {

        $n = $tn = $noc = 0;
        while ($n < strlen($string)) {

            $t = ord($string[$n]);
            if ($t == 9 || $t == 10 || (32 <= $t && $t <= 126)) {
                $tn = 1;
                $n++;
                $noc++;
            } elseif (194 <= $t && $t <= 223) {
                $tn = 2;
                $n += 2;
                $noc += 2;
            } elseif (224 <= $t && $t <= 239) {
                $tn = 3;
                $n += 3;
                $noc += 2;
            } elseif (240 <= $t && $t <= 247) {
                $tn = 4;
                $n += 4;
                $noc += 2;
            } elseif (248 <= $t && $t <= 251) {
                $tn = 5;
                $n += 5;
                $noc += 2;
            } elseif ($t == 252 || $t == 253) {
                $tn = 6;
                $n += 6;
                $noc += 2;
            } else {
                $n++;
            }

            if ($noc >= $length) {
                break;
            }

        }
        if ($noc > $length) {
            $n -= $tn;
        }

        $strcut = substr($string, 0, $n);

    } else {
        for ($i = 0; $i < $length; $i++) {
            $strcut .= ord($string[$i]) > 127 ? $string[$i] . $string[++$i] : $string[$i];
        }
    }

    $strcut = str_replace(array(
            $pre . '&' . $end,
            $pre . '"' . $end,
            $pre . '<' . $end,
            $pre . '>' . $end
    ), array(
            '&amp;',
            '&quot;',
            '&lt;',
            '&gt;'
    ), $strcut);

    $pos = strrpos($strcut, chr(1));
    if ($pos !== false) {
        $strcut = substr($strcut, 0, $pos);
    }
    return $strcut . $dot;
}

/**
 * 统计中文算2个字符，英文就是一个字符
 */
function dstrlen($str) {
    $count = 0;
    for ($i = 0; $i < strlen($str); $i++) {
        $value = ord($str[$i]);
        if ($value > 127) {
            $count++;
            if ($value >= 192 && $value <= 223)
                $i++;
            elseif ($value >= 224 && $value <= 239)
                $i = $i + 2;
            elseif ($value >= 240 && $value <= 247)
                $i = $i + 3;
        }
        $count++;
    }
    return $count;
}

/**
 * 统计，一个中文就是一个字符，英文字符就是一个字符
 */
function cstrlen($str) {
    $count = 0;
    for ($i = 0; $i < strlen($str); $i++) {
        $value = ord($str[$i]);
        if ($value > 127) {
            if ($value >= 192 && $value <= 223)
                $i++;
            elseif ($value >= 224 && $value <= 239)
                $i = $i + 2;
            elseif ($value >= 240 && $value <= 247)
                $i = $i + 3;
        }
        $count++;
    }
    return $count;
}

/**
 * 当前时间的时间戳
 */
function now($type = 0, $t = 0) {
    $t || $t = time();
    
    if($type < 0) {
        return $t;
    }
    
    switch($type) {
        case 1:
            $format = 'Y-m-d';
            break;
        case 2:
            $format = 'Ymd';
            break;
        case 3:
            $format = 'YmdHis';
            break;
        case 4:
            $format = 'H:i:s';
            break;
        default:
            $format = 'Y-m-d H:i:s';
    }
    
    return gmdate($format, $t + 8*3600);
}

/**
 * DISCUZ的加密解密函数
 */
function authcode($string, $operation = 'DECODE', $key = '', $expiry = 0) {
    // 动态密钥长度
    $ckey_length = 4;
    // 密钥
    $key = md5($key ? $key : 'abcdefghijklmnopqrstuvwxyz1357924680');
    // 密钥A用于加密
    $keya = md5(substr($key, 0, 16));
    // 密钥B用于验证
    $keyb = md5(substr($key, 16, 16));
    // 密钥C，生成动态密码部分
    // 解密的时候获取需要解密的字符串前面的$ckey_length长度字符串
    // 加密的时候，用当前时间戳的微妙数md5加密的最后$ckey_length长度字符串
    $keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length) : substr(md5(microtime()), -$ckey_length)) : '';
    // 用于运算的密钥
    $cryptkey = $keya . md5($keya . $keyc);
    $key_length = strlen($cryptkey);

    $string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0) . substr(md5($string . $keyb), 0, 16) . $string;
    $string_length = strlen($string);

    $result = '';
    $box = range(0, 255);

    $rndkey = array();
    for ($i = 0; $i <= 255; $i++) {
        $rndkey[$i] = ord($cryptkey[$i % $key_length]);
    }

    for ($j = $i = 0; $i < 256; $i++) {
        $j = ($j + $box[$i] + $rndkey[$i]) % 256;
        $tmp = $box[$i];
        $box[$i] = $box[$j];
        $box[$j] = $tmp;
    }

    for ($a = $j = $i = 0; $i < $string_length; $i++) {
        $a = ($a + 1) % 256;
        $j = ($j + $box[$a]) % 256;
        $tmp = $box[$a];
        $box[$a] = $box[$j];
        $box[$j] = $tmp;
        $result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
    }

    if ($operation == 'DECODE') {
        if ((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26) . $keyb), 0, 16)) {
            return substr($result, 26);
        } else {
            return '';
        }
    } else {
        return $keyc . str_replace('=', '', base64_encode($result));
    }

}

/**
 * 返回数组的维度
 * @param $array 判断的数据
 * @param $depth_count 默认的深度，如果不为数组，返回0
 * @param $depth_array 保存数据深度的数组，判断最长的数组维度
 */
function array_depth($array, $depth_count = -1, $depth_array = array()) {
    $depth_count++;
    if (is_array($array)) {
        foreach ($array as $key => $value) {
            // 递归进去，知道数组元素不为数组，返回深度保存到判断数组中
            $depth_array[] = array_depth($value, $depth_count);
        }
    } else {
        return $depth_count;
    }
    // 选出数组中最大的一个值，返回
    foreach($depth_array as $value) {
        $depth = $value > $depth ? $value : $depth;
    }
    return $depth;
}
 
/**
 * 格式化json数据格式 
 * {image:['image1', 'image2'], 'title':['title1', 'title2'], 'detail':['detail1', 'detail2']}
 * 相互转换
 * [{image:'image1', title:'title1', detail:'detail1'},{image:'image2', title:'title2', detail:'detail2'}]
 */
function json2json($json) {
    $data = json_decode($json, true);
    $rtn = array();
    if(is_array($data)) {
        foreach($data as $key => $value) {
            if(is_array($value)) {
                foreach($value as $k => $v) {
                    $rtn[$k][$key] = $v;
                }
            }
        }
    }
    return $rtn;
}

/**
 * 给 \r \n 加上斜线
 */ 
function naddslashes($str) {
	return str_replace(array("\r", "\n"), array("\\r", "\\n"), addslashes($str));
}

/**
 * 更新缓存
 * @param string $type web pt bz 
 * @param string $do inc data
 * @param string $name 
 * @param int $id
 */
function update_cache($type, $do, $name, $id = 0) {
    global $CI;
    $dateline = time();
    $sign = md5($dateline . $CI->config->item('sign_key'));
    return file_get_contents($CI->config->item($type . '_site') .
            '/cache/clear_cache/' . $dateline . '/' . $sign .
            '/' . $do . '/' . $name . '/' . $id);
}

/**
 * 转换html
 * @param string $str
 */
function format_html($str) {
	return nl2br($str);
}


function highlight(&$item1, $key){
	$item1 = '<font color=red style="font-size:1.3em"><b>'.$item1.'</b></font>';
}

function get_user_level($exp){
	global $CI;
	$level = $CI->db
        			  ->where("minExp <= ".$exp." and maxExp > ".$exp,null,false)
        			  ->get($CI->_tables['userlevelconstans'])
        			  ->row_array(0);
    return $level;    			  
}


/**
 * 随机一个图片服务器
 */
function image_random_server() {
    $image_config = config_item('img_remote');
    $key = array_rand($image_config['domain']);
        
    return $image_config['domain'][$key];
}

/**
 * 更新表的统计
 * @param 操作表 $table
 * @param 字段 $field
 * @param 条件 $where
 * @param 加减 $plus 默认加 true/false
 */
function update_count($table, $field, $where, $plus = true) {
    global $CI;
    $CI->db2->where($where, null, false)
                ->set($field, sprintf("%s%s1", $field, $plus?'+':'-'), false)
                ->update($CI->_tables[$table]);
    if(empty($plus)) {
        // 这种情况处理下，更新为负数的情况
        $CI->db2->where($where, null, false)->where($field . '<', 0, false)
                    ->update($CI->_tables[$table], array($field => 0));
    }
}

function array_value_to_be_key($array,$key){
	$arr = array();
	
	foreach($array as $k=>$v){
		$arr[$v[$key]] = $v;
	}
	return $arr ;
}

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
