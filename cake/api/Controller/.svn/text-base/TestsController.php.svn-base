<?php
// require_once(VENDORS.'phpcassa/lib/autoload.php');
require_once(VENDORS.'solarium/vendor/autoload.php');

use phpcassa\ColumnFamily;
use phpcassa\ColumnSlice;

/**
 * 
 *
 */
class TestsController extends AppController {
	
	public $uses = array('NotificationCass', 'Purchased', 
	    'Voice', 'SyncQueue');
	
	public $components = array('QiNiu');
	
	public $autoLayout = false;
	public $autoRender = false;
	
	public function beforeFilter() {
	    parent::beforeFilter();
	    $this->OAuth->allow($this->name, 'index_in_solr');
	    $this->OAuth->allow($this->name, 'push_sync_item');
	    $this->OAuth->allow($this->name, 'test_exception');
	    $this->OAuth->allow($this->name, 'test_index');
	    $this->OAuth->allow($this->name, 'test_insert');
	    $this->OAuth->allow($this->name, 'add_notification');
	    $this->OAuth->allow($this->name, 'get_notification');
	    $this->OAuth->allow($this->name, 'benchmark_insert_notification');
	    $this->OAuth->allow($this->name, 'benchmark_insert_redis');
	    $this->OAuth->allow($this->name, 'benchmark_insert_mongo');
	    $this->OAuth->allow($this->name, 'benchmark_insert_couchbase');
	    $this->OAuth->allow($this->name, 'count_voice');
	}
	
	public function count_voice($key) {
	    $counter = new \Model\Queue\Counter\Voice();
	    $counter->dequeue($key);
	}
	
	public function index_in_solr() {
// 	    $voices = $this->Voice->find('all', array(
// 	        'fields' => array('title'),
// 	        'order' => array('created' => 'desc')
// 	    ));
// 	    foreach($voices as $voice) {
// // 	        $voice = $this->Voice->findById('52b7f70b67033368708b45eb')['Voice'];
// 	    }
	    $voice = $this->Voice->findById('52b7f70b67033368708b45eb');
	    $config = array(
	        'endpoint' => array(
                'localhost' => array(
                    'host' => '127.0.0.1',
                    'port' => 8080,
                    'path' => '/solr/',
                )
            )
	    );
	    
	    pr($config);
	    $client = new Solarium\Client($config);
	    $update = $client->createUpdate();
	    $doc = $update->createDocument();
	    $doc->_id = $voice['Voice']['_id'];
	    $doc->title = $voice['Voice']['title'];
	    
	    $update->addDocument($doc);
	    $update->addCommit();
	    
	    // this executes the query and returns the result
	    $result = $client->update($update);
	    
	    echo '<b>Update query executed</b><br/>';
	    echo 'Query status: ' . $result->getStatus(). '<br/>';
	    echo 'Query time: ' . $result->getQueryTime();
	}
	
	public function push_sync_item() {
	    $voice = $this->Voice->find('first', array(
	        'page'  => rand(1, 100),
	        'limit' => 1
	    ))['Voice'];
	    $this->Patch->patchPath($voice);
	    $result = $this->SyncQueue->enqueue(array('type' => 'voice', 'url' => $voice['voice']));
	    var_dump($result);
	}
	
	public function test_exception($timestamp) {
// 	    throw new CakeException('uncaught exception testing');
//         echo $undefined;
// 	    trigger_error('arise a warning error', E_WARNING);
//         if($this->request->is('POST')) {
// 	        trigger_error('arise a warning error', E_USER_WARNING);
//         }
        
// 	    debug(strftime('%Y-%m-%d %H:%M:%S', $timestamp));
// 	    $result = CakeTime::gmt($timestamp) +  300 > time();
	    
// 	    debug($result);
	}
	
	public function add() {
		$result = $this->Picture->save(array(
			'title' => 'abc',
		    'file' => new MongoBinData(file_get_contents(APP.WEBROOT_DIR."/img/cake.icon.png"))
		));
		var_dump($result);
		debug($result);
	}
	
	public function test_index() {
		$str = 'abcd';
		$len = strlen($str);
		
		$collect = array();
		for($j = 0, $w = ''; $j < $len; $j++, $w = '') {
			for($i = $j; $i < $len; $i++) {
				$w .= $str[$i];
				$collect[] = $w;
			}
		}
		
		var_dump($collect);
	}
	
	public function test_insert() {
		$key = 'key1';
		$uuid = phpcassa\UUID::uuid1();
		var_dump($uuid);
		$data = array(array($uuid, 'val1'));
	    $cf = $this->NotificationCass->getDataSource()->getColumnFamily('notice');
	    $cf->insert_format = ColumnFamily::ARRAY_FORMAT;
// 	    $cf->return_format = ColumnFamily::ARRAY_FORMAT;
	    $result = $cf->insert($key, $data);
	    var_dump($result);
	    
	    $cols = $cf->get($key, new ColumnSlice('', '', 2));
	    foreach($cols as $time => $col) {
	    	var_dump($col);
	    }
// 	    return $this->result($result);
	}
	
