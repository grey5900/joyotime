<?php
require_once(VENDORS.'phpcassa/lib/autoload.php');

use phpcassa\ColumnFamily;
use phpcassa\SuperColumnFamily;
use phpcassa\Connection\ConnectionPool;

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
class CassandraSource extends DataSource 
{
    protected $host = '127.0.0.1';
    protected $port = 9160;
    protected $keyspace = '';
    
/**
 * @var ConnectionPool
 */
    public $pool = null;
    
/**
 * @var ColumnFamily
 */
    public $cf = null;

    public function __construct($config) {
        parent::__construct($config);
        $this->cacheSources = false;
        
        pr($config);

        $this->pool = new ConnectionPool($config['database'], $config['host']);
        $this->connected = true;
    }
    
/**
 * @return ColumnFamily
 */
    public function getColumnFamily($nameCF) {
        $cf = new ColumnFamily($this->pool, $nameCF);
        return $cf;
    }
    
/**
 * @return SuperColumnFamily
 */
    public function getSuperColumnFamily($nameCF) {
        $cf = new SuperColumnFamily($this->pool, $nameCF);
        return $cf;
    }

    public function __destruct() {
       $this->closeConnection();
       parent::__destruct();
    }
  

    public function closeConnection() {
        $this->pool = null;
        $this->connected = false;        
    }
  
    // Methods needed to avoid CakePHP ORM and SQL dependency.

    public function query() {
        return true;
    }
}