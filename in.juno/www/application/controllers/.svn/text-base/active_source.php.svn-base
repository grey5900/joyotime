<?php
/**
 * 动态碎片
 */
  
 // Define and include
if (!defined('BASEPATH'))
    exit('No direct script access allowed');   
   
 // Code
class Active_Source extends MY_Controller{
	
	   
	function __construct() {
        parent::__construct();
        $this->load->model("user_model","m_user");
	}
	
	/**
     * @param $type = mvp_post,mvp_post_list,[feed-list-by-place,feed-list-by-uid,feed-list-by-placecategory,feed-list]
     * @param $request 各种请求ID
     * @param $count 获取条数
     * @param $keyword 抓取点评或者其他神马的关键字
     * @param $josn 以json形式返回
     */
	function index($type,$request=0,$count=1,$keyword='',$json=true){

		switch($type){
			case "mvp_post_list": //一个UID时，count有效
				$uid = intval($request);
				$users = get_data("user",$uid);
				
				if($keyword){
					$where = " and p.content like '%".urldecode($keyword)."%'";
				}
				$sql = "select p.*,pl.placename from Post p , Place pl 
						where p.placeId=pl.id and p.uid=".$uid." and p.status<2 and p.type<4 ".$where." order by p.createDate desc limit ".$count;
				
				$data = array();
				
				$data['user'] = $users;
				$data['post'] = $this->db->query($sql)->result_array();
				
				foreach($data['post'] as $k=>$row){
					$data['post'][$k]['content'] = format_html($row['content']);
				}
				
				break;
			case "mvp_post": //多个MVP，count无效,有多少UID取多少个人
				$uid = explode("-",$request);
				$users = get_data("user",$uid);
				
				$uids = implode(",",$uid);
				
				$data = array();
				if($keyword){
					$where = " and p.content like '%".urldecode($keyword)."%'";
				}
				foreach($users as $k=>$v){
					$data[$k]['user'] = $v;
					$sql = "select p.*,pl.placename from Post p,Place pl 
						where p.placeId=pl.id and p.uid=".$v['id']." and p.status<2 and p.type<4 ".$where." order by p.createDate desc limit 1";
					$data[$k]['post'] = $this->db->query($sql)->row_array(0);
					$data[$k]['post']['content'] = format_html($data[$k]['post']['content']);
				}
				break;
			case "feed-list":
			case "feed-list-by-place":
			case "feed-list-by-uid":
			case "feed-list-by-placecategory":
				$type == "feed-list-by-place" ? $placeids = $request : "";
				$type == "feed-list-by-uid" ? $uids = $request : "";
				$type == "feed-list-by-placecategory" ? $p_categorys = $request : "";
				
				$data = array();
				if($keyword){
					$where = " and content like '%".urldecode($keyword)."%'";
				}
				$sql = "select * from Post p
						where type=1 ".$where;
				if($placeids){
					$place_string = str_replace("-",",",$placeids);
					//$place_arr = explode("-",$placeids);
					//$data['places'] = get_data("place",$place_arr);
					$sql .= " and placeId in (".$place_string.")";
				}
				if($uids){
					$uids_string = str_replace("-",",",$uids);
					$sql .= " and uid in (".$uids_string.")";
				}
				
				if($p_categorys){
					$category_string = str_replace("-",",",$p_categorys);
					
					$sql = "select p.* from Post p left join 
							PlaceOwnCategory pc on pc.placeId=p.placeId where pc.placeCategoryId in (".$category_string.") 
							and p.type=1 ";
				}
				
				$sql .= " order by p.createDate desc limit ".$count;
				
				$data['data'] = $this->db->query($sql)->result_array();
				//if(!$placeids){
					$pppp = array();
					foreach($data['data'] as $k=>$row){
						$data['data'][$k]['content'] = format_html($row['content']);
						$u  = get_data("user",$row['uid']);
						$data['data'][$k]['username'] = $u['username'];
						$data['data'][$k]['nickname'] = $u['nickname'] ? $u['nickname'] : $u['username'];
						$data['data'][$k]['head'] = image_url($u['avatar'], 'head', 'mdpl');
						$pppp[] = $row['placeId'];
					}
					$data['places'] = get_data("place",$pppp);
				//}
				
			break;
		}
		//var_dump($data);
		//$josn ? echo encode_json($data) : return $data;
		if($json) 
		echo encode_json($data);
		else 
		return $data;
		
	}
	
