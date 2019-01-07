<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/*
 * memcached缓存
 *
 * Author: piglet chenglin.zhu@gmail.com
 * Created on 2012-1-29
 */

class MemcachedCache implements ICache {
    static $_cache;
    var $_memcached;
    /**
     * 构造函数
     * @param $options 服务器配置信息 array
     * 如：
     * array(
     *      array('127.0.0.1', 11211, 1),
     *      array('192.168.1.1', 11211, 1)
     * )
     */
    function __construct($options) {
        $this->_memcached = new Memcached();
        $this->_memcached->addServers($options);
        // 关闭压缩，因为之前遇到问题，在保存JSON字符串进去的时候，超过2000个字符，取出来就变成乱码了，
        $this->_memcached->setOption(Memcached::OPT_COMPRESSION, false);
    }

    /**
     * 获取对象的单例
     */
    function & getInstance($options) {
        return self::$_cache ? self::$_cache : self::$_cache = new MemcachedCache($options);
    }

    /**
     * 设置缓存
     * @param $key 键值
     * @param $value 值 string
     * @param $expire 过期时间 秒
     */
    function set($key, $value, $expire = 0) {
        $this->_memcached->set($key, $value, $expire);
    }

    /**
     * 设置缓存
     * @param $key 键值
     * @param $value 值 array
     * @param $expire 过期时间 秒
     */
    function setArr($key, $value, $expire = 0) {
        $this->set($key, serialize($value), $expire);
    }

    /**
     * 得到缓存
     * @param $key 键值
     * @return 返回值 string
     */
    function get($key) {
        return $this->_memcached->get($key);
    }

    /**
     * 得到缓存
     * @param $key 键值
     * @return 返回值 array
     */
    function getArr($key) {
        return unserialize($this->get($key));
    }

    /**
     * 删除缓存
     * @param $key 键值
     */
    function del($key) {
        $this->_memcached->delete($key);
    }

    /**
     * 清楚所有缓存
     */
    function clear() {
        $this->_memcached->flush();
    }

}
