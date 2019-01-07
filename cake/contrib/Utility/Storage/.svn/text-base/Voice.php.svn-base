<?php
namespace Utility\Storage;

\App::uses('UploadComponent', 'QiNiu.Controller/Component');

class Voice extends QiNiu {
	
	public $_name = '\Utility\Storage\Voice';
	
	/**
	 * @var UploadComponent
	 */
	private $up;
	
	public function __construct() {
		$this->token = \CakeSession::read('Api.Token.uptoken.voice');
		$this->up = new \UploadComponent(new \ComponentCollection());
		parent::__construct();
	}
	
	public function write(\Model\Data\Upload $model, $rename = null) {
	    return $this->up->voice($model->tmpName);
	}
}