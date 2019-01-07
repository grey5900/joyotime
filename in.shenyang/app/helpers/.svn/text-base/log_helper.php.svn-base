<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/*
 * 日志的一些函数
 *
 * Author: piglet chenglin.zhu@gmail.com
 * Created on 2012-3-13
 */

/**
 * 清除日志中的特殊字符
 */
function clearlogstring($str) {
    if (!empty($str)) {
        if (!is_array($str)) {
            $str = dhtmlspecialchars(trim($str));
            $str = str_replace(array(
                    "\t",
                    "\r\n",
                    "\n",
                    "   ",
                    "  "
            ), ' ', $str);
        } else {
            foreach ($str as $key => $val) {
                $str[$key] = clearlogstring($val);
            }
        }
    }
    return $str;
}

/**
 * 转换数组
 */
function implodearray($array) {
    $return = '';
    if (is_array($array) && !empty($array)) {
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $return .= "$key={" . implodearray($value) . "}; ";
            } elseif (!empty($value)) {
                $return .= "$key=$value; ";
            } else {
                $return .= '';
            }
        }
    }
    return $return;
}
