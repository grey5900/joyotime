<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');   
/*
 * 缓存的一些操作
 * 
 * Author: piglet chenglin.zhu@gmail.com
 * Created on 2012-9-14
 */

class Cache extends MY_Controller {
    /**
     * 更新缓存
     */
    function clear_cache($timestamp, $sign) {
        $_sign = md5($timestamp . $this->config->item('sign_key'));
        ($_sign != $sign) && die();
        
        $cache_path = FCPATH . 'data/';
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
        
        // {"statusCode":"200","message":"\u7f13\u5b58\u66f4\u65b0\u6210\u529f"}
        $this->echo_json(array('statusCode' => 200, 'message' => '商家平台缓存清空成功'));
    }
}
