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
class FollowShell extends AppShell {

    public $uses = array(
        'Follow'
    );
    
    const LOG = 'follow';
    
    public function __construct($stdout = null, $stderr = null, $stdin = null) {
        parent::__construct($stdout, $stderr, $stdin);
    }

    public function main() {
        $this->out("Please execute command line looks like below,
./Console/cake follow new_posts
Please try again.");
    }

/**
 * Update cover data
 * 
 * @return boolean
 */
    public function new_posts() {
        $result = $this->Follow->updateAll(array(
			'$set' => array('new_posts' => 0),
    	), array());
        
        $this->out('All `new_posts` of follow are updated');
        return true;
    }
    
}