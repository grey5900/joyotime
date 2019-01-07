<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/*
 * 商品管理
 *
 * Author: piglet chenglin.zhu@gmail.com
 * Created on 2012-5-28
 */

class My_Goods extends MY_Controller {
    function __construct() {
        parent::__construct();
        $this->assign('editor_image', "upImgUrl=\"" . site_url(array(
                'main',
                'upload'
        )) . "\" upImgExt=\"jpg,jpeg,gif,png\"");
        
        $this->load->helper('goods');
    }

    /**
     * 团购
     */
    function groupon() {

        $this->display('groupon');
    }

    /**
     * 备选团购
     * status 0：可以用 1：不能选 2：已选择
     */
    function groupon_source() {
        // 判断备选中的数据是否已经过期，如果已过期，那么改变状态为1，不可以用
        $this->db->where('now() > endTime and status = 0')->update('GrouponSourceItem', array('status' => 1));
        // 如果又可以上架了
        $this->db->where('now() < endTime and status = 1')->update('GrouponSourceItem', array('status' => 0));

        // 获取备选数据
        $status = $this->post('status');
        $keywords = trim($this->post('keywords'));
        // $type = $this->post('type');
        $type = 2;
        $status || $status = 0;
        
        $where_sql = $status ? "status > $status" : "status = '$status'";
        $source_type = $this->config->item('source_type');
        $type && $where_sql .= " AND sourceType = '2'";
        if($keywords !== '') {
            $keytext = daddslashes($keywords);
            $where_sql .= " AND product LIKE '%{$keytext}%'";
        }

        $total_num = $this->db->where($where_sql)->from('GrouponSourceItem')->count_all_results();
        $paginate = $this->paginate($total_num);
        
        $groupon_source = $this->config->item('groupon_source');
        $list = $this->db->order_by('beginTime', 'desc')->limit($paginate['per_page_num'], $paginate['offset'])->where($where_sql)->get('GrouponSourceItem')->result_array();
        $this->assign(compact('status', 'keywords', 'list', 'groupon_source', 'type'));

        $this->display('groupon_source');
    }

    /**
     * 等待上架
     */
    function groupon_waiting() {
        // 判断备选中的数据是否已经过期，如果已过期，那么改变状态为1，不可以用
        $this->db->where('now() > endDate and status = -1')->update('GrouponItem', array('status' => -2));

        // 获取备选数据
        $status = $this->post('status');
        $keywords = $this->post('keywords');
        $status || $status = -1;

        $where_sql = "gi.sourceType = 2 AND gi.status = '$status'";
        $keywords && $where_sql .= " and gi.title like '%{$keywords}%'";

        $total_num = $this->db->where($where_sql)->from('GrouponItem gi')->count_all_results();
        $paginate = $this->paginate($total_num);

        $list = $this->db->select("gi.*, count(distinct(gip.placeId)) as placeNum, gsi.teamUrl")->order_by('startDate', 'desc')->group_by('gi.id')->limit($paginate['per_page_num'], $paginate['offset'])->join('GrouponItemAtPlace gip', 'gi.id = gip.grouponId', 'left')->join('GrouponSourceItem gsi', 'gi.sourceId = gsi.id', 'left')->where($where_sql)->get('GrouponItem gi')->result_array();
        
        $ship_type = $this->config->item('ship_type');
        $this->assign(compact('status', 'keywords', 'list', 'ship_type'));

        $this->display('groupon_waiting');
    }

    /**
     * 团购再次上架
     */
    function groupon_re_selling() {
        $id = $this->get('id');
        $groupon_item = $this->db->get_where('GrouponItem', "id = '{$id}'")->row_array();
        
        $b = true;
        if ($groupon_item) {
            $return_data = get_team($groupon_item['originalId'], $groupon_item['sourceName']);
            // 团购商品状态
            if ($groupon_item['status'] < 1) {
                $this->error('团购商品不是在下架状态，不能再次上架');
            }

            if ($return_data['sourceStatus'] != 1) {
                // 对方已下架状态
                $this->error('团购商品在来源方那里已经下架了，不能再次上架');
            }

            $b &= $this->db->where("id = '{$id}'")->update('GrouponItem', array('status' => 0));
        }

        $b ? $this->success('团购商品重新上架啦。') : $this->error('团购商品重新上架出错误啦，请稍后重试');
    }

    /**
     * 放入待上架
     */
    function to_waiting() {
        $id = intval($this->get('id'));
        
        // 只获取状态0的数据才能被选中
        $row = $this->db->get_where('GrouponSourceItem', array('id' => $id, 'status' => 0))->row_array();
        $b = true;
        if ($row) {
            // 检查是否已经选择到了待上架中
            if ($this->db->get_where('GrouponItem', "sourceId = '{$row['id']}'")->row_array()) {
                // 已近选择到了上架中去，
                $this->error('该商品已经选择过了，亲，不能再选择上架哦。');
            }
            // 转移到GrouponItem表
            $data = array();
            $data['sourceId'] = $row['id'];
            $data['originalId'] = $row['originalId'];
            $data['sourceName'] = $row['grouponName'];
            $data['grouponPrice'] = $row['teamPrice'];
            $data['marketPrice'] = $row['marketPrice'];
            $data['title'] = $row['product'];
            $data['name'] = $row['product'];
            $data['joinedCount'] = $row['nowNumber'];
            $data['imageUri'] = $row['imageThumb'];
            $data['content'] = $row['title'];
            $data['expireDate'] = $row['expireTime'];
            $data['startDate'] = $row['beginTime'];
            $data['endDate'] = $row['endTime'];
            $data['introduce'] = $row['detail'];
            // 可以上架
            $data['status'] = -1;
            // 没单限制
            $data['limited'] = $row['perNumber'];
            // 提示
            $data['notice'] = $row['notice'];

            // 新加入的
            $data['sourceType'] = $row['sourceType'];
            $data['shipType'] = $row['teamType'];
            $data['stock'] = $row['stock'];
            $data['quantityLock'] = $row['soldLimit'];

            $b &= $this->db->insert('GrouponItem', $data);
            $groupon_id = $this->db->insert_id();
            // 写入到自提地点表GrouponItemOwmPickupPlace
            $pickup_places = array_filter(explode(',', trim($row['pickupLocation'])));
            if($pickup_places) {
                // 去获取一次地点信息，这样子避免ID正确
                $places = $this->db->select('id')->where("id in ('" . implode("','", $pickup_places) . "')", null, false)->get('Place')->result_array();
                $datas = array();
                foreach($places as $row) {
                    $datas[] = array('grouponId' => $groupon_id, 'placeId' => $row['id']);
                }
                
                $datas && ($b &= $this->db->insert_batch('GrouponItemOwmPickupPlace', $datas));
            }

            // 设置原始数据状态为2
            $b &= $this->db->where("id = '{$id}'")->update('GrouponSourceItem', array('status' => 2));
        } else {
            $b = false;
        }

        $b ? $this->success('商品已进入【待上架】') : $this->error('选择进入【待上架】错误，请稍后重试');
    }