	public function add_notification() {
	    $userId = '123456';
	    $message = 'hahahahaha';
	    $type = 'official';
	    $result = $this->NotificationCass->official($userId, $message, $type);
	    var_dump($result);
// 	    return $this->result($result);
	}
	
	public function benchmark_insert_notification() {
	    $userId = 'user';
	    $message = 'msg';
	    $type = 'official';
	    $cf = $this->NotificationCass->getCF('inbox');
	    $count = 10000;
	    $time_start = microtime_float();
	    
	    for($i = 1; $i <= $count; $i++) {
	        $cf->insert($userId.$i, $data = array(
    	        $i => array(
        	        'user_id' => $userId.$i,
    	    )));
	    }
	    $time_end = microtime_float();
	    $time = $time_end - $time_start;
	    var_dump($time);
// 	    return $this->result($result);
	}
	
    public function benchmark_insert_redis() {
        $userId = 'user';
        $redis = $this->Purchased->getDataSource()->getInstance();
        $count = 10000;
        $time_start = microtime_float();
         
        $key = 'benchmark_key_';
        for($i = 1; $i <= $count; $i++) {
            $redis->set($key.$i, $userId.$i);
        }
        $time_end = microtime_float();
        $time = $time_end - $time_start;
        var_dump($time);
        $redis->del($redis->keys($key.'*'));
    }
	
    public function benchmark_insert_mongo() {
        $userId = 'user';
        $model = ClassRegistry::init('Comment');
        $ds = $model->getDataSource();
        if(method_exists($ds, 'getMongoDb')) {
        	$mongo = $ds->getMongoDb();
        }
        $collection = $mongo->selectCollection('test_col');
        $count = 10000;
        $time_start = microtime_float();
         
        for($i = 1; $i <= $count; $i++) {
            $collection->save(array('field' => $i, 'user_id' => $userId.$i));
        }
        $time_end = microtime_float();
        $time = $time_end - $time_start;
        var_dump($time);
//         $collection->drop();
    }
	
    public function benchmark_insert_couchbase() {
        $userId = 'user';
        $model = ClassRegistry::init('CouchbaseTest');
//         if(class_exists('Couchbase')){echo 'Yes';}
//         $model = new Couchbase('127.0.0.1:8091', '', '', 'default');
//         $model->set("default_jimmy_a", 101);
//         var_dump($model->get("default_jimmy_a"));
        $count = 10000;
        $model->prefix = 'bm';
        $time_start = microtime_float();
        for($i = 1; $i <= $count; $i++) {
//             $item = array('field' => $i, 'user_id' => $userId.$i);
//             $item = $userId.$i;
            $model->add(array('field' => $i, 'user_id' => $userId.$i));
        }
        $time_end = microtime_float();
        $time = $time_end - $time_start;
        var_dump($time);
    }
	
	public function get_notification() {
	    $userId = '123456';
	    $result = $this->NotificationCass->getData($userId);
	    foreach($result as $item) {
	        debug($item);
	    }
// 	    return $this->result($result);
	}
	
	public function auth() {
	    
	    debug(1111111);
	}
	
	public function upload() {
	    if($this->request->is('post')) {
	        debug($this->data);
	        debug($this->request->data);
	        debug($this->request->params);
	        $finfo = finfo_open(FILEINFO_MIME_TYPE);
	        $info = finfo_file($finfo, $this->request->params['form']['file']['tmp_name']);
	        debug($info);
	    }
	    $this->autoLayout = true;
	    $this->autoRender = true;
	}
	
	public function upload2() {
	    $this->autoLayout = true;
	    $this->autoRender = true;
	    $this->set('token', $this->QiNiu->uploadToken());
	}
	
	public function show($id = '') {
	    if($id) {
	        $pic = $this->Picture->find('first', array(
	            'fields' => array('modified'),
	            'conditions' => array(
	                '_id' => new MongoId($id)
	            )
	        ));
	        debug($pic); exit;
	        if(!$pic) {
	            var_dump('no found pic...');
	        } else {
	            header('Content-Type: image/png');
	            echo $pic['Picture']['file']->bin;
	            exit;
	        }
	    }
	}
	
	public function related($id = '') {
	    $pic = $this->Picture->find('first', array(
	        'conditions' => array(
	            '_id' => new MongoId($id),
	        )
	    ));
	    var_dump($pic); exit;
	}
	
	public function multi($ida = '', $idb = '') {
	    debug($ida); debug($idb);
	    $pic = $this->Picture->find('all', array(
	        'conditions' => array(
	            '_id' => array(
	                '$in' => array(
    	                new MongoId($ida),
    	                new MongoId($idb),
	                )
	            )
	        )
	    ));
	    var_dump($pic); exit;
	}
}

function microtime_float()
{
	list($usec, $sec) = explode(" ", microtime());
	return ((float)$usec + (float)$sec);
}