<?php
/**
 * The project of FenPay is a CRM platform based on Weixin MP API.
 *
 * Use it to communicates with Weixin MP.
 *
 * PHP 5
 *
 * FenPay(tm) : FenPay (http://fenpay.com)
 * Copyright (c) in.chengdu.cn. (http://in.chengdu.cn)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) in.chengdu.cn. (http://in.chengdu.cn)
 * @link          http://fenpay.com FenPay(tm) Project
 * @since         FenPay(tm) v 0.1.0
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
APP::uses('Security', 'Utility');
/**
 * It uses to execute command line task such as 
 * create account
 *
 * @package       app.Console.Command
 */
class UserShell extends AppShell {
	public $uses = array('User');
	
/**
 * An container for output messages.
 * @var array
 */
	private $desc = array();
	
	public function __construct($stdout = null, $stderr = null, $stdin = null) {
	    parent::__construct($stdout, $stderr, $stdin);
	    $this->desc['initial_account'] = "initial_account: create account and password for new user.
Example: Console/cake user initial_account bob pppp 商户名称
'bob' is username,
'pppp' is password
'商户名称' is title for this account";
	}

	public function main() {
		$this->out("Please execute command line looks like below,
Console/cake user [command_name]
The avaliable commands listed below:
{$this->desc['initial_account']}
Please try again.");
	}

	public function initial_account() {
	    if(!isset($this->args[0])) {
	        $this->out('please input account name...');
	        $this->out($this->desc['initial_account']);
	        return false;
	    }
	    if(!isset($this->args[1])) {
	        $this->out('please input a password for the account...');
	        $this->out($this->desc['initial_account']);
	        return false;
	    }
	    if(!isset($this->args[2])) {
	        $this->out('please input a title for the account...');
	        $this->out($this->desc['initial_account']);
	        return false;
	    }
	    
	    $this->User->create(array(
	        'username' => $this->args[0],
	        'password' => Security::hash($this->args[1], null, true),
	        'name' => $this->args[2],
	    ));
	    $user = $this->User->save();
	    
	    $this->out('Generated account successfully.');
	    $this->out('The name of account is '.$this->args[0]);
	    $this->out('The password of account is '.$this->args[1]);
	    $this->out('The title of account is '.$this->args[2]);
	    return true;
	}
}