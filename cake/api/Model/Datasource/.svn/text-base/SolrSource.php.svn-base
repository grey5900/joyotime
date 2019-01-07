<?php
if(!defined('CRLF'))
    define('CRLF', "\r\n");

require_once(VENDORS.'solarium/vendor/autoload.php');

/**
 * CakePHP 2.0 Couchbase Datasource.
 * 
 * Copyright (c) 2012 Iban Martinez (iban@nnset.com)
 *
 * https://github.com/nnset/Cake-PHP-2.xx-Redis-datasource
 * 
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @author Iban Martinez (iban@nnset.com)
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @note This DataSource has been tested with Redis 2.2.11
 * 
 * Credits : 
 *  Some methods where taken from https://github.com/jdp/redisent
 *  by Justin Poliey <jdp34@njit.edu>
 * 
 */
class SolrSource extends DataSource {
    
/**
 * @var Solarium\Client
 */
    public $client = null;
    
    public function __construct($config) {
        parent::__construct($config);
        $this->cacheSources = false;
        
        $this->client = new Solarium\Client($config);
        
        $this->connected = true;
    }
    
/**
 * @return SolrClient
 */
    public function getInstance() {
        return $this->client;
    }

    public function __destruct() {
       $this->closeConnection();
       parent::__destruct();
    }

    public function closeConnection() {
        $this->client = null;
        $this->connected = false;
    }
  
    // Methods needed to avoid CakePHP ORM and SQL dependency.

    public function query() {
        return true;
    }
}