<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');   
/*
 * 投票管理
 */

class Vote extends MY_Controller {
	
	function __construnct(){
		$this->load->model("vote_model","m_vote");
	}
	
	function add($item_type,$item_id){
		
		$this->display("add");
	}
	
	function result($item_type ,$item_id ,$type = "display"){
		
		$have_vote = $this->db->where(array('itemType'=>$item_type,'itemId'=>$item_id))->
								get($this->_tables['vote'])->
								row_array(0);
		if($have_vote){
			$count_ops = $this->db->where('voteId',$have_vote['id'])->count_all_results($this->_tables['voteoptions']);
			$list = array();
			if($count_ops){
				$parr = $this->paginate($count_ops);
				$list = $this->db->where('voteId',$have_vote['id'])->order_by('votes desc')->get($this->_tables['voteoptions'])->result_array();
				
				//总票数
				$count = $this->db->select('count(id) as vote_num,count(distinct uid) as users')->where('voteId',$have_vote['id'])->get($this->_tables['votelog'])->row_array(0);
				//参与用户
				//var_dump($count);
			}
		}
		if($type == "display"){
			$this->assign(compact('parr','list','count','item_id'));
			$this->display("result");
		}
		else{
			return array('count'=>$count_ops,'list'=>$list,'count_all'=>$count);
		}
		
	}
	function result_export($item_type ,$item_id ){
		$have_vote = $this->db->where(array('itemType'=>$item_type,'itemId'=>$item_id))->
								get($this->_tables['vote'])->
								row_array(0);
		if($have_vote){
			$options = $this->result($item_type,$item_id,'re');
			$filename = sprintf ( '投票['.$have_vote['subject'].']结果', $event ['subject'] );
			header ( 'Content-type: application/vnd.ms-excel; charset=gbk' );
			header ( 'Content-Disposition: attachment; filename="' . $filename . '.xls"' );
			$str = "候选项\t链接\t得票数\n";
			foreach($options['list'] as $row){
				$str .= "{$row['title']}\t{$row['link']}\t{$row['votes']}\n";
			}
			$str .= "合计 总票数：{$options['count_all']['vote_num']}   参与用户：{$options['count_all']['users']} \n";
			
		}
		echo mb_convert_encoding ( $str, 'GBK', 'utf-8' );
	}
}
