<?php
namespace Controller\Setting;

class Permission 
{
/**
 * 管理员
 * 
 * 拥有所有权限
 * 
 * @var string
 */
	const ROLE_ADMIN 	= 'admin';
/**
 * 普通用户
 * 
 * @var string
 */
	const ROLE_USER  	= 'user';
/**
 * 机器人用户
 * 
 * @var string
 */
	const ROLE_ROBOT  	= 'robot';
/**
 * 审核账号
 * 
 * 限制赠送一人或多人
 * 
 * @var string
 */
	const ROLE_CHECKER  = 'checker';
/**
 * 冻结账号
 * 
 * 限制被冻结用户账号的提现、购买、转移功能
 * 
 * @var string
 */
	const ROLE_FREEZE  	= 'freeze';
	
/**
 * The actions of forbids
 * 
 * @var array
 */
	private $forbids = array();
	
/**
 * The actions of allows
 * 
 * @var array
 */
	private $allows = array();
	
	public function __construct() {
		$this->forbids = array(
			self::ROLE_CHECKER => array(
				'broadcasts'    => array('admin_add')
			),
			self::ROLE_FREEZE => array(
				'purchases' 	=> array('add'),
				'checkouts' 	=> array('transfer', 'tip'),
				'withdrawals' 	=> array('add')
			)
		);
		
		$this->allows = array(
			'roles' => array(
				'admin_add' => self::ROLE_ADMIN
			),
			'versions' => array(
				'admin_add' => self::ROLE_ADMIN
			),
			'coupons' => array(
				'admin_add'  => self::ROLE_ADMIN,
				'admin_edit' => self::ROLE_ADMIN,
				'admin_view' => self::ROLE_ADMIN
			)
		);
	}
	
/**
 * Check user permission with current action
 * 
 * @param array $roles
 * @param \Controller $controller
 * @return boolean
 */
	public function forbid(array $roles, \Controller $controller) {
		$classname = strtolower($controller->name);
		$action = strtolower($controller->action);

		foreach($roles as $role) {
			if(isset($this->forbids[$role][$classname])) {
// 				if($this->forbids[$role][$classname] == $action) {
				if(in_array($action, $this->forbids[$role][$classname])) {
				
					return true;
				}
			}
		}
		return false;
	}
	
	public function allow(array $roles, \Controller $controller) {
		$classname = strtolower($controller->name);
		$action = strtolower($controller->action);
		
		if(isset($this->allows[$classname][$action])) {
			if(!in_array($this->allows[$classname][$action], $roles)) return false;
		}
		
		return true;
	}
	
/**
 * Check whether specify role name is valid or not
 * 
 * @param string $role The name of role
 * @return boolean
 */
	public static function validate($role) {
		switch(strtolower($role)) {
			case self::ROLE_ADMIN:
			case self::ROLE_CHECKER:
			case self::ROLE_FREEZE:
			case self::ROLE_USER:
			case self::ROLE_ROBOT:
				return true;
		}
		return false;
	}
}