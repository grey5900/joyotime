<?php
/**
 * The project of FishSaying is a SNS platform which is
 * based on voice sharing for each other with journey.
 *
 * The RESTful style API is used to communicate with each client-side.
 *
 * PHP 5
 *
 * FishSaying(tm) : FishSaying (http://www.fishsaying.com)
 * Copyright (c) fishsaying.com. (http://fishsaying.com)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) fishsaying.com. (http://www.fishsaying.com)
 * @link          http://fishsaying.com FishSaying(tm) Project
 * @since         FishSaying(tm) v 0.0.1
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
APP::uses('AppController', 'Controller');

/**
 *
 * @package app.Controller
 */
class TestsController extends AppController {
	public $name = 'Tests';
	public $layout = 'fishsaying';
	public $components = array(
		
	);
	public $uses = array();
	function add() {
	phpinfo();exit;
		$this->Test->test2();
		exit;
		//phpinfo();exit;
		$this->autoLayout = false;
		$this->autoRender = false;
		try {
			$this->Test->save(array(
				'title'=>'122' 
			),true);
			echo __LINE__;
		} catch (ErrorException $e) {
			$e->getMessage();
		}
	}
	function index() {
		echo preg_replace("/1.html\?page=(\d+)/ies", "\\1"."\.html", $multipage);
		$this->autoLayout = false;
		$this->autoRender = false;
		//dump($this->Test->getMongoDb());exit;
		$cond = array();
	
		$rows = $this->Test->find('all', array(
			'conditions'=>$cond, 
			'order'=>array(
				'_id'=>'desc' 
			), 
			'page'=>$this->request->query('page') ?  : 1, 
			'limit'=>$this->request->query('limit') ?  : 20 
		));
		dump($rows);
		$total = $this->Test->find('count', array(
			'conditions'=>$cond 
		));
		$this->Session->setFlash("tryrt");
	}
}
