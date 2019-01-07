<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 频道的几个页面
 * 需要根据域名不同访问的页面
 * Author: piglet chenglin.zhu@gmail.com
 * Created on 2012-12-7
 */

class Channel extends Controller {
	var $_tables;
	var $already = array('auto','f');//f
    /**
     * 构造函数
     * 判断域名，及访问的内容等等，来跳转域名
     */
    function __construct() {
    	//$already = array('auto');//f
        parent::__construct();
        //配置
        $this->_tables = $this->config->item('tables');
        $this->load->helper("channel");
    	$this->load->model('post_model', 'm_post');
    	$this->load->model('webpictureattachment_model', 'm_webpictureattachment');
        // 分类或者频道的ID号
        // 直接域名访问
        $segment = $this->uri->rsegments;
        // 获取频道栏目信息
        $domains = get_inc('domains');
        // 当前HOST的关联频道栏目ID
        $this->domain = $domains[HOST];
        $in_host = $this->config->item('in_host');
        if('index' == $segment[2]) {
            // 访问首页
            if(empty($this->domain)) {
                // 访问首页必须是域名，如果是通过频道ID过来，不能访问，跳转到IN首页
                if(HOST == $in_host) {
                    $this->id = 0;
                } else {
                    redirect(HTTP_SCHEMA_STR . $in_host, '', REDIRECT_CODE); // 最后要改成301
                }
            } else {
            	
                // 去获取频道的ID号
                $this->id = $this->domain['id'];
            }

            // 判断是否通过参数访问过来，首页是不允许的，所以要多跳转一次
            if($this->uri->segments && !in_array($this->uri->segments[1], array('new', 'hot'))) {
                redirect(HTTP_SCHEMA_STR . ($this->id?HOST:$in_host), '', REDIRECT_CODE);
            }
            
            if($this->domain) {
                // 获取频道信息
                $this->categories = get_inc('newscategory');
                //var_dump($this->categories);
                $this->category = $this->categories[$this->id];
                $theme = $this->category['style'];
            }
        } else {
            // 如果访问频道的其他页面
            // 检查传入的id号
            $this->id = intval($segment[3]);
            
            if($this->id > 0) {
                $this->categories = get_inc('newscategory');
                if('article' == $segment[2]) {
                    // 这里是内容的ID号
                    $this->load->model('webnews_model', 'm_webnews');
                    $this->article = $this->m_webnews->select_by_id($this->id);
                    $this->category = $this->categories[$this->article['newsCatId']];
                } else {
                    // 这里是栏目的ID号
                    $this->category = $this->categories[$this->id];
                }
               
                // 判断所在频道
                $category = $this->categories[$this->category['parentId']];
                $this->big_category = $category;
                $host = $category['domain'];
               
                if(empty($host)) {
                    // 如果没有属于频道。那么看是否访问的IN的HOST。如果是，那么继续。如果不是跳到in的HOST下执行
                    if( $segment[2]!='getpostdata' && $segment[2]!='all_posts'){
                    (HOST != $in_host) && redirect(HTTP_SCHEMA_STR . $in_host . '/' . uri_string(), '', REDIRECT_CODE);
                	}
                } else {
                    // 如果不是在本来的频道下
                    if($segment[2]!='all_posts'){
                    ($host != HOST) && redirect(HTTP_SCHEMA_STR . $host . '/' . uri_string(), '', REDIRECT_CODE);
                    }
                }
                $theme = $category['style'];
            } else {
                // 跳转到404
                show_404();
            }
        }
        
        if($this->domain) {
            // 获取频道下的栏目
            $this->assign(array(
                    'sub_cate' => get_channel_category($this->domain, $this->categories),
            		'lil_cate' => get_channel_category($this->domain, $this->categories, 0),
                    'theme' => $theme?$theme:'default',
                    'id' => $this->id,
                    'm' => $segment[2],
            		'channel_id' => $this->big_category['id'] ? $this->big_category['id'] : $this->id,
            		'place_id' => $this->category['placeId'],
                    'in_host' => $in_host
                    ));
        }
    }
    
