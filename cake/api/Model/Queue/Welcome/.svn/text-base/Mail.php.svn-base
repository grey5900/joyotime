<?php
namespace Model\Queue\Welcome;

class Mail extends \AppModel {
	
	public $name = '\Model\Queue\Welcome\Mail';
	
	public $useDbConfig = 'redis'; // Defined at app/Config/database.php
	
	private $keyQueue = 'welcome';
	
/**
 * @var Redis
 */
	protected $redis;
	
	public $mongoSchema = array(
		'email' => array('type'=>'string'),
		'username' => array('type'=>'string'),
	    'locale' => array('type'=>'string'),
	    'ios' => array('type'=>'string'),
	    'android' => array('type'=>'string')
	);
	
	public function __construct($id = false, $table = null, $ds = null) {
		parent::__construct($id, $table, $ds);
		$this->redis = $this->getDataSource()->getInstance();
	}
	
/**
 * (non-PHPdoc)
 * @see Model::implementedEvents()
 */
	public function implementedEvents() {
		$callbacks = parent::implementedEvents();
		return array_merge($callbacks, array(
			'Model.User.afterRegister' => 'enqueue',
		));
	}
    
/**
 * Initial saved data
 * 
 * @param mixed $data
 * @return boolean|array
 */
    public function init($data) {
        $email    = $this->gets('email',    $data);
        $username = $this->gets('username', $data);
        $locale   = $this->gets('locale',   $data);
        
        if($email && $username && $locale) {
            return array(
                'email'    => $email,
                'username' => $username,
                'locale'   => $locale,
                'ios'      => \Configure::read('APP.IOS.Download.Link'),
                'android'  => \Configure::read('APP.Android.Download.Link')
            );
        }
        return false;
    }
	
/**
 * Push an item into queue
 *
 * @param Array $save
 * @return boolean
 */
	public function enqueue(\CakeEvent $event) {
	    $model = $event->subject();
	    $data = $this->init($model->data[$model->name]);
	    
		$result = $data && $this->redis->lPush($this->keyQueue, json_encode($data));
		if(!$result) $this->failEvent($event);
	}
	
/**
 * Pop an Array from queue
 *
 * @return mixed|boolean
 */
	public function dequeue() {
		$jsonStr = $this->redis->rPop($this->keyQueue);
		if($jsonStr) {
			return json_decode($jsonStr, true);
		}
		return false;
	}
}