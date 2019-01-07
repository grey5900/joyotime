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
class BroadcastShell extends AppShell {

    public $uses = array(
        'User', 'Notification', 'Checkout', 'NotificationCounter', 'NotificationQueue'
    );

/**
 * Broadcast notification to users
 * 
 * @return boolean
 */
    public function send() {
        $data = $this->args[0];
        
        if(!$data) {
            $this->out('Data is empty, maybe param is wrong...');
            return false;
        }
        
        $collection = $this->getCollection($this->User);
        $cursor = $collection->find(array(), array('_id'));
        $count = 0;
        $saved = array();
        foreach($cursor as $row) {
            $userId = (string)$row['_id'];
            $this->Notification->broadcast($userId, $data);
            $this->NotificationCounter->incr($userId);
            $count++;
        }
        
        $this->sendPN($data);
        $this->out('All notifications ('.$count.') have been sent...');
        return true;
    }

/**
 * Broadcast notification to users
 * 
 * @return boolean
 */
    public function gift() {
        $data = $this->args[0];
        
        if(!$data) {
            $this->out('Data is empty, maybe param is wrong...');
            return false;
        }
        
        $seconds = $data['amount']['time'];
        $message = $data['message'];
        
        $collection = $this->getCollection($this->User);
        $cursor = $collection->find(array(), array('_id'));
        $count = 0;
        foreach($cursor as $row) {
            $userId = (string)$row['_id'];
            $data['user_id'] = $userId;
            $this->User->gift($userId, $seconds);
            $this->Checkout->gift($userId, $seconds, $message);
            $this->Notification->gift($data);
            $this->NotificationCounter->incr($userId);
            $count++;
        }
        unset($data['user_id']);
        $this->sendPN($data);
        $this->out('All notifications ('.$count.') have been sent...');
        return true;
    }
    
    private function sendPN($data) {
        if(!$this->NotificationQueue->enqueue($data)) {
        	$this->out('To insert push notice in queue fails');
        	$this->out(pr($data));
        	return false;
        }
    }
    
/**
 * Get instance of MongoCollection
 * 
 * @param Model $model 
 * @return MongoCollection it returns null if can not get mongo object.
 */
    private function getCollection(Model $model) {
        $ds = $model->getDataSource();
        if(method_exists($ds, 'getMongoDb')) {
        	$mongo = $ds->getMongoDb();
        	return $mongo->selectCollection($model->useTable);
        }
        return null;
    }
}