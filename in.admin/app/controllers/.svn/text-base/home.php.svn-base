<?php
/**
  * 首页管理
  * @Author: chenglin.zhu@gmail.com
  * @Date: 2013-2-19
  */
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
 
class Home extends MY_Controller {
    function __construct() {
        parent::__construct();
        $this->load->helper('home');
    }
    
    /**
     * 预览截取的图片
     * @param 图片地址 $image
     * @param 规格的宽度 $image_w
     * @param 规格的高度 $image_h
     * @param 图片缩放后的宽度 $w
     * @param 图片缩放后的高度 $h
     * @param 截取图片的x1 $x1
     * @param 截取图片的y1 $y1
     * @param 截取图片的宽度 $width
     * @param 截取图片的高度 $height
     */
    function preview_imgarea($image, $image_w, $image_h, $w, $h, $x1, $y1, $width, $height) {
        preview_imgarea(urldecode($image), $image_w, $image_h, $w, $h, $x1, $y1, $width, $height, true);
    }
    
    /**
     * 根据类型和图片获取需要截取的一些图片信息
     * @param 类型 $size_type
     * @param 图片 $image
     */
    function imageselect_size($size_type, $image) {
        $this->echo_json(imageselect_size($size_type, urldecode($image)));
    }
    
    /**
     * 截取图片的上传
     */
    function upload() {
        $file = $_FILES['imgareaselect_file'];
        $code = 1;
        if($file['tmp_name']) {
            list($w, $h, $type, ) = getimagesize($file['tmp_name']);
            $image_types = $this->config->item('image_types');
            $it = $image_types[$type];
            if($it) {
                $size_type = $this->get('size_type');
                $image_select = $this->config->item('image_select');
                list($width, $height) = $image_select[$size_type];
                if($width && $height && ($w >= $width) && ($h >= $height)) {
                    // 如果上传图片的大小大于等于规格，那么可以
                    $file_path = 'data/upload/' . microtime(true) . '.' . $it;
                    $dst_image = FCPATH . $file_path;
                    move_uploaded_file($file['tmp_name'], $dst_image);
                    $image = '/' . $file_path;
                    $code = 0;
                } else {
                    $message = sprintf('上传的图片大小不合适，请上传大于[%s x %s]的图片', $width, $height);
                }
            } else {
                $message = '上传图片文件类型不正确[gif/jpg/png]';
            }
            $code && unlink($file['tmp_name']);
        } else {
            $message = '请上传图片文件[gif/jpg/png]';
        }
        $this->echo_json(compact('code', 'message', 'image'));
    }
}

