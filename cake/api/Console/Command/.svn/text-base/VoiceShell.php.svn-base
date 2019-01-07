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
APP::uses('User', 'Model');
/**
 * @package		app.Console.Command
 */
class VoiceShell extends AppShell {

    public $uses = array(
        'Voice', 'Comment'
    );
    
    const LOG = 'voice';
    
    public function __construct($stdout = null, $stderr = null, $stdin = null) {
        parent::__construct($stdout, $stderr, $stdin);
    }

    public function main() {
        $this->out("Please execute command line looks like below,
./Console/cake voice cover
Please try again.");
    }

    /**
     * Update cover data
     * 
     * @return boolean
     */
    public function cover() {
        $voices = $this->Voice->find('all', array(
            'fields' => array('cover'),
            'order' => array('created' => 'desc')
        ));
        
        // Start to update...
        foreach($voices as $voice) {
            if(isset($voice['Voice']['cover'])) {
                $key = $voice['Voice']['cover']['source'];
                foreach($voice['Voice']['cover'] as &$item) {
                    $item = $key;
                }
            }
            $result = $this->Voice->updateAll(array(
            	'cover' => $voice['Voice']['cover']
            ), array('_id' => new MongoId($voice['Voice']['_id'])));
            
            if(!$result) {
                $this->log("Update fails: [{$voice['Voice']['_id']}]", self::LOG);
                $this->out("Update fails: [{$voice['Voice']['_id']}]");
            }
        }
        
        $this->out('All `cover` of voices are updated');
        return true;
    }
    
    /**
     * Update short id data
     * 
     * @return boolean
     */
    public function short_id() {
        $voices = $this->Voice->find('all', array(
            'fields' => array('_id', 'short_id'),
            'order' => array('created' => 'desc')
        ));
        
        $token = new MongoToken();
        
        // Start to update...
        foreach($voices as $voice) {
            if(isset($voice['Voice']['short_id']) && !empty($voice['Voice']['short_id'])) {
            	continue;
            }
            if(isset($voice['Voice']['_id'])) {
                $voice['Voice']['short_id'] = $token->generate($voice['Voice']['_id']);
                $result = $this->Voice->updateAll(
                		array('$set' => array('short_id' => $voice['Voice']['short_id'])),
                		array('_id' => new MongoId($voice['Voice']['_id'])));
            }
            
            if(!$result) {
                $this->log("Update fails: [{$voice['Voice']['_id']}]", self::LOG);
                $this->out("Update fails: [{$voice['Voice']['_id']}]");
            }
        }
        
        $this->out('All `short_id` of voices are updated');
        return true;
    }
    
    public function deleted() {
        $this->Voice->updateAll(array(
            '$set' => array('deleted' => false)
        ), array());
        
        $this->out('All `deleted` of voices are updated');
        return true;
    }
    
    public function comment_total() {
        $voices = $this->Voice->find('all', array(
            'fields' => array('_id'),
            'order' => array('created' => 'desc')
        ));
        foreach($voices as $voice) {
            $this->Voice->updateAll(array(
                'comment_total' => $this->getTotal($voice['Voice']['_id'])
            ), array(
                '_id' => new MongoId($voice['Voice']['_id'])
            ));
        }
        
        $this->out('All `comment_total` of voices are updated');
        return true;
    }
    
    public function cover_offset_y() {
        $voices = $this->Voice->find('all', array(
            'fields' => array('_id'),
            'order' => array('created' => 'desc')
        ));
        foreach($voices as $voice) {
            $this->Voice->updateAll(array(
                'cover_offset_y' => Voice::DEFAULT_OFFSET_Y
            ), array(
                '_id' => new MongoId($voice['Voice']['_id'])
            ));
        }
        
        $this->out('All `cover_offset_y` of voices are updated');
        return true;
    }
    
    public function recommend() {
        $voices = $this->Voice->find('all', array(
            'fields' => array('_id'),
            'order' => array('created' => 'desc')
        ));
        foreach($voices as $voice) {
            $this->Voice->updateAll(array(
                'recommend' => 0
            ), array(
                '_id' => new MongoId($voice['Voice']['_id'])
            ));
        }
        
        $this->out('All `recommend` of voices are updated');
        return true;
    }
    
/**
 * update score = 3 if Score == 0 
 * 
 * @return boolean
 */
    public function score() {
        $voices = $this->Voice->find('all', array(
    		'fields' => array('_id', 'score'),
            'conditions' => array('status' => 1),
    		'order' => array('created' => 'desc')
        ));
        foreach($voices as $voice) {
            $score = 3;
            if(isset($voice['Voice']['score']) && $voice['Voice']['score'] > 0) continue;
            
        	$this->Voice->updateAll(array(
        		'score' => $score
        	), array(
        		'_id' => new MongoId($voice['Voice']['_id'])
        	));
        }
        
        $this->out('All `score` of voices are updated');
        return true;
    }
    
    public function initScore() {
        $limit = 100;
        $page = 1;
        $total = $this->Voice->find('count', array(
            'conditions' => array('status' => 1)
        ));
        $this->out("The total of $total voices are reScoring...");
        
        $start = ($page - 1) * $limit;
        
        $ranks = array(3.0, 3.25, 3.5, 3.75);
        
        while($total > $start) {
        	$this->out("Page: $page voices are scoring...");
        	
        	$voices = $this->Voice->find('all', array(
        	    'fields' => array('_id'),
        	    'conditions' => array('status' => 1),
    			'order' => array('created' => 'desc'),
    			'page' => $page++,
    			'limit' => $limit
        	));
        	
        	foreach($voices as $voice) {
        	    $comments = $this->Comment->find('all', array(
        	        'fields' => array('_id', 'score'),
        	        'conditions' => array('voice_id' => $voice['Voice']['_id'])
        	    ));
        	    $scores = $score = 0;
        	    foreach($comments as $comment) $scores += $comment['Comment']['score'];
        	    if($scores > 0) $score = $scores / count($comments);
        	    if($score == 0) $score = $ranks[rand(0, 3)]; 
        	    $this->Voice->updateAll(array(
        	        'score' => $score
        	    ), array(
        	        '_id' => new MongoId($voice['Voice']['_id'])
        	    ));
        	    $this->out(sprintf("%s's score is %f", $voice['Voice']['_id'], $score));
        	    $comments = null;
        	    unset($comments);
        	}
        	$voices = null;
        	unset($voices);
        	$start = ($page - 1) * $limit;
        }
        
        $this->out('reScored '.$total.' voices');
    }
    
    public function approved() {
        $limit = 100;
        $page = 1;
        $total = $this->Voice->find('count', array(
            'conditions' => array('status' => 1)
        ));
        $this->out("The total of $total voices are reScoring...");
        
        $start = ($page - 1) * $limit;
        
        while($total > $start) {
        	$this->out("Page: $page voices are modifying...");
        	
        	$voices = $this->Voice->find('all', array(
        	    'fields' => array('_id', 'modified', 'approved'),
        	    'conditions' => array('status' => 1),
    			'order' => array('created' => 'desc'),
    			'page' => $page++,
    			'limit' => $limit
        	));
        	
        	foreach($voices as $voice) {
        	    if(isset($voice['Voice']['approved']) && !empty($voice['Voice']['approved'])) continue;
        	    $this->Voice->updateAll(array(
        	        'approved' => new MongoDate($voice['Voice']['modified']->sec, $voice['Voice']['modified']->usec)
        	    ), array(
        	        '_id' => new MongoId($voice['Voice']['_id'])
        	    ));
        	}
        	
        	$voices = null;
        	unset($voices);
        	$start = ($page - 1) * $limit;
        }
        
        $this->out('Initialized `approved` for '.$total.' voices');
    }
    
    /**
     * To fix incorrect `approved` for all voices that `status` isn't 1
     * 
     */
    public function approved_update_04_17() {
        $limit = 100;
        $page = 1;
        $c = 0; // counter
        $total = $this->Voice->find('count', array(
            'conditions' => array(
                'status' => array('$ne' => 1),
                'approved' => array('$exists' => true)
            )
        ));
        $this->out("The total of $total voices are initializing...");
        
        $start = ($page - 1) * $limit;
        
        while($total > $start) {
        	$this->out("Page: $page voices are modifying...");
        	
        	$voices = $this->Voice->find('all', array(
        	    'fields' => array('_id', 'approved', 'created'),
        	    'conditions' => array(
        	        'status' => array('$ne' => 1),
        	        'approved' => array('$exists' => true)
        	    ),
    			'order' => array('_id' => 'desc'),
    			'page' => $page++,
    			'limit' => $limit
        	));
        	
        	foreach($voices as $voice) {
        	    if(!isset($voice['Voice']['approved']) || empty($voice['Voice']['approved'])) continue;
        	    if($voice['Voice']['created']->sec != $voice['Voice']['approved']->sec) continue;
        	        
        	    $this->Voice->updateAll(array(
        	        'approved' => ''
        	    ), array(
        	        '_id' => new MongoId($voice['Voice']['_id'])
        	    ));
        	    $this->out('Modified voice '.$voice['Voice']['_id']);
        	    $c++;
        	}
        	
        	$voices = null;
        	unset($voices);
        	$start = ($page - 1) * $limit;
        }
        
        $this->out('Initialized `approved` for '.$c.' voices');
    }
    
    /**
     * To fix incorrect `approved` for all voices that `status` is 1 
     * 
     */
    public function approved_update_04_17_a() {
        $limit = 100;
        $page = 1;
        $c = 0; // counter
        $total = $this->Voice->find('count', array(
            'conditions' => array(
                'status' => 1,
                'approved' => array('$exists' => true)
            )
        ));
        $this->out("The total of $total voices are initializing...");
        
        $start = ($page - 1) * $limit;
        
        while($total > $start) {
        	$this->out("Page: $page voices are modifying...");
        	
        	$voices = $this->Voice->find('all', array(
        	    'fields' => array('_id', 'status_modified', 'approved', 'created'),
        	    'conditions' => array(
        	        'status' => 1,
        	        'approved' => array('$exists' => true)
        	    ),
    			'order' => array('_id' => 'desc'),
    			'page' => $page++,
    			'limit' => $limit
        	));
        	
        	foreach($voices as $voice) {
        	    if(!isset($voice['Voice']['approved']) || empty($voice['Voice']['approved'])) continue;
        	    if(!isset($voice['Voice']['status_modified']) || empty($voice['Voice']['status_modified'])) continue;
        	    if($voice['Voice']['created']->sec != $voice['Voice']['approved']->sec) continue;
        	        
        	    $this->Voice->updateAll(array(
        	        'approved' => new MongoDate($voice['Voice']['status_modified']->sec, $voice['Voice']['status_modified']->usec)
        	    ), array(
        	        '_id' => new MongoId($voice['Voice']['_id'])
        	    ));
        	    $this->out('Modified voice '.$voice['Voice']['_id']);
        	    $c++;
        	}
        	
        	$voices = null;
        	unset($voices);
        	$start = ($page - 1) * $limit;
        }
        
        $this->out('Initialized `approved` for '.$c.' voices');
    }
    
    public function status_modified() {
        $limit = 100;
        $page = 1;
        $total = $this->Voice->find('count');
        $this->out("The total of $total voices are reScoring...");
        
        $start = ($page - 1) * $limit;
        
        while($total > $start) {
        	$this->out("Page: $page voices are modifying...");
        	
        	$voices = $this->Voice->find('all', array(
        	    'fields' => array('_id', 'modified', 'status_modified'),
    			'order' => array('created' => 'desc'),
    			'page' => $page++,
    			'limit' => $limit
        	));
        	
        	foreach($voices as $voice) {
        	    if(isset($voice['Voice']['status_modified']) && !empty($voice['Voice']['status_modified'])) continue;
        	    $this->Voice->updateAll(array(
        	        'status_modified' => new MongoDate($voice['Voice']['modified']->sec, $voice['Voice']['modified']->usec)
        	    ), array(
        	        '_id' => new MongoId($voice['Voice']['_id'])
        	    ));
        	}
        	
        	$voices = null;
        	unset($voices);
        	$start = ($page - 1) * $limit;
        }
        
        $this->out('Initialized `status_modified` for '.$total.' voices');
    }
    
    public function play_total() {
        $limit = 100;
        $page = 1;
        $total = $this->Voice->find('count');
        $this->out("The total of $total voices are init play_total...");
        
        $start = ($page - 1) * $limit;
        
        while($total > $start) {
        	$this->out("Page: $page voices are modifying...");
        	
        	$voices = $this->Voice->find('all', array(
        	    'fields' => array('_id', 'play_total', 'checkout_total'),
    			'order' => array('created' => 'desc'),
    			'page' => $page++,
    			'limit' => $limit
        	));
        	
        	foreach($voices as $voice) {
        	    $this->Voice->updateAll(array(
        	        'play_total' => $this->initPlayTotal($voice['Voice'])
        	    ), array(
        	        '_id' => new MongoId($voice['Voice']['_id'])
        	    ));
        	}
        	
        	$voices = null;
        	unset($voices);
        	$start = ($page - 1) * $limit;
        }
        
        $this->out('Initialized `play_total` for '.$total.' voices');
    }
    
    private function initPlayTotal($voice)
    {
        $checkoutTotal = isset($voice['checkout_total'])?$voice['checkout_total']:1;
        $playTotal = rand(3, 10);
        
        if($checkoutTotal <= 0) $checkoutTotal = 1; 
        $playTotal += $checkoutTotal * rand(5, 15);
        
        return $playTotal;
    }
    
    private function getTotal($id) {
        return $this->Comment->find('count', array(
            'conditions' => array(
                'voice_id' => $id,
                'hide' => array('$ne' => true)
            )
        ));
    }
}