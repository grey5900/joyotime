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

class DeviceCode extends AppModel {
    
    public $useDbConfig = 'redis'; // Defined at app/Config/database.php
    
    public $name = 'DeviceCode';
    
/**
 * @var Redis
 */
    protected $redis;
    
/**
 * @var string
 */
    private $key = 'device_codes';       // aka, device code
    
    public function __construct($id = false, $table = null, $ds = null) {
        parent::__construct($id, $table, $ds);
        $this->redis = $this->getDataSource()->getInstance();
    }
    
/**
 * (non-PHPdoc)
 * @see Model::implementedEvents()
 */
    public function implementedEvents() {
    	$callbacks = parent::implementedEvents();
    	return array_merge($callbacks, array(
			'Model.User.afterRegister'  => 'onSave',
    	));
    }
    
    public function onSave(CakeEvent $event) {
        $model = $event->subject();
        $data  = $model->data[$model->name];
        $this->redis->hSet($this->key, $data['device_code'], $data['_id']);
    }
    
    public function isExist($dc) {
        return $this->redis->hExists($this->key, $dc);
    }
}