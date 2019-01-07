<?php
namespace Model\Queue;

\App::uses('User', 'Model');

class Mail extends \AppModel {
	
	public $name = '\Model\Queue\Mail';
	
	public $useDbConfig = 'redis'; // Defined at app/Config/database.php
	
	private $keyQueue = 'mails';
	
/**
 * @var Redis
 */
	protected $redis;
	
	public $mongoSchema = array(
		'email' => array('type'=>'string'),
		'content' => array('type'=>'string')
	);
	
	public function __construct($id = false, $table = null, $ds = null) {
		parent::__construct($id, $table, $ds);
		$this->redis = $this->getDataSource()->getInstance();
		$this->initValidates();
	}
	
	/**
	 * (non-PHPdoc)
	 * @see AppModel::initValidates()
	 */
	public function initValidates() {
        $this->validate = array(
            'email' => array(
                'format' => array(
                    'rule' => array('email'),
                    'allowEmpty' => true,
                    'message' => __('Invalid email') 
                ),
                'exist' => array(
                    'rule' => array('chkEmailExist'),
                    'allowEmpty' => true,
                    'message' => __('Invalid email') 
                )
            ),
            'content' => array(
                'required' => array(
                    'rule' => 'notEmpty',
                    'required' => 'create',
                    'allowEmpty' => false,
                    'message' => __('Invalid content') 
                ) 
            ) 
        );
    }
    
/**
 * Check whether email has been exist in DB
 * 
 * @param mixed $check
 * @return boolean
 */
    public function chkEmailExist($check) {
        $email = $this->getCheck('email', $check);
        $model = \ClassRegistry::init('User');
        return $model->find('count', array(
            'conditions' => array(
                'email' => $email
            )
        )) > 0;
    }
	
/**
 * Push an item into queue
 *
 * @param Array $save
 * @return boolean
 */
	public function enqueue($save) {
		return $this->create($save)
		    && $this->validates()
		    && $this->redis->lPush($this->keyQueue, json_encode($save));
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