    /**
     * 频道首页
     * @param int $id 访问首页
     */
    function index($id = 0, $tagid = 0,$sort = 'new',$subpage = 1,$page = 1) {
        $id = formatid($this->id);
        if($id > 0) {
            // 频道
            $this->_channel($id, $tagid,$sort,$subpage,$page);
        } else {
            // in的首页
            $this->_home($page);
        }
    }
    
   
    /**
     * 频道首页
     * @param int $id
     */
    private function _channel($id, $tagid = 0 ,$sort = 'new',$subpage=1,$curr_page = 1) {
    	
    	$this->load->model('webrecommendfragment_model', 'm_webrecommendfragment');
    	$this->load->model("webnewscategorydata_model","m_webnewscategorydata");
        // 获取频道信息
        $this->title = $this->category['catName'];
        //var_dump($title);
        if($this->title=="评房网" || $this->category['id']==3){
        	$this->config->load('config_search');
	        $this->search_config = $this->config->item('search');
		    $this->assign('search_config', $this->search_config);
		    //var_dump($this->search_config);
        }//school_search
        if($this->title=="教育" ){
        	$this->title = '成都教育网';
        	//$this->assign('title','成都教育网');
        	$this->assign('is_edu', 1);
        }
    	if($this->title=="旅游" ){
        	$this->assign('is_ly', 1);
        }
        
	    
        $this->keywords = $this->category['catName'] . ',' . $this->category['keywords'];
        $this->description = $this->category['description'];
        
        // 获取推荐内容
        //$hot = array('推荐的内容'); //'推荐的内容' 
        //标签
        
        $sidebar_data = array();
       
        //因为需要排序,不能直接用fragmentId字段的内容,需要再查询一次
        $fragments = $this->category['fragmentId'];
        
        $fragments_data_list = $this->m_webrecommendfragment->list_in_fid_order_ordervalue_desc(explode(",",$fragments));
        $hot = '';
        $banner_ad = array();
        $banner_large_ad = array();
        $banner_middle_ad = array();
        $couplet_left_ad = 0;
        $couplet_right_ad = 0;
        $v3_fragments = array();
        foreach($fragments_data_list as $row){
        	
        	if(!$row['fid']) continue;
        	$tmp_data = get_data("fragmentdata",$row['fid']);
        	//是否有头条或者幻灯
        	if($tmp_data['frag']['style']=="focus" || $tmp_data['frag']['style']=="headline" || $tmp_data['frag']['style']=="headline_movie" || $tmp_data['frag']['style']=="headline_edu"){
        		//$hot = array(true);
        		switch($tmp_data['frag']['style']){
        			case "focus":
        				empty($hot['focus']) && $hot['focus'] = $tmp_data;
        				break;
        			case "headline":
        				empty($hot['headline']) && $hot['headline'] = $tmp_data;
        				break;
        			case "headline_movie":
        				empty($hot['headline_movie']) && $hot['headline_movie'] = $tmp_data;
        				break;
        			case "headline_edu":
        				empty($hot['headline_edu']) && $hot['headline_edu'] = $tmp_data;
        				break;
        		}
        	}
        	if($tmp_data['frag']['style']=="fullscreen" && empty($bg)){
        		$bg = $tmp_data;
        	}
        	if($tmp_data['frag']['style']=="f_tf" && empty($f_tf)){
        		$f_tf = $tmp_data;
        	}
        	
        	if(strpos($tmp_data['frag']['style'],'v3_') > 0 || strpos($tmp_data['frag']['style'],'v3_') === 0){
        		$v3_fragments[$tmp_data['frag']['style']] =  $tmp_data;
        	}
        	
        	if($tmp_data['frag']['style']=="tuangou" && empty($tuangou)){
        		$tuangou = $tmp_data;
        	}// 动态碎片
        	if($tmp_data['frag']['style']=="huodong" && empty($huodong)){
        		$huodong = $tmp_data;
        	}// 动态碎片
        	
       		if($tmp_data['frag']['style']=="banner_ad"){
        		$banner_ad[] = $tmp_data['frag'];
        	}
        	if($tmp_data['frag']['style']=="halfing_ad_left"){
        		$halfing_ad_left[] = $tmp_data['frag'];
        	}
        	if($tmp_data['frag']['style']=="halfing_ad_right"){
        		$halfing_ad_right[] = $tmp_data['frag'];
        	}

       		if($tmp_data['frag']['style']=="banner_large_ad"){
        		$banner_large_ad[] = $tmp_data['frag'];
        	}

       		if($tmp_data['frag']['style']=="banner_middle_ad"){
        		$banner_middle_ad[] = $tmp_data['frag'];
        	}
	        if($tmp_data['frag']['style']=="banner_middle_ad2"){
	        		$banner_middle_ad2[] = $tmp_data['frag'];
	        }
	        if($tmp_data['frag']['style']=="banner_middle_ad3"){
	        		$banner_middle_ad3[] = $tmp_data['frag'];
	        }
	        if($tmp_data['frag']['style']=="banner_middle_ad4"){
	        		$banner_middle_ad4[] = $tmp_data['frag'];
	        }
	        if($tmp_data['frag']['style']=="banner_middle_ad5"){
	        		$banner_middle_ad5[] = $tmp_data['frag'];
	        }
	        if($tmp_data['frag']['style']=="banner_middle_ad6"){
	        		$banner_middle_ad6[] = $tmp_data['frag'];
	        }
	        if($tmp_data['frag']['style']=="banner_middle_ad7"){
	        		$banner_middle_ad7[] = $tmp_data['frag'];
	        }
	        if($tmp_data['frag']['style']=="banner_middle_ad8"){
	        		$banner_middle_ad8[] = $tmp_data['frag'];
	        }
	        if($tmp_data['frag']['style']=="banner_middle_ad9"){
	        		$banner_middle_ad9[] = $tmp_data['frag'];
        	}

       		if($tmp_data['frag']['style']=="couplet_left_ad"){
        		$couplet_left_ad = $tmp_data['frag'];
        	}
        	
        	if($tmp_data['frag']['style']=="back_ad"){
        		$back_ad = $tmp_data['frag'];
        	}
        	
        	if($tmp_data['frag']['style']=="back_ad_top"){
        		$back_ad_top = $tmp_data['frag'];
        	}
        	
        	if($tmp_data['frag']['style']=="pop_ad"){
        		$ad_pop = $tmp_data;
        	}

       		if($tmp_data['frag']['style']=="couplet_right_ad"){
        		$couplet_right_ad= $tmp_data['frag'];
        	}
        	
        	if($tmp_data['frag']['style']=="big_newslist"){
        		$big_newslist = $tmp_data;
        	}
        	
        	if($tmp_data['frag']['style']=="edu"){
        		$edu = $tmp_data;
        		$this->assign("has_edu",true);
        	}
        	//search_school
        	if($tmp_data['frag']['style']=="search_school"){
        		$search_school = $tmp_data;
        		//$this->assign("has_edu",true);
        	}
        	
        	if($tmp_data['frag']['style']=="v3_huodong"){
        		$this->load->model('webevent_model', 'm_webevent');
        		foreach($tmp_data['data'] as &$ev){
        			$extraData = $ev['extraData'];
        			$tem_event = $this->m_webevent->select_by_id ( $extraData['itemid']['data'] );
        			$ev['startDate'] = date('Y-m-d',strtotime($tem_event['startDate']));
        			$ev['endDate'] = date('Y-m-d',strtotime($tem_event['endDate']));
        			$ev['hits'] = $tem_event['hits'];
        			$event_property = json_decode($tem_event['applyProperty'],true);
        				
        			$ev['form'] = $event_property['form'] ? 1 : 0;
        			unset($extraData,$tem_event);
        		}
        		$v3_fragments[$tmp_data['frag']['style']] = $tmp_data;
        	}
			
        	if($tmp_data['frag']['fregType']==1){
        		$tmp_arr['frag'] = $tmp_data['frag'];
	        	$tmp_arr['data'] = get_active_source($tmp_data['frag']['dataSource']);  		
        		/*if(strpos($tmp_data['frag']['style'],'v3_') > 0 || strpos($tmp_data['frag']['style'],'v3_') === 0){ //暂时没有V3的需要这样处理的东西
	        		$v3_fragments[$tmp_data['frag']['style']] = $tmp_arr;
        		}else{	*/
	        		$sidebar_data[] = $tmp_arr;
        		//}
        	}
        	else{
        		$sidebar_data[] = $tmp_data;
        	}
        }
       
        
        $this->db->select('distinct(t.id),t.content');
        $this->db->from('Tag t');
        $this->db->join('WebNewsCategoryOwnTag cot','cot.tagId=t.id and cot.channelId='.$id.' and tagType=0','inner');
        $tags = $this->db->get()->result_array();
        
        $arr_size = count($sidebar_data);
        
        $tagid && $where = " and pot.tagId=".$tagid;
        
      	$catid = $this->uri->segments[2] ? $this->uri->segments[2] : $id;
        if('new' == $sort) {
            $count_post_data = $this->m_webnewscategorydata->count_post_data($catid,$where);
            $postdata = $this->post_data($catid,$tagid,$subpage,$curr_page,$sort,false,$where);
        } else {
            // 去缓存读取
            // 这里临时先用xcache去保存
            $postdata_id = sprintf('pagedata_%s_%s_%s_%s_%s', $catid, $tagid, $subpage, $curr_page, $sort);
            $cache_postdata = @xcache_get($postdata_id);
            if(empty($cache_postdata)) {
                $cache_postdata = array();
                $cache_postdata['count_post_data'] = $this->m_webnewscategorydata->count_post_data($catid,$where);
                $cache_postdata['postdata'] = $this->post_data($catid,$tagid,$subpage,$curr_page,$sort,false,$where);
                @xcache_set($postdata_id, $cache_postdata, 1800);
            }
            $count_post_data = $cache_postdata['count_post_data'];
            $postdata = $cache_postdata['postdata'];
        }

        $page = $this->pagination_rolling($catid,$count_post_data,$tagid,$subpage,$curr_page,5,$sort,"index",false);
     	
        
        $this->assign(compact('hot' , 'sidebar_data' ,'bg' ,'page'  ,'postdata','sort_link','sort','f_tf',
        			'banner_ad','banner_large_ad','banner_middle_ad','couplet_left_ad','couplet_right_ad',
        			'big_newslist','is_edu','edu','search_school','back_ad','back_ad_top','v3_fragments',
        			'tags','id','tagid','ad_pop','banner_middle_ad2','banner_middle_ad3','banner_middle_ad4',
        			'banner_middle_ad5','banner_middle_ad6','banner_middle_ad7','banner_middle_ad8','banner_middle_ad9',
        			'halfing_ad_left','halfing_ad_right'));
        $style = $this->category['style'] ? $this->category['style'] : $this->big_category['style'];
        
        if(!in_array($style,$this->already)) $style = "";
        $this->display('index',$style);
    }
    function getpostdata($id, $tagid = 0 ,$sort = 'new',$subpage=1,$curr_page = 1) {
    	$this->load->model('webrecommendfragment_model', 'm_webrecommendfragment');
    	$this->load->model("webnewscategorydata_model","m_webnewscategorydata");
    	$catid = $this->uri->segments[3] ? $this->uri->segments[3] : $id;
    	$tagid && $where = " and pot.tagId=".$tagid;
    	
    	if('new' == $sort) {
            $count_post_data = $this->m_webnewscategorydata->count_post_data($catid,$where);
            $postdata = $this->post_data($catid,$tagid,$subpage,$curr_page,$sort,false,$where);
        } else {
            // 去缓存读取
            // 这里临时先用xcache去保存
            $postdata_id = sprintf('pagedata_%s_%s_%s_%s_%s', $catid, $tagid, $subpage, $curr_page, $sort);
            $cache_postdata = @xcache_get($postdata_id);
            if(empty($cache_postdata)) {
                $cache_postdata = array();
                $cache_postdata['count_post_data'] = $this->m_webnewscategorydata->count_post_data($catid,$where);
                $cache_postdata['postdata'] = $this->post_data($catid,$tagid,$subpage,$curr_page,$sort,false,$where);
                @xcache_set($postdata_id, $cache_postdata, 1800);
            }
            $count_post_data = $cache_postdata['count_post_data'];
            $postdata = $cache_postdata['postdata'];
        }

        $page = $this->pagination_rolling($catid,$count_post_data,$tagid,$subpage,$curr_page,5,$sort,"index",false);
    	
        $this->assign(compact('postdata','page'));
       
        $this->display('waterfall','channel');
    }
    
