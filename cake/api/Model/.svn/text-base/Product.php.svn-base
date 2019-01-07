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
/**
 * @package		app.Model
 */
class Product extends AppModel {
	
	public $name = 'Product';
	
	public $useTable = false;
	
	private $alipay = array();
	
	private $ios = array();
	
	public function __construct($id = false, $table = null, $ds = null) {
	    parent::__construct($id, $table, $ds);
	    
	    $this->alipay = Configure::read('Alipay.Product');
	    $this->ios = Configure::read('AppStore.Product');
	}
	
/**
 * Get specified product from product list, 
 * it will return whole list if no $id provided.
 * 
 * @param string $id
 * @return array|boolean
 */
    public function alipay($id = '', $field = '') {
        if(!$id) {
            return $this->alipay;
        }
        
        foreach($this->alipay as $item) {
            if($item['id'] == $id) {
                if($field && isset($item[$field])) {
                    return $item[$field];
                } else {
                    return $item;
                }
            }
        }
        return false;
    }
	
/**
 * Get specified product from product list, 
 * it will return whole list if no $id provided.
 * 
 * @param string $id
 * @return array|boolean
 */
    public function ios($id = '') {
        if(!$id) {
            return $this->ios;
        }
        
        // return seconds directly...
        if(isset($this->ios[$id])) {
        	return $this->ios[$id];
        }
        
        return false;
    }

}