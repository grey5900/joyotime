<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/*
 * inc缓存
 * 生成php文件，通过include包含
 *
 * Author: piglet chenglin.zhu@gmail.com
 * Created on 2012-1-29
 */

class IncCache implements ICache {
    static $_cache;
    var $_inc_file;
    /**
     * 构造方法
     * @param $options 属性
     * 如：
     * array(
     *      'dir' => '/tmp/'
     * )
     */
    function __construct($options) {
        foreach ($options as $key => $value) {
            $this->$key = $value;
        }
    }

    /**
     * 获取对象的单例
     */
    function & getInstance($options) {
        return self::$_cache ? self::$_cache : self::$_cache = new IncCache($options);
    }

    /**
     * 设置缓存
     * @param $key 键值
     * @param $value 值 string
     * @param $expire 过期时间 秒
     */
    function set($key, $value, $expire = 0) {
        $this->_set_inc_file($key);
        $fp = fopen($this->_inc_file, 'w+');
        flock($fp, LOCK_EX);
        fwrite($fp, "<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');\r\n return " . var_export(array(
                't' => $expire === 0 ? 31536000 + time() : abs($expire) + time(),
                'data' => $value
        ), TRUE) . ';');
        fclose($fp);
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
        $this->_set_inc_file($key);
        $x = @
        include ($this->_inc_file);
        if ($x && time() < $x['t']) {
            // 如果返回为真，并且还没有达到过期时间
            return $x['data'];
        }
        return false;
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
        $this->_set_inc_file($key);
        @unlink($this->_inc_file);
    }

    /**
     * 清楚所有缓存
     */
    function clear() {
        // 读取目录并删除里面的inc文件
        $dir = opendir($this->dir);
        while (($file = readdir($dir)) !== FALSE) {
            // 判断文件前缀是否为inc_
            if (strpos($file, 'inc_') === 0) {
                @unlink($this->dir . $file);
            }
        }
        closedir($dir);
    }

    /**
     * 设置包含文件的路径
     */
    function _set_inc_file($key) {
        $this->_inc_file = $this->dir . 'inc_' . $key . '.php';
    }

}
