<?php

App::uses('HttpSocket', 'Network/Http');

if(!defined('CRLF'))
    define('CRLF', "\r\n");

/**
 * CakePHP 2.0 Redis Datasource.
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
 **/
class RedisSource extends DataSource 
{
    protected $redisHost = null;
    protected $redisPort = null;
    protected $redisDb   = null;
/**
 * @var Redis
 */
    public $redis = null;

    public function __construct($config) {
        parent::__construct($config);
        $this->cacheSources = false;

        $this->redisHost = $config['host'];
        $this->redisPort = $config['port'];
        $this->redisDb   = $config['database'];
        
        $this->redis = new Redis();
        $this->redis->connect($this->redisHost, $this->redisPort);
        $this->redis->select($this->redisDb);
        
        $this->connected = true;
    }
    
/**
 * Get an instance of Redis
 * 
 * @return Redis
 */
    public function getInstance() {
        return $this->redis;
    }

    public function __destruct() {
       $this->closeConnection();
       parent::__destruct();
    }
  

    public function closeConnection() {
        $this->redis = null;
        $this->connected = false;        
    }
  
    // Methods needed to avoid CakePHP ORM and SQL dependency.

    public function query() {
        return true;
    }
}