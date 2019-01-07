<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/*
 * 一些网站公用的方法
 *
 * Author: piglet chenglin.zhu@gmail.com
 * Created on 2012-5-3
 */

/**
 * DISCUZ的加密解密函数
 */
function authcode($string, $operation = 'DECODE', $key = '', $expiry = 0) {
    $ckey_length = 4;
    $key = md5($key != '' ? $key : config_item('authcode_key'));
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
 * 使用正则表达式检查内容是否匹配正则表达式
 * Create by 2012-5-4
 * @author liuw
 * @param string $content,待匹配的内容
 * @param string $regex_key,正则表达式的资源key
 * @return boolean,是否匹配
 */
function validate_regex($content, $regex_key) {
    global $CI;
    $regex = $CI->lang->line($regex_key);
    $mixed = preg_match($regex, $content);
    return $mixed ? TRUE : FALSE;
}

/**
 * 返回图片路径
 * @param $img 图片名称
 * @param $type 类型 用户发的图片放到user下 header放用户头像 common放所有其他的
 * @param $size 分辨率 如:hhdp hldp hmdp 默认为odp原图
 */
function image_url($img, $type, $size = '') {
	global $CI;
	//通过接口获取图片
/*	$api=$GLOBALS['CI']->config->item('api_serv').$GLOBALS['CI']->config->item('api_folder').$GLOBALS['CI']->lang->line('api_get_img');
	$param = array('file_name'=>$img, 'file_type'=>$type, 'resolution'=>empty($size)?'odp':$size);
	$result = json_decode(send_api_interface($api, 'GET', $param), true);
	return $result['result_code'] > 0 ? base_url() . 'img/head_default.png' : $result['uri']; */
	
    //M2 Add
    if ((empty($img) || $img == 'default.png') && $type == 'head') {
        return base_url() . 'img/head_default.png';
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
        return base_url() . 'img/icon_default.png';
    }
    if(empty($img) && $type == 'background')
    	return base_url() . 'img/user_header_default.jpg';
    // $cache = include FCPATH . './data/inc/inc_image_setting.php';
    // $image_setting = $cache['data'];
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
        /*    $o_uri = implode('/', array(
                    $url,
                    $cat['path'],
                    'odp',
                    $p[0],
                    $p[1],
                    $img
            ));
            //CURL检查图片是否存在
            $curl = curl_init($uri);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_BINARYTRANSFER, true);
			curl_exec($curl);
			$http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
			curl_close($curl);//关闭curl句柄
			return $http_code !== '200' ? $o_uri : $uri; */
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
            return base_url() . ($type === 'background' ? 'img/user_header_default.jpg':'img/head_default.png');
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
        return base_url() . ($type === 'background' ? 'img/user_header_default.jpg':'img/head_default.png');
    }
}

/**
 *
 * Create by 2012-5-25
 * @author liuw
 * @param mixed $url_attr
 */
function build_url($url_attr) {
    if (is_array($url_attr))
        return '/' . implode('/', $url_attr);
    else
        return '/' . $url_attr;
}

/**
 * 返回完整的cookie索引
 * Create by 2012-5-4
 * @author liuw
 * @param string $key
 * @return string
 */
function full_cookie_key($key) {
    global $CI;
    return $CI->config->item('cookie_prefix') . $key;
}

/**
 * 生成js分页条
 * Create by 2012-5-25
 * @author liuw
 * @param string $url
 * @param int $count
 * @param int $page
 * @param int $size
 * @param int $page_shown
 * @param string $js_func
 */