    /**
     * 上架
     */
    function groupon_sale() {
        // 上架及设定标志为0
        $id = $this->get('id');
        // 先判断必须要有地点
        $row = $this->db->select('gi.*, count(gip.placeId) as placeNum')->from('GrouponItem gi')->join('GrouponItemAtPlace gip', 'gi.id = gip.grouponId', 'left')->where("gi.id = '{$id}'")->group_by('gi.id')->get()->row_array();

        $b = true;
        if ($row) {
            //empty($row['placeNum']) && $this->error('请先关联商品的地点');
            
            if ($row['grouponPrice'] > $row['marketPrice']) {
                $this->error('团购价不能比市场价都贵哦。嘿，兄弟姐妹，请检查，确认。');
            }
            // 判断不能大于128
            if(strlen($row['title'])>128) {
                $this->error('标题不能超过128个字符哦，亲，一个中文3个字符哈，英文数字算1个。');
            }

            if ($row['status'] == -1) {
                // 只有为-1的状态才能上架
                // 先去生成
                generate_static_html($id) || $this->error('上架失败，无法生成详细信息');
                $b &= $this->db->where("id = '{$id}'")->update('GrouponItem', array(
                        'status' => 0,
                        'createDate' => date('Y-m-d H:i:s')
                ));
                $b &= $this->_update_place_goods($id, 1, 0);
            } elseif ($row['status'] == 0) {
                // 商品已上架
                $this->success('商品已上架');
            } else {
                $b = false;
            }
        } else {
            $b = false;
        }

        $b ? $this->success('上架成功', '', '', '', array('id' => $id)) : $this->error('团购商品上架失败');
    }

    /**
     * 下架
     */
    function groupon_sale_off() {
        // 上架及设定标志为3，手动下架的
        $id = $this->get('id');

        $b = true;
        // 作废订单
        // $b = $this->db->where("isPayed = 0 and itemType = 0 and status = 0 and itemId ='{$id}'")
        // ->update('OrderInfo', array('status' => 1));
        // 正常和结束状态的都可以下架
        $b &= $this->db->where("id = '{$id}' and status in (0, 2)")->update('GrouponItem', array('status' => 1));

        $b &= $this->_update_place_goods($id, 1, 1);

        $b ? $this->success('下架成功', '', '', '', array('id' => $id)) : $this->error('团购商品下架失败');
    }
    
    /**
     * 编辑团购
     */
    function groupon_edit() {
        $page_id = 'groupon_edit';
        $groupon = $this->_get_groupon($page_id);
        
        if($this->is_post()) {
            // 判断团购
            if($groupon['status'] != -1) {
                $this->error('团购状态不正确，不能编辑');
            }
            
            $this->_submit_groupon($page_id);
        }
        
        $this->display($page_id);
    }
    
    /**
     * 编辑进行中的团购
     */
    function groupon_edit_selling() {
        $page_id = 'groupon_edit_selling';
        $groupon = $this->_get_groupon($page_id);
        
        if($this->is_post()) {
            // 判断团购
            if($groupon['status'] != 0) {
                $this->error('团购状态不正确，不能编辑');
            }
            
            $this->_submit_groupon($page_id, true);
        }
        
        $this->display('groupon_edit');
    }
    
    /**
     * 浏览团购
     */
    function groupon_view() {
        $page_id = 'groupon_view';
        $this->_get_groupon($page_id);
        
        $this->display('groupon_edit');
    }
    
    /**
     * 提交一个团购修改
     * @param $selling 是否出售中
     */
    private function _submit_groupon($page_id, $selling = false) {
        $id = intval($this->get('id'));
        // 提交
        $data = array();
        $data['title'] = trim($this->post('title'));
        // 判断不能大于128
        if(strlen($data['title'])>128) {
            $this->error('标题不能超过128个字符哦，亲，一个中文3个字符哈，英文数字算1个。');
        }
        $data['name'] = $data['title'];
        $data['content'] = trim($this->post('content'));
        $data['notice'] = trim($this->post('notice'));
        $data['introduce'] = $this->post('introduce');
        empty($selling) && ($data['grouponPrice'] = $this->post('groupon_price'));
        $data['limited'] = intval($this->post('limited'));
        $data['deviceLimited'] = intval($this->post('device_limited'));
        $data['deviceLimitedDays'] = intval($this->post('device_limited_days'));

        if ($data['deviceLimited'] < 0 || $data['deviceLimitedDays'] < 0) {
            $this->error('兄弟姐妹，设备限制数量不能小于0哈');
        } elseif ($data['deviceLimited'] > 0 && $data['deviceLimitedDays'] <= 0) {
            $this->error('哦，允许设备购买数量设定了，你要设定限购的天数哦。');
        }
        // 缩略图
        $image_uri = $this->post($page_id . '_image');
        if (strpos($image_uri, 'http://') !== 0) {
            $image_uri = image_url($image_uri, 'common', 'odp');
        }
        $data['imageUri'] = $image_uri;

        $b = $this->db->where("id = '{$id}'")->update('GrouponItem', $data);

        // 写入地点
        $place = $this->post('place');
        $batch_data = array();
        if ($place) {
            $place = array_unique(array_filter($place));
            foreach ($place as $p) {
                $row = array();
                $row['placeId'] = $p;
                $row['grouponId'] = $id;

                $batch_data[] = $row;
            }
        }
        // 先删除之前的地点
        $b &= $this->db->delete('GrouponItemAtPlace', "grouponId = '{$id}'");
        if ($batch_data) {
            $b &= $this->db->insert_batch('GrouponItemAtPlace', $batch_data);
        }

        $b ? $this->success('修改团购信息成功', build_rel(array(
                'my_goods',
                $page_id
        )), site_url(array(
                'my_goods',
                $page_id,
                'id',
                $id
        ))) : $this->error('修改团购信息出错啦'); 
    }

    /**
     * 公用获取团购
     */
    private function _get_groupon($page_id) {
        $id = intval($this->get('id'));
        // 选出团购信息
        $groupon = $this->db->select('*, ifnull(quantityLock, 0) as quantityLock', false)
                            ->get_where('GrouponItem', "id = '{$id}'")->row_array();
        empty($groupon) && $this->error('错误的信息');
        
        $this->assign('page_id', $page_id);
        // 选出地点
        $place = $this->db->select('p.id, p.placename')->where("gip.grouponId = '{$id}' and p.id = gip.placeId")->get('GrouponItemAtPlace gip, Place p')->result_array();
        $this->assign('place', $place);
        $this->assign('ship_type', $this->config->item('ship_type'));
        $this->assign('groupon', $groupon);
        if($groupon['shipType'] == 1) {
            // 自提
            $pickup_places = $this->db->select('b.placename')
                                      ->where("a.grouponId = '{$groupon['id']}' AND a.placeId = b.id", null, false)
                                      ->get('GrouponItemOwmPickupPlace a, Place b')
                                      ->result_array();
            $this->assign('pickup_places', $pickup_places);
        }
        
        return $groupon;
    }

