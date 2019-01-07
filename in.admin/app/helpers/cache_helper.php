<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/*
 * 缓存的一些操作
 *
 * Author: piglet chenglin.zhu@gmail.com
 * Created on 2012-2-13
 */

/**
 * 获取角色权限
 * @param $flush 默认为false true：更新缓存
 */

/**
 * 获取到缓存数据
 * @param $flush 是否强制刷新
 */
function get_data($name, $flush = false) {
    $data = get_cache($name);
    if ($data === false || $flush === true) {
        $func = "";
        switch($name) {
            case 'rights_uri' :
            case 'rights_id' :
            case 'menu' :
            case 'act_log' :
                $func = 'flush_rights_menu_cache';
                $arr = $func();
                $data = $arr[$name];
                break;
            case 'taboo_user' :
            case 'taboo_post' :
                $func = 'flush_public_taboo_cache';
                $arr = $func();
                $data = $arr[$name];
                break;
            default :
                $func = "flush_{$name}_cache";
                $data = $func();
        }
    }

    return $data;
}

/**
 * 根据ID号获取信息 内存缓存
 * id可以是数组
 * @param string $name 例如：user post place
 * @param mixed $ids
 * @param bool $flag 强制更新
 * @param string $alias 别名
 * @return mixed
 */
function get_redis($name, $ids, $flag = false, $alias = '') {
    $b = is_array($ids);
    // 将数组中重复的值去掉
    $ids = array_unique($b?$ids:array($ids));
    
    $data = array();
    foreach($ids as $id) {
        $data[$id] = _get_data_by_id($name, $id, $flag, $alias);
    }
    
    // 如果只取一条数据，那么把第一条取出来返回
    return $b?$data:array_shift($data);
}

/**
 * 更新敏感词缓存
 * Create by 2012-5-3
 * @author liuw
 */
function flush_taboo_cache(){
	$taboos = array();
	$CI = &get_instance();
	//查询所有的敏感词
	$query = $CI->db->get('Taboo');
	foreach($query->result_array() as $row){
		$taboos[$row['types']][$row['id']] = $row['word'];
	}
	set_cache('taboo', $taboos);
	return $taboos;
}

/**
 * 公用敏感词
 */
function flush_public_taboo_cache() {
	$CI = &get_instance();
    $taboo_user = $taboo_post = array();
    //查询所有的敏感词
    $query = $CI->db->get('Taboo');
    foreach($query->result_array() as $row){
        $types = explode(',', $row['types']);
        if($types) {
            foreach($types as $type) {
                eval("\$taboo_{$type}[] = '{$row['word']}';");
            }
        }
    }
    set_cache('taboo_user', json_encode($taboo_user));
    set_cache('taboo_post', json_encode($taboo_post));
    return compact('taboo_user', 'taboo_post');
}

/**
 * 更新系统的设置信息
 */
function flush_common_setting_cache() {
	$CI = &get_instance();
    $query = $CI->db->get_where('AppSetting', array('skey' => 'common'));
    $row = $query->result();
    if (empty($row)) {
        $svalue = '';
    } else {
        $svalue = unserialize($row[0]->svalue);
    }
    set_cache('common_setting', $svalue);
    return $svalue;
}

/**
 * 更新积分规则
 */
function flush_point_case_cache() {
	$CI = &get_instance();
    $list = $CI->db->get_where('UserPointCase')->result_array();
    
    $data = array();
    foreach($list as $row) {
        $data[$row['id']] = $row;
    }
    
    set_cache('point_case', $data);
    return $data;
}

/**
 * 图片的缓存配置
 */
function flush_image_setting_cache() {
	$CI = &get_instance();
    $query = $CI->db->get_where('AppSetting', array('skey' => 'image'));
    $row = $query->result();
    if (empty($row)) {
        $svalue = '';
    } else {
        $svalue = unserialize($row[0]->svalue);
        if ($svalue['image_cat']) {
            foreach ($svalue['image_cat'] as $k => $v) {
                $values = explode(';', $v);
                if (count($values) < 4) {
                    // 如果数组不够，就返回。不缓存，错误数据
                    unset($svalue['image_cat'][$k]);
                    continue;
                }
                $arr = array();
                $key = $values[0];
                $arr['name'] = $values[1];
                $arr['path'] = $values[2];
                $types = explode(':', $values[3]);
                
                foreach($types as $type) {
                    // 每个类型定义了类型名和大小
                    list($k, $wh) = explode('=', $type);
                    $arr[$k] = explode('x', $wh);
                }

                $svalue['image_cat_arr'][$key] = $arr;
            }
        }
    }
    set_cache('image_setting', $svalue);
    return $svalue;
}