    /**
     * 网站首页
     */
    private function _home($page=1) {    	
    	//查询图片总数
    	/*$condition = array(
    		'where'=>array($this->_tables['post'].'.type <='=>4,$this->_tables['post'].'.photo <> '=>'', $this->_tables['post'].'.status'=>1)
    	); // status < 2
    	$count = $this->m_post->count_post($condition);
    	if($count){
    		//分页
    		$parr = $this->paginate('/new', $count, $page, array('id'=>0));
    		if($page <= $parr['total_page']){
	    		//数据
	    		$condition['limit']=array('size'=>$parr['size'], 'offset'=>$parr['offset']);
	    		$list = $this->m_post->list_post($condition, 'thweb');
    		}
    	}
    	empty($list) && $list = array();*/
    	
    	/*new home page*/
    	$this->load->model("homepagedata_model","m_home");
    	$this->load->model("post_model","m_post");
    	$this->config->load("config_home");
    	
    	$total = 30;//60;
    	$users = $this->m_home->getHomePageData(4,10); 
    	$count_u = count($users);   	
    	$homes = $this->m_home->getHomePageData(array(19 , 20 , 5),$total - $count_u);   
    	shuffle($homes);
    	$homes = array_merge($users,$homes); 	
    	$home_conf = $this->config->item("home_post_condition");
    	//取40条post
    	//$curr_time = date("Y-m-d H:i:s",TIMESTAMP); 
    	$index_post_pagesize = 8;
    	$offset = ($page-1)*$index_post_pagesize;
    	
    	if($offset<$home_conf['total']){
	    	$condition['where'] = array(
	    		'photo <> ' => '', 
	    		'type <=' => 4,
	    		'isEssence' => 1,
	    		'status' => 1 ,
	    		'praiseCount >= ' => $home_conf['praised']
	    	);
	    	
	    	$condition['limit'] = array('size'=>$index_post_pagesize,'offset'=>$offset);
	    	$condition['order_by'] = array('createDate','desc');
	    	
	    	$home_post = $this->m_post->list_post($condition,'thweb');
    	}
    	
       
        $this->config->load('config_vote');
		$wap2web = $this->config->item('wap2web');
        $home_data = array();
        
        foreach($homes as $k => $row){
        	//poi-1 不能large 4-user只能small
        	$home_data[$k]['title'] = $row['content'];
        	switch($row['itemType']){
        		case 19:
        		case 20:
        		case 5:
        			$type = 'large,middle,long';
        			$home_data[$k]['image'] = image_url($row['image'],'home','odp');
        			break;
        		case 1:
        			$type = 'small';
        			$home_data[$k]['image'] = image_url($row['image'],'home','mhomedp');
        			break;
        		case 4:
        			$type = 'small';
        			$home_data[$k]['image'] = image_url($row['image'],'home','odp');
        			break;
        	}
        	$home_data[$k]['type'] = $type;
        	/*$home_data[$k]['large'] = image_url($row['image'],'home','uhomedp');
        	$home_data[$k]['middle'] = image_url($row['image'],'home','hhomedp');
        	$home_data[$k]['small'] = image_url($row['image'],'home','mhomedp');
        	$home_data[$k]['long'] = image_url($row['image'],'home','hhomedp');*/
        	$home_data[$k]['link'] = $row['hyperLink']; 
        	$home_data[$k]['praiseCount'] = $row['praiseCount']; 
        	foreach($wap2web as $key => $w2w){       		
        		if(stripos($row['hyperLink'],$key)!==false){
        			$id = str_replace($key,'',$row['hyperLink']);
        			$home_data[$k]['link'] = $w2w.'/'.$id; 
        		}
        	}       	
        }
        $home_data = encode_json($home_data);
       
        //碎片数据 获取碎片。。
        //首页推荐内容的ID = 86
        $home_page_catid = $this->config->item('homepage_category_id');
        $categories = get_inc('newscategory');
        
        $this->load->model('webrecommendfragment_model', 'm_webrecommendfragment');
        $this->load->model('webevent_model', 'm_webevent');
        $fragments = $categories[$home_page_catid]['fragmentId'];
        //echo "<!-- {$this->config->item('homepage_category_id')} ： {$fragments} -->";
        $fragments_data_list = $this->m_webrecommendfragment->list_in_fid_order_ordervalue_desc(explode(",",$fragments));
        foreach($fragments_data_list as $row){
        	if(!$row['fid']) continue;
        	$tmp_data = get_data("fragmentdata",$row['fid']);
        	
        	switch($tmp_data['frag']['style']){
        		case 'index3_event':
        			foreach($tmp_data['data'] as &$ev){
        				$extraData = $ev['extraData'];
        				$tem_event = $this->m_webevent->select_by_id ( $extraData['itemid']['data'] );
        				$ev['startDate'] = date('Y-m-d',strtotime($tem_event['startDate']));
        				$ev['endDate'] = date('Y-m-d',strtotime($tem_event['endDate']));
        				$ev['hits'] = $tem_event['hits'];
        				$event_property = json_decode($tem_event['applyProperty'],true);
        				
        				$ev['form'] = $event_property['form'] ? 1 : 0;
        				unset($extraData,$tem_event);
        			}
        			$event = $tmp_data['data'];
        			break;
        		case 'index3_placecoll' :
        			foreach($tmp_data['data'] as &$pc){
        				$extraData = $pc['extraData'];
        				$tem_pc = $this->db->select('uid')->where('id',$extraData['itemid']['data'])->get($this->_tables['placecollection'])->row_array();
        				$user = get_data('user',$tem_pc['uid']);
        				$pc['nickname'] = $user['name'];
        				$pc['uid'] = $user['id'];
        				$pc['avatar'] = $user['avatar_m'];
        				unset($extraData,$tem_pc,$user);
        			}
        			$placecoll = $tmp_data;
        			break;
        		case 'index3_brand_ppt' :
        			$brand_ppt = $tmp_data['data'];
        			break;
        		case 'index3_brand_news' :
        			$brand_news = $tmp_data['data'];
        			break;
        		case 'index3_brand_newbe' :
        			$brand_newbe = $tmp_data['data'];
        			break;
        	}
        }
        
        //本月达人
        /*$topuser = $this->m_home->getHomePageData(4,10);    
        foreach($topuser as &$tu){
        	$user = get_data('user',$tu['itemId']);
        	$tu['description'] = $user['description'];
        	$tu['avatar'] = $user['avatar_m'];
        	$tu['name'] = $user['name'];        	
        }*/
       	$this->load->model("recommenddata_model","m_recommenddata");
        $topuser = $this->m_recommenddata->list_by_fid_order_serialNo_asc(32);
        foreach($topuser as &$tu){
        	$user = get_data('user',$tu['dataId']);
        	//$tu['uid'] = $user['id'];
        	$tu['itemId'] = $user['id'];
        	$tu['description'] = $user['description'];
        	$tu['avatar'] = $user['avatar_m'];
        	$tu['name'] = $user['name'];
        }
        //var_dump($topuser);
        
        //随机2个道具
        $this->db->where_in('level',array(1,3));
        $item_total = $this->db->count_all_results($this->_tables['item']);
        $rand_offset = rand(0,$item_total-3);
        $items = $this->db->where_in('level',array(0,3))->limit(2,$rand_offset)->get($this->_tables['item'])->result_array();
        
        //5个积分存量最多的地点
        $place_rob_most = $this->db->where('status',0)->order_by('point','desc')->limit(5)->get($this->_tables['place'])->result_array();
        foreach($place_rob_most as &$row){
        	$this_place = get_data('place',$row['id']);
        	$mayor_list = $this->db
        					   ->select('distinct uid',false)
        					   ->where('placeId',$row['id'])
        					   ->where('success',1)
        					   ->order_by('createDate','desc')
        					   ->limit(3)
        					   ->get($this->_tables['placerob'])
        					   ->result_array();
        	$mayors = array();
       		foreach($mayor_list as $user){
       				$mayors [] = get_data('user',$user['uid']);
       		}
       		$row['icon'] = $this_place['icon'];
       		$row['mayors'] = $mayors;
        }
        
        //3个积分票获得 
        $top3pointTickeet = get_data_ttl('index_usePointTicket',3,300);
        
    	/*new home page*/
    	//显示
    	$this->assign(compact('count', 'list','home_data','event','topuser','items','place_rob_most','top3pointTickeet','placecoll','brand_ppt','brand_news','brand_newbe','home_post'));
    	$this->display('index3.0', 'web');
    }
    
