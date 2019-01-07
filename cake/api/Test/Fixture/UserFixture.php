<?php
APP::uses('Security', 'Utility');

class UserFixture extends CakeTestFixture {
	public $import = array('model' => 'User');
	
    public function init() {
        $this->records = array(
            array(
                '_id' => new MongoId('51f0c30f6f159aec6fad8ce3'),
                'username' => 'baohanddd',
                'email' => 'baohanddd@gmail.com',
                'password' => Security::hash('pppppppp', 'md5'),
                'favorite_size' => 0,
                'income' => 10800,
                'money' => 9200,
                'earn' => 5000,
                'cost' => 0,
                'avatar' => array(
                    'source' => '/data/tmp/avatar.png',
                    'x80' => '/data/tmp/avatar_80.png',
                    'x180' => '/data/tmp/avatar_180.png',
                ),
                'latest_voice_posts' => 0,
                'voice_total' => 1,
                'created' => new MongoDate(),
                'modified' => new MongoDate(),
            ),
            array(
                '_id' => new MongoId('51f0c30f6f159aec6fad8ce4'),
                'username' => 'liuxuan',
                'email' => 'liuxuan@gmail.com',
                'password' => Security::hash('pppppppp', 'md5'),
                'favorite_size' => 0,
                'income' => 10800,
                'money' => 300,
                'earn' => 20,
                'cost' => 40,
                'avatar' => array(
                    'source' => '/data/tmp/avatar.png',
                    '80x80' => '/data/tmp/avatar_80.png',
                    '180x180' => '/data/tmp/avatar_180.png',
                ),
                'latest_voice_posts' => 0,
                'voice_total' => 0,
            	'role' => 'admin',
                'created' => new MongoDate(),
                'modified' => new MongoDate(),
            ),
        );
        parent::init();
    } 
}