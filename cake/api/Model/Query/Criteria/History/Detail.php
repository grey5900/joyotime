<?php
namespace Model\Query\Criteria\History;

class Detail extends \AppModel {
	
	public $name = '\Model\Query\Criteria\History\Detail';
	
	public $primaryKey = '_id';
	public $useTable = 'keyword_details';
	
	public $mongoSchema = array(
		'criteria' => array('type' => 'string'),
		'count' => array('type' => 'integer'),
		'location' => array(
		    'lat' => array('type' => 'float'),
		    'lng' => array('type' => 'float')
		)
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
			'location' => array(
                'require' => array(
                    'rule' => array('chkLocation'),
                    'message' => __('Invalid `location` while saving keyword in DB') 
                ) 
            )
		);
	}
	
	public function add($criteria, \Model\Data\Point $point = null) {
		$data = array(
			'criteria' => $criteria
		);
		if($point) {
			$data['location']= array(
				'lat' => $point->getLatitude(),
				'lng' => $point->getLongitude()
			);
		}
		return $this->create($data) && $this->save();
	}
	
/**
 * Check whether the location that is consisted with latitude
 * and longitude is valid or not.
 *
 * @param array $check
 * @return boolean
 */
	public function chkLocation($check) {
		$loc = $this->gets('location', $this->data[$this->name]);
		if($loc) {
			$lat = $this->gets('lat', $loc);
			$lng = $this->gets('lng', $loc);
			if($lat && $lng) return true;
		}
		return false;
	}
}