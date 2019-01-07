<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');   
/*
 * 缓存的一些操作
 * 
 * Author: piglet chenglin.zhu@gmail.com
 * Created on 2012-12-16
 */

class Cache extends Controller {
    /**
     * 更新缓存
     * @param 时间戳 $timestamp
     * @param 验证签名 $sign
     */
    function clear_cache($timestamp = 0, $sign = '', $do = '', $name= '', $id = 0) {
        $timestamp = intval($timestamp);
        $sign = trim($sign);
        if(empty($timestamp) || empty($sign)) {
            // 
            die();
        }
        
        $_sign = md5($timestamp . $this->config->item('sign_key'));
        ($_sign != $sign) && die();
        
        // 获取参数
        $do = trim($do);
        if('data' == $do) {
            // 主要用于更新redis下的缓存
            $name = trim($name);
            $id = intval($id);
            ($name && $id) && @get_data($name, $id, true);
        } elseif('inc' == $do) {
            // 清空inc某个文件的单独缓存
            @unlink(FRAMEWORK_PATH . 'data/inc/cache_' . trim($name) . '.inc.php');
        } else {
            // 清空data下的所有缓存
            $cache_path = FRAMEWORK_PATH . 'data/';
            $cache_dir = dir($cache_path);
            while(false != ($f = $cache_dir->read())) {
                if('.' != $f && '..' != $f && !is_dir($f)) {
                    $d = dir($cache_path . $f);
                    while(false != ($sf = $d->read())) {
                        if('.' != $sf && '..' != $sf && !is_dir($sf)) {
                            unlink($cache_path . $f . '/' . $sf);
                        }
                    }
                    $d->close();
                }
            }
            $cache_dir->close();
        }
        // {"statusCode":"200","message":"\u7f13\u5b58\u66f4\u65b0\u6210\u529f"}
        $this->echo_json(array('statusCode' => 200, 'message' => '网站缓存清空成功'));
    }
}
