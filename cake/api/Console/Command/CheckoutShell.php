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
/**
 * @package		app.Console.Command
 */
class CheckoutShell extends AppShell {

    public $uses = array(
        'Checkout'
    );
    
    const LOG = 'checkout';
    
    public function __construct($stdout = null, $stderr = null, $stdin = null) {
        parent::__construct($stdout, $stderr, $stdin);
    }

    public function main() {
        $this->out("Please execute command line looks like below,
./Console/cake checkout cover
Please try again.");
    }

    /**
     * Update cover data
     * 
     * @return boolean
     */
    public function cover() {
        $checkouts = $this->Checkout->find('all', array(
            'fields' => array('cover'),
            'order' => array('created' => 'desc')
        ));
        
        // Start to update...
        foreach($checkouts as $checkout) {
            if(isset($checkout['Checkout']['cover'])) {
                $key = $checkout['Checkout']['cover']['source'];
                foreach($checkout['Checkout']['cover'] as &$item) {
                    $item = $key;
                }
                $result = $this->Checkout->updateAll(array(
                	'cover' => $checkout['Checkout']['cover']
                ), array('_id' => new MongoId($checkout['Checkout']['_id'])));
                
                if(!$result) {
                	$this->log("Update fails: [{$checkout['Checkout']['_id']}]", self::LOG);
                	$this->out("Update fails: [{$checkout['Checkout']['_id']}]");
                }
            }
            
        }
        
        $this->out('All `cover` of checkouts are updated');
        return true;
    }
    
}