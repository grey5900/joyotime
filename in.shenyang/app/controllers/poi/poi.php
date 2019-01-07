<?php
/**
 * POI数据管理
 * Create by 2012-3-19
 * @author liuw
 * @copyright Copyright(c) 2012-2014 joyotime
 */

// Define and include
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

// Code
class Poi extends MY_Controller {
    var $poi_status;
    function __construct() {
        parent::__construct();
        $this->load->helper("home");
        $this->assign('poi_status', $this->config->item('poi_status'));
        $this->poi_status = $this->config->item('poi_status');
    }

    /**
     * poi列表
     * Create by 2012-3-19
     * @author liuw
     */
    public function index($cid = 0,$cname = '') {
        // 获取关键词
        $keywords = trim($this->post('keywords'));
        $where_sql = array();
        $where_sql[] = 'p.isChecked = 1';
        
        $pids = $this->get("pids") ? $this->get("pids") : $this->post("pids") ;
        if($pids)
        {
        	 $where_sql[] = "p.id in (".str_replace("-",",",$pids).")";
        }
        
        // 关键词条件
        if ($keywords) {
            $type = $this->post('type');
            switch($type) {
                case 'id' :
                    $where_sql[] = "p.id = '".intval($keywords)."'";
                    break;
                case 'placename' :
                    $where_sql[] = "p.placename like '%$keywords%'";
                    break;
                case 'address' :
                    $where_sql[] = "p.address like '%$keywords%'";
                    break;
            }
        }

        // 分类条件
        $cid = $cid ? $cid : $this->post('placeCategory_id');
        $cname = $cname ? $cname : $this->post('placeCategory_name');
        if ($cid) {
            // $where_sql[] = "find_in_set('{$cid}', cats)";
            $where_sql[] = "poc.placeCategoryId='{$cid}'";

            $this->assign('cid', $cid);
            $this->assign('cname', $cname);
			// 关联推荐表，查询推荐状态
            $from_table = "Place p left join LocalLandMark llm on p.id=llm.placeId 
                           left join HomePageData r on r.itemId = p.id and r.itemType = 1
                           left join PlaceOwnCategory poc on p.id=poc.placeId";
            // left join PlaceExtraRating per on per.placeId=p.id
        } else {
            $from_table = "Place p left join LocalLandMark llm on p.id=llm.placeId 
            			   left join HomePageData r on r.itemId = p.id and r.itemType = 1
                           ";
            //left join PlaceExtraRating per on per.placeId=p.id
        }

        // 是否有电话
        $hasphone = $this->post('hasphone');
        if ($hasphone) {
            $where_sql[] = "p.tel <> ''";

            $this->assign('hasphone', $hasphone);
        }

        // 地标
        $mark = $this->post('mark');
        if ($mark) {
            $where_sql[] = "llm.id > 0";

            $this->assign('mark', $mark);
        }

        // 状态
        $status = intval($this->post('status')) ? intval($this->post('status')) : 1;
        if ($status > 0) {
        	if($status == 4){
        		$where_sql[] = " r.itemId is not null ";
        	}
        	else{
            $where_sql[] = "p.status = '" . ($status - 1) . "'";
        	}
            $this->assign('status', $status);
        }

        // 等级
        $level1 = $this->post('level1');
        $level2 = $this->post('level2');
        if ($level1 || $level2) {
            // 有一个选择了都可以
            $level1 = $level1 ? $level1 : 0;
            $level2 = $level2 ? $level2 : 5;
            $where_sql[] = "p.level between '{$level1}' and '{$level2}'";

            $this->assign('level1', $level1);
            $this->assign('level2', $level2);
        }

        // 人均
        $pcc1 = floatval($this->post('pcc1'));
        if ($pcc1) {
            $where_sql[] = "p.pcc >= '{$pcc1}'";
            $this->assign('pcc1', $pcc1);
        }
        $pcc2 = floatval($this->post('pcc2'));
        if ($pcc2) {
            $where_sql[] = "p.pcc <= '{$pcc2}'";
            $this->assign('pcc2', $pcc2);
        }

        // 点评
        $tip1 = intval($this->post('tip1'));
        if ($tip1) {
            $where_sql[] = "p.tipCount >= '{$tip1}'";
            $this->assign('tip1', $tip1);
        }
        $tip2 = intval($this->post('tip2'));
        if ($tip2) {
            $where_sql[] = "p.tipCount <= '{$tip2}'";
            $this->assign('tip2', $tip2);
        }

        // 签到
        $checkin1 = intval($this->post('checkin1'));
        if ($checkin1) {
            $where_sql[] = "p.checkinCount >= '{$checkin1}'";
            $this->assign('checkin1', $checkin1);
        }
        $checkin2 = intval($this->post('checkin2'));
        if ($checkin2) {
            $where_sql[] = "p.checkinCount <= '{$checkin2}'";
            $this->assign('checkin2', $checkin2);
        }

        // 图片
        $photo1 = intval($this->post('photo1'));
        if ($photo1) {
            $where_sql[] = "p.photoCount >= '{$photo1}'";
            $this->assign('photo1', $photo1);
        }
        $photo2 = intval($this->post('photo2'));
        if ($photo2) {
            $where_sql[] = "p.photoCount <= '{$photo2}'";
            $this->assign('photo2', $photo2);
        }

        // 权重
        $rating1 = intval($this->post('rating1'));
        if ($rating1) {
            $where_sql[] = "p.rating >= '{$rating1}'";
            $this->assign('rating1', $rating1);
        }
        $rating2 = intval($this->post('rating2'));
        if ($rating2) {
            $where_sql[] = "p.rating <= '{$rating2}'";
            $this->assign('rating2', $rating2);
        }

        // 距离
        $distance = $this->get('distance');
        // ID
        $id = $this->get('id');
        if ($id && $distance) {
            // 如果有ID和距离，代表需要查询周围
            // 先查询出选中地点的信息
            $row = $this->db->select('*')->get_where('Place', array('id' => $id))->row_array();
            $latitude = floatval($row['latitude']);
            $longitude = floatval($row['longitude']);
            if (empty($latitude) || empty($longitude)) {
                // 没查询到纬度、经度
                $this->error('请选择正确的地点');
            }
            $distance = floatval($distance) / 1000;
            // $where_sql[] = "distance < {$distance}";
            $where_sql[] = "f_distance({$latitude}, {$longitude}, p.latitude, p.longitude) < {$distance}";
            // if($cid) {
            // 有分类条件和距离条件
            // $from_table = "(select p.*, group_concat(poc.placeCategoryId) as cats,
            // f_distance({$latitude}, {$longitude}, p.latitude, p.longitude) as distance
            // from Place p left join PlaceOwnCategory poc
            // on p.id=poc.placeId group by p.id) b";
            // $from_table = "Place p left join PlaceOwnCategory poc on p.id=poc.placeId";
            // } else {
            // 只有距离条件
            // $from_table = "(select p.*,
            // f_distance({$latitude}, {$longitude}, p.latitude, p.longitude) as distance
            // from Place p) b";
            // $from_table = "Place p";
            // }

            $this->assign('dis_place', $row);
        }
        // else {
        // if($cid) {
        // 有分类条件
        // $from_table = '(select p.*, group_concat(poc.placeCategoryId) as cats
        // from Place p left join PlaceOwnCategory poc
        // on p.id=poc.placeId group by p.id) b';
        // $from_table = "Place p left join PlaceOwnCategory poc on p.id=poc.placeId";
        // } else {
        // 没有分类条件和距离条件
        // $from_table = 'Place p';
        // }

        // }

        // $query = $this->db->from($from_table)->select('count(*) as num')
        // ->where($where_sql?implode(' and ', $where_sql):array())
        // ->get();
        $query = $this->db->query('select count(*) as num  from ' . $from_table . ($where_sql ? (' where ' . implode(' and ', $where_sql)) : ''));
        $row = $query->row_array();
        $total_num = $row['num'];

        // 返回获得每页显示，当前页等等参数
        $paginate = $this->paginate($total_num);

        // 排序字段
        $order_field = $this->post('orderField');
        // 排序方式
        $order_direction = $this->post('orderDirection');
        // if($order_field && $order_direction) {
        // $this->db->order_by($order_field, $order_direction);
        // } else {
        // $this->db->order_by('id', 'desc');
        // }
        if ($order_field && $order_direction) {
            $order_by = ' order by p.' . $order_field . ' ' . $order_direction;
        } else {
            $order_by = ' order by p.id desc ';
        }
        // $query = $this->db->from($from_table)
        // ->where($where_sql?implode(' and ', $where_sql):array())
        // ->limit($paginate['per_page_num'], $paginate['offset'])->get();
        $query = $this->db->query('select p.*, ifnull(llm.id, 0) as landmark , ifnull(r.expireDate,0) as isrecommend 
                                   from ' . $from_table . ($where_sql ? (' where ' . implode(' and ', $where_sql)) : ' ') . $order_by . ' limit ' . $paginate['per_page_num'] . ' offset ' . $paginate['offset']);
        $list = $query->result_array(); //, ifnull(per.rating, 0) as extraRating, ifnull(per.status, 0) as extra_status 
		$website = $this->config->item('web_site');
		$this->assign("website",$website);
        $current_date = date("Y-m-d H:i:s");
        $this->assign(compact('list', 'keywords', 'type', 'order_field', 'order_direction', 'pids','current_date','cid'));

        $this->display('poi_index', 'poi');
    }

    /**
     * 显示高级搜索
     * 
     */
    public function advsearch() {
        $this->display('poi_adv_search', 'poi');
    }
    
    /**
     * 创建修改POI
     */
    function add_poi() {
        // 用来表示该页面的唯一标识
        $page_id = 'poi_add';
        // placeId
        $id = $this->get('id');
        $cid = $this->get('cid');
        $fresh_type = $this->get('fresh_type');
        if ($id) {
            // 取出地点信息
            $query = $this->db->get_where('Place', array('id' => $id));
            $place = $query->row_array();
            $this->assign('poi', $place);
            
            // 取出地点的碎片数据
            $list = $this->db->select('*, ifnull(moduleId, 0) as moduleId', false)
                              ->order_by('rankOrder', 'desc')->from('PlaceOwnSpecialProperty')
                              ->where(array('placeId' => $id))->get()->result_array();
            $this->assign('properties', $list);
            
            // 选出地点分类
            $query = $this->db->select('id, content, isBrand')->from('PlaceOwnCategory poc, PlaceCategory pc')->where("poc.placeId = '{$id}' and poc.placeCategoryId = pc.id")->get();
            $category = $query->result_array();
            $cate = array();
            $split_str = '';
            foreach ($category as $row) {
                $isBrand = $row['isBrand'];
                unset($row['isBrand']);
                if ($isBrand) {
                    // 品牌分类
                    $cate['brand'] = $row;
                } else {
                    // 普通分类
                    $cate['common'] = array(
                    		'id' => $cate['common']['id'] . $split_str . $row['id'],
                    		'content' => $cate['common']['content'] . $split_str . $row['content'] 
                    		);
                    $split_str = ',';
                }
            }
            $this->assign('category', $cate);
        }
        else{
        	
	        if($cid == 100){
		        $category['common']['content'] = "楼盘";
		     	$category['common']['id'] = "100";
		     	$this->assign("category",$category);
	        }
        }

        if ($this->is_post() && $fresh_type!=="get") {
            $placename = trim($this->post('placename'));
            $poi_icon = $this->post('poi_icon');
            $poi_icon = $poi_icon?array_filter($poi_icon):array();
            
            $data = array(
                    'placename' => $placename,
                    'address' => $this->post('address'),
                    'latitude' => floatval($this->post('latitude')),
                    'longitude' => floatval($this->post('longitude')),
                    'tel' => $this->post('tel'),
                    'pcc' => $this->post('is_business') ? intval($this->post('pcc')) : 0,
                    'isBusiness' => $this->post('is_business'),
                    'icon' => $poi_icon[0],
                    'isChecked' => 1,
            		'isRepayPoint' => $this->post("isRepayPoint")
            );
            
            if($this->post("background")){
            	$data['background'] = $this->post("background");
            }
            
            $b = true;
            // 修改
            $b_tip = false;
            if (empty($id)) {
                // 新建
                $b &= $this->db->insert('Place', $data);
                $id = $this->db->insert_id();
                $b_tip = true;
            } else {
                // 编辑
                $b &= $this->db->where('id', $id)->update('Place', $data);
                // 删除之前的分类关系
                $b &= $this->db->delete('PlaceOwnCategory', array('placeId' => $id));
            }

            // 添加关系
            $cats = array();
//             $cats[0]['placeId'] = $id;
//             $cats[0]['placeCategoryId'] = $this->post('placeCategory_id');
//             $brand_cat_id = $this->post('placeBrand_id');
//             if ($brand_cat_id) {
//                 $cats[1]['placeId'] = $id;
//                 $cats[1]['placeCategoryId'] = $brand_cat_id;
//             }
            $pcids = explode(',', $this->post('placeCategory_id'));
            foreach($pcids as $pcid) {
            	$cats[] = array(
            			'placeId' => $id,
            			'placeCategoryId' => $pcid
            			);
            }
            $brand_cat_id = $this->post('placeBrand_id');
            if ($brand_cat_id) {
            	$cats[] = array(
            			'placeId' => $id,
            			'placeCategoryId' => $brand_cat_id
            	);
            }
            $b &= $this->db->insert_batch('PlaceOwnCategory', $cats);
            
            // 处理碎片模型的数据
            $this->load->helper('poi');
            $b &= handle_block_data($id, $page_id, $b_tip);
            
            if ($b) {
                // 更新缓存
                // 更新地点缓存
                $api_rtn1 = api_update_cache('Place', $id);
                // 更新分类关系
                $api_rtn2 = api_update_cache('PlaceCategoryShip');
                // 更新网站的缓存
                update_cache('web', 'data', 'place', $id);
                get_data("loupan",true);
                
                send_api_interface('/private_api/place/update_solr', 'POST', array('place_id' => $id));
                
                $this->load->helper('search');
                // 更新地点
                @update_index(20, $id);
                // 看是否为楼盘
                if(100 == $cats[0]['placeCategoryId']) {
                    @update_index(10, $id);
	                $this->load->helper('house');
	                // 去更新团房数据
	                $tf_id = sync_house_info($id);
                }
                
                // 通过最后一次判断是否成功
                $this->success(($b_tip ? '新建' : '修改') . 'POI成功', '', '', 'closeCurrent', array('p1'=>$api_rtn1, 'p2'=>$api_rtn2, 'tf_id' => $tf_id));
            } else {
                $this->success(($b_tip ? '新建' : '修改') . 'POI失败,请重试');
            }
        }
      
        $this->assign('page_id', $page_id);

        $this->display('poi_add_new', 'poi');
    }
    
    /**
     * 创建poi
     * 
     * @Deprecated 2012.10.12
     * 
     */
    public function add() {
        $this->add_poi(); // 直接转向到新的方法中
        // placeId
        $id = $this->get('id');
        if ($id) {
            // 取出地点信息
            $query = $this->db->get_where('Place', array('id' => $id));
            $place = $query->row_array();
            
            // 取出数据模型数据
            $list = $this->db->select('pm.name, pm.id, pom.rankOrder')
                              ->order_by('pom.rankOrder', 'asc')->from('PlaceOwnModule pom')
                              ->join('PlaceModule pm', 'pom.placeModuleId = pm.id')
                              ->where(array('pom.placeId' => $id))->get()->result_array();
            $module_id = array();
            foreach($list as $row) {
                $place['module'][$row['rankOrder']] = $row;
                $module_id['id_' . $row['id']] = 1;
            }
            $this->assign('poi', $place);
            $this->assign('module_id_json', json_encode($module_id));
            
            // 选出地点分类
            $query = $this->db->select('id, content, isBrand')->from('PlaceOwnCategory poc, PlaceCategory pc')->where("poc.placeId = '{$id}' and poc.placeCategoryId = pc.id")->get();
            $category = $query->result_array();
            $cate = array();
            foreach ($category as $row) {
                $isBrand = $row['isBrand'];
                unset($row['isBrand']);
                if ($isBrand) {
                    // 品牌分类
                    $cate['brand'] = $row;
                } else {
                    // 普通分类
                    $cate['common'] = $row;
                }
            }
            $this->assign('category', $cate);
        }

        // 获取模型数据
        $list = $this->db->order_by('rankOrder', 'asc')->get('PlaceModule')->result_array();
        $modules = array();
        foreach($list as $row) {
            $modules[$row['id']] = $row;
        }
        
        if ($this->is_post()) {
            $placename = trim($this->post('placename'));
            // $place_module = $this->post('place_module');
            $poi_icon = $this->post('poi_icon');
            $poi_icon = $poi_icon?array_filter($poi_icon):array();
            $data = array(
                    'placename' => $placename,
                    'address' => $this->post('address'),
                    'latitude' => floatval($this->post('latitude')),
                    'longitude' => floatval($this->post('longitude')),
                    'tel' => $this->post('tel'),
                    //'pcc' => $this->post('is_business') ? intval($this->post('pcc')) : 0, 取消了，最好还是不插入好了
                    'isBusiness' => $this->post('is_business'),
                    'icon' => $poi_icon[0],
                    'isChecked' => 1,
                    // 'placeModule' => $place_module
                    'isRepayPoint' => $this->post('isRepayPoint') ? intval($this->post('isRepayPoint') ) : 0 
            );

            // // 操作place module的东东
            // if ($place['placeModule'] != $place_module) {
                // $this->db->query("update PlaceModule set placeNum=PlaceNum-1 where id='{$place['placeModule']}'");
                // $this->db->query("update PlaceModule set placeNum=PlaceNum+1 where id='{$place_module}'");
            // }
            
            $b = true;
            // 修改
            $b_tip = false;
            if (empty($id)) {
                // 查询地点名称是否重复
                // $query = $this->db->select('count(*) as num')->get_where('Place', array('placename' => $placename));
                // $row = $query->row_array();
                // if ($row['num'] > 0) {
                    // $this->error('POI地点名称重复，请重新填写一个');
                // }

                // 新建
                $b &= $this->db->insert('Place', $data);
                $id = $this->db->insert_id();
                $b_tip = true;
            } else {
                // 编辑
                $b &= $this->db->where('id', $id)->update('Place', $data);
                // 删除之前的分类关系
                $b &= $this->db->delete('PlaceOwnCategory', array('placeId' => $id));
                // 删除之前的地点模型数据
                $b &= $this->db->delete('PlaceModuleData', array('placeId' => $id));
                // 删除模型关系数据
                $b &= $this->db->delete('PlaceOwnModule', array('placeId' => $id));
            }

            // 添加关系
            $cats = array();
            $cats[0]['placeId'] = $id;
            $cats[0]['placeCategoryId'] = $this->post('placeCategory_id');
            $brand_cat_id = $this->post('placeBrand_id');
            if ($brand_cat_id) {
                $cats[1]['placeId'] = $id;
                $cats[1]['placeCategoryId'] = $brand_cat_id;
            }
            $b &= $this->db->insert_batch('PlaceOwnCategory', $cats);

            // // 获取地点模型字段
            // $query = $this->db->order_by('orderValue', 'desc')->get_where('PlaceModuleField', array('moduleId' => $place_module));
            // $fields = $query->result_array();
            // // 添加模型数据
            // $is_visible = $this->post('is_visible');
            // $module_data = array();
            // foreach ($fields as $row) {
                // $value = $this->post($row['fieldId']);
                // $module_data[] = array(
                        // 'fieldId' => $row['fieldId'],
                        // 'placeId' => $id,
                        // 'mValue' => is_array($value) ? implode(',', $value) : $value,
                        // 'isVisible' => intval($is_visible[$row['fieldId']])
                // );
            // }
            // $b = true;
            // if ($module_data) {
                // $b = $this->db->insert_batch('PlaceModuleData', $module_data);
            // }
            
            // 更新模型的地点数
            if($place['module']) {
                foreach($place['module'] as $rank_order => $row) {
                    $b &= $this->db->query("UPDATE PlaceModule SET placeNum=placeNum-1 WHERE id='{$row['id']}'");
                }
            }
            
            $module_id = $this->post('module_id');
            //var_dump($module_id);exit;
            if($module_id) {
                // 地点模型关系数据
                $place_own_module = $module_data = array();
                foreach($module_id as $mid) {
                	if( intval($mid) <= 0 ) continue;
                    $place_own_module[] = array(
                        'placeId' => $id,
                        'placeModuleId' => $mid,
                        'rankOrder' => intval($this->post('rank_order_' . $mid))
                    );
                    // 获取模型字段信息
                    $fields = $this->db->order_by('orderValue', 'desc')->get_where('PlaceModuleField', array('moduleId' => $mid))->result_array();
                    foreach ($fields as $row) {
                        $value = $this->post($row['fieldId'] . '_' . $mid);
                        $is_visible = $this->post('is_visible_' . $mid);
                        $module_data[] = array(
                                'fieldId' => $row['fieldId'],
                                'placeId' => $id,
                                'moduleId' => $mid,
                                'mValue' => is_array($value) ? implode(',', $value) : $value,
                                'isVisible' => intval($is_visible[$row['fieldId']])
                        );
                    }
                    
                    // 更新模型的地点数
                    $b &= $this->db->query("UPDATE PlaceModule SET placeNum=placeNum+1 WHERE id='{$mid}'");
                }
            }
            $place_own_module && ($b &= $this->db->insert_batch('PlaceOwnModule', $place_own_module));
            $module_data && ($b &= $this->db->insert_batch('PlaceModuleData', $module_data));
            
            if ($b) {
                // 更新缓存
                // 更新地点缓存
                $api_rtn1 = api_update_cache('Place', $id);
                // 更新分类关系
                $api_rtn2 = api_update_cache('PlaceCategoryShip');
                
                // 通过最后一次判断是否成功
                $this->success(($b_tip ? '新建' : '修改') . 'POI成功', $this->_index_rel, $this->_index_uri, 'closeCurrent', array('p1'=>$api_rtn1, 'p2'=>$api_rtn2));
            } else {
                $this->success(($b_tip ? '新建' : '修改') . 'POI失败,请重试');
            }
        }
        // 计算模型选择的高度
        $h = count(array_keys($modules))*22;
        $this->assign('mheight', $h>120?120:$h);
        $this->assign('modules', $modules);

        $this->display('poi_add', 'poi');
    }

    /**
     * 编辑poi数据
     * 
     */
    public function edit() {

        $this->add();
    }
    
    /**
     * 修改审核POI
     */
    function edit_checked() {
        $id = $this->get('id');
            // 取出地点信息
        $query = $this->db->get_where('Place', array('id' => $id));
        $place = $query->row_array();
        $fresh_type = $this->get("fresh_type");
        empty($place) && $this->error('数据错误');
        if($place['isChecked']) {
            $arr = array('poi', 'poi', 'edit_checked');
            // 地点已经审核过了。不能再次审核
            $this->error('POI信息已经审核过了。不能再次审核。亲', build_rel($arr), site_url($arr), 'closeCurrent');
        }
        $this->assign('poi', $place);
        
        // 获取创建人的信息
        if($place['creatorId']) {
            $creator = $this->db->get_where('User', "id = '{$place['creatorId']}'")->row_array();
            $this->assign('creator', $creator);
        }
        
        if($this->is_post() && $fresh_type!=="get") {
            // 提交修改
            $placename = trim($this->post('placename'));
            // $place_module = $this->post('place_module');
            $poi_icon = $this->post('poi_icon');
            $poi_icon = $poi_icon?array_filter($poi_icon):array();
            $access_checked = $this->post('checked');
            
            //是否处理POI，否则仅保存
            $handle = ($this->post('checked') || $this->post('checked')==="0") ? 1 : 0;
            
            
            $data = array(
                    'placename' => $placename,
                    'address' => $this->post('address'),
                    'latitude' => floatval($this->post('latitude')),
                    'longitude' => floatval($this->post('longitude')),
                    'tel' => $this->post('tel'),
                    'pcc' => $this->post('is_business') ? intval($this->post('pcc')) : 0,
                    'isBusiness' => $this->post('is_business'),
                    'icon' => $poi_icon[0],
                    'isChecked' => !$handle ? 0 : 1,
                    // 'placeModule' => $place_module,
                    'status' => !$handle ? 0 : ($access_checked?0:2),
            		'isRepayPoint' => $this->post("isRepayPoint")
            );
            
            if($handle){
	            $this->load->helper('ugc');
	            if($this->post('checked') == 1 && $place['creatorId']) {
	                //积分操作
	                make_point($creator['id'], 'poi_create', "0", $id);
	            }
            }

            // 操作place module的东东
            // if ($place['placeModule'] != $place_module) {
                // $this->db->query("update PlaceModule set placeNum=PlaceNum-1 where id='{$place['placeModule']}'");
                // $this->db->query("update PlaceModule set placeNum=PlaceNum+1 where id='{$place_module}'");
            // }

            // 查询地点名称是否重复
            // $query = $this->db->select('count(*) as num')->get_where('Place', "placename = '".daddslashes($placename)."' and id<>'{$id}'");
            // $row = $query->row_array();
            // if ($row['num'] > 0) {
                // $this->error('POI地点名称重复，请重新填写一个');
            // }
            
            // 编辑
            $this->db->where('id', $id)->update('Place', $data);
            // 删除之前的分类关系
            $this->db->delete('PlaceOwnCategory', array('placeId' => $id));
            // 删除之前的地点模型数据
            // $this->db->delete('PlaceModuleData', array('placeId' => $id));

            // 添加关系
            $cats = array();
//             $cats[0]['placeId'] = $id;
//             $cats[0]['placeCategoryId'] = $this->post('placeCategory_id');
//             $brand_cat_id = $this->post('placeBrand_id');
//             if ($brand_cat_id) {
//                 $cats[1]['placeId'] = $id;
//                 $cats[1]['placeCategoryId'] = $brand_cat_id;
//             }
            $pcids = explode(',', $this->post('placeCategory_id'));
            foreach($pcids as $pcid) {
            	$cats[] = array(
            			'placeId' => $id,
            			'placeCategoryId' => $pcid
            	);
            }
            $brand_cat_id = $this->post('placeBrand_id');
            if ($brand_cat_id) {
            	$cats[] = array(
            			'placeId' => $id,
            			'placeCategoryId' => $brand_cat_id
            	);
            }
            $this->db->insert_batch('PlaceOwnCategory', $cats);

            // 获取地点模型字段
            // $query = $this->db->order_by('orderValue', 'desc')->get_where('PlaceModuleField', array('moduleId' => $place_module));
            // $fields = $query->result_array();
            // 添加模型数据
            // $is_visible = $this->post('is_visible');
            // $module_data = array();
            // foreach ($fields as $row) {
                // $value = $this->post($row['fieldId']);
                // $module_data[] = array(
                        // 'fieldId' => $row['fieldId'],
                        // 'placeId' => $id,
                        // 'mValue' => is_array($value) ? implode(',', $value) : $value,
                        // 'isVisible' => intval($is_visible[$row['fieldId']])
                // );
            // }
            $b = true;
            // if ($module_data) {
                // $b = $this->db->insert_batch('PlaceModuleData', $module_data);
            // }
            if ($b) {
                // 给用户发系统消息
                if($place['creatorId'] && $handle) {
                    if($this->post('checked') == 1) {
                        // 通过审核
                        // 发送系统信息
                        send_message('sm_poi_checked', array($place['creatorId']), array($id), array(1), TRUE, array(array('place'=>$placename)));
                    } elseif (2 == $this->post('checked')) {
                    	// 不需要送积分发送消息
                    	send_message($this->post('sys_msg'), array($place['creatorId']), array($id), array(1), FALSE);
                    } else {
                        // 删除地点
                        // 发送系统信息
                        send_message('sm_poi_checked_delete', array($place['creatorId']), array(-1), array(-1), TRUE, array(array('place'=>$placename)));
                    }
                }
                
                // 更新缓存
                // 更新地点缓存
                $api_rtn1 = api_update_cache('Place', $id);
                // 更新分类关系
                $api_rtn2 = api_update_cache('PlaceCategoryShip');
                
                send_api_interface('/private_api/place/update_solr', 'POST', array('place_id' => $id));
                
                $arr = array('poi', 'poi', 'checked_poi');
                
                if($access_checked && $handle) {
                    // 通过审核那么去添加索引
                    $this->load->helper('search');
                    // 更新地点
                    @update_index(20, $id);
                    // 看是否为楼盘
                    if(100 == $cats[0]['placeCategoryId']) {
                        @update_index(10, $id);
                    }
                }
                if($handle){
                	$tip = "审核编辑POI";
                }else{
                	$tip = "保存";
                }
                
                // 通过最后一次判断是否成功
                $this->success($tip.'成功', build_rel($arr), site_url($arr), 'closeCurrent', array('p1'=>$api_rtn1, 'p2'=>$api_rtn2));
            } else {
                $this->success($tip.'失败,请重试');
            }
        }
        
        // 选出地点分类
        $query = $this->db->select('id, content, isBrand')->from('PlaceOwnCategory poc, PlaceCategory pc')->where("poc.placeId = '{$id}' and poc.placeCategoryId = pc.id")->get();
        $category = $query->result_array();
        $cate = array();
        foreach ($category as $row) {
            $isBrand = $row['isBrand'];
            unset($row['isBrand']);
            if ($isBrand) {
                // 品牌分类
                $cate['brand'] = $row;
            } else {
                // 普通分类
                $cate['common'] = $row;
            }
        }
        $this->assign('category', $cate);
        $this->assign('creator_types', $this->config->item('poi_creator_type'));
        
        // 获取模型数据
        $query = $this->db->get('PlaceModule');
        $modules = $query->result_array();
        $this->assign('modules', $modules);
        
        // 获取系统消息
        $this->lang->load('premessage','chinese');
        $sys_msg = sprintf($this->lang->line('sm_poi_none_score'), $place['placename']);
        $this->assign('sys_msg', $sys_msg);
        
        
        $this->display('poi_edit_checked', 'poi');
    }
    
    function batch_check(){
    	if($this->is_post()){
    		$this->load->helper('search');
	    	$ids = $this->post("ids");
	    	$id_array = array_filter(explode(",",$ids));
	    	foreach($id_array as $id){
	    		$place = $this->db->where("id",$id)->get($this->_tables['place'])->row_array();
	    		$placeCategory = $this->db->select('placeCategoryId')->where("placeId",$id)->get($this->_tables['placeowncategory'])->row_array();
	    		
	    		if(!$placeCategory['placeCategoryId']){
	    			//没有设置分类的就跳过
	    			continue;
	    		}
	    		
	    		
	    		$data = array(
	               'isChecked' => 1,
	               'status' => 0,	
	            );
	            
		    	$this->load->helper('ugc');
		        if(/*$this->post('checked') == 1 && */$place['creatorId']) {
		           	//积分操作
		            make_point($place['creatorId'], 'poi_create', "0", $id);
		        }
		        
		        // 编辑
	            $this->db->where('id', $id)->update('Place', $data);
	            
	
	    		if($place['creatorId']) {
	                     // 通过审核
	                     // 发送系统信息
	                send_message('sm_poi_checked', array($place['creatorId']), array($id), array(1), TRUE, array(array('place'=>$place['placename'])));
	                    
	            }
	                
	            // 更新缓存
	            // 更新地点缓存
	            $api_rtn1 = api_update_cache('Place', $id);
	            // 更新分类关系
	            //$api_rtn2 = api_update_cache('PlaceCategoryShip');
	                
	            $arr = array('poi', 'poi', 'checked_poi');
	                
	            //if($access_checked) {
	                // 通过审核那么去添加索引
	                
	                // 更新地点
	                @update_index(20, $id);
	                // 看是否为楼盘
	                if(100 == $cats[0]['placeCategoryId']) {
	                    @update_index(10, $id);
	                }
	            //}
	    	}
	    	api_update_cache('PlaceCategoryShip');
	    	$this->success('批量审核编辑POI成功', "poi/poi/checked_poi", "poi/poi/checked_poi", 'forward');
    	}
    }
	
    function batch_delete_poi(){
    	if($this->is_post()){
    		$this->load->helper('search');
	    	$ids = $this->post("ids");
	    	$id_array = array_filter(explode(",",$ids));
	    	$this->load->helper('ugc');
	    	foreach($id_array as $id){
	    		$data = array(
	               'isChecked' => 1,
	               'status' => 2,	
	            );
	            $place = $this->db->where('id', $id)->get('Place')->row_array();
	            $res = $this->db->where('id', $id)->update('Place', $data);
	            $this->db->delete('PlaceOwnCategory', array('placeId' => $id));            
	            $res && send_message('sm_poi_checked_delete', array($place['creatorId']), array(-1), array(-1), TRUE, array(array('place'=>$place['placename'])));       
	    		api_update_cache('Place', $id);
	    	}
	    	api_update_cache('PlaceCategoryShip');
	    	$this->success('批量删除POI成功', "poi/poi/checked_poi", "poi/poi/checked_poi", 'forward');
    	}
    }
    /**
     * 删除poi数据
     * Create by 2012-3-19
     * @author liuw
     */
    public function delete() {
        exit('删除poi');
    }

    /**
     * 改变状态
     */
    function change_status(  ) {
        $id = $this->get('id');
        $status = $this->get('status');
		$type = $this->get('type');
		
        if (!in_array($status, array(
                0,
                1,
                2
        ))) {
            $this->error('错误的状态');
        }

        // 修改状态
        if ($this->db->where('id', $id)->update('Place', array('status' => $status))) {
            // 更新缓存
            // 更新地点缓存
            api_update_cache('Place', $id);
            
            $this->load->helper('search');
            // 更新地点
            @update_index(20, $id, $status<2?'update':'delete');
            if($type == 100){
            	@update_index(10, $id, $status<2?'update':'delete');
            }
            
            $this->success('更新POI状态成功', '', '', '', array(
                    'id' => $id,
                    'key' => $status,
                    'value' => $this->poi_status[$status]
            ));
        } else {
            $this->error('更新POI状态失败');
        }
    }

    /**
     * 地标
     */
    function place_mark() {
        $id = $this->get('id');
        $status = $this->get('status');
        $id || die();
        // 先删除地标
        $b = $this->db->where('placeId', $id)->delete('LocalLandMark');
        if ($status > 0) {
            // 设定地表
            $b = $this->db->query("insert into LocalLandMark(placeId, lat, lng, placename) 
                                   select id, latitude, longitude, placename from Place where id='{$id}'");
        }

        if ($b) {
            // 更新地点缓存
            api_update_cache('Place', $id);
            
            $this->success('设置地标成功', '', '', '', array(
                    'id' => $id,
                    'key' => $status
            ));
        } else {
            $this->error('地标设置出错');
        }
    }

    /**
     * 改变权重
     */
    function change_rating() {
        $id = $this->get('id');

        // 读取出本来的记录
        $rating = $this->db->get_where('PlaceExtraRating', array('placeId' => $id))->row_array();

        if ($this->is_post()) {
            // 提交
            $starttime = $this->post('start_date');
            $endtime = $this->post('end_date');
            // 判断是否结束时间小于开始时间
            $t1 = strtotime($starttime);
            $t2 = strtotime($endtime);
            if ($t2 <= $t1) {
                $this->error('设定结束时间必须在开始时间之后');
            }
            $rating_num = intval($this->post('rating'));
            $status = (time() > $t1) ? 1 : 0;
            $data = array(
                    'placeId' => $id,
                    'rating' => $rating_num,
                    'startDate' => $starttime,
                    'endDate' => $endtime,
                    'status' => $status
            );

            $b = $rating ? $this->db->where('placeId', $id)->update('PlaceExtraRating', $data) : $this->db->insert('PlaceExtraRating', $data);
            // 获取现在的权重
            $row = $this->db->select('rating')->where("id = '{$id}'")->get('Place')->row_array();
            if ($status) {
                // 当前时间已经大于开始时间
                // 之前已经改变过权重, 并权重已经加上了
                // 那么需要先减去加上的权重在用本次改变权重加上去
                // 没有改变过权重
                $rating_total = $row['rating'] + $rating_num - (($rating && $rating['status'] == 1) ? $rating['rating'] : 0);
            } else {
                // 没开始执行
                // 判断是否之前有添加过的，并正在执行中
                $rating_total = $row['rating'] - (($rating && $rating['status'] == 1) ? $rating['rating'] : 0);
            }
            // 需要写入到place中
            $b &= $this->db->where("id = '{$id}'")->update('Place', array('rating' => $rating_total));
            if ($b) {
                // 更新缓存
                // 更新地点缓存
                api_update_cache('Place', $id);
                $this->success('更新地点的权重成功', '', '', '', array(
                        'id' => $id,
                        'value' => $rating_num,
                        'status' => $status,
                        'rating' => $rating_total
                ));
            } else {
                $this->error('更新地点的权重出错，请重试');
            }
        }

        $this->assign('rating', $rating);
        $this->display('poi_extra_rating', 'poi');
    }

    /**
     * POI详情
     */
    function detail() {
        $keywords = $this->get('keywords') ? $this->get('keywords') : $this->post('keywords');
        $type = $this->get('type') ? $this->get('type') : $this->post('type');
        $this->assign(array(
                'type' => $type,
                'keywords' => $keywords
        ));

        // 获取地点的信息
        $place = $this->db->get_where('Place', array($type => $keywords))->row_array();
        if (empty($place)) {
            $this->error('没有该地点信息，请检查');
        }

        // 获取访客

        // 获取分类
        $cats = $this->db->select('pc.id as catid, pc.content as catname, pc.level')->from('PlaceOwnCategory poc, PlaceCategory pc')->where("poc.placeId = '{$place['id']}' and poc.placeCategoryId = pc.id")->get()->result_array();
        $new_cats = $catids = $category = array();
        if ($cats) {
            foreach ($cats as $cat) {
                if ($cat['level'] > 0) {
                    // 子分类，那么去获取上级分类
                    $new_cats['child'][$cat['catid']] = $cat['catname'];
                    $catids[] = $cat['catid'];
                } else {
                    $category[] = $cat['catname'];
                }
            }
            if ($catids) {
                // 需要获取父分类
                $cats = $this->db->select('pcs.parent as catid, pcs.child, pc.content as catname')->from('PlaceCategory pc, PlaceCategoryShip pcs')->where("pc.id = pcs.parent and pcs.child in ('" . implode("','", $catids) . "')")->get()->result_array();
                foreach ($cats as $cat) {
                    $category[] = $cat['catname'] . ' &gt; ' . $new_cats['child'][$cat['child']];
                }
            }
        }
        $place['category'] = implode("\n", $category);
        unset($new_cats);
        unset($catids);
        unset($category);
		
       
        $this->assign('place', $place);

        $this->display('poi_detail', 'poi');
    }

    /**
     * 签到数据
     */
    function find_checkin() {
        $id = $this->get('id');
        $where_sql = "u.id = p.uid and type = 1 and p.placeId = '{$id}'";
        $row = $this->db->from('User u, Post p')->select('count(*) as num')->where($where_sql)->get()->row_array();
        $total_num = $row['num'];
        $paginate = $this->paginate($total_num);

        // 排序字段
        $order_field = $this->post('orderField');
        $order_field || $order_field = 'createDate';
        // 排序方式
        $order_direction = $this->post('orderDirection');
        $order_direction || $order_direction = 'desc';
        $this->assign(compact('order_field', 'order_direction'));
        // if ($order_field && $order_direction) {
        $this->db->order_by($order_field, $order_direction);
        // }

        $query = $this->db->from('User u, Post p')->select('u.username, u.id, u.avatar, p.createDate')->where($where_sql)->limit($paginate['per_page_num'], $paginate['offset'])->get();
        $list = $query->result_array();
        foreach ($list as &$row) {
            $row['avatar_uri'] = image_url($row['avatar'], 'head', 'odp');
        }
        unset($row);

        $this->assign('list', $list);

        $this->display('detail_checkin', 'poi');
    }

    /**
     * 点评数据
     */
    function find_tip() {
        $id = $this->get('id');
        $this->assign('id', $id);
        // $where_sql = "u.id = p.uid and type = 2 and p.placeId = '{$id}'";
        // $row = $this->db->from('User u, Post p')->select('count(*) as num')->where($where_sql)->get()->row_array();
        $row = $this->db->query("
        select sum(num) as num from (SELECT count(*) as num
        FROM (`Post` p) 
        WHERE p.type in (2,3) and p.placeId = '{$id}' and p.status < 2 
        UNION 
        select 
        count(*) as num
        from Post p, Reply pr
        where p.id=pr.itemId and pr.itemType=19 and p.placeId = '{$id}' and p.type = 2 and pr.status < 2) b
        ")->row_array();
        $total_num = $row['num'];
        $paginate = $this->paginate($total_num);

        // 排序字段
        $order_field = $this->post('orderField');
        $order_field || $order_field = 'createDate';
        // 排序方式
        $order_direction = $this->post('orderDirection');
        $order_direction || $order_direction = 'desc';
        $this->assign(compact('order_field', 'order_direction'));
        // if ($order_field && $order_direction) {
        // $this->db->order_by($order_field, $order_direction);
        // }

        // $query = $this->db->from('User u, Post p')->select('p.id as pid, p.content, p.praiseCount, p.replyCount, u.username, u.id, u.avatar, p.createDate')->where($where_sql)->limit($paginate['per_page_num'], $paginate['offset'])->get();
        $query = $this->db->query("
        SELECT `p`.`id` as pid, `p`.`content`, `p`.`praiseCount`, `p`.`replyCount`, 
        `u`.`username`, `u`.`id`, `u`.`avatar`, `p`.`createDate`  ,`p`.`photo`
        FROM (`User` u, `Post` p) 
        WHERE `u`.`id` = p.uid and p.type in (2,3) and p.placeId = '{$id}' and p.status < 2
        UNION 
        SELECT 
        pr.id as pid, pr.content, '-', CONCAT('回复@', uu.username), u.username, 
        pr.uid, u.avatar, pr.createDate,p.photo
        FROM Post p, Reply pr, User u, User uu
        WHERE p.id=pr.itemId and pr.itemType=19 and p.placeId = '{$id}' and p.type in (2,3) and pr.uid = u.id and p.uid = uu.id and pr.status < 2
        ORDER BY {$order_field} {$order_direction} LIMIT {$paginate['per_page_num']} OFFSET {$paginate['offset']}
        ");
        $list = $query->result_array();
        foreach ($list as &$row) {
            $row['avatar_uri'] = image_url($row['avatar'], 'head', 'odp');
        }
        unset($row);

        $this->assign('list', $list);

        $this->display('detail_tip', 'poi');
    }

    /**
     * 图片数据
     */
    function find_photo() {
        $id = $this->get('id');
        $this->assign('id', $id);
        // $where_sql = "u.id = p.uid and type = 3 and p.placeId = '{$id}'";
        // $row = $this->db->from('User u, Post p')->select('count(*) as num')->where($where_sql)->get()->row_array();
        $row = $this->db->query("
        select sum(num) as num from (SELECT count(*) as num
        FROM (`Post` p) 
        WHERE p.type = 3 and p.placeId = '{$id}' and p.status < 2 
        UNION 
        select 
        count(*) as num
        from Post p, PostReply pr
        where p.id=pr.postId and p.placeId = '{$id}' and p.type = 3 and pr.status < 2) b
        ")->row_array();
        $total_num = $row['num'];
        $paginate = $this->paginate($total_num);

        // 排序字段
        $order_field = $this->post('orderField');
        $order_field || $order_field = 'createDate';
        // 排序方式
        $order_direction = $this->post('orderDirection');
        $order_direction || $order_direction = 'desc';
        $this->assign(compact('order_field', 'order_direction'));
        // if ($order_field && $order_direction) {
        // $this->db->order_by($order_field, $order_direction);
        // }

        // $query = $this->db->from('User u, Post p')->select('p.id as pid, p.photoName, p.content, p.praiseCount, p.replyCount, u.username, u.id, u.avatar, p.createDate')->where($where_sql)->limit($paginate['per_page_num'], $paginate['offset'])->get();
        $query = $this->db->query("
        SELECT `p`.`id` as pid, `p`.`content`, `p`.`praiseCount`, `p`.`replyCount`, 
        `u`.`username`, `u`.`id`, `u`.`avatar`, `p`.`createDate`, `p`.`photoName`
        FROM (`User` u, `Post` p) 
        WHERE `u`.`id` = p.uid and p.type = 3 and p.placeId = '{$id}' and p.status < 2
        UNION 
        SELECT 
        pr.id as pid, pr.content, '-', CONCAT('回复@', uu.username), u.username, 
        pr.uid, u.avatar, pr.createDate, ''
        FROM Post p, PostReply pr, User u, User uu
        WHERE p.id=pr.postId and p.placeId = '{$id}' and p.type = 3 and pr.uid = u.id and p.uid = uu.id and pr.status < 2
        ORDER BY {$order_field} {$order_direction} LIMIT {$paginate['per_page_num']} OFFSET {$paginate['offset']}
        ");
        $list = $query->result_array();
        foreach ($list as &$row) {
            $row['avatar_uri'] = $row['avatar'] ? image_url($row['avatar'], 'head', 'odp') : '';
            $row['photo_uri'] = $row['photoName'] ? image_url($row['photoName'], 'user', 'odp') : '';
        }
        unset($row);

        $this->assign('list', $list);

        $this->display('detail_photo', 'poi');
    }

    /**
     * 活动数据
     */
    function find_event() {

    }

    /**
     * 优惠数据
     */
    function find_prefer() {

    }

    /**
     * 获取模型数据
     */
    function find_module() {
        $id = $this->get('id');
        $module_id = $this->get('mid');

        // 选出模型的字段数据
        $query = $this->db->order_by('orderValue', 'desc')->get_where('PlaceModuleField', array('moduleId' => $module_id));
        $fields = $query->result_array();
        $this->assign('fields', $fields);

        //
        if ($id) {
            // 如果传递了POI的ID号，那么去获取POI的模型数据
            $query = $this->db->get_where('PlaceModuleData', array('placeId' => $id));
            $module_data = $query->result_array();
            $data = array();
            foreach ($module_data as $row) {
                $data[$row['fieldId']] = $row['mValue'];
            }
            $this->assign('data', $data);
        }

        $this->display('detail_module', 'poi');
    }

    /**
     * 点评
     */
    function tip() {
    	$this->photo();
    	exit;
        // 地点ID号
        $id = $this->get('id');

        if ($this->is_post()) {
            // 提交发布点评
            $content = $this->post('content');
            $v_id = $this->_get_vid();

            // 插入post数据
            $arr = array(
                    'uid' => $v_id,
                    'placeId' => $id,
                    'type' => 2,
                    'content' => $content
            );
            $this->db->insert('Post', $arr);
            $post_id = $this->db->insert_id();

            // 更新地点POI的点评数
            // 每次去重置一次点评数。保证他的正确
            // POI
            $row = $this->db->select('count(*) as num')->from('Post')->where("placeId = '{$id}' and type = 2")->get()->row_array();
            $this->db->where(array('id' => $id))->update('Place', array('tipCount' => $row['num']));
            // 用户
            $row = $this->db->select('count(*) as num')->from('Post')->where("uid = '{$v_id}' and type = 2")->get()->row_array();
            $this->db->where(array('id' => $v_id))->update('User', array('tipCount' => $row['num']));

            // FEED
            $this->_add_feed($post_id, $id, $v_id, 2, 2, $content);
            
            // 更新索引
            $this->load->helper('search');
            @update_index(40, $id);

            $this->success('发布点评成功', '', '', 'closeCurrent');
        }

        $this->display('poi_tip', 'poi');
    }

    /**
     * 发布照片
     */
    function photo() {
        // 地点ID号
        $id = $this->get('id');

        if ($this->is_post()) {
        	//var_dump($_POST,$_FILES);exit;
            // 提交发布图片
            /*$content = $this->post('content');

            $v_id = $this->_get_vid();
            // 插入post数据
            $arr = array(
                    'uid' => $v_id,
                    'placeId' => $id,
                    'type' => 2,
                    'content' => $content,
                    'photo' => $this->post('poi_pub_photo')
            );
            $this->db->insert('Post', $arr);
            $post_id = $this->db->insert_id();
            
            // 更新地点POI的图片数
            // 每次去重置一次图片数。保证他的正确
            // 地点
            $row = $this->db->select('count(*) as num')->from('Post')->where("placeId = '{$id}' and type = 3")->get()->row_array();
            $this->db->where(array('id' => $id))->update('Place', array('photoCount' => $row['num']));
            // 用户
            $row = $this->db->select('count(*) as num')->from('Post')->where("uid = '{$v_id}' and type = 3")->get()->row_array();
            $this->db->where(array('id' => $v_id))->update('User', array('photoCount' => $row['num']));
            
            $this->_add_feed($post_id, $id, $v_id, 3, 3, $content, $arr['photoName']);
            
            // 更新索引
            $this->load->helper('search');
            @update_index(40, $id);
            
            $this->success('发布图片成功', '', '', 'closeCurrent');*/
        	//应该使用接口去发图片/点评
        	
        	$content = $this->post('content');
			$photo = $this->post('poi_pub_photo');
            $v_id = $this->_get_vid();
            if($photo){ 
				$img_c = $this->config->item ( 'image_cfg' );
				$locale = $img_c ['upload_path'] . str_replace ( './', '', basename ( $photo ) );
            }
            $attrs = array(
            	'place_id' => $id,
            	'content' => $content,
            	//'@uploaded_file' => '@'.$locale
            );
            if($locale){
            	$attrs['uploaded_file'] = '@'.$locale;
            }
            //var_dump($locale);exit;
            $headers = array('uid' => $v_id);            
            $result = json_decode (request_api('/post/save_tip', 'POST', $attrs, $headers),true);
            //var_dump($result);
            
            $this->load->helper('search');
            @update_index(40, $id);
            $code = ! is_int ( $result ['result_code'] ) ? 1 : $result ['result_code'];
            if($code===0){
            	$this->success('发布点评成功', '', '', 'closeCurrent', $result);
            }
            else{
            	if(empty($result)){
            	$this->error('你发布的内容可能发出去了，可能没有~如果没有，请重试哦！', '', '', 'closeCurrent', $result);
            	}else{
            	$this->error('发布点评失败，错误信息:'.$result['result_msg'], '', '', 'closeCurrent', $result);
            	}
            }
        }

        $this->display('poi_photo', 'poi');
    }

    /**
     * 回复点评
     */
    function reply_tip() {
        $this->reply();
    }

    /**
     * 回复照片
     */
    function reply_photo() {
        $this->reply();
    }

    function reply() {
        list($receiver, $item_id, $reply_type) = explode('_', $this->get('id'));
        $reply_type = $reply_type == '-' ? 1 : 0;
        redirect(site_url(array(
                'ugcv3',
                'reply',
                'send',
                'receiver',
                $receiver,
                'reply_type',
                $reply_type,
                'item_id',
                $item_id,
                'item_type',
                $this->get('item_type')
        )));
    }

    /**
     * 获取马甲ID
     */
    function _get_vid() {
        $vest = $this->post('vest');
        if ($vest === 'random') {
            //随机
            $rs = $this->db->where('aid', $this->auth['id'])->select('uid')->order_by('', 'random')->limit(1)->get('MorrisVest')->first_row('array');
            $v_id = $rs['uid'];
        } else {
            $v_id = $this->post('v_id');
        }

        if (empty($v_id)) {
            $this->error('请先设定马甲');
        }

        return $v_id;
    }
    
    /**
     * 加入动态
     * $post_id POST ID号
     * $id 地点ID
     * $uid 用户ID
     * $type 加入动态 type值
     * $item_type 加入动态的itemType 
     * $content 内容
     * $photo 图片地址
     */
    function _add_feed($post_id, $id, $uid, $type, $item_type, $content, $photo = '') {
        // 获取地点信息
        $place = $this->db->get_where('Place', array('id' => $id))->row_array();
        // 获取用户信息
        $user = $this->db->get_where('User', array('id' => $uid))->row_array();
        $json = array(
                'item_id' => $post_id,
                'content' => $content,
                'placename' => $place['placename'],
                'place_id' => $id
        );
        $photo && $json['photo_uri'] = $photo;
        // FEED
        
        $data = array(
                'uid' => $uid,
                // 'detail' => php_json_encode($json),
                'detail' => encode_json($json),
                'itemType' => $item_type,
                'itemId' => $post_id,
                'nickname' => $user['nickname'],
                'avatar' => $user['avatar'],
                'type' => $type,
                'latitude' => $place['latitude'],
                'longitude' => $place['longitude'],
                'placename' => $place['placename'],
                'createDate' => date('Y-m-d H:i:s')
        );
        
//         $this->db->insert('UserFeed', $data);
    }

    /**
     * 待审核poi
     */
    function checked_poi() {
        $keywords = trim($this->post('keywords'));
        $creator_type = $this->post('creator_type');
        $checked = $this->post('checked');
        
        $orderField = $this->post('orderField');
        $orderField = isset($orderField)&&!empty($orderField)?$orderField:'createDate';
        $orderDirection = $this->post('orderDirection');
        $orderDirection = isset($orderDirection)&&!empty($orderDirection)?$orderDirection:'asc';
        
        $where_sql = array();
        if($keywords) {
            $keytext = daddslashes($keywords);
            $where_sql[] = "p.placename like '%{$keytext}%'";
        }
        if($creator_type) {
            $where_sql[] = "p.creatorType = '{$creator_type}'";
        } else {
            $where_sql[] = 'p.creatorType > 0';
        }
        $checked || $checked = 0;
        switch($checked) {
            case 0:
                $where_sql[] = 'p.isChecked = 0';
                break;
            case 1:
                $where_sql[] = 'p.isChecked = 1';
                $where_sql[] = 'p.status = 0';
                break;
            case -1:
                $where_sql[] = 'p.status = 2';
                $where_sql[] = 'p.isChecked = 1';
                break;
        }
        
        $where_sql && $where_sql = implode(' and ', $where_sql);
        
        $total_num = $this->db->where($where_sql)->from('Place p')->count_all_results();
        $paginate = $this->paginate($total_num);
        
        $list = $this->db->order_by($orderField, $orderDirection)
                         ->limit($paginate['per_page_num'], $paginate['offset'])
                         ->where($where_sql)
                         ->from('Place p')
                         ->get()->result_array();
        $this->assign(compact('keywords', 'creator_type', 'checked', 'list','orderField','orderDirection'));
        $this->assign('creator_types', $this->config->item('poi_creator_type'));
        $this->display('poi_checked', 'poi');
    }
    
    /**
     * 调用接口显示指定地点附近的地点列表
     * Create by 2012-9-12
     * @author liuw
     */
    function nearby_list(){
    	$id = $this->get('id');//地点ID
    	if(!isset($id)||empty($id))
    		$this->error($this->lang->line('poi_category_id_null'));
    	$cid = $this->post('placeCategory_id');//分类ID
    	$cid = isset($cid)&&!empty($cid)?intval($cid):false;
    	if($cid){//分类名称
    		$query = $this->db->where('id', $cid)->select('content')->get('PlaceCategory')->first_row('array');
    		$cname = $query['content'];
    	}else 
    		$cname = '全部地点';
    	$sort = $this->post('sort');//排序规则
    	$sort = isset($sort)&&!empty($sort)?intval($sort):4;
    	$offset = $this->post('offset');//数据长度
    	$offset = isset($offset)&&!empty($offset)?intval($offset):20;
    	$this->assign(compact('id', 'cid', 'sort', 'offset', 'cname'));
    	//获得指定地点的坐标
    	$place = $this->db->where('id', $id)->limit(1)->select('latitude, longitude')->get('Place')->first_row('array');
    	//接口参数
    	/*$arr = array(
    		'sort'=>$sort,
    		'lng'=>$place['longitude'],
    		'lat'=>$place['latitude'],
    		'page_num'=>1,
    		'page_size'=>$offset
    	);*/
    	$attrs = array(
    		'id' => $id,
    		'type' => 2,
    		'page_num'=>1,
    		'page_size'=>$offset
    	);
    	if($cid) $arr['cid'] = $cid;
    	//接口地址
        //$this->lang->load('api');
    	//$api_url = $this->lang->line('api_poi_category');
    	//获取数据
    	//$json = json_decode(send_api_interface($api_url, 'GET', $arr), true);
    	
    	$result = request_api('/place/list_relation', 'GET', $attrs);
    	if(!$result) $this->error("请求API失败");
    	$json = json_decode($result,true);
    	
    	//var_dump($result);exit;
    	if($json['result_code'] != 0)
    		$this->error($json['result_msg'], $this->_index_rel, $this->_index_uri, 'closeCurrent');
    	else{
    		$list = array();
    		foreach($json['result_list']['page_data'] as $key=>$data){
    			//人均
    			$data['pcc'] = floatval($data['pcc']);
    			$data['pcc'] > 0 || $data['pcc'] = '-';
    			//星级
    			$level = floor($data['level'] / 2);
    			$exp = intval($data['level']) % 2;
    			$data['level'] = ($level<=0?1:$level).'星'.($exp > 0 ? '半':'');
    			//距离
    			$data['distance'] .= '米';
    			//其他
    			$arr = array(
    				$data['has_event']>0?$data['has_event']:'无',
    				$data['has_prefer']>0?$data['has_prefer']:'无',
    				$data['has_groupon']>0?$data['has_groupon']:'无',
    				$data['has_film_ticket']>0?$data['has_film_ticket']:'无',
    			);
    			$data['other'] = implode('|', $arr);
    			
    			$list[] = $data;
    		}
    		$this->assign('list',$list);
    		$this->display('poi_nearby', 'poi');
    	}
    }
    
    /**
     * 地点的碎片数据添加
     */
    function poi_block() {
        // 属性的ID号
        $id = $this->get('id');
        $page_id = $this->get('page_id');
        $place_id = $this->get('place_id');
        $module_id = $this->get('module_id');
        $property_id = $this->get('property_id');
        $block_id = $this->get('block_id');
        
        // 获取所有的可以用的模型
        $module_list = $this->db->order_by('rankOrder', 'asc')->get_where('PlaceModule')->result_array();
        
        // 获取碎片数据
        $property = $this->db->get_where('PlaceOwnSpecialProperty', array('id' => $property_id))->row_array();
        $property['images'] = explode(',', $property['images']);
        
        $this->assign(compact('id', 'page_id', 'place_id', 'module_id', 'property_id', 'block_id', 'module_list', 'property'));
        
        $this->display('poi_block', 'poi');
    }
    
    /**
     * 地点碎片模型数据
     */
    function poi_block_module() {
        $page_id = $this->get('page_id');
        $place_id = $this->get('place_id');
        $module_id = $this->get('module_id');
        $block_id = $this->get('block_id');
        $property_id = $this->get('property_id');
        
        if($module_id > 0) {
            // 获取模型
            $module = $this->db->get_where('PlaceModule', array('id' => $module_id))
                               ->row_array();
            if(empty($module)) {
                $this->error('错误');
            }
            
            // 选出模型的字段数据
            $query = $this->db->order_by('orderValue', 'asc')->get_where('PlaceModuleField', array('moduleId' => $module_id));
            $fields = $query->result_array();

            if ($place_id) {
                // 获取模型的排序
                $row = $this->db->get_where('PlaceOwnModule', array('placeModuleId' => $module_id, 'placeId' => $place_id))
                                ->row_array();
                $module['rankOrder'] = $row['rankOrder'];              
                // 如果传递了POI的ID号，那么去获取POI的模型数据
                $query = $this->db->get_where('PlaceModuleData', array('placeId' => $place_id, 'moduleId' => $module_id));
                $module_data = $query->result_array();
                $data = array();
                foreach ($module_data as $row) {
                    $data[$row['fieldId']]['value'] = $row['mValue'];
                    $data[$row['fieldId']]['isVisible'] = $row['isVisible'];
                }
                $this->assign('data', $data);
            }
        } else {
            $module = $fields = array();
            // 根据property_id
            if($property_id > 0) {
                $place_property = $this->db->get_where('PlaceOwnSpecialProperty', array('id' => $property_id))->row_array();
                $this->assign('place_property', $place_property);
            }
        }
        $rich_image_fields = $this->config->item('rich_image');
        array_shift($rich_image_fields);
        $rich_image_fields = json_encode($rich_image_fields);
        $this->assign(compact('page_id', 'place_id', 'module_id', 'block_id', 'module', 'fields', 'rich_image_fields'));
        
        $this->display('poi_block_module', 'poi');
    }

    /**
     * 签到
     */
    function checkin() {
        if($this->is_post()) {
            // 选出地点经纬度
            $id = intval($this->get('id'));
            $place = $this->db->select('latitude, longitude')->get_where('Place', array('id' => $id))->row_array();
            
            empty($place) && $this->error('错误');
            
            $vest = $this->post('vest');
            if($vest === 'random'){
                //随机
                $rs = $this->db->where('aid', $this->auth['id'])->select('uid')->order_by('uid','random')->limit(1)->get('MorrisVest')->first_row('array');
                $v_id = $rs['uid'];
            }else{
                $v_id = $this->post('v_id');
            }
            
            
            $data = send_api_interface('/private_api/post/save_checkin', 'POST', array('place_id' => $id, 'lat' => $place['latitude'], 'lng' => $place['longitude']), array('X-INID: ' . $v_id));
            
            $json = json_decode($data, true);
            
            $json['result_code'] ? $this->error('签到失败['.$json['result_msg'].']', '', '', 'closeCurrent'):$this->success('签到成功', '', '', 'closeCurrent', array('value' => $data));
        }
        
        $this->display('checkin', 'poi');
    }
    
    public function specific_placecategory($cid = 100,$cname = '楼盘') {
     	
    	$this->assign("cid",$cid);
    	$this->index($cid,$cname);
    }
    
    function poi_select_image(){
    	 $id = $this->get("id") ? $this->get("id") : $this->post("id");
    	 $rel = $this->get("rel");
    	 //var_dump($_POST);exit;
    	 
    	 
    	 if($this->is_post() && !$this->input->post("pageNum")/*&& $this->post("image")*/){ //有分页参数，说明是在分页了
    	 	$image   = $this->post("image");
    	 	$image_w = $this->post("image_w");
    	 	$image_h = $this->post("image_h");
    	 	$w		 = $this->post("w");
    	 	$h		 = $this->post("h");
    	 	$x1		 = $this->post("x1");
    	 	$y1		 = $this->post("y1");
    	 	$width   = $this->post("width");
    	 	$height  = $this->post("height");
    	 	
    	 	switch($rel){
            		case "poi_poi_detail":
            			$uri = "/poi/poi/detail/type/id/keywords/".$id;
            			break;
            		case "poi_poi_add":
            			$uri = $id ? "/poi/poi/edit/id/".$id."/fresh_type/get" : "/poi/poi/add/id/cid/fresh_type/get";
            			break;
            		case "poi_report_edit":
            			$uri = "/poi/report/edit/id/".$id."/fresh_type/get";
            			break;
            		case 'poi_poi_edit_checked':
            			$uri = "/poi/poi/edit_checked/id/".$id."/fresh_type/get";
            			break;
            }
    	 	
    	 	if(empty($image)){
    	 		//那就删了图片嘛！
    	 		$b = $this->db->where("id",$id)->set("background","")->update($this->_tables['place']);
    	 		$b ? $this->success("图片删除成功!",$rel,$uri,"closeCurrent") : $this->error("更新图片失败。");
    	 	}
    	 	
    		// 处理图片
            $dst_image = preview_imgarea($image, $image_w, $image_h, $w, $h, $x1, $y1, $width, $height);
            if(empty($dst_image)) {
               $this->error("处理图片失败了，请重新来过");
            }
            // 提交图片给接口处理
            $image = api_upload($dst_image, 'common','odp');
            if(empty($image)) {
               $this->error("图片提交给API处理失败");
            }
            
            
            if($id){
            	$b = $this->db->where("id",$id)->set("background",$image)->update($this->_tables['place']);
            	
            	$b ? $this->success("封面保存成功",$rel,$uri,"closeCurrent") : $this->error("修改封面失败，请稍后再试!");
            }
            else{
            	$data['code'] = 0;
            	$data['image'] = $image;
            	$data['uri'] = image_url($image,"common");
            	//echo $image;exit;
            	echo json_encode($data);
            	exit;
            }
           
            
    	 }
    	 if($id){
    	 	
    	 
    	 $place = $this->db->where("id",$id)->get($this->_tables['place'])->row_array(0);
    	 
    	 $pageNum = $this->input->post("pageNum") ? $this->input->post("pageNum") : 1;
    	 $numPerPage = $this->input->post("numPerPage") ? $this->input->post("numPerPage") : 20;
    	 $this->db->where(array("placeId"=>$id,'type in '=>"(2,4)",'photo is not ' => " null ",'photo != ' =>" 'NULL' ",'status <'=> 2),null,false);
    	 $total = $this->db->count_all_results($this->_tables['post']);
    	 $total ? $parr = $this->paginate($total) : "";
    	 //备选图标列表   查询POST中点评个YY的图片
    	 $image_list = $this->db->where(array("placeId"=>$id,'type in '=>"(2,4)",'photo is not ' => " null ",'photo != ' =>" 'NULL' ",'status <'=> 2),null,false)->order_by("createDate","desc")->limit($numPerPage,$numPerPage*($pageNum-1))->get($this->_tables['post'])->result_array();
    	}
    	 //var_dump($image_list);exit;
    	 $size_type = 'place';
    	 $this->assign("image",image_url($place['background'],"common","odp"));
    	 $this->assign("id",$id);
    	 $this->assign(compact('size_type', 'place','parr','image_list','pageNum','numPerPage'));
    	 $this->display('select_image','poi');
    }
}

// File end