function js_paginate($url, $count, $page = 1, $size = 20, $page_shown = 7, $js_func) {
    global $CI;
    $paginate = '';
    //计算总页码
    $sumpage = ceil($count / $size);
    $page = $page >= $sumpage ? $sumpage : $page;
    if ($sumpage > 1) {
        $js_func = preg_replace("/@{url}/", $url, $js_func);
        //url
        $href = 'javascript:void(0);';
        $func = $js_func;
        //上一页
        $paginate .= '<div class="pagination pagination-centered"><ul><li' . ($page == 1 ? ' class="disabled"' : '') . '><a' . ($page > 1 ? ' href="' . $href . '" onclick="' . (preg_replace("/@{page}/", ($page - 1 <= 0 ? 1 : $page - 1) . '', $func)) . '"' : '') . '>上一页</a></li>';
        //总页码大于1才生成分页条
        if ($sumpage <= $page_shown) {
            //5页以下
            for ($i = 1; $i <= $sumpage; $i++) {
                $func = $js_func;
                $paginate .= '<li' . ($i == $page ? ' class="active"' : '') . '><a' . ($i == $page ? '' : ' href="' . $href . '" onclick="' . (preg_replace("/@{page}/", $i . '', $func)) . '"') . '>' . $i . '</a></li>';
            }
        } else {
            if ($page <= 4) {
                $begin = 1;
                $end = 7;
            } elseif ($page >= $sumpage - 3) {
                $begin = $sumpage - 6;
                $end = $sumpage;
            } else {
                $begin = $page - 3;
                $end = $page + 3;
            }

            for ($i = $begin; $i <= $end; $i++) {
                $func = $js_func;
                $paginate .= '<li' . ($i == $page ? ' class="active"' : '') . '><a' . ($i == $page ? '' : ' href="' . $href . '" onclick="' . (preg_replace("/@{page}/", $i . '', $func)) . '"') . '>' . $i . '</a></li>';
            }
        }
        //下一页
        $func = $js_func;
        $paginate .= '<li' . ($page >= $sumpage ? ' class="disabled"' : '') . '><a' . ($page + 1 <= $sumpage ? ' href="' . $href . '" onclick="' . (preg_replace("/@{page}/", ($page + 1 == $sumpage ? $sumpage : $page + 1) . '', $func)) . '"' : '') . '>下一页</a></li></ul></div>';
    }
    $per_page_num = $size;
    $offset = $size * ($page - 1);
    $CI->assign('paginate', $paginate);
    return compact('per_page_num', 'offset');
}

/**
 * 生成分页条,页码约定必须放在url的第一个参数位置
 * Create by 2012-5-8
 * @author liuw
 * @param string $url，分页跳转链接
 * @param int $count，数据总长
 * @param int $page，当前页码
 * @param int $size，页面数据长度
 * @param int $page_shown，页码显示长度
 * @return string，分页条html
 */
function paginate($url, $count, $page = 1, $url_arr = FALSE, $size = 20, $page_shown = 7, $page_first = FALSE) {
    global $CI;
    $paginate = '';
    //计算总页码
    $sumpage = ceil($count / $size);
    $real_page = $page;
    $page = $page >= $sumpage ? $sumpage : $page;
    if ($sumpage > 1) {
        $attr = $url_arr && is_array($url_arr) && !empty($url_arr) ? implode('/', array_values($url_arr)) : '';
        //上一页
        $paginate .= '<div class="pagination pagination-centered"><ul><li' . ($page == 1 ? ' class="disabled"' : '') . '><a' . ($page > 1 ? ' href="' . $url . '/' . ($page_first ? ($page - 1 <= 0 ? 1 : $page - 1) . '/' . $attr : $attr . '/' . ($page - 1 <= 0 ? 1 : $page - 1)) . '/"' : '') . '>上一页</a></li>';
        //总页码大于1才生成分页条
        if ($sumpage <= $page_shown) {
            //5页以下
            for ($i = 1; $i <= $sumpage; $i++) {
                $paginate .= '<li' . ($i == $page ? ' class="active"' : '') . '><a' . ($i == $page ? '' : ' href="' . $url . '/' . ($page_first ? $i . '/' . $attr : $attr . '/' . $i) . '/"') . '>' . $i . '</a></li>';
            }
        } else {
            if ($page <= 4) {
                $begin = 1;
                $end = 7;
            } elseif ($page >= $sumpage - 3) {
                $begin = $sumpage - 6;
                $end = $sumpage;
            } else {
                $begin = $page - 3;
                $end = $page + 3;
            }

            for ($i = $begin; $i <= $end; $i++) {
                $paginate .= '<li' . ($i == $page ? ' class="active"' : '') . '><a' . ($i == $page ? '' : ' href="' . $url . '/' . ($page_first ? $i . '/' . $attr : $attr . '/' . $i) . '/"') . '>' . $i . '</a></li>';
            }
        }
        //下一页
        $paginate .= '<li' . ($page >= $sumpage ? ' class="disabled"' : '') . '><a' . ($page + 1 <= $sumpage ? ' href="' . $url . '/' . ($page_first ? ($page + 1 == $sumpage ? $sumpage : $page + 1) . '/' . $attr : $attr . '/' . ($page + 1 == $sumpage ? $sumpage : $page + 1)) . '/"' : '') . '>下一页</a></li></ul></div>';
    }
    $per_page_num = $size;
    $offset = $size * ($real_page - 1);
    $CI->assign('paginate', $paginate);
    return compact('per_page_num', 'offset');
}

