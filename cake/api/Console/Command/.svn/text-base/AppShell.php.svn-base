<?php
/**
 * AppShell file
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
 * @since         CakePHP(tm) v 2.0
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

App::uses('Shell', 'Console');

/**
 * Application Shell
 *
 * Add your application-wide methods in the class below, your shells
 * will inherit them.
 *
 * @package       app.Console.Command
 */
class AppShell extends Shell {
    
    public function __construct($stdout = null, $stderr = null, $stdin = null) {
    	parent::__construct($stdout, $stderr, $stdin);
    	
    	spl_autoload_register(function($class) {
			$parts = explode('\\', $class);
			if(in_array(strtolower($parts[0]), array('model', 'controller', 'utility'))) {
				$cate = array_shift($parts);
				$path = ROOT.DS.APP_DIR.DS.$cate.DS.str_replace('\\', DS, implode('\\', $parts)) . '.php';
				if (file_exists($path)) {
					return include $path;
				}
			}
		});
    }
    
    public function perform() {
    	$this->initialize();
    	$this->{array_shift($this->args)}();
    }

}
