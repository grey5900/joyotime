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
 * The command uses push notice to ios/android client device, triggered by cron
 *
 * @package		app.Console.Command
 */
class LocationSwapShell extends AppShell {

    public $uses = array(
        'Voice'
    );
    
    const LOG = 'location_swap';
    
    public function __construct($stdout = null, $stderr = null, $stdin = null) {
        parent::__construct($stdout, $stderr, $stdin);
    }

    public function main() {
        $this->out("Please execute command line looks like below,
./Console/cake location_swap doit
Please try again.");
    }

    public function doit() {
        $voices = $this->Voice->find('all', array(
            'fields' => array('location'),
            'order' => array('created' => 'desc')
        ));
        foreach($voices as $voice) {
            $result = $this->Voice->updateAll(array(
                'location' => array(
                    'lat' => $voice['Voice']['location']['lat'],
                    'lng' => $voice['Voice']['location']['lng']
                )
            ), array('_id' => new MongoId($voice['Voice']['_id'])));
            if(!$result) {
                $this->log("Update fails: [{$voice['Voice']['_id']}]", self::LOG);
                $this->out("Update fails: [{$voice['Voice']['_id']}]");
            }
        }
        
        $this->out('All location of voices are updated');
        return true;
    }
}