    // /**
     // * 编辑
     // */
    // function groupon_edit() {
        // $id = intval($this->get('id'));
        // // 选出团购信息
        // $groupon = $this->db->select('*, ifnull(quantityLock, 0) as quantityLock', false)
                            // ->get_where('GrouponItem', "id = '{$id}'")->row_array();
        // empty($groupon) && $this->error('错误的信息');
//         
        // if ($this->is_post()) {
            // // 提交
            // $data = array();
            // $data['title'] = trim($this->post('title'));
            // // 判断不能大于128
            // if(strlen($data['title'])>128) {
                // $this->error('标题不能超过128个字符哦，亲，一个中文3个字符哈，英文数字算1个。');
            // }
            // $data['name'] = $data['title'];
            // $data['content'] = trim($this->post('content'));
            // $data['notice'] = trim($this->post('notice'));
            // $data['introduce'] = $this->post('introduce');
            // $data['grouponPrice'] = $this->post('groupon_price');
            // $data['limited'] = intval($this->post('limited'));
            // $data['deviceLimited'] = intval($this->post('device_limited'));
            // $data['deviceLimitedDays'] = intval($this->post('device_limited_days'));
// 
            // if ($data['deviceLimited'] < 0 || $data['deviceLimitedDays'] < 0) {
                // $this->error('兄弟姐妹，设备限制数量不能小于0哈');
            // } elseif ($data['deviceLimited'] > 0 && $data['deviceLimitedDays'] <= 0) {
                // $this->error('哦，允许设备购买数量设定了，你要设定限购的天数哦。');
            // }
            // // 缩略图
            // $image_uri = $this->post('groupon_edit_image');
            // if (strpos($image_uri, 'http://') !== 0) {
                // $image_uri = image_url($image_uri, 'common', 'odp');
            // }
            // $data['imageUri'] = $image_uri;
// 
            // $b = $this->db->where("id = '{$id}'")->update('GrouponItem', $data);
// 
            // // 写入地点
            // $place = $this->post('place');
            // $batch_data = array();
            // if ($place) {
                // foreach ($place as $p) {
                    // $row = array();
                    // $row['placeId'] = $p;
                    // $row['grouponId'] = $id;
// 
                    // $batch_data[] = $row;
                // }
            // }
            // // 先删除之前的地点
            // $b &= $this->db->delete('GrouponItemAtPlace', "grouponId = '{$id}'");
            // if ($batch_data) {
                // $b &= $this->db->insert_batch('GrouponItemAtPlace', $batch_data);
            // }
// 
            // $b ? $this->success('修改团购信息成功', build_rel(array(
                    // 'goods',
                    // 'groupon_edit'
            // )), site_url(array(
                    // 'goods',
                    // 'groupon_edit',
                    // 'id',
                    // $id
            // ))) : $this->error('修改团购信息出错啦');
        // }
        // $this->assign('page_id', 'groupon_edit');
        // // 选出地点
        // $place = $this->db->select('p.id, p.placename')->where("gip.grouponId = '{$id}' and p.id = gip.placeId")->get('GrouponItemAtPlace gip, Place p')->result_array();
        // $this->assign('place', $place);
        // $this->assign('ship_type', $this->config->item('ship_type'));
        // $this->assign('groupon', $groupon);
        // if($groupon['shipType'] == 1) {
            // // 自提
            // $pickup_places = $this->db->select('b.placename')
                                      // ->where("a.grouponId => '{$groupon['id']}' AND a.placeId = b.id", null, false)
                                      // ->get('GrouponItemOwmPickupPlace a, Place b')
                                      // ->result_array();
            // $this->assign('pickup_places', $pickup_places);
        // }
//         
        // $this->display('groupon_edit');
    // }
// 
    // /**
     // * 编辑正在进行的团购
     // */
    // function groupon_edit_selling() {
        // $id = $this->get('id');
        // // 选出团购信息
        // $groupon = $this->db->get_where('GrouponItem', "id = '{$id}'")->row_array();
        // if($groupon['status'] > 0) {
            // $this->error('商品已下架不能在编辑了哦，亲');
        // }
        // if ($this->is_post()) {
            // // 先判断地点是否有选择
            // $place = $this->post('place');
            // if (empty($place)) {
                // $this->error('关联地点不能为空');
            // }
// 
            // // 提交
            // $data = array();
            // $data['title'] = trim($this->post('title'));
            // // 判断不能大于128
            // if(strlen($data['title'])>128) {
                // $this->error('标题不能超过128个字符哦，亲，一个中文3个字符哈，英文数字算1个。');
            // }
            // $data['name'] = $data['title'];
            // $data['content'] = trim($this->post('content'));
            // $data['notice'] = trim($this->post('notice'));
            // $data['introduce'] = $this->post('introduce');
            // // $data['grouponPrice'] = $this->post('groupon_price');
            // $data['limited'] = intval($this->post('limited'));
            // $data['deviceLimited'] = intval($this->post('device_limited'));
            // $data['deviceLimitedDays'] = intval($this->post('device_limited_days'));
// 
            // if ($data['deviceLimited'] < 0 || $data['deviceLimitedDays'] < 0) {
                // $this->error('兄弟姐妹，设备限制数量不能小于0哈');
            // } elseif ($data['deviceLimited'] > 0 && $data['deviceLimitedDays'] <= 0) {
                // $this->error('哦，允许设备购买数量设定了，你要设定限购的天数哦。');
            // }
// 
            // // 先生成
            // $this->_generate_static_html($id) || $this->error('修改失败，无法生成详细信息');
// 
            // // 缩略图
            // $image_uri = $this->post('groupon_edit_selling_image');
            // if (strpos($image_uri, 'http://') !== 0) {
                // $image_uri = image_url($image_uri, 'common', 'odp');
            // }
            // $data['imageUri'] = $image_uri;
// 
            // $b = $this->db->where("id = '{$id}'")->update('GrouponItem', $data);
// 
            // // 写入地点
// 
            // $batch_data = array();
            // foreach ($place as $p) {
                // $row = array();
                // $row['placeId'] = $p;
                // $row['grouponId'] = $id;
// 
                // $batch_data[] = $row;
            // }
            // // 先删除之前的地点
            // $b &= $this->db->delete('GrouponItemAtPlace', "grouponId = '{$id}'");
            // $b &= $this->db->insert_batch('GrouponItemAtPlace', $batch_data);
// 
            // $b ? $this->success('修改在售团购信息成功', build_rel(array(
                    // 'goods',
                    // 'groupon_edit_selling'
            // )), site_url(array(
                    // 'goods',
                    // 'groupon_edit_selling',
                    // 'id',
                    // $id
            // ))) : $this->error('修改在售团购信息出错啦');
        // }
        // // 选出地点
        // $place = $this->db->select('p.id, p.placename')->where("gip.grouponId = '{$id}' and p.id = gip.placeId")->get('GrouponItemAtPlace gip, Place p')->result_array();
        // $this->assign('place', $place);
        // $this->assign('page_id', 'groupon_edit_selling');
        // $this->assign('groupon', $groupon);
// 
        // $this->display('groupon_edit_selling');
    // }
// 
    // /**
     // * 浏览团购
     // */
    // function groupon_view() {
        // $id = $this->get('id');
        // // 选出团购信息
        // $groupon = $this->db->get_where('GrouponItem', "id = '{$id}'")->row_array();
// 
        // // 选出地点
        // $place = $this->db->select('p.id, p.placename')->where("gip.grouponId = '{$id}' and p.id = gip.placeId")->get('GrouponItemAtPlace gip, Place p')->result_array();
        // $this->assign('place', $place);
        // $groupon['imageUri'] = image_url($groupon['imageUri'], 'common');
        // $this->assign('groupon', $groupon);
// 
        // $this->display('groupon_view');
    // }

