<?php
/**
 * POST管理，包括点评和图片
 * Create by 2012-3-28
 * @author liuw
 * @copyright Copyright(c) 2012-2014 joyotime
 */
  
 // Define and include
if ( ! defined('BASEPATH')) exit('No direct script access allowed');   
   
 // Code
class Post extends MY_Controller{
	public $post_type = array(
							 1 => '签到',
							 2 => '点评',
							 //3 => '照片',  不再有3这个类型了，判断是否有图片，用photo字段来判定
							 4 => 'YY', 
							 5 => '评价POST', 
							 6 => '购买商品', 
							 7 => '分享', 
							 8 => '成为会员', 
							 9 => '关注好友' 
						);
	function __construct(){
		parent::__construct();
		
		$this->load->model("post_model","m_post");
		
		$role_keys = $this->auth['role'];
		$roles = get_data("role");
		$newsRights = array();
		foreach($role_keys as $k=>$row){
			$newsRights = array_merge($newsRights,$roles[$row]['newsRights']);
		}
		$this->newsRight = array_unique(array_filter($newsRights));
		empty($this->newsRight) && ($this->newsRight = array(0));
		unset($newRights);
		
		
	}
	
	
	
	function index(){
		//全城POST POST MorrisUgcExtraInfo(标记管理员已读状态)
		$pageNum = $this->input->post("pageNum") ? $this->input->post("pageNum") : 1;
    	$numPerPage = $this->input->post("numPerPage") ? $this->input->post("numPerPage") : 20;
    	$keywords = $this->input->post("keywords") ? $this->input->post("keywords") : $this->input->get('keywords');
    	$status = $this->input->post("status");
    	$type = $this->input->post("type") ? $this->input->post("type") : $this->input->get("type");
    	
    	$field = $this->input->post("field") ? $this->input->post("field") : $this->input->get("field");
    	
    	$from = $this->input->get('from');
    	$topic_id = $this->input->get('topic_id');
    	
    	$orderby = $this->input->post('orderby');
    	
    	$isread = $this->input->post("isread");
    	$isEssence = $this->input->post("isEssence");
    	$isTaboo = $this->input->post("isTaboo");
    	$isTopic = false;
    	$photo = $this->input->post("photo");
    	
    	$isrecommend = $this->input->post("isrecommend");
    	
    	$not_include_keyword = $this->input->post("not_include_keyword");
    	
    	/**/
    	$begin = $this->input->post("begin");
    	$end = $this->input->post("end");
    	$poi = $this->input->post("poi_id");
    	$user = $this->input->post("user_id");
    	
    	$where = " 1=1 ";
    	$where .= $keywords ? ($field == "p.content" ? " and  {$field} like '%{$keywords}%' ": " and  {$field} = ".intval($keywords)." "): "";
    	$where .= $status === '0' || $status>=1 ? " and  p.status = ".$status  : "";
    	$where .= $type ? " and  p.type = ".$type  : " and ( p.type in (2,4) or (p.type = 7 and relatedItemType in (19,20,23) ) )"; // (relatedItemType in (19,20) or relatedItemType is null)) 
    	$where .= $isEssence ? " and isEssence = 1" : "";
    	$where .= $isTaboo ? " and isTaboo = 1" : "";
    	$where .= $photo ? " and p.photo IS NOT NULL " : ""; 
    	
    	$where .= $isread ? " and m.read is null" : ""; //查看没读的
    	$where .= $isrecommend ? " and r.itemId is not null" : "";
    	
    	$where .= $begin ? " and p.createDate >= '$begin' " : "";
    	$where .= $end ? " and p.createDate <= '$end' " : "";
    	
    	$where .= $poi ? " and p.placeId = $poi " : "";
    	$where .= $user ? " and p.uid = $user " : "";
    	
    	$where .= $not_include_keyword ? " and p.content not like '%{$not_include_keyword}%' " : "";
    	
    	if($topic_id && $from == 'topic') {
    	    $where .= ' and top.topicId = '.$topic_id;
    	    $isTopic = true;
    	    $this->db->where('id', $topic_id);
    	    $topic_row = $this->db->get('Topic')->row_array();
    	    if($topic_row) {
    	        $keywords = '#'.$topic_row['subject'].'#';
    	    }
    	}
    	
    	//$having = " 1=1 ";
    	//$having .= $isread ? " and isread = 0 " : ""; 
    	//$having .= $isrecommend ? " and isrecommend > 0 " : ""; 
    	// #机甲创建话题啊啊啊啊啊啊啊啊啊啊啊啊啊啊啊啊啊啊啊啊啊啊啊啊啊啊啊啊啊啊啊啊啊啊啊啊啊啊啊啊啊啊啊#
    	//机甲创建话题啊啊啊啊啊啊啊啊啊啊啊啊啊啊啊啊啊啊啊啊啊啊啊啊啊啊啊啊啊啊啊啊啊啊啊啊啊啊啊啊啊啊啊
    	
    	//$pattern = "/(\\#|＃)(机甲创建话题啊啊啊啊啊啊啊啊啊啊啊啊啊啊啊啊啊啊啊啊啊啊啊啊啊啊啊啊啊啊啊啊啊啊啊啊啊啊啊啊啊啊啊)(\\#|＃)/";
		//$test = preg_replace($pattern,'<a target="navTab" href="/ugcv3/post/index/topic_id/'.$row['topicId'].'/from/topic">$1$2$3</a>',"前面后面柚子就不行？#机甲创建话题啊啊啊啊啊啊啊啊啊啊啊啊啊啊啊啊啊啊啊啊啊啊啊啊啊啊啊啊啊啊啊啊啊啊啊啊啊啊啊啊啊啊啊#534534规范大哥大法官广泛地");
    	//var_dump($test);
    	$offset = ($pageNum - 1) * $numPerPage;
    	$order_by = $orderby ? $orderby : " createDate desc ";
    	
    	$list = $this->m_post->get_post_list($where , $numPerPage , $offset , $order_by ,$having ,$isread ? true : false,$isrecommend ? true :false, $isTopic ); 
    	$total = $this->m_post->count_post($where,$having  ,$isread ? true : false,$isrecommend ? true :false, $isTopic );
    	
    	$total_praiseCount = $total_stampCount = $total_shareCount = 0;
    	//if(!$isread || !$isrecommend){
	    	foreach($list as &$row){
	    		//如果没有按未读查询 那么获取读取状态
	    		if(!$isread){
	    			/*$join_read && $sql .= ", IFNULL(m.read,0) as isread ";
					 *$join_rec && $sql .= ",IFNULL(r.expireDate,0) as isrecommend ";
	    			 * */
	    			$rd = $this->db->select("read as isread",null,false)
	    				 ->where("itemId=".$row['id']." and itemType = 19",null,false)
	    				 ->get("MorrisUgcExtraInfo")->row_array(0);
	    			$row['isread'] = $rd['isread'];
	    		}
	    		//如果没有按推荐查新 那么获取推荐状态
	    		if(!$isrecommend){
	    			$rc = $this->db->select("expireDate as isrecommend",null,false)
	    				 ->where("itemId=".$row['id']." and itemType = 19",null,false)
	    				 ->get("HomePageData")->row_array(0);
	    			$row['isrecommend'] = $rc['isrecommend'];
	    		}
	    		$total_praiseCount += $row['praiseCount'];
	    		$total_stampCount += $row['stampCount'];
	    		$total_shareCount += $row['shareCount'];
	    		$total_replyCount += $row['replyCount'];
	    	}
    	//}
    	
		if($total){
    		$parr = $this->paginate($total);
    	}
    	
    	$current_date = date("Y-m-d H:i:s");
    	$this->assign(
    			compact('current_date',
    					'parr',
    					'list',
    					'keywords',
    					'status',
    					'type',
    					'isread',
    					'isEssence',
    					'isTaboo',
    					'isrecommend',
    					'photo',
    					'begin',
    					'end',
    					'poi',
    					'user',
    					'numPerPage',
    					'field',
    					'total_praiseCount',
    					'total_stampCount',
    					'total_shareCount',
    					'total_replyCount',
    					'orderby',
    					'not_include_keyword'
    			));
    	$this->display("post","ugcv3");
	}
	
	
/**
     * 设置关联TAG
     * Create by 2012-12-6
     * @author liuweijava
     */
    function set_tag(){
    	$objType = $this->get('type');
    	$objId = intval($this->get('ids'));//$this->get('postId');
    	$ids = explode("-",$this->get('ids'));
    	//var_dump($_REQUEST);
    	if($this->is_post()){
    		$tags = $this->post('tags');
    		if(!empty($tags)){
    			$tags = explode(' ', $tags);
    			foreach($tags as &$tag){
    				$tag = trim($tag);
    				unset($tag);
    			}
    		}else{
    			$tags = false;
    		}
    		$head = $this->config->item('tag_for_post_head');
    		
            // 访问接口数据
            foreach($ids as $k=>$row){
    			$datas[$k] = array('task'=>encode_json(array('head'=>$head, 'data'=>array('id'=>$row, 'tags'=>($tags?$tags:array())))));
            }
    		
    		//接口
    		$this->lang->load('api');
    		$api_uri = $this->lang->line('tag_api_post_join_tag');
    		//调用接口
    		//var_dump($api_uri,$data);exit;
    		foreach($datas as $data){
    			$result = send_api_interface($api_uri, 'POST', $data, array(), 'tag_api_domain');
    			if($result<0) break;
    		}
    		
    		if($result < 0) {
    		    $this->error('设置TAG的操作执行失败了！');
    		} else {
    		    // 更新网站的post_tags缓存
    		    update_cache('web', 'data', 'post_tags', $ids);
    		    foreach($ids as $row){
    		    	$this->m_post->do_read($row,19);
    		    }
    		    $this->success('操作已完成', '','', 'closeCurrent');
    		}
    	}else{
    		$this->db->select('Post.*, Place.placename, User.username, User.nickname, User.avatar');
    		$this->db->join('User', 'User.id = Post.uid','inner');
    		$this->db->join('Place', 'Place.id = Post.placeId', 'left');
    		$post = $this->db->where('Post.id', $objId)->limit(1)->get('Post')->first_row('array');
    		//var_dump($objId);
    		$post['type'] == 3 && $post['image'] = image_url($post['photoName'], 'user', 'hdp');
    		//已关联的TAG
    		$this->db->select('Tag.*');
    		$this->db->join('Tag', 'Tag.id = PostOwnTag.tagId', 'inner');
    		$tags = $this->db->where(array('PostOwnTag.postId'=>$objId))->order_by('PostOwnTag.tagId', 'asc')->get('PostOwnTag')->result_array();
    		$ts = array();
    		foreach($tags as $row){
    			$ts[] = $row['content'];
    		}
    		$post['tags'] = implode(' ', $ts);
    		//频道的TAG
    		$tags = $this->m_post->_get_cat_tags();
    		//设置TAG的选中状态
    		foreach($tags as &$tag){
    			if(strpos($post['tags'], $tag['content']) !== FALSE){
    				$tag['checked'] = 1;
    			}else{
    				$tag['checked'] = 0;
    			}
    			unset($tag);
    		}
    		//$item_types = $this->config->item("item_type");
    		
    		$typename = $this->post_type[$post['type']];//$item_types[$post['type']]['value'];
    		//var_dump($typename,$post['type']);
    		$this->assign(compact('objType', 'objId', 'post', 'tags','typename'));
    		unset($tags, $ts, $ctags, $list);
    		$this->display('join_tag', 'ugcv3');
    	}
    }
    
