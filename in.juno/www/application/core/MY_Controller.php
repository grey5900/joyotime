<?php
if (!defined('BASEPATH'))
	exit('No direct script access allowed');
/**
 * Controller 基类
 *
 * Author: piglet chenglin.zhu@gmail.com
 * Created on 2012-11-7
 */

abstract class MY_Controller extends CI_Controller {
	var $template;
	// 登录
	var $auth = array();
	// Class name
	var $_c;
	// Method name
	var $_m;
	// Directory name
	var $_d;

	function __construct() {
		parent::__construct();
		// 初始化属性
		$this->_m = $this->router->method;
		$this->_c = $this->router->class;
		$this->_d = $this->router->directory;

		$redirect_c = $this->config->item('redirect_c');
		if($redirect_c[$this->_c] && 'GET' == $_SERVER['REQUEST_METHOD'] && HOST != $redirect_c[$this->_c]) {
			// 在需要跳转的列表，且当前HOST不是的IN CITY的HOST，所以需要跳转
			redirect(HTTP_SCHEMA_STR . $redirect_c[$this->_c] . $_SERVER['REQUEST_URI'], '', REDIRECT_CODE);
		}

		// 初始化模板
		$this->template->setOptions($this->config->item('template'));
		// 加载语言包
		$language = $this->lang->language;
		$language && $this->set_language($language);

		// 全局配置XSS过滤，如果全局配置了，那么这里的方法就不在使用XSS过滤了
		$this->_enable_xss = (config_item('global_xss_filtering') === TRUE);
		
		// 处理从客户端过来的连接
		$x = $this->_handle_client_request();
		
		if ($x) {
			$auth = $this->cookie('auth');
		}
		if($auth) {
			// 有用户登录以后的cookie
			// 分析cookie
			list($this->auth['uid'], $this->auth['nickname'], $this->auth['avatar_uri'], ) = explode("\t", authcode($auth));
			//头像
			$avatar = pathinfo($this->auth['avatar_uri'], PATHINFO_BASENAME);
			$this->auth['avatar_m'] = image_url($avatar, 'head', 'hmdp');
			$this->assign('auth', $this->auth);
		}

		if(empty($this->auth) && $this->_c == "event_new" && $this->_m == "m_index"){
			$this->auth['uid'] = $this->get("uid") ;
		}
		$passport_config = $this->config->item('passport');
		$this->assign('signout_url', $passport_config['signout_url']);
		$this->assign('sso_logout_url', $passport_config['sso_logout_url']);

		//各Controller的样式表名称
		$this->template->assign('c_name', $this->router->class);
		// 		//页面META信息
		// 		$tmp_t = '%s_%s_title';
		// 		$tmp_k = '%s_%s_keyword';
		// 		$tmp_d = '%s_%s_description';
		// 		$title_k = sprintf($tmp_t, $this->_c, $this->_m);
		// 		$keyword_k = sprintf($tmp_k, $this->_c, $this->_m);
		// 		$description_k = sprintf($tmp_d, $this->_c, $this->_m);
		// 		$title = $this->lang->line('site_current_title').$this->lang->line($title_k);
		// 		$keywords = $this->lang->line('site_current_keyword').$this->lang->line($keyword_k);
		// 		$description = $this->lang->line('site_current_description').$this->lang->line($description_k);
		// 		$this->template->assign(compact('title', 'keywords', 'description'));
		//第三方登录URL
		$api_uri = $this->config->item('api')['url'];
		$this->template->assign('api_uri', $api_uri);

		$this->in_host = $this->config->item('in_host');

		$this->_tables = $this->config->item('tables');
		// 获取频道
		$this->assign('display_channels', get_display_channels());
		
		$this->assign('in_host', $this->config->item('in_host'));

		$this->assign('version', $this->config->item('version'));
	}

