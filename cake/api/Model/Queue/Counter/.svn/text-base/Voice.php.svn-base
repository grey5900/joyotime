<?php
namespace Model\Queue\Counter;

\App::uses("AppModel", "Model");

class Voice extends \AppModel {
	
	public $name = '\Model\Queue\Counter\Voice';
	
	public $useDbConfig = 'redis'; // Defined at app/Config/database.php
	
	private $hmap = 'count_voice_hmap';
	
/**
 * @var Redis
 */
	protected $redis;
/**
 * @var Voice
 */
	protected $model;
	
	public function __construct($id = false, $table = null, $ds = null) {
		parent::__construct($id, $table, $ds);
		$this->redis = $this->getDataSource()->getInstance();
		$this->model = \ClassRegistry::init('Voice');
	}
	
/**
 * Pop an Array from queue
 *
 * @return mixed|boolean
 */
	public function dequeue($voice) {
		if($voice) {
		    $count = $this->redis->hget($this->hmap, $voice);
			$this->model->updatePlayTotal($voice, (int)$count);
			$rs = $this->redis->hdel($this->hmap, $voice);
			return true;
		}
		return false;
	}
}