	function banned() {
        $type = $this->input->post('type');
        $id = $this->input->post('id');
        
        $score = $this->input->post("score");
        $reason = trim($this->input->post("reason"));
        
        if(empty($reason) || strlen($reason) > 420) {
            $this->error('请输入屏蔽原因，且不能大于140个汉字哦');
        }
        
        // 取出信息
        $table = $type=='100'?'Reply':'Post';
        //$row = $this->db->get_where($table, array('id' => $id))->row_array();
        $row = $this->m_post->get_one_post_or_replay($table,$id);
        if($row) {
            if($row['status'] == 3) {
                // 本身状态已经为屏蔽状态了
                $this->error('该记录已经屏蔽过了，不能重复屏蔽哦，亲。');
            }
            if($row['status'] == 4){
            	$this->error('用户自己已经删除了，不能再屏蔽了啊，亲。');
            }
            
            $this->load->helper('ugc');
            // 更新数量
            $field = '';
            $type_name = '';
            switch($type) {
                case 100:
                    $field = 'replyCount';
                    $type_name = '回复';
                    $key = "banned_reply";
                    $item_type = 100;
                    break;
                case 2:
                case 4:
                case 7:
                    $field = 'tipCount';
                    $type_name = '点评';
                    $key = "banned_tip";
                    $item_type = 19;
                    break;
                    /*case 3:
                     $field = 'photoCount';
                    $type_name = '图片';
                    break;  */
            }
            
            //是否扣分
            if($score){
                $point = 0 - $score;
                /*$uid = $row['uid'];
                  
                if(!is_array($uid)) {
                $uid = array($uid);
                }
                 
                $point_case_conf = $this->config->item('point_case');
                $point_id = $point_case_conf[$key];
                 
                $data = array();
                foreach($uid as $u) {
                // 更新用户的积分
                $r = $this->db->select('point')->where("id = '{$u}'")->get('User')->row_array();
                $new_point = $r['point'] + $point;
                $new_point = $new_point < 0 ? 0 : $new_point;
                $b &= $this->db->where("id = '{$u}'")->update('User', array('point'=>$new_point));
                 
                $data[] = array(
                        'uid' => $u,
                        'pointCaseId' => $point_id
                );
                }
                // 更新用户的积分日志
                $b &= $this->db->insert_batch('UserPointLog', $data);*/
                make_point($row['uid'], $key, $point, $id);
            }
            
            // 处理屏蔽信息
            $b = $this->db->where(array('id' => $id))->update($table, array('status' => 3));
            // 在频道关联POST中删除这条POST
            $this->db->where(array('postId' => $id))->delete($this->_tables['webnewscategorydata']);
            
            /*//要同时屏蔽相关的回复 2013.2.26说不用啦。。客户端还是看得到。。
            if($type==100){ //屏蔽回复的回复
            	$this->db->where(array('replyId'=>$row['id']))->update('Reply',array('status'=>3));
            }else{
            	$this->db->where(array('itemId'=>$row['id'],'itemType'=>19))->update('Reply',array('status'=>3));
            }*/
            
           
           
			
             // 还要删除推荐信息 
            $this->db->where(array('itemType'=> $item_type,'itemId'=> $row['id']))->delete("HomePageData");
            if($table == "Post"){
	            //屏蔽一条post还要去更新话题数（如果有话题）
	            $this->db->select("t.id");
				$this->db->from("Topic t");
				$this->db->join("TopicOwnPost top","top.topicId=t.id","right");
				$this->db->where("top.postId",$row['id']);
				$this->db->where("t.postCount > 0",null,false);
				$post_own_topics = $this->db->get()->result_array();
				
				if($post_own_topics)
				{
					$topics = array();
					foreach($post_own_topics as $t){
						$topics [] = $t['id'];
					}
					
					$this->db->where_in('topicId',$topics)->where('postId',$row['id'])->delete('TopicOwnPost');
					$this->db->where_in('id',$topics)->set('postCount','postCount-1', false)->update('Topic');
					//var_dump($q);
            	}
            }
            
            $place_id = $row['placeId'];
            if($field) { //去更新统计数
            	 $sql = "UPDATE User SET {$field}={$field}-1 WHERE id=? AND {$field} > 0";
                 $b &= $this->db->query($sql, array($row['uid']));
                 
                 if(in_array($type,array(2,4,7))){
                 	if($place_id){
                 	// 处理地点的 点评 数
                    $sql = "UPDATE Place SET {$field}={$field}-1 WHERE id=? AND {$field} > 0";
                    $b &= $this->db->query($sql, array($place_id));
                 	}
                 }
                 else{
                 	// 获取用户回复的信息的POST是什么数据
                    //$post_item = $this->db->get_where('Post', array('id'=>$row['itemId']))->row_array();
                   
                 	switch($row['itemType']){
                 		case 19 :
                 			$or_table = "Post";
                 			break;
                 		case 20 :
                 			$or_table = "PlaceCollection";
                 			break;
                 	}
                 	$item = $this->db->get_where($or_table, array('id'=>$row['itemId']))->row_array();
                 	
                    // POST count-1
                    $sql = "UPDATE $or_table SET replyCount=replyCount-1 WHERE id=? AND replyCount > 0";
                    $b &= $this->db->query($sql, array($row['itemId']));
                    // 回复的话需要获取POST的placeId
                    $place_id = $item['placeId'];
                 }
            }
            
                        
            /*$place_id = $row['placeId'];
            //if($field && $row['status'] != 2) {
                // 如果记录不是敏感词状态
            // 2012.09.11修改为即使在敏感状态也需要去处理数量
            if($field) {
                // 处理用户的 点评 图片 回复数
                $sql = "UPDATE User SET {$field}={$field}-1 WHERE id=? AND {$field} > 0";
                $b &= $this->db->query($sql, array($row['uid']));
                if($type < 11) {
                    // 处理地点的 点评 图片数
                    $sql = "UPDATE Place SET {$field}={$field}-1 WHERE id=? AND {$field} > 0";
                    $b &= $this->db->query($sql, array($place_id));
                } else {
                    // 获取用户回复的信息的POST是什么数据
                    $post_item = $this->db->get_where('Post', array('id'=>$row['postId']))->row_array();
                   
                    // POST count-1
                    $sql = "UPDATE Post SET replyCount=replyCount-1 WHERE id=? AND replyCount > 0";
                    $b &= $this->db->query($sql, array($row['postId']));
                    // 回复的话需要获取POST的placeId
                    $place_id = $post_item['placeId'];
                }
            }*/
            
            // 获取地点名称
            $place = $this->db->get_where('Place', array('id'=>$place_id))->row_array();

            // 发送系统信息
            send_message($reason, array($row['uid']), array(null), array($item_type), false, array(array('place'=>$place?$place['placename']:'未知地点', 'post_type'=>$type_name)));
            //积分操作
           
            
            // 去更新索引
            if(in_array($type,array(2,3,7))) {
                $this->load->helper('search');
                @update_index(40, $id, 'delete');
            }
            
            $b?$this->success('屏蔽成功','','','closeCurrent'):$this->error('屏蔽出错，请检查');
        } else {
            $this->error('错误');
        }
    }
    