    /**
     * -2：不可以上架
     * -1：已编辑可上架
     * 0：正常（正常状态下，如果开始时间已经到了，那么为未开始状态）
     * 1：已下架
     * 2：已结束
     */
    function groupon_selling() {
        // 修改已下单订单状态为已作废
        // $this->db->where('isPayed = 0 and itemType = 0 and status = 0 and itemId in (SELECT id FROM GrouponItem WHERE now() > endDate AND status = 0)')
        // ->update('OrderInfo', array('status' => 1));
        $datetime = date('Y-m-d H:i:s');
        // 判断的数据是否已经过期，如果已过期，那么改变状态为2 已结束 而不是 改成1下架
        $this->db->where(array('status' => 0))->where('endDate < ', $datetime)->update('GrouponItem', array('status' => 2));

        // 获取备选数据
        $keywords = $this->post('keywords');
        $status = $this->post('status');
        
        $where_sql = 'gi.sourceType = 2';
        // 状态判断
        switch($status) {
            case 1:
                // 售卖中
                $where_sql .= " AND gi.status = 0 and '{$datetime}' between gi.startDate and gi.endDate";
                break;
            case 2:
                // 未开始
                $where_sql .= " AND gi.status = 0 and gi.startDate > '{$datetime}'";
                break;
            case 3:
                // 已结束
                $where_sql .= ' AND gi.status = 2';
                break;
            default:
                // 全部
                $where_sql .= ' AND gi.status in (0, 2)';
                $status = 0;
        }
        
        if($keywords !== '') {
            $keytext = daddslashes($keywords);
            $where_sql .= " and gi.title like '%{$keytext}%'";
        }

        $total_num = $this->db->where($where_sql)->from('GrouponItem gi')->count_all_results();
        $paginate = $this->paginate($total_num);

        // 排序字段
        $order_field = $this->post('orderField');
        // 排序方式
        $order_direction = $this->post('orderDirection');
        empty($order_field) && $order_field = 'gi.startDate';
        empty($order_direction) && $order_direction = 'desc';
        $this->db->order_by($order_field, $order_direction);
        $list = $this->db->select("gi.*, count(distinct(gip.placeId)) as placeNum, gsi.teamUrl")->group_by('gi.id')->limit($paginate['per_page_num'], $paginate['offset'])->join('GrouponItemAtPlace gip', 'gi.id = gip.grouponId', 'left')->join('GrouponSourceItem gsi', 'gi.sourceId = gsi.id', 'left')->where($where_sql)->get('GrouponItem gi')->result_array();
        // 去获取订单数，出售数
        $groupon_ids = $groupon_list = array();
        foreach ($list as $row) {
            $groupon_ids[] = $row['id'];
            // 计算状态
            if($row['status'] == 2) {
                $row['status_name'] = '已结束';
                $row['status_class'] = 'tip_red';
            } else {
                // 当前时间戳
                $dateline = strtotime($datetime);
                $start_timestamp = strtotime($row['startDate']); 
                $end_timestamp = strtotime($row['endDate']);
                
                if($dateline >= $start_timestamp and $dateline <= $end_timestamp) {
                    $row['status_name'] = '售卖中';
                    $row['status_class'] = 'tip_green';
                } elseif($dateline < $start_timestamp) {
                    $row['status_name'] = '未开始';
                    $row['status_class'] = 'tip_blue';
                } else {
                    $row['status_name'] = '已结束';
                    $row['status_class'] = 'tip_red';
                }
            }
            $groupon_list[$row['id']] = $row;
            $groupon_list[$row['id']]['orderNum'] = 0;
            $groupon_list[$row['id']]['saleNum'] = 0;
        }
        $count_list = $this->db->select('oi.itemId, count(oi.id) as orderNum, sum(oi.quantity) as saleNum')->group_by('oi.itemId')->get_where('OrderInfo oi', "oi.itemType = 12 and oi.itemId in ('" . implode("','", $groupon_ids) . "') and oi.isPayed = 1")->result_array();

        foreach ($count_list as $row) {
            if (empty($groupon_list[$row['itemId']])) {
                continue;
            }
            $groupon_list[$row['itemId']]['orderNum'] = $row['orderNum'];
            $groupon_list[$row['itemId']]['saleNum'] = $row['saleNum'];
        }

        $this->assign(array(
                'keywords' => $keywords,
                'list' => $groupon_list,
                'status' => $status,
                'order_field' => $order_field, 
                'order_direction' => $order_direction
        ));

        $this->display('groupon_selling');
    }

    /**
     * 已下架商品 包括状态 // 1 售罄 2 过期 3 手动下架
     * 现在状态只有2中 0正常1过期
     */
    function groupon_over() {
        if ($this->is_post()) {
            // 数据
            $keywords = $this->post('keywords');
            $source_status = $this->post('source_status');

            $where_sql = array("gi.status = 1", "gi.sourceType = 2");
            $source_status && $where_sql[] = "gi.sourceStatus = '" . ($source_status - 1) . "'";
            if ($keywords) {
                $keytext = daddslashes($keywords);
                $where_sql[] = "gi.title like '%{$keytext}%'";
            }
            $where_sql = $where_sql ? implode(' and ', $where_sql) : array();
            $total_num = $this->db->where($where_sql)->from('GrouponItem gi')->count_all_results();
            $paginate = $this->paginate($total_num);

            $list = $this->db->select("gi.*, count(distinct(gip.placeId)) as placeNum, gsi.teamUrl")->order_by('endDate', 'desc')->group_by('gi.id')->limit($paginate['per_page_num'], $paginate['offset'])->join('GrouponItemAtPlace gip', 'gi.id = gip.grouponId', 'left')->join('GrouponSourceItem gsi', 'gi.sourceId = gsi.id', 'left')->where($where_sql)->get('GrouponItem gi')->result_array();

            // 去获取订单数，出售数
            $groupon_ids = $groupon_list = array();
            foreach ($list as $row) {
                $groupon_ids[] = $row['id'];
                $groupon_list[$row['id']] = $row;
                $groupon_list[$row['id']]['orderNum'] = 0;
                $groupon_list[$row['id']]['saleNum'] = 0;
                // 去接口获取每一条的状态
                $return_data = get_team($row['originalId'], $row['sourceName']);
                if ($return_data) {
                    $groupon_list[$row['id']]['expireDate'] = $return_data['expireDate'];
                    $groupon_list[$row['id']]['startDate'] = $return_data['startDate'];
                    $groupon_list[$row['id']]['endDate'] = $return_data['endDate'];
                    $groupon_list[$row['id']]['sourceStatus'] = $return_data['sourceStatus'];
                }
            }
            $count_list = $this->db->select('oi.itemId, count(oi.id) as orderNum, sum(oi.quantity) as saleNum')->group_by('oi.itemId')->get_where('OrderInfo oi', "oi.itemType = 12 and oi.itemId in ('" . implode("','", $groupon_ids) . "') and oi.isPayed = 1")->result_array();

            foreach ($count_list as $row) {
                if (empty($groupon_list[$row['itemId']])) {
                    continue;
                }
                $groupon_list[$row['itemId']]['orderNum'] = $row['orderNum'];
                $groupon_list[$row['itemId']]['saleNum'] = $row['saleNum'];
            }

            $this->assign(array(
                    'source_status' => $source_status,
                    'keywords' => $keywords,
                    'list' => $groupon_list
            ));
        }

        $this->display('groupon_over');
    }

    /**
     * 搜索地点
     */
    function place() {
        $keywords = trim(urldecode($this->get('keywords')));
        $num = intval(trim($this->get('num')));
        $ext_condition = $this->get('ext_condition');
        
        $result = array();

        if ($keywords !== '') {
            $num > 0 && $this->db->limit($num);
            $keytext = daddslashes($keywords);
            $where_sql = "placename like '%{$keytext}%' and status = 0";
            if('no_brand' == $ext_condition) {
                // 取出没有品牌的地点
                $where_sql .= " AND ifnull(brandId, 0) = 0";
            }
            $query = $this->db->select('id, placename, ifnull(brandId, 0) as brandId', false)->where($where_sql, null, false)->order_by('id', 'asc')->get_where('Place');
            $result = $query->result_array();
        }

        die(json_encode($result));
    }

    /**
     * 关联地点
     */
    function related_place() {
        $id = $this->get('id');
        $p = $this->get('p');

        $place = $this->db->select('p.id, p.placename')->from(($p ? 'ProductAtPlace a' : 'GrouponItemAtPlace a') . ', Place p')->where('p.status = 0 and a.placeId = p.id and a.' . ($p ? 'productId' : 'grouponId') . " = '{$id}'")->get()->result_array();

        $this->assign('place', $place);

        $this->display('related_place');
    }

