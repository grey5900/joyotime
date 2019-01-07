<?php
/**
 * 地点相关 
 * Create by 2012-12-17
 * @author liuweijava
 * @copyright Copyright(c) 2012-
 */
  
 // Define and include
if ( ! defined('BASEPATH')) exit('No direct script access allowed');   
   
 // Code
class Place extends Controller{
	
	var $_tables;
	
	public function __construct(){
		parent::__construct();
		$this->load->helper('cache');
		$this->load->model('post_model', 'm_post');
		$this->load->model('place_model', 'm_place');
		$this->load->model('placeowncategory_model', 'm_placeowncategory');
		$this->load->model('placeownspecialproperty_model', 'm_prop');
		
		$this->_tables = $this->config->item('tables');
	}
	
	/**
	 * 地点首页，TIMELINE
	 * Create by 2012-12-17
	 * @author liuweijava
	 * @param int $place_id
	 * @return array code:1=地点ID为空
	 */
	public function index($place_id, $sub_page=1, $page=1, $order='new'){
		
		$page = formatid($page , 1);
		$sub_page = formatid($sub_page , 1);
		$place_id = formatid($place_id);
		//地点ID为空
		empty($place_id) && $this->echo_json(array('code'=>1, 'msg'=>lang_message('place_empty_id')));
		//地点属性
		$info = get_data_ttl('place', $place_id, -1);
		if($info['status']>0){
			show_404();
		}
		//echo $info['placeCategoryId'];
		//var_dump($info);
	//	$info = $this->m_place->get_info($place_id);
		//TITLE,KEYWORD, DESCRIPTION
		//echo $order;
		$title = $this->lang->line('site_current_title').$info['placename'];
		$keywords = $this->lang->line('site_current_keyword').$info['placename'];
		$this->assign(compact('title', 'keywords'));
		//POST列表
		$list = array();
		//POST总数
		if($sub_page <= 5){
			$count_condition = array(
							'where'=>array('status <'=>2, 'type < '=>5, 'placeId'=>$place_id)
							);
			$count = $this->m_post->count_post($count_condition);//$info['tipCount'] + $info['photoCount']; 晕！居然直接这样。。还是统计一下吧！
			if($count){
				//分页
				$arr = array('place_id'=>$place_id, 'order_suffix'=>$order, 'sub_page'=>1);
				$parr = $this->paginate('/place', $count, $page, $arr, 50);
				if($page <= $parr['total_page']){
					//从新计算小分页
					$offset = 50 * ($page - 1) + 10 * ($sub_page - 1);
					$sub_page += 1;
					//列表
					$list = $this->m_post->get_post_timeline($place_id, $order, 10, $offset);
				}
				//划分成5个长度为10的小数组
			//	!empty($list) && $list = array_chunk($list, 10);
			}
		}
		
		empty($list) && $list = array();
		//最近5位访客
		$visitors = $this->m_place->get_visitors($place_id, 10);
		//最新5位会员
		//$members = $info['brandId'] ? $this->m_place->get_members($place_id, 10) : array();
		//幻灯，还不晓得从哪来，绑定的是什么碎片
		$project = array();
		//相关新闻, 5分钟更新一次
		$news = get_data_ttl('place_news', $place_id,300);
		//图片本地目录，预览要用
		$this->config->load('config_image');
		$upload_path = $this->config->item('image_cfg')['upload_view'];    	
    	//团购
    	//$this->load->model('grouponitematplace_model','m_groupon');
    	//$gs = $this->m_groupon->get_groupones($place_id, 1);
    	//$groupon = !empty($gs) ? $gs[0] : array();
    	//unset($gs);
    	
    	
    	$category_ids = $this->m_placeowncategory->list_by_placeid($info['id']);
    	
    	$cates = array();
    	foreach($category_ids as $row){
    		$cates[] = $row['placeCategoryId'];
    	}
    	
    	$is_loupan = 0;
    	//获取楼盘信息
    	if(in_array(100,$cates)){
    		$is_loupan = 1;
    		$array_loupan_key = array(
	    		28 => 'info',
	    		29 => 'imgs'
	    	);
	    	$loupan_info = array();
	    	$profile_id = array();
    		
    		foreach($info['profiles'] as $key=>$row){
    			switch($row['moduleId']){
    				case 29:
    					$limit = 2;
    				case 28:
    					$sql = "select * from PlaceModuleData where placeId = {$info['id']} and moduleId=".$row['moduleId'];
    					$limit ? $sql .= " limit ".$limit : "";
    					$loupan_info[$array_loupan_key[$row['moduleId']]] = $this->db->query($sql)->result_array($row['moduleId']);
    					$profile_id[$array_loupan_key[$row['moduleId']]] = $row['id'];
    					break;
    				default : break;
    			}
    		}
    		unset($key,$row);
    		if(!empty($loupan_info)){
	    		$loupan_imgs = array();
		    	//处理图片
		    	if(!empty($loupan_info['imgs'])){
		    		foreach($loupan_info['imgs'] as $key=>$row){
		    			if(count($loupan_imgs) >= 2) break;
		    			$tm_img = array();
		    			$images = decode_json($row['mValue']);
		    			foreach($images as $k=>$value){
		    				if(count($loupan_imgs) >= 2) break;
		    				!empty($value['image']) && $loupan_imgs[] = image_url($value['image'], 'common');//  array_merge($value['image'],$loupan_imgs);
		    			}
		    		}
		    		unset($key,$row);
		    	}
		    	$loupan_info = $loupan_info['info'];
		    	$loupan = array();
		    	foreach($loupan_info as $value){
		    		$loupan[$value['fieldId']] = $value;
		    	}
		    	
		    	// 获取模型字段
		        $this->load->model('placemodulefield_model', 'm_placemodulefield');
				$module_list = $this->m_placemodulefield->list_order_ordervalue(array('moduleId' => 28));
				$module_fields = array();
				foreach($module_list as $row){
					$module_fields[$row['fieldId']] = $row['fieldName'];
				}
				
				$display_fields = array(
							'v-develop',
							'v-address',
							'v-saleaddress',
							'v-housetype',
							'v-saletele',
							'v-avgprice',
							'v-ownarea',
							'v-biz',
							'v-towards',
							'v-indoor',
							'v-decorate',
							'v-starttime',
							'v-introduce'
				);
    		}
    	}
    	
    	$this->title = $info['placename'];
    	
		//页面显示
		$this->assign(compact('info', 'visitors', 'members', 'project', 'list', 'page', 'sub_page', 'order', 'news', 'upload_path', 'groupon' ,'loupan' ,'loupan_imgs' ,'module_fields','display_fields' ,'profile_id' ,'is_loupan'));
		$this->display('timeline');
	}
	
