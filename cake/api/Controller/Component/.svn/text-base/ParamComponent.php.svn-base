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
 * @since         FishSaying(tm) v 1.0.0
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
APP::uses('Component', 'Controller/Component');
/**
 * @package app.Controller.Component
 */
class ParamComponent extends Component {
    
/**
 * @var Controller
 */
    private $c;
    
    public function __construct(ComponentCollection $collection, $settings = array()) {
        parent::__construct($collection, $settings);
        $this->c = $collection->getController();
    }
    
    public function deviceCode() {
        return $this->c->request->header('X-Device')?:$this->c->request->query('device_code')?:'';
    }
    
    public function language() {
        return $this->c->request->query('language')?:'zh_CN';
    }
}