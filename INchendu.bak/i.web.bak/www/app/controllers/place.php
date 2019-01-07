<?php
/**
 * 地点相关
 * Create by 2012-5-7
 * @author liuw
 * @copyright Copyright(c) 2012-2014 joyotime
 */

// Define and include
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
 
// Code
class Place extends MY_Controller{
	
	var $as_type;
    var $cates = array();
	
	function __construct(){
		parent::__construct();
		$this->as_type = $this->config->item('assert_type');
	}
	
	/**
	 * 地点时间轴
	 * Create by 2012-6-6
	 * @author liuw
	 * @param int $id
	 */
	public function index($id=0){
	//	!$id && $this->showmessage('非法操作', '/');
    	//地点属性
    	$info = $this->_get_place_info($id);
    	//title
    	$this->assign('site_title', $info['placename'].'的点评、照片 - '.$this->lang->line('site_title'));
    	$this->assign('site_keywords', $info['placename'].','.$this->lang->line('site_keywords'));
    	$this->assign('site_description', '成都' . $info['placename'] . ' 地址：' . $info['address'] . ' 电话：' . $info['tel'] . '；' . $info['description']);
    	//评分等级
    	$info['star'] = ceil($info['level']);
    	
    	$first_timeline = $this->_get_timeline($id);
    	$firs = explode('|', $first_timeline);
    	$this->assign('tag_first_show', $firs[1]);
    	$this->assign('tag_first_url', $firs[0]);
    	//查询最近一个月的POST
    	$list = $this->get_posts($id, $firs[0], 1);
    	$this->assign('firstes', $list);
    	
    	//优惠列表
    	$now = gmdate('Y-m-d H:i:s', time()+8*3600);
    	$where = array(
    		'PlaceOwnPrefer.placeId'=>$id,
    		'Preference.beginDate <= '=>$now,
    		'Preference.endDate > '=>$now,
    		'Preference.status'=>1
    	);
    	$query = $this->db->join('PlaceOwnPrefer', 'PlaceOwnPrefer.preferId=Preference.id', 'join')->where($where)->order_by('Preference.endDate', 'asc')->get('Preference')->result_array();
    	$preferences = array();
    	foreach($query as $row){
    		$row['thumbPhoto'] = image_url($row['thumbPhoto'], 'common', 'odp');
    		$row['middlePhoto'] = image_url($row['middlePhoto'], 'common', 'odp');
    		$preferences[$row['id']] = $row;
    	}
    	$this->assign('prefes', $preferences);
    	//end
    	
    	//会员卡
    	$this->db->select('BrandMemberCard.*, Brand.tel');
    	$this->db->join('Place', 'Place.brandId = BrandMemberCard.brandId', 'inner');
    	$this->db->join('Brand', 'Brand.id = BrandMemberCard.brandId', 'inner');
    	$card = $this->db->where(array('Place.id'=>$id, 'BrandMemberCard.status'=>1, 'Brand.status'=>0))->order_by('BrandMemberCard.createDate', 'desc')->limit(1)->get('BrandMemberCard')->first_row('array');
    	$card['image'] = $card['image'];
    	$this->assign('card', $card);
    	//end
    	
    	$cfg = $this->config->item('image_cfg');
		$this->assign('upload_path', $cfg['upload_view']);
		$this->assign('auth', $this->auth);
    	$this->assign('info', $info);
    	$this->assign('tip', $this->config->item('post_tip'));
    	$this->assign('photo', $this->config->item('post_photo'));
    	$this->assign('istimeline', true);
		$this->display('place');
	}
	
