<?php
namespace Model\Queue\Password;

\App::uses('User', 'Model');

class Reset extends \AppModel {
	
	public $name = '\Model\Queue\Password\Reset';
	
	public $useDbConfig = 'redis'; // Defined at app/Config/database.php
	
	private $keyQueue = 'password_reset';
	private $keyHash  = 'pwd_reset_hash';
	
/**
 * @var Redis
 */
	protected $redis;
	
	public $mongoSchema = array(
		'email' => array('type'=>'string'),
		'username' => array('type'=>'string'),
		'url' => array('type'=>'string'),
	    'locale' => array('type'=>'string')
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
                    'message' => __("No account exists for this email") 
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
        $row = $model->find('first', array(
            'fields' => array('username', 'locale'),
            'conditions' => array(
                'email' => $email
            )
        ));
        if(isset($row[$model->name]['username'])) {
            $this->data[$this->name]['username'] = $row[$model->name]['username'];
            $this->data[$this->name]['url'] = $this->generateURL($email);
            $this->data[$this->name]['locale'] = $row[$model->name]['locale'];
            $this->data[$this->name]['ios'] = \Configure::read('APP.IOS.Download.Link');
            $this->data[$this->name]['android'] = \Configure::read('APP.Android.Download.Link');
            return true;
        }
        return false;
    }
    
    /**
     * 
     * @param string $email
     * @return string
     */
    public function generateURL($email) {
        $expire = time() + 24 * 3600;
        $hash = $this->encode($email, $expire);
        return 'http://'.\Configure::read('URL.Website.Domain').'/reset?email='.$email.'&expire='.$expire.'&hash='.$hash;
    }
    
/**
 * Generate hash
 * 
 * @param string $email
 * @param int    $expire
 * @return string
 */
    public function encode($email, $expire) {
        return sha1(md5($email.$expire.\Configure::read('Security.cipherSeed')));
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
		    && $this->redis->lPush($this->keyQueue, json_encode($this->data[$this->name]));
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
	
/**
 * Check whether hash comes at first time or not
 * 
 * @param string $hash
 * @return boolean 1: first time, 0: not first time
 */
	public function firstTime($hash) {
	    return (bool)!$this->redis->hexists($this->keyHash, $hash);
	}
	
/**
 * Save hash code 
 * 
 * Avoid to repeat reset password use the same URL
 * 
 * @param string $hash
 * @return boolean
 */
	public function record($hash) {
	    return $this->redis->hset($this->keyHash, $hash, 1);
	}
}