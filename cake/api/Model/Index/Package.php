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

class Package extends \AppModel {
    
    public $useDbConfig = 'solr_package'; // Defined at app/Config/database.php
    
    public $name = '\Model\Index\Package';
    
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
			'Model.Package.afterSaved'   => 'onUpdate',
			'Model.Pacakge.afterUpdated' => 'onUpdate',
			'Model.Pacakge.afterDeleted' => 'onUpdate'
    	));
    }
    
    public function onUpdate(\CakeEvent $event) {
        $model = $event->subject();
        $result = \CakeResque::enqueue('indexing', 'PackageIndexShell',
            array('update', $model->data[$model->name])
        );
        if(!$result) $this->failEvent($event);
    }
    
    public function update($voice = array()) {
    	// add the document and a commit command to the update query
    	$this->update->addDocument($this->createDoc($voice));
        $this->update->addCommit();
        // this executes the query and returns the result
        return $this->client->update($this->update);
    }
    
    public function batch($pacakges = array()) {
        // create a new document for the data
        // please note that any type of validation is missing in this example to keep it simple!
        foreach($pacakges as $package) {
            // add the document and a commit command to the update query
            $this->update->addDocument($this->createDoc($package['Package']));
        }
        
        $this->update->addCommit();
        // this executes the query and returns the result
        return $this->client->update($this->update);
    }
    
/**
 * Create a document
 * 
 * @param array $pkg
 * @return \Solarium\QueryType\Update\Query\Document\DocumentInterface
 */
    public function createDoc($pkg = array()) {
        $doc = $this->update->createDocument();
        
        $pkg['modified'] = (array) $pkg['modified'];
        
        $doc->_id     = $pkg['_id'];
        $doc->title   = $pkg['title'];
        $doc->subject = $pkg['title'];
        $doc->store   = $pkg['location']['lat'].','.$pkg['location']['lng'];
        if(isset($pkg['deleted'])) $doc->deleted = (int)$pkg['deleted'];
        else $doc->deleted = 0;
        $doc->language = $pkg['language'];
        $doc->status   = (int)$pkg['status'];
        
        if(isset($pkg['created']->sec))
            $doc->created = (int)$pkg['created']->sec;
        else if(isset($pkg['created']['sec']))
            $doc->created = (int)$pkg['created']['sec'];
        
        if(isset($pkg['modified']->sec))
            $doc->modified = (int)$pkg['modified']->sec;
        else if(isset($pkg['modified']['sec']))
            $doc->modified = (int)$pkg['modified']['sec'];
        
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