	/**
     * post数据
     * @param int $catid 分类id
     * @param int $subpage 小分页
     * @param int $page 大分页
     * @param int $orderby new/hot
     */
    function post_data($catid,$tagid=0,$subpage=1,$page=1,$orderby="hot",$json=true){
    	
    	$subpage = $subpage<1 ?  1 : $subpage;
    	$page = $page<1 ?  1 : $page;
    	
    	$this->load->model("webnewscategorydata_model","m_webnewscategorydata");
    	$result = $this->m_webnewscategorydata->get_post_data($catid,$tagid,$subpage,$page,$orderby,50,10);
    	
    	$pagination = $this->pagination_rolling($catid,$result['total'],$tagid,$subpage,$page,5,$orderby,$json);
    	//$postdata = $this->post($id,1,1,"new",false);
    	/*if($json)
    	echo json_encode(array("data"=>$result,"pagination"=>$pagination));
    	else
    	return array("data"=>$result,"pagination"=>$pagination);*/
    	
    	$data = array("data"=>$result,"pagination"=>$pagination);
    	
    	if($json==="false"){
    		echo encode_json($data);
    	}
    	else{
    		//return $data;
    		$this->assign("page",$pagination);
	    	$this->assign("postdata",$data);
	    	$this->display("post_item","channel");
    	}
    	
    }
    
    /**
     * 
     * 
     * @param $subpage 当前小分页
     * @param $page 当前大分页
     * @param $size 每次显示的分页数
     * @return html
     */
    private function pagination_rolling($catid,$total,$subpage=1,$page=1,$size=5,$tail,$type="index",$encode=true){
    	$pagesize=50;
    	$pages = ceil($total/$pagesize);
    	
    	if($type=="index"){
    		$url_base = '/'.$tail.'/'.$catid ;//.$subpage
    	}
    	elseif($type=="category"){
    		$url_base = '/category/'.$catid.'/hot' ;//.$subpage
    	}
    	$prev = (($page-1)<=0)? 1 : ($page-1) ;
		$next = (($page+1)>$pages)? $pages : ($page+1) ;
    	
    	$html = "<ul>";
    	
		$html .= "<li><a href='".$url_base."/".$prev."/".$tail."' >上一页</a></li>";
		if($pages<=$size)
		{
			for($i=1;$i<=$size;$i++)
			{
				if($i>$pages){break;}
				else{
				$cl = ($i==$page)?"active" : "";
				$bq = "a";
				$html .= "<li class='".$cl."'><a href='".$url_base."/".$i."/".$tail."' >".$i."</a></li>";
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
				$html .= "<li class='".$cl."'><a href='".$url_base."/".$i."/".$tail."' >".$i."</a></li>";
				}
			}
		}
		
		$html .= "<li><a href='".$url_base."/".$next."/".$tail."' >下一页</a></li>";
    	
    	$html .= "</ul>";
    	
    	$loading_link = "/active_source/post_data/{$catid}/2/{$page}/{$tail}";
    	/*if($encode){
    	return array("page"=>urlencode($html),"loding"=>urlencode($loading_link));
    	}
    	else{*/
    	return array("page"=>$html,"loding"=>$loading_link);
    	//}
    } 
    
 	/**
     * 获取得到rsa的签名sign
     */
    function rsa_sign() {
        $sign = rsa_sign();
        echo $sign ? $sign : '';
    }
    
	/**
     * 得到图片的url地址
     */
    function get_image_url() {
        $image_name = $this->get('file_name');

        if (strpos($image_name, 'http://') === 0) {
            die(json_encode(array(
                    'type' => 'url',
                    'image_name' => $image_name,
                    'image' => $image_name,
                    'source_image' => $image_name
            )));
        }

        $file_type = $this->get('file_type');
        $resolution = $this->get('resolution');

        $image = image_url($image_name, $file_type, $resolution);
        $source_image = $image;

        echo json_encode(compact('image_name', 'image', 'source_image'));
    }
}