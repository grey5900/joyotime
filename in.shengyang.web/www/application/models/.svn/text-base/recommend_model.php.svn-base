<?php
/**
 * Create by 2012-11-27
 * @author liuweijava
 * @copyright Copyright(c) 2012-
 */
  
 // Define and include
if ( ! defined('BASEPATH')) exit('No direct script access allowed');   
   
 // Code
class Recommend_model extends MY_Model{
	
	function __construct(){
		parent::__construct();
		$this->load->helper('recommend_helper');//公共函数库
	}
	
	/**
	 * 获取旧的推荐数据
	 * Create by 2012-11-27
	 * @author liuweijava
	 * @param int $fid
	 * @return array, array('size'=>[count($list)],'datas'=>$list)
	 */
	function old_rec_datas($fid){
		$query = $this->db->where('fragmentId', $fid)->order_by('orderValue', 'asc')->get('WebRecommendData')->result_array();
		
		return array(
			'size'=>count($query),
			'datas'=>$query
		);
	}
	
	/**
	 * 保存推荐数据
	 * Create by 2012-11-27
	 * @author liuweijava
	 * @param int $fid
	 * @return int code:0=保存&更新成功；1=碎片不存在；2=保存数据失败；3=标题未录入；4=内容链接未设置
	 */
	function save_data($fid){
		//获得碎片的扩展属性设置
		$query = $this->db->where('fid', $fid)->get('WebRecommendFragment')->first_row('array');
		if(empty($query))
			return 1;
		else{
			//清空以前的推荐数据
			$this->db->where('fragmentId', $fid)->delete('WebRecommendData');
			$extProps = array();
			!empty($query['extraProperty']) && $extProps = json_decode($query['extraProperty'], true);
			//POST数据
			$titles = $this->input->post('title');
			$linkes = $this->input->post('title_link');
			if(empty($titles)) 
				return 3;
			elseif(empty($linkes))
				return 4;
			else{
				$titles = explode('┆', urldecode($titles));
				$linkes = explode('┆', urldecode($linkes));
				$summaries = $this->input->post('intro');
				!empty($summaries) && $summaries = explode('┆', urldecode($summaries));
				$images = $this->input->post('image');
				!empty($images) && $images = explode('┆', urldecode($images));
				if(!empty($extProps)){//扩展属性数据
					foreach($extProps as $key=>&$ext){
						$exp_data = $this->input->post('extra_'.$ext['key']);
						!empty($exp_data) && $ext['datas'] = explode('┆', urldecode($exp_data));
						unset($ext);
					}
				}
			//	$dateline = time()+8*3600;
				//封装数据
				$datas = array();
				for($i=0;$i<count($titles);$i++){
					$data = array('fragmentId'=>$fid);
					$data['orderValue'] = $i+1;
					$data['dateline'] = time()+8*3600;
					!empty($titles[$i]) && $data['title'] = $titles[$i];
					!empty($linkes) && !empty($linkes[$i]) && $data['link'] = $linkes[$i];
					if(!empty($summaries) && !empty($summaries[$i]))
						$data['summary'] = $summaries[$i];
					else 
						$data['summary'] = '';
					if(!empty($images) && !empty($images[$i]))
						$data['image'] = $images[$i];
					else 
						$data['image'] = '';
					//扩展属性
					if(!empty($extProps)){
						foreach($extProps as $key=>$ext){
							if(!empty($ext['datas']) && !empty($ext['datas'][$i]))
								$data['extraData'][$key] = $ext['datas'][$i];
							else 
								$data['extraData'][$key] = '';
						}
						foreach($data['extraData'] as $k=>$v){
							if(empty($v))
								unset($data['extraData'][$k]);
						}
					}
					if(!empty($data['extraData'])){
						$ed = $data['extraData'];
						$data['extraData'] = encode_json($ed);
					}else{
						$data['extraData'] = '';
					}
					$datas[] = $data;
					unset($data);
				}
				//保存数据
				$this->db->insert_batch('WebRecommendData', $datas);
				unset($datas);
			//	exit($this->db->last_query());
				//更新碎片
				if(flush_rec_data($fid))
					return 0;
				else 
					return 2;
			}
		}
	}
	
