<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/*
 * 对外数据接口
 *
 * Author: piglet chenglin.zhu@gmail.com
 * Created on 2012-6-27
 */

class Api extends MY_Controller {
    /**
     * 兼容1.0的接口
     /**
     * 获取feed的数据 ************************* URL参数
     *
     * @param placeId
     *            地点ID 可以多个用,分割
     * @param eventId
     *            活动ID 可以多个用,分割 （注：活动ID带入是为了取出placeId，两者为or关系）
     * @param cid
     *            数据类型 签到53、点评54、勋章55 可以多个用,分割
     * @param badgeId
     *            勋章ID 可以多个用,分割
     * @param image
     *            是否含图片 0：无图 1：有图
     * @param content
     *            内容关键字 模糊查询
     * @param datetime
     *            时间戳 开始时间和结束时间 格式：2011-11-11 23:59:59,2012-12-12 23:59:59
     *            年与日时分秒，如果从某个时间到现在时间带入一个时间
     * @param id
     *            ID号得范围 10,100 10<id<100 也可以是 10 那么为id>10
     * @param order
     *            排序 desc 倒序 默认为正序 asc
     * @param pageSize
     *            每页显示条数
     * @param pageNum
     *            第几页 ****************************
     *
     */
    function rtf_feed() {
        // 参数
        $page = $this->input->get('pageNum');
        $page_size = $this->input->get('pageSize');
        $place_id = str_replace('|', ',', $this->input->get('placeId'));
        $event_id = str_replace('|', ',', $this->input->get('eventId'));
        $cid = str_replace('|', ',', $this->input->get('cid'));
        $badge_id = str_replace('|', ',', $this->input->get('badgeId'));
        $image = $this->input->get('image');
        $content = $this->input->get('content');
        $id = $this->input->get('id');
        $datetime = $this->input->get('datetime');
        $order = $this->input->get('order');
        $callback = $this->input->get('callback');

        $cids = array();
        if (strpos($cid, ',') === false) {
            // 不存在多个
            $cids[] = intval($cid);
        } else {
            // 多个
            $cids = explode(',', $cid);
        }
        if ($cids) {
            $type = array();
            foreach ($cids as $cid) {
                switch($cid) {
                    case 53 :
                        $type[] = 1;
                        break;
                    case 54 :
                        $type[] = 2;
                        break;
                }
            }
        }
        $image && $type[] = 3;

        if ($datetime) {
            $datetime = urldecode($datetime);
            $d = explode(',', $datetime);
            foreach ($d as &$r) {
                $r = date('YmdHis', strtotime($r));
            }
            unset($r);
            $datetime = implode(',', $d);
        }

        $content && $content = urldecode($content);

        ob_start();
        $this->feed($page ? $page : 1, $page_size ? $page_size : 200, $place_id ? $place_id : 0, $type ? implode(',', $type) : 0, $content, $id ? $id : 0, $datetime ? $datetime : '', $order == 'asc' ? 0 : 1, $callback);
        $json = ob_get_contents();
        ob_end_clean();

        $data = json_decode($callback?substr($json, strlen($callback)+1, -1):$json, true);
        unset($json);
        // sysMsg: "操作成功",
        // time: "2012-06-30 12:00:06",
        // resultCount: {
        // totalNum: 42227
        // },
        // hour24Count: {
        // totalNum: 235
        // },
        // errorCode: 0

        $out_data = array();
        $out_data['sysMsg'] = '操作成功';
        $out_data['time'] = date('Y-m-d H:i:s');
        $out_data['resultCount'] = array('totalNum' => $data['total'] ? $data['total'] : 0);
        $out_data['hour24Count'] = array('totalNum' => $data['total24'] ? $data['total24'] : 0);
        $out_data['errorCode'] = 0;
        $out_data['resultList'] = array();
        if ($data['list']) {
            foreach ($data['list'] as $row) {
                $r = array();

                $r['created'] = $row['create_date'];
                $r['avatarUri'] = $row['avatar'];
                $r['postId'] = $row['id'];
                $r['web'] = $row['avatar_udpl'];
                $r['placename'] = $row['placename'];
                $r['longitude'] = $row['longitude'];
                $r['latitude'] = $row['latitude'];
                $r['thumbWall'] = $row['photo_thweb'];
                $r['username'] = $row['nickname'] ? $row['nickname'] : $row['username'];
                $r['photoThumbUri'] = $row['photo_thudp'];
                $r['placeId'] = $row['place_id'];
                $r['type'] = intval($row['type']) + 52;
                $r['uid'] = $row['uid'];
                $r['photoMiddleUri'] = $row['photo_thudp'];
                $r['postText'] = $row['content'];
                $r['thumbWeb'] = $row['photo_thweb'];
                $r['ogrinial'] = $row['photo_odp'];
                $r['thumbWallHeight'] = $row['photo_height'];

                $out_data['resultList'][] = $r;
            }
        }
        unset($data);

        $this->echo_json($out_data, $callback);
    }

