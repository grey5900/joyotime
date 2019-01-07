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
class NotificationShell extends AppShell {

    public $uses = array(
        'Notification',
        'NotificationQueue',
        'NotificationCounter',
        'Follow'
    );
    
/**
 * Send message to user when someone gave money to him.
 * 
 * @return boolean
 */
    public function payfail() {
        $data = $this->args[0];
        
        if(!$data) {
        	$this->out('Data is empty, maybe param is wrong...');
        	return false;
        }
        
        $saved = $this->Notification->payfail($data);
        if(!$this->afterSaved($data, $saved)) {
            return false;
        }
        
        return $this->response($saved['Notification']);
    }
    
/**
 * Send message to user when someone gave money to him.
 * 
 * @return boolean
 */
    public function transfer() {
        $data = $this->args[0];
        
        if(!$data) {
        	$this->out('Data is empty, maybe param is wrong...');
        	return false;
        }
        
        $saved = $this->Notification->transfer($data);
        if(!$this->afterSaved($data, $saved)) {
            return false;
        }
        
        return $this->response($saved['Notification']);
    }
    
/**
 * Send message to user when someone gave money to him.
 * 
 * @return boolean
 */
    public function receiveTip() {
        $data = $this->args[0];
        
        if(!$data) {
        	$this->out('Data is empty, maybe param is wrong...');
        	return false;
        }
        
        $saved = $this->Notification->receiveTip($data);
        if(!$this->afterSaved($data, $saved)) {
            return false;
        }
        
        return $this->response($saved['Notification']);
    }
    
/**
 * Send message to voice author when admin accept his submit.
 * 
 * @return boolean
 */
    public function approvedAgain() {
        $data = $this->args[0];
        
        if(!$data) {
        	$this->out('Data is empty, maybe param is wrong...');
        	return false;
        }
        
        $saved = $this->Notification->approvedAgain($data);
        if(!$this->afterSaved($data, $saved)) {
            return false;
        }
        
        return $this->response($saved['Notification']);
    }
    
/**
 * Send message to voice author when admin accept his submit.
 * 
 * @return boolean
 */
    public function approved() {
        $data = $this->args[0];
        
        if(!$data) {
        	$this->out('Data is empty, maybe param is wrong...');
        	return false;
        }
        
        $saved = $this->Notification->approved($data);
        if(!$this->afterSaved($data, $saved)) {
            return false;
        }
        
        return $this->response($saved['Notification']);
    }
    
/**
 * Send message to voice author when admin got his voice off page...
 * 
 * @return boolean
 */
    public function invalid() {
        $data = $this->args[0];
        
        if(!$data) {
        	$this->out('Data is empty, maybe param is wrong...');
        	return false;
        }
        
        $saved = $this->Notification->invalid($data);
        if(!$this->afterSaved($data, $saved)) {
            return false;
        }
        
        return $this->response($saved['Notification']);
    }
    
/**
 * Send message to voice author when admin got his voice off page...
 * 
 * @return boolean
 */
    public function unavailable() {
        $data = $this->args[0];
        
        if(!$data) {
        	$this->out('Data is empty, maybe param is wrong...');
        	return false;
        }
        
        $saved = $this->Notification->unavailable($data);
        if(!$this->afterSaved($data, $saved)) {
            return false;
        }
        
        return $this->response($saved['Notification']);
    }
    
/**
 * Send message to comment author when admin got rid of his comment...
 * 
 * @return boolean
 */
    public function hideComment() {
        $data = $this->args[0];
        
        if(!$data) {
        	$this->out('Data is empty, maybe param is wrong...');
        	return false;
        }
        
        $saved = $this->Notification->hideComment($data);
        if(!$this->afterSaved($data, $saved)) {
            return false;
        }
        
        return $this->response($saved['Notification']);
    }
    
/**
 * Send message when voice author received new comment
 * 
 * @return boolean
 */
    public function newComment() {
        $data = $this->args[0];
        
        if(!$data) {
        	$this->out('Data is empty, maybe param is wrong...');
        	return false;
        }
        
        $saved = $this->Notification->newComment($data);
        // Just send notification to voice author...
        $data['user_id'] = $saved['Notification']['user_id'];
        // Don't send push notice...
        if(!$this->afterSaved($data, $saved, false)) {
            return false;
        }
        
        return $this->response($saved['Notification']);
    }
    
/**
 * Send message when admin rejected request for withdrawal
 * 
 * @return boolean
 */
    public function reverseWithdawal() {
        $data = $this->args[0];
        
        if(!$data) {
        	$this->out('Data is empty, maybe param is wrong...');
        	return false;
        }
        
        $saved = $this->Notification->reverseWithdawal($data);
        if(!$this->afterSaved($data, $saved)) {
            return false;
        }
        
        return $this->response($saved['Notification']);
    }
    
/**
 * Send message when user submit request for withdrawal
 * 
 * @return boolean
 */
    public function withdrawal() {
        $data = $this->args[0];
        
        if(!$data) {
        	$this->out('Data is empty, maybe param is wrong...');
        	return false;
        }
        
        $saved = $this->Notification->withdrawal($data);
        if(!$this->afterSaved($data, $saved)) {
            return false;
        }
        
        return $this->response($saved['Notification']);
    }
    
/**
 * Send message when admin gave gift to user
 * 
 * @return boolean
 */
    public function giftRegister() {
        $data = $this->args[0];
        
        if(!$data) {
        	$this->out('Data is empty, maybe param is wrong...');
        	return false;
        }
        
        $saved = $this->Notification->giftRegister($data);
        $total = $this->NotificationCounter->incr($data['_id']);
        return $this->response($saved['Notification']);
    }
    
    
/**
 * Send message when admin gave gift to user
 * 
 * @return boolean
 */
    public function gift() {
        $data = $this->args[0];
        
        if(!$data) {
        	$this->out('Data is empty, maybe param is wrong...');
        	return false;
        }
        
        $saved = $this->Notification->gift($data);
        if(!$this->afterSaved($data, $saved)) {
            return false;
        }
        
        return $this->response($saved['Notification']);
    }
    
/**
 * Send message when admin gave gift to user
 * 
 * @return boolean
 */
    public function broadcast() {
        $data = $this->args[0];
        
        if(!$data) {
        	$this->out('Data is empty, maybe param is wrong...');
        	return false;
        }
        
        $saved = $this->Notification->broadcast($data['user_id'], $data);
        if(!$this->afterSaved($data, $saved)) {
            return false;
        }
        
        return $this->response($saved['Notification']);
    }
    
/**
 * Send message when someone posted new voice, to his followers.
 * 
 * @return boolean
 */
    public function newPost() {
        $data = $this->args[0];
        
        if(!$data) {
        	$this->out('Data is empty, maybe param is wrong...');
        	return false;
        }
        
        $followers = $this->Follow->getFollowers($data['user_id']);
        
        $item['message'] = '';    // It has to empty...
        foreach($followers as $follower) {
            $userId = $follower['Follow']['user_id'];
            $item['badge']  = $this->NotificationCounter->count($userId);
            $item['badge'] += $this->Follow->countNewPosts($userId);
            $item['user_id'] = $userId;
            if(!$this->NotificationQueue->enqueue($item)) {
            	$this->out('To insert push notice in queue fails');
            	$this->out(pr($item));
            	return false;
            }
        }
        
        return $this->out("Sent new post notification to users(".count($followers).")...");
    }
    
    private function afterSaved(&$data, &$saved, $sendPN = true) {
        if($saved) {
            $total = $this->NotificationCounter->incr($data['user_id']);
            if($sendPN) {
                $saved['Notification']['badge'] = $total;
                $saved['Notification']['badge'] += $this->Follow->countNewPosts($data['user_id']);
                if(!$this->NotificationQueue->enqueue($saved['Notification'])) {
                    $this->out('To insert push notice in queue fails');
                    $this->out(pr($saved['Notification']));
                    return false;
                }
            }
        } else {
            $this->out('Sending notification fails...');
            $this->out(pr($data));
            return false;
        }
        return true;
    }
    
    private function response(&$data) {
        $this->out("Sent '{$data['message']}' to [{$data['user_id']}]...");
        return true;
    }
}