    /**
     * 栏目
     * @param int $id 栏目ID号
     */
    function category($id,$tagid = 0,$sort = "hot",$subpage = 1,$curr_page=1) {
    	$this->load->model('webrecommendfragment_model', 'm_webrecommendfragment');
    	$this->load->model("webnewscategorydata_model","m_webnewscategorydata");
        // 获取栏目信息
        $this->title = $this->big_category['catName'] . ' - ' . $this->category['catName'];
        $this->keywords = $this->category['keywords']?$this->category['keywords']:$this->category['catName'];
        $this->description = $this->category['description']?$this->category['description']:$this->category['catName'];
        
        /*
        $sidebar_data = array();
        
        $big_fragments = $this->big_category['fragmentId'];
        $this_fragments = $this->category['fragmentId'];
       
        if(!empty($this_fragments)){
        	$fragments = $this_fragments;
        	$is_this_frag = true; //当字栏目有设置的时候才取幻灯和头条
        }
        else{
        	$fragments = $big_fragments;
        }
        $v3_fragments = array();
        $fragments_data_list = $this->m_webrecommendfragment->list_in_fid_order_ordervalue_desc(explode(",",$fragments));
        foreach($fragments_data_list as $row){
        	if(!$row['fid']) continue;
        	$tmp_data = get_data("fragmentdata",$row['fid']);
        	
        	if(($tmp_data['frag']['style']=="focus" || $tmp_data['frag']['style']=="headline" || $tmp_data['frag']['style']=="headline_movie" || $tmp_data['frag']['style']=="headline_edu") && $is_this_frag){
        		//$hot = array(true);
        		switch($tmp_data['frag']['style']){
        			case "focus":
        				empty($hot['focus']) && $hot['focus'] = $tmp_data;
        				break;
        			case "headline":
        				empty($hot['headline']) && $hot['headline'] = $tmp_data;
        				break;
        			case "headline_movie":
        				empty($hot['headline_movie']) && $hot['headline_movie'] = $tmp_data;
        				break;
        			case "headline_edu":
        				empty($hot['headline_edu']) && $hot['headline_edu'] = $tmp_data;
        				break;
        		}
        	}
       	 	if($tmp_data['frag']['style']=="fullscreen" && empty($bg) && $is_this_frag){
        		$bg = $tmp_data;
        	}
        	
        	if(strpos($tmp_data['frag']['style'],'v3_') > 0 || strpos($tmp_data['frag']['style'],'v3_') === 0){
        		$v3_fragments[$tmp_data['frag']['style']] =  $tmp_data;
        	}
        	if($tmp_data['frag']['fregType']==1){
        		$tmp_arr['frag'] = $tmp_data['frag'];
        		$tmp_arr['data'] = get_active_source($tmp_data['frag']['dataSource']);//decode_json(curl_get_contents($tmp_data['frag']['dataSource']));//        		
        		$sidebar_data[] = $tmp_arr;
        	}
        	else{
        		$sidebar_data[] = $tmp_data;
        	}
        }
        $arr_size = count($sidebar_data);
        //$bg = $sidebar_data[$arr_size-3];//get_data("fragmentdata",7); //按频道取背景。。。暂时7 对应汽车频道的背景
        */ //post列表栏目不再有碎片，并且弱化栏目属性，数据全部取频道的数据
        //var_dump($this->big_category);
        //查询出频道所有的标签，备选
        $style =  $this->big_category['style'];
        $id = in_array($style,$this->already) ? $this->big_category['id'] : $this->category['id'];
        //var_dump($this->big_category);exit;
        
        $catid = $this->id;
       
        $catname =  $this->category['catName'];
        $this->db->select('distinct(t.id),t.content');
        $this->db->from('Tag t');
        $this->db->join('WebNewsCategoryOwnTag cot','cot.tagId=t.id and cot.channelId='.$id.' and tagType=0','inner');
        $tags = $this->db->get()->result_array();
        
        $tagid && $where = " and pot.tagId=".$tagid;
        
        //数据取大频道，分页用小频道
        $page = $this->pagination_rolling($catid,$this->m_webnewscategorydata->count_post_data($id,$where),$tagid,$subpage,$curr_page,5,$sort."/".$subpage,"category",false);
        $postdata = $this->post_data($id,$tagid,$subpage,$curr_page,$sort,false,$where);
        
        //$sort_link['new'] = "/category/{$id}/{$tagid}/new/1";
        //$sort_link['hot'] = "/category/{$id}/{$tagid}/hot/1";
        $inner_cate = true;
        $category = $this->category;
        $this->assign(compact('sidebar_data','page' ,'postdata','sort_link','sort','hot','bg','inner_cate','tags','catid','catname','id','tagid','category','curr_page')); //inner_cate
       
        
        if(in_array($style,$this->already)) {
        	$this->display('topic','channel'/*$style*/);
        }
        else{
        	$this->display('index');
        }
    }
    
