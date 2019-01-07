<?php

  
 // Define and include
if ( ! defined('BASEPATH')) exit('No direct script access allowed');   
   
 // Code
class User_model extends MY_Model{
	
	function get_user($uids){
		
		if(!is_array($uids)){
			//$uids = implode(",",$uids);
			$uids = explode(",",$uids);
		}
		//var_dump($uids);
		/*if(is_numeric($uids) || is_string($uids)){
			
		}*/
		return $this->db2->where_in("id",$uids)->get("User")->result_array();
	}
	
	function get_user_by_username($usernames){
		if(is_array($usernames)){
			$this->db2->where_in($usernames);
		}
		else{
			$this->db2->where("username",$usernames);
		}
		return $this->db2->get("User")->result_array();
	}
}   
   