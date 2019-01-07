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
APP::uses('ComponentCollection', 'Controller');
APP::uses('CacheComponent', 'Controller/Component');
/**
 * The command of ensureindex for collections of mongo.
 *
 * @package		app.Console.Command
 */
class MockupShell extends AppShell {

    public $uses = array(
        'User', 'Voice'
    );
    
    /**
     * @var CacheComponent
     */
    private $cache;

    public function __construct($stdout = null, $stderr = null, $stdin = null) {
        parent::__construct($stdout, $stderr, $stdin);
        $collection = new ComponentCollection();
        $this->cache = new CacheComponent($collection);
    }

    public function main() {
        $this->out("Please execute command line looks like below,
./Console/cake mockup bought username [voiceid] [year] [month]
If no voiceid supplied, random voices will be chosen by system in automatically.
If no year supplied, random year will be chosen by system in automatically.
If no month supplied, random month will be chosen by system in automatically.
Please try again.");
    }
    
    
    public function user() {
        set_time_limit(0);
        if(!isset($this->args[0])) {
        	$this->out('please input from number...');
        	return false;
        }
        if(!isset($this->args[1])) {
        	$this->out('please input how many users you want to mockup...');
        	return false;
        }
        $start = intval($this->args[0]);
        $num = intval($this->args[1]);
        if($num < 1 || $start >= $num) {
            $this->out('please input valid number of users...');
            return false;
        }
        $password = 'b4fb8c802583d75c36858811115b6272';
        $locale = 'zh_CN';
        $money = 10000;
        for($i = $start; $i < $num; $i++) {
            $no = $i + 1;
            $username = 'mockuser_'.$no;
            $email = $username.'@gmail.com';
            $this->User->create();
            $result = (bool)$this->User->save(array(
                'username' => $username,
                'email' => $email,
                'password' => $password,
                'money' => $money,
                'locale' => $locale
            ), FALSE);
            if(!$result) {
                $this->out("fails....\n");
            }
        }
        $this->out("Done!!!");
    }

    public function bought() {
        if(!isset($this->args[0])) {
	        $this->out('please input username...');
	        return false;
	    }
	    $username = $this->args[0];
	    $user = $this->User->find('first', array(
	        'conditions' => array(
	            'username' => $username
	        )
	    ));
	    if(!$user) {
	        $this->out('No found the user by username...');
	        return false;
	    }
	    $userId = $user['User']['_id'];
	    
	    if(!isset($this->args[1])) {
	        $length = rand(31, 180);
	        $voice = $this->Voice->find('first', array(
	            'fields' => array('_id'),
	            'conditions' => array(
	                'user_id' => array('$ne' => $userId),
	                'length' => array('$gt' => $length),
	            )
	        ));
	        if(!$voice) {
	            $this->out('There is no found valid voice to mock...');
	            return false;
	        }
	        $voiceId = $voice['Voice']['_id'];
	    } else {
	        $voiceId = trim($this->args[1]);
	    }
	    
	    if(!isset($this->args[2])) {
	        $years = array(2010, 2011, 2012, 2013, 2014);
	        $idx = array_rand($years);
	        $year = $years[$idx];
	    } else {
	        $year = intval($this->args[2]);
	    }
	    
	    if(!isset($this->args[3])) {
	        $months = array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12);
	        $idx = array_rand($months);
	        $month = $months[$idx];
	    } else {
	        $month = intval($this->args[3]);
	    }
	    
	    $bought = $this->cache->voice()->bought();
	    $bought->push($userId, $voiceId, $year, $month);
        
        $this->out('Created bought mockup successfully.');
        return true;
    }
}