    /**
     * 电影票
     */
    function cinema_ticket() {
        // 判断是否电影票过期
        // 1.先要作废订单 未支付的 下架时间已经到了 本身状态正常的 商品状态正在销售的
        // $this->db->where("itemType = 1 and isPayed = 0 and status = 0 and itemId in
        // (SELECT id FROM Product WHERE type = 0 and status = 0 and (endDate <= '" . date('Y-m-d H:i:s') . "' or stock = 0))")
        // ->update('OrderInfo', array('status' => 1));
        // 2.修改商品状态为 电影票 状态正在销售的 下架时间已到 status 0:在售 1：待上架 2：售罄 3：下架 4：手动下架 5:已结束
        $this->db->where("type = 0 and status = 0 and endDate <= '" . date('Y-m-d H:i:s') . "'")->update('Product', array('status' => 5));
        // 3.商品库存为0的
        $this->db->where('type = 0 and status = 0 and quantity <= 0')->update('Product', array('status' => 2));
        // 4.观影券过期 观影券的expireDate已经到了时间了 本身状态正常
        $this->db->where("status = 0 and expireDate <= '" . date('Y-m-d H:i:s') . "'")->update('FilmTicketCode', array('status' => 1));

        $this->display('cinema_ticket');
    }

    /**
     * 电影票等待上架
     */
    function cinema_waiting() {
        // 获取备选数据
        $keywords = $this->post('keywords');

        $where_sql = "p.type = '0' and p.status = 1";
        // 电影票
        $keywords && $where_sql .= " and p.name like '%{$keywords}%'";

        $total_num = $this->db->where($where_sql)->from('Product p')->count_all_results();
        $paginate = $this->paginate($total_num);

        $list = $this->db->select("p.*, count(distinct(pp.placeId)) as placeNum, ftc.expireDate")->order_by('p.id', 'desc')->group_by('p.id')->limit($paginate['per_page_num'], $paginate['offset'])->join('FilmTicketCode ftc', 'ftc.productId = p.id', 'left')->join('ProductAtPlace pp', 'pp.productId = p.id', 'left')->where($where_sql)->get('Product p')->result_array();
        
        $this->assign(compact('keywords', 'list'));

        $this->display('cinema_waiting');
    }

    /**
     * 电影票上架中
     * 0：正常
     * 1：未上架
     * 2：已售罄
     * 3：已下架
     * 4：手动下架
     * 5：已结束
     */
    function cinema_selling() {
        $datetime = date('Y-m-d H:i:s');
        // 判断的数据是否已经过期，如果已过期，那么改变状态为5
        $this->db->where(array('status' => 0))->where('endDate < ', $datetime)->update('Product', array('status' => 5));
        
        $status = $this->post('status');
        $keywords = $this->post('keywords');
        
        $where_sql = 'p.type = 0';
        // $where_sql = "p.type = '0' and p.status = 0";
        // // 电影票
        // $keywords && $where_sql .= " and p.name like '%{$keywords}%'";
        
        // 状态判断
        switch($status) {
            case 1:
                // 售卖中
                $where_sql .= " and p.status in (0, 2) and '{$datetime}' between p.startDate and p.endDate";
                break;
            case 2:
                // 未开始
                $where_sql .= " and p.status in (0, 2) and p.startDate > '{$datetime}'";
                break;
            case 3:
                // 已结束
                $where_sql .= ' and p.status = 5';
                break;
            case 4:
                // 已售罄
                $where_sql .= ' and p.status = 2';
                break;
            default:
                // 全部
                $where_sql .= ' and p.status in (0, 2, 5)';
                $status = 0;
        }
        
        if($keywords !== '') {
            $keytext = daddslashes($keywords);
            $where_sql .= " and p.name like '%{$keytext}%'";
        }

        $total_num = $this->db->where($where_sql)->from('Product p')->count_all_results();
        $paginate = $this->paginate($total_num);

        $list = $this->db->select("p.*, count(distinct(pp.placeId)) as placeNum, count(distinct(ftc.id)) as ticketNum, ftc.expireDate")->order_by('p.rankOrder', 'desc')->group_by('p.id')->limit($paginate['per_page_num'], $paginate['offset'])->join('FilmTicketCode ftc', 'ftc.productId = p.id', 'left')->join('ProductAtPlace pp', 'pp.productId = p.id', 'left')->where($where_sql)->get('Product p')->result_array();

        // 去获取订单数，出售数
        $cinema_ids = $cinema_list = array();
        foreach ($list as $row) {
            $cinema_ids[] = $row['id'];
            // 计算状态
            if($row['status'] == 2) {
                $row['status_name'] = '已售罄';
            } elseif(5 == $row['status']) {
                $row['status_name'] = '已结束';
            } else {
                // 当前时间戳
                $dateline = strtotime($datetime);
                $start_timestamp = strtotime($row['startDate']); 
                $end_timestamp = strtotime($row['endDate']);
                
                if($dateline >= $start_timestamp and $dateline <= $end_timestamp) {
                    $row['status_name'] = '售卖中';
                } elseif($dateline < $start_timestamp) {
                    $row['status_name'] = '未开始';
                } else {
                    $row['status_name'] = '已结束';
                }
            }
            $cinema_list[$row['id']] = $row;
            $cinema_list[$row['id']]['orderNum'] = 0;
            $cinema_list[$row['id']]['saleNum'] = 0;
        }
        $count_list = $this->db->select('oi.itemId, count(oi.id) as orderNum, sum(oi.quantity) as saleNum')->group_by('oi.itemId')->get_where('OrderInfo oi', "oi.itemType = 13 and oi.itemId in ('" . implode("','", $cinema_ids) . "') and oi.isPayed = 1")->result_array();

        foreach ($count_list as $row) {
            if (empty($cinema_list[$row['itemId']])) {
                continue;
            }
            $cinema_list[$row['itemId']]['orderNum'] = $row['orderNum'];
            $cinema_list[$row['itemId']]['saleNum'] = $row['saleNum'];
        }

        $this->assign(array(
                'keywords' => $keywords,
                'list' => $cinema_list,
                'status' => $status
        ));

        $this->display('cinema_selling');
    }

    /**
     * 电影票下架列表
     */
    function cinema_over() {
        $keywords = $this->post('keywords');

        $where_sql = "p.type = 0 and p.status in (3, 4)";
        // 电影票
        $keywords && $where_sql .= " and p.name like '%{$keywords}%'";

        $total_num = $this->db->where($where_sql)->from('Product p')->count_all_results();
        $paginate = $this->paginate($total_num);

        $list = $this->db->select("p.*, count(distinct(pp.placeId)) as placeNum, count(distinct(ftc.id)) as ticketNum, ftc.expireDate")->order_by('p.endDate', 'desc')->group_by('p.id')->limit($paginate['per_page_num'], $paginate['offset'])->join('FilmTicketCode ftc', 'ftc.productId = p.id', 'left')->join('ProductAtPlace pp', 'pp.productId = p.id', 'left')->where($where_sql)->get('Product p')->result_array();

        // 去获取订单数，出售数
        $cinema_ids = $cinema_list = array();
        foreach ($list as $row) {
            $cinema_ids[] = $row['id'];
            $cinema_list[$row['id']] = $row;
            $cinema_list[$row['id']]['orderNum'] = 0;
            $cinema_list[$row['id']]['saleNum'] = 0;
        }
        $count_list = $this->db->select('oi.itemId, count(oi.id) as orderNum, sum(oi.quantity) as saleNum')->group_by('oi.itemId')->get_where('OrderInfo oi', "oi.itemType = 13 and oi.itemId in ('" . implode("','", $cinema_ids) . "') and oi.isPayed = 1")->result_array();

        foreach ($count_list as $row) {
            if (empty($cinema_list[$row['itemId']])) {
                continue;
            }
            $cinema_list[$row['itemId']]['orderNum'] = $row['orderNum'];
            $cinema_list[$row['itemId']]['saleNum'] = $row['saleNum'];
        }

        $this->assign(array(
                'keywords' => $keywords,
                'list' => $cinema_list
        ));

        $this->display('cinema_over');
    }

