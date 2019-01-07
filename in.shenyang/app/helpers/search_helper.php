<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
  * 关于搜索的helper
  * @Author: chenglin.zhu@gmail.com
  * @Date: 2013-1-21
  */

/**
 * 更新索引
 * @param 索引类型 $type
 * // 类型 10：楼盘 20：地点 30：用户 40：POST数据 50：新闻
 * @param 索引的ID号 $id
 * @param 操作类型 $do update/delete
 */
function update_index($type, $id, $do = 'update') {
    $search_config = config_item('search');
    
    $solr = new SolrClient($search_config['options']);
    
    if (empty($solr)) {
    	return false;
    }
    
    $doc_id = $type . '-' . $id;
    
    if('update' == $do) {
        $func_name = array(
                '10' => 'doc_building',
                '20' => 'doc_place',
                '30' => 'doc_user',
                '40' => 'doc_post',
                '50' => 'doc_news'
                );
        $func = $func_name[$type];
        if(empty($func)) {
            return false;
        }
        $data = $func($id);
        if(empty($data)) {
            return false;
        } else {
            $doc = new SolrInputDocument();
            $doc->addField('id', $doc_id);
            $doc->addField('type', $type);
            foreach($data as $field => $value) {
                if(is_array($value)) {
                    foreach($value as $v) {
                        $doc->addField($field, $v);
                    }
                } else {
                    $doc->addField($field, $value);
                }
            }
            try {
            	@$solr->addDocument($doc, false, 1);
            } catch(SolrClientException $e) {
            	
            }
            // 不需要commit，这里自动会提交，设定了提交的时间
        }
    } else {
        // 删除
//         $solr->deleteById($doc_id);
        // 这里使用solr扩展函数无法及时提交，所以改用提交到地址上
        $submit_url = sprintf('http://%s:%s%s/update/?stream.body=<delete><query>id:%s</query></delete>&stream.contentType=text/xml;charset=utf-8&commit=true',
                $search_config['options']['hostname'], $search_config['options']['port'],
                $search_config['options']['path'], $doc_id);
        @http_request($submit_url, array(), array(), 'GET', true);
    }
    return true;
}

/**
 * 获取楼盘的DOC
 * @param ID号 $id
 */
function doc_building($id) {
    // 获取地点信息及扩展信息
    $delim_str = 'kVo1MY9Wz5';
    $sql = "SELECT GROUP_CONCAT(a.fieldId SEPARATOR '{$delim_str}') AS `fields`, 
            GROUP_CONCAT(IFNULL(a.mValue, 'null') SEPARATOR '{$delim_str}') AS `values`, 
            a.placeId, b.placename, b.address, b.tel, b.createDate, 
            b.latitude, b.longitude FROM PlaceModuleData a 
            LEFT JOIN Place b ON a.placeId = b.id 
            WHERE a.moduleId = 28 AND a.placeId = '{$id}' AND a.isVisible = 1 GROUP BY placeId";
    $CI =& get_instance();
    $row = $CI->db->query($sql)->row_array();
    if(empty($row)) {
        return array();
    } else {
        // 分解字段
        $fields = explode($delim_str, $row['fields']);
        $values = explode($delim_str, $row['values']);
        $module_data = array();
        $length = count($fields);
        for($i = 0; $i < $length; $i++) {
            $field = trim($fields[$i]);
            $value = trim($values[$i]);
            if($field) {
                $module_data[$field] = $value==='null'?'':$value;
            }
        }
        
        $house_type = $module_data['v-housetype'];
        
        $image = $row['icon'];
        if(empty($image)) {
            $image = get_place_icon($row['brandId'], $row['placeId']);
        }
        
        return array(
                'title' => $row['placename'],
                'desc' => $module_data['v-introduce'],
                'timestamp' => strtotime($row['createDate']),
                'address' => $row['address'],
                'developers' => $module_data['v-develop'],
                'tel' => $row['tel'],
                'price' => floatval($module_data['v-avgprice']),
                'area' => $module_data['v-ownarea'],
                'loop' => $module_data['v-towards'],
                'selling' => $module_data['v-indoor'],
                'direction' => $module_data['v-biz'],
                'subway' => $module_data['v-ditie'],
                'fitment' => $module_data['v-decorate'],
                'latitude' => $row['latitude'],
                'longitude' => $row['longitude'],
                'house_type' => $house_type?(preg_split('/[, ，]/', $house_type, -1, PREG_SPLIT_NO_EMPTY)):'',
                'image' => $image
                );
    }
}

/**
 * 获取地点的DOC
 * @param ID号 $id
 */