	/**
	 * 地点列表，默认显示全部地点，筛选条件使用post传递
	 * Create by 2012-6-20
	 * @author liuw
	 * @param int $page，当前页码
	 * @param int $cid，一级分类id
	 * @param int $s_cid，二级分类id
	 * @param int $star，评分星级
	 * @param string $pcc，人均消费
	 * @param int $group，是否有团购
	 * @param int $event，是否有活动
	 * @param int $prefer，是否有优惠
	 * @param int $order，排序规则
	 * @param string $keyword，搜索关键词
	 */
	public function listes($page=1, $cid=0, $s_cid=0, $star=0, $pcc='F', $group=0, $event=0, $prefer=0, $order='default', $keyword=''){
		$keyword = urldecode($keyword);
		if(strpos($keyword, '%')!==FALSE){
			$keyword = str_replace('%','(', $keyword).')';
			$keyword = trim($keyword);
		}
		//
		$this->cates = get_data('category');
		//分类图标
		foreach($this->cates as $cat_id=>$cat){
			if(!empty($cat['categoryIcon']))
				$this->cates[$cat_id]['c_icon'] = image_url($cat['categoryIcon'],'common','odp');
			else 
				$this->cates[$cat_id]['c_icon'] = '';
		}
		//分类
		$this->assign('cates', $this->cates);
		
		$this->assign('page_form', '/place/list/'.$page.'/');
		
		$f_cid = $cid;
		$uri_arr = compact('f_cid', 's_cid', 'star', 'pcc', 'group', 'event', 'prefer', 'order', 'keyword');
		
		$this->assign(compact('star', 'pcc', 'group', 'event', 'prefer', 'order', 'keyword'));
		//数据筛选
		$cid = $cid > 0 ? $cid:FALSE;
		
		$s_cid = $s_cid > 0 ? $s_cid:FALSE;
		// if($cid){
			// $cids[] = $cid;
			// $this->assign('f_cid', $cid);
		// }
// 		
		// if($s_cid){
			// $cids[] = $s_cid;
			// $this->assign('s_cid', $s_cid);
		// }
		
		if($s_cid) {
            $cids[] = $s_cid;
            $this->assign('s_cid', $s_cid);
		} elseif($cid) {
            $cids[] = $cid;
            $this->assign('f_cid', $cid);
		}
		
		if($cid) {
		    $this->assign('sel_cid', $cid);
		}
			
		$star = $star > 0 ? $star:FALSE;
		//查询总长
		$arr = array();
		$sql = 'SELECT COUNT(*) AS all_count FROM Place p LEFT JOIN PlaceOwnCategory poc ON poc.placeId=p.id LEFT JOIN PlaceCategory c ON c.id=poc.placeCategoryId WHERE 1 ';
		if(isset($cids) && !empty($cids)){
			$sql .='AND c.id IN (?) ';
			$arr[] = implode(',',$cids);
			//把分类和父分类查出来返回到页面
			$clist = array();
			$query = $this->db->select('id, content, level')->where_in('id', $cids)->get('PlaceCategory')->result_array();
			$this->cates = get_data('category');
			foreach($query as $row){
				$row['div_id'] = $row['level'] == 0 ? 'first':'second';
				$row['h_type'] = $row['level'] == 0 ? 'cid' : 's_cid';
				$clist[] = $row;
				if(isset($this->cates[$row['id']]))
					unset($this->cates[$row['id']]);
			}
			$this->assign('clist', $clist);
		}
		if($star != FALSE){
			$level = $star * 2;
			$sql .= 'AND p.level>=? ';
			$arr[] = $level;
			switch($star){
				case 1:$this->assign('str_star', '一星');break;
				case 2:$this->assign('str_star', '二星');break;
				case 3:$this->assign('str_star', '三星');break;
				case 4:$this->assign('str_star', '四星');break;
				case 5:$this->assign('str_star', '五星');break;
			}
		}
		if($pcc != 'F'){
			$pccs = explode('-', $pcc);
			if(intval($pccs[0]) <= 0){
				$arr[] = intval($pccs[1]);
				$sql .='AND p.pcc<=? ';
				$this->assign('str_pcc', $pccs[1].'元以下');
			}elseif(intval($pccs[1])<=0){
				$arr[] = intval($pccs[0]);
				$sql .= 'AND p.pcc>=? ';
				$this->assign('str_pcc', $pccs[0].'元以上');
			}else{
				$arr[] = intval($pccs[0]);
				$arr[] = intval($pccs[1]);
				$sql .= 'AND p.pcc BETWEEN ? AND ? ';
				$this->assign('str_pcc', $pccs[0].'-'.$pccs[1].'元');
			}
		}
		if($order != 'default'){
			switch($order){
				case 'level':$this->assign('str_order', '按星级排序');break;
				case 'pcc':$this->assign('str_order', '按人均消费排序');break;
				case 'tipCount':$this->assign('str_order', '按点评数排序');break;
				case 'pop':$this->assign('str_order', '按人气值排序');break;
			}
		}

		//有团购的
		if($event > 0){//有活动的			
			$sql .= 'AND p.eventCount > 0 ';
		}
		if($prefer > 0){//有优惠的
			$sql .= 'AND p.preferCount > 0 ';
		}
		//关键字搜索
		if(!empty($keyword))
			$sql .= 'AND p.status<=1 AND p.placename LIKE \'%'.$keyword.'%\' ';
		else
			$sql .= 'AND p.status = 0 ';
		$rs = $this->db->query($sql, $arr)->first_row('array');
		$count = $rs['all_count'];
		$this->assign('page', $page);
		
		if($count){
			//分页
			$parr = paginate('/place/list', $count, $page, $uri_arr, 20, 7, TRUE);
			//数据
			$list = array();
			$sql = 'SELECT p.*, c.icon AS c_icon FROM Place p LEFT JOIN PlaceOwnCategory poc ON poc.placeId=p.id LEFT JOIN PlaceCategory c ON c.id=poc.placeCategoryId WHERE 1 ';
			//筛选条件
			$sql .= isset($cids) && !empty($cids) ? 'AND c.id IN (?) ' : '';
			$sql .= $star > 0 ? 'AND p.level >=? ':'';		
			if($pcc !== 'F'){
				$pccs = explode('-', $pcc);
				if(intval($pccs[0]) <= 0){
					$sql .='AND p.pcc<=? ';
				}elseif(intval($pccs[1])<=0){
					$sql .= 'AND p.pcc>=? ';
				}else{
					$sql .= 'AND p.pcc BETWEEN ? AND ? ';
				}
			}
			//团购
			
			//活动 
			$sql .= $event > 0 ? 'AND p.eventCount>0 ':'';
			//优惠
			$sql .= $prefer > 0  ? 'AND p.preferCount>0 ':'';
			//关键字搜索
			if(!empty($keyword))
				$sql .= 'AND p.status<=1 AND p.placename LIKE \'%'.$keyword.'%\' ';
			else 
				$sql .= 'AND p.status = 0 ';
			//排序
			switch($order){
				case 'level'   : $sql .= 'ORDER BY p.level DESC ';break;
				case 'pcc'     : $sql .= 'ORDER BY p.pcc DESC ';break;
				case 'tipCount': $sql .= 'ORDER BY p.tipCount DESC ';break;
				case 'pop'     : $sql .= 'ORDER BY p.pop DESC ';break;
				default        : $sql .= 'ORDER BY p.pop DESC ';break;
			}
			$sql .= 'LIMIT ?, ?';
			$arr[] = $parr['offset'];
			$arr[] = $parr['per_page_num'];
			$query = $this->db->query($sql, $arr)->result_array();
			foreach($query as $row){
				$row['description'] = !empty($row['description']) && $row['description'] !== 'NULL' ? $row['description']:'';
				$row['tel'] = !empty($row['tel'])&&$row['tel']!=='NULL'?$row['tel']:'';
				//ICON
				$row['icon'] = !empty($row['icon'])?image_url($row['icon'], 'common', 'odp'):image_url($row['c_icon'], 'common', 'odp');
				//星级
				$row['star'] = ceil($row['level']);
				$row['level'] = $row['level'] >= 10 ? 10 : intval($row['level']);
				//访客
				// $sql = 'SELECT COUNT(*) AS visitorCount FROM (SELECT DISTINCT uid FROM Post WHERE placeId=? AND type=? GROUP BY uid) AS tmp';
				// $rs = $this->db->query($sql, array($row['id'], $this->config->item('post_checkin')))->first_row('array');
				// $row['v_count'] = $rs['visitorCount'];
                $row['v_count'] = $row['checkinCount'];
				$list[$row['id']] = $row;
			}
			$this->assign('list', $list);
		}
		
		//最新的十条地点纪录
		$news = array();
		$sql = 'SELECT p.*, c.icon AS c_icon FROM Place p LEFT JOIN PlaceOwnCategory poc ON poc.placeId=p.id LEFT JOIN PlaceCategory c ON c.id=poc.placeCategoryId WHERE p.status=0 ORDER BY p.id DESC LIMIT 5';
		$query = $this->db->query($sql)->result_array();
		foreach($query as $row){
			$row['icon'] = !empty($row['icon'])?image_url($row['icon'], 'common', 'odp'):image_url($row['c_icon'], 'common', 'odp');
			$news[] = $row;
		}
		$this->assign('news', $news);
		$this->display('list');
	}
	
