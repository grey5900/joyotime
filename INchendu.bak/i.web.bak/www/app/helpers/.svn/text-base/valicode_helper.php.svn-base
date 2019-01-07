<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/*
 * 验证码
 *
 * Author: piglet chenglin.zhu@gmail.com
 * Created on 2012-1-10
 */

if (!function_exists('create_valicode')) {
    /**
     * 生成验证码
     * @params $words 字符串
     * @params $type 图片类型
     * @params $width 宽度
     * @params $height 高度
     * @params $sessName 保存session的名称
     */
    function create_valicode($words, $type = 'png', $width = 48, $height = 22) {
        if (empty($words))
            return false;
        $length = count($words);
        $width = ($length * 9 + 10) > $width ? $length * 9 + 10 : $width;
        if ($type != 'gif' && function_exists('imagecreatetruecolor')) {
            $im = @ imagecreatetruecolor($width, $height);
        } else {
            $im = @ imagecreate($width, $height);
        }
        $r = array(
                225,
                255,
                255,
                223
        );
        $g = array(
                225,
                236,
                237,
                255
        );
        $b = array(
                225,
                236,
                166,
                125
        );
        $key = mt_rand(0, 3);

        $backColor = imagecolorallocate($im, $r[$key], $g[$key], $b[$key]);
        //背景色（随机）
        $borderColor = imagecolorallocate($im, 100, 100, 100);
        //边框色
        $pointColor = imagecolorallocate($im, mt_rand(0, 255), mt_rand(0, 255), mt_rand(0, 255));
        //点颜色

        @ imagefilledrectangle($im, 0, 0, $width - 1, $height - 1, $backColor);
        @ imagerectangle($im, 0, 0, $width - 1, $height - 1, $borderColor);
        $stringColor = imagecolorallocate($im, mt_rand(0, 200), mt_rand(0, 120), mt_rand(0, 120));
        // 干扰
        for ($i = 0; $i < 10; $i++) {
            $fontcolor = imagecolorallocate($im, mt_rand(0, 255), mt_rand(0, 255), mt_rand(0, 255));
            imagearc($im, mt_rand(-10, $width), mt_rand(-10, $height), mt_rand(30, 300), mt_rand(20, 200), 55, 44, $fontcolor);
        }
        for ($i = 0; $i < 25; $i++) {
            $fontcolor = imagecolorallocate($im, mt_rand(0, 255), mt_rand(0, 255), mt_rand(0, 255));
            imagesetpixel($im, mt_rand(0, $width), mt_rand(0, $height), $pointColor);
        }

        @imagestring($im, 5, 5, 3, $words, $stringColor);
        ob_clean();
        header("Content-type: image/" . $type);
        $ImageFun = 'image' . $type;
        $ImageFun($im);
        imagedestroy($im);
    }

}