    /**
     * 下架电影票
     */
    function cinema_sale_off() {
        // 下架正在售的电影票 status = 0 ，下架前需要作废已经下单未付款的订单
        $id = $this->get('id');
        empty($id) && $this->error('错误');

        $b = true;
        // 作废未付款订单
        // $b = $this->db->where("itemType = 1 and isPayed = 0 and status = 0 and itemId = '{$id}'")
        // ->update('OrderInfo', array('status' => 1));

        // 下架商品 手动下架置为4
        // 0：正常 2：售罄 5：结束
        $b &= $this->db->where("type = 0 and status in (0, 2, 5) and id = '{$id}'")->update('Product', array('status' => 4));

        $b &= $this->_update_place_goods($id, 0, 1);

        $b ? $this->success('下架电影票成功') : $this->error('下架电影票失败');
    }

    /**
     * 修改电影票信息
     */
    function cinema_edit() {
        $this->cinema_add();
    }

    /**
     * 添加电影票
     */
    function cinema_add() {
        $id = $this->get('id');
        if($id > 0) {
            // 选出地点
            $place = $this->db->select('p.id, p.placename')->where("pp.productId = '{$id}' and p.id = pp.placeId")->get('ProductAtPlace pp, Place p')->result_array();
            $this->assign('place', $place);
            // 选出团购信息
            $cinema = $this->db->get_where('Product', "id = '{$id}' and type = '0'")->row_array();
            
            // 2012.08.30获取扩展的信息
            $properties = $this->db->get_where('ProductSpecialProperty', array('productId' => $id))->result_array();
            foreach($properties as $row) {
                $cinema['properties'][$row['propName']] = $row['propValue'];
            }
            
            $this->assign('cinema', $cinema);
        }
        

        if ($this->is_post()) {
            // 提交
            $data = array();
            $data['name'] = trim($this->post('name'));
            // 判断不能大于128
            if(strlen($data['name'])>128) {
                $this->error('标题不能超过128个字符哦，亲，一个中文3个字符哈，英文数字算1个。');
            }
            $data['description'] = trim($this->post('description'));
            $data['notice'] = trim($this->post('notice'));
            $data['image'] = $this->post('cinema_edit_image');
            // 图片
            $data['introduce'] = $this->post('introduce');
            $data['marketPrice'] = $this->post('market_price');
            $data['price'] = $this->post('price');
            $data['limited'] = intval($this->post('limited'));
            // 加入了开始时间
            $data['startDate'] = $this->post('start_date');
            $data['endDate'] = $this->post('end_date');
            $data['type'] = 0;
            // 电影票
            $data['status'] = 1;
            // 等待上架
            $data['additionalFee'] = $this->post('additional_fee');
            $data['additionalFeeDesc'] = $data['additionalFee'] ? $this->post('additional_fee_desc') : '';
            $data['stock'] = $this->post('stock');
            // 实际库存
            $data['quantity'] = $this->post('quantity');
            // 销售库存
            $data['rankOrder'] = intval($this->post('rank_order'));
            $data['deviceLimited'] = intval($this->post('device_limited'));
            $data['deviceLimitedDays'] = intval($this->post('device_limited_days'));

            if ($data['deviceLimited'] < 0 || $data['deviceLimitedDays'] < 0) {
                $this->error('兄弟姐妹，设备限制数量不能小于0哈');
            } elseif ($data['deviceLimited'] > 0 && $data['deviceLimitedDays'] <= 0) {
                $this->error('哦，允许设备购买数量设定了，你要设定限购的天数哦。');
            }

            if ($data['price'] > $data['marketPrice']) {
                $this->error('卖价不能比市场价都贵哦。嘿，兄弟姐妹，请检查，确认。');
            }

            if ($id) {
                // 修改
                if ($data['quantity'] > $data['stock']) {
                    $this->error('销售库存不能大于实际库存');
                }

                $b = $this->db->where("id = '{$id}'")->update('Product', $data);
                $tip = '修改';
            } else {
                // 新建
                $b = $this->db->insert('Product', $data);
                $id = $this->db->insert_id();
                $tip = '新建';
            }

            // 写入地点
            $place = $this->post('place');
            $batch_data = array();
            if ($place) {
                foreach ($place as $p) {
                    $row = array();
                    $row['placeId'] = $p;
                    $row['productId'] = $id;

                    $batch_data[] = $row;
                }
            }
            // 先删除之前的地点
            $b &= $this->db->delete('ProductAtPlace', "productId = '{$id}'");
            if ($batch_data) {
                $b &= $this->db->insert_batch('ProductAtPlace', $batch_data);
            }
            
            // 2012.08.30加入在线选坐
            $online_seat = $this->post('online_seat');
            // 先删除
            $this->db->delete('ProductSpecialProperty', "productId = '{$id}' and propName='chooseSeatOnline'");
            if($online_seat) {
                // 可以选
                $b &= $this->db->insert('ProductSpecialProperty', array('productId' => $id, 'propName' => 'chooseSeatOnline', 'propValue' => 1));
            }

            $b ? $this->success($tip . '电影票信息成功', build_rel(array(
                    'goods',
                    'cinema_add'
            )), site_url(array(
                    'goods',
                    'cinema_edit',
                    'id',
                    $id
            ))) : $this->error($tip . '电影票信息出错啦');
        }        
        
        $this->display('cinema_add');
    }

    /**
     * 删除电影票
     * 只有状态为1的商品可以删除
     */
    function cinema_del() {
        $id = $this->get('id');

        $row = $this->db->get_where('Product', "id = '{$id}' and type = 0")->row_array();
        $b = true;
        if ($row) {
            if ($row['status'] == 1) {
                // 先删地点
                $b = $this->db->delete('ProductAtPlace', "productId = '{$id}'");
                $b &= $this->db->delete('Product', "id = '{$id}'");
            } else {
                $b = false;
            }
        } else {
            $b = false;
        }

        $b ? $this->success('成功删除电影票') : $this->error('删除电影票出错');
    }

    /**
     * 清空观影券
     * 只有状态为1的商品可以清空
     */
    function cinema_clear_ticket() {
        $id = $this->get('id');

        $row = $this->db->get_where('Product', "id = '{$id}' and type = 0")->row_array();
        $b = true;
        if ($row) {
            if ($row['status'] == 1) {
                // 清空观影券
                $b = $this->db->delete('FilmTicketCode', "productId = '{$id}'");
                $b &= $this->db->where("id = '{$id}'")->update('Product', array(
                        'stock' => 0,
                        'quantity' => 0
                ));
            } else {
                $b = false;
            }
        } else {
            $b = false;
        }

        $b ? $this->success('成功清空观影券') : $this->error('清空观影券出错');
    }