	/**
	 * 返回指定分类的子分类
	 * Create by 2012-5-30
	 * @author liuw
	 * @param int $parent
	 */
	public function list_soncategory($parent){
		$this->cates = get_data('category');
		$cate = $this->cates[$parent];
		if(!empty($cate) && !empty($cate['child']))
			$this->echo_json(($cate['child']));
		else
			$this->echo_json((array()));
	}
	
	/**
	 * 获取指定分类的父分类
	 * Create by 2012-5-30
	 * @author liuw
	 * @param int $cid
	 */
	public function get_parent($cid){
		$rs = $this->db->where('child', $cid)->get('PlaceCategoryShip')->first_row('array');
		exit($rs['parent'].'');
	}

	/**
	 * 添加地点
	 * Create by 2012-5-7
	 * @author liuw
	 */
	public function add_place(){
		$this->is_login(true);
		if($this->is_post()){
			$code = 0;
			$msg = '';
			$refer = FALSE;
			if(!isset($this->auth) || empty($this->auth)){
				$msg = $this->lang->line('user_not_signin');
				$refer = '/signin';
			}else{
				//获取数据 
				$placename = $this->post('placename');
				$cates = $this->post('catelog');
				$address = $this->post('address');//地址
				$coordinate = $this->post('coordinate');//坐标，格式为[纬度:经度]
				$tel = $this->post('phone');
				$pcc = $this->post('average');
				$captcha = $this->post('verifyCode');
				//数据检查
				if(!isset($placename)||empty($placename)){
					$msg = $this->lang->line('place_add_name_empty');
				}elseif(!isset($cates)||empty($cates)){
					$msg = $this->lang->line('place_add_cate_empty');
				}elseif(!isset($address)||empty($address)){
					$msg = $this->lang->line('place_add_addr_empty');
				}elseif(!isset($coordinate)||empty($coordinate)){
					$msg = $this->lang->line('place_add_coordinate_empty');
				}/*elseif(!isset($tel)||empty($tel)){
					$msg = $this->lang->line('place_add_phone_empty');
				}elseif(!isset($pcc)||empty($pcc)){
					$msg = $this->lang->line('place_add_pcc_empty');
				}*/else{
					$location = explode(',',$coordinate);
					$latitude = $location[0];
					$longitude = $location[1];
					//格式化数据
					$data = compact('placename', 'address', 'latitude', 'longitude');
					!empty($pcc) && $data['pcc'] = $pcc;
					!empty($tel) && $data['tel'] = $tel;
					$data['status'] = 1;
					$data['creatorType'] = 2;
					$data['creatorId'] = $this->auth['id'];
					//保存 
					$this->db->insert('Place', $data);
					$id = $this->db->insert_id();
					if(!$id){
						$msg = $this->lang->line('do_error');
					}else{
						//关联地点类型
						$datas = array();
						foreach($cates as $key=>$catId){
							if($catId)
								$datas[] = array('placeId'=>$id, 'placeCategoryId'=>$catId);
						}
						if(!empty($datas))
							$this->db->insert_batch('PlaceOwnCategory', $datas);
						$code = 1;
						$msg = $this->lang->line('place_add_success');
						$refer = '/';
					}
				}
			}
			$this->echo_json((compact('code','msg','refer')));
		}else{
			//获取分类列表
			$cats = array();
			$results = $this->db->select('PlaceCategory.*, PlaceCategoryShip.parent')->join('PlaceCategoryShip', 'PlaceCategoryShip.child=PlaceCategory.id','inner')->where('PlaceCategoryShip.parent', 0, FALSE)->order_by('PlaceCategory.level', 'asc')->order_by('PlaceCategory.orderValue', 'desc')->order_by('PlaceCategory.id', 'asc')->get('PlaceCategory')->result_array();
			foreach($results as $row){
				$cats[$row['id']] = $row;
			}
			$this->assign('cats', $cats);
			$this->display('make');
		}
	}
	

    /**
     * 验证码图片
     */
    public function captcha() {
        $this->load->helper('valicode');
        $words = strtolower(random_string('alnum', 4));
        $this->session->set_userdata($this->config->item('sess_captcha'), md5($words));
        create_valicode($words);
    }
    
    public function check_captcha(){
    	if($this->is_post()){
    		$sess_captcha = $this->session->userdata($this->config->item('sess_captcha'));
    		$captcha = $this->post('captcha');
    		if(md5(strtolower($captcha)) != $sess_captcha)
    			$this->echo_json((array('code'=>0)));
    		else 
    			$this->echo_json((array('code'=>1)));
    	}
    }
    
    /**
     * 查询指定的地点分类
     * Create by 2012-5-8
     * @author liuw
     * @param int $parent
     */
    public function list_category($parent=0){
    	$query = $this->db->select('PlaceCategory.*')->join('PlaceCategoryShip', 'PlaceCategoryShip.child=PlaceCategory.id', 'inner')->where('PlaceCategoryShip.parent', $parent, FALSE)->get('PlaceCategory')->result_array();
    	$list = array();
    	foreach($query as $row){
    		$row['c_icon'] = !empty($row['categoryIcon']) ? image_url($row['categoryIcon'], 'common', 'odp') : '';
    		$list[$row['id']] = $row;
    	}
    	exit(json_encode($list));
    	$this->echo_json(($list));
    }
    
