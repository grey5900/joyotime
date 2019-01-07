<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/*
 * apc缓存
 *
 * Author: piglet chenglin.zhu@gmail.com
 * Created on 2012-1-29
 */

class ApcCache implements ICache {
    static $_cache;
    function __construct() {

    }

    /**
     * 获取对象的单例
     */
    function & getInstance() {
        return self::$_cache ? self::$_cache : self::$_cache = new ApcCache();
    }

    /**
     * 设置缓存
     * @param $key 键值
     * @param $value 值 string
     * @param $expire 过期时间 秒
     */
    function set($key, $value, $expire = 0) {
        apc_store($key, $value, $expire);
    }
    
    /**
     * 设置缓存
     * @param $key 键值
     * @param $value 值 array
     * @param $expire 过期时间 秒
     */
    function setArr($key, $value, $expire = 0) {
        $this->set($key, $value, $expire);
    }

    /**
     * 得到缓存
     * @param $key 键值
     * @return 返回值 string
     */
    function get($key) {
        return apc_fetch($key);
    }

    /**
     * 得到缓存
     * @param $key 键值
     * @return 返回值 array
     */
    function getArr($key) {
        return $this->get($key);
    }

    /**
     * 删除缓存
     * @param $key 键值
     */
    function del($key) {
        apc_delete($key);
    }

    /**
     * 清楚所有缓存
     */
    function clear() {
        apc_clear_cache();
    }
}
