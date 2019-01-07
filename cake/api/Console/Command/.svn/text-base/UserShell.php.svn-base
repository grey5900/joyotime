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
APP::uses('Voice', 'Model');
/**
 * @package		app.Console.Command
 */
class UserShell extends AppShell {

    public $uses = array(
        'User', 'Voice', 'Checkout'
    );
    
    const LOG = 'user';
    
    public function __construct($stdout = null, $stderr = null, $stdin = null) {
        parent::__construct($stdout, $stderr, $stdin);
    }

    public function main() {
        $this->out("Please execute command line looks like below,
./Console/cake user role
Please try again.");
    }

    /**
     * Keep user model in consistent...
     * 
     * @return boolean
     */
    public function role() {
        $users = $this->User->find('all', array(
            'fields' => array('role'),
            'order' => array('created' => 'desc')
        ));
        
        // All available valid roles...
        $roles = array(
        	User::ROLE_ADMIN,
        	User::ROLE_USER
        );
        
        // Start to update...
        // Add `role` for user who hasn't this field...
        foreach($users as $user) {
            $result = $this->User->updateAll(array(
            	'role' => isset($user['User']['role']) 
            		&& !empty($user['User']['role']) 
            		&& in_array($user['User']['role'], $roles) 
            			? $user['User']['role'] : User::ROLE_USER
            ), array('_id' => new MongoId($user['User']['_id'])));
            if(!$result) {
                $this->log("Update fails: [{$user['User']['_id']}]", self::LOG);
                $this->out("Update fails: [{$user['User']['_id']}]");
            }
        }
        
        $this->out('All `role` of users are updated');
        return true;
    }
    
    /**
     * Update cover data
     *
     * @return boolean
     */
    public function avatar() {
    	$users = $this->User->find('all', array(
			'fields' => array('avatar'),
			'order' => array('created' => 'desc')
    	));
    
    	// Start to update...
    	foreach($users as $user) {
    		if(isset($user['User']['avatar'])) {
    			$key = $user['User']['avatar']['source'];
    			foreach($user['User']['avatar'] as &$item) {
    				$item = $key;
    			}
    			
    			$result = $this->User->updateAll(array(
    				'avatar' => $user['User']['avatar']
    			), array('_id' => new MongoId($user['User']['_id'])));
    			
    			if(!$result) {
    				$this->log("Update fails: [{$user['User']['_id']}]", self::LOG);
    				$this->out("Update fails: [{$user['User']['_id']}]");
    			}
    		}
    	}
    
    	$this->out('All `avatar` of users are updated');
    	return true;
    }
    
    public function voice_total() {
    	$users = $this->User->find('all', array(
    		'fields' => array('_id'),
    		'order' => array('created' => 'desc')
    	));
    	
    	$this->out('Total number of user: '.count($users));
    	$this->out('Now start to update...');
    	
    	foreach($users as $user) {
    		$count = $this->Voice->find('count', array(
    			'conditions' => array(
    				'user_id' => $user['User']['_id'],
    			    'status' => Voice::STATUS_APPROVED
    			)
    		));
    		$this->User->updateAll(array(
    			'voice_total' => $count
    		), array(
    			'_id' => new MongoId($user['User']['_id'])
    		));
    	}
    	
    	$this->out('All `voice_total` of users are updated');
    }
    
    public function purchase_total() {
    	$users = $this->User->find('all', array(
    		'fields' => array('_id'),
    		'order' => array('created' => 'desc')
    	));
    	
    	$this->out('Total number of user: '.count($users));
    	$this->out('Now start to update...');
    	
    	foreach($users as $user) {
    		$this->User->updateAll(array(
    			'purchase_total' => $this->Checkout->getPurchaseCount($user['User']['_id'])
    		), array(
    			'_id' => new MongoId($user['User']['_id'])
    		));
    	}
    	
    	$this->out('All `purchase_total` of users are updated');
    }
    
    public function voice_income_total() {
    	$users = $this->User->find('all', array(
    		'fields' => array('_id'),
    		'order' => array('created' => 'desc')
    	));
    	
    	$this->out('Total number of user: '.count($users));
    	$this->out('Now start to update...');
    	
    	foreach($users as $user) {
    		$this->User->updateAll(array(
    			'voice_income_total' => new MongoInt32($this->Checkout->getVoiceIncomeCount($user['User']['_id']))
    		), array(
    			'_id' => new MongoId($user['User']['_id'])
    		));
    	}
    	
    	$this->out('All `voice_income_total` for each users are updated');
    }
    
    public function voice_length_total() {
    	$users = $this->User->find('all', array(
    		'fields' => array('_id'),
    		'order' => array('created' => 'desc')
    	));
    	
    	$this->out('Total number of user: '.count($users));
    	$this->out('Now start to update...');
    	
    	foreach($users as $user) {
    		$this->User->updateAll(array(
    			'voice_length_total' => $this->Voice->getLengthCount(
    			        $user['User']['_id'], Voice::STATUS_APPROVED)
    		), array(
    			'_id' => new MongoId($user['User']['_id'])
    		));
    	}
    	
    	$this->out('All `voice_length_total` of users are updated');
    }
    
    public function reg_source() {
        $users = $this->User->find('all', array(
    		'order' => array('created' => 'desc')
        ));
        
        $this->out('Total number of user: '.count($users));
        $this->out('Now start to update...');
        
        foreach($users as $user) {
            $this->User->updateAll(array(
                'reg_source' => $this->getCertified($user)
            ), array(
                '_id' => new MongoId($user['User']['_id'])
            ));
        }
        
        $this->out('All `reg_source` of users are updated');
    }
    
    public function is_verified() {
    	$users = $this->User->find('all', array(
			'fields' => array('_id'),
			'order' => array('created' => 'desc')
    	));
    	foreach($users as $user) {
    		$this->User->updateAll(array(
    			'is_verified' => 0
    		), array(
    			'_id' => new MongoId($user['User']['_id'])
    		));
    	}
    
    	$this->out('All `is_verified` of users are updated');
    	return true;
    }
    
    public function is_contributor() {
    	$users = $this->User->find('all', array(
			'fields' => array('_id', 'is_contributor'),
			'order' => array('created' => 'desc')
    	));
    	foreach($users as $user) {
    		$this->User->updateAll(array(
    			'is_contributor' => (int) isset($user['User']['is_contributor']) ? $user['User']['is_contributor'] : 0
    		), array(
    			'_id' => new MongoId($user['User']['_id'])
    		));
    	}
    
    	$this->out('All `is_contributor` of users are updated');
    	return true;
    }
    
    public function recommend() {
    	$users = $this->User->find('all', array(
			'fields' => array('_id', 'recommend'),
			'order' => array('created' => 'desc')
    	));
    	foreach($users as $user) {
    		$this->User->updateAll(array(
    			'recommend' => (int) isset($user['User']['recommend']) ? $user['User']['recommend'] : 0
    		), array(
    			'_id' => new MongoId($user['User']['_id'])
    		));
    	}
    
    	$this->out('All `recommend` of users are updated');
    	return true;
    }
    
    private function getCertified($user) {
        if(isset($user['User']['certified']) 
            && is_array($user['User']['certified']) 
            && !empty($user['User']['certified'])) {
            foreach($user['User']['certified'] as $name => $item) {
                if($item['open_id']) {
                    return $name;
                }
            }
        }
        return 'email';
    }
}