    /**
     * 地点图片墙
     * Create by 2012-5-18
     * @author liuw
     * @param int $place_id
     * @param int $page
     */
    public function photo($place_id = 0, $page=1){
    	//地点属性
    	$info = $this->_get_place_info($place_id);
    	$this->assign('site_title', $info['placename'].'的图片墙 - '.$this->lang->line('site_title'));
     	$this->assign('site_keywords', $info['placename'].','.$this->lang->line('site_keywords'));
    	$this->assign('site_description', '成都' . $info['placename'] . ' 地址：' . $info['address'] . ' 电话：' . $info['tel'] . '；' . $info['description']);
   
    	//先从缓存获取
    	$cache_id = 'cache_photo_'.$page;
    	//Comment by M2
    	//$list = get_recommend_cache($cache_id);
    	//if(empty($list)){
    	
		//查询总长度
			$count = $this->db->where(array('placeId'=>$place_id, 'status < '=>$this->config->item('post_close'), 'type'=>$this->config->item('post_photo')))->count_all_results('Post');
			if($count){
				//分页
				$url_arr = array('placeId'=>$place_id);
				$parr = paginate('/place_photo', $count, $page, $url_arr, 15);
				//数据
				$sql = 'SELECT p.*, if(u.nickname is not null and u.nickname != \'\', u.nickname, u.username) AS uname, u.avatar, u.isVerify AS u_isverify, pl.isVerify, pl.placename, '.
						'(SELECT COUNT(*) FROM UserFavorite WHERE uid=? AND itemType=p.type AND itemId=p.id) AS favorite '.
						'FROM Post p, User u, Place pl '.
						'WHERE u.id=p.uid AND pl.id=p.placeId AND pl.id=? AND p.status < ? AND p.type=\''.$this->config->item('post_photo').'\' ORDER BY p.createDate DESC LIMIT ?,?';
				$arr = array($this->auth['id'], $place_id, $this->config->item('post_close'), $parr['offset'], $parr['per_page_num']);
				$query = $this->db->query($sql, $arr)->result_array();
				$list = array();
				foreach($query as $row){
					$row['uname'] = get_my_desc($this->auth['id'], array('uid'=>$row['uid'], 'name'=>$row['uname']));
					//头像
					$row['avatar'] = image_url($row['avatar'], 'head', 'hhdp');
					//照片缩略图
					if(!empty($row['photoName'])){
						$row['photo'] = image_url($row['photoName'], 'user', 'thweb');
	                    $wh = image_wh($row['photoName']);
	                    $row['w'] = $wh['w'];
	                    $row['h'] = $wh['h'];
					}else{
						$row['photo'] = '';
					}
					//时间
					//$row['createDate'] = substr($row['createDate'],0, -3);/*gmdate('Y-m-d H:i', strtotime($row['createDate']));*/
					
	    			//是否已收藏 
	    			if(!empty($this->auth)){
	    				$favorite = $this->db->where(array('uid'=>$this->auth['id'],'itemId'=>$row['id'],'itemType'=>$row['type']))->count_all_results('UserFavorite');
	    				if($favorite > 0)
	    					$row['favorite'] = $favorite;
	    			}
	    			//是否已赞 
	    			if(!empty($this->auth)){
	    				$praise = $this->db->where(array('uid'=>$this->auth['id'],'itemId'=>$row['id'],'itemType'=>$row['type']))->count_all_results('UserPraise');
	    				if($praise > 0)
	    					$row['praise'] = $praise;
	    			}
					$list[$row['id']] = $row;
				}
				//更新缓存,5分钟自动更新
				set_recommend_cache($cache_id, $list, 3000);
			}
    	//Comment by M2
		//}
		$this->assign('list', $list);
    	$this->display('photo');
    }
    
    /**
     * 地点收藏纪录
     * Create by 2012-5-18
     * @author liuw
     * @param int $place_id
     * @param int $page
     */
    public function favorite($place_id, $page=1){
        // $this->is_login();
    	//地点属性
    	$info = $this->_get_place_info($place_id);
    	//title
    	$this->assign('site_title', $info['placename'].'的收藏 - '.$this->lang->line('site_title'));
    	$this->assign('site_keywords', $info['placename'].','.$this->lang->line('site_keywords'));
    	$this->assign('site_description', '成都' . $info['placename'] . ' 地址：' . $info['address'] . ' 电话：' . $info['tel'] . '；' . $info['description']);
    	
    	$count = $info['favoriteCount'];
    	if($count > 0){
    		//分页条
    		$parr = paginate('/place_favorite', $count, $page, array('placeId'=>$place_id), 30);
    		//数据
    		$sql = 'SELECT u.id, IF(u.nickname is not null AND u.nickname != \'\', u.nickname, u.username) AS uname, '.
    				'u.avatar, u.checkinCount, u.tipCount, u.photoCount, u.description, u.isVerify, '.
    				'(SELECT COUNT(*) FROM UserShip WHERE follower=? AND beFollower=u.id) AS has_follow '.
    				'FROM User u, UserFavorite uf WHERE uf.uid=u.id AND uf.itemId=? AND uf.itemType=? '.
    				'ORDER BY uf.createDate DESC LIMIT ?, ?';
    		$arr = array($this->auth['id'], $place_id, $this->as_type['place'], $parr['offset'], $parr['per_page_num']);
    		$query = $this->db->query($sql, $arr)->result_array();
    		$list = array();
    		foreach($query as $row){
    			$row['uname'] = get_my_desc($this->auth['id'], array('uid'=>$row['id'], 'name'=>$row['uname']));
    			//头像
    			$row['avatar'] = image_url($row['avatar'], 'head', 'hmdp');
    			$list[$row['id']] = $row;
    		}
    		$this->assign('list', $list);
    	}
    	
    	$this->display('favorite');
    }
    
