<?php
APP::uses('MongoToken', 'Utility');

class MongoTokenTest extends CakeTestCase {
	
/**
 * @var MongoToken
 */
	private $token;
	
	public function setUp() {
		parent::setUp();
		$this->token = new MongoToken();
	}
	
	public function testGenerate() {
	    $this->assertEqual(strlen($this->token->generate('5270cba46703339b4b8b48e9')), 6);
	    $this->assertEqual(strlen($this->token->generate('5270cba46703339b4b8b48ea')), 6);
	    $this->assertEqual(strlen($this->token->generate('5270cba46703339b4b8b48eb')), 6);
	}
}