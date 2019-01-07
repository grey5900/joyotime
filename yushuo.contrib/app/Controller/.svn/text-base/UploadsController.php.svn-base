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
require_once VENDORS.'wideimage/lib/WideImage.php';
/**
 * FishSaying Controller
 *
 * Handle events posted from fishsaying server.
 *
 * @package		app.Controller
 */
class UploadsController extends AppController
{
    public $name = 'Uploads';
    
    public $autoLayout = false;
    public $autoRender = false;
    
    public $uses = false;

    public function cropping() {
        $voice = $this->request->data('voices');
        if(isset($voice['cover']['error']) && $voice['cover']['error'] == 0) {
            $image = WideImage::load($voice['cover']['tmp_name']);
            $cropped = $image->crop(
                    $voice['crop']['left'], 
                    $voice['crop']['top'], 
                    $voice['crop']['width'], 
                    $voice['crop']['height']);
            $cropped->output('jpg');
        } 
    }
}