    /**
     * 地点访客列表
     * Create by 2012-5-18
     * @author liuw
     * @param int $place_id
     * @param int $page
     */
    public function visitor($place_id, $page=1){
    	//地点属性
    	$info = $this->_get_place_info($place_id);
    	$this->assign('site_title', $info['placename'].'的访客 - '.$this->lang->line('site_title'));
    	$this->assign('site_keywords', $info['placename'].','.$this->lang->line('site_keywords'));
    	$this->assign('site_description', '成都' . $info['placename'] . ' 地址：' . $info['address'] . ' 电话：' . $info['tel'] . '；' . $info['description']);
    	
    	$count = $info['visitorCount'];
    	if($count){
    		//分页条
    		$parr = paginate('/place_visitor',$count, $page, array('place_id'=>$place_id), 30);
    		//数据列表
    		$list = array();
    		$sql = 'SELECT u.*,'.
    				'(SELECT COUNT(*) FROM UserShip WHERE follower=? AND beFollower=u.id) AS has_follow'.
    				' FROM User u, '.
    				'(SELECT uid, MAX(createDate) AS createDate FROM Post '.
    				'WHERE placeId=? AND type=? GROUP BY uid) AS v '.
    				'WHERE u.id=v.uid ORDER BY v.createDate DESC LIMIT ?, ?';
    		$arr = array($this->auth['id'], $place_id, $this->config->item('post_checkin'), $parr['offset'], $parr['per_page_num']);
    		$query = $this->db->query($sql, $arr)->result_array();
    		foreach($query as $row){
    			//头像
    			$row['avatar'] = image_url($row['avatar'], 'head', 'hmdp');
    			$row['uname'] = !empty($row['nickname']) ? $row['nickname'] : $row['username'];
    			$row['uname'] = get_my_desc($this->auth['id'],array('uid'=>$row['id'], 'name'=>$row['uname']));
    			$list[$row['id']] = $row;
    		}
    		
    		$this->assign('list', $list);
    	}
    	
    	$this->display('visitor');
    }
    
    /**
     * 地点详情，调取模板内容
     * Create by 2012-5-18
     * @author liuw
     * @param int $place_id
     */
    public function info($place_id){
    	//地点属性
    	$info = $this->_get_place_info($place_id);
    	$this->assign('info', $info);
    	//title
    	$this->assign('site_title', $info['placename'].'的收藏 - '.$this->lang->line('site_title'));
    	$this->assign('site_keywords', $info['placename'].','.$this->lang->line('site_keywords'));
    	$this->assign('site_description', '成都' . $info['placename'] . ' 地址：' . $info['address'] . ' 电话：' . $info['tel'] . '；' . $info['description']);

    	$list = array();
    	if(!empty($info['placeModule'])){
    		foreach($info['placeModule'] as $mod){
	    		//有模型的
	    		$url = format_msg($this->config->item('poi_url'), array('place_id'=>$place_id, 'module_id'=>$mod['id']));
	    		$curl = curl_init($url);
				curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($curl, CURLOPT_BINARYTRANSFER, true);
				$html = curl_exec($curl);
				$code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
				curl_close($curl);
				if($code == 200){
					$html = str_replace(array('<html>', '</html>'), array('',''), $html);
					$body = preg_replace("/.*?<body>(.*?)<\/body>/si", "$1", $html);
					$list[$mod['id']] = array('tab_sub'=>$mod['name'], 'content'=>$body);
				}
			}	
    	}
		$this->assign('list', $list);		
    	$this->display('info');    	
    }
    
    /**
     * 查询指定地点的团购信息，返回一个json数组，前台通过ajax调用该函数
     * Create by 2012-6-7
     * @author liuw
     * @param int $place_id
     */
    public function get_group($place_id){
    	
    	$this->assign('has_group', 0);
    	$this->display('group');
    }
    
    /**
     * 查询指定地点的优惠信息，返回一个json数组，前台通过ajax调用
     * Create by 2012-6-7
     * @author liuw
     * @param int $place_id
     */
    public function get_prefer($place_id){
    	
    	$this->assign('has_prefer', 0);
    	
    	$this->display('prefer');
    }
    
    /**
     * 查询指定地点的活动信息，返回一个json数组，前台通过ajax调用
     * Create by 2012-6-7
     * @author liuw
     * @param int $place_id
     */
    public function get_event($place_id){
    	
    	$this->assign('has_event', 0);
    	
    	$this->display('event');
    }
    
    /**
     * 查询指定地点的时间轴列表，返回一个json数组，前台通过ajax调用
     * Create by 2012-6-7
     * @author liuw
     * @param int $place_id
     */
    private function _get_timeline($place_id){
    	$sql = 'SELECT DISTINCT YEAR(DATE(createDate)) AS c_year, MONTH(DATE(createDate)) AS c_month FROM Post WHERE placeId=? AND status < 2 AND type IN (?,?) GROUP BY createDate ORDER BY createDate DESC';
    	$query = $this->db->query($sql, array($place_id, $this->config->item('post_tip'), $this->config->item('post_photo')))->result_array();
    	$list = array();
    	$anchors = array();//时间轴锚点列表
    	$index = 0;
    	foreach($query as $row){
    		$list[$row['c_year']]['months'][$row['c_month']] = format_month(intval($row['c_month']));
    		$anchors[$row['c_year'].'-'.($row['c_month'] < 10 ? '0'.$row['c_month']:$row['c_month'])] = $row['c_year'].'年'.($row['c_month'] < 10 ? '0'.$row['c_month']:$row['c_month']).'月';
    		if($index <= 0)
    			$list[$row['c_year']]['active'] = 1;
    		$index++;
    	}
    	$this->assign('list', $list);
    	$keys = array_keys($anchors);
    	$now = $keys[0];
    	$this->assign('now', $keys[0]);
    	unset($anchors[$keys[0]]);
    	$this->assign('anchors', $anchors);
    	//获得最近有内容的月份锚点，2.2需求
    //	$keys = array_keys($anchors);
    	return $now.'|'.$anchors[$now];
    }
    
    /**
     * 查询指定地点的商家公告，前台通过ajax调用
     * Create by 2012-6-7
     * @author liuw
     * @param int $place_id
     */
    public function get_notify($place_id){
    	$this->assign('has_notify', 0);
    	$this->assign('content', 'testtesttesttesttesttesttesttesttesttesttest');
    	$this->display('notify');
    }
    