	private function _handle_client_request() {
		/*
		 * --------------------------------------------------
		* 终于等到用上了。2013.08.01
		* --------------------------------------------------
		*/
		// 先去判断是否为客户端通过token访问过来
		$header = $this->input->request_headers(true);
		// X-Incd20-Auth
		// ”接口授权控制“中，用于验证用户身份的md5值。
		// X-INID
		// 用户的IN成都ID
		// X-ATX
		// 对应”接口授权控制“中的tx
		$h = array();
		foreach($header as $key => $value) {
			$h[strtolower($key)] = $value;
		}

		
		$token = trim($h['x-incd20-auth']);
		$uid = trim($h['x-inid']);
		$tx = trim($h['x-atx']);
		if (isset($h['x-user-agent'])) {
			// 客户端过来的
			if($token && $uid && isset($h['x-atx'])) {
				// 如果都存在那么是从客户端过来的
				$token_password = $this->config->item('token_password');
				$token_str = strtoupper(md5($token_password[$tx] . $uid));
	
				if($token_str == $token) {
					// TOKEN相同，那么把这个用户登陆了
					$this->auth['uid'] = $uid;
					$user = get_data('user', $uid);
	
					$this->auth['nickname'] = $user['nickname']?$user['nickname']:$user['username'];
					$this->auth['avatar_uri'] = $user['avatar'];
					//保存到cookie
					$auth = $this->auth['uid'] . "\t" . $this->auth['nickname'] . "\t" . $this->auth['avatar_uri'] . "\t";
	
					set_cookie('auth', authcode($auth, 'ENCODE'), 0);
				}
			} else {
				delete_cookie('auth');
				return false;
			}
		}
		
		return true;
	}

	/**
	 * 设置模板的变量内容
	 * @param $key mixed
	 * @param $value
	 */
	function assign($key, $value = null) {
// 		list($key, $value) = pre_handle($key, $value);
// 		list(, $key, $value) = pre_google2baidu(null, $key, $value);
		$this->template->assign($key, $value);
	}

	/**
	 * 显示模板
	 * @param $t_name 模板名称
	 * @param $group 模板组
	 */
	function display($t_name, $group = '') {
		$group = empty($group) ? $this->router->class : $group;

		// 去设置SEO部分的逻辑
		// 三个固定的变量$this->title $this->keywords $this->description
		// 去获取当前配置的SEO信息
		$format_title = $this->lang->line(join('_', array($this->_c, $this->_m, 'title')));
		$format_keywords = $this->lang->line(join('_', array($this->_c, $this->_m, 'keywords')));
		$format_description = $this->lang->line(join('_', array($this->_c, $this->_m, 'description')));

		$format_title = $format_title?$format_title:($this->title?$this->lang->line('seo_default_title'):$this->lang->line('seo_default_empty_title'));
		$format_keywords = $format_keywords?$format_keywords:($this->keywords?$this->lang->line('seo_default_keywords'):$this->lang->line('seo_default_empty_keywords'));
		$format_description = $format_description?$format_description:($this->description?$this->lang->line('seo_default_description'):$this->lang->line('seo_default_empty_description'));

		// 去判断HOST是否为in_host的域名
		if(strpos(HOST, $this->in_host) !== false) {
			$in_host_title = $this->lang->line('in_host_title');
			// IN网站
			$format_title = strpos($format_title, '%s')!==false?($format_title . ' - ' . $in_host_title):sprintf('%s - '. $format_title, $in_host_title);
			$format_keywords = strpos($format_keywords, '%s')!==false?($format_keywords . ',' . $in_host_title):sprintf('%s,' . $format_keywords, $in_host_title);
			$format_description = strpos($format_description, '%s')!==false?($format_description . '，' . $in_host_title):sprintf('%s，' . $format_description, $in_host_title);
		}

		$this->assign(array(
				'title' => strpos($format_title, '%s')!==false?sprintf($format_title, $this->title?$this->title:$in_host_title):$format_title,
				'keywords' => strpos($format_keywords, '%s')!==false?sprintf($format_keywords, $this->keywords?$this->keywords:$in_host_title):$format_keywords,
				'description' => strpos($format_description, '%s')!==false?sprintf($format_description, $this->description?$this->description:$in_host_title):$format_description
		));

		$this->template->display($t_name, $group);
	}

