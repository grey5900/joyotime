<?php
/**
 * This is core configuration file.
 *
 * Use it to configure core behaviour of Cake.
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Config
 * @since         CakePHP(tm) v 0.2.9
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 *
 * Database configuration class.
 * You can specify multiple configurations for production, development and testing.
 *
 * datasource => The name of a supported datasource; valid options are as follows:
 *		Database/Mysql 		- MySQL 4 & 5,
 *		Database/Sqlite		- SQLite (PHP5 only),
 *		Database/Postgres	- PostgreSQL 7 and higher,
 *		Database/Sqlserver	- Microsoft SQL Server 2005 and higher
 *
 * You can add custom database datasources (or override existing datasources) by adding the
 * appropriate file to app/Model/Datasource/Database. Datasources should be named 'MyDatasource.php',
 *
 *
 * persistent => true / false
 * Determines whether or not the database should use a persistent connection
 *
 * host =>
 * the host you connect to the database. To add a socket or port number, use 'port' => #
 *
 * prefix =>
 * Uses the given prefix for all the tables in this database. This setting can be overridden
 * on a per-table basis with the Model::$tablePrefix property.
 *
 * schema =>
 * For Postgres/Sqlserver specifies which schema you would like to use the tables in. Postgres defaults to 'public'. For Sqlserver, it defaults to empty and use
 * the connected user's default schema (typically 'dbo').
 *
 * encoding =>
 * For MySQL, Postgres specifies the character encoding to use when connecting to the
 * database. Uses database default not specified.
 *
 * unix_socket =>
 * For MySQL to connect via socket specify the `unix_socket` parameter instead of `host` and `port`
 */
class DATABASE_CONFIG {

// 	public $default = array(
// 		'datasource' => 'Database/Mysql',
// 		'persistent' => false,
// 		'host' => 'localhost',
// 		'login' => 'user',
// 		'password' => 'password',
// 		'database' => 'database_name',
// 		'prefix' => '',
// 		//'encoding' => 'utf8',
// 	);

// 	public $test = array(
// 		'datasource' => 'Database/Mysql',
// 		'persistent' => false,
// 		'host' => 'localhost',
// 		'login' => 'user',
// 		'password' => 'password',
// 		'database' => 'test_database_name',
// 		'prefix' => '',
// 		//'encoding' => 'utf8',
// 	);

	public $default = array(
		'datasource' => 'Mongodb.MongodbSource',
		'host' => '',
// 		'database' => 'foobar',
// 		'database' => 'fishsaying_test',
// 		'database' => 'fishsaying_9_26',
// 		'database' => 'fishsaying_9_28',
		'database' => 'fishsaying_10_8',
		'port' => null,
		'prefix' => '',
		'persistent' => 'true',
		'login' => '',
		'password' => '',
		'replicaset' => array(
			'host' => 'mongodb://127.0.0.1:10001,127.0.0.1:20001',
			'options' => array('replicaSet' => 'rs')
		)
	);
	
	// To make sure all tests are passing create the following entry in app/Config/database.php
	public $test = array(
		'datasource' => 'Mongodb.MongodbSource',
		'database' => 'test_foobar',
		'host' => 'localhost',
		'port' => 10001,
	);
	
	public $redis = array(
		'datasource' => 'RedisSource',
		'host' => '127.0.0.1',
		'port' => 6377,
		'database' => 1 /* Redis database number */
	);
	
	public $test_redis = array(
		'datasource' => 'RedisSource',
		'host' => '127.0.0.1',
		'port' => 6377,
		'database' => 15 /* Redis test database */
	);
	
	public $solr_voice = array(
	    'datasource' => 'SolrSource',
        'endpoint' => array(
            'localhost' => array(
                'host' => '127.0.0.1',
                'port' => 8080,
                'path' => '/solr/fs-voice/',
            )
        )
    );
	
	public $solr_user = array(
	    'datasource' => 'SolrSource',
        'endpoint' => array(
            'localhost' => array(
                'host' => '127.0.0.1',
                'port' => 8080,
                'path' => '/solr/fs-user/',
            )
        )
    );
	
	public $solr_package = array(
	    'datasource' => 'SolrSource',
        'endpoint' => array(
            'localhost' => array(
                'host' => '127.0.0.1',
                'port' => 8080,
                'path' => '/solr/fs-package/',
            )
        )
    );
}
