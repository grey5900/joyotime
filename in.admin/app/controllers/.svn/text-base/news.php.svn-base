<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');   
/*
 * 新闻频道管理
 */

class News extends MY_Controller {
	
	public $newsRight = '';
	public $role ;
	public $_webnews_rel = "news_webnews";
	public $_webnews_uri = "/news/webnews";
	
	function __construct(){
		parent::__construct();
		$this->load->model("news_model","news");
		$this->load->helper("news");
		
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
    /**
     * 新闻频道管理
     */
    function index() {
    	
    	$pageNum = $this->input->post("pageNum") ? $this->input->post("pageNum") : 1;
    	$numPerPage = $this->input->post("numPerPage") ? $this->input->post("numPerPage") : 20;
    	$keywords = $this->input->post("keywords");
    	//var_dump($this->auth['role']);
    	$parentId = $this->input->post("parentId");
    	
    	$rightstring = implode(",",$this->newsRight);
    	
    	$status = $this->get("status");
    	$st = " status in (0,1)";
    	if(!empty($status) || $stauts===0){
    		$st = " status in ($status) ";
    	}
    	if($this->auth['role'][0] != 1 && !$parentId){
    		$st .= $rightstring ? " and (id in ($rightstring) or parentId in ($rightstring))" : " and id in (0) " ;
    	}
    	if($keywords){
    		$st .= " and catName like '%".$keywords."%'";
    	}
    	if($parentId){
    		$st .= " and parentId = ".$parentId;
    	}
    	$list = $this->news->getChannelList($st,$pageNum,$numPerPage);  
    	
    	
    	foreach($list['result'] as $k=>$row){
    		if($row['parentId']){
    			$tmp = $this->news->get_cateinfo_by_id($row['parentId']);
    			$list['result'][$k]['parentName'] = $tmp['catName'];
    		}
    	}
    	
    	$data['list'] = $list['result'];
    	$data['total'] = intval($list['total']);// ? $list['total'] : 0 ;

    	if($list['total']){
    		$parr = $this->paginate($list['total']);
    	}
    	$data['parr'] = $parr;
    	
    	if($this->auth['role'][0]==1){
    		$news_category = get_data("newscategory");
    	}
    	else{
	    	$where = " id in (".implode(",",$this->newsRight).") and status=1 ";
	    	
	    	$list = $this->db->where($where,null,false)->get("WebNewsCategory")->result_array();
	    	$news_category = array();
	    	foreach($list as $row){
	    		$news_category[$row['id']] = $row;
	    	}
//	    	get_cate_list_by_class1($news_category, $news_category);
	    	
    	}
    	
		$tt = build_news_category($news_category,$parentId);
		
    	$this->assign("category",$tt);
    	$this->assign("keywords",$keywords);
    	$this->assign("numPerPage",$numPerPage);
    	$this->assign("status",$status);   
    	$this->assign($data);   
     	$this->display("news_channel");   
    }
    
    //删除父分类，子分类也删除
    function delete(){
    	
    	$id = $this->get('id');
    	if ('POST' == $this->server('REQUEST_METHOD')) {
    		$statusCode = 200;
    		$set = array("status"=>2);
    		$where = '( 1=1 and id='.$id .')';
    		$where .= " or catPath like '%,".$id."%' ";
    		$this->db->where($where);
    		$this->db->set($set);
    		//$this->db->like("catPath",",".$id);
    		$res = $this->db->update("WebNewsCategory");
    		
    		get_data("newscategory",true);
    		update_cache("web","inc","newscategory",0);
            update_cache("web","inc","domains",0);
    		$this->message($statusCode, "删除成功!", $this->_index_rel, $this->_index_uri, 'forward');
    	}
    }
    
	function delthem(){
    	
		$ids = $this->input->post('ids');
		if ('POST' == $this->server('REQUEST_METHOD')) {
    		$statusCode = 200;
    		$set = array("status"=>2);
    		$where = '( 1=1 and id in ('.$ids .' ) ) ';
    		$id_arr = explode(",",$ids);
    		foreach($id_arr as $k=>$row){
    			//$id_arr[$k] = ",".$row;
    			$where .= " or catPath like '%,".$row."%' ";
    		}
    		$this->db->where($where);
    		//$this->db->like("catPath",$id_arr);
    		$res = $this->news->update("",$set);
    		
    		get_data("newscategory",true);
    		update_cache("web","inc","newscategory",0);
            update_cache("web","inc","domains",0);
    		$this->message($statusCode, "批量删除成功!", $this->_index_rel, $this->_index_uri, 'forward');
    	}
    }
    
    
	function delete_news(){
	    $id = $this->get('id');
    	if ('POST' == $this->server('REQUEST_METHOD')) {
    		$statusCode = 200;
    		$set = array("status"=>2);
    		$this->db->where(array("id"=>$id));
    		$res = $this->news->update("WebNews",$set);
    		$this->load->helper('search');
    		@update_index(50, $id, 'delete');
    		$this->message($statusCode, "删除成功!", $this->_index_rel, $this->_webnews_uri, 'forward');
    	}
    }
    
    function delthem_news(){
    	$ids = $this->input->post('ids');
		if ('POST' == $this->server('REQUEST_METHOD')) {
    		$statusCode = 200;
    		$set = array("status"=>2);
    		$res = $this->news->update("WebNews",$set,"id in (".$ids.")");
    		$ids = array_unique(array_filter(explode(',', $ids)));
    		if($ids) {
    		    // 去更新索引
    		    $this->load->helper('search');
    		    foreach($ids as $id) {
    		        $id = intval($id);
    		        @update_index(50, $id, 'delete');
    		    }
    		}
    		
    		$this->message($statusCode, "批量删除成功!", $this->_index_rel, $this->_wenbews_uri, 'forward');
    	}
    }
    
	function add(){
		$id = intval($this->get('id'));
		$news_category_selected = 0;
		$cate_self = 0;
		
		if($id>0){
			//读数据
			$info = $this->db->where(array("id"=>$id))->get('WebNewsCategory')->row(0,"array");
			$news_category_selected = $info['parentId'];	
			$cate_self = $id;		
			//查询出标签
			$tag_list = $this->db->where(array("catId"=>$id))->get('WebNewsCategoryOwnTag')->result_array();
			//详细标签数据
			$tag = array();
			foreach($tag_list as $key=>$value){
				switch($value['tagType']){
					case 0:
						$table = "Tag";
						$field = "content";
						$desc  = "标签";
						break;
					case 1:
						$table = "PlaceCategory";
						$field = "content";
						$desc  = "地点分类";
						break;
					case 2:
						$table = "Place";
						$field = "placename";
						$desc  = "地点";
						break;
					case 3:
						$table = "User";
						$field = "username";
						$desc  = "用户";
						break;
				}
				
				$tmp_con = $this->db->select("id,".$field)->where(array("id"=>$value['tagId']))->get($table)->row(0,"array");			
				$tag[] = array(
					"id"=>$tmp_con['id'],
					$field=>$tmp_con[$field],
					'desc' => $desc,
					'type' => $value['tagType']
				);
			}
			if($info['placeId']){
				$tmp_ext = $this->db->select("id,placename")->where(array("id"=>$info['placeId']))->get("Place")->row(0,"array");		
				$ext_data[] = array(
					"id" => $info['placeId'],
					'placename' => $tmp_ext['placename'],
					'desc' => "地点",
					'type' => 2  
				);
			}
			$fragment_list = array();
			//读取fragment数据
			if($info['fragmentId'] /*&& $info['parentId']==0*/){
				
				$fragment_list = $this->db->select("fid,name")->where("fid in (".$info['fragmentId'].")")->get('WebRecommendFragment')->result_array();
				
				$this->assign("fragment",$fragment_list);
			}
			
			
			$this->assign("ext_data",$ext_data);
			$this->assign("info",$info);
			$this->assign("tag",$tag);
		}
		
		if($this->is_post()) {
		 	$catName = $this->post("catName");
		 	$parentId = intval($this->post("parentId"));
		 	$style = $this->post("style");
		 	$fragment_arr = $this->post("fragment_id");
		 	$keyword = $this->post("keyword");
		 	$description = $this->post("description");
		 	$domain = $this->post("domain");
		 	$tag_arr = $this->post("tag_id");
		 	$status = $this->post("status");
		 	
		 	$link = $this->post("link");
		 	$orderValue = $this->post("orderValue");
		 	
		 	$catType = $this->post("catType");
		 	
		 	$place_arr = $this->post("Place_id");
		 	
		 	$exp_arr = explode("-",$place_arr[0]);
		 	$placeId = $exp_arr[1];
		 	
			if(empty($catName)) {
                $this->error('请填写频道名称');
            }
            
            //貌似其他都不是必填
            $fragment =  is_array($fragment_arr) ? implode(",",$fragment_arr) : "";
            $tags = array();
            if(is_array($tag_arr)){
	            foreach($tag_arr as $tag){
	            	$tmp_arr = explode("-",$tag);
	            	$tags[$tmp_arr[0]][] = $tmp_arr[1];
	            }
            }
            
            $news_data = array(
            	'catName' => $catName,
            	'parentId' => $parentId,
            	'catPath' => 0, //插入后再编辑
            	'depth' => -1,   //插入后再编辑
            	'status' => $status,
            	//'dateline' => time(),
            	'fragmentId' => $fragment,
            	'style' => $style,
            	'keywords' => $keyword,
            	'description' => $description,
            	'domain' => $domain,
            	'link' => $link,
            	'orderValue' => $orderValue,
            	'catType' => $catType,
            	'placeId' => intval($placeId)
            );
            $tip = "添加";
            if($id) //编辑
            {
	            $tip = "修改";
	            if($id==$parentId){
	            	$this->error("不能把自己作为自己的上级分类！");
	            }
	            
	            $prent_info = $this->news->get_cateinfo_by_id($parentId);
	            if($prent_info['depth']>$info['depth']){
	            	$this->error("不能分类指定给下级分类！");
	            }
	            
	            $res = $this->db->update('WebNewsCategory',$news_data,array("id"=>$id));
	            if($res){
	            	$channel_id = $id;
	            }
            }
            else{
            	$news_data['dateline'] = time();
            	$ins = $this->db->insert('WebNewsCategory', $news_data);
	            $channel_id = $this->db->insert_id();
            }
            
			if($channel_id <= 0) {
                $this->error($tip . '频道出错啦，亲');
            }
            
			$this->db->select('id');
            $this->db->where('depth', -1);
            $query = $this->db->get('WebNewsCategory');
            $ids = array();
            foreach ($query->result() as $row) {
                $ids[] = $row->id;
            }
            //修改
            $this->_update_depth_path($ids);
            
            if($parentId==0){
            	$parentId = $channel_id;
            }
            
            $tag_data = array();
            foreach($tags as $k=>$row){
            	foreach($row as $key=>$v){
            		$tag_data[] = array(
            			'channelId' => $parentId,
            			'catId' => $channel_id,
            			'tagType' => $k,
            			'tagId' => $v
            		);
            	}
            }
            
			if($id) //编辑,先删除WebNewsCategoryOwnTag中相关数据 $tag
            {
            	$this->db->delete('WebNewsCategoryOwnTag',array("catId"=>$info['id']));
            } 
            
            $ib = true;
            if($tag_data)
            $ib = $this->db->insert_batch('WebNewsCategoryOwnTag', $tag_data);
            update_cache("web","inc","newscategory",0);
            update_cache("web","inc","domains",0);
            get_data("newscategory",true);
            $ib?$this->success($tip . '频道成功', $this->_index_rel, $this->_index_uri, 'closeCurrent'):$this->error($tip . '频道失败'); 
		}
		 
		//上级分类 目前只有两级分类，所以这里只读出最高级就好了
				
		if($this->auth['role'][0]==1){
    		$news_category = get_data("newscategory");
    	}
    	else{
	    	$where = " (id in (".implode(",",$this->newsRight).") or  parentId in (".implode(",",$this->newsRight).")) and status=1 ";
	    	
	    	$list = $this->db->where($where,null,false)->get("WebNewsCategory")->result_array();
	    	$news_category = array();
	    	foreach($list as $row){
	    		$news_category[$row['id']] = $row;
	    	}
	    	//get_cate_list_by_class1($news_category,$news_category);
	    	
    	}
		
		$tt = build_news_category($news_category,$news_category_selected,$id);
		
    	$this->assign("category",$tt);
		$this->assign("page_id","news_add_fragment_".$id."_".time());
    	$this->display("add");
    }
    
    function status(){
    	$ids = $this->input->post('ids');
		if ('POST' == $this->server('REQUEST_METHOD')) {
			$statusCode = 200;
    		$set = array("status"=>"ABS(status-1)");
    		$this->db->set($set,'',false);
    		$this->db->where("id in (".$ids.")");
    		$res = $this->news->update();
    		update_cache("web","inc","newscategory",0);
            update_cache("web","inc","domains",0);
    		get_data("newscategory",true);
    		$this->message($statusCode, "批量修改状态成功!", $this->_index_rel, $this->_prev_uri, 'forward');
		}
    }
    
	function news_status(){
    	$ids = $this->input->post('ids');
		if ('POST' == $this->server('REQUEST_METHOD')) {
			$statusCode = 200;
    		$set = array("status"=>"ABS(status-1)");
    		$this->db->set($set,'',false);
    		$this->db->where("id in (".$ids.")");
    		$res = $this->news->update('WebNews');
    		$this->message($statusCode, "批量修改状态成功!", $this->_index_rel, $this->_prev_uri, 'forward');
		}
		
    }
    
    function webnews(){
    	
    	$pageNum = $this->input->post("pageNum") ? $this->input->post("pageNum") : 1;
    	$numPerPage = $this->input->post("numPerPage") ? $this->input->post("numPerPage") : 20;
    	
    	$keywords = $this->input->post("keywords");
    	$newsCatId = $this->input->post("newsCatId");
    	
    	$status = $this->input->post("status");
    	
    	$rightstring = implode(",",$this->newsRight);
    	
    	if($this->get("status")){
    		$status = $this->get("status");
    	}
    	$where = '';
    	
    	if($status || $status==='0' || $status===0){//isset($status) && (!empty($status) || $status===0)){
    		
    		$where .= '  status = '.$status;
    	}
    	else{
    		
    		$where .= '  status in (0,1)';
    	}
    	if($keywords){
    		
    		$where .= ' and subject like \'%'.$keywords.'%\'';
    	}
    	if($newsCatId){
    		
    		$where .= ' and newsCatId = '.$newsCatId;
    	}
    	if($this->auth['role'][0] != 1){
    		$where .= $rightstring ? " and newsCatId in ($rightstring)" : " and newsCatId in (0) " ;
    	}
    	
    	$q_total = $this->db->where($where, null, false)->get('WebNews');
    	$this->db->limit($numPerPage,($pageNum-1)*$numPerPage);
    	
    	$q = $this->db->order_by("id desc,dateline desc")->where($where, null, false)->get('WebNews');//->result_array();
    	$list['result'] = $q->result_array();
    	$list['total'] = $q_total->num_rows();//$this->db->count_all_results("WebNews");
    	$list['total'] = $list['total'] ? $list['total'] : 0 ;
    	
    	$cates = get_hash_newscategory();
    	
    	foreach($list['result'] as $k=>$row){
    		if($row['newsCatId']){
//     			$tmp = $this->news->get_cateinfo_by_id($row['newsCatId']);
//     			$list['result'][$k]['cate'] = $tmp['catName'];
                // 这里保存了缓存，就从缓存去读取名字塞 2013.02.05
    		    $list['result'][$k]['cate'] = $cates[$row['newsCatId']]['catName'];
    		}
    	}
    	
    	$data['list'] = $list['result'];
    	$data['total'] = intval($list['total']);// ? $list['total'] : 0;

    	if($list['total']){
    		$parr = $this->paginate($list['total']);
    	}
    	$data['parr'] = $parr;
    	
    	
    	
    	if($this->auth['role'][0]==1){
    		$news_category = get_data("newscategory");
    	}
    	else{
	    	$where = " id in (".implode(",",$this->newsRight).") and status=1 ";
	    	
	    	$list = $this->db->where($where,null,false)->get("WebNewsCategory")->result_array();
	    	$news_category = array();
	    	foreach($list as $row){
	    		$news_category[$row['id']] = $row;
	    	}
	    	//get_cate_list_by_class1($news_category,$news_category);
	    	
    	}
		$tt = build_news_category($news_category,$newsCatId,$id,true);
		
		$website = $this->config->item('web_site');
		$this->assign("website",$website);
		$this->assign("status",$status);
		$this->assign("numPerPage",$numPerPage);
		$this->assign("status",$status);
		$this->assign("keywords",$keywords);
    	$this->assign("category",$tt);
    	$this->assign($data);   
    	$this->display("news");
    }
    
    function add_news(){
    	$config = $this->config->item("img_remote");
    	if($this->auth['role'][0]==1){
    		$news_category = get_data("newscategory");
    	}
    	else{
	    	$where = " id in (".implode(",",$this->newsRight).") and status=1 ";
	    	
	    	$list = $this->db->where($where,null,false)->get("WebNewsCategory")->result_array();
	    	$news_category = array();
	    	//$tmp_category = array();
	    	foreach($list as $row){
	    		$news_category[$row['id']] = $row;
	    	}
	    	//get_cate_list_by_class1($tmp_category,$news_category);
    	}
    	
    	$id = intval($this->get('id'));
    	
    	$info['editor'] = $this->auth['truename'];
    	$this->assign("info",$info);
    	$this->assign("timestamp",$this->microtime_float());
    	
    	$news_category_selected = 0;
		if($id>0){
			$info = $this->db->where(array("id"=>$id))->get("WebNews")->row_array(0);
			$news_category_selected = $info['newsCatId'];
			$this->assign("info",$info);
			
			//得到图片
			if($info['thumb']){
				$url_path = $config['domain'][1].$config['path'].$this->get_img_path($info['thumb']);
				
				$this->assign("img",$info['thumb']);
				$this->assign("image",$url_path);
			}
			
			//批量图片
			if($info['newsType']==1){
				$pics = $this->db->select("attachment as image,description as detail")->where(array("itemId"=>$id,"itemType"=>0))->order_by("id","asc")->get("WebPictureAttachment")->result_array();
				$this->assign("batch_img",json_encode($pics));
			}
			
			//获取选择的地点数据
			$place_list = $this->db->where(array("newsId"=>$id))->get('WebNewsPlace')->result_array();
			
			if($place_list){
				$place = array();
				foreach($place_list as $k=>$value){
					$tmp = $this->db->select("id,placename")->where(array("id"=>$value['placeId']))->get("Place")->row_array(0);		
					$place[] = array(
						"id"=>$tmp['id'],
						"placename"=>$tmp["placename"]
					);
				}
				
				$this->assign("place",$place);
			}
			
			if($info[relatedNews]){
				$ids = explode(",",$info[relatedNews]);
				$this->db->where_in('id',$ids);
				$relatedNews = $this->db->get('WebNews')->result_array();
			}
			$this->assign("relatedNews",$relatedNews);
			
		}
		if($this->is_post()) {
			
			$subject = $this->post("subject");
			$newsCatId = intval($this->post("newsCatId"));
			$newsType = intval($this->post("newsType"));
			$summary = $this->post("summary");
			$content = $this->post("content");
			$img = $this->post("newsimg");
			
			$batch_images = (array)$this->post("images");
			
			$source = $this->post("source");
			$editor = $this->post("editor_name");
			$keywords = $this->post("keywords");
			$status = $this->post("status");
			
			$author = $this->post("author");
			
			$linkuri = $this->post("linkuri");
			
			$news = $this->post("relatedNews");
			$news && $relatedNews = @implode(",",$news);
			
			$auto_link = $this->post("autolp_link");
			//var_dump($auto_link);exit;
			
			if(empty($subject)){
				$this->error("请填写标题！");
			}
			if(empty($content) && empty($linkuri)){
				$this->error("请填写内容！");
			}
			
			if(empty($newsCatId)){
				$this->error("没有选择分类，如果没有分类，请先添加分类！");
			}
			
			if(empty($summary)){
				$summary = cut_string(strip_tags(str_replace(array('[next]','[pagenext]'),'',$content)),120,"...");
			}
			
			$news_data = array(
				'subject' => $subject,
				'summary' => $summary,
				'content' => $content,
				'thumb' => $newsType==0 ? $img : $batch_images['image'][0],
				'newsType' => $newsType,
				//'dateline' => time(),
				'newsCatId' => $newsCatId,
				'source' => $source,
				'editor' => $editor,
				'status' => $status,
				'keywords' => $keywords,
				'author' => $author,
				'linkuri' => $linkuri,
				'relatedNews' => $relatedNews
			);
			$an_content = $this->analysis($news_data,false,false,$auto_link ? true : false);
			if($an_content['content']){
				$news_data['content'] = $an_content['content'];
			}
			if($an_content['thumb'] && empty($news_data['thumb'])){
				$news_data['thumb'] = $an_content['thumb'];
			}
			
			/*if(basename($img)){
				$news_data['thumb'] = $img;//basename($img);
			}*/
			//echo $id;
			$tip = "添加";
			if($id<=0){
				$news_data['dateline'] = time(); 
				$ins = $this->db->insert('WebNews', $news_data);
	        	$news_id = $this->db->insert_id();
			}
			else{
				$tip = "修改";
				$news_id = $id;
				
				$this->db->set($news_data);
				$this->db->where(array("id"=>$id));
				$this->news->update('WebNews');
			}
			if(empty($news_id)){
				$this->error($tip."新闻失败");
			}
			
			//完全忘了处理新闻关联palaceid了吧。。。
			$palaces = $this->input->post("place");
			if($palaces){
				$place_data = array();
	            foreach($palaces as $k=>$row){
	            	
	            	$place_data[] = array(
	            		'newsId' => $news_id,
	            		'placeId' => $row
	            	);
	            }
	            
				if($id) //编辑,先删除WebNewsCategoryOwnTag中相关数据 $tag
	            {
	            	$this->db->delete('WebNewsPlace',array("newsId"=>$id));
	            } 
	            
	            $ib = true;
	            if($place_data)
	            $ib = $this->db->insert_batch('WebNewsPlace', $place_data);
			}
			
			
			
			if($id){
				//貌似要先清除图片附件
				$this->db->delete('WebPictureAttachment',array("itemId"=>$id,"itemType"=>0));
			}
			if($img || !empty($batch_images)){	
				if($newsType==0 && $img){
					
					$img_info = $this->getImgInfo($img);
					
					$img_data = array(
						'filesize' => $img_info['filesize'],
						'attachment' => $img_info['url_path'],
						'description' => '',
						'dateline' => time(),
						'width' => intval($img_info['img_info'][0]),
						'height' => intval($img_info['img_info'][1]),
						'itemId' => $news_id,
						'itemType' => 0
					);
					
					$ii = $this->db->insert('WebPictureAttachment', $img_data);
					if(empty($ii)){
						$this->error("添加附件失败，请稍后编辑新闻来添加附件",'',$this->_index_uri);
					}
				}
				else if($newsType==1 && !empty($batch_images)){
					$img_data = array();
					
					foreach($batch_images['image'] as $k=>$row){
						
						$img_info = $this->getImgInfo($row);
						
						$tmp_arr = array(
							'filesize' => $img_info['filesize'],
							'attachment' => $img_info['url_path'],
							'description' => $batch_images['detail'][$k],
							'dateline' => time(),
							'width' => $img_info['img_info'][0],
							'height' => $img_info['img_info'][1],
							'itemId' => $news_id,
							'itemType' => 0 //新闻：0
						);
						$img_data[$k] = $tmp_arr;
					}
					
				
					$ii = $this->db->insert_batch('WebPictureAttachment', $img_data);
					if(empty($ii)){
						$this->error("添加附件失败，请稍后编辑新闻来添加附件",'',$this->_index_uri);
					}
					
				}
			}
			// 去更新索引
			$this->load->helper('search');
			@update_index(50, $news_id, $status?'update':'delete');
			
			$this->success($tip . '新闻成功', $this->_webnews_rel, '','closeCurrent'); 
		}
		
		$tt = build_news_category($news_category,$news_category_selected,0,true);
		$this->assign("page_id","news_add_news_".$id."_".time());
    	$this->assign("category",$tt);
    	$this->display("add_news");
    }
    
    function analysis($data,$changeData = false,$cancel_a = false,$link_loupan=true){
    	$loupan = get_data("loupan");
    	$search = $loupan['data'];
    	$replace = $loupan['replace'];
    	$info = $data;
    	if(is_int($data) || is_string($data) ){
    		
    		$info = $this->db->where("id",intval($data))->get("WebNews")->row_array(0);
    	}
    	if(empty($info)) return false;
    	//处理 content 增加楼盘链接
    	//要屏蔽a标签的替换，如有有a标签，忽略。
		$pattern = "/<a(.*?)>(.*?)<\/a>/i";//"<a(.*?)>(.*?)</a>";
		$content = $info['content'];
		
		if($cancel_a){
			$content = preg_replace(array("/<a(.*?)>(.*?)<\/a>/i", "/<a(.*?)>(.*?)/i", "/<\/a>/i"), array('$2', '$2', ''), $content);
		}
		else{
	    	preg_match_all($pattern,$content,$matches);
	    	//var_dump($matches);
	    	$escape_arr = array();
	    	$replace_arr = array();
	    	foreach($matches[0] as $k=>$row){
	    		$escape_arr[$k] = $row; 
	    		$replace_arr[$k] = "{Y_".$k."_Y}";
	    	}
		}
    	$cout_ma = count($matches[0]);
    	if(!$cancel_a){
    	//先把超链接换成其他字符串
    	$content = str_ireplace($escape_arr,$replace_arr,$content);
    	}
    	//把楼盘名字换成链接
    	$link_loupan && $content = str_ireplace($search,$replace,$content);
    	if(!$cancel_a){
    	//再把之前换成字符串的链接还原
    	$content = str_ireplace($replace_arr,$escape_arr,$content);
    	}
    	$thumb = "";
    	if(empty($info['thumb'])){
	    	//如果没有缩略图，把正文第一张匹配的图片作为缩略图保存
	    	$img_pattern = "/<img(.*?)src=['\"](.*?)['\"]/i";
    		preg_match_all($img_pattern,stripslashes($info['content']),$matches);
	    	foreach($matches[2] as $row){
	    		if(stripos($row,"cdqss.net") || stripos($row,"cdqss.com") || stripos($row,"chengdu.cn") || stripos($row,"joyotime.com") ){
	    			$thumb = $row;
	    			break;
	    		}
	    	}
    	}
    	//var_dump($info);
    	$set = array(
    			'content' => $content
    	);
    	if(empty($info['thumb'])){
    		$set['thumb'] = $thumb ;
    	}
    	
    	if($changeData && $info['id']){
    		return $this->db->where("id",$info['id'])->update("WebNews",$set) == false ? 0 : 1+$cout_ma;
    	}
    	
    	return $set;
    }
    
    function analysis_run_all($page = 1,$pagesize = 100){
    	
    	$list = $this->db->limit($pagesize,($page-1)*$pagesize)->order_by("id","asc")->get("WebNews")->result_array();
    	if(empty($list)) die("没有啦");
    	foreach($list as $k=>$row){
    		$res = $this->analysis($row,true,true);
    		if(!$res){
    			die("更新第".$row['id']."新闻失败，更新终止");
    		}
    		if($res>1){
    			echo $row['id']."<br/>";
    		}
    	}
    	header("Location:/news/analysis_run_all/".($page+1)."/".$pagesize);
    }
    
    /*function test($data){
    	//$info = $this->db->where("id",intval($data))->get("WebNews")->row_array(0);
    	$content = '<p><a target="_blank" class="place" href="/place/322556">龙湖弗莱明戈</a>&mdash;&mdash;龙湖地产高端旗舰力作，城西首席洋房大社区！龙湖用心倾注城西，200亩西班牙顶级花园洋房大社区完美呈现，高品质醇熟生活卓然于世。项目由清水瞰景楼王、精装格调公馆、墅级花园洋房、创意“海棠”跃层四大精品业态和50000㎡别墅级园林组成。位于郫县新城核心，与郫县政府大楼相视而坐，区域价值逐年攀升，国家级综合保税区产业链集群带来的财富聚变。成都唯一双轨物业样本快铁（已通车），地铁2号线（即将贯通）通达全城。<a target="_blank" class="place" href="/place/322556">龙湖弗莱明戈</a>毗邻成都最大购物中心&mdash;&mdash;<a target="_blank" href="http://f.chengdu.cn/fang/fang-2119-1.html" class="c-blue"></a><a target="_blank" href="http://f.chengdu.cn/fang/fang-2119-1.html" class="c-blue">龙湖时代天街</a>180万㎡商业综合体，一站式吃喝玩乐游购娱，满足所有消费需求。更有太平洋购物、凯旋门广场、欧尚（规划中）、优玛特（规划中）、商业美食街（规划中）等7大商圈环绕，步行仅需3-5分钟。四川大学、电子科大、西南交大、成都七中等完善的教育配套，不仅享受从幼儿园到大学的一流全程教育配套，更有数万租房需求。区域内西南兵工医院、郫县人民医院、郫县妇幼保健院等十大医院，随时为您的健康保驾护航。<a target="_blank" class="place" href="/place/322556">龙湖弗莱明戈</a>周边完善的生活配套为龙湖？弗莱明戈提供着国际化的升势平台，成为目前城西最具投资和自住的双宜楼盘，成功树立高新西人居标杆！</p>';
    	$pattern = "/<a(.*?)>(.*?)<\/a>/i";
    	preg_match_all($pattern,$content,$matches);
    	//$content = preg_replace(array("/<a(.*?)>(.*?)<\/a>/", "/<a(.*?)>(.*?)/", "/<\/a>/"), array('$2', '$2', ''), $content);
    	
    	var_dump($matches);
    }*/
    
    function report(){
    	//初始日期 2013-01-20
    	$pageNum = $this->input->post("pageNum") ? $this->input->post("pageNum") : 1;
    	$numPerPage = $this->input->post("numPerPage") ? $this->input->post("numPerPage") : 20;
    	$keywords = $this->input->post("keywords"); //指定一个日期来查看
    	
    	$data['numPerPage'] = $numPerPage;
    	
    	
    	$cates = $this->db->select("id,catName")->where("parentId",0)->where("status",1)->get("WebNewsCategory")->result_array();
    	$data['cates'] = $cates;
    	
    	$ini_date = "2013-01-20";
    	
    	//按日期倒序排列，从今天开始 ---- 一直到2013-01-20结束
    	$today = time();
    	$timestamp = time() - (($pageNum-1)*$numPerPage)*(3600*24) - 3600*24*($pageNum-1); //开始的日期
    	$overdate =  date("Y-m-d",$timestamp - $numPerPage*3600*24) ;
    	
    	if(!empty($keywords)){
    		$timestamp = strtotime($keywords);
    		$overdate = $keywords;
    		$data['keywords'] = $keywords;
    	}
    	
    	if($overdate < $ini_date && !$keywords){
    		$overdate = $ini_date;
    	}
    	
    	//总天数 $today - 2013-01-20 
    	$total = floor(($today - strtotime($ini_date))/(3600*24)) ;
    	
    	if($total){
    		$parr = $this->paginate($total);
    	}
    	$data['parr'] = $parr;
    	
    	$timeTable = array();
    	for($i = $timestamp ;$i>= strtotime($overdate) ; $i=$i-3600*24){
    		//$timeTable[date("Y-m-d",$i)] = "";
    		$thisday = date("Y-m-d",$i);
    		$tmp = array();
    		//foreach($cates as $row){
    			
    			$sql = "select d.channelId,count(d.postId) as c_post,count( DISTINCT p.uid) as c_uid FROM 
    					WebNewsCategoryData d left JOIN Post p on p.id=d.postId where  
    					p.createDate like '".$thisday."%' group by d.channelId";
    			
    			$tmp = $this->db->query($sql)->result_array();
    			
    			$format_tmp = array();
    			foreach($tmp as $row){
    				$format_tmp[$row['channelId']] = array("c_post"=>$row['c_post'],"c_uid"=>$row["c_uid"]);
    			}
    			
    			//$timeTable[$thisday][$row['id']] = $tmp;
    			foreach($cates as $row){
    				$timeTable[$thisday][$row['id']] = $format_tmp[$row['id']];
    			}
    		//}
    	}
    	$data['timetable'] = $timeTable;
    	
    	
    	$this->assign($data);
    	$this->display("report");
    }
    
    function preview($pages = 1,$page = 1,$place_id = 0){
    	
    	$total_page = $pages;
    	$page_string = "";
    	if($total_page>1){
	    	$page_string = "<ul>";
	    	for($i=1;$i<=$total_page;$i++){
	    		$class = $page==$i ? "class='active'": "";
	    		$page_string .= "<li {$class} ><a href='/news/preview/{$pages}/{$i}/{$place_id}'>{$i}</a></li>";
	    	}
	     	$page_string .= "</ul>";;
    	}
    	
    	if($place_id){
    		$location = $this->db->where("id",$place_id)->get("Place")->row_array(0);
    	}
    	
    	$site = $this->config->item('web_site');
    	$this->assign(compact('site','page_string','page','location'));
    	$this->display("preview");
    }
	
	private function _update_depth_path($ids) {
        if (isset($ids) && !empty($ids)) {
            $sql = 'SELECT a.id,b.depth,b.catPath,a.parentId FROM WebNewsCategory a 
            INNER JOIN (SELECT id,depth,catPath,parentId FROM WebNewsCategory) b 
            ON b.id=a.parentId WHERE a.id IN (\'' . implode("','", $ids) . '\')';
            $query = $this->db->query($sql);
            
            $row = $query->first_row();
            if (!isset($row) || empty($row)) {
                //上级权限为空的情况，只有创建根节点菜单时才会出现
                foreach ($ids as $id) {
                    $edit = array(
                            'depth' => 0,
                            'catPath' => '0'
                    );
                    $this->db->where('id', $id);
                    $this->db->update('WebNewsCategory', $edit);
                }
            } else {
                //一般情况
                foreach ($query->result() as $row) {
                    $edit = array(
                            'depth' => $row->depth + 1,
                            'catPath' => $row->catPath . ',' . $row->parentId,
                    );
                    $this->db->where('id', $row->id);
                    $this->db->update('WebNewsCategory', $edit);
                }
            }
        }
    }
    function get_img_path($url){
    	//如：201212_06_151152712_3.jpg
    	$url = basename($url);
    	$pathinfo = pathinfo($url);
    	$name = str_replace(".".$pathinfo['extension'],"",$url);
    	$name_arr = explode("_",$name);
    	
    	return "picture".$name_arr[3]."/".$name_arr[0]."/".$name_arr[1]."/".$url;
    }
	function microtime_float()
	{
	    list($usec, $sec) = explode(" ", microtime());
	    return str_replace(".","",((float)$usec + (float)$sec));
	}
	
	function getImgInfo($img){
		$config = $this->config->item("img_remote");
		//分析文件名，获得路径
		$file_path = $config['root'].$this->get_img_path($img);
		$url_path = $img;//$config['domain'][array_rand($config['domain'])].$config['path'].$this->get_img_path($img);
		$filesize = @filesize($file_path); //
					
		$img_info = @getimagesize($file_path) ;
		return array("img_info"=>$img_info,"file_path"=>$file_path,"url_path"=>$url_path,"filesize"=>$filesize);
	}
	    
   
}