	/**
	 * 返回模板内容
	 * @param $t_name 模板名称
	 * @param $group 模板组
	 * @return 返回 string
	 */
	function fetch($t_name, $group = '') {
		$group = empty($group) ? $this->router->class : $group;
		return $this->template->fetch($t_name, $group);
	}

	/**
	 * 获取get数据
	 * @param $key
	 * @param $default 默认值
	 * @param bool
	 * @return string
	 */
	function get($key, $default = false, $xss_clean = true) {
		$value = $this->input->get($key, $this->_enable_xss?false:$xss_clean);
		return empty($value)?(false===$default?$value:$default):$value;
	}

	/**
	 * 获取post数据
	 * @param $key
	 * @param $default 默认值
	 * @param bool
	 * @return string
	 */
	function post($key, $default = false, $xss_clean = true) {
		$value = $this->input->post($key, $this->_enable_xss?false:$xss_clean);
		return empty($value)?(false===$default?$value:$default):$value;
	}

	/**
	 * 获取cookie数据
	 * @param $key
	 * @param bool
	 * @return string
	 */
	function cookie($key, $xss_clean = true) {
		return $this->input->cookie($key, $this->_enable_xss?false:$xss_clean);
	}

	/**
	 * 获取server数据
	 * @param $key
	 * @param bool
	 * @return string
	 */
	function server($key, $xss_clean = true) {
		return $this->input->server($key, $this->_enable_xss?false:$xss_clean);
	}

	/**
	 * 设置语言包
	 */
	function set_language($language) {
		$this->template->set_language($language);
	}

	/**
	 * 是否为POST数据
	 */
	function is_post() {
		return 'POST' == $this->server('REQUEST_METHOD');
	}

	/**
	 * 删除为JSON格式
	 * @param mixed $array
	 */
	function echo_json($array) {
		//     	header("Content-Type: application/json;charset=UTF-8");
		$this->assign('str', encode_json($array));
		$this->display('return', 'common');
	}

	/**
	 * 显示提示信息
	 * @param string $message
	 * @param string $url_forward
	 * @param int $timeout
	 */
	function showmessage($message, $url_forward = '', $timeout = -1) {
		empty($url_forward) && ($url_forward = $_SERVER['HTTP_REFERER']);
		// 如果返回站点为站外。那么跳转到首页
		$url_info = parse_url($url_forward);
		$in_domain = $this->config->item('in_domain');
		if($url_info['host'] && strpos($url_info['host'], $in_domain) === false) {
			$url_forward = 'http://' . $this->config->item('in_host');
		}
		if($this->input->is_ajax_request()) {
			// AJAX访问
			$this->assign(array('str' => encode_json(array('code' => $url_forward, 'message' => $message))));
			$this->display('return', 'common');
		} else {
			$this->assign(compact('message', 'url_forward', 'timeout'));
			$this->display('showmessage', 'common');
		}
	}

