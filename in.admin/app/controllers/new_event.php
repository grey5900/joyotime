<?php
if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
/**
 * 新的活动
 * Author: piglet chenglin.zhu@gmail.com
 * Created on 2012-12-25
 * @internal
 *  @fc_lamp: 由于取消了报名方式，所以凡以后无报名方式，applyType均为0
 *  现在applyProperty的存储方式:
 *  json
 *  值：array('form'=>'报名表','keyword'=>'标签' ,'tagId' =>'标签ID，方便检索')
 */

class new_event extends MY_Controller
{
	function __construct()
	{
		parent::__construct ();
		
		$this->load->model ( 'webevent_model', 'm_webevent' );
		$this->load->model ( 'webeventapply_model', 'm_webeventapply' );
		$this->load->model ( 'webeventownplace_model', 'm_webeventownplace' );
		$this->load->model('tag_model', 'm_tag');
		$this->load->helper ( 'recommend_helper' );
		$this->load->helper ( 'vote_helper' );
		$this->load->helper ( "news" );
		$this->apply_type = &config_item ( 'event_apply_type' );
		
		$role_keys = (array)$this->auth ['role'];
		$roles = get_data ( "role" );
		$newsRights = array ();
		foreach ( $role_keys as $k => $row )
		{
			$newsRights = array_merge ( $newsRights, $roles [$row] ['newsRights'] );
		}
		$this->newsRight = array_unique ( array_filter ( $newsRights ) );
		empty ( $this->newsRight ) && ($this->newsRight = array (0 ));
		unset ( $newRights );
	}
	
