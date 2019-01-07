<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * 具体cache操作的函数
 * 
 * @author chenglin.zhu@gmail.com
 * @date 2012-11-19
 */

/**
 * 处理之前1.X里面的图片的显示
 */
function cache_func_defaulthead() {
    $array = array();
    foreach (range(0, 75) as $i) {
        $array[$i . '.jpg'] = 1;
    }
    
    return $array;
}

/**
 * 图片配置
 */
function cache_func_imagesetting() {
    $CI = &get_instance();
    $CI->load->model('appsetting_model', 'm_appsetting');
    
    $row = $CI->m_appsetting->select_by_skey('image');
    if (empty($row)) {
        $svalue = array();
    } else {
        $svalue = unserialize($row['svalue']);
        if ($svalue['image_cat']) {
            foreach ($svalue['image_cat'] as $k => $v) {
                $values = explode(';', $v);
                if (count($values) < 4) {
                    // 如果数组不够，就返回。不缓存，错误数据
                    unset($svalue['image_cat'][$k]);
                    continue;
                }
                $arr = array();
                $key = $values[0];
                $arr['name'] = $values[1];
                $arr['path'] = $values[2];
                $types = explode(':', $values[3]);
    
                foreach($types as $type) {
                    // 每个类型定义了类型名和大小
                    list($k, $wh) = explode('=', $type);
                    $arr[$k] = explode('x', $wh);
                }
    
                $svalue['image_cat_arr'][$key] = $arr;
            }
        }
    }
    return $svalue;
}

/**
 * 新闻的分类
 */
function cache_func_newscategory() {
    $CI = &get_instance();
    $CI->load->model('webnewscategory_model', 'm_webnewscategory');
    $CI->load->model('webnewscategoryowntag_model', 'm_webnewscategoryowntag');
    
    $CI->db->where("status",1);
    $CI->db->order_by("parentId asc ,orderValue desc");
    
    //$sql = "select c.*,t.tagId as placeid,count(t.tagId) as tag_count from WebNewsCategory c left join WebNewsCategoryOwnTag t on
    //		(t.catId = c.id) where c.status=1 and (t.tagType=2 or t.tagType is NULL) group by c.id";
    
    $list =  $CI->m_webnewscategory->list();//$CI->db->query($sql)->result_array();//
    //根据频道是否有关联TAG来判定链接
    
    $arr = array();
    foreach($list as $row) {
    	$t = $CI->m_webnewscategoryowntag->count_by_catid($row['id']);
    	/*$sql = "select t.tagId as placeid from WebNewsCategory c left join WebNewsCategoryOwnTag t on
    		t.catId = c.id where t.tagType=2 and c.id=".$row['id']." limit 1";
    	$row['placeid'] = $CI->db->query($sql)->row_array(0)['placeid'];*/
    	$row['tag_count'] = $t;
        $arr[$row['id']] = $row;
    }
    unset($list);
    
    return $arr;
}

/**
 * 域名和频道栏目的对应关系
 */
function cache_func_domains() {
    $CI = &get_instance();
    $CI->load->model('webnewscategory_model', 'm_webnewscategory');
    
    $CI->db->where("status",1);
    $CI->db->order_by("parentId asc ,orderValue desc");
    $list = $CI->m_webnewscategory->list();
    
    $arr = $arr2 = array();
    foreach($list as $row) {
        $domain = $row['domain'];
        if($row['parentId']) {
            $domain = $arr2[$row['parentId']];
            if($domain) {
                $arr[$domain]['sub'][$row['id']] = $row['status'];
            }
        } else {
            if($domain) {
                $arr[$domain] || $arr[$domain] = array();
                $arr[$domain]['id'] = $row['id'];
                $arr2[$row['id']] = $domain;
            }
        }
    }
    unset($list, $arr2);
    
    return $arr;
}

/**
 * 碎片分类
 */
function cache_func_fragment() {
    $CI = &get_instance();
    $CI->load->model('webrecommendfragment_model', 'm_webrecommendfragment');
    
    $list = $CI->m_webrecommendfragment->list();
    
    $arr = array();
    foreach($list as $row) {
        $arr[$row['fid']] = $row;
    }
    unset($list);

    return $arr;
}

/**
 * 获取一个用户
 * @param int $id
 */