function doc_place($id) {
    $sql = "SELECT a.* FROM Place a WHERE a.`status` < 2 AND a.id = '{$id}'";
    $CI =& get_instance();
    $row = $CI->db->query($sql)->row_array();
    if(empty($row)) {
        return array();
    } else {
        $image = $row['icon'];
        if(empty($image)) {
            $image = get_place_icon($row['brandId'], $row['placeId']);
        }
        return array(
                'title' => $row['placename'],
                'desc' => $row['description'],
                'timestamp' => strtotime($row['createDate']),
                'image' => $image,
                'address' => $row['address'],
                'tel' => $row['tel'],
                'price' => $row['pcc'],
                'level' => $row['level'],
                'latitude' => $row['latitude'],
                'longitude' => $row['longitude']
                );
    }
}

/**
 * 获取用户的DOC
 * @param ID号 $id
 */
function doc_user($id) {
    $sql = "SELECT a.* FROM User a WHERE a.`status` = 0 AND a.id = '{$id}'";
    $CI =& get_instance();
    $row = $CI->db->query($sql)->row_array();
    if(empty($row)) {
        return array();
    } else {
        return array(
//                 'title' => sprintf('%s - %s', $row['username']?$row['username']:$row['nickname'], $row['nickname']?$row['nickname']:$row['username']),
                'title' => $row['nickname']?$row['nickname']:$row['username'],
        		'desc' => $row['description'],
                'timestamp' => strtotime($row['createDate']),
                'image' => $row['avatar'],
                'username' => $row['username'],
                'nickname' => $row['nickname']
        );
    }
}

/**
 * 获取POST的DOC
 * @param ID号 $id
 */
function doc_post($id) {
    $sql = "SELECT a.*, b.placename, b.address, c.username, c.nickname FROM Post a 
            LEFT JOIN Place b ON b.id = a.placeId 
            LEFT JOIN User c ON a.uid = c.id 
            WHERE a.`type` > 1 AND a.`status` < 2 AND a.id = '{$id}'";
    $CI =& get_instance();
    $row = $CI->db->query($sql)->row_array();
    if(empty($row)) {
        return array();
    } else {
        return array(
                'title' => sprintf('%s在%s发布了%s', $row['nickname']?$row['nickname']:$row['username'], $row['placename'], '2'==$row['type']?'点评':'图片'),
                'desc' => $row['content'],
                'timestamp' => strtotime($row['createDate']),
                'image' => $row['photo'],
                'username' => $row['username'],
                'nickname' => $row['nickname'],
                'post_type' => $row['type'],
                'uid' => $row['uid'],
                'place_id' => $row['placeId'],
                'placename' => $row['placename'],
                'address' => $row['address']
        );
    }
}

/**
 * 获取新闻的DOC
 * @param ID号 $id
 */
function doc_news($id) {
    $sql = "SELECT a.*, b.id as catId, b.catName, c.id as channelId, 
			c.catName as channelName FROM WebNews a 
			LEFT JOIN WebNewsCategory b ON a.newsCatId = b.id 
			LEFT JOIN WebNewsCategory c ON b.parentId = c.id 
			WHERE a.`status` = 1 AND a.id = '{$id}'";
    $CI =& get_instance();
    $row = $CI->db->query($sql)->row_array();
    if(empty($row)) {
        return array();
    } else {
        return array(
                'title' => $row['subject'],
                'desc' => $row['summary'],
                'timestamp' => $row['dateline'],
                'image' => $row['thumb'],
                'content' => htmlspecialchars_decode(strip_tags($row['content'])),
                'news_type' => $row['newsType'],
                'category_id' => $row['catId']?$row['catId']:'-1',
                'category_name' => $row['catName'],
                'channel_id' => $row['channelId']?$row['channelId']:'-1',
                'channel_name' => $row['channelName']
        );
    }
}

/**
 * 获取地点的ICON
 * @param 品牌ID号 $brand_id
 * @param 地点ID号 $place_id
 */
function get_place_icon($brand_id, $place_id) {
    $image = "";
    $CI =& get_instance();
    // 地点的图片没有，那么就去获取地点的分类的图标
    // 看地点是否为品牌，是品牌先获取品牌的图标
    if($brand_id > 0) {
        // 那么去获取品牌的图标
        $sql = "SELECT * FROM Brand WHERE id = '{$brand_id}'";
        $row = $CI->db->query($sql)->row_array();
        if($row) {
            $image = $row['logo'];
        }
    } 
    if(empty($image)) {
        // 去获取分类的
        $sql = "SELECT a.* FROM PlaceCategory a, PlaceOwnCategory b
                WHERE b.placeCategoryId = a.id AND a.isBrand = 0 AND b.placeId = '{$place_id}' LIMIT 1";
        $row = $CI->db->query($sql)->row_array();
        $image = $row['icon']?$row['icon']:$row['categoryIcon'];
    }

    return $image;
}
