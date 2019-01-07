<?php
/**
 * The project of FishSaying is a SNS platform which is
 * based on voice sharing for each other with journey.
 *
 * The RESTful style API is used to communicate with each client-side.
 *
 * PHP 5
 *
 * FishSaying(tm) : FishSaying (http://www.fishsaying.com)
 * Copyright (c) fishsaying.com. (http://fishsaying.com)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) fishsaying.com. (http://www.fishsaying.com)
 * @link          http://fishsaying.com FishSaying(tm) Project
 * @since         FishSaying(tm) v 0.0.1
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
namespace Model\Index;

\APP::uses('AppModel', 'Model');

class User extends \AppModel {
    
    public $useDbConfig = 'solr_user'; // Defined at app/Config/database.php
    
    public $name = '\Model\Index\User';
    
/**
 * @var \Solarium\Client
 */
    protected $client;
    
/**
 * @var \Solarium\QueryType\Update\Query\Query
 */
    protected $update;
    
    public function __construct($id = false, $table = null, $ds = null) {
        parent::__construct($id, $table, $ds);
        $this->client = $this->getDataSource()->getInstance();
        // get an update query instance
        $this->update = $this->client->createUpdate();
    }
    
/**
 * (non-PHPdoc)
 * @see Model::implementedEvents()
 */
    public function implementedEvents() {
    	$callbacks = parent::implementedEvents();
    	return array_merge($callbacks, array(
			'Model.User.afterRegister' => 'onUpdate',
			'Model.User.afterUpdated' => 'onUpdate'
    	));
    }
    
    public function onUpdate(\CakeEvent $event) {
        $model = $event->subject();
        $data = $model->data[$model->name];
        if(!isset($data['_id'])) $data['_id'] = $model->id;
        $result = \CakeResque::enqueue('indexing', 'UserIndexShell',
            array('add', $data)
        );
        if(!$result) $this->failEvent($event);
    }
    
    public function add($item = array()) {
    	// add the document and a commit command to the update query
    	$doc = $this->createDoc($item);
    	if($doc) {
    		$this->update->addDocument($doc);
    		$this->update->addCommit();
    		// this executes the query and returns the result
    		return $this->client->update($this->update);
    	}
    }
    
    public function batch($items = array()) {
        // create a new document for the data
        // please note that any type of validation is missing in this example to keep it simple!
        foreach($items as $item) {
            // add the document and a commit command to the update query
            $doc = $this->createDoc($item['User']);
            if($doc) $this->update->addDocument($doc);
        }
        
        $this->update->addCommit();
        // this executes the query and returns the result
        return $this->client->update($this->update);
    }
    
/**
 * Create a document
 * 
 * @param array $item
 * @return \Solarium\QueryType\Update\Query\Document\DocumentInterface
 */
    public function createDoc($item = array()) {
        $doc = $this->update->createDocument();
        $doc->_id = $item['_id'];
        $doc->username = isset($item['username']) ? $item['username'] : '';
        if(!$doc->username) return false;
        return $doc;
    }
    
/**
 * (non-PHPdoc)
 * @see Model::delete()
 */
    public function delete($id = null, $cascade = true) {
        // add the delete id and a commit command to the update query
        $this->update->addDeleteById($id);
        $this->update->addCommit();
        
        // this executes the query and returns the result
        return $this->client->update($this->update);
    }
}