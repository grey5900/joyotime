<?php
APP::uses('AppTestCase', 'Test/Case/Model');
APP::uses('AuthorizeToken', 'Model');
APP::uses('User', 'Model');

/**
 * The user model test class
 *
 * @package       app.Test.Case.Model
 */
class AuthorizeTokenTest extends AppTestCase {
	
	public $fixtures = array(
		'app.user',
	);
    
    public function getModelName() {
        return 'AuthorizeToken';
    }
    
    public function setUp() {
        parent::setUp();
        
        $fix = new UserFixture();
        $this->fix['user1'] = $fix->records[0];
        $this->fix['user2'] = $fix->records[1];
        
        $this->adminItem = new CredentialItem(array('_id' => (string) $this->fix['user1']['_id'], 'role' => User::ROLE_ADMIN));
        $this->userItem  = new CredentialItem(array('_id' => (string) $this->fix['user1']['_id'], 'role' => User::ROLE_USER));
    }
    
    public function testAdd() {
		$token1 = $this->model->add($this->adminItem);
		$token2 = $this->model->add($this->adminItem);
		$this->assertEqual($token1 == $token2, true);
		$credential = $this->model->getCredential($token1);
		$this->assertEqual($credential->isAdmin(), true);
		$this->assertEqual($credential->getUserId(), (string) $this->fix['user1']['_id']);
		$this->assertEqual($credential->getRole(), User::ROLE_ADMIN);
		
		$this->model->remove($credential->getUserId());
		$null = $this->model->getCredential($token1);
		$this->assertEqual($null->getUserId(), '');
		$this->assertEqual($null->getRole(), '');
    }
}