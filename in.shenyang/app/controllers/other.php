<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');   
/*
 * 其他
 * 
 * Author: piglet chenglin.zhu@gmail.com
 * Created on 2012-10-30
 */

class Other extends MY_Controller {
    /**
     * 生成二维码
     */
    function qr_generator() {
        $link_type = $this->config->item('link_type');
        $page_id = 'qr_generator';
        $web_site = $this->config->item('web_site');
        $this->assign(compact('link_type', 'page_id', 'web_site'));
        $this->display('qr_generator');
    }
    
    /**
     * 二维码下载
     */
    function qr_download($qr_code_url = '') {
        $qr_code_url = trim(urldecode($qr_code_url));
        if($qr_code_url == '') {
            $this->error('错误的连接');
        }
        
        header('Content-type: application/octet-stream');
        header('Content-Disposition: attachment; filename=qrcode.jpg');
        echo readfile($qr_code_url);
    }
}
