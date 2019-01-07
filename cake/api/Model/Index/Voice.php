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

class Voice extends \AppModel {
    
    public $useDbConfig = 'solr_voice'; // Defined at app/Config/database.php
    
    public $name = 'VoiceIndex';
    
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
			'Model.Voice.afterCreated' => 'onUpdate',
			'Model.Voice.afterUpdated' => 'onUpdate',
			'Model.Voice.afterDeleted' => 'onUpdate'
    	));
    }
    
    public function onUpdate(\CakeEvent $event) {
        $model = $event->subject();
        $result = \CakeResque::enqueue('indexing', 'VoiceIndexShell',
            array('add', $model->data[$model->name])
        );
        if(!$result) $this->failEvent($event);
    }
    
    public function add($voice = array()) {
    	// add the document and a commit command to the update query
    	$this->update->addDocument($this->createDoc($voice));
        $this->update->addCommit();
        // this executes the query and returns the result
        return $this->client->update($this->update);
    }
    
    public function batch($voices = array()) {
        // create a new document for the data
        // please note that any type of validation is missing in this example to keep it simple!
        foreach($voices as $voice) {
            // add the document and a commit command to the update query
            $this->update->addDocument($this->createDoc($voice['Voice']));
        }
        // this executes the query and returns the result
        return $this->client->update($this->update);
    }
    
    public function commit() {
        $this->update->addCommit();
    }
    
/**
 * Create a document
 * 
 * @param array $voice
 * @return \Solarium\QueryType\Update\Query\Document\DocumentInterface
 */
    public function createDoc($voice = array()) {
        $doc = $this->update->createDocument();
        
        $voice['modified'] = (array) $voice['modified'];
        
        $doc->_id = $voice['_id'];
        $doc->title = $voice['title'];
        $doc->subject = $voice['title'];
        $doc->store = $voice['location']['lat'].','.$voice['location']['lng'];
        
        if(isset($voice['deleted'])) {
        	$doc->deleted = (int)$voice['deleted'];
        } else {
        	$doc->deleted = 0;
        }
        $doc->user_id = $voice['user_id'];
        $doc->language = $voice['language'];
        $doc->status = (int)$voice['status'];
        $doc->recommend = (int) isset($voice['recommend']) ? $voice['recommend'] : 0;
        $doc->tags = isset($voice['tags']) ? $voice['tags'] : array();
        $doc->modified = (int) $voice['modified']['sec'];
        if(isset($voice['approved']->sec)) 
            $doc->approved = $voice['approved']->sec;
        else if(isset($voice['approved']['sec'])) 
            $doc->approved = $voice['approved']['sec'];
        if(isset($voice['status_modified']->sec))
            $doc->status_modified = $voice['status_modified']->sec;
        else if(isset($voice['status_modified']['sec']))
            $doc->status_modified = $voice['status_modified']['sec'];
        $doc->isfree = (int) $voice['isfree'];
        $doc->score = (float) $voice['score'];
        $doc->checkout_total = (int) $voice['checkout_total'];
        $doc->play_total = (int) isset($voice['play_total']) ? $voice['play_total'] : 0;
        $doc->verified_author = (int)isset($voice['verified_author']) ? $voice['verified_author'] : 0;
        
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