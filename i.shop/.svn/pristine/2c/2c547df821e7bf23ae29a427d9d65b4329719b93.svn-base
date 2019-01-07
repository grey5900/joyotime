<?php
/*
 * 公用方法
 * 
 * Author: piglet chenglin.zhu@gmail.com
 * Created on 2012-9-13
 */

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
 * 设置cookie
 * @param $var 变量名
 * @param $value 保存值
 * @param $life 保存时间 0 浏览器生命周期 
 * @param $prefix 是否有前缀
 * @param $httponly 是否http请求
 */
function dsetcookie($var, $value = '', $life = 0, $prefix = 1, $httponly = false) {
    $var = ($prefix ? config_item('cookie_pre') : '').$var;
    $_COOKIE[$var] = $value;

    if($value == '' || $life < 0) {
        $value = '';
        $life = -1;
    }
    
    $timestamp = now(-1);
    
    $life = $life > 0 ? $timestamp + $life : ($life < 0 ? $timestamp - 31536000 : 0);
    $path = config_item('cookie_path') . ($httponly && PHP_VERSION < '5.2.0' ? '; HttpOnly' : '');

    $secure = $_SERVER['SERVER_PORT'] == 443 ? 1 : 0;
    if(PHP_VERSION < '5.2.0') {
        setcookie($var, $value, $life, $path, config_item('cookie_domain'), $secure);
    } else {
        setcookie($var, $value, $life, $path, config_item('cookie_domain'), $secure, $httponly);
    }
}

/**
 * 获取cookie
 */
function dgetcookie($var, $prefix = 1) {
    $var = ($prefix ? config_item('cookie_pre') : '').$var;
    return $_COOKIE[$var];
}

/**
 * DISCUZ的加密解密函数
 */
function authcode($string, $operation = 'DECODE', $key = '', $expiry = 0) {
    $ckey_length = 4;
    $key = md5($key ? $key : 'abcdefghijklmnopqrstuvwxyz1357924680');
    $keya = md5(substr($key, 0, 16));
    $keyb = md5(substr($key, 16, 16));
    $keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length) : substr(md5(microtime()), -$ckey_length)) : '';

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
 * 分页条
 * Create by 2012-9-26
 * @author liuw
 * @param string $url，链接地址
 * @param int $count，总长度
 * @param int $page，当前页码
 * @param array $url_params，URL参数集合
 * @param int $size，页长
 * @param int $page_show，显示页数
 * @return array,游标位置和页长
 */
function paginate($url, $count, $page=1, $url_params=array(), $size=20, $page_show=7){
	global $CI;
	$paginate = array();
	
	//计算游标
	$offset = $size * ($page - 1);
	//计算总页码
	$sum_page = ceil($count / $size);
	$page > $sum_page && $page = $sum_page;
	//url
	$url .= !empty($url_params)?'/'.implode('/', $url_params):'';
	
	if($sum_page > 1){
		$paginate[] = '<div class="pagination pagination-centered"><ul>';
		
		//上一页
		$paginate[] = $page > 1 ? sprintf("<li><a href=\"{$url}/%d\">上一页</a></li>", $page-1?$page-1:1) : '<li class="active"><a>上一页</a></li>';
		
		//数字页码
		
		if($sum_page <= $page_show){
			for($i=1;$i<=$sum_page;$i++){
				$paginate[] = $i == $page ? sprintf("<li class=\"active\"><a>%d</a></li>", $page) : sprintf("<li><a href=\"{$url}/%d\">%d</a></li>", $i, $i);
			}
		}else{
			$move = ceil($page_show / 2);
			$m = floor($page_show / 2);
			if ($page <= $move) {
                $begin = 1;
                $end = $page_show;
            } elseif ($page >= $sum_page - $m) {
                $begin = $sum_page - $page_show + 1;
                $end = $sum_page;
            } else {
                $begin = $page - $m;
                $end = $page + $m;
            }

			if($begin > 1) $paginate[] = '<li><a>…</a></li>';
			
			for($i=$begin;$i<=$end;$i++){
				$paginate[] = $i == $page ? sprintf("<li class=\"active\"><a>%d</a></li>", $page) : sprintf("<li><a href=\"{$url}/%d\">%d</a></li>", $i, $i);
			}
			
			if($end < $sum_page) $paginate[] = '<li><a>…</a></li>';
		}
		
		//end
		
		//当前页数/总页数
		$paginate[] = sprintf("<li>%d/%d</li>", $page, $sum_page);
		//下一页
		$paginate[] = $page == $sum_page ? '<li class="active"><a>下一页</a></li>' : sprintf("<li><a href=\"{$url}/%d\">下一页</a></li>", $page+1 >= $sum_page?$sum_page:$page+1);
		
		$paginate[] = '</ul></div>';		
	}
	
	$CI->template->assign('paginate', implode('', $paginate));
	return compact('size', 'offset');	
}

/**
 * 返回图片路径
 * @param $img 图片名称
 * @param $type 类型 用户发的图片放到user下 header放用户头像 common放所有其他的
 * @param $size 分辨率 如:hhdp hldp hmdp 默认为odp原图
 */
