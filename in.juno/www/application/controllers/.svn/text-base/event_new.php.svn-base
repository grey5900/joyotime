<?php
if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
/**
 * 活动
 * Author: piglet chenglin.zhu@gmail.com
 * Created on 2012-12-26
 */

class Event_New extends Controller
{
	var $_tables;
	function __construct()
	{
		parent::__construct ();
		
		//         $this->event_cid = $this->config->item('event_cid');// 获取当前频道的信息
		$newscategory = get_inc ( 'newscategory' );
		$this->categories = $newscategory;
		//         $this->category = $newscategory[$this->event_cid];
		$this->load->helper ( 'channel_helper' );
		$this->load->helper ( 'fragment_helper' );
		$this->load->model ( 'webevent_model', 'm_webevent' );
		$this->load->model ( 'webeventapply_model', 'm_webeventapply' );
		$this->load->model ( 'webeventownplace_model', 'm_webeventownplace' );
		$this->load->model ( 'post_model', 'm_post' );
		$this->load->model ( 'webnewscategory_model', 'm_webnewscategory' );
		$this->load->model ( 'vote_model', 'm_vote' );
		
		$this->config->load ( 'config_tables' );
		$this->_tables = $this->config->item ( 'tables' );
		$domains = get_inc ( 'domains' );
		// 当前HOST的关联频道栏目ID
		$this->domain = $domains [HOST];
		$this->id = $this->domain ['id'];
		$this->channel = $newscategory [$this->id];
		
		$this->lat = $this->get ( 'lat' );
		$this->lng = $this->get ( 'lng' );
		if ($this->lat && $this->lng)
		{
			$this->assign ( array ('lat' => $this->lat, 'lng' => $this->lng) );
		}
		
		// 处理文明测评活动特殊需求
		$this->special = array(
			'id' => 110,
			'tag_id' => 3540,
			'place_cid' => 124
		);
	}
	
	function index($catid = 0, $page = 1)
	{
		
		//         $fragment_data = get_fragment_by_cid($this->event_cid);
		//         $fragments = get_inc('fragment');
		$this->load->model ( 'webrecommendfragment_model', 'm_webrecommendfragment' );
		$curr_cate = $this->categories [$catid];
		// 获取幻灯部分
		//         $slider = array();
		//         foreach($fragment_data as $fid => $row) {
		//             if($fragments[$fid]['orderValue'] < 0) {
		//                 foreach($row as $k => $v) {
		//                     $slider[$k] = $v;
		//                 }
		//             }
		//         }
		//         $this->assign('slider', $slider);
		

		$page = formatid ( $page );
		$page = $page ? $page : 1;
		$page_size = 10;
		// 获取列表信息
		//取活动方式变化，只取出正在进行中的活动 和 3 条已经结束的活动
		$this->db->where_in ( 'type', array (0, 2 ) );
		//$this->db->where("( endDate > '".date('Y-m-d H:i:s',TIMESTAMP)."' and startDate <='".date('Y-m-d H:i:s',TIMESTAMP)."' )",null,false);
		
		$catid && $this->db->where( 'newsCatId' , $catid);
		$count = $this->m_webevent->count ( array ('status' => 0/*, 'newsCatId' => $catid*/ ) );
		$paginate = $this->paginate ( '/event_new/' . $catid, $count, $page, false, $page_size );
		$this->db->select('* , endDate < '."'".date('Y-m-d H:i:s',TIMESTAMP)."'".' as is_expired ');
		
		//$this->db->where("( endDate > '".date('Y-m-d H:i:s',TIMESTAMP)."' and startDate <='".date('Y-m-d H:i:s',TIMESTAMP)."' )",null,false);
		
		$this->db->where_in ( 'type', array (0, 2 ) );
		$this->db->order_by ( 'is_expired ' , 'asc' );
		$where =  array ('status' => 0);
		$catid && $where['newsCatId'] = $catid;
		$list = $this->m_webevent->list_order_rankorder_desc ($where, $paginate ['size'], $paginate ['offset'] );
		
		foreach ( $list as &$row )
		{
			$row ['is_expired'] = (TIMESTAMP > strtotime ( $row ['endDate'] ));
			$row ['is_begin'] = (TIMESTAMP > strtotime ( $row ['startDate'] ));
			$row ['intro'] = decode_json ( $row ['intro'] );
			$row ['startDate'] = idate_format ( strtotime ( $row ['startDate'] ), 'Y.m.d' );
			$row ['endDate'] = idate_format ( strtotime ( $row ['endDate'] ), 'Y.m.d' );
			$row ['applyProperty'] = decode_json ( $row ['applyProperty'] );
		}
		unset ( $row );
		
		$theme = $this->channel ['style'];
		$segment = $this->uri->rsegments;
		$this->assign ( array ('sub_cate' => get_channel_category ( $this->domain, $this->categories ), 'lil_cate' => get_channel_category ( $this->domain, $this->categories, 0 ), 'theme' => $theme ? $theme : 'default', 'id' => $catid ) );
		
		$fragments = $this->categories [$this->channel ['id']] ['fragmentId'];
		//var_dump($fragments);
		

		$fragments_data_list = $this->m_webrecommendfragment->list_in_fid_order_ordervalue_desc ( explode ( ",", $fragments ) );
		
		foreach ( $fragments_data_list as $row )
		{
			
			if (! $row ['fid'])
				continue;
			$tmp_data = get_data ( "fragmentdata", $row ['fid'] );
			//是否有头条或者幻灯
			if ($tmp_data ['frag'] ['fregType'] == 1)
			{
				$tmp_arr ['frag'] = $tmp_data ['frag'];
				$tmp_arr ['data'] = get_active_source ( $tmp_data ['frag'] ['dataSource'] ); //decode_json(curl_get_contents($tmp_data['frag']['dataSource']));//        		
				$sidebar_data [] = $tmp_arr;
			} else
			{
				$sidebar_data [] = $tmp_data;
			}
		}
		//var_dump($sidebar_data);
		/*$this->db->where("endDate < '".date('Y-m-d H:i:s',TIMESTAMP)."'",null,false);
		$this->db->order_by('endDate desc,rankOrder desc,hits desc');
		$expired_event = $this->db->limit(3)->get($this->_tables['webevent'])->result_array();*/
		//右侧取10个商品
		$this->db->where('status',0);
		$this->db->where("( endDate > '".date('Y-m-d H:i:s',TIMESTAMP)."' and startDate <='".date('Y-m-d H:i:s',TIMESTAMP)."' )",null,false);
		$products = $this->db->limit(8)->order_by('rankOrder','desc')->get($this->_tables['product'])->result_array();

		$this->assign ( array ('list' => $list, 'sidebar_data' => $sidebar_data ,'products' => $products ) );
		$this->display ( 'index3.0' );
	}
	
