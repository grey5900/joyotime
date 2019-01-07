<?php
namespace Model\Query\Criteria\History;

class Table extends \AppModel {
	
	public $name = '\Model\Query\Criteria\History\Table';
	
	public $primaryKey = '_id';
	public $useTable = 'keywords';
	
	public $mongoSchema = array(
		'criteria' => array('type' => 'string'),
		'count' => array('type' => 'integer')
	);
	
	public function __construct($id = false, $table = null, $ds = null) {
		parent::__construct($id, $table, $ds);
		$this->validate = array(
			'criteria' => array(
				'require' => array(
					'rule' => 'notEmpty',
					'required' => 'create',
					'allowEmpty' => false,
					'message' => __('`criteria` is empty while saving keyword in DB')
				)
			),
			'count' => array(
				'require' => array(
					'rule' => array('naturalNumber', true),
					'required' => 'create',
					'allowEmpty' => false,
					'message' => __('Invalid `count` while saving keyword in DB')
				)
			)
		);
	}
	
/**
 * @param string $criteria
 * @return number The count of criteria
 */
	public function update($criteria) {
		$doc = $this->find('first', array(
			'modify' => array('$inc' => array('count' => 1)),
			'conditions' => array('criteria' => $criteria)
		));
		if(!$doc[$this->name]) return $this->add($criteria, 1);
		return true;
	}
	
	public function add($criteria, $count) {
		$data = array(
			'criteria' => $criteria,
			'count' => $count
		);
		return $this->create($data) && $this->save();
	}
}