    /**
     * 新闻列表页
     * @param int $id 栏目ID号
     * @param int $page 页码
     */
    function news_list($id, $page = 1) {
    	if(!$this->big_category){
    		 show_404();
    	}
    	$this->load->model('webnews_model', 'm_webnews');
    	
    	$this->load->model('webrecommendfragment_model', 'm_webrecommendfragment');
    	$this->load->model("webnewscategorydata_model","m_webnewscategorydata");
    	
    	$category = $this->category;
    	$sidebar_data = array();
    	$big_fragments = $this->big_category['fragmentId'];
        $this_fragments = $this->category['fragmentId'];
       
        if(!empty($this_fragments)){
        	$fragments = $this_fragments;
        	//$is_this_frag = true; //当字栏目有设置的时候才取幻灯和头条
        }
        else{
        	$fragments = $big_fragments;
        }
       // $fragments = $this->big_category['fragmentId'];
		
        //var_dump($this->category['style']);
        $v3_fragments = array();
        $fragments_data_list = $this->m_webrecommendfragment->list_in_fid_order_ordervalue_desc(explode(",",$fragments));
        foreach($fragments_data_list as $row){
       		$tmp_data = get_data("fragmentdata",$row['fid']);
        	if(strpos($tmp_data['frag']['style'],'v3_') > 0 || strpos($tmp_data['frag']['style'],'v3_') === 0){
        		$v3_fragments[$tmp_data['frag']['style']] =  $tmp_data;
        	}
        	if($tmp_data['frag']['fregType']==1){
        		$tmp_arr['frag'] = $tmp_data['frag'];
        		$tmp_arr['data'] = get_active_source($tmp_data['frag']['dataSource']);//decode_json(curl_get_contents($tmp_data['frag']['dataSource']));//        		
        		$sidebar_data[] = $tmp_arr;
        	}
        	else{
        		$sidebar_data[] = $tmp_data;
        	}
        }
        $pagesize=20;
        $page = $page<1 ? 1 : $page;
        $total = $this->m_webnews->count_news($id);
        
        
        $this->paginate_style2('/nlist/'.$id,$total,$page,20,5);
        
        /*$this->load->library('pagination');

		$config['base_url'] = '/nlist/'.$id;
		$config['total_rows'] = $total;
		$config['per_page'] = 20;
		$config['use_page_numbers'] = true;
		$config['num_tag_open'] = '<li>';
		$config['num_tag_close'] = '</li>';
		$config['prev_tag_open'] = '<li>';
		$config['prev_tag_close'] = '</li>';
		$config['next_tag_open'] = '<li>';
		$config['next_tag_close'] = '</li>';
		$config['cur_tag_open'] = '<li class="active"><a href="javascript:;">';
		$config['cur_tag_close'] = '</a></li>';
		$config['first_link'] = false;
		$config['last_link'] = false;
		$config['prev_link'] = "上一页";
		$config['next_link'] = "下一页";
		
		$this->pagination->initialize($config);
		
		$pagination = $this->pagination->create_links();*/
		
		$list = $this->m_webnews->news_list($id,$page,$pagesize);
		$channel = $this->big_category;
		// 获取栏目信息
		$this->title = $this->big_category['catName'] . ' - ' . $this->category['catName'];
		$this->keywords = $this->category['keywords']?$this->category['keywords']:$this->category['catName'];
		$this->description = $this->category['description']?$this->category['description']:$this->category['catName'];
		
    	$this->assign(compact('sidebar_data','pagination' ,'category','channel','list','v3_fragments'));
    	$style = $this->big_category['style'] ;
        if(!in_array($style,$this->already)) $style = "";
    	$template = $this->category['style'] ? $this->category['style'] :'list';
        $this->display($template,$style);
    }
    