/**
 * 回复
 * Create by 2012-5-28
 * @author liuw
 * @param int $uid 发回复的用户id
 * @param int $item_id 要回复的内容id
 * @param mixed $reply_id 如果不为假，则是一条回复的id
 * @return array
 */
function reply($uid, $content, $post_id, $reply_id = FALSE) {
    global $CI;
    $post_uid = $post_type = $reply_uid = $reply_user = FALSE;
    //获得post所有者id和post类型
    $sql = 'SELECT uid, type FROM Post WHERE id=? LIMIT 1';
    $rs = $CI->db->query($sql, array($post_id))->first_row('array');
    $post_uid = $rs['uid'];
    $post_type = $rs['type'];
    //如果是回复的回复，获得回复所有者的id
    if ($reply_id !== FALSE) {
        $sql = 'SELECT r.uid, IF(u.nickname IS NOT NULL AND u.nickname != \'\', u.nickname, u.username) AS reply_user FROM PostReply r, User u WHERE u.id=r.uid AND r.id=? LIMIT 1';
        $rs = $CI->db->query($sql, array($reply_id))->first_row('array');
        $reply_uid = $rs['uid'];
        $reply_user = $rs['reply_user'];
    } else {
        $sql = 'SELECT IF(nickname IS NOT NULL AND nickname != \'\', nickname, username) AS reply_user FROM User WHERE id=? LIMIT 1';
        $rs = $CI->db->query($sql, array($post_uid))->first_row('array');
        $reply_user = $rs['reply_user'];
    }
    //保存回复
    $reply = array(
            'uid' => $uid,
            'content' => $content,
            'postId' => $post_id
    );
    if ($reply_id !== FALSE && $reply_uid !== FALSE) {
        $reply['replyUid'] = $reply_uid;
        $reply['replyId'] = $reply_id;
    }
	//关键词检查
	if(check_taboo($content, 'post'))
		$reply['status'] = 2;
	else 
		$reply['status'] = 0;
    $CI->db->insert('PostReply', $reply);
    $id = $CI->db->insert_id();
    if (!$id)
        return array(
                'code' => 0,
                'msg' => $CI->lang->line('reply_faild')
        );
    else {
        //更新回复者的回复数量
        $CI->db->query('UPDATE User SET replyCount=replyCount+1 WHERE id=?', array($uid));
        //更新post的回复数量
        $CI->db->query('UPDATE Post SET replyCount=replyCount+1 WHERE id=?', array($post_id));
        //更新feed的回复数量
        $CI->db->query('UPDATE UserFeed SET replyCount=replyCount+1 WHERE itemId=? AND itemType=?', array(
                $post_id,
                $post_type
        ));
        //发消息给相关的人
        $prms = array();
        if ($uid != $reply_uid) {//不是回复自己
            $prms[] = array(
                    'uid' => $reply_uid,
                    'replyId' => $id
            );
        }
        if ($uid != $post_uid) {//不是回复自己的post
            $prms[] = array(
                    'uid' => $post_uid,
                    'replyId' => $id
            );
        }
        $CI->db->insert_batch('PostReplyMessage', $prms);
        
		if($replay['status'] <= 0){
	        //返回结果
	        return array(
	                'code' => 1,
	                'msg' => preg_replace("/@{reply_user}/", $reply_user, $CI->lang->line('reply_success'))
	        );
		}else{
			//给用户发敏感词的系统通知
			$place = $CI->db->select('Place.placename')->join('Place', 'Place.id=Post.placeId')->where('Post.id', $post_id)->get('Post')->first_row('array');
			$data = array(
				'type'=>23,//回复包含敏感词而被屏蔽
				'recieverId'=>$uid,
				'content'=>format_msg($this->lang->line('sm_has_taboo'), array('place'=>$place['placename'])),
			);
			$CI->db->insert('SystemMessage', $data);
			$msg_id = $this->db->insert_id();
			if($msg_id){
				send_api_interface($api_url.'push/push_system_message', 'POST', array('sys_msg_id'=>$msg_id));
			}
			return array('code'=>1, 'msg'=>$CI->lang->line('reply_has_taboo'));
		}
    }

}

