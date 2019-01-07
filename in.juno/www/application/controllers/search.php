<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');   
/**
  * 搜索页面
  * 
  * @Author: chenglin.zhu@gmail.com
  * @Date: 2013-1-11
  */

class Search extends Controller {
	function __construct() {
		parent::__construct();
		
		$this->config->load('config_search');
		$this->search_config = $this->config->item('search');
		$this->search_type = $this->config->item('search_type');
	    $this->assign('search_config', $this->search_config);
	    $this->load->helper('search');
	    $this->load->model('post_model', 'm_post');
	    $this->search_url = $this->search_config['search_url'];
	}
	
	/**
	 * 搜索
	 */
	function index() {
	    // 关键词
		$kw = trim($this->get('kw'));
		$page = formatid($this->get('page'));
		($page < 1) && $page = 1;
		
		$redis = &redis_instance();
		$hot_key = 'hot_keywords';
		$t = trim($this->get('t'));
		
		$kw = htmlspecialchars($kw);
		
		if($kw === '' || $kw === '*') {
		    $kw = '';
		} else {
		    if($this->search_type[$t]) {
		        // 设置查询的$fq
		        $fq = 'type:' . $t;
		    } else {
		        $fq = '';
		        $t = '';
		    }
		    $kw = urldecode($kw);
		    // 把搜索关键词放入redis
		    // 返回名称为key的zset中元素val2的score
		    $score = $redis->zScore($hot_key, $kw);
		    $score = intval($score) + 1;
		    // 在保存回redis
		    $redis->zAdd($hot_key, $score, $kw);
		    
		    $q = array_map('urlencode', explode(' ', $kw));
		    $rows = 10;
		    $start = ($page - 1) * $rows;
		    $query_string = build_query_string($q, $start, $rows, $fq);
		    $data = decode_json(http_request(($this->search_url . '?' . $query_string), array(), array(), 'GET', true));
		    if($data['response']['numFound'] > 0) {
		        foreach($data['response']['docs'] as &$row) {
		            // 判断类型
		            /*
		            类型
		            10：楼盘
		            20：地点
		            30：用户
		            40：POST数据
		            50：新闻
		            */
		            list(, $id) = explode('-', $row['id']);
		            $row['item_id'] = $id;
		            $row['hl_title'] = $data['highlighting'][$row['id']]['title'][0];
		            $row['hl_title'] || ($row['hl_title'] = $row['title']);
		            $row['hl_desc'] = $data['highlighting'][$row['id']]['desc'][0];
		            $row['hl_desc'] || ($row['hl_desc'] = $row['desc']);
		            $row['date'] = idate_format($row['timestamp'], 'u');
		            switch($row['type']) {
		                case 30:
		                    $row['image'] && ($row['photo'] = image_url($row['image'], 'head', 'odp'));
		                    break;
		                case 40:
		                    $row['image'] && ($row['photo'] = image_url($row['image'], 'user', 'odp'));
		                    // 去获取用户信息
		                    $user = get_data('user', $row['uid']);
		                    $row['avatar'] = $user['avatar_m'];
		                    // 去处理标题
		                    preg_match_all('/<em>(.*?)<\/em>/', $row['hl_title'], $arr);
		                    if($arr) {
		                        $row['show_name'] = $row['nikname']?$row['nikname']:$row['username'];
    		                    foreach($arr[1] as $match_str) {
    		                        if(strpos($row['show_name'], $match_str) !== false) {
    		                            $row['show_name'] = str_replace($match_str, '<em>'.$match_str.'</em>', $row['show_name']);
    		                        }
    		                        
    		                        if(strpos($row['placename'], $match_str) !== false) {
    		                            $row['placename'] = str_replace($match_str, '<em>'.$match_str.'</em>', $row['placename']);
    		                        }    		                        
    		                    }
		                    }
		                    break;
		                default:
		                    $row['image'] && ($row['photo'] = image_url($row['image'], 'common', 'odp'));
		            }
		        }
		        unset($row);
		    }
		    $page_url = sprintf('/search?kw=%s&t=%s&page=%%s', $kw, $t);
		    $pagination = pagination($page_url, $data['response']['numFound'], $page, $rows, 10);
		}
		
		// 获取保存的热门搜索前10位
		$hot_list = $redis->zRevRange($hot_key, 0, 9);
		
		$this->assign(compact('kw', 't', 'page', 'data', 'pagination', 'hot_list'));
		$this->display($kw?'result':'index');
	}
	