    /**
     * 查询指定地点指定时间轴范围的post列表，返回一个json数组，前台通过ajax调用
     * Create by 2012-6-7
     * @author liuw
     * @param int $place_id
     * @param string $timeline
     */
    public function get_posts($place_id, $timeline = '', $not_show=0){
    	$this->assign('tip', $this->config->item('post_tip'));
    	$this->assign('photo', $this->config->item('post_photo'));
        $timeline || $timeline = date('Y-m');
    	//查询所有数据，页面js分页
    	$arr = array($place_id, $timeline.'%', $this->config->item('post_tip'),$this->config->item('post_photo'));
    	$sql = 'SELECT p.*,u.avatar, IF(u.nickname IS NOT NULL AND u.nickname != \'\',u.nickname, u.username) AS uname,u.description, u.isVerify AS u_isverify, pl.isBusiness, pl.isVerify FROM Post p INNER JOIN User u ON u.id=p.uid INNER JOIN Place pl ON pl.id=p.placeId WHERE p.placeId=? AND p.status<=1 AND p.createDate LIKE ? AND p.type IN (?,?) ORDER BY p.createDate DESC';
    	$query = $this->db->query($sql, $arr)->result_array();
    	$list = array();
    	$index = 1;
    	foreach($query as $row){
    		//使用我的备注替换好友的昵称
    		$row['uname'] = get_my_desc($this->auth['id'], array('uid'=>$row['uid'], 'name'=>$row['uname']));
    		//是否已收藏和已赞
    		if(!empty($this->auth)){
    			$fav = $this->db->where(array('uid'=>$this->auth['id'], 'itemId'=>$row['id'], 'itemType'=>$row['type']))->count_all_results('UserFavorite');
    			$has_fav = isset($fav)&&!empty($fav)?1:0;
    			$pra = $this->db->where(array('uid'=>$this->auth['id'], 'itemId'=>$row['id'], 'itemType'=>$row['type']))->count_all_results('UserPraise');
    			$has_praise = isset($pra)&&!empty($pra)?1:0;
    		}else 
    			$has_fav = $has_praise = 0;
    		$row['has_fav'] = $has_fav;
    		$row['has_praise'] = $has_praise;
    		//赞过的人,前3个
    		$sql = 'SELECT u.id, u.avatar, IF(u.nickname IS NOT NULL AND u.nickname != \'\',u.nickname,u.username) AS uname, u.isVerify FROM UserPraise p INNER JOIN User u ON u.id=p.uid WHERE p.itemId=? AND p.itemType=? ORDER BY p.createDate DESC LIMIT 3';
    		$q = $this->db->query($sql, array($row['id'], $row['type']))->result_array();
    		$praiser = array();
    		foreach($q as $r){
    			$r['uname'] = get_my_desc($this->auth['id'], array('uid'=>$r['id'],'name'=>$r['uname']));
    			//头像
    			$r['avatar'] = image_url($r['avatar'], 'head', 'hhdp');
    			$praiser[$r['id']] = $r;
    		}
    		$row['praiser'] = $praiser;
    		//全部回复
    		$sql = 'SELECT pr.*, u.avatar,IF(u.nickname IS NOT NULL AND u.nickname != \'\',u.nickname,u.username) AS uname, u.isVerify FROM PostReply pr INNER JOIN User u ON u.id=pr.uid WHERE pr.postId=? AND pr.status<=1 ORDER BY pr.createDate DESC LIMIT 2';
    		$q = $this->db->query($sql, array($row['id']))->result_array();
    		$replies = array();
    		foreach($q as $r){
    			$r['uname'] = get_my_desc($this->auth['id'], array('uid'=>$r['uid'],'name'=>$r['uname']));
    			//头像
    			$r['avatar'] = image_url($r['avatar'], 'head', 'hhdp');
    			//时间
    			//$r['createDate'] = substr($r['createDate'], 0, -3)/*gmdate('Y-m-d H:i', strtotime($r['createDate']))*/;
    			$replies[$r['id']] = $r;
    		}
    		$row['replies'] = array_reverse($replies, true);
    		//post发布者头像
    		$row['avatar'] = image_url($row['avatar'], 'head', 'hhdp');
    		$row['description'] = htmlspecialchars($row['description']);
	    	$row['content'] = nl2br(htmlspecialchars($row['content']));
    		//时间
    		//$row['createDate'] = substr($row['createDate'],0,-3);/*gmdate('Y-m-d H:i', strtotime($row['createDate']));*/
    		//区分左右
    		if($row['type'] == $this->config->item('post_tip')){//点评在左边
    			//星级
    			$row['star'] = ceil($row['level']);
    		}elseif($row['type'] == $this->config->item('post_photo')){//照片在右边
    			//照片
    			$row['photo'] = !empty($row['photoName']) ? image_url($row['photoName'], 'user', 'mdp') : '';
                if($row['photo']) {
                    $wh = image_wh($row['photoName'], 400);
                    $row['h'] = $wh['h'];
                    $row['w'] = $wh['w'];
                    // $row['w'] = $wh['w'];
                    // $row['h'] = $row['w']?intval($wh['h']*((float)390/$row['w'])):0;
                    // $row['w'] = 390;
                }
    		}
    		if($index == 1)
    			$row['add_a'] = 1;
    		$row['side'] = ($index++) % 2 == 1 ? 'left':'right';
    		$row['praise_t'] = $row['type'] == $this->config->item('post_tip')?'tip':'image';
    		
    		$list[$row['id']] = $row;
    	}
    	if($not_show){
    		return $list;
    	}else{
	    	$this->assign('list', $list);
	    	$this->display('post_list');
    	}
    }
    
