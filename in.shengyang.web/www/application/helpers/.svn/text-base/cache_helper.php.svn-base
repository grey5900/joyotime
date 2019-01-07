<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * 缓存操作的助手类
 * 包括生成  php包含文件的缓存 以及 redis缓存等
 * 
 * php包含文件主要用于保存一些系统的配置信息，数据量小的，不经常修改的，如用户等级、栏目配置、推荐配置等
 * redis用于保存一些基础信息，如用户信息、地点信息、POST信息等，更快捷的把信息查询出来，不用连接数据表
 * ID作为键值
 * 
 * @author chenglin.zhu@gmail.com
 * @date 2012-11-19
 */

/**
 * 获取数据 inc文件
 * @param string $name
 * @param bool $flag 强制更新
 * @return mixed
 */
function get_inc($name, $flag = false) {
    if(empty($name)) {
        return array();
    }
    
    // 这里用global来做的的，但是static有点问题不能定义为 static $$rtn_name的形式(报错)
    // 如果用eval('static $' . $rtn_name . ' = null;'); 这样子，会每次调用eval，导致没法真的静态
    $rtn_name = 'inc_rtn_' . $name;
    
    if(null === $GLOBALS[$rtn_name]) {
        $cache_inc_file = FRAMEWORK_PATH . "data/inc/cache_{$name}.inc.php";
        $GLOBALS[$rtn_name] = @include_once $cache_inc_file;
        
        if(false === $GLOBALS[$rtn_name] || $flag) {
            // 调用获取缓存函数
            $GLOBALS[$rtn_name] = call_user_func('cache_func_' . $name);
            // 保存缓存
            _save_cache($cache_inc_file, $GLOBALS[$rtn_name]);
        }
    }
    
    return $GLOBALS[$rtn_name];
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
function get_data($name, $ids, $flag = false, $alias = '') {
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
 * 获取一个缓存，根据ttl判断数据是否过期更新
 * @param string $name
 * @param mixed $id 这里只能传入单个
 * @param int $ttl 单位:秒  0 不过期   <0 一直都取最新的  >0时间
 * @param bool $flag
 * @param string $alias 别名
 */
function get_data_ttl($name, $id, $ttl = 0, $alias = '') {
    if($ttl > 0) {
        // 需要保存一个过期时间
        // 保存的时间
        $t = get_data('ttl', $name . $id, false, $alias);
        if((TIMESTAMP - $t) > $ttl) {
            // 已经过期
            // 保存最新的时间
            get_data('ttl', $name . $id, true, $alias);
            // 返回更新的数据
            return get_data($name, $id, true, $alias);
        } else {
            return get_data($name, $id, false, $alias);
        }
    } else {
        return get_data($name, $id, $ttl?true:false, $alias);
    }
}

/**
 * 保存缓存
 * @param string $file
 * @param mixed $data
 * @return void
 */
function _save_cache($file, $data) {
    file_put_contents($file, ALLOWED_HEADER . "\n<?php return " . var_export($data, true) . ";");
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