	/**
	 * 活动详情
	 * @param int $id
	 * @param int $sub_page
	 * @param int $page
	 * @param int $path 空的话就是WEB连接  有值的话是移动连接
	 */
	function detail($id, $sub_page = 1, $page = 1, $path = '' ,$vote_option_limit = 0)
	{
		$id = formatid ( $id );
		$sub_page = formatid($sub_page , 1);
		$page = formatid($page , 1);
		// 获取活动内容
		$event = $this->m_webevent->select_by_id ( $id );
		if (empty ( $event ))
		{
			$this->showmessage ( '访问活动出错啦' );
			return;
		}
		
		// 点击数+1
		$this->m_webevent->hits ( $id );
		
		// 如果活动是跳转到其他页面
		($event ['link'] && strpos ( $event ['link'], $this->config->item ( 'in_domain' ) . '/event_new/' ) === false) && redirect ( $event ['link'], '', REDIRECT_CODE );
		
		// 判断是否手机端过来的
		if (empty ( $path ) && is_mobile ())
		{
			//redirect ( sprintf ( 'http://%s/event_new/m_index/%s?uid=%s&lat=%s&lng=%s', $this->in_host, $id, $this->get ( 'uid' ), $this->get ( 'lat' ), $this->get ( 'lng' ) ), '', REDIRECT_CODE );
// 			redirect ( sprintf ( 'http://%s/event_new/m_index/%s?uid=%s', $this->in_host, $id, $this->get ( 'uid' ) ), '', REDIRECT_CODE );
			$this->m_index($id, $this->get('uid'));
		}
		
		$event ['banner'] = image_url ( $event ['image'], 'common', 'udp' );
		// 活动时间
		$is_expired = (TIMESTAMP > strtotime ( $event ['endDate'] ));
		$is_begin = (TIMESTAMP > strtotime ( $event ['startDate'] ));
		$event ['startDate'] = idate_format ( strtotime ( $event ['startDate'] ), 'Y.m.d' );
		$event ['endDate'] = idate_format ( strtotime ( $event ['endDate'] ), 'Y.m.d' );
		// 简介
		$event ['intro'] = decode_json ( $event ['intro'] );
		
		//fc_lamp：此方式已不要
		//if($event['applyType'] == 1) {
		//    $event['properties'] = decode_json($event['applyProperty']);
		//}
		

		// 获取年月
		$date = idate_format ( 0, 'Y-m-d' );
		$year_month = substr ( $date, 0, - 3 );
		$day = substr ( $date, - 2 );
		
		// 获取活动地点
		$place_list = $this->m_webeventownplace->list_by_eventid ( $id );
		$markers = $places = $pids = array ();
		foreach ( $place_list as $row )
		{
			$place = get_data ( 'place', $row ['placeId'] );
			$markers [] = $place ['longitude'] . ',' . $place ['latitude'];
			$places [] = array ('id' => $row ['placeId'], 'longitude' => $place ['longitude'], 'latitude' => $place ['latitude'], 'placename' => $place ['placename'], 'address' => $place ['address'] );
			$pids [] = $row ['placeId'];
		}
		
		count ( $pids ) >= 1 && $is_selcted_place = true;
		
		//如果活动关联地点是空，去关联的频道栏目关联的placeID取
		//fc_lamp：可为空
		/*
        if(empty($places)){
        	$newsCatId = $event['newsCatId'];
        	if($newsCatId){
	        	$f_cate = $this->categories[$newsCatId];
	        	$placeid = $f_cate['placeId'] ? $f_cate['placeId'] : $this->categories[$f_cate['parentId']]['placeId'] ;
	        	
	        	if($placeid){
		        	$place = get_data('place', $placeid);
		        	$markers[] = $place['longitude'] . ',' . $place['latitude'];
		            $places[] = array('id' => $place['id'], 'longitude' => $place['longitude'], 'latitude' => $place['latitude'], 'placename' => $place['placename'], 'address' => $place['address']);
		            $pids[] = $place['id'];
	        	}
        	}
        }*/
		
		//fc_lamp:处理报名方式
		$apply_property = json_decode ( $event ['applyProperty'], true );
		$event ['apply_form'] = ! empty ( $apply_property ['form'] ) ? $apply_property ['form'] : False;
		$event ['apply_tags'] = False;
		$where_tagIds = array ();
		if (! empty ( $apply_property ['keyword'] ))
		{
			$event ['apply_tags'] = tags_to_array ( $apply_property ['keyword'] );
			$event ['default_tags'] = reset ( $event ['apply_tags'] );
			
			//获取出标签ID
			$tags = '';
			foreach ( $event ['apply_tags'] as $v )
			{
				$tags .= "'$v',";
			}
			$tags = trim ( $tags, ',' );
			/*$tagIDs = $this->db->where ( "content in ($tags)" )->order_by ( "id", "desc" )->get ( "Tag" )->result_array ();
			if (! empty ( $tagIDs ))
			{
				foreach ( $tagIDs as $tag )
				{
					$where_tagIds [$tag ['id']] = $tag ['id'];
				}
			}*/
			$where_tagIds = $apply_property ['tagids'];
		}
		
		// 自己是否已经报名 
		if ($this->auth)
		{
			$is_apply = $this->m_webeventapply->select ( array ('uid' => $this->auth ['uid'], 'eventId' => $id ) );
			//fc_lamp：是否已填报名表
			if (empty ( $is_apply ['signInfo'] ))
			{
				$is_apply = False;
			} else
			{
				$is_apply = True;
			}
		}
		
		unset ( $event ['applyProperty'], $apply_property );
		
		// 查询随机几个活动。出来
		$relation_event = get_data_ttl ( 'relation_event', $id, 600, 'event_detail' );
		
		//POST列表,先只查询关联的地点的POST 还有关键字的 
		$feeds = $condition = array ();
		//文明测评
		
		if ($id == $this->special['id']) {
			$condition['order'] = array($this->_tables['post'].'.photo', 'desc');
			//$special_order = '';
		}
		if(/*!empty($pids) &&*/ $sub_page <= 5)
		{
			$table = $this->_tables ['post'];
			
			//基本条件
			$where = 'a.`type`<5 and a.`status` <2';
				
			$this->load->model ( 'post_model', 'm_post' );
			//组合条件
			$count =0;
			//既有地点，又有关键字
			if(!empty($pids) and !empty($where_tagIds))
			{
				$pids = implode(',',$pids);	
				$where_tagIds = implode(',',$where_tagIds);
				$sql = "select count(a.id) as num from `$table` a where a.`placeId` in($pids) and $where";
				$sql .= " UNION  select count(a.id) as num from `$table` a,`PostOwnTag` b where a.id=b.postId and $where and b.tagId in($where_tagIds)";
				$tpl = $this->db->query( $sql )->result_array();
				foreach ($tpl as $num)
				{
					$count += $num['num'];
				}
				$limit = $this->list_page($id,$count,$page,$sub_page);
				
				$sql = "(select a.* from `$table` a where a.`placeId` in($pids) and $where)";
				$sql .= " UNION (select a.* from `$table` a,`PostOwnTag` b where a.id=b.postId and $where and b.tagId in($where_tagIds)) ";
				$sql .= ($id == $this->special['id']) ? "order by `photo` desc" : "order by id desc";
				$sql .= " limit {$limit['offset']},{$limit['size']}";				
 				//echo $sql;
				$feeds = $this->db->query( $sql )->result_array();
				$feeds = $this->m_post->list_post(array(),'thweb',false,$feeds);
			
			//仅地点
			}elseif(!empty($pids))
			{
				
		        $condition['where_in'] = array(
		        	'column' => $table.'.placeId',
		        	'in' => $pids
		        );
		        $where = 'Post.`type`<5 and Post.`status` <2';
		        /*$condition['where'] = array(
		        	$table.'.`type`'=>'<5',
		        	$table.'.`status`' => '<2'
		        );*/
		        $this->db->where($where,null,false);
				$count = $this->m_post->count_post ( $condition );				
				$limit = $this->list_page($id,$count,$page,$sub_page);
				$condition['limit'] = $limit;
				$this->db->where($where,null,false);
				$feeds = $this->m_post->list_post($condition,'thweb');
				
				
		    //仅关键字   
			}elseif(!empty($where_tagIds))
			{
				if($id == $this->special['id']){ // 文明测评数据太多 ,只展示1000好了？
					$count = 1000;
				}else{
				
					$sql = "select count(a.id) as num from `$table` a,`PostOwnTag` b where a.id=b.postId and $where and b.tagId in(".implode(',',$where_tagIds).")";
						
					$tpl = $this->db->query( $sql )->row_array(0);
						
						/*foreach ($tpl as $num)
						{
							$count += $num['num'];
						}*/
					$count = $tpl['num'];
				}
				
				if($count){
				$limit = $this->list_page($id,$count,$page,$sub_page);
				}
				
				$feeds = array();
				if($count){
					//$sql = "select a.* from `$table` a,`PostOwnTag` b where a.id=b.postId and $where and b.tagId in(".implode(',',$where_tagIds).") order by id DESC limit {$limit['offset']},{$limit['size']}";
					$xcache_sql_key = "cache_wenming_{$limit['offset']}_{$limit['size']}";
					$feeds = xcache_get($xcache_sql_key);
					if(!$feeds){
						
						if($limit['size'] == 1){
							$sql = "select a.* from `$table` a,`PostOwnTag` b where a.id=b.postId and $where and b.tagId in(".implode(',',$where_tagIds).") and length(a.photo)>0 order by id desc";
							$sql .= " limit {$limit['offset']},{$limit['size']}";	
						}
						else{
							($id == $this->special['id']) && $where .= " and length(photo) >0";
							$sql = "select a.* from `$table` a,`PostOwnTag` b where a.id=b.postId and $where and b.tagId in(".implode(',',$where_tagIds).") ";
							$sql .= /*($id == $this->special['id']) ? "order by length(photo) >0 desc ,id desc " :*/ "order by id desc ";
							$sql .= " limit {$limit['offset']},{$limit['size']}";	
						}
						$feeds = $this->db->query( $sql )->result_array();
						$feeds = $feeds ? $this->m_post->list_post(array(),'thweb',false,$feeds) : array();
						xcache_set($xcache_sql_key,$feeds,600);
						
						
					}
				}
						
				
			}
			unset ( $pids );
		}
		
		// 获取报名人数
		$apply_count = $this->m_webeventapply->count_by_eventid ( $id );
		
		//获取投票模块
		$vote = $this->m_vote->get_vote(5,$id,$vote_option_limit,$path);
		
		$this->title = $event ['subject'];
		
		$this->assign ( array (
			'event' => $event, 
			'year_month' => $year_month, 
			'day' => $day, 
			'relation_event' => $relation_event, 
			'markers' => implode ( '|', $markers ), 
			'places' => $places, 
			'feeds' => $feeds, 
			'sub_page' => $sub_page, 'page' => $page, 
			'apply_count' => $apply_count, 'is_apply' => $is_apply, 
			'is_expired' => $is_expired, 'is_begin' => $is_begin,
			'vote' => $vote ) 
		);
		
		// 处理文明测评活动特殊需求
		if ($id == $this->special['id']) {
			// 查询参与人次
			$join_row = $this->db->query(sprintf("SELECT COUNT(postId) AS num 
					FROM PostOwnTag WHERE tagId='%s'", $this->special['tag_id']))->row_array();
			
			// 获取附近地点
			$near_places = $this->_near_places(5, $this->special['place_cid']);
			
			//参与人次 = placeCategory = 124下的所以POST量
			$ugc_count = $this->db->query("select sum(tipCount) as count from Place where id in 
			(select placeId FROM PlaceOwnCategory where placeCategoryId={$this->special['place_cid']})")->row_array();
			
			$this->assign(array(
					'special' => true,
					'join_count' => $ugc_count['count'],//$join_row['num'],
					'near_places' => $near_places,
					'near_num' => 20,
					'cid' => $this->special['place_cid']
					));
		}
		
		empty ( $path ) ? $this->display ( 'detail' ) : $this->display ( 'm_index' );
	}
	
	function m_near_place() {
		$n = intval($this->get('n'));
		$cid = intval($this->get('cid'));
		
		if ($n <= 0 || $cid < 0) {
			$this->showmessage ( '错误的参数' );
			return;
		}
		
		$near_places = $this->_near_places($n, $cid);
		$this->assign(array(
				'near_places' => $near_places,
		));
		
		$this->display('m_near_place');
	}
	
	/**
	 * 获得附近地点的数量
	 * @param 数量 $num
	 * @param 地点分类ID号 $place_cid
	 */
	private function _near_places($num, $place_cid = 0) {
		if ($this->lng > 0 && $this->lng > 0) {
			$distance = sprintf(",f_distance(a.latitude, a.longitude, %s, %s) AS distance", $this->lat, $this->lng);
		}
		
		$sql = sprintf("SELECT a.id, a.placename,
				a.`level`, c.icon as c_icon
				%s
				FROM Place a, PlaceOwnCategory b, PlaceCategory c
				WHERE a.status = 0 AND a.id = b.placeId
				AND b.placeCategoryId = c.id %s
				ORDER BY %s ASC LIMIT %d",
				$distance, $place_cid?" AND c.id = '{$place_cid}'":'', 
				$distance?'distance':'a.id', $num);
		
		$list = $this->db->query($sql)->result_array();
		
		foreach($list as &$row) {
			$row['star'] = intval($row['level']);
			if (isset($row['distance'])) {
				$row['show_distance'] = true;
				$row['distance'] = $row['distance']>2?(intval($row['distance']).'公里'):(intval($row['distance']*1000).'米');
			}
			$row['icon'] = $row['icon']?image_url($row['icon'], 'common'):image_url($row['c_icon'], 'common');
		}
		unset($row);
		
		return $list;
	}
	
	private function list_page($id,$count=0,$page,$sub_page)
	{
		$post_page_size = $this->post_page_size ? $this->post_page_size : 10;
		$limit = array ('size' => $post_page_size, 'offset' => 0 );
		if ($count > 0)
		{
			//分页
			$parr = $this->paginate ( '/event_new/detail', $count, $page, array ('id' => $id, 'sub_page' => $sub_page ), 50 );
			//计算OFFSET
			$offset = 50 * ($page - 1) + $post_page_size * ($sub_page - 1);
			$sub_page += 1;
			//POST列表
			$limit = array ('size' => $post_page_size, 'offset' => $offset );
		}
		return $limit;		
	}
	
	
	/**
	 * 手机访问活动
	 * @param 活动ID号 $id
	 */
	function m_index($id, $uid = 0)
	{
		// 为了适应以前的手机端。直接传入uid
// 		empty ( $this->auth ) && ($this->auth ['uid'] = $this->get ( 'uid' ));
		empty ( $this->auth ) && ($this->auth ['uid'] = $uid);
		// 自己是否已经报名
		if ($this->auth ['uid'])
		{
			$is_apply = $this->m_webeventapply->select ( array ('uid' => $this->auth ['uid'], 'eventId' => $id ) ) ? true : false;
			$this->assign ( 'is_apply', $is_apply );
		}
		
		$this->is_return = true;
		$this->page_size = 5;
		$data = $this->apply_list ( $id, 1 );
		$this->assign ( 'joines', $data ['list'] );
		
		$this->post_page_size = 1;
		
		$this->assign ( array ('uid' => $this->auth ['uid'], 'is_expired' => (TIMESTAMP > strtotime ( $event ['endDate'] )), 'is_begin' => (TIMESTAMP > strtotime ( $event ['startDate'] )) ) );
		
		$this->detail ( $id, 1, 1, 'm' , 3 );
	}
	
	/**
	 * 报名页面
	 * @param int $id
	 */
	function m_apply($id)
	{
		$id = formatid ( $id );
		
		// 获取活动内容
		$event = $this->m_webevent->select_by_id ( $id );
		if (empty ( $event ))
		{
			$this->showmessage ( '访问活动出错啦' );
			return;
		}
		
		$event ['banner'] = image_url ( $event ['image'], 'common', 'udp' );
		
		/*if ($event ['applyType'] == 1)
		{
			$event ['properties'] = decode_json ( $event ['applyProperty'] );
		} else
		{
			$this->showmessage ( '访问活动出错啦' );
			return;
		}*/
		$event ['properties'] = decode_json ( $event ['applyProperty'] )['form'];
		
		$this->title = $event ['subject'];
		
		$this->assign ( 'uid', $this->get ( 'uid' ) );
		$this->assign ( 'event', $event );
		
		$this->display ( 'm_apply' );
	}
	
	/**
	 * POST数据
	 * @param int $id
	 * @param int $page
	 */
	function m_post_list($id, $page = 1)
	{
		$id = formatid ( $id );
		
		// 获取活动内容
		$event = $this->m_webevent->select_by_id ( $id );
		if (empty ( $event ))
		{
			$this->showmessage ( '访问活动出错啦' );
			return;
		}
		
		$event ['banner'] = image_url ( $event ['image'], 'common', 'udp' );
		
		//活动关联的地点
		$place_list = $this->m_webeventownplace->list_by_eventid ( $id );
		$pids = array ();
		foreach ( $place_list as $row )
		{
			$pids [] = $row ['placeId'];
		}
		
		count ( $pids ) >= 1 && $is_selcted_place = true;
		
		//如果活动关联地点是空，去关联的频道栏目关联的placeID取
		if (empty ( $places ))
		{
			$newsCatId = $event ['newsCatId'];
			if ($newsCatId)
			{
				$f_cate = $this->categories [$newsCatId];
				$placeid = $f_cate ['placeId'] ? $f_cate ['placeId'] : $this->categories [$f_cate ['parentId']] ['placeId'];
				//if($placeid){
				//	$place = get_data('place', $placeid);
				$pids [] = $placeid;
			
		//}
			}
		}
		
		
		$apply_property = json_decode ( $event ['applyProperty'], true );
		$event ['apply_tags'] = False;
		$where_tagIds = array ();
		if (! empty ( $apply_property ['keyword'] ))
		{
			$event ['apply_tags'] = tags_to_array ( $apply_property ['keyword'] );
			$event ['default_tags'] = reset ( $event ['apply_tags'] );
			
			//获取出标签ID
			$tags = '';
			foreach ( $event ['apply_tags'] as $v )
			{
				$tags .= "'$v',";
			}
			$tags = trim ( $tags, ',' );
			/*$tagIDs = $this->db->where ( "content in ($tags)" )->order_by ( "id", "desc" )->get ( "Tag" )->result_array ();
			if (! empty ( $tagIDs ))
			{
				foreach ( $tagIDs as $tag )
				{
					$where_tagIds [$tag ['id']] = $tag ['id'];
				}
			}*/
			$where_tagIds = $apply_property ['tagids'];
		}
	
			$table = $this->_tables ['post'];
			
			//基本条件
			$where = 'a.`type`<5 and a.`status` <2';
				
			$this->load->model ( 'post_model', 'm_post' );
			//组合条件
			$count =0;
			$pagesize = 10;
			//既有地点，又有关键字
			$pids = array_filter($pids);
			
			if ($id == $this->special['id']) {
				$condition['order'] = array($this->_tables['post'].'.photo', 'desc');
				//$special_order = '';
			}
			if(!empty($pids) and !empty($where_tagIds) )
			{
				is_array($pids) && $pids = implode(',',$pids);	
				$where_tagIds = implode(',',$where_tagIds);
				$sql = "select count(a.id) as num from `$table` a where a.`placeId` in($pids) and $where";
				$sql .= " UNION  select count(a.id) as num from `$table` a,`PostOwnTag` b where a.id=b.postId and $where and b.tagId in($where_tagIds)";
				$tpl = $this->db->query( $sql )->result_array(0);
				
				foreach ($tpl as $num)
				{
					$count += $num['num'];
				}
				
				//$count = $tpl['num'];
				
				//$limit = $this->list_page($id,$count,$page,$sub_page);
				$list = array();
				if($count){
					$limit['offset'] =  ($page-1)*$pagesize < 0 ? 0 : ($page-1)*$pagesize;
					$limit['size'] = $pagesize;
					$sql = "(select a.* from `$table` a where a.`placeId` in($pids) and $where)";
					$sql .= " UNION (select a.* from `$table` a,`PostOwnTag` b where a.id=b.postId and $where and b.tagId in($where_tagIds)) ";
					$sql .= ($id == $this->special['id']) ? "order by `photo` desc " : "order by id desc ";
					$sql .=  " limit {$limit['offset']},{$limit['size']}";				
					$feeds = $this->db->query( $sql )->result_array();
					$list = $feeds ? $this->m_post->list_post(array(),'thweb',false,$feeds) : array();
				}
			
			//仅地点
			}elseif(!empty($pids))
			{
		        $condition['where_in'] = array(
		        	'column' => $table.'.placeId',
		        	'in' => $pids
		        );
				$count = $this->m_post->count_post ( $condition );
				$condition['limit'] = array(
					'offset' => ($page-1)*$pagesize,
					'size' => $pagesize
				);
				
				$list = $this->m_post->list_post($condition,'thweb');
				
		    //仅关键字   
			}elseif(!empty($where_tagIds))
			{
				
				if($id == $this->special['id']){ // 文明测评数据太多 ,只展示1000好了？
					$count = 1000;
				}else{
				
					$sql = "select count(a.id) as num from `$table` a,`PostOwnTag` b where a.id=b.postId and $where and b.tagId in(".implode(',',$where_tagIds).")";
						
					$tpl = $this->db->query( $sql )->row_array(0);
						
						/*foreach ($tpl as $num)
						{
							$count += $num['num'];
						}*/
					$count = $tpl['num'];
				}
				
				
				if($count){
				$limit['offset'] =  ($page-1)*$pagesize;
				$limit['size'] = $pagesize;
				}
				
				$list = array();
				if($count){
					($id == $this->special['id']) && $where .= " and length(photo) >0 ";
					$sql = "select a.* from `$table` a,`PostOwnTag` b where a.id=b.postId and $where and b.tagId in(".implode(',',$where_tagIds).") ";
					$sql .= /*($id == $this->special['id']) ? "order by length(photo) >0 desc ,id desc " :*/ "order by id desc ";
					$sql .= " limit {$limit['offset']},{$limit['size']}";		
					$feeds = $this->db->query( $sql )->result_array();
					$list = $feeds ? $this->m_post->list_post(array(),'thweb',false,$feeds) : array();
				}
						
				
			}
			unset ( $pids );
			
			if ($page > ceil ( $count / $pagesize ) && $count)
				show_404 ();
		
		
		//动态总数 
		/*$condition = array (

		'where' => array ($this->_tables ['post'] . '.status < ' => 2, $this->_tables ['post'] . '.type < ' => 4 ) );
		if (! empty ( $pids ) && $is_selcted_place)
		{
			$condition ['where_in'] = array ('column' => $this->_tables ['post'] . '.placeId', 'in' => $pids );
		}
		
		if ($event ['applyType'] != 1 && ! empty ( $event ['applyProperty'] ))
		{
			$condition ['like'] = array (//$this->_tables['post'].'.content '=> "#".$event['applyProperty']."#"
'column' => $this->_tables ['post'] . '.content', 'like' => "#" . $event ['applyProperty'] . "#" );
		}
		
		$count = $this->m_post->count_post ( $condition );
		if ($count)
		{
			//分页
			$parr = $this->paginate ( '/event_new/m_post_list', $count, $page, array ('id' => $id ) );
			if ($page > ceil ( $count / $parr ['size'] ))
				show_404 ();
			
		//列表
			$condition ['limit'] = array ('size' => $parr ['size'], 'offset' => $parr ['offset'] );
			$list = $this->m_post->list_post ( $condition, 'mdp' );
		}*/
		
		empty ( $list ) && $list = array ();
		$this->title = $event ['subject'];
		$this->assign ( array ('event' => $event, 'list' => $list ) );
		$this->display ( 'm_post_list' );
	}
	
	/**
	 * 参与活动的用户列表
	 * Create by 2012-12-30
	 * @author liuweijava
	 * @param int $id
	 * @param int $page
	 */
	function m_apply_list($id, $page = 1)
	{
		$id = formatid ( $id );
		
		// 获取活动内容
		$event = $this->m_webevent->select_by_id ( $id );
		if (empty ( $event ))
		{
			$this->showmessage ( '访问活动出错啦' );
			return;
		}
		
		$event ['banner'] = image_url ( $event ['image'], 'common', 'udp' );
		//参与活动的总人数
		$count = $this->m_webeventapply->count_by_eventid ( $id );
		if ($count)
		{
			
			//分页
			$parr = $this->paginate ( '/event_new/m_apply_list', $count, $page, array ('id' => $id ) );
			if ($page > ceil ( $count / $parr ['size'] ))
				show_404 ();
			
		//列表
			$list = $this->m_webeventapply->list_applies ( $id, $parr ['size'], $parr ['offset'] );
		}
		
		empty ( $list ) && $list = array ();
		$this->title = $event ['subject'];
		$this->assign ( compact ( 'count', 'list', 'event' ) );
		
		$this->display ( 'm_apply_list' );
	}
	
	/**
	 * 参加活动列表
	 * @param 活动id $id
	 * @param 页码 $page
	 */
	function apply_list($id, $page = 1)
	{
		$id = formatid ( $id );
		$page = formatid ( $page );
		$page = $page ? $page : 1;
		
		if (empty ( $id ) || empty ( $page ))
		{
			$this->showmessage ( '访问错误' );
		}
		$page_size = $this->page_size ? $this->page_size : 21;
		$count = $this->m_webeventapply->count_by_eventid ( $id );
		$paginate = $this->paginate ( "/event_new/apply_list/{$id}", $count, $page, false, $page_size );
		
		$list = $this->m_webeventapply->list_by_eventid_order_createdate_desc ( $id, $paginate ['size'], $paginate ['offset'] );
		
		$data = array ();
		foreach ( $list as $row )
		{
			$user = get_data ( 'user', $row ['uid'] );
			//var_dump($user);
			$data [] = array ('uid' => $row ['uid'], 'avatar' => (empty ( $user ['avatar_m'] ) || $user ['avatar_m'] == "null") ? "/static/img/head_default.png" : $user ['avatar_m'], //( empty($user['avatar_m']) || $user['avatar_m']=="null" ) ? "/static/img/head_default.png" : 
'name' => $user ['nickname'] ? $user ['nickname'] : $user ['username'] );
		}
		unset ( $list );
		
		$arr = array ('paginate' => $paginate, 'list' => $data );
		if ($this->is_return)
		{
			return $arr;
		} else
		{
			$this->echo_json ( $arr );
		}
	}
	
	/**
	 * 更新申请列表
	 * @param int $id
	 */
	function flush_apply_list($id)
	{
		$id = formatid ( $id );
		
		// 获取活动内容
		$event = $this->m_webevent->select_by_id ( $id );
		if (empty ( $event ))
		{
			return;
		}
		
		// 如果是 0 1 报名表和点击报名 不进行了
		/*if ($event ['applyType'] < 2)
		{
			return;
		}*/
		
		// 判断是否活动已结束或未开始
		if (TIMESTAMP < strtotime ( $event ['startDate'] ) || TIMESTAMP > strtotime ( $event ['endDate'] ))
		{
			// 如果小于开始时间 或 大于结束时间
			return;
		}
		$ttl_id = 'flush_event_apply_' . $id;
		// 去获取一个ttl时间记录
		$dateline = get_data ( 'ttl', $ttl_id );
		$ttl = 60;
		echo 'var t = "', TIMESTAMP, ':', $dateline, '";';
		if ((TIMESTAMP - $dateline) > $ttl)
		{
			// 去获取关联地点
			$place_list = $this->m_webeventownplace->list_by_eventid ( $id );
			$places = array ();
			if ($place_list)
			{
				// 开始看那些记录有关键词
				foreach ( $place_list as $row )
				{
					$places [] = $row ['placeId'];
				}
				unset ( $place_list );
			}
			$uids = $this->m_post->get_event_apply ( $event, $places );
			
			if ($uids)
			{
				$data = array ();
				foreach ( $uids as $uid )
				{
					$data [] = array ('eventId' => $id, 'uid' => $uid );
				}
				$this->m_webeventapply->breplace ( $data );
				echo 'var new_count = "', count ( $data ), '";';
				unset ( $uids, $data );
			}
			
			get_data ( 'ttl', $ttl_id, true );
		}
	}
	
	function vote_list($id){
		$id = formatid ( $id );
		empty ( $this->auth ) && ($this->auth ['uid'] = $this->get ( 'uid' ));
		// 获取活动内容
		$event = $this->m_webevent->select_by_id ( $id );
		if (empty ( $event ))
		{
			$this->showmessage ( '访问活动出错啦' );
			return;
		}
		
		$event ['banner'] = image_url ( $event ['image'], 'common', 'udp' );
		
		$vote = $this->m_vote->get_vote(5,$id,0,'m');
		
		$is_over = 0;
		if($vote['startDate'] > date("Y-m-d H:i:s") && $vote['startDate']!='0000-00-00 00:00:00'){
			
			$is_over = 1;
		}
		elseif($vote['endDate'] < date("Y-m-d H:i:s") && $vote['endDate']!='0000-00-00 00:00:00'){
			
			$is_over = 1;
		}	
		
		$this->assign("uid",$this->auth ['uid']);
		$this->assign(compact('event','vote','is_over'));
		$this->display("m_poll_list");
	}
	
	function wenming_ranking(){
		//统计每个月发布在文明测评地点，包含#文明评价#的点评，根据总点数形成排行榜，带图点评算4点，不带图点评算1点，被编辑删除扣10点（8月的数量需要计入9月）
		//用户自行删除的post排除在外
		
		/*select uid,sum( if( status<2 , if( length(photo) > 0 , 4 , 1 ) , -10 ) ) as totalPoint
			from Post INNER JOIN TopicOwnPost on Post.id=TopicOwnPost.postId 
			where status not in(2,4) and createDate>'2013-08-01' and createDate<'2013-09-01' and TopicOwnPost.topicId = 3501 
			group by uid order by totalPoint desc 
		  limit 10;*/
		
		$cache_id = "cache_wenming_ranking";
		$result = xcache_get($cache_id);
		if(!$result){
			//统计当月的 如果当月=9月 那么时间就跨越8-9月 当月1号到下月1号
			$year = date('Y');
			$month = date('m');
			$start = $year."-".$month."-01";
			if(date('Y-m') == '2013-09'){
				$start = "2013-08-01";
			}
			$endmonth = str_pad($month+1,2,0,STR_PAD_LEFT);
			if($endmonth > 12){
				$year ++ ;
				$endmonth = '01';
			}
			$end = $year."-".$endmonth."-01";
			
			$topic_id = 4979;//4979 ;  
			$place_category = 124 ;
			
			/*	
			 * 增加扣分语句
			 * */
			
			$reduce_sql = '';
			
			if(date('Y-m') == '2013-10'){
				/*
				 *  M先生（uid 456191）：1862条=7448点 3条=12点
				 *	付双儿（uid 445097）：83条=332点 59条=236点 218条=872点
				 *	阴天（uid 459422）：67条=268点 32条=128点 133条=552点
				 *	冯小毛（uid 485447）：72条=288点  9条=36点 39条=156点
				 *	摩西摩西（uid 455713）：15条=60点 48条=192点 64条=256点

				 * */
				$arr_reduce_list = array(
					'456191' => 7448+12+0 ,
					'445097' => 332+236+872 ,
					'459422' => 268+128+552 ,
					'485447' => 288+36+156,
					'455713' => 60+192+256
				);
				if($arr_reduce_list){
					foreach($arr_reduce_list as $k => $row){
						$reduce_sql .= " - if( b.uid = {$k}   , {$row} , 0) ";
					}
				}
			}
			
			$sql = "select b.uid , sum( if(  b.status<2 , if( length(b.photo) > 0 , 4 , 1 ) , -10 ) ) ".$reduce_sql." as totalPoint
  from ( select placeId 
           from PlaceOwnCategory 
          where placeCategoryId = {$place_category} ) a
 inner join Post b 
    on a.placeId = b.placeId
	INNER JOIN TopicOwnPost t on b.id=t.postId
where  b.createDate > '{$start}' 
       and b.createDate < '{$end}' and t.topicId = {$topic_id} and b.status not in(2,4) and b.type=2
 group by b.uid
 order by totalPoint desc
 limit 10 ";
			
			$result = $this->db->query($sql)->result_array();
			foreach($result as &$row){
				$user = get_data('user',$row['uid']);
				$row = array_merge($row,$user);
				unset($user);
			}
			
			xcache_set($cache_id, $result, 3600);
		}
		$uid = $this->auth ['uid'];
		$this->assign(compact('result','uid'));
        $this->display("wenming_ranking");
	}
}