/**
 * 刷新角色缓存
 */
function flush_role_cache() {
    // 取角色表
    $CI = &get_instance();
    $sql = 'select * from MorrisRole order by id';
    $role_rows = $CI->db->query($sql)->result_array();
    $role = array();
    foreach ($role_rows as $row) {
        $rights = explode(',', $row['rights']);
        $newsRights = explode(',', $row['newsRights']);
        $role[$row['id']] = array(
                'name' => $row['name'],
                'rights' => $rights,
        		'newsRights' => $newsRights
        );
    }
    set_cache('role', $role);
    return $role;
}

/**
 * 刷新权限、菜单缓存
 */
function flush_rights_menu_cache() {
    // 取权限表
    $CI = &get_instance();
    $sql = 'select * from MorrisRights order by depth asc, serialno asc';
    $rights_rows = $CI->db->query($sql)->result_array();
    // 组合数据
    // 菜单
    $menu = array();
    //  地址对应ID号
    $rights_uri = array();
    // ID对应地址
    $rights_id = array();
    // 日志记录
    $act_log = array();
    foreach ($rights_rows as $row) {
        if ($row['ismenu'] == 1) {
            // 是菜单
            $paths = '[' . str_replace(',', '][', substr($row['path'], 2)) . ']';
            eval("\$menu{$paths}=array('id'=>'{$row['id']}', 'name'=>'{$row['name']}', 'uri'=>'{$row['uri']}');");
        }
        if ($row['islog'] == 1) {
            $act_log[$row['uri']] = $row['name'];
        }
        $rights_uri[$row['uri']] = $row['id'];
        $rights_id[$row['id']] = $row['uri'];
    }

    set_cache('rights_uri', $rights_uri);
    set_cache('rights_id', $rights_id);
    set_cache('menu', $menu);
    set_cache('act_log', $act_log);
    return compact('rights_uri', 'rights_id', 'menu', 'act_log');
}

/**
 * 更新碎片缓存
 */
function flush_fragment_cache() {
	$CI = &get_instance();
    $fragment = $CI->db->order_by('orderValue', 'asc')->get('WebRecommendFragment')->result_array();
    
    $data = array();
    foreach($fragment as $row) {
        $data[$row['fid']] = $row;
    }
    
    set_cache('fragment', $data);
    return $data;
}


/**
 * 更新碎片缓存
 * 老的
 */
function flush_morris_fragment_cache() {
	$CI = &get_instance();
    $fragment = $CI->db->order_by('orderNo', 'asc')->get('MorrisRecommendFragment')->result_array();

    $data = array();
    foreach($fragment as $row) {
        $data[$row['id']] = $row;
    }

    set_cache('fragment', $data);
    return $data;
}

/*
 * 机甲用户缓存
 * */
/*function flush_user_cache(){
	$CI = &get_instance();
	$list = $CI->db->order_by('id','asc')->get($CI->_tables['user'])->result_array();
	
	$data = array();
    foreach($list as $row) {
        $data[$row['id']] = $row;
    }

    set_cache('user', $data);
    return $data;
}
*/

/**
 * 更新楼盘缓存
 */
function flush_loupan_cache(){
	$CI = &get_instance();
	$palce_categoryid = 100; //楼盘信息
	$sql = "select p.id,p.placename from Place p left join PlaceOwnCategory pc on p.id=pc.placeId where pc.placeCategoryId=".$palce_categoryid." and status=0";
	
	$list = $CI->db->query($sql)->result_array();
	$data = array();
	$replace = array();
	foreach($list as $row) {
		$data[$row['id']] = strtolower($row['placename']);
		$replace[$row['id']] = "<a href='/place/{$row['id']}' class='place' target='_blank'>".strtolower($row['placename'])."</a>";
	}
	$result = array(
			'data'=>$data,
			'replace'=>$replace
	);
	set_cache('loupan', $result);
    return $data;
}

/**
 * 更新新闻频道缓存
 */