    /**
     * 新闻页面
     * @param int $id 新闻ID号
     * @param int $page 页码
     */
    function article($id, $page = 1 ) {
    	
    	//$page = intval($page);
    	$page = $page<1 ? 1 : $page;
    	$detail = $this->article;
    	
    	if($detail['status']==2 || empty($detail)){
    		show_404();
    	}
    	//var_dump($detail['newsType']); 
    	if($detail['newsType']==2){
    		header("Location:".$detail['linkuri']);
    	}
    	
    	$fragments = $this->category['fragmentId'];
    	
    	if($fragments){
    		$this->load->model('webrecommendfragment_model', 'm_webrecommendfragment');
    		
	   		$fragments_data_list = $this->m_webrecommendfragment->list_in_fid_order_ordervalue_desc(explode(",",$fragments));
	        foreach($fragments_data_list as $row){
	       		$tmp_data = get_data("fragmentdata",$row['fid']);
	        	if($tmp_data['frag']['fregType']==1){
	        		$tmp_arr['frag'] = $tmp_data['frag'];
	        		$tmp_arr['data'] = get_active_source($tmp_data['frag']['dataSource']);//decode_json(curl_get_contents($tmp_data['frag']['dataSource']));//        		
	        		$sidebar_data[] = $tmp_arr;
	        	}
	        	else{
	        		$sidebar_data[] = $tmp_data;
	        	}
	        	
	        }
    	}
    	
    	
    	$content_arr =  preg_split("/\[next\]|\[pagenext\]/",$detail['content']); //explode("[next]",$detail['content']);
    	$config = array('show-body-only'=>true);
    	$detail['content'] = @tidy_repair_string($content_arr[($page-1)],$config,'utf8');//$content_arr[($page-1)];
    	
    	//图片新闻
    	if($detail['newsType']==1){
    		//读取附件
    		$attach = $this->m_webpictureattachment->list_by_itemid_order_id($detail['id']);
    		$this->assign("attach",$attach);
    	}
    	
    	//分页
    	$total_page = count($content_arr);
    	$page_string = "";
    	if($total_page>1){
	    	$page_string = "<ul>";
	    	for($i=1;$i<=$total_page;$i++){
	    		$class = $page==$i ? "class='active'": "";
	    		$page_string .= "<li {$class} ><a href='/article/{$id}/{$i}'>{$i}</a></li>";
	    	}
	     	$page_string .= "</ul>";;
    	}
    	
    	$set = array("hitCount"=>$detail['hitCount']+1);
    	$this->m_webnews->update_by_id($id,$set);
    	
    	$category = $this->category;
    	$channel = $this->big_category;
    	$prev = $this->m_webnews->prev_news($this->category['id'],$id);
    	$next = $this->m_webnews->next_news($this->category['id'],$id);
    	
    	//点击量热门排行
    	$hotnews = $this->m_webnews->hot_news($this->category['id']);
    	//相关讯息,读取有图片的新闻,如果全部木有图片,不显示整个板块
    	$related = $this->m_webnews->related_news($this->category['id'],$id);
    	
    	//新闻关联的地点的内容
    	$location = $this->m_webnews->location_info($detail['id']);
    	
    	if($detail['relatedNews']){
    		$news = explode(",",$detail['relatedNews']);
    		$relatedNews = $this->db->where_in('id',$news)->get('WebNews')->result_array();
    	}
    	
    	// 获取栏目信息
    	$this->title = $detail['subject'] . ' - ' . $this->category['catName'];
    	$this->keywords = $detail['keywords']?$detail['keywords']:($detail['subject'] . ',' . $this->category['catName']);
    	$this->description = $detail['summary']?$detail['summary']:$detail['subject'];
    	
    	$this->assign(compact('sidebar_data','detail' ,'page_string','category','channel','prev','next' ,'hotnews','related','location' ,'goupon','relatedNews'));
        $this->display("detail");
    }
    
