<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * 一些需要WEB访问的操作
 * @author chenglin.zhu@gmail.com
 * @date 2012-11-28
 */

class Www extends MY_Controller {
    function __construct() {
        parent::__construct();
        
        $this->load->model('www_model', 'model');
    }
    
    /**
     * 电子报
     * @param int $id 电子报的ID号 
     */
    function enewspaper($id = 0) {
        $id = intval($id);
        if($id < 0) {
            die('错误');
        }
        
        // 获取当前
        $paper = $this->model->find_enewspaper($id);
        
        if($paper) {
            // 获取前一条
            $prev = $this->model->find_prev_enewspaper($paper['publishDate']);
            
            // 获取后一条
            $next = $this->model->find_next_enewspaper($paper['publishDate']);
            
            $content = json_decode($paper['content'], true);
            
            $this->assign(compact('content', 'paper', 'prev', 'next'));
        }
        
        $this->display('enewspaper');
    }
}
