<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/*
 * 缓存操作的类
 *
 * Author: piglet chenglin.zhu@gmail.com
 * Created on 2012-3-15
 */

class Cache extends MY_Controller {
    /**
     * 更新缓存
     */
    function index() {
        $cache_list = $this->config->item('cache');
        if ('POST' == $this->server('REQUEST_METHOD')) {
            // 提交了更新
            $cache_ed = $this->post('cache');
            foreach ($cache_ed as $key) {
                if ('template' == $key) {
                    // 更新模板特殊处理
                    $template = $this->config->item('template');
                    $d = dir($template['compiled_dir']);
                    while(false != ($f = $d->read())) {
                        if('.' != $f && '..' != $f && !is_dir($f)) {
                            unlink($template['compiled_dir'] . $f);
                        }
                    }
                    $d->close();
                } else {
                    if ($cache_list[$key]) {
                        // 存在那么更新
                        get_data($key, true);
                    }
                }
            }

            $this->success($this->lang->line('tip_cache_success'), $this->_index_rel, $this->_index_uri);
        }
        
        $this->assign(array('cache_list' => $cache_list));
        $this->display('index');
    }
    
    /**
     * 刷新第三方平台缓存
     */
    function flush_cache() {
        $dateline = time();
        $sign = md5($dateline . $this->config->item('sign_key'));
        echo file_get_contents($this->config->item($this->get('type') . '_site') . '/cache/clear_cache/' . $dateline . '/' . $sign);
    }
    
    /**
     * 刷新第三方平台缓存
     */
    function flush_cache_new() {
    	echo clear_third_cache($this->get('type'));
    }
    
    /**
     * 更新第三方的缓存
     */
    function update_cache() {
//         $dateline = time();
//         $sign = md5($dateline . $this->config->item('sign_key'));
//         echo file_get_contents($this->config->item($this->get('type') . '_site') . 
//                 '/cache/clear_cache/' . $dateline . '/' . $sign . 
//                 '/' . $this->get('do') . '/' . $this->get('name') . '/' . $this->get('id'));
    	echo clear_third_cache($this->get('type'), $this->get('do'), $this->get('name'), $this->get('id'));
    }
    
    /**
     * 刷新所有的模型模板
     */
    function flush_module_template() {
        // 获取所有的模型
        $modules = $this->db->get_where('PlaceModule')->result_array();
        if($modules) {
            $this->load->helper('poi');
            foreach($modules as $module) {
                update_module_template($module['id']);
            }
        }
    }

    
    /**
     * 刷新电影票团购
     */
    function flush_goods_template() {
        $this->load->helper('goods');
        // 获取所有团购
        $rows = $this->db->get_where('Product')->result_array();
        if($rows) {
            foreach($rows as $row) {
                generate_static_html($row['id'], 'product');
            }
        }
        
        // 所有电影票
        $rows = $this->db->get_where('GrouponItem')->result_array();
        if($rows) {
            foreach($rows as $row) {
                generate_static_html($row['id']);
            }
        }
    }

    // /**
     // * 刷新网站缓存
     // */
    // function flush_site_cache() {
        // $dateline = time();
        // $sign = md5($dateline . $this->config->item('sign_key'));
        // die(@file_get_contents($this->config->item('web_site') . '/cache/clear_cache/' . $dateline . '/' . $sign));
    // }
//     
    // /**
     // * 清空渠道商缓存
     // */
    // function flush_channel_cache() {
        // $dateline = time();
        // $sign = md5($dateline . $this->config->item('sign_key'));
        // die(@file_get_contents($this->config->item('channel_site') . '/cache/clear_cache/' . $dateline . '/' . $sign));
    // }
//     
    // /**
     // * 清空商家平台缓存
     // */
    // function flush_channel_cache() {
        // $dateline = time();
        // $sign = md5($dateline . $this->config->item('sign_key'));
        // die(@file_get_contents($this->config->item('merchant_site') . '/cache/clear_cache/' . $dateline . '/' . $sign));
    // }
}