    /**
     * 1.0的评房网
     */
    function rtf_white() {
        $page_size = $this->input->get('limit');
        $callback = $this->input->get('callback');

        ob_start();
        $this->feed(1, $page_size ? $page_size : 20, 0, 2, '', 0, '', 1, $callback);
        $json = ob_get_contents();
        ob_end_clean();
        
        $data = json_decode($callback?substr($json, strlen($callback)+1, -1):$json, true);
        unset($json);

        // sysMsg: "操作成功",
        // length: 20,
        // errorCode: 0,
        // array: [
        // {
        // placename: "中德英伦联邦",
        // gender: 2,
        // text: "好久没有购物了！ ",
        // avatarUri: "http://pic.in.chengdu.cn/img/uhead/327632_1331064123502.jpg",
        // uid: 327632,
        // username: "左手不会发牌",
        // id: 833220
        // }]

        $out_data = array();
        $out_data['sysMsg'] = '操作成功';
        $out_data['length'] = count($data['list']);
        $out_data['errorCode'] = 0;
        if ($data['list']) {
            foreach ($data['list'] as $row) {
                $r = array();

                $r['placename'] = $row['placename'];
                $r['gender'] = $row['gender'];
                $r['text'] = $row['content'];
                $r['avatarUri'] = $row['avatar_udpl'];
                $r['uid'] = $row['uid'];
                $r['username'] = $row['nickname'] ? $row['nickname'] : $row['username'];
                $r['id'] = $row['id'];
                
                $out_data['array'][] = $r;
            }
        }
        unset($data);

        $this->echo_json($out_data, $callback);
    }

    /**
     * ciis接口对应
     *
     */
    function ciis($index = '') {
        $full = $this->input->get('full');
        $this->assign('full', $full?$full:40);
        
        // 获取最新数据
        ob_start();
        $this->feed(1, 20);
        $json = ob_get_contents();
        ob_end_clean();
        
        $data = json_decode(substr($json, 2, -1), true);
        unset($json);
        
        // Add By piggy
        if($data['list']) {
            foreach($data['list'] as &$row) {
                $row['name'] = $row['nickname']?$row['nickname']:$row['username'];
                $row['time'] = $this->_format_time($row['create_date']);
                $row['text'] = cut_string($row['content'], 20);
            }
            
            $this->assign('list', $data['list']);
        }
        
        $this->display('ciis');
    }

