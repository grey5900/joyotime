<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/*
 * 缓存的一些操作
 *
 * Author: piglet chenglin.zhu@gmail.com
 * Created on 2012-6-18
 */

/**
 * 获取到缓存数据
 * @param $flush 是否强制刷新
 */
function get_data($name, $type = 'array', $flush = false) {
    $data = get_cache($name, $type);
    if ($data === false || $flush === true) {
        $func = "flush_{$name}_cache";
        $data = $func();
    }

    return $data;
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
    $cache_conf = $GLOBALS['CI']->config->item($type . '_conf');
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
function set_cache($key, $value, $expire=0) {
    $apps_cache = $GLOBALS['CI']->config->item('cache');
    $apps_cache = $apps_cache[$key]['type'];
    $cache = get_cache_obj($apps_cache);
    if (is_array($value)) {
        $cache->setArr($key, $value, $expire);
    } else {
        $cache->set($key, $value, $expire);
    }
}

/**
 * 获取缓存
 * @param $key
 * @param $type 返回类型 array string
 * @return 返回 mixed
 */
function get_cache($key, $type = 'array') {
    $apps_cache = $GLOBALS['CI']->config->item('cache');
    $apps_cache = $apps_cache[$key]['type'];
    $cache = get_cache_obj($apps_cache);
    if ('array' == $type) {
        return $cache->getArr($key);
    } else {
        return $cache->get($key);
    }
}

/**
 * 获取推荐数据
 */
function get_recommend_cache($cache_id) {
    $conf = $GLOBALS['CI']->config->item('recommend_conf');
    $cache_app = get_cache_obj($conf['type']);
    return $cache_app->getArr($cache_id);
}

/**
 * 保存推荐数据
 */
function set_recommend_cache($cache_id, $data, $expire=false) {
    $conf = $GLOBALS['CI']->config->item('recommend_conf');
    $cache_app = get_cache_obj($conf['type']);
    $cache_app->setArr($cache_id, $data, $expire ? $expire : $conf['expire']);
}

/**
 * 更新指定的缓存数据
 * Create by 2012-8-21
 * @author liuw
 * @param array $cache_arr，缓存参数，包含[cache_id]：缓存ID；[cache_data]：缓存数据集合；[cache_type]：缓存类型(file,memcache...)；[expire]：缓存生命周期，默认为5分钟
 */
function update_cache($cache_arr){
	extract($cache_arr);
	$cache_app = get_cache_obj($cache_type);
	$cache_app->setArr($cache_id, $cache_data, $cache_expire ? $cache_expire : 3000);
}

/**
 * 读取指定的缓存数据
 * Create by 2012-8-21
 * @author liuw
 * @param string $cache_type，缓存类型
 * @param string $cache_id，缓存ID
 * @return array
 */
function get_cache_data($cache_type, $cache_id){
	$cache_app = get_cache_obj($cache_type);
	return $cache_app->getArr($cache_id);
}

/**
 * 刷新分类缓存
 */
function flush_category_cache() {
    global $CI;
    $category = array();
    $list = $CI->db->select('c.*, pc.parent')
                     ->from('PlaceCategory c, PlaceCategoryShip pc')
                     ->where('pc.child = c.id')
                     ->order_by('c.level', 'asc')
                     ->order_by('c.id', 'asc')
                     ->get()
                     ->result_array();
    foreach ($list as $row) {
        if (!$row['parent'])
            $category[$row['id']] = $row;
        else
            $category[$row['parent']]['child'][$row['id']] = $row;
    }
    
    set_cache('category', $category);
    return $category;
}

/**
 * 图片的缓存配置
 */
function flush_image_setting_cache() {
    $query = $GLOBALS['CI']->db->get_where('AppSetting', array('skey' => 'image'));
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
 * 更新碎片缓存
 */
function flush_fragment_cache() {
    $fragment = $GLOBALS['CI']->db->order_by('orderNo', 'asc')->get('MorrisRecommendFragment')->result_array();
    
    $data = array();
    foreach($fragment as $row) {
        $data[$row['id']] = $row;
    }
    
    set_cache('fragment', $data);
    return $data;
}