/**
 * 发送系统消息
 * Create by 2012-5-28
 * @author liuw
 * @param array $msg_data，消息封装数组，包含：<div><ul>
 * <li>消息主体(message),</li>
 * <li>接收者id(reciever_id),</li>
 * <li>发送者id(sender_id,可为空，不为空表示发私信),</li>
 * <li>替换内容(replace, 可为空，不为空表示需要格式化message),</li>
 * <li>消息指向的资源(item_id, 可为空，因为私信没有这玩意；item_type, 可为空，必须与item_id一起使用)</li>
 * </ul></div>
 * @return int 消息id
 */
function send_message($msg_data) {
    global $CI;
    $smsgs = $pmsgs = array();
    //格式化消息
    foreach ($msg_data as $key => $datas) {
        $data = array();

        //检查并格式化消息主体
        $message = $datas['message'];
        if (!empty($datas['replace'])) {
            $r_keys = array_keys($datas['replace']);
            $r_vals = array_values($datas['replace']);
            foreach ($r_keys as $k => $rk) {
                $rk = "/@{" . $rk . "}/";
                $r_keys[$k] = $rk;
            }
            $message = preg_replace($r_keys, $r_values, $message);
        }
        $data['content'] = $message;

        if (empty($datas['sender_id'])) {//系统消息
            $data['recieverId'] = $datas['reciever_id'];
            if (isset($datas['item']) && !empty($datas['item']) && !empty($datas['item']['id']) && !empty($datas['item']['type'])) {//附件
                $data['type'] = $datas['item']['type'];
                $data['itemId'] = $datas['item']['id'];
            }
            $smsgs[] = $data;
        } else {//私信
            $data['sender'] = $datas['sender_id'];
            $data['receiver'] = $datas['reciever_id'];
            $pmsgs[] = $data;
        }
    }
    //发系统消息
    if (!empty($smsgs))
        $CI->db->insert_batch('SystemMessage', $smsgs);
    //发私信
    if (!empty($pmsgs))
        $CI->db->insert_batch('UserPrivateMessage', $pmsgs);

}

/**
 * 替换消息内容中的自定义标签
 * Create by 2012-5-16
 * @author liuw
 * @param string $message
 * @param array $replaces
 * @return string
 */
function format_msg($message, $replaces = array()) {
    if (empty($replaces))
        return $message;
    else {
        $tags = $contents = array();
        foreach ($replaces as $key => $val) {
            $tags[] = "/@{" . $key . "}/";
            $contents[] = $val;
        }
        return preg_replace($tags, $contents, $message);
    }
}

