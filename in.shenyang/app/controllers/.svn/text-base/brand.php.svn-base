<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');   
/*
 * 品牌管理
 * 
 * Author: piglet chenglin.zhu@gmail.com
 * Created on 2012-9-19
 */

class Brand extends MY_Controller {
    /**
     * 品牌管理
     */
    function index() {
        $where_sql = array();
        $keywords = trim($this->post('keywords'));
        if($keywords !== '') {
            $type = $this->post('type');
            $keytxt = $keywords;
			$keywords = dstripslashes($keywords);
            $this->assign('keywords', $keywords);
            $this->assign('type', $type);
            
            if('name' == $type) {
                $where_sql = "b.name like '%{$keytxt}%'";
            } else {
                $where_sql = "b.id = '{$keytxt}'";
            }
        }
        
        $total_num = $this->db->where($where_sql)->from($this->_tables['brand'].' b')->count_all_results();
        $paginate = $this->paginate($total_num);
        
        $list = $this->db->select('b.*, ifnull(u.nickname, u.username) as uname', false)
        				 ->order_by('id', 'desc')
                         ->limit($paginate['per_page_num'], $paginate['offset'])
                         ->from($this->_tables['brand'].' b')
                         ->join($this->_tables['user'].' u', 'b.lordId = u.id', 'left')
                         ->get()->result_array();
                         /*$this->db->select('b.*, ifnull(u.nickname, u.username) as uname', false)
                         ->order_by('b.id', 'desc')
                         ->limit($paginate['per_page_num'], $paginate['offset'])
                         ->where(($where_sql?$where_sql:array()))
                         ->from('Brand b')
                         ->join('User u', 'b.lordId = u.id', 'left')
                         ->get()->result_array();*/
                         
        $this->assign('list', $list);
        
        $this->display('index');
    }
    
    /**
     * 关联地点
     */
    function related_place() {
        $id = intval($this->get('id'));

        $place = $this->db->select('id, placename')->from($this->_tables['place'])
                          ->where("status = 0 and brandId = '{$id}'")->get()->result_array();

        $this->assign('place', $place);

        $this->display('related_place', 'goods');
    }