function cache_func_user($id) {
    static $db = null;
    
    if(null == $db) {
        $CI = &get_instance();
        $CI->load->model('user_model', 'm_user');
        $db = $CI->m_user;
    }
    
    $user = $db->get_info($id);
    
    return $user;
}

/**
 * 获取一组敏感词
 * Create by 2012-12-14
 * @author liuweijava
 * @param string $type
 * @return array
 */
function cache_func_taboos($type=''){
	static $db = null;
	if(null == $db){
		$CI = &get_instance();
		$CI->load->model('taboo_model', 'm_taboo');
		$db = $CI->m_taboo;
	}
	$taboos = $db->get_taboos($type);
	return $taboos;
}

/**
 * 获取一个地点
 * @param int $id
 */
function cache_func_place($id) {
    static $db = null;
    
    if(null == $db) {
        $CI = &get_instance();
        $CI->load->model('place_model', 'm_place');
        $db = $CI->m_place;
    }
    
    return $db->get_info($id);
}

/**
 * 包租婆收租公排行
 */
function cache_func_rent() {
    static $db = null;
    
    if(null == $db) {
        $CI = &get_instance();
        $CI->load->model('userpointlog_model', 'm_userpointlog');
        $db = $CI->m_userpointlog;
    }
    
    return $db->list_rent();
}

/**
 * 热门美食排行
 */
function cache_func_toplist() {
    static $db = null;
    
    if(null == $db) {
        $CI = &get_instance();
        $CI->load->model('place_model', 'm_place');
        $db = $CI->m_place;
    }
    
    return $db->delicious_places();
}

/**
 * 地点分类
 * @param int $id
 */
function cache_func_placecategory($id) {
    static $db = null;
    
    if(null == $db) {
        $CI = &get_instance();
        $CI->load->model('placecategory_model', 'm_place');
        $db = $CI->m_place;
    }
    
    return $db->select_by_id($id);
}

/**
 * 获取tag
 * @param int 
 */
function cache_func_tag($id) {
    static $db = null;
    
    if(null == $db) {
        $CI = &get_instance();
        $CI->load->model('tag_model', 'm_tag');
        $db = $CI->m_tag;
    }
    
    return $db->select_by_id($id);
}

/**
 * 碎片数据
 */
function cache_func_fragmentdata($fid) {
    $CI = &get_instance();
    $CI->load->model('webrecommenddata_model', 'm_webrecommenddata');
    $CI->load->model('fragment_model', 'm_fragment');
    $frag = $CI->m_fragment->get_frag($fid);//get_data("fragment",$fid);//get_frag($fid);
    
    $frag['rule'] = json_decode($frag['rule'], true);
	//!empty($frag['rule']) && $frag['rule'] = json_decode($frag['rule'], true);
	!empty($frag['rule']) && !empty($frag['rule']['pic_size']) && $frag['rule']['pic_size'] = explode('*', $frag['rule']['pic_size']);
	!empty($frag['extraProperty']) && $frag['extraProperty'] = json_decode($frag['extraProperty'], true);
			
	//查询数据
	$cache_data = $CI->m_webrecommenddata->list_by_fragmentid_order_ordervalue($fid);
	foreach($cache_data as &$r){
		if(!empty($r['extraData'])){
			$exd = json_decode($r['extraData'], true);
			foreach($exd as $k=>&$e){
				$t = $frag['extraProperty'][$k]['type'];
				$e = array(
					'type'=>$t,
					'data'=>$e
				);
				unset($e);
			}
			$r['extraData'] = $exd;
		}
		unset($r);
	}
    
    return array("data"=>$cache_data,"frag"=>$frag);
}

/**
 * 地点的扩展信息详情
 * Create by 2012-12-18
 * @author liuweijava
 * @param int $prop_id
 */
function cache_func_specialproperty($prop_id){
	static $db = null;
	if($db == null){
		$CI = &get_instance();
		$CI->load->model('placeownspecialproperty_model', 'm_place_property');
		$db = $CI->m_place_property;
	}
	return $db->get_info($prop_id);
}

/**
 * 获取图片尺寸
 * Create by 2012-12-18
 * @author liuweijava
 * @param string $image_name
 */
function cache_func_imagesize($image_name){
	static $db = null;
	
	if(null == $db) {
		$CI = &get_instance();
		$CI->load->model('post_model', 'm_post');
		$db = $CI->m_post;
	}
	
	list($name, $size) = explode('||', $image_name);
	return $db->image_wh($name, $size);
}