/**
 * 把数字月份替换成中文月份
 * Create by 2012-6-7
 * @author liuw
 * @param int $month
 * @return string
 */
function format_month($month) {
    $return = '';
    switch($month) {
        case 1 :
            $return = '一月';
            break;
        case 2 :
            $return = '二月';
            break;
        case 3 :
            $return = '三月';
            break;
        case 4 :
            $return = '四月';
            break;
        case 5 :
            $return = '五月';
            break;
        case 6 :
            $return = '六月';
            break;
        case 7 :
            $return = '七月';
            break;
        case 8 :
            $return = '八月';
            break;
        case 9 :
            $return = '九月';
            break;
        case 10 :
            $return = '十月';
            break;
        case 11 :
            $return = '十一月';
            break;
        case 12 :
            $return = '十二月';
            break;
    }
    return $return;
}

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
        $attrs['uploaded_file'] && $headers[] = 'Content-type: multipart/form-data;charset=UTF-8';
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
        if ($attrs['uploaded_file']) {
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

function get_image_wall($image){
    $apc_cache = get_cache_obj('apc');
    // 先到缓存去读取
    $cache_id = substr($image, 0, strpos($image, '.')).'_path';
    $r = $apc_cache->get($cache_id);
    
    if(empty($r)) {
        // 如果缓存中不存在，那么计算
        // 算出图片路径
        $p = explode('_', $image);
        $pic_path = $GLOBALS['CI']->config->item('pic_path') . '/user/thweb/' . $p[0] . '/' . $p[1];
        $r = $pic_path . '/' . $image;
        $apc_cache->set($cache_id, $r);
    }
    return $r;
}

/**
 * 获取图片的宽度和高度
 * 始终使用thweb图片进行计算。这样子避免出现问题，
 * 因为WEB图始终存在，如果用原图计算，有些可能比较大
 * @param $image 带入图片名称 
 * @param $width 需要返回的图片的宽高比列
 */
function image_wh($image, $width = 200) {
    // $apc_cache = get_cache_obj('apc');
    // // 先到缓存去读取
    // $cache_id = crc32($image) . '_' . $width;
    // $r = $apc_cache->get($cache_id);

    // if (empty($r)) {
        // $r = getjpegsize($image);
        // if ($r) {
            // $r = array(
                    // 'w' => $r[0],
                    // 'h' => $r[1]
            // );
            // $apc_cache->set($cache_id, $r);
        // }
    // }
// 
    // return $r;
    
    $apc_cache = get_cache_obj('apc');
    // 先到缓存去读取
    $cache_id = substr($image, 0, strrpos($image, '.'));
    $r = $apc_cache->get($cache_id);
    
    if(empty($r)) {
        // 如果缓存中不存在，那么计算
        // 算出图片路径
        $p = explode('_', $image);
        $pic_path = $GLOBALS['CI']->config->item('pic_path') . '/user/thweb/' . $p[0] . '/' . $p[1];
        $pic = $pic_path . '/' . $image;
        // 获取图片的高宽
        $image_wh = getjpegsize($pic);
        if($image_wh) {
            // 获取到了宽高
            $r = array(
                'w' => $image_wh[0],
                'h' => $image_wh[1]
            );
            $apc_cache->set($cache_id, $r);
        }
    }
    
    // 再次判断是否有，避免没有获取到
    if(empty($r)) {
        return array();
    } else {
        // 计算需要的比列大小，并返回
        if($width != 200) {
            // 计算新的比列
            $scale = @(floatval($width)/$r['w']);
            $r['w'] = $width;
            $r['h'] = intval($r['h'] * $scale);
        }
        
        return $r;
    }
}

/**
 * 获取JPG图片宽高
 */
function getjpegsize($img_loc) {
  /*  $handle = fopen($img_loc, "rb");
    if (!$handle) {
        return FALSE;
    }
    $new_block = NULL;
    if (!feof($handle)) {
        $new_block = fread($handle, 32);
        $i = 0;
        if ($new_block[$i] == "\xFF" && $new_block[$i + 1] == "\xD8" && $new_block[$i + 2] == "\xFF" && $new_block[$i + 3] == "\xE0") {
            $i += 4;
            if ($new_block[$i + 2] == "\x4A" && $new_block[$i + 3] == "\x46" && $new_block[$i + 4] == "\x49" && $new_block[$i + 5] == "\x46" && $new_block[$i + 6] == "\x00") {
                // Read block size and skip ahead to begin cycling through blocks in search of SOF marker
                $block_size = unpack("H*", $new_block[$i] . $new_block[$i + 1]);
                $block_size = hexdec($block_size[1]);
                while (!feof($handle)) {
                    $i += $block_size;
                    $new_block .= fread($handle, $block_size);
                    if ($new_block[$i] == "\xFF") {
                        // New block detected, check for SOF marker
                        $sof_marker = array(
                                "\xC0",
                                "\xC1",
                                "\xC2",
                                "\xC3",
                                "\xC5",
                                "\xC6",
                                "\xC7",
                                "\xC8",
                                "\xC9",
                                "\xCA",
                                "\xCB",
                                "\xCD",
                                "\xCE",
                                "\xCF"
                        );
                        if (in_array($new_block[$i + 1], $sof_marker)) {
                            // SOF marker detected. Width and height information is contained in bytes 4-7 after this byte.
                            $size_data = $new_block[$i + 2] . $new_block[$i + 3] . $new_block[$i + 4] . $new_block[$i + 5] . $new_block[$i + 6] . $new_block[$i + 7] . $new_block[$i + 8];
                            $unpacked = unpack("H*", $size_data);
                            $unpacked = $unpacked[1];
                            $height = hexdec($unpacked[6] . $unpacked[7] . $unpacked[8] . $unpacked[9]);
                            $width = hexdec($unpacked[10] . $unpacked[11] . $unpacked[12] . $unpacked[13]);
                            return array(
                                    $width,
                                    $height
                            );
                        } else {
                            // Skip block marker and read block size
                            $i += 2;
                            $block_size = unpack("H*", $new_block[$i] . $new_block[$i + 1]);
                            $block_size = hexdec($block_size[1]);
                        }
                    } else {
                        return FALSE;
                    }
                }
            }
        }
    }
    return FALSE; */
	//直接使用getimagesize函数获取图片尺寸
	list($w, $h, $t) = @getimagesize($img_loc);
	empty($w) && $w = false;
	empty($h) && $h = false;
	return !$w && !$h ? array() : array($w, $h);
}

/**
 * 发送系统消息
 * @param $type 1 收藏 2 赞 3回复
 * @param $item_type 连接到的系统消息类型
 * @param $item_id 连接ID
 */
function send_system_message($type, $item_type, $item_id) {
    if($item_type == 1) {
        // PLACE的话不发送
        return;
    }
    global $CI;
    // 用户是否登录
    if(empty($CI->auth['id'])) {
        // 没登陆
        return;
    }
    $types = array_values($CI->config->item('assert_type'));
    if(!in_array($item_type, $types)) {
        // 如果不在里面那么返回
        return;
    }
    
    // 去获取post和place数据
    $p = $CI->db->where(array('p.id' => $item_id))->join('Place pp', 'p.placeId = pp.id', 'inner')->get('Post p')->row_array();
    if(empty($p)) {
        return;
    }
    
    // 比较操作人和接受者是不是一样的
    if($CI->auth['id'] == $p['uid']) {
        // 一样的就返回
        return;
    }
    
    $type_name = $CI->config->item('type_name');
    // 获取content 数据
    switch($type) {
        case 2:
            $line = $CI->lang->line('sys_msg_praise');
            $content = sprintf($line, $CI->auth['name'], $p['placename'], $type_name[$item_type]);
            break;
        case 3:
            $line = $CI->lang->line('sys_msg_reply');
            $content = sprintf($line, $p['placename'], $type_name[$item_type]);
            break;
    }
    
    // 写入系统表
    $data['recieverId'] = $p['uid'];
    $data['type'] = $item_type;
    $data['content'] = $content;
    $data['itemId'] = $item_id;
    //构建链接
    switch($item_type){
    	case 1:$data['relatedHyperLink'] = 'inplace://'.$item_id;break;
    	case 2:$data['relatedHyperLink'] = 'intip://'.$item_id;break;
    	case 3:$data['relatedHyperLink'] = 'inphoto://'.$item_id;break;
    	default:break;
    }

    $CI->db->insert('SystemMessage', $data);
    $id = $CI->db->insert_id();
    if ($id) {
        // 发送系统消息
        $api_url = $CI->config->item('api_serv') . $CI->config->item('api_folder');
        send_api_interface($api_url . 'push/push_system_message', 'POST', array('sys_msg_id' => $id));
    }
}

/**
 * http请求
 * @param $url 请求地址
 * @param $req_params 请求参数
 * @param $headers 请求头
 * @param $method 请求方法 默认GET
 * @param $return 是否输出
 */
function http_request($url, $req_params = array(), $headers = array(), $method = 'GET', $return = false) {
    $method = strtoupper($method);

    $ch = curl_init();
    if ($method == 'GET') {
        // GET请求
        $url_parsed = parse_url($url);
        curl_setopt($ch, CURLOPT_URL, $url . ($url_parsed['query'] ? '&' : '?') . http_build_query($req_params));
    } else {
        // POST请求
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, count($req_params));
        // 判断参数是否有@开头的，有的话为传文件
        $is_file = false;
        foreach ($req_params as $key => $value) {
            if (strpos($value, '@') === 0) {
                $is_file = true;
                break;
            }
        }
        if ($is_file) {
            // 传文件直接用数组
            curl_setopt($ch, CURLOPT_POSTFIELDS, $req_params);
        } else {
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($req_params));
        }
    }
    // 设定头
    if ($headers) {
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    }

    if ($return) {
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
        $data = curl_exec($ch);
    } else {
        curl_exec($ch);
    }
    curl_close($ch);

    if ($return) {
        return $data;
    }
}