    function edit(){
    	$do = $this->get("do") ;
    	$ids = $this->input->post("ids");
    	
    	$itemType = $this->get("itemtype");
    	
    	//itemType = 19
    	if($do == "read"){
    		foreach($ids as $row){
    			$result = $this->m_post->do_read($row,$itemType);
    			if(!$result){
    				$this->error('修改ID为'.$row.'的数据的已读状态时出错!');
    				exit;
    			}
    		}
    		$this->success('操作完成');
    	}
    }
    
/**
     * 推荐到首页
     */
    function recommend() {
        $id = $this->get('id');
        $type = $this->get('type');
        $has_digest = intval($this->get("has_digest"));
        $has_home = isset($_GET['has_home'])?intval($this->get("has_home")):1;
        //$product = $this->m_product->get($id);
        
        $this->assign('homepage', $this->get('homepage'));
        
        $combo = $this->get("combo");
        if($combo){
        	list($id,$type) = explode("-",$combo);
        }
        $size_type="home";
        
        $bool = false;
        switch($type){
            case 18 :
        	case 19 : // user
        		$table = $this->_tables['post'];
        		$condition = "\$bool = \$product['status'] < 2;";
        		$image_field = "photo";
        		$image_place = "user";
        		break;
        	case 20 : // user
        		$table = $this->_tables['placecollection'];
        		$condition = "\$bool = \$product['status'] == 0;";
        		$image_field = "image";
        		$image_place = "placeColl";
        		break;
        	/*case 100:
        		$table = "Reply";
        		$condition = "\$product['status'] < 2";
        		$image_field = "";
        		break;*/
        	case 1 : // common
        		$table = $this->_tables['place'];
        		$condition = "\$bool = \$product['status'] == 0;";
        		$image_field = "background";
        		$image_place = "common";
        		$size_type = "place";
        		break;
        	case 23 : // product
        		$table = $this->_tables['product'];
        		$condition = "\$bool = 1==1;";
        		$image_field = "image";
        		$image_place = "product";
        		break;
        	case 5 : // common
        		$table = $this->_tables['webevent'];
        		$condition = "\$bool = 1==1;";
        		$image_field = "image";
        		$image_place = "common";
        		break;	
        	case 4 : // user
        		    $table = $this->_tables['user'];
        		    $condition = "\$bool = 1==1;";
        		    $image_field = "avatar";
        		    $image_place = "head";
        		    $size_type = "avatar";
        		    break;
        	case 26: // topic
        		$table = 'Topic';
        		$condition = "\$bool = 1==1;";
        		$image_field = "image";
        		$image_place = "common";
        		break;
        }
        
        $product = $this->db->where("id",$id)->get($table)->row_array(0);
        //var_dump($condition,eval($condition),$product['status']);
        eval($condition);
        
        if($product &&  $bool) {
            if($type == 19) {
                // 判断下是否为YY 暂时不这样分开，先确认是否必要区分 。但是分享不能被推荐哦
                /*if(empty($product['placeId'])) {
                    $type = 18;
                }*/
            	/*if($product['type']==7){
            		$this->error("您不能推荐一个分享",null,null,"closeCurrent");
            	}*/
            }
            $this->load->helper('home');
            if($this->is_post()) {
                // 提交数据过来
                $b = recommend_digest_post($type, $id , $has_digest);
                $b===0?$this->success('推荐成功', '', '', 'closeCurrent'):$this->error($b);
            }
            recommend_digest($type, $id, image_url($product[$image_field], $image_place, 'odp'), $has_digest , $has_home ,$size_type);
        } else {
            $this->error('被屏蔽/删除的内容不能执行这个操作');
        }
    }
    
