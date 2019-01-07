<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * UserFaverPlaceCollection表操作
 * Author: piglet chenglin.zhu@gmail.com
 * Created on 2012-12-6
 */

class Userfaverplacecollection_Model extends MY_Model {
	
	public function check_favorite($uid, $pcid){
		$c = $this->db->where(array('uid'=>$uid, 'pcId'=>$pcid))->count_all_results($this->_tables['userfaverplacecollection']);
		return $c <= 0;
	}
}