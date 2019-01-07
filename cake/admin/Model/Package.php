<?php
APP::uses('AppModel', 'Model');
class Package extends AppModel {
	//var $useDbConfig = 'fishsaying';
	public $name = 'Package';
	public $useTable = FALSE;
	public $results = array();
	public $count = 0;
	const PENDING = 0;
	const AVALIABLE = 1;
	public function paginate() {
		return $this->results;
	}
	public function paginateCount() {
		return $this->count;
	}
	
}