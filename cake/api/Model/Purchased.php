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
 * @package		app.Model
 */
class Purchased extends AppModel {
    
    public $useDbConfig = 'redis'; // Defined at app/Config/database.php
    
    public $name = 'Purchased';
    
    /**
     * @var Redis
     */
    protected $redis;
    
    public function __construct($id = false, $table = null, $ds = null) {
    	parent::__construct($id, $table, $ds);
    	$this->redis = $this->getDataSource()->getInstance();
    }

    public function key($name, $userId, $year = '', $month = '') {
        if($name == 'list') {
            return "voices:$userId:bought";
        } else if($name == 'group') {
            return "voices:$userId:$year:$month:bought";
        } else if($name == 'track') {
            return "voices:$userId:track";
        } else if($name == 'exist') {
            return "voices:$userId:exist";
        }
    }

    /**
     * Get list of voice which has bought.
     *
     * @param string $userId            
     * @return array
     */
    public function voices($userId) {
        $key = $this->key('list', $userId);
        return $this->redis->lrange($key, 0, -1);
    }

    /**
     * Get records by month
     *
     * @param string $userId            
     * @param number $year            
     * @param number $month            
     * @return array
     */
    public function byDate($userId, $year, $month) {
        $key = $this->key('group', $userId, $year, $month);
        return $this->redis->lrange($key, 0, -1);
    }

    /**
     * Get groups of record by date
     *
     * @param string $userId            
     * @param number $limit            
     * @param string $sinceYear            
     * @param string $sinceMonth            
     * @return array
     */
    public function byGroup($userId, $limit, $sinceYear = '', $sinceMonth = '') {
        $result = array();
        $count = 0;
        $kTrack = $this->key('track', $userId);
        
        if($sinceYear && $sinceMonth) {
            $score = $this->calcScore((int)$sinceYear, (int)$sinceMonth);
            // return all groups which are <= $score
            $groups = $this->redis->zRangeByScore($kTrack, '-inf', $score);
        } else {
            $groups = $this->redis->zRange($kTrack, 0, -1);
        }
        
        // reverse groups
        rsort($groups);
        
        if(!$groups) {
            $groups = array();
        }
        
        foreach($groups as $kGroup) {
            list($foo, $userId, $year, $month, $bar) = explode(':', $kGroup);
            $result[] = array(
                'date' => $year . "-" . $month,
                'list' => $this->redis->lrange($kGroup, 0, -1) 
            );
            $count += $this->redis->lLen($kGroup);
            if($count >= $limit) {
                break;
            }
        }
        return $result;
    }

    /**
     * Push voiceid to bought list
     *
     * @param string $userId            
     * @param string $voiceId            
     * @param int $year            
     * @param int $month            
     */
    public function push($userId, $voiceId, $year = '', $month = '') {
        if(!$year) {
            $year = (int)strftime('%Y');
        }
        if(!$month) {
            $month = (int)strftime('%m');
        }
        $score = $this->calcScore($year, $month);
        $kList = $this->key('list', $userId);
        $kGroup = $this->key('group', $userId, $year, $month);
        $kTrack = $this->key('track', $userId);
        $kExist = $this->key('exist', $userId);
        $this->redis->lPush($kList, $voiceId);
        $this->redis->lPush($kGroup, $voiceId . '#' . time());
        $this->redis->zAdd($kTrack, $score, $kGroup);
        $this->redis->sAdd($kExist, $voiceId);
    }

    /**
     * Check whether the voice has been bought by user.
     *
     * @param string $userId            
     * @param string $voiceId            
     * @return boolean
     *
     */
    public function isExist($userId, $voiceId) {
        return $this->redis->sismember($this->key('exist', $userId), $voiceId);
    }

    private function calcScore($year, $month) {
        return $year * 100 + $month;
    }

    /**
     * How many voices have bought by $userId?
     *
     * @param string $userId            
     * @return int if key is empty return 0
     *        
     */
    public function count($userId) {
        $key = $this->key('list', $userId);
        return $this->redis->lLen($key);
    }
}