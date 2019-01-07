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
APP::uses('AppModel', 'Model');
APP::uses('Checkout', 'Model');
APP::uses('PriceComponent', 'Controller/Component');
/**
 * The model of Checkout
 *
 * @package		app.Model
 */
class FSConfig extends AppModel {
    
	public $primaryKey = '_id';
	
	public $name = 'FSConfig';
	
	public $useTable = 'fs_configs';
	
	public $mongoSchema = array(
	    'splash' => array('type' => 'string')
	);
}