    /**
     * 上架电影票
     * 1.已经关联地点
     * 2.状态为1，待上架状态
     * 3.已经导入观影券
     * 4.下架时间未到
     */
    function cinema_sale() {
        $id = $this->get('id');

        $row = $this->db->select('p.*, count(pp.placeId) as placeNum, count(ftc.id) as codeNum')->group_by('p.id')->from('Product p')->join('ProductAtPlace pp', "p.id = pp.productId", 'left')->join('FilmTicketCode ftc', "p.id = ftc.productId", 'left')->where("p.id = '{$id}' and p.type = 0")->get()->row_array();

        if ($row['price'] > $row['marketPrice']) {
            $this->error('卖价不能比市场价都贵哦。嘿，兄弟姐妹，请检查，确认。');
        }

        $b = true;
        if ($row) {
            if ($row['status'] == 1) {
                // 判断是否下架时间
                if (now() > strtotime($row['endDate'])) {
                    $b = false;
                    $err = '商品下架时间已过';
                } elseif ($row['placeNum'] <= 0) {
                    // 判断地点数
                    $b = false;
                    $err = '请先关联地点';
                } elseif ($row['codeNum'] <= 0) {
                    // 判断观影码
                    $b = false;
                    $err = '请导入观影码';
                } elseif ($row['quantity'] > $row['stock']) {
                    $b = false;
                    $err = '销售库存不能大于实际库存';
                } elseif ($row['quantity'] <= 0) {
                    $b = false;
                    $err = '必须要有销售库存才能上架';
                } else {
                    generate_static_html($id, 'product') || $this->error('上架失败，无法生成详细信息');
                    // 修改状态和上架时间 createDate
                    $b = $this->db->where("id = '{$id}' and type = 0")->update('Product', array(
                            'status' => 0,
                            'createDate' => date('Y-m-d H:i:s')
                    ));
                    $b &= $this->_update_place_goods($id, 0, 0);
                }
            } else {
                $b = false;
            }
        } else {
            $b = false;
        }

        $b ? $this->success('成功上架电影票') : $this->error($err ? $err : '上架电影票出错');
    }

    /**
     * 导入观影券
     */
    function cinema_ticket_add() {
        $id = $this->get('id');

        empty($id) && $this->error('错误');

        if ($this->is_post()) {
            $file = $_FILES['import_file'];
            $expire_date = $this->post('expire_date');
            if (empty($file) && empty($expire_date)) {
                // 都为空的话，不能提交
                $this->error('请上传需要导入的观影券或者需要修改的到期时间');
            }

            $arr = $file['tmp_name'] ? file($file['tmp_name']) : array();

            $num = false;
            if ($arr) {
                // 用于判断重复的
                $rearr = array();
                foreach ($arr as $r) {
                    $r = trim($r);
                    // 检查编码
                    $code = check_text($r);
                    if(strtolower($code) != 'utf-8') {
                        // 转换编码
                        $r = iconv($code, 'utf-8', $r);
                    }
                    if (empty($r) || in_array($r, $rearr)) {
                        continue;
                    }
                    $rearr[] = $r;
                }
                $arr = $rearr;
                // 能够导入的CODE的状态必须是2，导出过的CODE
                // $arr && $num = $this->db->where("code in ('" . implode("','", $arr) . "') and status <> 2")->from('FilmTicketCode')->count_all_results();
                $arr && $num = $this->db->where('status <> 2')->where_in('code', $arr)->from('FilmTicketCode')->count_all_results();
            }
            if($num === false) {
                $this->error('请检查要导入的编码文件，确认有编码');
            }
            if ($num > 0) {
                // 已经存在了。
                $this->error('观影券已经存在，请检查');
            }
            $b = true;
            // 取出之前加入的时间，如果没有那么必须设定时间
            $ticket = $this->db->get_where('FilmTicketCode', "productId = '{$id}'")->row_array();
            if (empty($expire_date)) {
                // 使用原来的时间
                if ($ticket) {
                    $expire_date = $ticket['expireDate'];
                } else {
                    $this->error('请选择观影券到期时间');
                }
            } else {
                $expire_date = $expire_date . ' 23:59:59';
                // 新时间更新原来的码的时间
                $b = $this->db->where("productId = '{$id}'")->update('FilmTicketCode', array('expireDate' => $expire_date));
            }

            $data = array();
            if ($arr) {
                $stock_num = 0;
                foreach ($arr as $code) {
                    $row = array();
                    $code = trim($code);
                    if (empty($code)) {
                        continue;
                    }
                    $row['code'] = $code;
                    $row['sold'] = 0;
                    $row['productId'] = $id;
                    $row['expireDate'] = $expire_date;
                    $row['status'] = 0;

                    $stock_num++;

                    $data[] = $row;
                }

                // 加入观影券
                $data && ($b &= $this->db->insert_batch('FilmTicketCode', $data));
            }

            // 取出电影票
            $cinema = $this->db->get_where('Product', "id = '$id'")->row_array();
            // 更新库存
            $stock_num && ($b &= $this->db->where("id = '{$id}'")->update('Product', array(
                    'quantity' => ($cinema['quantity'] + $stock_num),
                    'stock' => ($cinema['stock'] + $stock_num)
            )));

            $b ? $this->success('导入观影码成功', '', '', 'closeCurrent') : $this->error('导入观影码出错');
        }

        $this->display('cinema_ticket_add');
    }

    /**
     * 修改已上架电影票
     */
    function cinema_edit_selling() {
        $id = $this->get('id');
        // 选出团购信息
        $cinema = $this->db->get_where('Product', "id = '{$id}' and type = '0'")->row_array();
        
        if($cinema['status'] > 1) {
            $this->error('商品已下架或售罄，不能再编辑了，兄弟姐妹。');
        }
        
        if ($this->is_post()) {
            // 提交
            $data = array();
            $data['name'] = trim($this->post('name'));
            // 判断不能大于128
            if(strlen($data['name'])>128) {
                $this->error('标题不能超过128个字符哦，亲，一个中文3个字符哈，英文数字算1个。');
            }
            $data['description'] = trim($this->post('description'));
            $data['notice'] = trim($this->post('notice'));
            $data['image'] = $this->post('cinema_edit_selling_image');
            // 图片
            $data['introduce'] = $this->post('introduce');
            $data['marketPrice'] = $this->post('market_price');
            $data['price'] = $this->post('price');
            $data['limited'] = intval($this->post('limited'));
            $data['startDate'] = $this->post('start_date');
            $data['endDate'] = $this->post('end_date');
            $data['type'] = 0;
            // 电影票
            // $data['status'] = 0;
            // 上架
            $data['additionalFee'] = $this->post('additional_fee');
            $data['additionalFeeDesc'] = $data['additionalFee'] ? $this->post('additional_fee_desc') : '';
            // $data['stock'] = $this->post('stock');
            // 实际库存
            $data['quantity'] = $this->post('quantity');
            // 销售库存
            $data['rankOrder'] = intval($this->post('rank_order'));
            $data['deviceLimited'] = intval($this->post('device_limited'));
            $data['deviceLimitedDays'] = intval($this->post('device_limited_days'));

            if ($data['deviceLimited'] < 0 || $data['deviceLimitedDays'] < 0) {
                $this->error('兄弟姐妹，设备限制数量不能小于0哈');
            } elseif ($data['deviceLimited'] > 0 && $data['deviceLimitedDays'] <= 0) {
                $this->error('哦，允许设备购买数量设定了，你要设定限购的天数哦。');
            }

            // 修改
            if ($data['quantity'] > $cinema['stock']) {
                $this->error('销售库存不能大于实际库存，当前实际库存为['.$cinema['stock'].']');
            }
            generate_static_html($id, 'product') || $this->error('修改失败，无法生成详细信息');

            $b = $this->db->where("id = '{$id}'")->update('Product', $data);

            // 修改观影券到期时间
            $b &= $this->db->where("productId = '{$id}'")->update('FilmTicketCode', array('expireDate' => $this->post('expire_date')));

            // 写入地点
            $place = $this->post('place');
            $batch_data = array();
            if ($place) {
                foreach ($place as $p) {
                    $row = array();
                    $row['placeId'] = $p;
                    $row['productId'] = $id;

                    $batch_data[] = $row;
                }
            }
            // 先删除之前的地点
            $b &= $this->db->delete('ProductAtPlace', "productId = '{$id}'");
            if ($batch_data) {
                $b &= $this->db->insert_batch('ProductAtPlace', $batch_data);
            }

            // 2012.08.30加入在线选坐
            $online_seat = $this->post('online_seat');
            // 先删除
            $this->db->delete('ProductSpecialProperty', "productId = '{$id}' and propName='chooseSeatOnline'");
            if($online_seat) {
                // 可以选
                $b &= $this->db->insert('ProductSpecialProperty', array('productId' => $id, 'propName' => 'chooseSeatOnline', 'propValue' => 1));
            }
            
            $b ? $this->success('修改电影票信息成功', build_rel(array(
                    'goods',
                    'cinema_add'
            )), site_url(array(
                    'goods',
                    'cinema_edit_selling',
                    'id',
                    $id
            ))) : $this->error('修改电影票信息出错啦');
        }
        // 选出地点
        $place = $this->db->select('p.id, p.placename')->where("pp.productId = '{$id}' and p.id = pp.placeId")->get('ProductAtPlace pp, Place p')->result_array();
        $this->assign('place', $place);

        // 选出观影券的到期时间
        $ticket = $this->db->limit(1)->select('expireDate')->where("productId = '{$id}'")->get('FilmTicketCode')->row_array();
        $cinema['expireDate'] = $ticket['expireDate'];
        
        // 2012.08.30获取扩展的信息
        $properties = $this->db->get_where('ProductSpecialProperty', array('productId' => $id))->result_array();
        foreach($properties as $row) {
            $cinema['properties'][$row['propName']] = $row['propValue'];
        }

        $this->assign('cinema', $cinema);

        $this->display('cinema_edit_selling');
    }