/**
 * 检查内容中是否包含敏感词
 * Create by 2012-8-2
 * @author liuw
 * @param string $content
 * @param string $taboo_type
 * @return boolean
 */
function check_taboo($content, $taboo_type='all'){
	global $CI;
	//确定缓存健
	$cache_key = '';
	switch($taboo_type){
		case 'all':$cache_key = 'taboo';break;
		default:$cache_key = 'taboo_'.$taboo_type;break;
	}
	//从缓存中获得相应敏感词，如果没有则从数据库获取
	$CI->load->helper('cache');
	$mem = get_cache_obj('memcached');
	$taboos = json_decode($mem->get($cache_key), true);
	if(empty($taboos) || !is_array($taboos)){
		if($taboo_type !== 'all')
			$CI->db->where("FIND_IN_SET('{$taboo_type}', types)");
		$query = $CI->db->order_by('id', 'asc')->get('Taboo')->result_array();
		foreach($query as $row){
			$taboos[] = $row['word'];
		}
	}
	//检查内容中是否包含敏感词
	$t_count = 0;
	foreach($taboos as $t){
		if(strpos($content, $t) !== FALSE)
			$t_count += 1;
	}
	
	return $t_count > 0 ? true : false;
}

/**
 * 获取用户的自定义备注
 * Create by 2012-8-27
 * @author liuw
 * @param int $uid
 */
function get_my_desc($uid, $m_user){
	global $CI;
	$CI->load->model('universal', 'univ');
	$desc = $CI->univ->get_my_desc($uid);
	
	$d = !empty($desc) && isset($desc[$m_user['uid']]) && $uid!=$m_user['uid'] && !empty($desc[$m_user['uid']]) ? $desc[$m_user['uid']] : $m_user['name'];
	unset($desc);
	
	return $d;
}