	public function advsearch(){
		$this->assign('do','advsearch');
		$this->display('post','ugcv3');
	}
	
	function share(){
		$item_id = $this->get('item_id');
		$item_type = $this->get('item_type');
		$from_rel = $this->get('rel');
		if($this->is_post()){
			$content = $this->post('content');
			$vest = $this->post('vest');
			$this->load->model("share_model","m_share");
			if($vest === 'random'){
				//随机
				$rs = $this->db->where('aid', $this->auth['id'])->select('uid')->order_by('uid','random')->limit(1)->get('MorrisVest')->first_row('array');
				$v_id = $rs['uid'];
			}else{
				$v_id = $this->post('v_id');
			}
			if(isset($v_id) && !empty($v_id)){
				
				/*$shared = $this->m_share->is_shared($v_id,$item_id,$item_type,0);
				if($shared){
					$this->error ('您(马甲ID：'.$v_id.')已经分享过这个玩意儿啦！');
				}*/
				
				$attrs = array(
					'type' => 0,
					'item_type' => $item_type,
					'item_id' => $item_id,
					'content' => $content,
				);
				
				$result = request_api('/user/share', 'POST', $attrs, array('uid' => $v_id));
				if(!$result) $this->error("请求API失败",'','','','res:'.$result);
				$rt = json_decode($result,true);
				
				if($rt['result_code'] == 0){
					
					$this->success($rt['result_msg'],null,null,'closeCurrent');//$from_rel?$from_rel:
				}
				else{
					
					$this->error($rt['result_msg']);
				}
			}
			else{
				$this->error('没有马甲寸步难行，先去马甲管理里面添加自己的马甲先吧。');
			}
		}
		
		switch($item_type){
			case 19:
				$item = $this->db->where('id',$item_id)->get('Post')->row_array();
				$condition = "\$bool = \$item['status'] < 2;";
				break;
		}
		eval($condition);
		if(isset($bool) && !$bool){
			 $this->error('被屏蔽/删除/隐藏的内容不能执行这个操作');
		}
		
		$this->assign(compact('item'));
		$this->display('share_panel','ugc');
	}
	