	/**
	 * 生成分页条
	 * Create by 2012-5-8
	 * @author liuw
	 * @param string $url，分页跳转链接
	 * @param int $count，数据总长
	 * @param int $page，当前页码
	 * @param int $size，页面数据长度
	 * @param int $page_shown，页码显示长度
	 * @return string，分页条html
	 */
	function paginate($url, $count, $page = 1, $url_arr = FALSE, $size = 20, $page_shown = 7) {
		$paginate = '';
		//计算总页码
		$sumpage = ceil($count / $size);
		$sumpage = $sumpage?$sumpage:1;
		$real_page = $page;
		$page = $page >= $sumpage ? $sumpage : $page;
		if ($sumpage > 1) {
			$attr = $suf_p = array();
			if($url_arr && is_array($url_arr) && !empty($url_arr)){
				foreach($url_arr as $k=>$p){
					if(strpos($k, 'suffix') !== false)
						$suf_p[] = $p;
					else
						$attr[] = $p;
				}
			}
			$attr = !empty($attr) ? implode('/', $attr) : false;
			$suf_p = !empty($suf_p) ? implode('/', $suf_p) : false;
			//上一页
			$paginate .= '<div class="pagination pagination-centered"><ul><li' . ($page == 1 ? ' class="disabled"' : '') . '><a' . ($page > 1 ? ' href="' . $url . '/' . ($attr?($attr . '/'):'') . ($page - 1 <= 0 ? 1 : $page - 1) . '/'.$suf_p.'"' : '') . '>上一页</a></li>';
			//总页码大于1才生成分页条
			if ($sumpage <= $page_shown) {
				//5页以下
				for ($i = 1; $i <= $sumpage; $i++) {
					$paginate .= '<li' . ($i == $page ? ' class="active"' : '') . '><a' . ($i == $page ? '' : ' href="' . $url . '/' . ($attr?($attr . '/'):'') . $i . '/'.$suf_p.'"') . '>' . $i . '</a></li>';
				}
			} else {
				if ($page <= 4) {
					$begin = 1;
					$end = 7;
					$is_more = 1;
				} elseif ($page >= $sumpage - 3) {
					$begin = $sumpage - 6;
					$end = $sumpage;
				} else {
					$begin = $page - 3;
					$end = $page + 3;
					$is_more = 1;
				}
				if($page > 4){
					$is_first = 1;
				}
				if($is_first){
					$paginate .= '<li><a href="' . $url . '/'  . 1 . '"' . '>1</a></li><li><a>...</a></li>';
				}
				for ($i = $begin; $i <= $end; $i++) {
					$paginate .= '<li' . ($i == $page ? ' class="active"' : '') . '><a' . ($i == $page ? '' : ' href="' . $url . '/' . $attr . '/' . $i . '/'.$suf_p.'"') . '>' . $i . '</a></li>';
				}
				if($is_more){
					$paginate .= '<li><a>...</a></li><li><a href="' . $url . '/'  . $sumpage . '"' . '>' . $sumpage . '</a></li>';
				}
			}
			//下一页
			$paginate .= '<li' . ($page >= $sumpage ? ' class="disabled"' : '') . '><a' . ($page + 1 <= $sumpage ? ' href="' . $url . '/' . $attr . '/' . ($page + 1 == $sumpage ? $sumpage : $page + 1) . '/'.$suf_p.'"' : '') . '>下一页</a></li></ul></div>';
		}
		$offset = $size * ($page - 1);
		$this->assign('paginate', $paginate);
		$total_page = $sumpage;
		return compact('size', 'offset', 'total_page', 'page');
	}


	/**
	 * 生成分页条2
	 * Create by 2012-5-8
	 * @author liuw
	 * @param string $url，分页跳转链接
	 * @param int $count，数据总长
	 * @param int $page，当前页码
	 * @param int $size，页面数据长度
	 * @param int $page_shown，页码显示长度
	 * @return string，分页条html
	 */
	function paginate_style2($url, $count, $page = 1, $size = 20, $page_shown = 7) {
		$paginate = '';
		//计算总页码
		$sumpage = ceil($count / $size);
		$sumpage = $sumpage?$sumpage:1;
		$real_page = $page;
		$page = $page >= $sumpage ? $sumpage : $page;
		if ($sumpage > 1) {

			//上一页
			$paginate .= '<div class="pagination pagination-centered">
			<ul>
			<li' . ($page == 1 ? ' class="disabled"' : '') . '><a' . ($page > 1 ? ' href="' . $url . '/' . ($page - 1 <= 0 ? 1 : $page - 1) .'"' : '') . '>上一页</a>
			</li>';
			//总页码大于1才生成分页条
			if ($sumpage <= $page_shown) {
				//设定条数页以下
				for ($i = 1; $i <= $sumpage; $i++) {
					$paginate .= '<li' . ($i == $page ? ' class="active"' : '') . '><a' . ($i == $page ? '' : ' href="' . $url . '/' . $i .'"') . '>' . $i . '</a></li>';
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
					$paginate .= '<li><a href="' . $url . '/'  . 1 . '"' . '>1</a></li><li><a>...</a></li>';
				}
				for ($i = $begin; $i <= $end; $i++) {
					$paginate .= '<li' . ($i == $page ? ' class="active"' : '') . '><a' . ($i == $page ? '' : ' href="' . $url .  '/' . $i .'"') . '>' . $i . '</a></li>';
				}
				if($is_more){
					$paginate .= '<li><a>...</a></li><li><a href="' . $url . '/'  . $sumpage . '"' . '>' . $sumpage . '</a></li>';
				}
			}
			//下一页
			$paginate .= '<li' . ($page >= $sumpage ? ' class="disabled"' : '') . '><a' . ($page + 1 <= $sumpage ? ' href="' . $url .  '/' . ($page + 1 == $sumpage ? $sumpage : $page + 1) . '"' : '') . '>下一页</a></li></ul></div>';
		}
		$offset = $size * ($page - 1);
		$this->assign('paginate', $paginate);
		$total_page = $sumpage;
		return compact('size', 'offset', 'total_page', 'page');
	}
}