    /**
     * 获取POST信息
     * @param $page 页数 默认1页
     * @param $page_size 每页大小 默认：100 最大1000
     * @param $place_id 地点ID号 多个用,分开
     * @param $type 操作类型 1=chekcin,2=tip,3=photo 多个用,分开
     * @param $content 内容匹配
     * @param $id 对应信息的ID号 多个用,隔开 范围值用-隔开
     * @param $post_date 发布时间 范围值用-隔开 格式：20121231235959
     * @param $order 排序 1：倒序 0：正序 默认1
     * @param $callback 跨域需要JSONP，回调函数
     * @param $force 是否强制更新 默认 0  1/0
     * @param $is_count 是否需要统计 默认1 需要统计 1/0
     */
    function feed($page = 1, $page_size = 100, $place_id = 0, $type = 0, $content = '', $id = 0, $post_date = '', $order = 1, $callback = '?', $force = 0, $is_count = 1) {
        
        //Add By M2
        $_callback = $this->input->get('callback');
        $_callback && $callback = $_callback;
        //Add by Liuw
        $place_id = strpos(urldecode($place_id), '|')!==FALSE ? str_replace('|', ',', urldecode($place_id)) : urldecode($place_id);
       	$id = strpos(urldecode($id), '|')!==FALSE ? str_replace('|', ',', urldecode($id)):urldecode($id);
        
        $params = array(
                $page,
                $page_size,
                $place_id,
                $type,
                $content,
                $id,
                $post_date,
                $order
        );

        $cache_id = 'cache_' . implode('_', $params);

        $data = get_recommend_cache($cache_id);

        if (empty($data) || $force) {
            // 状态为0的
            $where_sql = array('p.status <= 1');

            // 页数
            $page = intval($page);
            $page <= 0 && $page = 1;

            // 每页大小
            $page_size = intval($page_size);
            $page_size <= 0 && $page_size = 100;
            $page_size > 1000 && $page_size = 1000;

            // 地点ID
            $place_id = $this->_format_params('p.placeId', $place_id);
            $place_id && $where_sql[] = $place_id;

            // 信息类型 1签到 2点评 3图片
            $type = $this->_format_params('p.type', $type);
            $type && $where_sql[] = $type;

            // 对应的信息ID号post id
            $id = $this->_format_params('p.id', $id);
            $id && $where_sql[] = $id;

            // 匹配内容
            $content = trim(urldecode($content));
            if ($content && substr_count($content, '%') != count($content)) {
                // 有搜索内容且搜索内容不是%
                $content = addslashes($content);
                $where_sql[] = "p.content like '%{$content}%'";
            }

            $where_sql = implode(' and ', $where_sql);
            // 在获取post_date参数前计算24小时的量
            $d = new DateTime();
            // 当前时间
            $d2 = $d->format('Y-m-d H:i:s');
            // 以前以前的时间
            $d->modify('-1 day');
            $d1 = $d->format('Y-m-d H:i:s');
            
            // 获取24小时的量
            $is_count && $total24 = $this->db->from('Post p')->where($where_sql . (" and p.createDate between '{$d1}' and '{$d2}'"))->count_all_results();

            // 如果传入了时间查询参数
            $post_date = trim($post_date);
            if ($post_date) {
                $post_date = explode('-', addslashes($post_date));
                if (count($post_date) > 1) {
                    // 如果发布时间多于两个
                    $where_sql .= " and p.createDate '" . _dateval($post_date[0]) . "' and '" . _dateval($post_date[1]) . "'";
                } else {
                    //
                    $where_sql .= " and p.createDate > '" . $this->_dateval($post_date) . "'";
                }
            }

            // 获取总的数据
            $is_count && $total = $this->db->from('Post p')->where($where_sql)->count_all_results();

            $order = intval($order);
            $order = $order ? 'desc' : 'asc';

            if ($total) {
                $list = $this->db->select('p.*')->from('Post p')->where($where_sql)->order_by('p.createDate', $order)->limit($page_size, ($page - 1) * $page_size)->get()->result_array();
                                
                // #用户上传(post)
                // user=udp,hdp,mdp,odp,thudp,thhdp,thmdp,thweb
                // #用户头像
                // head=hudp,hhdp,hmdp,odp,udpl,hdpl,mdpl
                // #分辨率别名所对应的分辨率
                // udp=960x?
                // hdp=640x?
                // mdp=480x?
                // #头像特别定制分辨率
                // hudp=192x192
                // hhdp=144x144
                // hmdp=96x96
                // udpl=80x80
                // hdpl=60x60
                // mdpl=40x40
                // #上传缩略图
                // thudp=180x180
                // thhdp=135x135
                // thmdp=90x90
                // thweb=200x?
                // #原图
                // odp=?x?

                $data = array();
                if ($list) {
                    // 获取地点信息
                    // 获取用户信息
                    $place_ids = $uids = array();
                    foreach($list as $row) {
                        in_array($row['placeId'], $place_ids) || ($place_ids[] = $row['placeId']);
                        in_array($row['uid'], $uids) || ($uids[] = $row['uid']);
                    }
                    
                    // pp.placename, pp.latitude, pp.longitude, u.nickname, u.username, u.avatar, u.gender
                    $places = $this->db->select('pp.id, pp.placename, pp.latitude, pp.longitude')
                                       ->get_where('Place pp', "id in ('" . implode("','", $place_ids) . "')")->result_array();

                    $users = $this->db->select('u.id, u.nickname, u.username, u.avatar, u.gender')
                                      ->get_where('User u', "id in ('" . implode("','", $uids) . "')")->result_array();
                    
                    $p = array();
                    foreach($places as $row) {
                        $p[$row['id']] = $row;
                    }
                    
                    $u = array();
                    foreach($users as $row) {
                        $u[$row['id']] = $row;
                    }

                    $user_image_size = $this->config->item('image_user');
                    $head_image_size = $this->config->item('image_head');
                    foreach ($list as $row) {
                        $r = array();

                        $r['id'] = $row['id'];
                        $r['uid'] = $row['uid'];
                        $r['place_id'] = $row['placeId'];
                        $r['placename'] = $p[$row['placeId']]['placename'];
                        $r['username'] = $u[$row['uid']]['username'];
                        $r['nickname'] = $u[$row['uid']]['nickname'];
                        $r['create_date'] = $row['createDate'];
                        $r['place_url'] = base_url() . 'place/' . $row['placeId'];
                        $r['user_url'] = base_url() . 'user/' . $row['uid'];
                        $avatar = image_url($u[$row['uid']]['avatar'], 'head');
                        if ($u[$row['uid']]['avatar'] && $u[$row['uid']]['avatar'] != 'default.png') {
                            // 如果不是默认头像或者头像为空，那么获取更多格式的头像
                            // 获取更多的头像格式
                            foreach ($head_image_size as $size) {
                                $r['avatar_' . $size] = str_replace('odp', $size, $avatar);
                            }
                        }
                        $r['avatar'] = $avatar;
                        $r['latitude'] = $p[$row['placeId']]['latitude'];
                        $r['longitude'] = $p[$row['placeId']]['longitude'];
                        $r['type'] = $row['type'];
                        if ($row['photoName']) {
                            // 如果有图片，那么
                            $r['photo'] = image_url($row['photoName'], 'user');
                            // 获取更多的图片格式
                            foreach ($user_image_size as $size) {
                                $r['photo_' . $size] = str_replace('odp', $size, $r['photo']);
                            }

                            $image = image_wh($row['photoName']);
                            if ($image !== false) {
                                $r['photo_width'] = $image['w'];
                                $r['photo_height'] = $image['h'];
                            }
                        }
                        $r['content'] = $row['content'];
                        $r['gender'] = $u[$row['uid']]['gender'];

                        $data['list'][] = $r;
                    }
                    unset($list);
                }
            }
            $total && $data['total'] = $total;
            $total24 && $data['total24'] = $total24;

            set_recommend_cache($cache_id, $data);
        }
        $data || $data = array();
        echo $callback ? ($callback . '(' . json_encode($data) . ')') : json_encode($data);
    }
    