    /**
     * 浏览已下架的
     */
    function cinema_view() {
        $id = $this->get('id');
        // 选出团购信息
        $cinema = $this->db->get_where('Product', "id = '{$id}' and type = '0'")->row_array();

        // 选出地点
        $place = $this->db->select('p.id, p.placename')->where("pp.productId = '{$id}' and p.id = pp.placeId")->get('ProductAtPlace pp, Place p')->result_array();
        $this->assign('place', $place);

        // 选出观影券的到期时间
        $ticket = $this->db->limit(1)->select('expireDate')->where("productId = '{$id}'")->get('FilmTicketCode')->row_array();
        $cinema['expireDate'] = $ticket['expireDate'];
        $cinema['image'] = image_url($cinema['image'], 'common');

        $this->assign('cinema', $cinema);

        $this->display('cinema_view');
    }

    /**
     * 导出电影票
     */
    function cinema_export_ticket() {
        $id = $this->get('id');
        empty($id) && ($this->input->is_ajax_request() ? $this->error('错误') : die('错误'));
        // 已经下架商品 未卖出
        $cinema = $this->db->get_where('Product', "id = '{$id}' and type = 0 and status > 1")->row_array();
        empty($cinema) && ($this->input->is_ajax_request() ? $this->error('电影票还未下架') : die('电影票还未下架'));

        // 获取没有卖出的 不管有没有过期的
        $tickets = $this->db->get_where('FilmTicketCode', "productId = '{$id}' and sold = 0")->result_array();

        empty($tickets) && ($this->input->is_ajax_request() ? $this->error('电影票没有可以导出的观影码') : die('电影票没有可以导出的观影码'));

        if ($this->input->is_ajax_request()) {
            $this->success('成功导出', '', '', '', array('id' => $id));
        } else {
            Header('Content-type: application/octet-stream');
            Header('Content-Disposition: attachment; filename=tickets_export_' . mdate('%Y-%m-%d') . '.txt');

            // 把所有导出过的CODE状态变成2，便于导入的时候判断
            @$this->db->where("productId = '{$id}' and sold = 0")->update('FilmTicketCode', array('status' => 2));

            foreach ($tickets as $row) {
                echo $row['code'], "\r\n";
            }

            exit();
        }
    }

    /**
     * 根据商品ID更新在PLACE表里面的电影票数量和团购数量
     * @param $id 商品ID或者团购ID
     * @param $t 0 电影票 1 团购
     * @param $op 0 上架 1 下架
     */
    function _update_place_goods($id, $t = 0, $op = 0) {
        if ($op) {
            // 下架的时候直接返回，不做处理
            return true;
        }

        $field_count = $t ? 'grouponCount' : 'filmTicketCount';
        $field_has = $t ? 'hasGroupon' : 'hasFilmTicket';
        $op_char = $op ? '-' : '+';
        $table = $t ? 'GrouponItemAtPlace' : 'ProductAtPlace';
        $field_id = $t ? 'grouponId' : 'productId';
        // 先更新count字段
        $b = $this->db->query("UPDATE Place SET {$field_count} = {$field_count}{$op_char}1 WHERE 
                          id IN (SELECT placeId FROM {$table} WHERE $field_id = '{$id}')");
        // 在更新has字段 如果count字段>0那么则为1
        $b &= $this->db->query("UPDATE Place SET {$field_has} = ({$field_count}>0) WHERE 
                          id IN (SELECT placeId FROM {$table} WHERE $field_id = '{$id}')");

        return $b;
    }
    
    /**
     * 导出订单信息
     * @param $id 商品ID号
     * @param $type 12：团购 13：电影票
     * @param $pay  1：已付款 0：未付款 -1：所有
     * @param $payway 0：支付宝 1：银联 -1：所有
     */
    function export_order() {
        $id = intval($this->get('id'));
        $type = intval($this->get('type'));
        $pay = $this->get('pay');
        // 默认已付款的查询
        $pay = intval($pay||$pay==='0'?$pay:1);
        $payway = $this->get('payway');
        $payway = intval($payway||$payway==='0'?$payway:-1);
        
        // 选出商品信息
        $goods = $this->db->get_where(12===$type?'GrouponItem':'Product', array('id' => $id))->row_array();
        
        empty($goods) && die('ID号错误');
        
        $where_sql = array('itemId'=>$id, 'itemType'=>$type);
        $pay >= 0 && $where_sql['isPayed'] = $pay;
        $payway >= 0 && $where_sql['payWay'] = $payway;
        
        // 选出订单信息
        $list = $this->db->order_by('payDate', 'desc')->get_where('OrderInfo', $where_sql)->result_array();
        
        $typename = array('12' => '团购', '13' => '电影票');
        $payname = array('-1' => '所有', '0' => '未付款', '1' => '已付款');
        $paywayname = array('-1' => '所有', '0' => '支付宝', '1' => '银联');
        
        $filename = $typename[$type] . '[' . $goods['name'] . '] - 订单数据(是否付款:' . $payname[$pay] . ' - 支付方式:' . $paywayname[$payway] . ')';
        
        header('Content-type: application/vnd.ms-excel; charset=gbk');
        header('Content-Disposition: attachment; filename="'.$filename.'.xls"');
                
        $str = "订单号\t".(12===$type?"来源方订单号\t":'')."下单时间\t付款时间\t付款用户\t商品ID号\t".(12===$type?"来源方商品ID\t":'')."购买数量\t支付方式\t实付金额\n";
        
        $alipay_amount = $chinapay_amount = 0;
        foreach($list as $row) {
            $str .= "{$row['id']}\t".(12===$type?"{$row['partnerOrderCode']}\t":'');
            $str .= "{$row['createDate']}\t{$row['payDate']}\t{$row['uid']}\t";
            $str .= "{$goods['id']}\t".(12===$type?"{$goods['originalId']}\t":'');
            $str .= "{$row['quantity']}\t{$paywayname[$row['payWay']]}\t{$row['money']}\n";
            $row['payWay'] === '0' && $alipay_amount += $row['money'];
            $row['payWay'] === '1' && $chinapay_amount += $row['money'];
        }
        
        $str .= "合计：\t支付宝：{$alipay_amount}\t银联：{$chinapay_amount}\t总计：" . ($alipay_amount + $chinapay_amount);
        
        die(iconv('utf-8', 'gbk', $str));
    }
}
