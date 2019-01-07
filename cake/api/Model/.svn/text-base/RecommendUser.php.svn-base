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

class RecommendUser extends AppModel {
    
    public $useDbConfig = 'redis'; // Defined at app/Config/database.php
    
    public $name = 'RecommendUser';
    
/**
 * @var Redis
 */
    protected $redis;
    
/**
 * @var string
 */
    private $key = 'recommend_user';       // aka, recommend user
    
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
			'Model.User.afterUpdate.Recommend' => 'onRecommendUpdate',
			'Model.User.afterUpdate.RecommendOffset' => 'onRecommendOffsetUpdate'
    	));
    }
    
    public function onRecommendUpdate(CakeEvent $event) {
        $model = $event->subject();
        $data  = $model->data[$model->name];
        
        if(!isset($data['recommend']) || !$model->id) return ;
        if($data['recommend']) {
            $users = $this->redis->zRangeByScore($this->key, '-inf', '+inf', array('withscores' => TRUE));
            $rank = -1;
            foreach($users as $id => $pre) if($id == $model->id) { $rank = $pre; break; }
            // not exist yet...
            if($rank == -1) $this->redis->zAdd($this->key, $this->redis->zCard($this->key) + 1, $model->id);
        } else {
            $users = $this->redis->zRangeByScore($this->key, '-inf', '+inf', array('withscores' => TRUE));
            $rank = -1;
            foreach($users as $id => $pre) if($id == $model->id) { $rank = $pre; break; }
            if($rank < 0) return ;
            $this->redis->zRem($this->key, $model->id);
            foreach($users as $id => $score) if($score > $rank) {
                --$score;
                $this->redis->zAdd($this->key, $score, $id);
            }
        }
    }
    
    public function onRecommendOffsetUpdate(CakeEvent $event) {
        $model = $event->subject();
        $data  = $model->data[$model->name];
        
        if(!isset($data['recommend_offset']) || !$model->id) return ;
        $users = $this->redis->zRangeByScore($this->key, '-inf', '+inf', array('withscores' => TRUE));
        
        $rank = -1;
        $offset = (int)$data['recommend_offset'] * -1;
        $size = count($users);
        foreach($users as $id => $pre) if($id == $model->id) { $rank = $pre; break; }
        if($rank < 0) return ;
        
        $pos = $rank + $offset;
        if($pos >= $size) $pos = $size;
        if($pos <= 0)     $pos = 1;
        
        $up = function(&$score, $id) use ($pos, $pre) { 
            if($pos <= $score && $pre > $score) { 
                ++$score; 
                $this->redis->zAdd($this->key, $score, $id);
            }
        };
        $dn = function(&$score, $id) use ($pos, $pre) { 
            if($pos >= $score && $pre < $score) {
                --$score; 
                $this->redis->zAdd($this->key, $score, $id);
            }
        };
        $cb = ($offset > 0) ? $dn : $up;
        $up = function(&$score, $id) use ($pos, $pre) {
            $score = $pos;
            $this->redis->zAdd($this->key, $score, $id);
        };
        
        foreach($users as $id => &$score) {
        	$id == $model->id ? $up($score, $id) : $cb($score, $id);
        }
        
        return true;
    }
    
    /**
     * (non-PHPdoc)
     * @see Model::find()
     */
    public function find($type = 'first', $query = array()) {
        if($type == 'all') {
            $page  = Hash::get($query, 'page')?:1;
            $limit = Hash::get($query, 'limit')?:20;
            $start = ($page - 1) * $limit;
            $stop  = $start + $limit;
            
            if($start == 0) { $start = -1; $stop *= -1; }
            else { $start += 1; $start *= -1; $stop *= -1; }
            
            $ids = $this->redis->zRange($this->key, $stop, $start);
            $reIds = array();
            $size = count($ids) - 1;
            for($i = $size; $i >= 0; --$i) $reIds[] = $ids[$i];
            return $reIds;
        }
        if($type == 'count') {
            return $this->redis->zCard($this->key);
        }
    }
}