	function batch_essence(){
		$idstring = $this->input->get("ids");
    	$itemType = $this->get("item_type");
    	
    	if($this->is_post()){
    		$ids = explode('-',$idstring);
	    	$this->load->helper("home");
	    	$b=0;
	    	foreach($ids as $row){
	    		if(intval($row)>0){
		    		switch($itemType){
			            case 18 :
			        	case 19 : // user
			        		$table = $this->_tables['post'];
			        		$condition = "\$bool = \$product['status'] < 2;";		   
			        		break;
			        	case 20 : // user
			        		$table = $this->_tables['placecollection'];
			        		$condition = "\$bool = \$product['status'] == 0;";
			        		break;		        	
			        	case 1 : // common
			        		$table = $this->_tables['place'];
			        		$condition = "\$bool = \$product['status'] == 0;";
			        		
			        		break;
			        	case 23 : // product
			        		$table = $this->_tables['product'];
			        		$condition = "\$bool = 1==1;";		        		
			        		break;
			        	case 5 : // common
			        		$table = $this->_tables['webevent'];
			        		$condition = "\$bool = 1==1;";		        		
			        		break;	
			        	case 4 : // user
			        		$table = $this->_tables['user'];
			        		$condition = "\$bool = 1==1;";		        		
			        		break;
			        	case 26: // topic
			        		$table = 'Topic';
			        		$condition = "\$bool = 1==1;";		        		
			        		break;
		        	}
		        	$product = $this->db->where("id",$row)->get($table)->row_array(0);
			        
			        eval($condition);
					
			        if($product && $bool) {
			        		
		    				$b===0 && $b = recommend_digest_post($itemType, $row , true , false , true);
		    				if(!($b===0)){
		    					$this->error('给ID为'.$row."的东西加精的时候失败啦，批量加精终止。".$b);
		    					break;
		    				}
			        }
	    			else{
	    				$this->error('被屏蔽/删除的内容不能执行这个操作，批量加精终止。');
	    				break;
	    			}
	    		}
	    	}
	    	$b===0?$this->success('批量加精成功','','','closeCurrent'):$this->error($b);
    	}
    	$this->assign(compact('idstring','itemType'));
    	$this->display('batch_essence','ugcv3');
	}
	
