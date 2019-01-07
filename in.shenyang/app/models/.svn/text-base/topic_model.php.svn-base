<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * Topic model
 * Author: bob baohanddd@gmail.com
 * Created on 2013-7-3
 */
class Topic_model extends MY_Model {
	
    /**
     * Return true if the specify subject has been existed in db.
     * 
     * @param string $subject
     * @return boolean
     */
    public function has_exist_subject($subject) {
        $this->db->where('subject', $subject);
        $exist = $this->db->get('Topic', 1)->result_array();    // just get one record
        return empty($exist) ? false : true;
    }
}