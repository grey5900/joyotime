<?php
namespace Model\Counter;

\App::uses("AppModel", "Model");

class Voice extends \AppModel {
	
	public $name = '\Model\Counter\Voice';
	
	public $useDbConfig = 'redis'; // Defined at app/Config/database.php
	
/**
 * @var Redis
 */
	protected $redis;
	
	private $list = 'count_voice_list';
	private $hmap = 'count_voice_hmap';
	
	public function __construct($id = false, $table = null, $ds = null) {
		parent::__construct($id, $table, $ds);
		$this->redis = $this->getDataSource()->getInstance();
	}
	
	public function increase($key) {
		if(!$this->redis->hexists($this->hmap, $key)) {
			\CakeResque::enqueue($this->list, 'VoiceCountShell',
				array('refresh', $key)
			);
		}
		$this->redis->hincrby($this->hmap, $key, 1);
	}
}