function flush_newscategory_cache() {
	$CI = &get_instance();
	$category_class1 = $CI->db->select("id,parentId,catName,catPath,status,domain")->where(array("parentId"=>0))->order_by("id","asc")->get("WebNewsCategory")->result_array();
	
	$data = array();
	foreach($category_class1 as $row){
		$data[$row['id']] = $row;
	}
	
	get_cate_list_by_class1($data,$data);
	set_cache('newscategory', $data);
    return $data;
}

function get_cate_list_by_class1($data,&$realdata){
	$CI = &get_instance();
	if(!empty($data)){
		$paretIds = array();
		foreach($data as $row){
			$paretIds[] = $row['id'];
		}
		
		$paretList = implode(",",$paretIds);
		
		$child = $CI->db->select("id,parentId,catName,catPath,status")->where( "parentId in (".$paretList.") and status=1" )->order_by("id","asc")->get("WebNewsCategory")->result_array();
		if(!empty($child)){
			$tmp_arr = array();
			foreach($child as $k=>$v){
				$arr = array_filter(explode(",",$v['catPath']));//array_reverse(array_filter(explode(",",$v['catPath'])));
				$varpath = '';
				if(!empty($arr)){
					foreach($arr as $value){
						$varpath.= "[$value]";
					}
					$varpath .= "[{$v[id]}]";
				}
				$varpath ? eval("\$realdata{$varpath} = \$v;") :"";
				$tmp_arr = $data[$v['parentId']];
			}
			get_cate_list_by_class1($child,$realdata);
		}
	}
}

/**
 * 根据缓存类型得到缓存对象
 * @param $type 缓存类型 apc memcached file inc 默认为inc类型
 */

function get_cache_obj($type = 'inc') {
    include_once (FCPATH . APPPATH . 'libraries/cache/ICache.php');
    $type = in_array($type, array(
            'apc',
            'memcached',
            'file',
            'inc'
    )) ? $type : 'inc';
    include_once (FCPATH . APPPATH . 'libraries/cache/drivers/' . $type . '.php');
//    $cache_conf = $GLOBALS['CI']->config->item($type . '_conf');
	$cache_conf = config_item($type . '_conf');
    if ($cache_conf) {
        return call_user_func(array(
                ucfirst($type) . 'Cache',
                'getInstance'
        ), $cache_conf);
    } else {
        return call_user_func(array(
                ucfirst($type) . 'Cache',
                'getInstance'
        ));
    }
}

/**
 * 保存缓存
 * @param $key
 * @param $value
 */
function set_cache($key, $value) {
	$CI = &get_instance();
    $apps_cache = $CI->config->item('cache');
    $apps_cache = $apps_cache[$key]['type'];
    $cache = get_cache_obj($apps_cache);
    if (is_array($value)) {
        $cache->setArr($key, $value);
    } else {
        $cache->set($key, $value);
    }
}

/**
 * 获取缓存
 * @param $key
 * @param $type 返回类型 array string
 * @return 返回 mixed
 */
function get_cache($key, $type = 'array') {
	$apps_cache = config_item('cache');
//    $apps_cache = $GLOBALS['CI']->config->item('cache');
    $apps_cache = $apps_cache[$key]['type'];
    $cache = get_cache_obj($apps_cache);
    if ('array' == $type) {
        return $cache->getArr($key);
    } else {
        return $cache->get($key);
    }
}

function clear_third_cache($type, $do = '', $name = '', $id = '') {
	$CI = &get_instance();
	$dateline = time();
	$sign = md5($dateline . $dateline{$dateline{9}});
	return file_get_contents($CI->config->item($type . '_site') .
			'/cache/clear_cache/' . $dateline . '/' . $sign .
			'/' . $do . '/' . $name . '/' . $id);
}

/**
 * 获取一条数据
 * @param string $name
 * @param bool $flag 强制更新
 * @param int $id
 * @param string $alias 别名
 */
function _get_data_by_id($name, $id, $flag = false, $alias) {
    $redis = redis_instance();
    $alias = $alias?$alias:$id;
    if($flag || empty($redis) || ($value = $redis->hGet($name, $alias)) === false) {
        // 如果redis挂了或者没有获取到
        // 到数据库去命中
        
        $CI =& get_instance();
        $CI->load->helper('cache_func');
        
        $value = @call_user_func('cache_func_' . $name, $id);
        
        if($value !== false) {
            // 把获取的数据保存到redis
            $redis->hSet($name, $alias, encode_json($value));
        }
        
        return $value;
    }
    
    return decode_json($value);
}