	function batch_ban(){
		
	 	$type = $this->input->post('type');
        $ids = $this->input->get('ids');
        
        $score = $this->input->post("score");
        $reason = trim($this->input->post("reason"));
       
        if(empty($reason) || strlen($reason) > 420) {
            $this->error('请输入屏蔽原因，且不能大于140个汉字哦');
        }
        
        // 取出信息
        $table = $type=='100'?'Reply':'Post';
        $id_arr = explode('-',$ids);
        
	    foreach($id_arr as $id){
	        
	    	$row = $this->m_post->get_one_post_or_replay($table,$id);
	        if($row) {
	        	
	            if($row['status'] == 3) {
	                // 本身状态已经为屏蔽状态了
	                $this->error('该记录已经屏蔽过了，不能重复屏蔽哦，亲。');
	            }
	            if($row['status'] == 4){
	            	$this->error('用户自己已经删除了，不能再屏蔽了啊，亲。');
	            }
	            
	            $this->load->helper('ugc');
	            // 更新数量
	            $field = '';
	            $type_name = '';
	            switch($type) {
	                case 100:
	                    $field = 'replyCount';
	                    $type_name = '回复';
	                    $key = "banned_reply";
	                    $item_type = 100;
	                    break;
	                case 2:
	                case 4:
	                case 7:
	                case 'post':
	                    $field = 'tipCount';
	                    $type_name = '点评';
	                    $key = "banned_tip";
	                    $item_type = 19;
	                    break;
	                   
	            }
	            
	            //是否扣分
	            if($score){
	                $point = 0 - $score;
	                make_point($row['uid'], $key, $point, $id);
	            }
	            
	            // 处理屏蔽信息
	            $b = $this->db->where(array('id' => $id))->update($table, array('status' => 3));
	            // 在频道关联POST中删除这条POST
	            $this->db->where(array('postId' => $id))->delete($this->_tables['webnewscategorydata']);	
	             // 还要删除推荐信息 
	            $this->db->where(array('itemType'=> $item_type,'itemId'=> $row['id']))->delete("HomePageData");
	            if($table == "Post"){
		            //屏蔽一条post还要去更新话题数（如果有话题）
		            $this->db->select("t.id");
					$this->db->from("Topic t");
					$this->db->join("TopicOwnPost top","top.topicId=t.id","right");
					$this->db->where("top.postId",$row['id']);
					$this->db->where("t.postCount > 0",null,false);
					$post_own_topics = $this->db->get()->result_array();
					
					if($post_own_topics)
					{
						$topics = array();
						foreach($post_own_topics as $t){
							$topics [] = $t['id'];
						}
						
						$this->db->where_in('topicId',$topics)->where('postId',$row['id'])->delete('TopicOwnPost');
						$this->db->where_in('id',$topics)->set('postCount','postCount-1', false)->update('Topic');						
	            	}
	            }
	            
	            $place_id = $row['placeId'];
	            if($field) { //去更新统计数
	            	 $sql = "UPDATE User SET {$field}={$field}-1 WHERE id=? AND {$field} > 0";
	                 $b &= $this->db->query($sql, array($row['uid']));
	                 
	                 if(in_array($type,array(2,4,7,'post'))){
	                 	if($place_id){
		                 	// 处理地点的 点评 数
		                    $sql = "UPDATE Place SET {$field}={$field}-1 WHERE id=? AND {$field} > 0";
		                    $b &= $this->db->query($sql, array($place_id));
	                 	}
	                 }
	                 else{
                   
	                 	switch($row['itemType']){
	                 		case 19 :
	                 			$or_table = "Post";
	                 			break;
	                 		case 20 :
	                 			$or_table = "PlaceCollection";
	                 			break;
	                 	}
	                 	$item = $this->db->get_where($or_table, array('id'=>$row['itemId']))->row_array();
	                 	
	                    // POST count-1
	                    $sql = "UPDATE $or_table SET replyCount=replyCount-1 WHERE id=? AND replyCount > 0";
	                    $b &= $this->db->query($sql, array($row['itemId']));
	                    // 回复的话需要获取POST的placeId
	                    $place_id = $item['placeId'];
	                 }
	            }
	            
	            // 获取地点名称
	            $place = $this->db->get_where('Place', array('id'=>$place_id))->row_array();
	
	            // 发送系统信息
	            send_message($reason, array($row['uid']), array(null), array($item_type), false, array(array('place'=>$place?$place['placename']:'未知地点', 'post_type'=>$type_name)));
	            //积分操作
	           
	            
	            // 去更新索引
	            if(in_array($type,array(2,3,7))) {
	                $this->load->helper('search');
	                @update_index(40, $id, 'delete');
	            }
	            if(!$b){
	            	$this->error('在屏蔽ID为'.$id.'的东西出错，屏蔽终止');
	            }
	            //$b?$this->success('屏蔽成功','','','closeCurrent'):$this->error('屏蔽出错');
	        } else {
	            $this->error('错误');
	        }
	    }
        $b?$this->success('屏蔽成功','','','closeCurrent'):$this->error('屏蔽出错');
        
	}
	
	function TopPosters(){
		$date = $this->get('date') ? $this->get('date') : date('Y-m-d');
		
	}
}   
   
 // File end