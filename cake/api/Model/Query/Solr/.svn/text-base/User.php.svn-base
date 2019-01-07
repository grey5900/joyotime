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

class User extends \AppModel {
    
    public $useDbConfig = 'solr_user'; // Defined at app/Config/database.php
    
    public $name = '\Model\Query\Solr\User';
    
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
            $resultset = $this->client->select($this->query);
            return $resultset;
        } catch (\Exception $e) {
            return array();
        }
    }
    
    public function setPage($page, $limit = 20) {
        // set start and rows param (comparable to SQL limit) using fluent interface
        $this->query->setStart(($page - 1) * $limit)->setRows($limit);
        return $this;
    }
    
    public function username($word) {
        if($word) {
        	$term = $this->term($word);
            $this->query->setQuery('username:'.$term);
        }
        return $this;
    }
    
    public function chkSort() {
        $sort = $this->query->getSorts();
        if(!$sort) {
            $this->query->addSort('username', \Solarium\QueryType\Select\Query\Query::SORT_ASC);
        }
    }
    
    private function term($word) {
        $len = mb_strlen($word);
        for($i = 0; $i < $len; $i++) {
            if(preg_match('/[?!\[\]{}^<>~*+:\-;\/"()]/', $word[$i])) {
                $word[$i] = '\\'.$word[$i];
            }
        }
    	return $word."*";
    }
}