    /**
     * 地点报错
     * Create by 2012-6-15
     * @author liuw
     * @param int $place_id
     */
    public function report($place_id){
    	//检查登录
    	$this->is_login();
    	
    	$uid = $this->auth['id'];
    	if($this->is_post()){
    		//表单数据
    		$type = $this->post('type');
    		//构造数据
    		$data = array(
    			'placeId'=>$place_id,
    			'uid'=>$uid,
    			'type'=>$type
    		);
    		//检查是否已提交过相同地点的相同类型错误
    	/*	$rs = $this->db->where(array('placeId'=>$place_id,'uid'=>$uid,'type'=>$type,'status'=>0))->count_all_results('PlaceErrorReport');
    		if($rs > 0){//已提交过相同地点的相同错误且机甲未处理,更新提交时间
    			$sql = 'UPDATE PlaceErrorReport SET createDate=CURRENT_TIMESTAMP WHERE placeId=? AND uid=? AND type=? AND status=0';
    			$this->db->query($sql, array($place_id, $uid, $type));    	
    			$code = 1;
    			$msg = $this->lang->line('report_success');
    		}else{//新提交数据 */
    			$cfg = $this->config->item('place_err');
    			$content = $cfg[$type];
    			switch($type){
    				case 0://其他错误
    					$content .= ':'.$this->post('content');
    					break;
    				case 3://地点信息有错
    					$pname = $this->post('place_name');
    					if(!empty($pname))
    						$data['placename'] = $pname;
    					$addr = $this->post('place_address');
    					if(!empty($addr))
    						$data['address'] = $addr;
    					break;
    				case 4://地点位置有错
    					$coods = explode(',',$this->post('coordinate'));
    					$data['latitude'] = floatval(trim($coods[0]));
    					$data['longitude'] = floatval(trim($coods[1]));
    					break;
    				default:
    					break;
    			}
    			$data['content'] = $content;
    			//提交报错信息
    			$this->db->insert('PlaceErrorReport', $data);
    			//更新地点状态
    			$this->db->where('id', $place_id)->update('Place', array('isConfirm'=>0));
    			$code = 1;
    			$msg = $this->lang->line('report_success');
    //		}
    		$this->echo_json(array('code'=>$code, 'msg'=>$msg));
    	}else{
    		//报错类型
    		$err_code = $this->config->item('place_err');
    	}
    }
    
    /**
     * 发布post
     * Create by 2012-6-19
     * @author liuw
     */
    public function do_post($place_id){
        $this->is_login();
    	if($this->is_post()){
    		$api_link = $this->config->item('api_serv').$this->config->item('api_folder');
    		$uid = $this->auth['id'];
  
    		$sync = $this->post('synchronous');
    		if(!empty($sync)){
    			$is_sync_to_sina = $this->auth['is_sync_sina'];
    			$is_sync_to_tencent = $this->auth['is_sync_tencent'];
    		}else{
    			$is_sync_to_sina = $is_sync_to_tencent = 0;
    		}
    		$code = 0;
    		$msg = '';
    		//POST类型
    		$post_type = $this->post('type');
    		$content = trim($this->post('content'));
            if(cstrlen($content) > 500) {
                $this->showmessage('内容不能超过500个字哦。');
            }
          	
            //敏感词检查
            $has_taboo = !empty($content) && check_taboo($content,'post');
            
            $attr = array();
            $attr['is_sync_to_sina'] = $is_sync_to_sina;
            $attr['is_sync_to_tencent'] = $is_sync_to_tencent;
    		switch($post_type){
    			case $this->config->item('post_tip')://点评
		    		$level = $this->post('score');
		    		$pcc = $this->post('pcc');
		    		//封装数据
		    		$params = array(
		    			'api'=>$this->lang->line('api_post_tip'),
		    			'uid'=>$uid,
		    			'has_return'=>TRUE
		    		);
		    		$attr['place_id'] = $place_id;
		    		if(empty($content)) {
		    		    $this->echo_json(array('code'=>0, 'msg'=>'点评内容不能为空'));
		    		}
		    		$attr['content'] = $content;
		    			
		    		if($level) {
		    		    $attr['level'] = floatval($level) * 2;
		    		}

		    		if($pcc) {
		    		    $attr['pcc'] = intval($pcc);
		    		}
		    		$params['attr'] = $attr;
		    		$result = json_decode($this->call_api($params), true);
		    		echo $result;
		    		if($result['result_code']==-1||in_array($result['result_code'],$this->config->item('bind_err'))){
    					$msg = $result['result_msg'];
    					//重新绑定
		    			$api_uri = $this->config->item('api_serv').$this->config->item('api_folder').'oauth/bind';
		    			$api_query = array('uid'=>$this->auth['id'],'is_redirect'=>1);
    					switch($result['result_code']){
    						case 4507:$api_query['stage_code']=1;break;//腾讯微博
    						default:$api_query['stage_code']=0;break;//新浪微博
    					}
    					$jump = $api_uri.'?'.http_build_query($api_query);
    					//保存绑定跳转地址
    					$this->session->set_flashdata('bind_jump', '/place/'.$place_id);
    					$this->showMessage($msg, $jump);
		    		}elseif($result['result_code'] > 0){
		    			// $code = 0;
		    			$msg = format_msg($this->lang->line('post_faild'), array('post_type'=>'点评'));
                        $this->showmessage($msg, '/place/' . $place_id);
    				}else{
		    			// $code = 1;
		    			if(!$has_taboo){
			    			$msg = format_msg($this->lang->line('post_success'), array('post_type'=>'点评'));
		    			}else{
		    				$place = $this->db->where('id', $place_id)->get('Place')->first_row('array');
		    				$msg = format_msg($this->lang->line('post_taboo'), array('place'=>$place['placename'], 'post_type'=>'点评'));
		    			}
	                    $this->showmessage($msg, '/place/' . $place_id, 3);
		    		}	
    				// $this->echo_json(array('code'=>$code, 'msg'=>$msg));	    		
    				break;
    			case $this->config->item('post_photo')://照片
    				$file = $_FILES['photo'];
    				if(empty($file['tmp_name'])) {
    				    $this->showmessage('请选择要上传的图片');
    				}
    				//先把图片保存到本地
			    	$cfg = $this->config->item('image_cfg');
			    	$upload_path = $cfg['upload_path'];
					$targetPath = $upload_path;
					$targetFile = rtrim($targetPath,'/') . '/' . $file['name'];
					move_uploaded_file($file['tmp_name'],$targetFile);
					
    				//封装数据
    				$params = array(
    					'api'=>$this->lang->line('api_post_photo'),
    					'has_return'=>TRUE,
    					'uid'=>$uid
    				);
                    
    				$attr['uploaded_file'] = '@'.$targetFile;
    				$attr['place_id'] = $place_id;
    				$attr['content'] = $content;
    				$params['attr'] = $attr;
    				$result = json_decode($this->call_api($params), true);
                    
    				if($result['result_code']==-1||in_array($result['result_code'],$this->config->item('bind_err'))){
    					$msg = $result['result_msg'];
    					//重新绑定
		    			$api_uri = $this->config->item('api_serv').$this->config->item('api_folder').'oauth/bind';
		    			$api_query = array('uid'=>$this->auth['id'],'is_redirect'=>1);
    					switch($result['result_code']){
    						case 4507:$api_query['stage_code']=1;break;//腾讯微博
    						default:$api_query['stage_code']=0;break;//新浪微博
    					}
    					$jump = $api_uri.'?'.http_build_query($api_query);
    					//保存绑定跳转地址
    					$this->session->set_flashdata('bind_jump', '/place/'.$place_id);
    					$this->showMessage($msg, $jump);
		    		}elseif($result['result_code'] != 0){
		    			// $code = 0;
		    			$msg = format_msg($this->lang->line('post_faild'), array('post_type'=>'图片'));
                        $this->showmessage(msg,'/place/' . $place_id);
    				}else{
		    			// $code = 1;
		    			if(!$has_taboo){
		    				$msg = format_msg($this->lang->line('post_success'), array('post_type'=>'照片'));
		    			}else{
		    				$place = $this->db->where('id', $place_id)->get('Place')->first_row('array');
		    				$msg = format_msg($this->lang->line('post_taboo'), array('place'=>$place['placename'], 'post_type'=>'图片'));
		    			}
                        $this->showmessage($msg, '/place/' . $place_id, 3);
                    }
    				// $this->echo_json(array('code'=>$code, 'msg'=>$msg));
    				break;
    		}
    	}
    }
    
