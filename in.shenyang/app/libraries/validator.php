<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/*
 * 验证框架
 *
 * Author: piglet chenglin.zhu@gmail.com
 * Created on 2012-2-9
 */

class Validator {
    // 验证规则
    // 如：
    // array(
    // 'name' => array('type'=>'need,minlen{4},maxlen{10}', 'msg'=>'请输入用户名'),
    // 'email' => array('type'=>'need,email', 'msg'=>'请输入正确的email地址')
    // );
    var $rules = array();
    var $_default_msg = array(
        'need' => '必须填写',
        'email' => '错误的EMAIL地址',
        'minlen' => '最少%d字符',
        'maxlen' => '最多%d字符',
        'rangelen' => '%d到%d字符之间',
        'number' => '必须数字格式',
        'phone' => '电话格式错误，如：028-84444444或02884444444',
        'mobile' => '填写正确的手机号',
        'alnum' => '只能是数字或英文字符',
        'url' => '错误的url地址'
    );

    /**
     * 构造函数
     * @param $rules 验证规则
     * 验证类型：
     * need:必填
     * email:邮件
     * minlen:最小长度{4}
     * maxlen:最大长度{10}
     * rangelen:长度判断{4,10}
     * number:数字验证
     * phone:固定电话
     * mobile:手机号
     * alnum:数字+字符
     * url:地址验证
     */
    function __construct($rules = array()) {
        $this->setRules($rules);
    }

    /**
     * 设置验证规则
     * @param $rules
     */
    function setRules($rules) {
        $this->rules = $rules;
    }
    
    /**
     * 运行验证
     * @param $params array 需要验证的数组
     * @param $rules 规则
     * @param $rtn_break 默认true 打断式返回，及发现错误就返回，false判断所有的最后返回
     * @return 返回数组
     * 如：
     *
     */
    function run($params, $rules = array(), $rtn_break = true) {
        // 开始验证每条规则
        foreach($rules as $field=>$data) {
            
        }
    }

    /**
     * 必填
     */
    function need($str) {
        return !empty(trim($str));
    }

    /**
     * email判断
     */
    function email($str) {
        return eregi("^[_\.0-9a-zA-Z-]+@([0-9a-zA-Z][0-9a-zA-Z-]+\.)+[a-zA-Z]{2,6}$", $email);
    }
    
    /**
     * 电话号码
     */
    function phone($str) {
        return eregi("^(\(\d{3,4}\)|\d{3,4}-)?\d{7,8}$", $str);
    }
    
    /**
     * 手机号码
     */
    function mobile($str) {
        return eregi("/^13[0-9]{1}[0-9]{8}$|15[0-9]{1}[0-9]{8}$|18[0-9]{1}[0-9]{8}$/", $str);
    }
    
    /**
     * 数字、英文字符
     */
    function alnum($str) {
        return eregi("^[A-Za-z0-9]+$", $str);
    }

    /**
     * url地址验证
     */
    function url($str) {
        return eregi("/^http:\/\/[A-Za-z0-9]+\.[A-Za-z0-9]+[\/=\?%\-&_~`@[\]\':+!]*([^<>\"\"])*$/", $str);
    }    

    /**
     * 最长字段
     */
    function maxlen($str, $len) {
        return $this->length($str) <= $len;
    }

    /**
     * 最小长度
     */
    function minlen($str, $len) {
        return $this->length($str) >= $len;
    }

    /**
     * 长度范围
     */
    function rangelen($str, $min, $max) {
        return $this->length($str) < $min ? false : $this->length($str) <= $max;
    }

    /**
     * 数字
     */
    function number($str) {
        return eregi("^[0-9]+$", $str);
    }

    /**
     * 真实的长度，字节数 本来一个中文3个字节，统计以后转换成2个字节
     * 如：
     * china 5
     * 中国 4
     * 中国china 9
     * 如果是 strlen系统内置函数 结果应该是 5 6 11
     * @param $str utf-8编码
     */
    function str_len($str) {
        $count = 0;
        for ($i = 0; $i < strlen($str); $i++) {
            $value = ord($str[$i]);
            if ($value > 127) {
                $count++;
                if ($value >= 192 && $value <= 223)
                    $i++;
                elseif ($value >= 224 && $value <= 239)
                    $i = $i + 2;
                elseif ($value >= 240 && $value <= 247)
                    $i = $i + 3;
            }
            $count++;
        }
        return $count;
    }

    /**
     * 只统计个数，不管中英文
     * 如：
     * china 5
     * 中国 2
     * 中国china 7
     * @param $str
     */
    function length($str) {
        $count = 0;
        for ($i = 0; $i < strlen($str); $i++) {
            $value = ord($str[$i]);
            if ($value > 127) {
                if ($value >= 192 && $value <= 223)
                    $i++;
                elseif ($value >= 224 && $value <= 239)
                    $i = $i + 2;
                elseif ($value >= 240 && $value <= 247)
                    $i = $i + 3;
            }
            $count++;
        }
        return $count;
    }

}