/**
 * 需要登录以后使用的功能继承这个类实现
 * @author piggy
 */
abstract class AuthController extends MY_Controller {
	function __construct() {
		parent::__construct();
		if(empty($this->auth)) {
			if($this->_c == "common" && $this->_m == "send"){
				$this->auth['uid'] = $this->post("uid") ;
				if(empty($this->auth['uid'])) $this->showmessage('没有登录，请先登录', "-1");
			}else{
				$this->showmessage('没有登录，请先登录', "-1");
			}
		}
	}
}

/**
 * 不需要登录的功能继承这个类实现
 * @author piggy
 */
abstract class Controller extends MY_Controller {
	function __construct() {
		parent::__construct();
	}
}

/**
 * 预先处理模板变量
 * @param string $key
 * @param string $value
 */
function pre_handle($key, $value = null) {
	if(is_array($key)) {
		foreach($key as $k => $v) {
			if(is_array($v)) {
				list($v, ) = pre_handle($v);
				$key[$k] = $v;
			} else {
				if(in_array($k, array('lat', 'latitude'))) {
					$key['baidu_map']['lat'] = $v;
				} elseif (in_array($k, array('lng', 'longitude'))) {
					$key['baidu_map']['lng'] = $v;
				}
			}
		}
	} else {
		if(in_array($key, array('lat', 'latitude'))) {
			$key['baidu_map']['lat'] = $value;
		} elseif (in_array($key, array('lng', 'longitude'))) {
			$key['baidu_map']['lng'] = $value;
		}
	}
	return array($key, $value);
}

function pre_google2baidu($pre, $key, $value = null) {
	if(is_array($key)) {
		foreach($key as $k => $v) {
			if(is_array($v) && strval($k) != 'baidu_map') {
				list($key, $v, ) = pre_google2baidu($key, $v);
				$key[$k] = $v;
			} elseif($k == 'baidu_map') {
				$a = $key['baidu_map']['lat'];
				$key['baidu_map'] = google2baidu($key['baidu_map']['lat'], $key['baidu_map']['lng']);
				
				isset($key['lat']) && ($key['lat'] = $key['baidu_map']['lat']);
				isset($key['latitude']) && ($key['latitude'] = $key['baidu_map']['lat']);
					
				isset($key['lng']) && ($key['lng'] = $key['baidu_map']['lng']);
				isset($key['longitude']) && ($key['longitude'] = $key['baidu_map']['lng']);
				
				if($pre) {
					isset($pre['lat']) && ($pre['lat'] = $key['baidu_map']['lat']);
					isset($pre['latitude']) && ($pre['latitude'] = $key['baidu_map']['lat']);
					
					isset($pre['lng']) && ($pre['lng'] = $key['baidu_map']['lng']);
					isset($pre['longitude']) && ($pre['longitude'] = $key['baidu_map']['lng']);
				}
				
				unset($key['baidu_map']);
			}
		}
	}
	return array($pre, $key, $value);
}

