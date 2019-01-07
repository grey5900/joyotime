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
namespace Model\Query\Solr;

class Voice extends \AppModel {
    
    public $useDbConfig = 'solr_voice'; // Defined at app/Config/database.php
    
    public $name = '\Model\Query\Solr\Voice';
    
/**
 * @var Solarium\Client
 */
    protected $client;
    
/**
 * @var \Solarium\QueryType\Select\Query\Query
 */
    protected $query;
    
    protected $page = 1;
    
    protected $limit = 20;
    
    protected $_geodist = 'geodist()';
    
/**
 * unit: kilometer
 * @var int
 */
    protected $radius = 1000000;
    
    public function __construct($id = false, $table = null, $ds = null) {
        parent::__construct($id, $table, $ds);
        $this->client = $this->getDataSource()->getInstance();
        $this->query = $this->client->createSelect();
    }
    
/**
 * Get Set of result according to query
 * 
 * @return \Solarium\QueryType\Select\Result\Result
 */
    public function getResultSet() {
        // this executes the query and returns the result
        try {
            $this->chkSort();
            $sort = $this->query->getSorts();
            $resultset = $this->client->select($this->query);
            return $resultset;
        } catch(\Exception $e) {
            return array();
        }
    }
    
    public function setRadius($radius) {
        if(!$radius) return $this;
        $this->radius = (float) $radius;
        $this->query->addSort('play_total', \Solarium\QueryType\Select\Query\Query::SORT_DESC);
        return $this;
    }
    
    public function setPage($page, $limit = 20) {
        // set start and rows param (comparable to SQL limit) using fluent interface
        $this->query->setStart(($page - 1) * $limit)->setRows($limit);
        return $this;
    }
    
    public function title($word) {
        if($word) {
            $term = $this->terms($word);
            $this->query->setQuery('subject:'.$term.' OR tags:'.$word);
        }
        return $this;
    }
    
    public function available() {
        $delete = 1;
        $this->query->createFilterQuery('available')->setQuery('-deleted:'.$delete);
        
        return $this;
    }
    
    public function tags($tags) {
        if(!$tags) return $this;
        $tags = explode(',', $tags);
        foreach($tags as &$tag) $tag = trim($tag);
        $criteria = implode(' AND ', $tags);
        $this->query->createFilterQuery('tags')->setQuery("tags:($criteria)");
        
        return $this;
    }
    
    public function author($userId) {
        if($userId) {
            $this->query->createFilterQuery('author')->setQuery('user_id:'.$userId);
        }
        return $this;
    }
    
    public function recommend($val) {
        $this->query->createFilterQuery('recommend')->setQuery('recommend:'.(int)$val);
        return $this;
    }
    
    public function language($lang) {
    	if($lang) {
    		if($lang == 'zh_CN' || $lang == 'zh_TW') {
    		    $this->query->createFilterQuery('language')->setQuery("language:(zh_CN zh_TW)");
    		} else {
    		    $this->query->createFilterQuery('language')->setQuery('language:en_US');
    		}
    	}
    	return $this;
    }
    
    public function status($status) {
    	if(is_numeric($status)) {
    	    $this->query->createFilterQuery('status')->setQuery('status:'.(int)$status);
    	}
    	return $this;
    }
    
    public function geo(\Model\Data\Point $p) {
        $sort = $this->query->getSorts();
        
        $this->query->createFilterQuery('distance')->setQuery('{!geofilt}');
        $this->query->addField($this->_geodist);
        $this->query->addParam('pt',$p->getLatitude().','.$p->getLongitude());
        $this->query->addParam('sfield', 'store');
        $this->query->addParam('d', $this->radius);    // unit: km
        if(!$sort) $this->query->addSort('geodist()', \Solarium\QueryType\Select\Query\Query::SORT_ASC);
        
        return $this;
    }
    
    public function chkSort() {
        $sort = $this->query->getSorts();
        if(!$sort) {
            $this->query->addSort('modified', \Solarium\QueryType\Select\Query\Query::SORT_DESC);
        }
    }
    

    public function bySort($sort) {
        !empty($sort) && $sort = strtolower($sort);
    	if($sort == 'expert') {
    		$this->query->createFilterQuery('verified_author')->setQuery('verified_author: 1');
    	}
    	if($sort == 'hot') {
    		$this->query->createFilterQuery('hot')->setQuery('recommend: 1 OR isfree: 1 OR (checkout_total: [4 TO *] AND score: [4 TO *])');
    	}
    	if($sort == 'approved') {
    	    $this->query->addSort('approved', \Solarium\QueryType\Select\Query\Query::SORT_DESC);
    	}
    	if($sort == 'status_modified') {
    	    $this->query->addSort('status_modified', \Solarium\QueryType\Select\Query\Query::SORT_DESC);
    	}
    	return $this;
    }
    
    public function terms($word) {
        $word = preg_replace('/\s+/', ' ', $word);
        $terms = explode(' ', trim($word));
        foreach($terms as &$term) {
//             if(preg_match("/^[a-z]+$/i", $term)) {
//                 $term = strtolower($term).'*';
//             } 
            $term = '*'.strtolower($term).'*';
        }
        return "(".implode(' AND ', $terms).")";
    }
}