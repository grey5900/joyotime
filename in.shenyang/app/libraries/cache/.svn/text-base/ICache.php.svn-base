<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/*
 * 缓存
 *
 * Author: piglet chenglin.zhu@gmail.com
 * Created on 2012-1-29
 */

interface ICache {
    /**
     * 设置缓存
     * @param $key 键值
     * @param $value 值 string
     * @param $expire 过期时间 秒
     */
    function set($key, $value, $expire = 0);
    
    /**
     * 设置缓存
     * @param $key 键值
     * @param $value 值 array
     * @param $expire 过期时间 秒
     */
    function setArr($key, $value, $expire = 0);    

    /**
     * 得到缓存
     * @param $key 键值
     * @return 返回值 string
     */
    function get($key);

    /**
     * 得到缓存
     * @param $key 键值
     * @return 返回值 array
     */
    function getArr($key);

    /**
     * 删除缓存
     * @param $key 键值
     */
    function del($key);

    /**
     * 清楚所有缓存
     */
    function clear();
}
