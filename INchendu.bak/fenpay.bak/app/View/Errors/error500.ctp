<?php
/**
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
 * @package       app.View.Errors
 * @since         CakePHP(tm) v 0.10.0.1076
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
?>
<?php
if (Configure::read('debug') > 0):
	echo $this->element('exception_stack_trace');
endif;
?>
<style>
  .center {text-align: center; margin-left: auto; margin-right: auto; margin-bottom: auto; margin-top: auto;}
</style>
<title><?php echo $name; ?></title>
<body>
  <div class="hero-unit center">
  	<h1>错误 <small><font face="Tahoma" color="red"><?php echo $name; ?></font></small></h1>
    <br />
    <p><b><?php echo __d('cake', '错误'); ?>: </b>
	<?php echo __d('cake', '这是一个内部错误。'); ?></p>
  </div>    
</body>
<?php 
	$this->start('top_nav');
	echo $this->element('top_nav');
	$this->end();
?>