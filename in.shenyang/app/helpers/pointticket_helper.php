<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
  * 积分票的helper
  * @Author: chenglin.zhu@gmail.com
  * @Date: 2013-3-5
  */

/**
 * 获取code
 */
function get_code() {
    return dechex(rand(256, 4095)) . uniqid();
}

