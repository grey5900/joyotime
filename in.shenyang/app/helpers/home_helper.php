<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
  * 首页
  * @Author: chenglin.zhu@gmail.com
  * @Date: 2013-2-19
  */

/**
 * 推荐到首页及推荐精华
 * @param 类型 $item_type
 * @param ID $item_id
 * @param 默认图片 $image
 * @param 是否需要推荐精华 $has_digest
 * @param 是否有推荐到首页 $has_home
 * @param 图片的类型
 */
function recommend_digest($item_type, $item_id, $image = '', $has_digest = false, $has_home = true, $size_type = 'home') {
    $CI = &get_instance();
    
    $data = array();
    if($has_home) {
        // 去获取推荐的信息
        $data = $CI->db2->get_where($CI->_tables['homepagedata'], array(
                        'itemType' => $item_type, 
                        'itemId' => $item_id))->row_array();
        $data?($data['image']?($image = image_url($data['image'], 'home', 'odp')):($image = '')):'';

        // 计算一个建议权重值 默认 100
        // SELECT MAX(HomePageData.baseRankOrder) WHERE HomePageData.itemType=$type AND HomePageData.expireDate有效
        $sql = "SELECT IFNULL(MAX(baseRankOrder), 100) AS n FROM {$CI->_tables['homepagedata']} WHERE 
                itemType='{$item_type}' AND expireDate > '" . now() . "'";
        $row = $CI->db2->query($sql)->row_array();
        $CI->assign(array('data' => $data, 'size_type' => $size_type?$size_type:'home', 'n' => $row['n']?$row['n']:100));
    }
    
    // 去获取信息的加入精华信息
    $digest_config = $CI->config->item('digest');
    empty($digest_config[$item_type]) && ($has_digest = false);
    $has_digest && $CI->assign('digests', $digest_config[$item_type]);
    $expire_day = 7;
    if($has_digest || empty($data)) {
        // 如果有推荐 或者 首页数据没有，那么需要去获取原始数据
        // 去获取信息
        switch($item_type) {
            case 1:
                $table = $CI->_tables['place'];
                $field = 'placename';
                break;
            case 4:
                $table = $CI->_tables['user'];
                $field = 'nickname';
                break;
            case 5:
                $table = $CI->_tables['webevent'];
                $field = 'subject';
                break;
            case 18:
            case 19:
                $table = $CI->_tables['post'];
                $field = 'content';
                $expire_day = 1;
                break;
            case 20:
                $table = $CI->_tables['placecollection'];
                $field = 'name';
                $expire_day = 2;
                break;
            case 23:
                $table = $CI->_tables['product'];
                $field = 'name';
                break;
            case 26:
                $table = 'Topic';
                $field = 'subject';
                $expire_day = 30;
                break;
            default:
        }
        $item = $table?($CI->db2->get_where($table, array(
                'id' => $item_id))->row_array()):array();
        $CI->assign(compact('item', 'field', 'item_type'));
    }
    
    
    $page_id = 'page_' . TIMESTAMP;
    $CI->assign(compact('item_type', 'item_id', 
            'image', 'has_digest', 'has_home',
            'page_id', 'expire_day'));

    $CI->display('recommend', 'home');
}

/**
 * 获取
 * @param 图片的规则类型 $size_type
 * @param 图片 $image
 */
