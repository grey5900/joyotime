<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');   
/*
 * 推荐
 * 
 * Author: piglet chenglin.zhu@gmail.com
 * Created on 2012-6-19
 */

class Recommend extends MY_Controller {
    /**
     * 获取数据
     */
    function index($fid, $force = '') {
        $cache_id = 'data_' . $fid;
        
        $data = get_recommend_cache($cache_id);
        if(empty($data) || $force) {
            // 数据为空那么去获取数据
            $this->load->helper('recommend');
            $data = get_recommend_data(array($fid), true);
            // 并保存
            set_recommend_cache($cache_id, $data);
        }
        
        $this->assign('data', $data);
        $this->display('data_' . $fid);
    }
    
    /**
     * 动态
     */
    function feed($size = 20, $force = '') {
        $cache_id = 'feed_index_' . $size;
        
        $data = get_recommend_cache($cache_id);
        if(empty($data) || $force) {
            // 数据为空那么去获取数据
            $data = $this->db->select('f.*, uf.nickname as feedName, uf.type as feedType, uf.itemId as vid')->limit($size)
                             ->order_by('f.createDate', 'desc')
                             ->join('UserFeed uf', 'f.feedId = uf.id', 'left')
                             ->get('UserFeed f')->result_array();
            $feed_type = $this->config->item('feed_types');
            foreach($data as &$row) {
                $detail = json_decode($row['detail'], true);
            	if($detail['placeId']){
            		$row['at_do_link'] = '/place/' . $detail['placeId'] . '/';
            	}else{
            		$rs = $this->db->select('id')->where('placename', $row['placename'])->get('Place')->first_row('array');
            		$row['at_do_link'] = '/place/' . $rs['id'] . '/';
            	}
                switch($row['type']) {
                    case 1:
                        if($detail['is_crowned']) {
                            $row['at'] = '成为';
                            $row['do'] = '地主';
                        } else {
                            $row['at'] = '在';
                            $row['do'] = '签到';                            
                        }
                        // $row['link'] = '/review/' . $row['itemId'];
                        $row['link'] = '';
                        $row['at_do'] = $row['placename'];
                        break;
                    case 2:
                        $row['at'] = '在';
                        $row['do'] = '发布了点评';
                        $row['link'] = '/review/' . $row['itemId'] . '/';
                        $row['at_do'] = $row['placename'];
                        break;
                    case 3:
                        $row['at'] = '在';
                        $row['do'] = '发布了照片';
                        $row['link'] = '/review/' . $row['itemId'] . '/';
                        $row['at_do'] = $row['placename'];
                        break;
                    case 4:
                        $feed_name = $feed_type[$row['feedType']];
                        $row['at'] = '分享了';
                        $row['do'] = $feed_name;
                        $row['link'] = $row['feedType']>=4?'':'/review/' . $row['vid'] . '/';
                        $row['at_do'] = $row['feedType']>=4?$feed_name:$row['feedName'];
                        break;
                    case 6:
                    	$row['at'] = '分享了';
                    	$row['at_do'] = '团购';
                    	//查询团购名称
                    	$group_id = $detail['item_id'];
                    	$query = $this->db->where(array('id'=>$group_id, 'status'=>0))->get('GrouponItem')->first_row('array');
                    	$row['do'] = empty($query) ? '' :$query['title'];
                    	break;
                    case 7:
                    	$row['at'] = '分享了';
                    	$row['at_do'] = '电影票';
                    	//查询电影票名称
                    	$id = $detail['item_id'];
                    	$type = $status = 0;
                    	$query = $this->db->where(compact('id', 'type', 'status'))->get('Product')->first_row('array');
                    	$row['do'] = empty($query) ? '' : $query['name'];
                    	break;
                    case 5:
                        $row['at'] = '在';
                        $row['do'] = '获得勋章';
                        $row['link'] = '/review/' . $row['itemId'] . '/';
                        $row['at_do'] = $row['placename'];
                        break;
                    case 8:
                    	$row['at'] = '成为';
                    	$row['do'] = '的会员';
                    //	$row['link'] = $row['at_do_link'];
                    	//获取品牌名称
                    	$id = $detail['item_id'];
                    	$this->db->join('BrandMemberCard', 'BrandMemberCard.brandId = Brand.id', 'inner');
                    	$query = $this->db->where('BrandMemberCard.id', $id)->get('Brand')->first_row('array');
                        $row['at_do'] = $query['name'];
                        break;
                }
            }
    	    unset($row);
            // 并保存
            set_recommend_cache($cache_id, $data);
        }
        $this->assign('data', $data);
                
        $this->display('feed');
    }
}