    /**
     * 格式化时间
     */
    function _format_time($datetime) {
        $time = strtotime($datetime);
        $timediff = time() - $time;
        if($timediff > 3600) {
            return intval($timediff/3600) . '小时前';
        } elseif($timediff > 60) {
            return intval($timediff/60) . '分钟前';
        } else {
            return $timediff . '秒前';
        } 
    }
    
    /**
     * 格式化参数
     * @param $field 字段
     * @param $param 参数
     */
    function _format_params($field, $param) {
        $param = trim(urldecode($param));
        if (empty($param)) {
            return '';
        }
        $params = explode('-', $param);
        if (count($params) > 1) {
            array_walk($params, "_intval");
            // 范围值
            return "{$field} between {$params[0]} and {$params[1]}";
        } else {
            //
            $params = explode(',', $param);
            if (count($params) > 1) {
                // 那么为IN语句
                array_walk($params, "_intval");
                return "{$field} in (" . implode(',', $params) . ")";
            } else {
                // 一个
                $param = intval($param);
                return "{$field} = {$param}";
            }
        }
    }
    
    /**
     * 兼容1.0 跳转到地点图片墙
     */
    function place_wall() {
        $place_id = $this->input->get('placeId');
        
        redirect(base_url() . 'place_photo/' . $place_id);
    }
    
    /**
     * 兼容1.0 跳转到地点图片详情页
     */
    function place_pic_detail() {
        $post_id = $this->input->get('postId');
        // $place_id = $this->input->get('placeId');
        
        redirect(base_url() . 'review/' . $post_id);
    }
    
    /**
     * 兼容1.0 跳转到用户图片墙
     */
    function user_wall() {
        $uid = $this->input->get('uid');
        
        redirect(base_url() . 'user_photo/' . $uid);
    }

}

/**
 *
 */
function _intval(&$value) {
    $value = intval($value);
}

/**
 * 格式时间 传入为20121231235959
 */
function _dateval($date) {
    $y = substr($date, 0, 4);
    $m = substr($date, 4, 2);
    $d = substr($date, 6, 2);
    $h = substr($date, 8, 2);
    $i = substr($date, 10, 2);
    $s = substr($date, 12, 2);
    $time = mktime($h, $i, $s, $m, $d, $y);

    return date('Y-m-d H:i:s', $time);
}
