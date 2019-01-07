<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * 应用的一些公用函数
 * @author chenglin.zhu@gmail.com
 * @date 2012-11-12
 */

	/**
	 * 获取标签列表
	 * @param string $channel_id 频道ID
	 * @param string $type 0,1,2,3 标签/地点分类/地点/用户
	 */

	function get_tags_by_cid($channel_id,$type=NULL){
		
		$CI = &get_instance();
		$CI->load->model('tag_model', 'm_tag');
		$CI->load->model('webnewscategoryowntag_model', 'm_webnewscategoryowntag');
		
		$tags_array = $CI->m_webnewscategoryowntag->list_by_catid($channel_id);
		
		$tags = array();
		foreach($tags_array as $k=>$row){
			$tags[] = $row['tagId'];
		}
		
		return get_data("tag",$tags);
	}
	
	/*function get_frag($fid){
		$CI = &get_instance();
		$CI->load->model('webrecommendfragment_model', 'm_webrecommendfragment');
		//$info = $CI->db->where('fid', $fid)->get('WebRecommendFragment')->first_row('array');
		$info = $CI->m_webrecommendfragment->select_by_fid($fid);
		//碎片关联的频道
		$info['cates'] = $CI->categories[$CI->id];
		
		return $info;
	}*/

	function get_fragment_data($fid,$flush=false){
		$CI = &get_instance();
		$cache_id = 'fragmentdata_'.$fid;
		$frag = get_frag($fid);
		$cache_data = get_data($cache_id,false);
		if($flush || empty($cache_data)){
			echo "flush";
			$CI->load->model('webrecommendfragment_model', 'm_webrecommendfragment');
			$CI->load->model('webrecommenddata_model', 'm_webrecommenddata');
			
			//碎片属性
			
			!empty($frag['rule']) && $frag['rule'] = json_decode($frag['rule'], true);
			!empty($frag['rule']) && !empty($frag['rule']['pic_size']) && $frag['rule']['pic_size'] = explode('*', $frag['rule']['pic_size']);
			!empty($frag['extraProperty']) && $frag['extraProperty'] = json_decode($frag['extraProperty'], true);
			
			//查询数据
			$cache_data = $CI->m_webrecommenddata->list_by_fragmentid_order_ordervalue($fid);
			foreach($cache_data as &$r){
				if(!empty($r['extraData'])){
					$exd = json_decode($r['extraData'], true);
					foreach($exd as $k=>&$e){
						$t = $frag['extraProperty'][$k]['type'];
						$e = array(
							'type'=>$t,
							'data'=>$e
						);
					}
					unset($e);
					$r['extraData'] = $exd;
				}
			}
			unset($r);

			_save_cache($cache_id, $cache_data);
		}
		
		//echo "noflush";
			
		
		return array("data"=>$cache_data,"frag"=>$frag);
	}
	
	
	function get_active_source($datasource){
		$CI = &get_instance();
		$arr = explode("/",str_replace("http://","",$datasource));
		//$type,$request=0,$count=1,$keyword='',$json=true
		$type = $arr[3];
		$request = $arr[4];
		$count = $arr[5] ? $arr[5] : 1 ;
		$keyword = $arr[6];
		$json= $arr[7] ? $arr[7] : true;
		$data = array();
		switch($type){
			case "mvp_post_list": //一个UID时，count有效
				$uid = intval($request);
				$users = get_data("user",$uid);
				
				if(!$uid) break;
				
				if($keyword){
					$where = " and p.content like '%".urldecode($keyword)."%'";
				}
				$sql = "select p.*,pl.placename from Post p , Place pl 
						where p.placeId=pl.id and p.uid=".$uid." and p.status<2 and p.type<4 ".$where." order by p.createDate desc limit ".$count;
				
				$data = array();
				$users['nickname'] = $users['nickname'] ? $users['nickname'] : $users['username'];
				
				$data['user'] = $users;
				$data['post'] = $CI->db->query($sql)->result_array();
				
				break;
			case "mvp_post": //多个MVP，count无效,有多少UID取多少个人
				$uid = explode("-",$request);
				$users = get_data("user",$uid);
				
				$uids = implode(",",$uid);
				if(!$uids) break;
				
				
				if($keyword){
					$where = " and p.content like '%".urldecode($keyword)."%'";
				}
				foreach($users as $k=>$v){
					
					$data[$k]['user'] = $v;
					$data[$k]['user']['nickname'] = $v['nickname'] ? $v['nickname'] : $users['username'];
					$sql = "select p.*,pl.placename from Post p left join Place pl  
						on p.placeId=pl.id where p.uid=".$v['id']." and p.status<2 and p.type<4 ".$where." order by p.createDate desc limit 1";
					$data[$k]['post'] = $CI->db->query($sql)->row_array(0);
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
				$sql = "select * from Post 
						where type=1".$where;
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
				
				$sql .= " order by createDate desc limit ".$count;
				
				$data['data'] = $CI->db->query($sql)->result_array();
				//if(!$placeids){
					$pppp = array();
					foreach($data['data'] as $k=>$row){
						$u  = get_data("user",$row['uid']);
						$data['data'][$k]['username'] = $u['username'];
						$data['data'][$k]['nickname'] = $u['nickname'] ? $u['nickname'] : $u['username'] ;
						$data['data'][$k]['head'] = image_url($u['avatar'], 'head', 'mdpl');
						$pppp[] = $row['placeId'];
					}
					$data['places'] = get_data("place",$pppp);
				//}
				
			break;
		}
		
		return $data;
	}
	
	
	/**
	 * 获取频道的栏目
	 */
	function get_channel_category($domain, $categories, $type=1) {
	    $sub_cate = array();
	    if($domain['sub']) {
	        foreach($domain['sub'] as $key => $value) {
	            //var_dump($value);
	            //if($this->categories[$key]['cateType']==1){
	            //    ($value == 1) && ($sub_cate[$key]['catName'] = $this->categories[$key]['catName']);
	            //    ($value == 1) && ($sub_cate[$key]['link'] = $this->categories[$key]['link']);
	            //    ($value == 1) && ($sub_cate[$key]['tag_count'] = $this->categories[$key]['tag_count']);
	            //}
	            if($value==1){
	                if($categories[$key]['catType']==$type){
	                    $sub_cate[$key]['catName'] = $categories[$key]['catName'];
	                    $sub_cate[$key]['link'] = $categories[$key]['link'];
	                    $sub_cate[$key]['tag_count'] = $categories[$key]['tag_count'];
	                }
	            }
	        }
	    }
	
	    return $sub_cate;
	}