    /**
     * 查询地点的信息
     * Create by 2012-5-18
     * @author liuw
     * @param int $place_id
     */
    private function _get_place_info($place_id){
    	$sql = 'SELECT p.*, c.icon AS c_icon FROM Place p LEFT JOIN PlaceOwnCategory oc ON oc.placeId=p.id LEFT JOIN PlaceCategory c ON c.id=oc.placeCategoryId WHERE p.id=? ORDER BY c.isBrand DESC,p.createDate DESC';
    	$info = $this->db->query($sql, array($place_id))->first_row('array');
   // 	$info = $this->db->where('id', $place_id)->get('Place')->first_row('array');
        if(empty($info)) {
            $this->showmessage('错误的地点信息，请访问正确的地点');
        }
		//图标有问题，逻辑问题，所以没改这块，默认图标写到了image_url，逻辑应该是和上头的icon一样的！！
		$info['icon'] = empty($info['icon']) ? image_url($info['c_icon'], 'common', 'odp') : image_url($info['icon'], 'common','odp');
    	//图标
    //	$sql = 'SELECT c.icon,c.categoryIcon FROM PlaceCategory c, PlaceOwnCategory oc WHERE oc.placeCategoryId=c.id AND oc.placeid=? LIMIT 1';
    //	$q = $this->db->query($sql, array($place_id))->first_row('array');
    	// $info['icon'] = !empty($q['icon']) ? image_url($q['icon'], 'common', 'odp') : (!empty($q['categoryIcon'])?image_url($q['categoryIcon'],'common','odp'):'');
    //	$info['icon'] = empty($info['icon'])?(image_url($q['icon'], 'common', 'odp')):image_url($info['icon'], 'common', 'odp');
        //统计访客数
    	$sql = 'SELECT COUNT(*) AS visitorCount FROM (SELECT DISTINCT uid FROM Post WHERE placeId=? AND type=? GROUP BY uid) AS tmp';
    	$rs = $this->db->query($sql, array($place_id, $this->config->item('post_checkin')))->first_row('array');
    	$info['visitorCount'] = !empty($rs) && !empty($rs['visitorCount']) ? $rs['visitorCount'] : 0;
    	unset($rs);
    	//统计收藏数
    	$sql = 'SELECT COUNT(*) AS favoriteCount FROM UserFavorite WHERE itemType='.$this->as_type['place'].' AND itemId=?';
    	$rs = $this->db->query($sql, array($place_id))->first_row('array');
    	$info['favoriteCount'] = !empty($rs) && !empty($rs['favoriteCount']) ? $rs['favoriteCount'] : 0;
    	unset($rs);
    	//是否已收藏过
    	$sql = 'SELECT COUNT(*) AS has_favorite FROM UserFavorite WHERE itemType=? AND itemId=? AND uid=?';
    	$arr = array($this->as_type['place'], $place_id, $this->auth['id']);
    	$rs = $this->db->query($sql, $arr)->first_row('array');
    	$this->assign('has_favorite', $rs['has_favorite']);
    	//地主 
    	$sql = 'SELECT u.*, ul.level FROM User u, UserLevelConstans ul WHERE ul.minExp <= u.exp AND ul.maxExp > u.exp AND id=?';
    	$mayor = $this->db->query($sql, array($info[mayorId]))->first_row('array');
    	if(!empty($mayor)){
    		//用户头像
    		$mayor['avatar'] = image_url($mayor['avatar'], 'head', 'hhdp');
    		$mayor['uname'] = !empty($mayor['nickname']) ? $mayor['nickname'] : $mayor['username'];
    		$mayor['uname'] = get_my_desc($this->auth['id'], array('uid'=>$mayor['id'], 'name'=>$mayor['uname']));
    		$info['mayor'] = $mayor;
    	}
    	//模型
    	$sql = 'SELECT pm.* FROM PlaceModule pm INNER JOIN PlaceOwnModule pom ON pom.placeModuleId=pm.id WHERE pom.placeId=? ORDER BY pom.rankOrder ASC';
    	$query = $this->db->query($sql, array($info['id']))->result_array();
    	$ms = array();
    	foreach($query as $row){
    		$ms[] = $row;
    	}
    	$info['placeModule'] = $ms;
    	//是否有模型
    	$info['has_module'] = !empty($ms);
    	$this->assign('info', $info);
    	return $info;
    }

}
 
// File end