<?php
namespace Utility\Storage;

\App::uses('UploadComponent', 'QiNiu.Controller/Component');

class Cover extends QiNiu {
	
	public $_name = '\Utility\Storage\Cover';
	
	/**
	 * @var UploadComponent
	 */
	private $up;
	
	public function __construct() {
		$this->token = \CakeSession::read('Api.Token.uptoken.cover');
		$this->up = new \UploadComponent(new \ComponentCollection());
		parent::__construct();
	}
	
	public function write(\Model\Data\Upload $model, $rename = null) {
		return $this->up->cover($model->tmpName);
	}
}