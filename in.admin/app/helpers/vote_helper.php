<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

	/*
	 * type = add , edit
	 * */
	function vote_add($item_type,$item_id){
		$CI = &get_instance();
		
		$need_vote = $CI->post("need_vote");
		$have_vote = $CI->db->where(array('itemType'=>$item_type,'itemId'=>$item_id))
								->where('status',0)
								->get($CI->_tables['vote'])
								->row_array(0);    
		if($need_vote){
			$options = $CI->post("vote_option_images");
			$subject = $CI->post('vote_subject');
			if(empty($options)){
				$CI->error("请添加候选项！");
			}
			if(empty($subject)){
				$CI->error("请填写活动标题！");
			}
			if(count($options['defineId']) != count(array_unique($options['defineId']))){
				$CI->error("自定义ID不能重复，请检查并修改！");
			}
			foreach($options['defineId'] as $row){
				intval($row) <= 0 ? $CI->error("自定义ID必须大于0，请修改！") : '';
			}
			
			$data = array(
				'subject' =>  $CI->post('vote_subject'),
				'startDate' =>  $CI->post('vote_startDate'),
				'endDate' => $CI->post('vote_endDate'),
				'votePerDay' => intval($CI->post('vote_votePerDay')),
				'point' => intval($CI->post('vote_point')),
				'relatedItem' => intval($CI->post('vote_relatedItem')),
				'indexTitle' => $CI->post('vote_indexTitle'),
				'btnTitle' => $CI->post('vote_btnTitle'),
				'countTitle' =>  $CI->post('vote_countTitle'),
				'itemId' => $item_id,
				'itemType' => $item_type,
				'rankOrder' => $CI->post('vote_rankOrder'),
				'theme' => $CI->post('vote_theme'),
				'during' => $CI->post('vote_during')
			);
			
			if(!empty($have_vote)){ //update
				$CI->db->where('id',$have_vote['id'])->update($CI->_tables['vote'],$data);
				$vote_id = $have_vote['id'];
			}
			else{ //insert
				$CI->db->insert($CI->_tables['vote'],$data);
				$vote_id = $CI->db->insert_id();
			}
			
			//添加或者编辑选项！ - ID不存在就新加 ID存在就编辑  / ID本来有，新加列表没有 就删除选项
			
			$option_ids = @array_filter($options['id']);
			
			//先遍历一次本来就存在的options来删除选项
			if(!empty($have_vote)){
				$option_list = $CI->db->select('id')->where('voteId',$have_vote['id'])->get($CI->_tables['voteoptions'])->result_array();
				foreach($option_list as $oid){
					if(!in_array($oid['id'],$option_ids)){
						$CI->db->where('id',$oid['id'])->delete($CI->_tables['voteoptions']);
						//$CI->db->where(array('voteId'=>$have_vote['id'],'optionId'=>$oid['id']))->delete($CI->_tables['votelog']);
					}
				}
			}
			
			//再新加和编辑
			foreach($options['id'] as $k=>$op){
				$option_data = array(
					'voteId' => $vote_id,
					'title' => $options['vote_option_title'][$k],
					'link' => $options['vote_option_link'][$k],
					'image' => $options['image'][$k],
					'defineId' => $options['defineId'][$k],
				);
				
				$op = intval($op);
				
				if(empty($op)){ //添加
					//新加的话，去统计一下votes数 。
					$count = $CI->db->where('voteId',$have_vote['id'])->where('optionId',$options['defineId'][$k])->count_all_results($CI->_tables['votelog']);
					$option_data['votes'] = $count;
					$res = $CI->db->insert($CI->_tables['voteoptions'],$option_data);
				}
				else{ //编辑
					$CI->db->where('id',$op)->update($CI->_tables['voteoptions'],$option_data);
				}
				unset($option_data);
			}
		}
		else{ //如果不需要投票，看是否已经关联过投票，如果有，那么删除掉
			
			if(!empty($have_vote)){
				//删除options 删不删log？再说。。
				//$CI->db->where('voteId',$have_vote['id'])->delete($CI->_tables['votelog']);
				//$CI->db->where('voteId',$have_vote['id'])->delete($CI->_tables['voteoptions']);
				//$CI->db->where(array('itemType'=>$item_type,'itemId'=>$item_id))->delete($CI->_tables['vote']);
				$CI->db->where(array('itemType'=>$item_type,'itemId'=>$item_id))->update($CI->_tables['vote'],array('status'=>1));
			}		

		}
	}
	
	function show_vote($item_type = 0,$item_id = 0){
		$CI = &get_instance();
		
		//可选 - 道具列表
		if($item_type && $item_id){
			$items = $CI->db->get($CI->_tables['item'])->result_array();
			$chosen_json = '[]';
			$info = $CI->db
				->where(array('itemType'=>$item_type,'itemId'=>$item_id))		
				->where('status',0)	
				->get($CI->_tables['vote'])
				->row_array(0);
			$options = $CI->db
				->where(array('voteId'=>$info['id']))
				->order_by('rankOrder','desc')
				->get($CI->_tables['voteoptions'])
				->result_array();
			/*
			 * 组成已经存入的选项
			 * */
			$chosen = array();
			
			foreach($options as $row){
				if(!$row['defineId']){
					$CI->db->where('id',$row['id'])->update($CI->_tables['voteoptions'],array('defineId'=>$row['id']));
				}
				$chosen[] = array(
					'vote_option_title' => $row['title'],
					'vote_option_link' => $row['link'],
					'id' => $row['id'],
					'image' => image_url($row['image'],'vote','odp'),
					'defineId' => $row['defineId'] ? $row['defineId'] : $row['id'],
				);
			}
			$chosen_json = json_encode($chosen);
		}
		$CI->assign(compact('info','items','options','chosen_json'));
		$CI->display("add","vote");
	}
?>