	/**
	 * 查询备选数据
	 * Create by 2012-11-30
	 * @author liuweijava
	 * @param string $type
	 * @param int $fid
	 * @param string $pt
	 * @param string $key
	 * @return array;
	 */
	function ds_datas($type, $fid, $pt='', $key=''){	
    	$datas = array();
    	$key = urldecode($key);
    	//根据type从不同的地方获取数据
    	switch($type){
    		case 'news'://从WebNews获取数据,忽略PT，从碎片关联的频道查询数据
    			//获得关联了碎片的频道
    			$cids = array();
    			$cats = $this->db->where('FIND_IN_SET(\''.$fid.'\', fragmentId)')->get('WebNewsCategory')->result_array();
    			foreach($cats as $c){
    				$cids[] = $c['id'];
    			}
    			unset($cats);
    			$this->db->select('WebNews.*')->join('WebNewsCategory', 'WebNewsCategory.id=WebNews.newsCatId', 'inner');
    			$this->db->where('WebNews.status = 0 AND (WebNewsCategory.id IN (\''.implode("','", $cids).'\') OR WebNewsCategory.parentId IN (\''.implode("','", $cids).'\'))');
    			if(!empty($key)){
    				$this->db->where("(WebNews.subject LIKE '%{$key}%' OR WebNews.summary LIKE '%{$key}%' OR WebNews.content LIKE '%{$key}%')");
    			}
    			$query = $this->db->order_by('WebNews.dateline', 'desc')->limit(200)->get('WebNews')->result_array();
    			foreach($query as $row){
    				$data = array(
    					'id'=>$row['id'],
    					'title'=>$row['subject'],
    					'title_link'=>'',
    					'intro'=>$row['summary']
    				);
    				!empty($row['thumb']) && $data['image'] = $row['thumb'];
    				!empty($row['thumb']) && $data['image_url'] = $row['thumb'];//获取图片的访问地址
    		//		$row['create_date'] = gmdate('Y-m-d H:i:s', $row['dateline']);//格式化发布时间
    		//		unset($row);
    				$datas[] = $data;
    			}
    			unset($query);
    			break;
    		case 'post'://点评、签到、图片
    			$this->db->select('Post.*, User.username, User.nickname, Place.placename')->join('WebNewsCategoryData', 'WebNewsCategoryData.postId=Post.id', 'inner')
    						->join('WebNewsCategory', 'WebNewsCategory.id=WebNewsCategoryData.catId', 'inner')
    						->join('User', 'User.id = Post.uid', 'left')
    						->join('Place', 'Place.id = Post.placeId', 'left');
    			$this->db->where('Post.status', 1)->where("FIND_IN_SET('{$fid}', WebNewsCategory.fragmentId)");
    			if(!empty($pt)){
    				$this->db->where('Post.type', $pt);
    			}
    			if(!empty($key)){
    				$this->db->like('Post.content', $key);
    			}
    			$query = $this->db->order_by('Post.createDate', 'desc')->limit(200)->get('Post')->result_array();
    			foreach($query as $row){
    				$data = array(
    					'id'=>$row['id'],
    					'intro'=>$row['content'],
    					'image'=>$row['photoName'],
    					'title_link'=>'http://in.jin95.com/review/'.$row['id'],
    					'title'=>(!empty($row['nickname'])?$row['nickname']:$row['username']).'在'.$row['placename'].($row['type']==1 ? '的签到':'发布的'.($row['type']==2?'点评':'图片'))
    				);
    				!empty($data['image']) && $data['image_url'] = image_url($data['image'], 'user', 'hdp');
    				$datas[] = $data;
    			}
    			unset($query);
    			break;
    		case 'user'://用户，忽略FID
    			$this->db->where('status', 0);
    			if(!empty($key)){
    				switch($pt){
    					case '1':$this->db->like('username', $key);break;
    					case '2':$this->db->like('nickname', $key);break;
    					case '3':$this->db->like('description', $key);break;
    					default:$this->db->where("(username LIKE '%{$key}%' OR nickname LIKE '%{$key}%' OR description LIKE '%{$key}%')");break;
    				}
    			}
    			$query = $this->db->order_by('lastSigninDate', 'desc')->limit(200)->get('User')->result_array();
    			foreach($query as $row){
    				$data = array(
    					'id'=>$row['id'],
    					'title'=>$row['username'],
    					'title_link'=>'http://in.jin95.com/user/'.$row['id'],
    					'intro' => (!empty($row['nickname']) ? '昵称：'.$row['nickname']:'').(!empty($row['description']) ? ';签名：'.$row['description']:'')
    				);
    				$data['image'] = !empty($row['avatar']) ? $row['avatar'] : 'default.png';
    				$data['image_url'] = image_url($data['image'], 'head', 'hhdp');
    				$datas[] = $data;
    			}
    			unset($query);
    			break;
    		case 'place'://地点
    			$this->db->where('Place.status', 0);
    			if(!empty($pt) && $pt === '3')
    				$this->db->join('Brand', 'Brand.id = Place.brandId', 'inner');
    			if(!empty($key)){
    				switch($pt){
    					case '1'://地点名
    						$this->db->like('Place.placename', $key);
    						break;
    					case '2'://地址
    						$this->db->like('Place.address', $key);
    						break;
    					case '3'://品牌商家
    						$this->db->like('Brand.name', $key);
    						break;
    				}
    			}
    			$query = $this->db->order_by('Place.createDate', 'desc')->limit(200)->get('Place')->result_array();
    			foreach($query as $row){
    				$datas[] = array(
    					'id'=>$row['id'],
    					'title'=>$row['placename'],
    					'intro'=>$row['address'],
    					'title_link'=>'http://in.jin95.com/place/'.$row['id']
    				);
    			}
    			unset($query);
    			break;
    		case 'event'://活动
    			if(!empty($key))
    				$this->db->like('name', $key);
    			$query = $this->db->where('status', 0)->order_by('createDate', 'desc')->limit(200)->get('Event')->result_array();
    			foreach($query as $row){
    				$datas[] = array(
    					'id' => $row['id'],
    					'title' => $row['name'],
    					'image' => $row['image'],
    					'image_url' => image_url($row['image'], 'common', 'hdp'),
    					'title_link' => 'http://in.jin95.com/event/index/'.$row['id']
    				);
    			}
    			unset($query);
    			break;
    		case 'groupon'://团购
    			break;
    		default:break;
    	}
    	return $datas;
	}
	
}   
   
 // File end