    function preview(){
    	$detail = array(
    		'subject' => $this->input->post("subject"),
			'summary' => $this->input->post("summary"),
			'content' => $this->input->post("content"),
			'thumb' => $this->input->post("thumb"),
			'newsType' => $this->input->post("newsType"),
			'dateline' => time(),
			'newsCatId' => $this->input->post("newsCatId"),
			'source' => $this->input->post("source"),
			'editor' => $this->input->post("editor"),
			'status' => $this->input->post("status"),
			'keywords' => $this->input->post("keywords")
    	);
    	
    	$this->assign(compact('detail'));
    	$this->display("preview");
    }
    
    /**
     * 地点页
     * 这个方法不用去判断域名，用那个域名访问过来就用它了
     * @param int $id 地点分类ID号
     * @param int $page 页码
     */
    function place($catid , $id , $page = 1) {
        $this->load->model("placeowncategory_model","m_placeowncategory");
        $page = $page<1 ? 1 : $page;
        $pagesize = 7;
        $placeid_list = $this->m_placeowncategory->list_by_placecategoryid($id,$pagesize,($page-1)*$pagesize);
        $total =  $this->m_placeowncategory->count_by_placecategoryid($id);
       
        $this->load->model('webrecommendfragment_model', 'm_webrecommendfragment');
    	$this->load->model("webnewscategorydata_model","m_webnewscategorydata");
    	
        $places = array();
        foreach($placeid_list as $k=>$v){
        	$places[] = $v['placeId'];
        }
  		
        $place = get_data("place",$places);
        
    	$big_fragments = $this->big_category['fragmentId'];
        $this_fragments = $this->category['fragmentId'];
        if(!empty($this_fragments)){
        	$fragments = $this_fragments;
        }
        else{
        	$fragments = $big_fragments;
        }
    	$v3_fragments = array();
        $fragments_data_list = $this->m_webrecommendfragment->list_in_fid_order_ordervalue_desc(explode(",",$fragments));
        foreach($fragments_data_list as $row){
       		$tmp_data = get_data("fragmentdata",$row['fid']);
        	if(strpos($tmp_data['frag']['style'],'v3_') > 0 || strpos($tmp_data['frag']['style'],'v3_') === 0){
        		$v3_fragments[$tmp_data['frag']['style']] =  $tmp_data;
        	}
        	if($tmp_data['frag']['fregType']==1){
        		$tmp_arr['frag'] = $tmp_data['frag'];
        		$tmp_arr['data'] = get_active_source($tmp_data['frag']['dataSource']);//decode_json(curl_get_contents($tmp_data['frag']['dataSource']));//        		
        		$sidebar_data[] = $tmp_arr;
        	}
        	else{
        		$sidebar_data[] = $tmp_data;
        	}
        }
        
        /*$this->load->library('pagination');
		$config['base_url'] = "/channel/place/{$catid}/{$id}/";
		$config['total_rows'] = $total;
		$config['per_page'] = $pagesize;
		
		//$config['num_links'] = 3;
		$config['cur_page'] = $page;
		$config['use_page_numbers'] = true;
		$config['num_tag_open'] = '<li >';
		$config['num_tag_close'] = '</li>';
		$config['prev_tag_open'] = '<li>';
		$config['prev_tag_close'] = '</li>';
		$config['next_tag_open'] = '<li>';
		$config['next_tag_close'] = '</li>';
		$config['cur_tag_open'] = '<li class="active"><a href="javascript:;">';
		$config['cur_tag_close'] = '</a></li>';
		$config['first_link'] = false;
		$config['last_link'] = false;
		$config['prev_link'] = "上一页";
		$config['next_link'] = "下一页";
		
		$this->pagination->initialize($config);
		
		$pagination = $this->pagination->create_links();*/
        $this->paginate_style2("/channel/place/{$catid}/{$id}",$total,$page,7,3);
        
        $category = $this->category;
       
		$this->assign(compact('title', 'keywords', 'description' , 'pagination' ,'place','c_name','category','v3_fragments'));
		$this->display("map");
    }
    
    /**
     * post数据
     * @param int $catid 分类id
     * @param int $subpage 小分页
     * @param int $page 大分页
     * @param int $orderby new/hot
     */
    function post_data($catid,$tagid=0,$subpage=1,$page=1,$orderby="hot",$json=true,$where = ''){
    	
    	$subpage = $subpage<1 ?  1 : $subpage;
    	$page = $page<1 ?  1 : $page;
    	
    	$this->load->model("webnewscategorydata_model","m_webnewscategorydata");
    	$result = $this->m_webnewscategorydata->get_post_data($catid,$subpage,$page,$orderby,50,10,$where);
    	
    	//$pagination = $this->pagination_rolling($catid,$result['total'],$tagid,$subpage,$page,5,$orderby,$json);
    	//$postdata = $this->post($id,1,1,"new",false);
    	
    	
    	$data = array("data"=>$result,"pagination"=>$pagination);
    	
    	if($json){
    		$this->assign("page",$pagination);
	    	$this->assign("postdata",$data);
	    	$this->display("post_item");
    	}
    	else{
    		return $data;
    	}
    	
    }
    
