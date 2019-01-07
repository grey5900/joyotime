<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
  * 专门用于测试的Controller
  * @Author: chenglin.zhu@gmail.com
  * @Date: 2013-3-20
  */

class Test extends Controller {
    /**
     * 积分票测试
     * @param 地点ID $place_id
     * @param 积分 $point
     * @param 过期时间 20130228235959 $expire_date
     */
    function index($place_id = 0, $point = 0, $expire_date = '') {
        header('Content-type:text/html; charset=utf-8');
        
        $place_id = intval($place_id);
        $point = intval($point);
        $expire_date = trim($expire_date);
        
        ($place_id <= 0 || $point <= 0 || empty($expire_date)) && die('参数错误');
        
        // 判断时间是否过期
        $expire_time = strtotime($expire_date);
        if(TIMESTAMP > $expire_time) {
            die('过期时间错误');
        }
        
        // 查询是否有这个地点
        $place = $this->db->get_where($this->_tables['place'], array('id' => $place_id))->row_array();
        empty($place) && die('地点错误');
        $code = get_code();
        $data = array(
                'code' => $code,
                'point' => $point,
                'placeId' => $place_id,
                'expireDate' => gmdate('Y-m-d H:i:s', $expire_date + 3600*8),
                'listId' => 0,
                'status' => 0
        );
        $this->db->insert($this->_tables['pointticket'], $data);
        $id = $this->db->insert_id();
        
        exit(sprintf('http://%s/qr/inpt/%s?code=%s', 'in.chengdu.cn', $id, $code));
    }
    
    function lucky() {
    	$list = $this->db->select('id, rules')->order_by('id', 'asc')->get('WebLucky')->result_array();
    	foreach($list as $row) {
    		$rules = decode_json($row['rules']);
    		echo 'id:', $row['id'], '=>', var_export($rules['point'], true), var_export($rules['item'], true), "<br/>";
    	}
    }
    
    function google2baidu($lat = 0, $lng = 0) {
    	var_dump(google2baidu($lat, $lng));
    }
}

/**
 * 获取code
 */
function get_code() {
    return dechex(rand(256, 4095)) . uniqid();
}
