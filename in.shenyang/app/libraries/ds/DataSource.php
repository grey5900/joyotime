<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/*
 * 数据源基类
 * 
 * Author: piglet chenglin.zhu@gmail.com
 * Created on 2012-5-24
 */

abstract class DataSource {
    // Controller
    var $c;
    // 默认条数
    var $num = 200; 
    /**
     * 构造函数
     */
    function __construct($c) {
        $this->c = $c;
    }
    
    /**
     * 得到列表记录
     */
    abstract function get_list();
    
    /**
     * 得到一条记录
     */
    abstract function get_one();
}
