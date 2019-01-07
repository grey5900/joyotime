<?php
if (!defined('BASEPATH'))
	exit('No direct script access allowed');

/**
 * web需要的一些函数
 */
	

/**
 * 访问接口
 * @param 接口uri $api_uri
 * @param 调用方法 $request_type
 * @param 参数 $attrs
 */
function request_api($api_uri, $attrs = array(), $request_type = 'POST' ,$always_return_code = false)
{
	$CI = & get_instance ();
	
	$url = $CI->config->item('api_url') . $api_uri;

	$t = strval(TIMESTAMP);
	$dc = $CI->config->item('dc');
	$k = md5(md5($dc).md5($t));
	$k = $k . ($k{$t{$t{9}}});
	$headers = array(
		'ov: '. $CI->config->item('ov'),
		'av: '. $CI->config->item('av'),
		'dt: '. $CI->config->item('dt'),
		'dc: '. $dc,
		'dp: '. $CI->config->item('dp'),
		't: '. $t,
		'k: '. $k,
	);
	
	$data = http_request ($url, $attrs, $headers, $request_type, true, true);
	$header_body = explode("\r\n\r\n", $data);
	$headers = explode("\r\n", $header_body[0], 2);
	preg_match("/HTTP\/1.1 (\d{3}) (.*)/i", $headers[0], $status);
	return $status[1]==200 && !$always_return_code ?$header_body[1]:encode_json(array('status' => $status[1], 'message' => urldecode($status[2])));
}

/**
 * http请求
 * @param $url 请求地址
 * @param $req_params 请求参数
 * @param $headers 请求头 例：array('X-INID: 1', 'uid: 1');
 * @param $method 请求方法 默认GET
 * @param $return 是否输出
 */
function http_request($url, $req_params = array(), $headers = array(), $method = 'GET', $return = false, $return_header = false)
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
	curl_setopt ( $ch, CURLOPT_HEADER, $return_header );
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

function paginate_style2($url, $count, $page = 1, $size = 20, $page_shown = 7 ,$param = array() ) {
		$CI = & get_instance ();
		$paginate = '';
		//计算总页码
		$sumpage = ceil($count / $size);
		$sumpage = $sumpage?$sumpage:1;
		$real_page = $page;
		$page = $page >= $sumpage ? $sumpage : $page;
		$param_string = '';
		if($param){
			
			foreach($param as $row){
				$param_string .= "/".$row;
			}
		}
		
		if ($sumpage > 1) {

			//上一页
			$paginate .= '<div class="model-footer">
			<ul class="pagination pagination">
			<li' . ($page == 1 ? ' class="disabled"' : '') . '><a' . ($page > 1 ? ' href="' . $url . '/' . ($page - 1 <= 0 ? 1 : $page - 1) .$param_string.'"' : '') . '>&laquo;</a>
			</li>';
			//总页码大于1才生成分页条
			if ($sumpage <= $page_shown) {
				//设定条数页以下
				for ($i = 1; $i <= $sumpage; $i++) {
					$paginate .= '<li' . ($i == $page ? ' class="active"' : '') . '><a' . ($i == $page ? '' : ' href="' . $url . '/' . $i .$param_string.'"') . '>' . $i . '</a></li>';
				}
			} else {
				$half_num = ceil($page_shown/2);
				$show_b_a = $half_num - 1;
				if ($page < $half_num) {
					$begin = 1;
					$end = $page_shown;
					$is_more = 1;
				} elseif ($page >= $sumpage - $half_num) {
					$begin = $sumpage - $half_num - 1;
					$end = $sumpage;
				} else {
					$begin = $page - $show_b_a;
					$end = $page + $show_b_a;
					$is_more = 1;
				}
				if($page > $half_num){
					$is_first = 1;
				}

				//page修正
				$begin = $begin < 1 ? 1 : $begin;
				$end = $end > $sumpage ? $sumpage : $end;
				if($is_first){
					$paginate .= '<li><a href="' . $url . '/'  . 1 .$param_string. '"' . '>1</a></li><li><a>...</a></li>';
				}
				for ($i = $begin; $i <= $end; $i++) {
					$paginate .= '<li' . ($i == $page ? ' class="active"' : '') . '><a' . ($i == $page ? '' : ' href="' . $url .  '/' . $i .$param_string. '"') .'>' . $i . '</a></li>';
				}
				if($is_more){
					$paginate .= '<li><a>...</a></li><li><a href="' . $url . '/'  . $sumpage .$param_string. '"' . '>' . $sumpage . '</a></li>';
				}
			}
			//下一页
			$paginate .= '<li' . ($page >= $sumpage ? ' class="disabled"' : '') . '><a' . ($page + 1 <= $sumpage ? ' href="' . $url .  '/' . ($page + 1 == $sumpage ? $sumpage : $page + 1) .$param_string. '"' : '') . '>&raquo;</a></li></ul></div>';
		}
		$offset = $size * ($page - 1);
		$CI->assign('paginate', $paginate);
		$total_page = $sumpage;
		return compact('size', 'offset', 'total_page', 'page');
	}
	