	/**
	 * 首页
	 */
	function index()
	{
		$keywords = trim ( $this->post ( 'keywords' ) );
		$status = $this->post ( 'status' );
		$status = in_array ( $status, array ("0", "1" ) ) ? intval ( $this->post ( 'status' ) ) : - 1;
		
		$where_sql = array ();
		if ($status >= 0)
		{
			$where_sql [] = " a.status = {$status} ";
		}
		if ('' !== $keywords)
		{
			$where_sql [] = " a.subject like '%{$keywords}%' ";
		}
		if ($this->auth ['role'] [0] != 1)
		{
			$where_sql [] = " a.newsCatId in (0," . implode ( ",", $this->newsRight ) . ") ";
		}
		$where_sql = $where_sql ? implode ( ' AND ', $where_sql ) : array ();
		$total_num = $this->db->where ( $where_sql, null, false )->from ( 'WebEvent a' )->count_all_results ();
		$paginate = $this->paginate ( $total_num );
		
		$list = $this->db->select ( 'a.*, count(b.uid) as applyCount,
                            IFNULL((SELECT c.expireDate FROM  ' . $this->_tables ['homepagedata'] . ' c 
                            WHERE c.itemType=5 AND c.itemId=a.id), 0) AS digest ,
                            IFNULL(d.id,0) as have_vote', false )
						 ->order_by ( 'a.rankOrder', 'desc' )
						 ->limit ( $paginate ['per_page_num'], $paginate ['offset'] )
						 ->where ( $where_sql, null, false )
						 ->from ( 'WebEvent a' )
						 ->join ( 'WebEventApply b', 'a.id = b.eventId', 'left' )
						 ->join ( 'Vote d', 'd.itemId=a.id and d.itemType=5', 'left' )
						 ->group_by ( 'a.id' )
						 ->get ()
						 ->result_array ();
		$web_site = $this->config->item ( 'web_site' );
		
		$cates = get_hash_newscategory ();
		
		$this->assign ( array ('cates' => $cates, 'status' => $status, 'web_size' => $web_site, 'keywords' => dstripslashes ( $keywords ), 'list' => $list ) );
		
		$this->display ( 'index' );
	}
	
	/**
	 * 添加，
	 */
	function add()
	{
		
		$id = $this->get ( 'id' );
		if ($id)
		{
			// 获取活动信息
			$event = $this->m_webevent->select_by_id ( $id );
			$event ['is_link'] = $event ['link'] ? (strpos ( $event ['link'], sprintf ( '%s/event_new/detail/%s', $this->config->item ( 'in_host' ), $id ) ) === false) : false;
		}
		if ($this->is_post ())
		{
			//var_dump($_POST);exit;
			$images = $this->post ( 'image' );
			$is_link = $this->post ( 'is_link' );
			
			$property = array();
			
			if (empty ( $is_link ))
			{
				// 那么去构造$property $intro
				$titles = $this->post ( 'title' );
				$intros = $this->post ( 'intro' );
				$links = $this->post('link');
				$intro = array ();
				if ($titles)
				{
					foreach ( $titles as $k => $v )
					{
						if ($v)
						{
							$intro [] = array ('title' => $v, 'intro' => $intros [$k], 'link' => $links[$k] );
						}
					}
				}
				
				$apply_type = 0;
				//报名表单
				$labels = $this->post ( 'apply_label' );
				if(!empty($labels))
				{
					$tips = $this->post('apply_tip');
					$apply_req = $this->post('apply_req');
					foreach ($labels as $k=>$v)
					{
						$v = trim($v);
						if(!empty($v))
						{
							$property['form'][] = array(
								'label'=>$v,
								'tip'=>isset($tips[$k]) ? $tips[$k] : '',
								'req'=>isset($apply_req[$k]) ? $apply_req[$k] : ''
							);
							
						}
						
					}
				}
				
				//关键字标签
				$tags = $this->post('tag_keywords');
				$tags = tags_to_array($tags);
				$property['keyword'] = implode(' ',$tags);
				//通过关键字，看是否已经存在标签，不存在就添加标签
				$property['tagids'] = array();
				//var_dump($this->_tables['tag']);exit;
				//写入标签表
				foreach ($tags as $tag)
				{
					$f = $this->m_tag->select(array('content'=>$tag));
					$tag_id = $f['id'];
					if(empty($f))
					{
						$this->m_tag->insert(array('content'=>$tag));
						$tag_id = $this->db->insert_id();
						
						api_update_cache($this->_tables['tag'],array());
					}
					$property['tagids'][] = $tag_id;
				}
				
				
				/** fc_lamp:3.0此方式屏蔽掉
				$apply_type = $this->post ( 'apply_type' );
				switch ($apply_type)
				{
					case 1 :
						$labels = $this->post ( 'apply_label' );
						$tips = $this->post ( 'apply_tip' );
						$property = array ();
						if ($labels)
						{
							foreach ( $labels as $k => $v )
							{
								if ($v)
								{
									$property [] = array ('label' => $v, 'tip' => $tips [$k] );
								}
							}
						}
						$property = encode_json ( $property );
						break;
					case 2 :
						$property = $this->post ( 'tip_keywords' );
						break;
					case 3 :
						$property = $this->post ( 'photo_keywords' );
						break;
				}**/
			}
			$news_cat_id = $this->post ( 'newsCatId' );
			$link = $is_link ? $this->post ( 'link' ) : '';
			
			$data = array (
				'subject' => $this->post ( 'subject' ), 
				'image' => $images [0], 
				'startDate' => $this->post ( 'start_date' ), 
				'endDate' => $this->post ( 'end_date' ), 
				'applyType' => intval ( $apply_type ), 
				'applyProperty' => encode_json($property), 
				'intro' => $intro ? encode_json ( $intro ) : '', 
				//'rankOrder' => intval ( $this->post ( 'rank_order' ) ), 
				'link' => $link, 'newsCatId' => $news_cat_id, 
				'type' => $this->post ( 'type' ) ,
				'show_joins' => $this->post ( 'show_joins' )
			);
			
			$need_vote = $this->post("need_vote");
			
			if ($id)
			{
				$data ['id'] = $id;
				// 修改
				
				if($need_vote){
					$options = $this->post("vote_option_images");
					$subject = $this->post('vote_subject');
					if(empty($options)){
						$this->error("请添加候选项！");
						exit;
					}
					if(empty($subject)){
						$this->error("请填写活动标题！");
						exit;
					}
				}
				$this->m_webevent->update_by_id ( $id, $data );
				vote_add(5,$id);
			} else
			{
				
				if($need_vote){
					$options = $this->post("vote_option_images");
					$subject = $this->post('vote_subject');
					if(empty($options)){
						$this->error("请添加候选项！");
						exit;
					}
					if(empty($subject)){
						$this->error("请填写活动标题！");
						exit;
					}
				}
				
				// 添加
				$data ['status'] = 1;
				$id = $this->m_webevent->insert ( $data );
				vote_add(5,$id);
			}
			// 更新链接
			if (empty ( $is_link ))
			{
				//                 if($news_cat_id) {
				//                     // 属于频道
				//                     $cates = get_hash_newscategory();
				//                     $domain = $cates[$news_cat_id]['domain'];
				//                     $domain = $domain?$domain:$cates[$cates[$news_cat_id]['parentId']]['domain'];
				//                     $host = ($domain?('http://' . $domain):$this->config->item('web_site'));
				//                 } else {
				//                     //
				//                     $host = $this->config->item('web_site');
				//                 }
				// 改成了不管那种情况都用in的域名，要不然客户端不会传入uid 2013.02.04
				$host = $this->config->item ( 'web_site' );
				
				$link = sprintf ( '%s/event_new/detail/%s', $host, $id );
				$this->m_webevent->update_by_id ( $id, array ('link' => $link ) );
			}
			
			// 添加关联的地点
			$places = $this->post ( 'place' );
			$data = array ();
			if ($places)
			{
				foreach ( $places as $pid )
				{
					$data [] = array ('eventId' => $id, 'placeId' => $pid );
				}
			}
			$b = true;
			
			if ($event && empty ( $event ['status'] ))
			{
				$event ['tag_keywords'] = '';
				// 查询出所有的活动关联地点，先减去之前关联地点的活动数
				$placeid_list = $this->db2->select ( 'placeId' )->get_where ( $this->_tables ['webeventownplace'], array ('eventId' => $id ) )->result_array ();
				$pids = array ();
				foreach ( $placeid_list as $row )
				{
					$pids [] = $row ['placeId'];
				}
				$pids && update_count ( 'place', 'eventCount', 'id in (\'' . implode ( '\',\'', $pids ) . '\')', false );
				
				// 添加现在活动的关联数
				$places && update_count ( 'place', 'eventCount', 'id in (\'' . implode ( '\',\'', $places ) . '\')' );
			}
			
			// 先删除关联
			$this->m_webeventownplace->delete_by_eventid ( $id );
			// 添加关联地点
			$data && ($b = $this->m_webeventownplace->binsert ( $data ));
			
			
			$b ? $this->success ( '新建活动成功', $this->_index_rel, $this->_index_uri, 'closeCurrent' ) : $this->error ( '新建活动失败' );
		}
		
		if ($id)
		{
			// 获取关联的地点
			$place = $this->db->from ( 'WebEventOwnPlace a, Place b' )->where ( "a.placeId = b.id AND a.eventId = '{$id}'", null, false )->get ()->result_array ();
			if (empty ( $event ['is_link'] ))
			{
				$event ['intro'] = json_decode ( $event ['intro'], true );
				//@fc_lamp:由于不存在报名方式了，但又要考虑向前兼容(凡以后无报名方式，applyType为0)。
				if ($event ['applyType'] > 0)
				{
					//向前兼容
					if ($event ['applyType'] == 1)
					{
						$event ['apply_property'] = json_decode ( $event ['applyProperty'], true );
					} else
					{
						$event ['tag_keywords'] = $event ['applyProperty'];
					}
				} else
				{
					//现在的方式
					$apply_property = json_decode($event['applyProperty'],true);
					$event['apply_property'] = $apply_property['form'];
					$event['tag_keywords'] = $apply_property['keyword'];
				}
				unset($event['applyProperty']);
			}
			$this->assign ( array ('id' => $id, 'event' => $event, 'place' => $place ) );
		}
		//$cates = get_categorys(0, 1);
		if ($this->auth ['role'] [0] == 1)
		{
			$cates = get_data ( "newscategory" );
		} else
		{
			$where = " id in (" . implode ( ",", $this->newsRight ) . ") and status=1 ";
			
			$list = $this->db->where ( $where, null, false )->get ( "WebNewsCategory" )->result_array ();
			$cates = array ();
			foreach ( $list as $row )
			{
				$cates [$row ['id']] = $row;
			}
			get_cate_list_by_class1 ( $cates, $cates );
		
		}
		
		$category = build_news_category ( $cates, $event ['newsCatId'] );
		$this->assign ( 'category', $category );
		$this->assign ( 'cates', $cates );
		$this->assign ( 'apply_type', $this->apply_type );
		
		$this->display ( 'add' );
	}
	
	/**
	 * 修改
	 */
	function edit()
	{
		$this->add ();
	}
	
	/**
	 * 报名列表
	 */
	function apply_list()
	{
		$keywords = trim ( $this->post ( 'keywords' ) );
		$id = intval ( $this->get ( 'id' ) );
		
		$where_sql = array ("a.eventId = '{$id}'" );
		if ('' !== $keywords)
		{
			$where_sql [] = " b.username like '%{$keywords}%' 
                        OR b.nickname like '%{$keywords}%' 
                        OR a.signInfo like '%{$keywords}%' ";
		}
		$where_sql = $where_sql ? implode ( ' AND ', $where_sql ) : array ();
		$total_num = $this->db->where ( $where_sql, null, false )->from ( 'WebEventApply a' )->join ( 'User b', 'a.uid = b.id', 'left' )->count_all_results ();
		$paginate = $this->paginate ( $total_num );
		
		$list = $this->db->select ( 'a.*, b.username, b.nickname', false )->order_by ( 'a.createDate', 'desc' )->limit ( $paginate ['per_page_num'], $paginate ['offset'] )->where ( $where_sql, null, false )->from ( 'WebEventApply a' )->join ( 'User b', 'a.uid = b.id', 'left' )->get ()->result_array ();
		
		$event = $this->m_webevent->select_by_id ( $id );
		//$apply_property = json_decode ( $event ['applyProperty'], true );
		//if ($event ['applyType'] == 1)
		//if ($event ['applyType'] == 1)
		//{
			$event ['property'] = json_decode ( $event ['applyProperty'], true );
			
			if ($event ['property']['form'])
			{
				foreach ( $list as &$row )
				{
					$row ['signInfo'] = json_decode ( $row ['signInfo'], true );
				}
				unset ( $row );
			}
			//echo "<pre>";
			//var_dump($list);
		//}
		
		$this->assign ( array ('keywords' => dstripslashes ( $keywords ), 'list' => $list, 'event' => $event ) );
		
		$this->display ( 'apply_list' );
	}
	
	/**
	 * 改变活动状态
	 */
	function change_status()
	{
		$id = $this->get ( 'id' );
		$status = $this->get ( 'status' );
		
		$event = $this->m_webevent->select_by_id ( $id );
		
		if ($event ['status'] == $status)
		{
			$this->error ( '提交要修改的数据状态和本来状态一直' );
		}
		
		
		
		// 查询出所有的活动关联地点，先减去之前关联地点的活动数
		$placeid_list = $this->db2->select ( 'placeId' )->get_where ( $this->_tables ['webeventownplace'], array ('eventId' => $id ) )->result_array ();
		$pids = array ();
		foreach ( $placeid_list as $row )
		{
			$pids [] = $row ['placeId'];
		}
		$pids && update_count ( 'place', 'eventCount', 'id in (\'' . implode ( '\',\'', $pids ) . '\')', $status ? false : true );
		
		$data = array ('status' => $status );
		//如果修改成启用的话
		if($event['status']!=0 && $status==0 && $event['type']<2){
			$current = date('Y-m-d G:i:s');
			$data['createDate'] = $current;
			$redis = redis_instance();
			$notify_key_event = $redis->get('notify_key_event');
			//var_dump($notify_key_event_timestamp,strtotime($current));
			$new_t = strtotime($current);
			if($new_t > $notify_key_event) {
				// 写入缓存
				$redis->set('notify_key_event', $new_t);
			}
		}
		
		$b = $this->m_webevent->update_by_id ( $id, $data );
		
		$b ? $this->success ( '活动状态修改成功'.$notify_key_event_timestamp ) : $this->error ( '活动状态修改失败' );
	}
	
	/**
	 * 导出报名列表
	 */
	function apply_list_export()
	{
		$id = intval ( $this->get ( 'id' ) );
		
		$list = $this->db->select ( 'a.*, b.username, b.nickname', false )->order_by ( 'a.createDate', 'desc' )->where ( "a.eventId = '{$id}'", null, false )->from ( 'WebEventApply a' )->join ( 'User b', 'a.uid = b.id', 'left' )->get ()->result_array ();
		
		$event = $this->m_webevent->select_by_id ( $id );
		
		//if ($event ['applyType'] == 1)
		//{
			$arr = json_decode ( $event ['applyProperty'], true );
			$event ['property'] = $arr['form'];
			if ($event ['property'])
			{
				foreach ( $list as &$row )
				{
					$row ['signInfo'] = json_decode ( $row ['signInfo'], true );
				}
				unset ( $row );
			}
		//}
		
		$filename = sprintf ( '活动[%s]报名列表', $event ['subject'] );
		header ( 'Content-type: application/vnd.ms-excel; charset=gbk' );
		header ( 'Content-Disposition: attachment; filename="' . $filename . '.xls"' );
		$property_title = array ();
		if ($event ['property'])
		{
			foreach ( $event ['property'] as $property )
			{
				$property_title [] = $property ['label'];
			}
		}
		$str = "用户ID\t用户名(昵称)\t报名时间\t" . implode ( "\t", $property_title ) . "\n";
		
		foreach ( $list as $row )
		{
			$str .= "{$row['uid']}\t{$row['username']}({$row['nickname']})\t{$row['createDate']}\t";
			$str .= ($event ['property'] ? @implode ( "\t'", $row ['signInfo'] ) : "") . "\n";
		}
		echo mb_convert_encoding ( $str, 'GBK', 'utf-8' );
	}
	
	/**
	 * 推荐到首页
	 */
	function recommend()
	{
		$id = $this->get ( 'id' );
		
		$data = $this->m_webevent->select_by_id ( $id );
		if ($data && empty ( $data ['status'] ) && $data ['type'] < 2)
		{
			// 正常状态
			// 判断是否已经过期
			if (TIMESTAMP > strtotime ( $data ['endDate'] ))
			{
				$this->error ( '活动已过期，不能推荐' );
			}
			
			$this->load->helper ( 'home' );
			if ($this->is_post ())
			{
				// 提交数据过来
				$b = recommend_digest_post ( 5, $id );
				$b === 0 ? $this->success ( '推荐成功', '', '', 'closeCurrent' ) : $this->error ( $b );
			}
			
			recommend_digest ( 5, $id, image_url ( $data ['image'], 'common', 'odp' ) );
		} else
		{
			$this->error ( '活动正在显示中，且在客户端显示的，才能被推荐到首页' );
		}
	}
	
	function rank()
	{
		$id = intval ( $this->get ( 'id' ) );
		$table = trim ( $this->get ( 'table' ) );
		$field = trim ( $this->get ( 'field' ) );
		$order = intval ( $this->get ( 'order' ) );
		
		$update_cache = trim ( $this->get ( 'cache' ) );
		
		redirect ( "/block/rank/id/" . $id . "/table/" . $table . "/field/" . $field . "/order/" . $order . "/cache/" . $update_cache );
	}
	function show_vote($item_type = 0,$item_id = 0){
		show_vote($item_type,$item_id);
	}
}