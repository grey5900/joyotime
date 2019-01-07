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
App::uses('AppShell', 'Console/Command');
/**
 * @package		app.Console.Command
 */
class UserIndexShell extends AppShell {
    
    public $name = 'UserIndex';
    
    public $uses = array(
        'User'
    );
    
/**
 * @var \Model\Model\Index\User
 */
    private $index;
    
    public function __construct($stdout = null, $stderr = null, $stdin = null) {
        parent::__construct($stdout, $stderr, $stdin);
        $this->index = new \Model\Index\User();
    }
    
    public function batch() {
        $items = $this->User->find('all', array(
            'order' => array('created' => 'desc')
        ));
        $result = $this->index->batch($items);
        $this->response($result, 'reIndexed all users');
    }
    
    public function add() {
        $data = $this->args[0];
        
        if(!$data) {
        	$this->out('Data is empty, maybe param is wrong...');
        	return false;
        }
        
        $result = $this->index->add($data);
        
        return $this->response($result, 'indexed '.$data['username']);
    }
    
    public function delete() {
        $data = $this->args[0];
        
        if(!$data) {
        	$this->out('Data is empty, maybe param is wrong...');
        	return false;
        }
        
        $result = $this->index->delete($data['_id']);
        
        return $this->response($result, 'deleted '. $data['username']);
    }
    
    /**
     * Output response data on console
     */
    private function response($result, $title = '') {
    	$this->out('Update query executed by ' . $title);
    	$this->out('Query status: ' . $result->getStatus());
    	$this->out('Query time: ' . $result->getQueryTime());
    	return true;
    }
}