<?php
  
 // Define and include
if ( ! defined('BASEPATH')) exit('No direct script access allowed');   
   
 // Code
class Itemawards extends MY_Controller{
	
	function index(){
		$this->display("index");
	}
	function home(){
		echo "不知道主页是干嘛的";
	}
	
	function actions(){
		$list = $this->db->get($this->_tables['itemawardaction'])->result_array();
		
		$this->assign(compact('list'));
		$this->display("actions");
	}
	
	function items(){

		$total = $this->db->query("select sum(ia.probability) as total from ".$this->_tables['item']." i right join ".$this->_tables['itemawards']." ia on i.id=ia.itemId")->row_array(0);
		
		$sql = "select i.name,i.id as item_id,IFNULL(ia.id,0) as id,IFNULL(ia.itemId,0) as itemId,IFNULL(ia.probability,0) as probability,IFNULL(ia.frequency,0) as frequency,IFNULL(ia.quantity,0) as quantity from ".$this->_tables['item']." i 
				left join ".$this->_tables['itemawards']." ia on i.id=ia.itemId order by ia.probability desc";
		
		$today = date("Y-m-d");
		$list = $this->db->query($sql)->result_array();
		foreach($list as &$row){
			$row['percent'] = $total['total']?round(($row['probability'] / $total['total']),4) * 100 ."%":"0%";
			if(!$row['id']){
			//直接插入一条算了！
				$set = array(
					'itemId' => $row['item_id'],
					'probability' => 0,
					'frequency' => 0,
					'quantity' => 0
				);
				$this->db->insert($this->_tables['itemawards'],$set);
				$id = $this->db->insert_id();
				$row['itemId'] = $row['item_id'];
				$row['id'] = $id;
			}
			if($row['id']){
			//今日剩余
			$used_count = $this->db->query("select count(0) as num from ItemAwardLog where itemId=".$row['id']." and createDate like '".$today."%'")->row_array(0);
			$row['left_over'] = $row['quantity'] - $used_count['num'];
			}
		}
		
		
		$this->assign(compact('list'));
		$this->display("items");
	}
	function itemAwardsForObject(){
		
		$this->display("itemawardsobject");
	}
	function addItemAwardObject(){
		
		
		$items = $this->db->get($this->_tables['item'])->result_array();
		$this->display("addobject");
	}
	
}