function imageselect_size($size_type, $image) {
    $CI = &get_instance();
    
    // 获取图片剪切的配置信息
    $image_select = $CI->config->item('image_select');
    
    $imagesize = array();
    list($imagesize['w'], $imagesize['h']) = $image_select[$size_type];
        
    $scale1 = floatval($imagesize['w'])/$imagesize['h'];
    // 判断格式是否大于320x320，这种图片因为编辑器那里空间比较小，等比处理下
    if($imagesize['w'] >= $imagesize['h']) {
        // 宽度
        $nw = 320;
        $nh = intval($nw/$scale1);
    } else {
        $nh = 320;
        $nw = intval($scale1*$nh);
    }
    // 判断图片的大小是否符合规格
    if($image) {
        list($w, $h, , ) = getimagesize($image);
        $w0 = $w;
        $h0 = $h;
        if($w >= $imagesize['w'] && $h >= $imagesize['h']) {
            // 高宽都符合，那么对图片大小进行等比的处理
            $scale1 = floatval($nw)/$nh;
            $scale2 = floatval($w)/$h;
            if($scale2 >= $scale1) {
                // 宽度更大或比例相同，那么以宽度为准
                $w = $nw;
                $h = intval($w/$scale2);
            } else {
                // 高度更大，那么以高度为准
                $h = $nh;
                $w = intval($scale2*$h);
            }
        } else {
            $image = '';
            $w = $nw;
            $h = $nh;
        }
        // 计算最小寬高度
        $mw = $w0?intval((floatval($w)/$w0)*$imagesize['w']):0;
        $mh = $h0?intval((floatval($h)/$h0)*$imagesize['h']):0;
    } else {
        $w = $nw;
        $h = $nh;
        $mw = 0;
        $mh = 0;
        $w0 = 0;
        $h0 = 0;
    }
    
    /**
     * w h 图片缩放到选择区域的寬高
     * nw nh 根据图片原始规格，计算出缩放区域的标准寬高
     * mw mh 选择框的最小寬高
     * imagesize(w, h) 图片标准选择寬高 配置
     * image 图片地址
     */
    return compact('w', 'h', 'nw', 'nh', 'mw', 'mh', 'imagesize', 'image', 'w0', 'h0');
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
 * @param 是否输出显示 $is_show
 */
function preview_imgarea($image, $image_w, $image_h, $w, $h, $x1, $y1, $width, $height, $is_show = false) {
    if(empty($image) || empty($image_w) || empty($image_h) 
            || empty($w) || empty($h)  || empty($width) || empty($height)) {
        return false;
    }
    
    list($w0, $h0, $type, ) = getimagesize($image);
    $CI = &get_instance();
    $image_types = $CI->config->item('image_types');
    $it = $image_types[(0-$type)];
    if($it) {
        // 如果存在类型
        $create_func = "imagecreatefrom{$it}";
        $im = $create_func($image);
        
        $func = "image{$it}";
        header('Content-Type: image/' . $it);
        
        // 计算图片处理的$x $y $nw $nh
        // 算出图片的比例
        $scale1 = floatval($w0)/$w;
        $scale2 = floatval($h0)/$h;
        $scale = ($scale1 + $scale2)/2;
        // 计算新的$x $y
        $x = intval($x1*$scale);
        $y = intval($y1*$scale);
        // 去计算新的寬高 $nw $nh
        $nw = intval($width*$scale);
        $nh = intval($height*$scale);
        
        $img = imagecreatetruecolor($image_w, $image_h);
        imagecopyresized($img, $im, 0, 0, $x, $y, $image_w, $image_h, $nw, $nh);
        
        if($is_show) {
            $func($img);
        } else {
            $dst_img = FCPATH . 'data/upload/' . microtime(true) . '.' . ($image_types[$type]);
            $func($img, $dst_img);
        }
        
        imagedestroy($img);
        return $is_show?false:$dst_img;
    }
    return false;
}

/**
 * 推荐提交处理
 * @param 类型 $item_type
 * @param ID $item_id
 * @param 是否有精华 $has_digest
 * @param 是否有推荐首页 $has_home
 */
function recommend_digest_post($item_type, $item_id, $has_digest = false, $has_home = true,$batch = false) {
    $CI = &get_instance();
    
    // 去获取信息的加入精华信息
    $digest_config = $CI->config->item('digest');
    // 如果不再精华的设置配置里面，那么就不会去处理了。
    empty($digest_config[$item_type]) && ($has_digest = false);
    
    $recommend_digest = $CI->post('recommend_digest');
    $recommend_home = $CI->post('recommend_home');
    
    if($batch){
    	
    	$recommend_digest = true;
    }
   
    if($recommend_digest || $recommend_home) {
    	
        // 去获取是否已经设置过精华加分了
        switch($item_type) {
            case 18:
            case 19:
                $table = $CI->_tables['post'];
                break;
            case 20:
                $table = $CI->_tables['placecollection'];
                break;
            default:
        }
        $item = $table?($CI->db2->get_where($table, array(
                'id' => $item_id))->row_array()):array();
        // 如果之前没有设置过精华
        if($item && empty($item['essenceScore'])) {
            // 处理加入精华
            $score = $CI->post('score');
            
            if('input' == $score) {
                // 填写的积分
                $score = $CI->post('input_score');
            }
    
            $score = intval($score);
            if($score < 0) {
                return '精华积分不能小于0';
            }
            
            $CI->load->helper('ugc');
            // 获取用户信息
            $b &= make_point($item['uid'], 'digest', $score, $item_id, '', $item_type);
            
            // 之前没有提交过积分
            $b = $CI->db2->where('id', $item_id)->update($table, array(
                    'isEssence' => $recommend_digest?1:0,
                    'essenceDate' => now(),
                    'essenceScore' => $score
            ));
            
    
            if(empty($b)) {
                return '设置精华失败';
            } else {
                $CI->lang->load('premessage','chinese');
                $msg_key = sprintf('%s_%s_%s', ($recommend_digest?'1':'0'), 
                			($recommend_home?'1':'0'), ($score>0?'1':'0')); 
                switch($item_type) {
//                     case 18:
//                         $message = sprintf($CI->lang->line('sm_yy'), $score);
//                         break;
//                     case 19:
//                         $message = sprintf($CI->lang->line('sm_post'), $score);
//                         break;
//                     case 20:
//                         // 去获取地点册
//                         $pl = $CI->db2->get_where($CI->_tables['placecollection'],
//                         array('id' => $item_id))->row_array();
//                         $message = sprintf($CI->lang->line('sm_pl'), $pl['name'], $score);
//                         break;
//                     default:
                	case 18:
                	case 19:
                		// YY和POST
         				$message = sprintf($CI->lang->line('sm_post_' . $msg_key), substr($item['createDate'], 0, 16), $score);
                		break;
                	case 20:
                		// 地点册
                		$message = sprintf($CI->lang->line('sm_pl_' . $msg_key), $item['name'], $score);
                		break;
                }
                // 去更新用户的精华数
                $CI->db2->set('essenceCount', 'essenceCount+1', false)
                            ->where(array('id' => $item['uid']))
                            ->update($CI->_tables['user']);
                
                $message && send_message($message, $item['uid'], $item_id, $item_type, false);
            }
        }
    }
    
    if($has_home) {
        if($recommend_home) {
            // 处理推荐到首页
            $content = trim($CI->post('content'));
            $baseRankOrder = intval($CI->post('baseRankOrder'));
            $expireDate = $CI->post('expireDate');
            $praiseCount = $CI->post('praiseCount');
            
            if($baseRankOrder < 0) {
                return '权重值不能小于0';
            }
            
            if(TIMESTAMP > strtotime($expireDate)) {
                // 已经过期
                return '过期时间不能小于当前时间';
            }
            
            $image = $CI->post('image');
            if(empty($content) && empty($image)) {
                return '内容和图片必须填一项';
            }
            
            if(cstrlen($content) > 255) {
                return '推荐内容不能大于255个字';
            }
            
            if($image) {
                // 需要做图片处理
                $width = $CI->post('width');
                $height = $CI->post('height');
                $image_w = $CI->post('image_w');
                $image_h = $CI->post('image_h');
                if(empty($width) || empty($height)) {
                    // 没有寬高
                    $width = $image_w;
                    $height = $image_h;
                    $x1 = 0;
                    $y1 = 0;
                    $w = $width;
                    $h = $height;
                } else {
                    $w = $CI->post('w');
                    $h = $CI->post('h');
                    $x1 = $CI->post('x1');
                    $y1 = $CI->post('y1');
                }
                
                // 处理图片
                $dst_image = preview_imgarea($image, $image_w, $image_h, $w, $h, $x1, $y1, $width, $height);
                if(empty($dst_image)) {
                    return '处理图片出错了，请重试';
                }
                // 提交图片给接口处理
                $image = api_upload($dst_image, 'default');
                if(empty($image)) {
                    return '提交接口处理图片出错了，请重试';
                }
            }
            
            if(in_array($item_type, array(1, 19))) {
                $table = array(
                        '1' => $CI->_tables['place'],
                        '18' => $CI->_tables['post'],
                        '19' => $CI->_tables['post']
                );
                $item = $CI->db2->get_where($table[$item_type], array(
                                    'id' => $item_id))->row_array();
//                 if(in_array($item_type, array(18, 19)) 
//                         && !in_array($item['type'], array(2, 3, 4, 5))) {
//                     // 如果是post数据，而且不是 点评 图片 yy POST图片
//                     return '不是POST或YY数据，其他数据不能被推荐到首页';
//                 }
                if($item_type == 19 && $item['placeId']) {
                    // 去获取地点信息
                    $item = $CI->db2->get_where($CI->_tables['place'], array(
                                        'id' => $item['placeId']))->row_array();
                }
                $lat = $item['latitude'];
                $lng = $item['longitude'];
            }
            
            $conf_item_type = $CI->config->item('item_type');
            $conf_item_type = $conf_item_type[$item_type];
            if($conf_item_type['key']) {
                if($item_type == 5) {
                    // 活动要特殊处理
                    $web_site = $CI->config->item('web_site');
                    $hyperLink = sprintf('%s/event_new/detail/%s?refer=home', $web_site, $item_id);
                } else {
                    $hyperLink = sprintf('%s://%s?refer=home', $conf_item_type['key'], $item_id);
                }
            }
            
            // 去计算不同的rankOrder
//             $rankOrder = $baseRankOrder + get_ext_rank_order($item_type, $item_id);
            $rankOrder = intval($CI->post('rankOrder'));
            
            $data = compact('praiseCount', 'content', 'expireDate', 'rankOrder', 'baseRankOrder', 'hyperLink', 'image');
            $data['itemType'] = $item_type;
            $data['itemId'] = $item_id;
            if($lat && $lng) {
                $data['lat'] = $lat;
                $data['lng'] = $lng;
            }
            if($CI->db2->get_where($CI->_tables['homepagedata'], array(
            		'itemType' => $item_type,
            		'itemId' => $item_id
            		))->row_array()) {
            	// update
            	$b = $CI->db2->where(array(
            		'itemType' => $item_type,
            		'itemId' => $item_id
            		))->update($CI->_tables['homepagedata'], $data);
            } else {
            	// insert
            	$data['lastUpdate'] = now();
            	$b = $CI->db2->insert($CI->_tables['homepagedata'], $data);
            }
            
            if(empty($b)) {
                return '推荐到首页失败';
            }
        } else {
            // 取消了首页推荐
            $b = $CI->db2->delete($CI->_tables['homepagedata'], array('itemType' => $item_type, 'itemId' => $item_id));
            if(empty($b)) {
                return '取消首页推荐失败';
            }
        }
    }
    
    if($b) {
        // 去刷新首页缓存
        $CI->load->helper('poi');
        api_update_cache($CI->_tables['homepagedata']);
    }
    
    return 0;
}

/**
 * 提交图片到接口
 * @param 图片名称 $file_name
 * @param 图片类型 $file_type
 * @param 图片格式 $resolution
 */
function api_upload($file_name, $file_type = 'common', $resolution = '') {
    $data = array(
            'file_type' => $file_type,
            'file' => '@' . $file_name,
            'resolution' => $resolution
    );
    $data['sign'] = rsa_sign();
    $CI = &get_instance();
    $upload_url = $CI->config->item('v3_upload_image_api');
    $result = http_request($resolution?$upload_url['transfer_image']:$upload_url['save_image'], $data, array(), 'POST', true);
    $json = json_decode($result, true);
    return $json['result_code']?false:$json['file_name'];
}

/**
 * 返回rank order
 * @param int $item_type
 * @param int $item_id
 */
function get_ext_rank_order($item_type, $item_id) {
    $CI = &get_instance();
    // 1	活动	权重加成 + 报名人数 x 10
    // 2	图片POST	权重加成 + (赞+踩) x 3 + 分享 x 7
    // 3	商品	权重加成 + 购买数 x 7 + 分享 x 3
    // 4	地点	权重加成 + 点评数 x 4 + 地点册数 x 2 + 地主抢劫数 x 2
    // 5	地点册	权重加成 + (赞+踩) x 3 + 分享数 x 5 + 收藏数 x 2
    $rank_order = 0;
    switch($item_type) {
        case 1:
            // 地点
            $row = $CI->db2->get_where($CI->_tables['place'], 
                        array('id' => $item_id))->row_array();
            $rank_order = intval($row['tipCount'])*4 + 
                          intval($row['atCollectionCount'])*2 + 
                          intval($row['robCount'])*2;
            break;
        case 5:
            // 活动 活动参与人数
            $num = $CI->db2->where(array('eventId' => $item_id))
                            ->from($CI->_tables['webeventapply'])->count_all_results();
            $rank_order = $num * 10;
            break;
        case 18:
        case 19:
            // YY和POST
            $row = $CI->db2->get_where($CI->_tables['post'],
                    array('id' => $item_id))->row_array();
            $rank_order = intval($row['praiseCount'])*3 +
                          intval($row['stampCount'])*3 +
                          intval($row['shareCount'])*7;
            break;
        case 20:
            // 地点册
            $row = $CI->db2->get_where($CI->_tables['placecollection'],
                    array('id' => $item_id))->row_array();
            $rank_order = intval($row['praiseCount'])*3 +
                          intval($row['stampCount'])*3 +
                          intval($row['shareCount'])*5 + 
                          intval($row['beFavorCount'])*2;
            break;
        case 23:
            // 商品
            // 购买数
            $row = $CI->db2->get_where($CI->_tables['product'],
                        array('id' => $item_id))->row_array();
            $rank_order = intval($row['buyerCount'])*7 +
                          intval($row['shareCount'])*3;
            break;
    }
    
    return $rank_order;
}
