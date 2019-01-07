<?php
use Monolog\Handler\Mongo;
/**
 *
 * @abstract 内容合作商管理相关方法
 *           统计相关数据用的是mongodb slave数据库
 *          
 */
App::uses('AppController', 'Controller');
App::uses('SlaveVoice', 'Model');
/**
 * FishSaying Controller
 *
 *
 * @package app.Controller
 */
class StatisticsController extends AppController {
	public $name = 'Statistics';
	public $layout = 'fishsaying';
	public $components = array(
		'ConnectApi'=>array(
			'className'=>'FishSayingApi.Connect' 
		), 
		'UserApi'=>array(
			'className'=>'FishSayingApi.User' 
		), 
		'RoleApi'=>array(
			'className'=>'FishSayingApi.Role' 
		), 
		'Patch' 
	);
	public $uses = array(
		'Partner', 
		'User', 
		'SlaveUser', 
		'SlaveVoice' 
	);
	public function map($kw = '') {
		
	}
	
	
}