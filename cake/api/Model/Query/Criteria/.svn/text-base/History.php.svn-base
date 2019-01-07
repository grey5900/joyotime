<?php
namespace Model\Query\Criteria;

use \Model\Query\Criteria\History\Table;
use \Model\Query\Criteria\History\Detail;

class History {
	
	public $name = '\Model\Query\Criteria\History';
	
	public function add($criteria, \Model\Data\Point $point = null) {
		$table = new Table(); 
		
		if($table->update($criteria)) {
			$detail = new Detail();
			return $detail->add($criteria, $point);
		} 
		
		return false;
	}
}