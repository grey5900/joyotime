<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * API访问的一些方法
 * Author: piglet chenglin.zhu@gmail.com
 * Created on 2012-12-6
 */

/**
 * 访问API函数
 * @param string $api_name
 * @param array $req_params
 * @param array $headers
 * @param string $method
 * @param bool $return
 * @return 
 */
function call_api($api_name, $req_params = array(), $method = 'POST', $return = true) {
    $GLOBALS['CI']->config->load('config_api');
    $api = $GLOBALS['CI']->config->item('api');
    $url = $api['url'] . '/private_api' . $api['path'][$api_name];
    $sign = rsa_sign();
    $header = array(
        'sign:'.$sign
    );
    $req_params['sign'] = $sign;
    ($req_params['uid'] || $GLOBALS['CI']->auth['uid']) && ($header[] = 'X-INID:'.($req_params['uid']?$req_params['uid']:($GLOBALS['CI']->auth['uid']?$GLOBALS['CI']->auth['uid']:'')));
    unset($req_params['uid']);
    $rtn = http_request($url, $req_params, $header, $method, $return);
    if($return) {
        return $rtn;
    }
}

/**
 * RSA签名
 */
function rsa_sign() {
    $GLOBALS['CI']->config->load('config_api');
    $return = '';
    $second = number_format(microtime(true) * 1000, 0, '', '');
    $b = openssl_private_encrypt($second, $return, file_get_contents($GLOBALS['CI']->config->item('rsa_private_key_path')));
    return $b ? base64_encode($return) : false;
}

/**
 * 发新回复提醒
 * Create by 2012-12-14
 * @author liuweijava
 * @param array $attr
 */
function send_reply_msg($attr){
	call_api('msg_push_rep', $attr);
}

/**
 * 发送系统消息
 * Create by 2012-12-14
 * @author liuweijava
 * @param int $msg_level 1=收藏；2=赞；3=回复
 * @param int $item_type 内容类型
 * @param int $item_id 内容ID
 * @param int $msg_id SystemMessage表中的记录ID
 * @param array $post POST详情，包含地点名称
 */
function send_sys_msg($msg_level, $item_type, $item_id, $post){
	//不发送地点相关的消息
	if($item_type == 1)
		return false;
	global $CI;
	//未登录不发送
	if(empty($CI->auth['uid']))
		return false;
	$CI->config->load('config_common');
	$CI->config->load('config_tables');
	$tables = $CI->config->item('tables');
	$item_types = array_values($CI->config->item('assert_type'));
	//非允许的内容类型不发送
	if(!in_array($item_type, $item_types))
		return false;
	//无法获取到POST和PLACE不发送
	if(empty($post))
		return false;
	//自己操作自己的POST不发送
	if($CI->auth['uid'] == $post['uid'])
		return false;
	//消息主体
	$type_name = $CI->config->item('type_name');
	switch($msg_level){
		case 2://点评
			$content = lang_message('sys_msg_praise', array($CI->auth['nickname'], $post['placename'], $type_name[$msg_level]));
			break;
		case 3://图片
			$content = lang_message('sys_msg_reply', array($post['placename'], $type_name[$msg_level]));
			break;
	}
	//SystemMessage记录
	$sys_msg = array('recieverId'=>$post['uid'], 'type'=>$item_type, 'content'=>$content, 'itemId'=>$item_id);
	switch($item_type){
		case 1:$sys_msg['relatedHyperLink'] = 'inplace://'.$item_id;break;
		case 2:$sys_msg['relatedHyperLink'] = 'intip://'.$item_id;break;
		case 3:$sys_msg['relatedHyperLink'] = 'inphoto://'.$item_id;break;
		default:break;
	}
	$CI->db->insert($tables['systemmessage'], $sys_msg);
	$msg_id = $CI->db->insert_id();
	if($msg_id){
		//发送系统消息
		call_api('msg_pus_sys', array('sys_msg_id'=>$msg_id));
		return true;
	}else 
		return false;
}
