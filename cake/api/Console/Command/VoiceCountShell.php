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
class VoiceCountShell extends AppShell {
    
    public $name = 'VoiceCount';
    
/**
 * @var \Model\Queue\Counter\Voice
 */
    private $voice;
    
    public function __construct($stdout = null, $stderr = null, $stdin = null) {
        parent::__construct($stdout, $stderr, $stdin);
        $this->voice = new \Model\Queue\Counter\Voice();
    }
    
    public function refresh() {
        $data = $this->args[0];
        if(!$data) {
        	$this->out('Data is empty, maybe param is wrong...');
        	return false;
        }
        $this->voice->dequeue($data);
        $this->out($data.' refresh successful.');
    }
}