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
/**
 * @package		app.Console.Command
 */
class CommentShell extends AppShell {

    public $uses = array(
        'Comment',
    	'Voice'
    );
    
    const LOG = 'comment';
    
    public function __construct($stdout = null, $stderr = null, $stdin = null) {
        parent::__construct($stdout, $stderr, $stdin);
    }

    public function main() {
        $this->out("Please execute command line looks like below,
./Console/cake comment [inject_voice_title]
Please try again.");
    }

    /**
     * Keep comment model in consistent...
     * 
     * @return boolean
     */
    public function inject_voice_title() {
        $comments = $this->Comment->find('all', array(
            'order' => array('created' => 'desc')
        ));
        
        // Start to update...
        foreach($comments as $comment) {
        	$result = false;
        	$voice = $this->Voice->findById($comment['Comment']['voice_id']);
        	if($voice && isset($voice['Voice']['title'])) {
        		$result = $this->Comment->updateAll(array(
        			'voice_title' => $voice['Voice']['title']
        		), array(
        			'_id' => new MongoId($comment['Comment']['_id'])
        		));
        	}
            if(!$result) {
                $this->log("Update fails: [{$comment['Comment']['_id']}]", self::LOG);
                $this->out("Update fails: [{$comment['Comment']['_id']}]");
            }
        }
        
        $this->out('All `voice_title` of comments are updated');
        return true;
    }

    /**
     * Keep comment model in consistent...
     * 
     * @return boolean
     */
    public function inject_voice_user_id() {
        $comments = $this->Comment->find('all', array(
            'order' => array('created' => 'desc')
        ));
        
        // Start to update...
        foreach($comments as $comment) {
        	$result = false;
        	$voice = $this->Voice->findById($comment['Comment']['voice_id']);
        	if($voice && isset($voice['Voice']['title'])) {
        		$result = $this->Comment->updateAll(array(
        			'voice_user_id' => $voice['Voice']['user_id']
        		), array(
        			'_id' => new MongoId($comment['Comment']['_id'])
        		));
        	}
            if(!$result) {
                $this->log("Update fails: [{$comment['Comment']['_id']}]", self::LOG);
                $this->out("Update fails: [{$comment['Comment']['_id']}]");
            }
        }
        
        $this->out('All `voice_user_id` of comments are updated');
        return true;
    }
    
/**
 * Check whether comment belongs of voice has been removed or not
 * 
 * @return boolean
 */
    public function chk_deprecated_voice() {
        $comments = $this->Comment->find('all', array(
        	'order' => array('created' => 'desc')
        ));
        
        $counter = 0;
        foreach($comments as $comment) {
            $voice = $this->Voice->findById($comment['Comment']['voice_id']);
            if($voice) {
                continue;
            }
            
            $this->Comment->delete($comment['Comment']['_id']);
            $counter++;
        }
        
        $this->out('All `deprecated voice` of comments('.$counter.') are updated');
        return true;
    }
}