    function index_post($page = 1){
    	
    	$this->load->model("post_model","m_post");
    	$this->config->load("config_home");
    	$home_conf = $this->config->item("home_post_condition");
    	$index_post_pagesize = 8;
    	$offset = ($page-1)*$index_post_pagesize;
    	
    	if($offset<$home_conf['total']){
	    	$condition['where'] = array(
	    		'photo <> ' => '', 
	    		'type <=' => 4,
	    		'isEssence' => 1,
	    		'status' => 1 ,
	    		'praiseCount >= ' => $home_conf['praised']
	    	);
	    	
	    	$condition['limit'] = array('size'=>$index_post_pagesize,'offset'=>$offset);
	    	$condition['order_by'] = array('createDate','desc');
	    	
	    	$home_post = $this->m_post->list_post($condition,'thweb');
    	}
    	
    	
    	$this->assign(compact('home_post'));
    	$this->display('index_post','web');
    }
    
    
    /**
     * 
     * 
     * @param $subpage 当前小分页
     * @param $page 当前大分页
     * @param $size 每次显示的分页数
     * @return html
     */
    private function pagination_rolling($catid,$total,$tagid=0,$subpage=1,$page=1,$size=5,$tail,$type="index",$encode=true,$page_mark='#waterfall'){
    	$pagesize=50;
    	$pages = ceil($total/$pagesize);
    	//domain/hot/4/1
    	//domain/category/27/hot/1
    	//echo "p:".$pages;
    	$tagid = intval($tagid);
    	if($type=="index"){
    		$url_base = '/'.$tail.'/'.$catid.'/'.$tagid ;//.$subpage
    	}
    	elseif($type=="category"){
    		$url_base = '/category/'.$catid.'/'.$tagid.'/'.$tail ;//.$subpage
    	}
    	//$url_base = '/active_source/post/'.$catid.'/1' ;
    	$prev = (($page-1)<=0)? 1 : ($page-1) ;
		$next = (($page+1)>$pages)? $pages : ($page+1) ;
    	
		$html = "";
		
		if($pages>1){
		
    	$html = "<ul>";
		$html .= "<li><a href='".$url_base."/".$prev.$page_mark."' >上一页</a></li>";
		if($pages<=$size)
		{
			for($i=1;$i<=$size;$i++)
			{
				if($i>$pages){break;}
				else{
				$cl = ($i==$page)?"active" : "";
				$bq = "a";
				$html .= "<li class='".$cl."'><a href='".$url_base."/".$i.$page_mark."' >".$i."</a></li>";
				}
			}
		}
		else
		{
			//每页只显示5个页码
			$start_pageNo = ($page - 2)<1 ? 1 : ($page - 2);
			$end_pageNo = ($start_pageNo + 2) > $pages ? $pages : ($page + 2);
			
			if($pages - $page <=2 ) // 最后两页
			{
				$cha = $pages - $page ;
				$start_pageNo  = $page - ($size - 1 - $cha) ; 
				$end_pageNo = $pages ; 
				
			}
			
			if($page == 1 || $page == 2){ //前两页
				$start_pageNo = 1;
				$end_pageNo = 5;
			}
			
			for($i = $start_pageNo;$i<=$end_pageNo;$i++)
			{
				if($i>$pages){break;}
				else{
				$cl = ($i==$page)?"active" : "";
				$bq = "a";
				$html .= "<li class='".$cl."'><a href='".$url_base."/".$i.$page_mark."' >".$i."</a></li>";
				}
			}
		}
		
		$html .= "<li><a href='".$url_base."/".$next.$page_mark."' >下一页</a></li>";
    	
    	$html .= "</ul>";
		}
    	
    	$loading_link = "/channel/getpostdata/{$catid}/{$tagid}/{$tail}/2/{$page}/";
    	//if($encode){
    	//return array("page"=>urlencode($html),"loding"=>urlencode($loading_link));
    	//}
    	//else{
    	return array("page"=>$html,"loding"=>$loading_link);
    	//}
    } 
    
    function all_posts($subpage = 1,$page = 1){
    	$index_post_pagesize = 12;
    	$offset = ($page-1)*60 + ($subpage-1)*$index_post_pagesize;
    	
    	if($subpage<=5){
		    /*$condition['where'] = array(
		    	//'photo <> ' => '', 
		    	'type <=' => 4,
		    	//'isEssence' => 1,
		    	'status <' => 2 ,
		    	//'praiseCount >= ' => $home_conf['praised']
		    );
		    	
		    $condition['limit'] = array('size'=>$index_post_pagesize,'offset'=>$offset);
		    $condition['order_by'] = array('createDate','desc');
		    $count = 1000;//= $this->m_post->count_post($condition);
		    $home_post = $this->m_post->list_post($condition,'thweb');*/
    		
    		$topic_id = 4979;//4979;
    		
    		$where = " p.type <= 4 and p.status < 2 ";
    		$sql = " select n.*  
					  from ( select top.postId 
					           from Post p 
					          inner join TopicOwnPost top 
					             on top.postId=p.id and top.topicId != {$topic_id}
					          where {$where}
					          order by top.postId desc 
					          limit {$offset},{$index_post_pagesize} ) m
					 inner join Post n
					    on m.postId = n.id ";
    		$arr = $this->db->query($sql)->result_array();
    		$count = 1000;
    		$home_post = $this->m_post->list_post(array(),'thweb',false,$arr);
    	}
    	$parr = $this->paginate('/channel/all_posts', $count, $page, array('subpage'=>$subpage), 60);
    	
    	$this->assign(compact('home_post','page','subpage'));
    	$this->display('all_post');
    }

}