    /**
     * 添加
     */
    function add() {
        // 品牌ID
        $id = intval($this->get('id'));
        
        if($id > 0) {
            // 品牌
            $brand = $this->db->get_where($this->_tables['brand'], array('id' => $id))->row_array();
            // 获取基础卡
            //$card = $this->db->get_where('BrandMemberCard', array('brandId' => $id, 'isBasic' => 1))->row_array();
            // 获取地点
            $place = $this->db->get_where($this->_tables['place'], array('brandId' => $id, 'status' => 0))->result_array();
            
            $category_id = $brand['placeCategoryId'];
            // 去获取用户的分类信息数据
            $category = $this->db->get_where($this->_tables['placecategory'], array('id' => $category_id))->row_array();
            
            $show_category = $this->db->from($this->_tables['placecategoryship'])->where(array('child' => $category_id))->count_all_results();
        }
        
        if($this->is_post()) {
            $name = trim($this->post('name'));
            if(empty($name)) {
                $this->error('请输入品牌名称');
            }
            
            // $card_title = trim($this->post('card_title'));
            // if(empty($card_title)) {
                // $this->error('请输入会员卡名称');
            // }
            
            //砍掉
            $lord_id = intval($this->post('content_id'));
//             if($lord_id <= 0) {
//                 $this->error('请选择店长');
//             }
            
            // 检查店长是否已经存在
//             $b = $this->db->get_where($this->_tables['brand'], array('lordId' => $lord_id))->row_array();
//             if($b && $b['id'] != $brand['id']) {
//                 $this->error('店长已经存在请重新选择一个');
//             }
            
            /*$summary = $this->post('card_summary');
            if(dstrlen($summary) > 60) {
                $this->error('特权摘要不能多于30个汉字，60个英文');
            }*/
            
            $place_ids = $this->post('place');
            $is_edit = !empty($brand);
            // 地点清空之前的
            if($is_edit) {
                // 查询所有的地点
                $places = $this->db->select('id')->get_where($this->_tables['place'], array('brandId' => $id))->result_array();
                $pids = array();
                foreach($places as $r) {
                    $pids[] = $r['id'];
                }
            
                // 编辑，那么清楚之前的所有的关系
                $this->db->update('Place', array('isVerify' => 0, 'brandId' => null, 'icon' => '', 'hasPrefer' => 0), array('brandId' => $id));
                
                // 更新缓存
               
                $pids && api_update_cache('Place', $pids);
                
            }
            
            // 判断地点是否已经被使用
            $belong_brand = $this->db->from($this->_tables['place'])->where_in('id', $place_ids)
                                     ->where('ifnull(brandId, 0) > 0', null, false)->count_all_results();
            if($belong_brand) {
                $this->error('您选择的地点已经被其他品牌占用，请重新选择地点哦，亲');
            }
            
            $status = intval($this->post('status'));
            
            $logo = $this->post('logo');
            $logo = $logo?array_filter($logo):array();
            $data = array(
                'name' => $name,
                //'description' => $this->post('description'),
                //'websiteUri' => $this->post('url'),
                //'tel' => $this->post('tel'),
                //'email' => $this->post('email'),
                //'content' => $this->post('content'),
                'logo' => $logo[0],
                'lordId' => $lord_id,
                'status' => $status
            );
            
            $cate_data = array(
                'content' => $name,
                'icon' => $logo[0],
                'level' => 1,
                'isBrand' => 1
            );
            $is_category = $this->post('is_category');
            if($is_category) {
                $category_icon = $this->post('category_icon');
                $category_icon = $category_icon?array_filter($category_icon):array();
                $cate_data['categoryIcon'] = $category_icon[0];
            }
            if($category_id) {
                // 存在分类。那么修改分类的信息
                $b = $this->db->update($this->_tables['placecategory'], $cate_data, array('id' => $category_id));
            } else {
                // 新建分类
                $b = $this->db->insert($this->_tables['placecategory'], $cate_data);
                $category_id = $this->db->insert_id();
            }
            $data['placeCategoryId'] = $category_id;
            if($is_category) {
                // 需要现在分类中
                $b &= $this->db->replace($this->_tables['placecategoryship'], array('parent' => '45', 'child' => $category_id));
            } else {
                // 删除之前的关系
                $b &= $this->db->delete($this->_tables['placecategoryship'], array('child' => $category_id));
            }
            
            $tip = '添加';
            if($is_edit) {
                $b &= $this->db->update($this->_tables['brand'], $data, array('id' => $id));
                $tip = '修改';
            } else {
                $b &= $this->db->insert($this->_tables['brand'], $data);
                $id = $this->db->insert_id();
            }
            
            if($id <= 0) {
                $this->error($tip . '品牌出错啦，亲');
            }
            
            // 把所有会员卡作废
            //$b &= $this->db->update('BrandMemberCard', array('status' => $status), array('brandId' => $id));
            
            //$card_image = $this->post('card_image');
            // 基础会员卡
            /*$data = array(
                          // 'title' => $card_title, 
                          'title' => $name,
                          'brandId' => $id,
                          'content' => $this->post('card_detail'),
                          'image' => $card_image[0],
                          'status' => $status,
                          'summary' => $summary                         
                         );
            if($card) {
                // 更新
                $b &= $this->db->update('BrandMemberCard', $data, array('id' => $card['id']));
            } else {
                $b &= $this->db->insert('BrandMemberCard', $data);
                $card_id = $this->db->insert_id();
            }
            */
            
            if($place_ids) {
            	
                $b &= $this->db->where_in('id', $place_ids)
                           ->where('ifnull(brandId, 0) = 0')
                           ->update($this->_tables['place'], array( 'brandId' => $id, 'icon' => $logo[0] ));
                // 更新缓存
                api_update_cache('Place', $place_ids);
               
            }
            
            
            // 更新地点数量
            $b &= $this->db->update($this->_tables['brand'], array('placeCount' => count($place_ids) ), array('id' => $id));
            
            // 更新用户验证状态
            //$b &= $this->db->update('User', array('isVerify' => $status?0:1), array('id' => $lord_id));
            
            // 更新品牌关联的模型信息
            // 先删除原来的
            /*if($is_edit) {
                $this->db->delete('BrandOwnModule', array('brandId' => $id));
            }*/
            // 加入选中的
            /*$module_id = $this->post('module_id');
            if($module_id) {
                $batch_data = array();
                foreach($module_id as $mid) {
                    $batch_data[] = array('brandId' => $id, 'moduleId' => $mid);
                }
                $b &= $this->db->insert_batch('BrandOwnModule', $batch_data);
            }
            
            // 更新用户的缓存
            $lord_id && api_update_cache('User', $lord_id);
            if($lord_id != $brand['lordId'] && $brand['lordId']) {
                // 清楚掉之前地主的认证标识
                $this->db->update('User', array('isVerify' => 0), array('id' => $brand['lordId']));
                api_update_cache('User', $brand['lordId']);
            }*/
            
            $b?$this->success($tip . '品牌成功', $this->_index_rel, $this->_index_uri, 'closeCurrent'):$this->error($tip . '品牌失败');
        }

        if($id > 0) {
            // 选出已给品牌的模型
            $module = $this->db->select('pm.*')
                               ->get_where('BrandOwnModule bom, PlaceModule pm', "bom.moduleId = pm.id AND bom.brandId = '{$id}'")
                               ->result_array();
            
            // 查询出一个品牌的地点，看他的hasPrefer字段值
            $p = $this->db->get_where($this->_tables['place'], array('brandId' => $id))->row_array();
            $brand['hasPrefer'] = $p['hasPrefer']?$p['hasPrefer']:0;
            
            $this->assign(compact('brand', 'card', 'place', 'module', 'category', 'show_category'));
        }
        
        $this->display('add');
    }
    