	/**
	 * 搜索楼盘
	 */
	function building($area = 0, $price = 0, $house_type = 0, $loop = 0, 
	        $selling = 0, $direction = 0, $subway = 0, $fitment = 0, $page = 1, $keywords = '') {
		
		$area = formatid($area);
		$price = formatid($price);
		$house_type = formatid($house_type);
		$selling = formatid($selling);
		$direction = formatid($direction);
		$subway = formatid($subway);
		$fitment = formatid($fitment);
		$page = formatid($page,1);
		$loop = formatid($loop);
		
	    // 获取频道栏目信息
	    $domains = get_inc('domains');
	    // 当前HOST的关联频道栏目ID
	    $domain = $domains[HOST];
	    $categories = get_inc('newscategory');
	    if($domain) {
	        $this->load->helper('channel');
            // 获取频道下的栏目
            $this->assign(array(
                    'sub_cate' => get_channel_category($domain, $categories),
            		'lil_cate' => get_channel_category($domain, $categories, 0),
            		'channel_id' => $domain['id'],
                    'm' => 'index'
                    ));
        }
        $area = formatid($area);
        $price = formatid($price);
        $house_type = formatid($house_type);
        $loop = formatid($loop);
        $selling = formatid($selling);
        $direction = formatid($direction);
        $subway = formatid($subway);
        $fitment = formatid($fitment);
        $page = formatid($page);
        // 根据输入参数条件组合查询
        $rows = 8;
        ($page < 1) && $page = 1;
        $start = ($page - 1) * $rows;
        $keywords = urldecode(trim($keywords)); 
        $q = '';
        if($keywords !== '' || '*' == $keywords) {
            $q = array(
                    '(title:' . urlencode($keywords) . ')',
                    '(address:' . urlencode($keywords) . ')',
                    '(developers:' . urlencode($keywords) . ')'
                    );
        }
        $filter = compact('area', 'price', 'house_type', 'loop',
                'selling', 'direction', 'subway', 'fitment');
        
        $fq = array('type:10');
        foreach($filter as $key => $value) {
            $val = $this->search_config['building'][$key][$value];
            if($value > 0 && $val) {
                $fq[] = $key . ':' . urlencode('price'==$key?price_range($val):$val);
            }
        }
        
        $filter['page'] = $page;
        $filter['keywords'] = $keywords;
        $query_string = search_query_string($q, $fq, $start, $rows);
        $data = decode_json(http_request(($this->search_url . '?' . $query_string), array(), array(), 'GET', true));
        
        if($data['response']['numFound'] > 0) {
        	// 链接团房
//         	$this->tfdb = $this->load->database('tfdb', true);
        	$this->tfdb = $this->db;
            foreach($data['response']['docs'] as &$row) {
                // 去获取地点的信息
                list(, $id) = explode('-', $row['id']);
                if($id) {
                   $place = get_data('place', $id);
                   $row['checkinCount'] = $place['checkinCount'];
                   $row['tipCount'] = $place['tipCount'];
                   $row['photoCount'] = $place['photoCount'];
                   $row['id'] = $id;
                   
                   // 去获取最后一个点评
                   $last_tip = $this->m_post->select_order_id_desc(array('placeId' => $id, 'status < ' => 2, 'type' => 2));
                   if($last_tip) {
                       $user = get_data('user', $last_tip['uid']);
                       $last_tip['name'] = $user['nickname']?$user['nickname']:$user['username'];
                       $last_tip['show_date'] = idate_format(strtotime($last_tip['createDate']), 'u');
                       $num = 96 - dstrlen($last_tip['name']);
                       $last_tip['content'] = cut_string($last_tip['content'], $num);
                       $row['last_tip'] = $last_tip;
                   }
                   
                   // 获取是否有团购信息
                   $house = $this->tfdb->select('has_group, group_status, group_title')
						->get_where('tuanfang.house', array('house_id' => $id))->row_array();
                   if($house['has_group'] && empty($house['group_status'])) {
                   		// 有效的团购
                   		$row['group_title'] = $house['group_title'];
                   }
                }
                $row['photo'] = image_url($row['image'], 'common', 'odp');
            }
            unset($row);
        }
        
        $url = $page_url = '/search/building/area/price/house_type/loop/selling/direction/subway/fitment/page/keywords/';
        $filter_url = array();
        foreach($filter as $key => $value) {
            $uri = $url;
            foreach($filter as $k => $v) {
                if($key == $k) {
                    $uri = str_replace($k, '%s', $uri);
                } else {
                    $uri = str_replace($k, 'page'==$k?1:$v, $uri);
                }
            }
            $filter_url[$key] = $uri;
            $page_url = str_replace($key, 'page'==$key?'%s':$value, $page_url);
        }
        // 计算分页
        $pagination = pagination($page_url, $data['response']['numFound'], $page, $rows, 5);
		$this->assign(array('theme' => 'f', 
		        'c_name' => 'channel',
		        'data' => $data,
		        'pagination' => $pagination,
		        'filter' => $filter,
		        'filter_url' => $filter_url));
		$this->display('building');
	}
}