	/**
	 * 地点的扩展信息
	 * Create by 2012-12-18
	 * @author liuweijava
	 * @param int $place_id 地点ID
	 * @param int $property_id 属性ID号
	 */
	public function info($place_id, $property_id = 0){
		$place_id = formatid($place_id);
		$property_id = formatid($property_id);
		
		//地点属性
		$info = get_data('place', $place_id);
		//TITLE,KEYWORD, DESCRIPTION
		$title = $this->lang->line('site_current_title').$info['placename'];
		$keywords = $this->lang->line('site_current_keyword').$info['placename'];
		$this->assign(compact('title', 'keywords'));
		
		// 如果property_id小于零。那么获取第一个属性
		if($property_id <= 0 && $info['profiles']) {
			$property_id = $info['profiles'][0];
		}
		$property = $info['profiles'][$property_id];
		if(empty($property)) {
			$this->showmessage("错误的地点属性");
		}
		
		if($property['moduleId'] == -1) {
			// 直接跳转网页
			redirect($property['hyperLink']);
		}
		
		$list = array();
		//扩展模型
		$this->load->model('placemoduledata_model', 'm_placemoduledata');
		$ext_info = $this->m_placemoduledata->list(array('placeId'=>$place_id, 'isVisible'=>1, 'moduleId' => $property['moduleId']));
		$result = array();
		if ($ext_info) {
			$this->load->config('config_image');
	        foreach ($ext_info as $row) {
	            if($fields[$row['fieldId']]['fieldType'] == 'rich_image') {
	                // 对rich_image里面的元素排序
	                $m_value = json_decode($row['mValue'], true);
	                $rich_image_fields = $this->config->item('rich_image');
	                $value = $sort_field = array();
	                if($m_value) {
	                    foreach($rich_image_fields as $k => $val) {
	                        $sort_field[$k] = $val['key'];
	                    }
	                    foreach($m_value as $val) {
	                        $arr = array();
	                        foreach($sort_field as $k => $v) {
	                            $arr[$v] = $val[$v];
	                        }
	                        $value[] = $arr;
	                    }
	                }
	                $result[$row['fieldId']] = $value;
	            } else {
	                $result[$row['fieldId']] = $row['mValue'];
	            }
	        }
	        unset($ext_info, $fields);
	        
	        // 获取模型字段
	        $this->load->model('placemodulefield_model', 'm_placemodulefield');
			$module_fields = $this->m_placemodulefield->list_order_ordervalue(array('moduleId' => $property['moduleId']));
	        
	        $this->assign(array('list' => $result, 'module_fields' => $module_fields));
	    }
	    
	    $this->title = $info['placename'];
	    
		$this->assign(array('info' => $info, 'property' => $property));
		
		$this->display('info');
	}
	
