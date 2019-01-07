<?php
/**
 * The project of FishSaying is a SNS platform which is
 * based on voice sharing for each other with journey.
 *
 * The contributor platform is used to CP create/publish costomize content.
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
 * @since         FishSaying(tm) v 1.0.0
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
App::uses('AppController', 'Controller');
/**
 * FishSaying Controller
 *
 * Handle events posted from fishsaying server.
 *
 * @package		app.Controller
 */
class TestsController extends AppController
{
    public $name = 'Tests';
    
    public $autoLayout = false;
    public $autoRender = false;
    
    public $components = array('FileInfo');
    
    public $uses = false;

    public function info() {
        $filename = '/data/assets/voice/2014/01/09/FiHkyPJPsHVuPV3fZWlbOowpiHl5';
        $finfo = finfo_open(FILEINFO_MIME_TYPE); // return mime type ala mimetype extension
        echo finfo_file($finfo, $filename) . "\n";
        finfo_close($finfo);
    }

    public function is_mp4() {
        $filename = '/data/assets/voice/2014/01/09/FiHkyPJPsHVuPV3fZWlbOowpiHl5';
        if($this->FileInfo->isMP4($filename)) {
            var_dump('It\'s mp4.');
        }
    }

    public function is_mp3() {
        $filename = '/data/assets/voice/2014/01/09/1.mp3';
        if($this->FileInfo->isMP3($filename)) {
            var_dump('It\'s mp3.');
        }
    }
}