function image_url($img, $type, $size = '') {
	global $CI;
    
    if ((empty($img) || $img == 'default.png') && $type == 'head') {
        return base_url() . 'static/img/head_default.png';
    }
    global $image_setting;
    if (empty($image_setting)) {
        $image_setting = get_data('image_setting');
    }
    if ($type == 'head') {
        foreach (range(0, 75) as $i) {
            $o_img = $i . '.jpg';
            if ($img == $o_img) {
                return $image_setting['image_base_uri'] . '/head/odp/' . $o_img;
            }
        }
    }
    if (empty($img) && $type == 'common') {
        return base_url() . 'static/img/icon_default.png';
    }
    if(empty($img) && $type == 'background')
    	return base_url() . 'img/user_header_default.jpg';
    if ($image_setting) {
        $url = $image_setting['image_base_uri'];
        $cat = $image_setting['image_cat_arr'][$type];
        $size || $size = 'odp';
        $p = explode('_', $img);

        if ($cat) {
            return implode('/', array(
                    $url,
                    $cat['path'],
                    $size,
                    $p[0],
                    $p[1],
                    $img
            ));
        }elseif($type === 'background'){
	    	return implode('/', array(
	    		$CI->config->item('pic_host'),
	    		'background',
	    		$size,
	    		$p[0],
	    		$p[1],
	    		$img
	    	));
        } else {
            return base_url() . ($type === 'background' ? 'static/img/user_header_default.jpg':'static/img/head_default.png');
        }
    }elseif($type === 'background'){
        $size || $size = 'odp';
        $p = explode('_', $img);
    	return implode('/', array(
    		$CI->config->item('pic_host'),
    		'background',
    		$size,
    		$p[0],
    		$p[1],
    		$img
    	));
    } else {
        return base_url() . ($type === 'background' ? 'static/img/user_header_default.jpg':'static/img/head_default.png');
    }
}

/**
 * 调用API接口
 */
function send_api_interface($api_uri, $request_type = 'POST', $attrs = array()) {
    global $CI;
    $request_type = strtolower($request_type);

    //对参数值进行url加密
    $request_str = '';
    // $request_arr = array();
    $attrs['sign'] = rsa_sign();
    $request_str = http_build_query($attrs);
    //CURL
    $curl = curl_init();
    if ($CI->auth['id'] || $attrs['uid']) {
        $headers[] = 'X-INID:' . (!empty($CI->auth['id'])?$CI->auth['id']:$attrs['uid']);
        ($attrs['uploaded_file'] || $attrs['file']) && $headers[] = 'Content-type: multipart/form-data;charset=UTF-8';
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        unset($attrs['uid']);
    }
    if ($request_type == 'get') {
    	curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_URL, $api_uri . '?' . $request_str);
    } else {//POST
    	curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_URL, $api_uri);
        curl_setopt($curl, CURLOPT_POST, count($attrs));
        if ($attrs['uploaded_file'] || $attrs['file']) {
            curl_setopt($curl, CURLOPT_POSTFIELDS, $attrs);
        } else {
            curl_setopt($curl, CURLOPT_POSTFIELDS, $request_str);
        }
    }
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_BINARYTRANSFER, true);
    $content = curl_exec($curl);
    //执行
    curl_close($curl);
    //关闭句柄

    return $content;
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
 * 生成缩略图
 * Create by 2012-10-22
 * @author liuw
 * @param string $image
 * @param string $cfg_name
 * @return string
 */
function make_icon($image, $cfg_name, $file_name=''){
	global $CI;
	//获取缩略图尺寸
	$ds = $CI->config->item($cfg_name);
	empty($ds) && $CI->echo_json(array('code'=>999, 'msg'=>'获取图片尺寸配置失败了'));
	//图片物理地址
	$large_image_location = FRAMEWORK_PATH . '/www/data/img/' . str_replace('./', '', $image);
	//生成缩略图
	$CI->load->library('upload_image');
	//先缩放
	list($w, $h, $t) = getimagesize($large_image_location);
	if($w < $ds['w'] || $h < $ds['h'])
		$thumb = $large_image_location;
	else{
		$s = $ds['w'] / $w;
		$CI->upload_image->resizeImage($large_image_location, $w, $h, $s);
		//计算缩略图左上角坐标
		list($w, $h, $t) = getimagesize($large_image_location);
		$start_x = $w > $ds['w'] ? floor(($w - $ds['w']) / 2) : 0;
		$start_y = $h > $ds['h'] ? floor(($h - $ds['h']) / 2) : 0;
		$w < $ds['w'] && $ds['w'] = $w;
		$h < $ds['h'] && $ds['h'] = $h;
		$thumb = $CI->upload_image->resizeThumbNailImage($large_image_location, $large_image_location, $ds['w'], $ds['h'], $start_x, $start_y, 1);
		empty($thumb) && $CI->echo_json(array('code'=>1000, 'msg'=>'生成缩略图失败了'));
	}
	//调用接口把缩略图上传到图片服务器
	$param = array(
		'api' => $CI->lang->line('api_upload_image'),
		'uid' => $CI->auth['id'],
		'has_return' =>true,
		'attr' => array(
			'file' => '@'.$thumb,
			'file_type' => 'common',
			'resolution' => 'odp'
		),
	);
	!empty($file_name) && $param['attr']['file_name'] = $file_name;
	!empty($file_name) && $param['attr']['resolution'] = 'thumb';
	$result = json_decode($CI->call_api($param), true);
	if(intval($result['result_code']) != 0)
		$CI->echo_json(array('code'=>$result['result_code'], 'msg'=>'上传缩略图到图片服务器失败了：'.$result['result_msg']));
	$thumb = $result['file_name'];
	//清理本地图片
//	@unlink($large_image_location);
	return $thumb;
}