	/**
	 * 图片墙
	 * Create by 2012-12-18
	 * @author liuweijava
	 * @param int $place_id
	 * @param int $page
	 */
	public function photo($place_id, $page=1){
		$page = formatid($page , 1);
		$place_id = formatid($place_id);
		//地点属性
		$info = get_data('place', $place_id);
		//TITLE,KEYWORD, DESCRIPTION
		$title = $this->lang->line('site_current_title').$info['placename'].'_图片墙';
		$keywords = $this->lang->line('site_current_keyword').$info['placename'];
		$this->assign(compact('title', 'keywords'));
		
		//查询条件
		$condition = array(
			//'where'=>array($this->_tables['post'].'.placeId'=>$place_id, $this->_tables['post'].'.type'=>3, $this->_tables['post'].'.status < '=>2),
			'where'=>array($this->_tables['post'].'.placeId'=>$place_id, $this->_tables['post'].'.photo != '=>'', $this->_tables['post'].'.type <= '=>4, $this->_tables['post'].'.status <'=>2), // status<2
			'order'=>array($this->_tables['post'].'.createDate', 'desc')
		);
		//总长度
		$count = $this->m_post->count_post($condition);
		if($count){
			//分页
			$parr = $this->paginate('/place_photo', $count, $page, array('place_id'=>$place_id), 10);
			$total_page = $parr['total_page'];
			if($page <= $total_page){
				//增加查询条件
				$condition['limit'] = array('size'=>$parr['size'], 'offset'=>$parr['offset']);
				$list = $this->m_post->list_post($condition, 'thweb');
			}
		}
		empty($list) && $list = array();
		$this->title = $info['placename'];
		$this->assign(compact('count', 'list', 'info'));
		$this->display('photo');
	}
	
	/**
	 * 访客列表
	 * Create by 2012-12-18
	 * @author liuweijava
	 * @param int $place_id
	 * @param int $page
	 */
	public function visitor($place_id, $page=1){
		$page = formatid($page , 1);		
		$place_id = formatid($place_id);
		//地点属性
		$info = get_data('place', $place_id);
		//TITLE,KEYWORD, DESCRIPTION
		$title = $this->lang->line('site_current_title').$info['placename'].'_访客';
		$keywords = $this->lang->line('site_current_keyword').$info['placename'];
		$this->assign(compact('title', 'keywords'));
		
		$list = array();
		//访客总数
		$count = $this->m_place->get_visitor_count($place_id);
		if($count){
			//分页
			$parr = $this->paginate('/place_visitor', $count, $page, array('place_id'=>$place_id), 40);
			//数据
			$list = $this->m_place->get_visitors($place_id, $parr['size'], $parr['offset']);
		}
		$this->title = $info['placename'];
		$this->assign(compact('count', 'list', 'info'));
		$this->display('visitor');
	}
	
	/**
	 * 收藏列表
	 * @param int $place_id
	 * @param int $page
	 */
	public function favorite($place_id, $page=1){
		$page = formatid($page , 1);		
		$place_id = formatid($place_id);
		//地点属性
		$info = get_data('place', $place_id);
		//TITLE,KEYWORD, DESCRIPTION
		$title = $this->lang->line('site_current_title')."收藏".$info['placename'].'的用户';
		$keywords = $this->lang->line('site_current_keyword').$info['placename'];
		$this->assign(compact('title', 'keywords'));
		
		$list = array();
		//收藏列表
		$count = $this->m_place->get_favorite_count($place_id);
		if($count){
			//分页
			$parr = $this->paginate('/place_favorite', $count, $page, array('place_id'=>$place_id), 40);
			//数据
			$list = $this->m_place->get_favorites($place_id, $parr['size'], $parr['offset']);
		}
		$this->title = $info['placename'];
		$this->assign(compact('count', 'list', 'info'));
		$this->display('visitor');
	}
	
	/**
	 * 会员列表
	 * Create by 2012-12-18
	 * @author liuweijava
	 * @param int $place_id
	 * @param int $page
	 */
	public function member($place_id, $page=1){
		$page = formatid($page , 1);
		$place_id = formatid($place_id);
		//地点属性
		$info = get_data('place', $place_id);
		//TITLE,KEYWORD, DESCRIPTION
		$title = $this->lang->line('site_current_title').$info['placename'].'_会员';
		$keywords = $this->lang->line('site_current_keyword').$info['placename'];
		$this->assign(compact('title', 'keywords'));
		
		$list = array();
		//会员总数
		$count = $this->m_place->get_member_count($place_id);
		if($count){
			//分页
			$parr = $this->paginate('/place_member', $count, $page, array('place_id'=>$place_id), 40);
			//数据
			$list = $this->m_place->get_members($place_id, $parr['size'], $parr['offset']);
		}
		$this->title = $info['placename'];

		$this->assign(compact('count', 'list', 'info'));
		$this->display('visitor');
	}
	
	/**
	 * 地点列表 
	 * Create by 2012-12-24
	 * @author liuweijava
	 * @param int $page
	 */
	public function search_place($page=1){
		$page = formatid($page , 1);
		
		$keyword = $this->get('keyword');
		empty($keyword) && $keyword = '';
		exit($keyword);
	}
	
	/**
	 * 添加新地点 
	 * Create by 2012-12-18
	 * @author liuweijava
	 */
	public function add_place(){
		
	}
	
}   
   
 // File end