/**
 * ttl时间记录
 * @param $id
 */
function cache_func_ttl($id) {
    return TIMESTAMP;
}

/**
 * 获取地点最新的10条相关新闻
 * Create by 2012-12-24
 * @author liuweijava
 * @param int $place_id
 */
function cache_func_place_news($place_id){
	static $db = null;
	
	if(null == $db) {
		$CI = &get_instance();
		$CI->load->model('place_model', 'm_place');
		$db = $CI->m_place;
	}
	
	return $db->get_newes($place_id);
}

/**
 * 随机获取一些活动
 * @param int $event_id
 */
function cache_func_relation_event($event_id) {
    static $db = null;
    
    if(null == $db) {
    	$CI = &get_instance();
    	$CI->load->model('webevent_model', 'm_webevent');
    	$db = $CI->m_webevent;
    }
    
    $list = $db->relation_event($event_id, 5);
    
    return $list;
}

/**
 * 获取备注
 * @param  $id $uid-$uid （自己的ID-备注人的ID）
 */
function cache_func_mnemonic($id) {
	// 分解$id
	$ids = array_filter(explode('-', $id));
	if(count($ids) != 2) {
		return array();
	}
	
	static $db = null;
	
	if(null == $db) {
		$CI = &get_instance();
		$CI->load->model('usermnemonic_model', 'm_usermnemonic');
		$db = $CI->m_usermnemonic;
	}
	
	$user_mnemonic = $db->select(array('uid' => $ids[0], 'mUid' => $ids[1]));
	
	if(empty($user_mnemonic)) {
		$user_mnemonic = array('uid' => $ids[0], 'mUid' => $ids[1]);
		// 如果用户没有备注，那么把用户的昵称当备注写进缓存
		$user = get_data('user', $ids[1]);
		$user_mnemonic['mnemonic'] = $user['nickname'];//?$user['nickname']:$user['username'];
	}
	
	return $user_mnemonic;
}

/**
 * 查询post关联的tag
 * @param POST的ID号 $id 
 */
function cache_func_post_tags($id) {
    static $db = null;

    if(null == $db) {
        $CI = &get_instance();
        $CI->load->model('postowntag_model', 'm_postowntag');
        $db = $CI->m_postowntag;
    }
    
    return $db->get_postowntag($id);
}

/**
 * POST的最新的tag 2条
 * @param POST的ID号 $id
 */
function cache_func_post_replies($id) {
    static $db = null;
    
    if(null == $db) {
        $CI = &get_instance();
        $CI->load->model('post_model', 'm_post');
        $db = $CI->m_post;
    }
    
    return $db->get_reply($id, 10);
}

/**
 * 获取用户对这个POST的赞的状态
 * @param 用户ID和POST的ID号和类型ID的组合 $id  2：点评 3：图片
 */
function cache_func_post_praised($id) {
    // 分解$id
    $ids = array_filter(explode('-', $id));
    if(count($ids) != 3) {
        return 1;
    }
    
    static $db = null;
    
    if(null == $db) {
        $CI = &get_instance();
        $CI->load->model('postappraise_model', 'm_postappraise');
        $db = $CI->m_postappraise;
    }
    
    $row = $db->select(array('uid' => $ids[0], 'postId' => $ids[1], 'type' => $ids[2]));
        
    return $row?0:1;
}

/**
 * 获取用户对这个POST的收藏的状态
 * @param 用户ID和POST的ID号和类型ID的组合 $id  2：点评 3：图片
 */
function cache_func_post_favorited($id) {
    // 分解$id
    $ids = array_filter(explode('-', $id));
    if(count($ids) != 3) {
        return 1;
    }

    static $db = null;

    if(null == $db) {
        $CI = &get_instance();
        $CI->load->model('userfavorite_model', 'm_userfavorite');
        $db = $CI->m_userfavorite;
    }

    $row = $db->select(array('uid' => $ids[0], 'itemId' => $ids[1], 'itemType' => $ids[2]));

    return $row?0:1;
}

function cache_func_index_usePointTicket($limit = 3){
	static $db = null;
	if(null == $db) {
        $CI = &get_instance();
        $CI->load->model('userpointlog_model', 'm_userpointlog');
        $db = $CI->m_userpointlog;
    }
    
    return $db->get_usePointTicket_list($limit);
}
