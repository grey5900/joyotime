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
App::uses('ComponentCollection', 'Controller');
App::uses('SubmitterComponent', 'Controller/Component');
App::uses('CakeSession', 'Model/Datasource');
App::uses('Transcode', 'Model');
/**
 * @package		app.Console.Command
 */
class VoiceShell extends AppShell {
    
    public $name = 'Voice';
    
    public $uses = array();
    
    public function __construct($stdout = null, $stderr = null, $stdin = null) {
        parent::__construct($stdout, $stderr, $stdin);
    }
    
    public function encode() {
        $data = $this->args[0];
        
        if(!$data) {
        	$this->out('Data is empty, maybe param is wrong...');
        	return false;
        }
        
        $file = $data['voice']['tmp_name'];
        $file = escapeshellarg($file);
        
        $command = Configure::read('FFMPEG_BIN_PATH')." -i $file -c:a libfdk_aac -b:a 16k $file.m4a";
        
        exec($command, $output, $result);
        
        if($result == 0) {
            unlink($data['voice']['tmp_name']);
            $data['voice']['tmp_name'] .= '.m4a';
            
            CakeResque::enqueue('postings', 'VoiceShell',
                array('post', $data)
            );
            
        	$this->out($file.' is convert into mp4 successfully.');
        	return true;
        } else {
        	$this->out($file.' encoding failed.');
        	return false;
        }
    }
    
    public function post() {
        $data = $this->args[0];
        
        if(!$data) {
        	$this->out('Data is empty, maybe param is wrong...');
        	return false;
        }
        
        $sub = $this->submitter();
        $sub->initSession($data);
        $this->out(sprintf("To start upload `%s`...", $data['title']));
        if($sub->upload($data)) {
            $this->out(sprintf("Uploaded `%s` to QiNiu is OK...", $data['title']));
        } else {
            $this->out("Uploading progress failed...");
            $this->out("Posting progress is terminated...");
            return false;
        }
        $resp = $sub->save($data);
        
        $transcode = new Transcode();
        
        if($resp->isFail()) {
            $transcode->setStatus($sub->getUniqid(), Transcode::STATUS_FAIL);
            $this->out(sprintf('Post `%s` to api server failed, because ', $data['title']));
            $this->out($resp->getMessage());
            return false;
        } else {
            $sub->clean();
            $transcode->delete($sub->getUniqid());
            $this->out(sprintf('Post `%s` to api server is OK...', $data['title']));
            return true;
        }
    }
    
    private function submitter() {
        return new SubmitterComponent(new ComponentCollection());
    }
    
    /**
     * Output response data on console
     */
    private function response($result, $filename) {
        if($result == 0) {
    	    $this->out($filename.' is convert into mp4 successfully.');
        } else {
            $this->out($filename.' encoding failed.');
        }
    	return true;
    }
}