    /**
     * 删除
     */
    function edit() {
        $this->add();
    }
    
    /**
     * 欢迎消息管理
     */
    function welcome() {
        $type = intval($this->post('type'));
        $where_sql = 'a.brandId = b.id';
        if($type >= 0) {
            $where_sql .= " AND a.status = '{$type}'";
        }
        $keywords = trim($this->post('keywords'));
        if($keywords !== '') {
            $keywords_txt = daddslashes($keywords);
            $this->assign('keywords', $keywords);
            $where_sql .= " AND a.welcomeMsg like '%{$keywords_txt}%'";
        }
        
        $total_num = $this->db->where($where_sql)->from($this->_tables['applybrand'].' a, '.$this->_tables['brand'].' b')->count_all_results();
        $paginate = $this->paginate($total_num);
        
        $brands = $this->db->select('a.*, if(unix_timestamp(a.checkDate)=0, a.createDate, a.checkDate) as lastDate, b.memberCount', false)
                         ->order_by('lastDate', 'desc')
                         ->limit($paginate['per_page_num'], $paginate['offset'])
                         ->where($where_sql)
                         ->from('ApplyBrand a, Brand b')
                         ->get()->result_array();
                         
        $link_types = array_values($this->config->item('item_type'));
        $link_type = array();
        foreach($link_types as $row) {
            $link_type[$row['key']] = $row['value'];
        }
        
        // 用于保存需要去获取优惠的标题的连接
        $prefer = $list = array();
        foreach($brands as $row) {
            $scheme = parse_url($row['welcomeLink']);
            $protocol = $scheme['scheme'];
            $link = $scheme['host'];
            $row['link'] = $link_type[$protocol];
            if('inprefer' == $protocol) {
                $prefer[$link] = $row['id'];
            } else {
                $row['link'] .= ' - ' . $row['welcomeLink'];
            }
            $list[$row['id']] = $row;
        }
        if($prefer) {
            $list_prefer = $this->db->where_in('id', array_keys($prefer))->get($this->_tables['preference'])->result_array();
            foreach($list_prefer as $row) {
                $r = &$list[$prefer[$row['id']]];
                $r['link'] .= ' - ' . $row['title'];
            }
            unset($r);
        }
                         
        $this->assign('type', $type);                 
        $this->assign('list', $list);
        
        $this->display('welcome');
    }

    /**
     * 消息操作 通过、驳回
     */
    function handle_welcome() {
        $id = intval($this->get('id'));
        $do = $this->get('do');
        
        
        // 获取信息
        $brand = $this->db->get_where($this->_tables['applybrand'], array('id' => $id))->row_array();
        if(empty($brand)) {
            // 错误的
            $this->error('错误信息提交');
        }
        // 检查信息的状态
        if($brand['status'] > 0) {
            // 状态不是0，不能改变其他状态为通过或者驳回
            $this->error('该条已经处理过了，不能重复处理');
        }
        $this->lang->load('brand');
        $remark = $this->post('remark');
        if ('pass' == $do) {
            // 通过
            // 先更新正式表数据
            $b = $this->db->update($this->_tables['brand'], 
                                    array('welcomeMsg' => $brand['welcomeMsg'], 
                                    'welcomeLink' => $brand['welcomeLink']), 
                                    array('id' => $brand['brandId']));
            $b &= $this->db->update($this->_tables['applybrand'], array('status' => 20, 'remark' => $remark, 'checkDate' => now()), array('id' => $id));
            
            $arr = array('brand', 'welcome');
            $lan = $this->lang->line('message');
            $b && $this->db->insert($this->_tables['brandmessage'], array('brandId' => $brand['brandId'], 'title' => $lan['pass']['title'], 'content' => ''));
            $b?$this->success('提交通过成功', build_rel($arr), site_url($arr)):$this->error('提交通过失败');
        } else {
            // 
            if($this->is_post()) {
                // 提交
                $b = $this->db->update($this->_tables['applybrand'], array('status' => 10, 'remark' => $remark, 'checkDate' => now()), array('id' => $id));
                
                $arr = array('brand', 'welcome');
                $lan = $this->lang->line('message');
                $b && $this->db->insert($this->_tables['brandmessage'], array('brandId' => $brand['brandId'], 'title' => $lan['reject']['title'], 'content' => ''));
                $b?$this->success('提交驳回成功', build_rel($arr), site_url($arr), 'closeCurrent'):$this->error('提交驳回失败');
            }
            $this->assign('brand', $brand);
            $this->display('add_remark');
        }
    }
}
