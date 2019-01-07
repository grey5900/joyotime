<?php
/**
 * 品牌商家相关
 * Create by 2012-9-19
 * @author liuw
 * @copyright Copyright(c) 2012-2014 joyotime
 */
  
 // Define and include
if ( ! defined('BASEPATH')) exit('No direct script access allowed');   
   
 // Code
class Brand extends MY_Controller{
	
	/**
	 * 成为会员
	 * Create by 2012-9-21
	 * @author liuw
	 */
	function join(){
		$this->is_login(true);
		if($this->is_post()){
			$uid = $this->auth['id'];
			//POST参数
			$memberCardId = $this->post('card_id');
			//直接调用接口完成获得会员卡的业务逻辑
			
		/*	$code = $msg = $brandId = $cardId = false;
			//检查是否已过的了这张会员卡
			$count = $this->db->where(array('uid'=>$uid, 'memberCardId'=>$memberCardId))->count_all_results('UserOwnMemberCard');
			if($count)
				$this->echo_json(array('code'=>1, 'msg'=>$this->lang->line('mcard_has_got')));
			//检查会员是否是基础会员卡
			$card = $this->db->where('id', $memberCardId)->get('BrandMemberCard')->first_row('array');
			if(!$card['isBasic']){//不是基础会员卡
				//首先检查用户是否已获得了这个地点的基础会员卡
				$basic = $this->db->where(array('brandId'=>$card['brandId'],'isBasic'=>1))->get('BrandMemberCard')->first_row('array');
				if(empty($basic)){
					$code = 1;
					$msg = $this->lang->line('mcard_expired');
				}else{
					$count = $this->db->where(array('uid'=>$uid, 'memberCarddId'=>$basic['id']))->count_all_results('UserOwnMemberCard');
					if(!$count){//没有获得基础会员卡
						$cardId = $basic['id'];
					}else{
						$cardId = false;
					}
					$brandId = $basic['brandId'];
				}
			}else{
				$brandId = $card['brandId'];
			}
			if($code == 1)
				$this->echo_json(compact('code', 'msg'));
			if($cardId){
				//会员卡号
				$cardCode = '';
				$data = compact('uid', 'brandId');
				$data['memberCardId'] = $cardId;
				$data['isBasic'] = 1;
				$this->db->insert('UserOwnMemberCard', $data);
			}
			//获得当前卡
			$data = compact('uid', 'memberCardId' 'brandId');
			$data['isBasic'] = $card['isBasic'];
			$this->db->insert('UserOwnMemberCard', $data);
			$id = $this->db->inser_id();
			if(!$id)
				$this->echo_json(array('code'=>1, 'msg'=>$this->lang->line('mcard_get_faild')));
			else 
				$this->echo_json(array('code'=>0, 'msg'=>$this->lang->line('mcard_get_success')));*/
		}
	}
	
	/**
	 * 检查当前用户是否是指定地点的会员
	 * Create by 2012-9-21
	 * @author liuw
	 * @param int $place_id
	 */
	function is_member($place_id){
		$this->is_login();
		$uid = $this->auth['id'];
		//检查用户是否获得了指定地点的基础会员卡
		$this->db->from('UserOwnMemberCard');
		$this->db->join('BrandMemberCard', 'BrandMemberCard.id=UserOwnMemberCard.memberCardId', 'inner');
		$this->db->join('Place', 'Place.brandId=BrandMemberCard.brandId', 'inner');
		$count = $this->db->where(array('Place.id'=>$place_id, 'BrandMemberCard.isBasic'=>1, 'UserOwnMemberCard.uid'=>$uid))->count_all_results();
		$this->echo_json(array('code'=>$count ? 1 : 0));
	}
	
	/**
	 * 申请成为品牌商家
	 * Create by 2012-9-19
	 * @author liuw
	 */
	function apply(){
		if($this->is_post()){
			//申请类型
			$types = $this->config->item('apply_type');
			$tarr = array_flip($types);
			//POST参数
			$contact = $this->post('contact');
			$tel = $this->post('tel');
			$content = $this->post('content');
			//检查参数完整性
			empty($contact) && $this->echo_json(array('code'=>1, 'msg'=>$this->lang->line('apply_brand_contact_empty')));
			empty($tel) && $this->echo_json(array('code'=>1, 'msg'=>$this->lang->line('apply_brand_tel_empty')));
			empty($content) && $this->echo_json(array('code'=>1, 'msg'=>$this->lang->line('apply_brand_content_empty')));
			//保存数据
			$applyType = $tarr['申请品牌商家入驻'];
			$data = compact('contact', 'tel', 'applyType', 'content');
			$this->db->insert('Application', $data);
			$id = $this->db->insert_id();
			if(!$id)
				$this->echo_json(array('code'=>1, 'msg'=>$this->lang->line('apply_brand_faild')));
			else 
				$this->echo_json(array('code'=>0, 'msg'=>$this->lang->line('apply_brand_success')));
		}else{
			
			$this->display('test', 'web');
		}
	}
	
	/**
	 * 会员卡详情，与用户无关
	 * Create by 2012-9-21
	 * @author liuw
	 * @param int $card_id
	 */
	function card_info($card_id){
		$this->db->select('BrandMemberCard.*, Brand.tel');
		$this->db->join('Brand', 'Brand.id = BrandMemberCard.brandId', 'inner');
		$card = $this->db->where(array('BrandMemberCard.id'=>$card_id, 'BrandMemberCard.status'=>1, 'Brand.status'=>0))->get('BrandMemberCard')->first_row('array');
		if($card){
			//适用地点列表
			$query = $this->db->select('id, placename, isVerify')->where('brandId', $card['brandId'])->order_by('createDate', 'desc')->get('Place')->result_array();
			foreach($query as $row){
				$card['places'][$row['id']] = $row;
			}
		}
		$this->assign('card', $card);
	//	$this->display();
	}
	
	/**
	 * 用户获得的商家会员卡详情
	 * Create by 2012-9-19
	 * @author liuw
	 * @param int $id
	 * @param int $uid
	 */
	function my_card_info($id, $uid){
		//会员卡属性
		$this->db->select('BrandMemberCard.*, Brand.tel, UserOwnMemberCard.cardCode');
		$this->db->from('UserOwnMemberCard');
		$this->db->join('BrandMemberCard', 'BrandMemberCard.id = UserOwnMemberCard.memberCardId', 'inner');
		$this->db->join('Brand', 'Brand.id = BrandMemberCard.brandId', 'inner');
		$card = $this->db->where(array('UserOwnMemberCard.memberCardId'=>$id, 'UserOwnMemberCard.uid'=>$uid, 'BrandMemberCard.status'=>1))->get()->first_row('array');
		if($card){
			//适用的地点列表
			$query = $this->db->select('id, placename, isVerify')->where('brandId', $card['brandId'])->order_by('createDate', 'desc')->get('Place')->result_array();
			empty($query) && $query = false;
			$card['places'] = $query;
			//会员卡图片
			$card['image'] = $card['image'];
		}
		
		empty($card) && $card = array();
		$this->assign('card', $card);
	//	$this->display('');
	}
	
}   
   
 // File end