<?php
if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
	
/**
 * 
 * 主要是和用户积分相关的操作
 */	
	
	/**
	 * 判断积分够不够 - 写日志 - 操作user积分
	 * $point 加分正数 扣分负数
	 * return 
	 * 		1 操作成功
	 * 		0 积分不够
	 * 		-1 操作没有进行，因为操作的积分是0
	 * 		-2 没有传递UID，也没有当前登录的用户
	 * 		-3 操作失败，未知错误
	 * 		
	 */
	function change_point($uid = 0, $point_case , $point = 0 , $remark='' , $place_id='' , $fee = '' ){
		$CI = &get_instance();
		$uid = !$uid ? $CI->auth['uid'] : $uid; 
		
		if(!$uid){
			return -2;
		}
		
		if($point == 0){ //不扣分不加分，做记录干啥
			return -1;
		}
		
		if($point < 0){
			$user_point = $CI->db->select('point')->where('id',$uid)->get($CI->_tables['user'])->row_array(0);
			if($user_point['point'] < abs($point) ){
				return 0;
			}
		}
		
		$log_data = array(
			'uid' => $uid ,
			'pointCaseId' => $point_case ,
			'point' => $point ,
			'remark' => $remark ,
			'placeId' => $place_id ,
			'fee' => $fee
		);
		
		
		$b = $CI->db->insert($CI->_tables['userpointlog'],$log_data);
		if(!$b){
			return -4;
		}
		$b && $b = $CI->db->where('id',$uid)->set('point','point +'.$point,false)->update($CI->_tables['user']);
		
		get_data("user",$uid,true);
		
		if($b){
			
			return 1;
		}else{
			return -3;
		}
	}